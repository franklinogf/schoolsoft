<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;
use Classes\Server;
use Classes\Util;

Session::is_logged();
$lang = new Lang([
    ['ID del estudiante', 'Student ID'],
    ['Atrás', 'Go back'],
    ['Detalle de compras de articulos de los estudiantes', 'Details of student item purchases']
]);
$school = new School(Session::id());
$year = $school->info('year2');

class nPDF extends PDF
{
    function Header()
    {
        global $year;
        parent::header();
        global $lang;
        global $colegio;
        list($y1, $y2) = explode("-", $year);
        $y3 = $y1 + 2;
        $this->Cell(0, 5, 'Articulos Pagados ' . $year, 0, 1, 'C');
        $this->Ln(5);
    }
}

$estudiantesSS = $_REQUEST['students'] ?? [];

$pdf = new nPDF();
$pdf->AliasNbPages();

foreach ($estudiantesSS as $ss) {

    $students7 = DB::table('compra_cafeteria')->select("DISTINCT ss")->where([
        ['ss', $ss],
        ['year', $year],
        ['total', '<>', 0]
    ])->orderBy('ss')->get();

    $gt = 0;
    $num_res = count($students7);
    if ($num_res > 0) {
        foreach ($students7 as $student7) {

            $students2 = DB::table('year')->select("DISTINCT ss, nombre, apellidos, grado, cantidad, balance_a, mt, can")->where([
                ['ss', $student7->ss],
                ['year', $year]
            ])->orderBy('apellidos')->first();

            $pdf->AddPage();
            $pdf->Fill();
            $cafeteria = DB::table('compra_cafeteria')->where([
                ['ss', $student7->ss],
                ['year', $year]
            ])->orderBy('fecha')->get();

            list($s1, $s2, $s3) = explode("-", $student7->ss);
            $pdf->SetFont('Times', 'B', 11);
            $pdf->Cell(70, 5,'Nombre', 1, 0,'C', true);
            $pdf->Cell(20, 5, 'ID', 1, 0, 'C', true);
            $pdf->Cell(20, 5,'Grado', 1, 0, 'C', true);
            $pdf->Cell(20, 5, 'Balance', 1, 0, 'C', true);
            $pdf->Cell(30, 5, utf8_encode('Bal. Año Ant.'), 1, 0, 'C', true);
            $pdf->Cell(20, 5, 'Bal. Total', 1, 1, 'C', true);

            $pdf->SetFont('Times', '', 10);
            $pdf->Cell(70, 5, $students2->apellidos . ' ' . $students2->nombre, 1, 0, 'C');
            $pdf->Cell(20, 5, $s3, 1, 0, 'C');
            $pdf->Cell(20, 5, $students2->grado, 1, 0, 'C');
            $pdf->Cell(20, 5, number_format($students2->cantidad - $students2->balance_a, 2), 1, 0, 'R');
            $pdf->Cell(30, 5, $students2->balance_a, 1, 0, 'R');
            $pdf->Cell(20, 5, number_format($students2->cantidad, 2), 1, 1, 'R');

            $pdf->Ln(5);
            $mt = $students2->mt;
            $t1 = number_format($students2->cantidad, 2);
            $ba = $students2->balance_a;

            $students3 = DB::table('depositos')->where([
                ['ss', $student7->ss],
                ['year', $year]
            ])->orderBy('fecha')->get();

            $pdf->SetFont('Times', 'B', 11);

            $pdf->Cell(10, 5, '#', 1, 0, 'C', true);
            $pdf->Cell(20, 5, 'Fecha', 1, 0, 'C', true);
            $pdf->Cell(20, 5, 'Hora', 1, 0, 'C', true);
            $pdf->Cell(50, 5, utf8_encode('Tipo Depóitos'), 1, 0, 'C', true);
            $pdf->Cell(20, 5, 'Cantidad', 1, 1, 'C', true);

            $pdf->SetFont('Times', '', 10);
            $count = 1;
            $t = 0;

            foreach ($students3 as $deposito) {
                $pdf->Cell(10, 5, $count, 1, 0, 'R');
                $pdf->Cell(20, 5, $deposito->fecha, 1, 0, 'R');
                $pdf->Cell(20, 5, $deposito->hora, 1, 0, 'R');
                if ($deposito->tipoDePago == 'Otros') {
                    $pdf->Cell(50, 5, $deposito->otros, 1, 0, 'R');
                } else {
                    $pdf->Cell(50, 5, $deposito->tipoDePago, 1, 0, 'R');
                }
                $pdf->Cell(20, 5, '$' . $deposito->cantidad, 1, 1, 'R');

                $t = $t + $deposito->cantidad;
                $count++;
            }
            $pdf->SetFont('Times', 'B', 11);
            $pdf->Cell(120, 5, utf8_encode('Total depóitos: '), 1, 0, 'R');
            $pdf->Cell(20, 5, '$' . number_format($t, 2), 1, 1, 'R');
            $t2 = $t;
            $pdf->Ln(5);
            $pdf->Cell(10, 5, '#', 1, 0, 'C', true);
            $pdf->Cell(25, 5, 'Nro. Fac.', 1, 0, 'C', true);
            $pdf->Cell(20, 5, 'Fecha', 1, 0, 'C', true);
            $pdf->Cell(50, 5, utf8_encode('Descripción'), 1, 0, 'C', true);
            $pdf->Cell(30, 5, 'Tipo de pago', 1, 0, 'C', true);
            $pdf->Cell(20, 5, 'Precio', 1, 1, 'C', true);

            $pdf->SetFont('Times', '', 10);
            $count = 1;
            $t = 0;
            foreach ($cafeteria as $student) {
                $students4 = DB::table('compra_cafeteria_detalle')->where([
                    ['id_compra', $student->id]
                ])->orderBy('id')->get();

                foreach ($students4 as $student4) {
                    $pdf->Cell(10, 5, $count, 1, 0, 'R');
                    $pdf->Cell(25, 5, $student4->id_compra, 1, 0, 'C');
                    $pdf->Cell(20, 5, $student->fecha, 1, 0, 'C');
                    $pdf->Cell(50, 5, $student4->descripcion, 1, 0, 'C');
                    if ($student->tdp == 1) {
                        $tdp = 'Efectivo';
                    }
                    if ($student->tdp == 2) {
                        $tdp = 'Tarjeta';
                    }
                    if ($student->tdp == 3) {
                        $tdp = 'Id Estudiante';
                    }
                    if ($student->tdp == 4) {
                        $tdp = 'Nombre Estudiante';
                    }
                    if ($student->tdp == 5) {
                        $tdp = 'ATH';
                    }
                    $pdf->Cell(30, 5, $tdp, 1, 0, 'L');
                    if ($student4->precio_final > 0) {
                        $pdf->Cell(20, 5, '$' . $student4->precio_final, 1, 1, 'R');
                        $t = $t + $student4->precio_final;
                    } else {
                        $pdf->Cell(20, 5, '$' . $student4->precio, 1, 1, 'R');
                        $t = $t + $student4->precio;
                    }


                    $count++;
//                    $gt = $gt + $student->tmat;
                }
            }
            $pdf->SetFont('Times', 'B', 11);
            $pdf->Cell(135, 5, 'Total Comprado: ', 1, 0, 'R');
            $pdf->Cell(20, 5, '$' . number_format($t, 2), 1, 1, 'R');
            $pdf->Ln(5);

            $pdf->SetFont('Times', 'B', 11);
            $pdf->Cell(35, 5, utf8_encode('Total Depósitos: '), 1, 0, 'C', true);
            $pdf->Cell(35, 5, 'Total Comprado: ', 1, 0, 'C', true);
            $pdf->Cell(35, 5, 'Balance: ', 1, 1, 'C', true);
            $pdf->Cell(35, 5, '$' . number_format($t2 + $ba, 2), 1, 0, 'R');
            $pdf->Cell(35, 5, '$' . number_format($t, 2), 1, 0, 'R');
            $pdf->SetFillColor(255, 0, 0);
            $b1 = round($t2 - $t + $ba, 2);
            $b2 = round($t1, 2);
            if ($t1 < 0) {
                $pdf->Cell(35, 5, '$' . number_format($t1, 2), 1, 0, 'R');
                $pdf->Cell(20, 5, '', 1, 1, 'R', true);
            } else {
                if ($t1 > 0 and $t2 == 0 and $t == 0) {
                    $pdf->Cell(35, 5, '$' . number_format($t1, 2), 1, 0, 'R');
                    $pdf->Cell(20, 5, '', 1, 1, 'R', true);
                } else {
                    $pdf->Cell(35, 5, '$' . number_format($t1, 2), 1, 1, 'R');
                }
            }

            $thisCourse2 = DB::table('year')->where([
                ['mt', $mt]
            ])->update([
                'cantidad' => $b1,
            ]);
        }
    }
}

$pdf->Output();
