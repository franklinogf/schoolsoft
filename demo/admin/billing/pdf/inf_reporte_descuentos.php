<?php
require_once '../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ['DESCUENTO POR MES', 'DISCOUNT PER MONTH'],
    ['DESCRIPCION', 'DESCRIPTION'],
    ['CODIGO', 'CODE'],
    ['PAGOS', 'PAYS'],
    ['DEUDAS', 'DEBTS'],
    ['BALANCES', 'BALANCES'],
    ['Pagina ', 'Page '],
    ['FECHA: ', 'DATE: '],
    ['NOMBRE ESTUDIANTES', 'STUDENT NAMES'],
    ['HASTA', 'TO'],
    ['GRAN TOTAL:', 'GRAND TOTAL:'],
    ['Ago', 'Aug'],
    ['Sep', 'Sep'],
    ['Oct', 'Oct'],
    ['Nov', 'Nov'],
    ['Dic', 'Dec'],
    ['Ene', 'Jan'],
    ['Feb', 'Feb'],
    ['Mar', 'Mar'],
    ['Abr', 'Abr'],
    ['May', 'May'],
    ['Jun', 'Jun'],
    ['Jul', 'Jul'],
    ['Grados', 'Grades'],
    ['Matri/Junio', 'Regis/June'],
    ['TOTAL: ', 'TOTAL'],
    ['GRADO: ', 'GRADE: '],
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
        $sp = 120;
        $this->Cell($sp);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(30, 10, 'DESCUENTO POR MES' . ' / ' . $year, 0, 1, 'C');
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(60);
        $this->Ln(1);
        $this->SetFont('Arial', 'B', 10);
        $this->Ln(6);
        $this->Cell(8, 5, '  ', 1, 0, 'C', true);
        $this->Cell(50, 5, $lang->translation('DESCRIPCION'), 1, 0, 'C', true);
        $this->Cell(17, 5, $lang->translation('AGO'), 1, 0, 'C', true);
        $this->Cell(17, 5, $lang->translation('SEP'), 1, 0, 'C', true);
        $this->Cell(17, 5, $lang->translation('OCT'), 1, 0, 'C', true);
        $this->Cell(17, 5, $lang->translation('NOV'), 1, 0, 'C', true);
        $this->Cell(17, 5, $lang->translation('DIC'), 1, 0, 'C', true);
        $this->Cell(17, 5, $lang->translation('ENE'), 1, 0, 'C', true);
        $this->Cell(17, 5, $lang->translation('FEB'), 1, 0, 'C', true);
        $this->Cell(17, 5, $lang->translation('MAR'), 1, 0, 'C', true);
        $this->Cell(17, 5, $lang->translation('ABR'), 1, 0, 'C', true);
        $this->Cell(17, 5, $lang->translation('MAY'), 1, 0, 'C', true);
        $this->Cell(17, 5, $lang->translation('JUN'), 1, 0, 'C', true);
        $this->Cell(17, 5, $lang->translation('JUL'), 1, 0, 'C', true);
        $this->Cell(17, 5, $lang->translation('TOTAL'), 1, 0, 'C', true);
        $this->Ln(5);
    }

    function Footer()
    {
        global $lang;
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, $lang->translation('Pagina ') . $this->PageNo() . '/{nb}' . ' / ' . date('m-d-Y'), 0, 0, 'C');
    }
}
$pdf = new nPDF();
$pdf->AliasNbPages();
$pdf->Fill();
$pdf->SetTitle($lang->translation('DESCUENTO POR MES') . ' ' . $year);
$pdf->AddPage('L');
$pdf->SetFont('Times', '', 10);
$result = DB::table('presupuesto')->where([
    ['year', $year]
])->get();
$x = 0;
$b1 = 0;
$b2 = 0;
$b3 = 0;
$b4 = 0;
$b5 = 0;
$b6 = 0;
$b7 = 0;
$b8 = 0;
$b9 = 0;
$b10 = 0;
$b11 = 0;
$b12 = 0;
$b13 = 0;
foreach ($result as $row1) {
    $result2 = DB::table('pagos')->where([
        ['year', $year],
        ['codigo', $row1->codigo],
        ['deuda', '<', 0],
        ['baja', '']
    ])->get();
    $a1 = 0;
    $a2 = 0;
    $a3 = 0;
    $a4 = 0;
    $a5 = 0;
    $a6 = 0;
    $a7 = 0;
    $a8 = 0;
    $a9 = 0;
    $a10 = 0;
    $a11 = 0;
    $a12 = 0;
    $a13 = 0;
    foreach ($result2 as $row2) {
        list($y, $m, $d) = explode("-", $row2->fecha_d);
        if ($m == '08') {
            $a1 = $a1 + $row2->deuda;
        }
        if ($m == '09') {
            $a2 = $a2 + $row2->deuda;
        }
        if ($m == '10') {
            $a3 = $a3 + $row2->deuda;
        }
        if ($m == '11') {
            $a4 = $a4 + $row2->deuda;
        }
        if ($m == '12') {
            $a5 = $a5 + $row2->deuda;
        }
        if ($m == '01') {
            $a6 = $a6 + $row2->deuda;
        }
        if ($m == '02') {
            $a7 = $a7 + $row2->deuda;
        }
        if ($m == '03') {
            $a8 = $a8 + $row2->deuda;
        }
        if ($m == '04') {
            $a9 = $a9 + $row2->deuda;
        }
        if ($m == '05') {
            $a10 = $a10 + $row2->deuda;
        }
        if ($m == '06') {
            $a11 = $a11 + $row2->deuda;
        }
        if ($m == '07') {
            $a12 = $a12 + $row2->deuda;
        }
        $a13 = $a13 + $row2->deuda;
        if ($m == '08') {
            $b1 = $b1 + $row2->deuda;
        }
        if ($m == '09') {
            $b2 = $b2 + $row2->deuda;
        }
        if ($m == '10') {
            $b3 = $b3 + $row2->deuda;
        }
        if ($m == '11') {
            $b4 = $b4 + $row2->deuda;
        }
        if ($m == '12') {
            $b5 = $b5 + $row2->deuda;
        }
        if ($m == '01') {
            $b6 = $b6 + $row2->deuda;
        }
        if ($m == '02') {
            $b7 = $b7 + $row2->deuda;
        }
        if ($m == '03') {
            $b8 = $b8 + $row2->deuda;
        }
        if ($m == '04') {
            $b9 = $b9 + $row2->deuda;
        }
        if ($m == '05') {
            $b10 = $b10 + $row2->deuda;
        }
        if ($m == '06') {
            $b11 = $b11 + $row2->deuda;
        }
        if ($m == '07') {
            $b12 = $b12 + $row2->deuda;
        }
        $b13 = $b13 + $row2->deuda;
    }
    $x = $x + 1;
    $pdf->Cell(8, 5, $x, 1, 0, 'R');
    $pdf->Cell(50, 5, $row1->descripcion, 1, 0, 'C');
    $pdf->Cell(17, 5, $a1, 1, 0, 'R');
    $pdf->Cell(17, 5, $a2, 1, 0, 'R');
    $pdf->Cell(17, 5, $a3, 1, 0, 'R');
    $pdf->Cell(17, 5, $a4, 1, 0, 'R');
    $pdf->Cell(17, 5, $a5, 1, 0, 'R');
    $pdf->Cell(17, 5, $a6, 1, 0, 'R');
    $pdf->Cell(17, 5, $a7, 1, 0, 'R');
    $pdf->Cell(17, 5, $a8, 1, 0, 'R');
    $pdf->Cell(17, 5, $a9, 1, 0, 'R');
    $pdf->Cell(17, 5, $a10, 1, 0, 'R');
    $pdf->Cell(17, 5, $a11, 1, 0, 'R');
    $pdf->Cell(17, 5, $a12, 1, 0, 'R');
    $pdf->Cell(17, 5, $a13, 1, 1, 'R');
}
$pdf->Cell(58, 5, $lang->translation('GRAN TOTAL:'), 1, 0, 'R');
$pdf->Cell(17, 5, $b1, 1, 0, 'R');
$pdf->Cell(17, 5, $b2, 1, 0, 'R');
$pdf->Cell(17, 5, $b3, 1, 0, 'R');
$pdf->Cell(17, 5, $b4, 1, 0, 'R');
$pdf->Cell(17, 5, $b5, 1, 0, 'R');
$pdf->Cell(17, 5, $b6, 1, 0, 'R');
$pdf->Cell(17, 5, $b7, 1, 0, 'R');
$pdf->Cell(17, 5, $b8, 1, 0, 'R');
$pdf->Cell(17, 5, $b9, 1, 0, 'R');
$pdf->Cell(17, 5, $b10, 1, 0, 'R');
$pdf->Cell(17, 5, $b11, 1, 0, 'R');
$pdf->Cell(17, 5, $b12, 1, 0, 'R');
$pdf->Cell(17, 5, $b13, 1, 1, 'R');
$pdf->Output();
