<?php
require_once '../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ['INFORME DE PAGOS DIARIOS RESUMEN', 'DAILY PAYMENTS REPORT SUMMARY'],
    ['NOMBRE', 'NAME'],
    ['CTA', 'ACCT'],
    ['PAGOS', 'PAYS'],
    ['FECHA P.', 'PAY DAY'],
    ['T. PAGO', 'TIPE PAY'],
    ['DESDE', 'FROM'],
    ['HASTA', 'TO'],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
]);

$school = new School(Session::id());
$year = $school->info('year2');

$date = date('Y-m-d');
class nPDF extends PDF
{
    public function Header()
    {
        global $colegio;
        global $year;
        parent::header();
        $this->Ln(1);
        $this->SetFont('Arial', 'B', 12);
        //        $this->Cell(0, 5, "INFORME DE DEUDORES POR GRADO $year", 0, 0, 'C');
        //        $this->Ln(10);
    }
}

$pdf = new nPDF();
$pdf->SetTitle($lang->translation('T-Shirts Size') . ' ' . $year);
$pdf->Fill();
$pdf->AliasNbPages();

$c = 0;
$c2 = 0;
$grado = '';
$z1 = 0;
$z2 = 0;
$z3 = 0;
$z4 = 0;
$z5 = 0;
$z6 = 0;
$z7 = 0;
$z8 = 0;
$z9 = 0;
$z10 = 0;
$stu7 = DB::table('year')
    ->whereRaw("year='$year' and grado != '01-' and grado != '13-'")->orderBy('grado, apellidos')->get();
foreach ($stu7 as $estu) {
    if ($grado != $estu->grado) {
        $c = 0;
        $x = 0;
        $grado = $estu->grado;
        $pdf->Ln(10);
        if ($c2 == 2) {
            $pdf->Cell(15, 5, '', 1, 0, 'R', true);
            $pdf->Cell(30, 5, 'Size', 1, 0, 'C', true);
            $pdf->Cell(25, 5, 'Cantidad', 1, 1, 'C', true);
            if ($z1 > 0) {
                $x = $x + 1;
                $pdf->Cell(15, 5, $x, 1, 0, 'R', true);
                $pdf->Cell(30, 5, '2-4', 1, 0, 'L');
                $pdf->Cell(25, 5, $z1, 1, 1, 'C');
            }
            if ($z2 > 0) {
                $x = $x + 1;
                $pdf->Cell(15, 5, $x, 1, 0, 'R', true);
                $pdf->Cell(30, 5, '6-8', 1, 0, 'L');
                $pdf->Cell(25, 5, $z2, 1, 1, 'C');
            }
            if ($z3 > 0) {
                $x = $x + 1;
                $pdf->Cell(15, 5, $x, 1, 0, 'R', true);
                $pdf->Cell(30, 5, '10-12', 1, 0, 'L');
                $pdf->Cell(25, 5, $z3, 1, 1, 'C');
            }
            if ($z4 > 0) {
                $x = $x + 1;
                $pdf->Cell(15, 5, $x, 1, 0, 'R', true);
                $pdf->Cell(30, 5, '14-16', 1, 0, 'L');
                $pdf->Cell(25, 5, $z4, 1, 1, 'C');
            }
            if ($z5 > 0) {
                $x = $x + 1;
                $pdf->Cell(15, 5, $x, 1, 0, 'R', true);
                $pdf->Cell(30, 5, 'Small', 1, 0, 'L');
                $pdf->Cell(25, 5, $z5, 1, 1, 'C');
            }
            if ($z6 > 0) {
                $x = $x + 1;
                $pdf->Cell(15, 5, $x, 1, 0, 'R', true);
                $pdf->Cell(30, 5, 'Medium', 1, 0, 'L');
                $pdf->Cell(25, 5, $z6, 1, 1, 'C');
            }
            if ($z7 > 0) {
                $x = $x + 1;
                $pdf->Cell(15, 5, $x, 1, 0, 'R', true);
                $pdf->Cell(30, 5, 'Large', 1, 0, 'L');
                $pdf->Cell(25, 5, $z7, 1, 1, 'C');
            }
            if ($z8 > 0) {
                $x = $x + 1;
                $pdf->Cell(15, 5, $x, 1, 0, 'R', true);
                $pdf->Cell(30, 5, 'X-Large', 1, 0, 'L');
                $pdf->Cell(25, 5, $z8, 1, 1, 'C');
            }
            if ($z9 > 0) {
                $x = $x + 1;
                $pdf->Cell(15, 5, $x, 1, 0, 'R', true);
                $pdf->Cell(30, 5, 'XX-Large', 1, 0, 'L');
                $pdf->Cell(25, 5, $z9, 1, 1, 'C');
            }
            if ($z10 > 0) {
                $x = $x + 1;
                $pdf->Cell(15, 5, $x, 1, 0, 'R', true);
                $pdf->Cell(30, 5, 'XXX-Large', 1, 0, 'L');
                $pdf->Cell(25, 5, $z10, 1, 1, 'C');
            }
        }
        $z1 = 0;
        $z2 = 0;
        $z3 = 0;
        $z4 = 0;
        $z5 = 0;
        $z6 = 0;
        $z7 = 0;
        $z8 = 0;
        $z9 = 0;
        $z10 = 0;
        $c2 = 2;
        $pdf->AddPage();
        $pdf->Cell(0, 5, 'T-Shirts Pagadas por grado resumen ' . $grado . ' / ' . $year, 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Times', 'B', 12);
        $pdf->Cell(15, 5, '', 1, 0, 'C', true);
        $pdf->Cell(60, 5, 'Apellidos', 1, 0, 'C', true);
        $pdf->Cell(55, 5, 'Nombre', 1, 0, 'C', true);
        $pdf->Cell(30, 5, 'Size', 1, 0, 'C', true);
        $pdf->Cell(25, 5, 'Cantidad', 1, 1, 'C', true);
    }
    $st7 = DB::table('compras')
        ->whereRaw("deliveryTo = '$estu->ss' and shopping=1 and year='$year'")->orderBy('date')->get();
    foreach ($st7 as $camisa) {
        $c = $c + 1;
        $pdf->SetFont('Times', '', 11);
        $pdf->Cell(15, 4, $c, 1, 0, 'R');
        $pdf->Cell(60, 4, $estu->apellidos, 1, 0, 'L');
        $pdf->Cell(55, 4, $estu->nombre, 1, 0, 'L');
        $a = 0;
        $students7 = DB::table('compras_detalle')
            ->whereRaw("id_compra='$camisa->id'")->orderBy('size')->get();
        foreach ($students7 as $camisa2) {
            if ($a == 1) {
                $pdf->Cell(130, 4, '', 0, 0, 'R');
            }
            $pdf->Cell(30, 4, $camisa2->size, 1, 0, 'L');
            $pdf->Cell(25, 4, $camisa2->amount, 1, 1, 'C');
            if ($camisa2->size == '2-4') {
                $z1 = $z1 + $camisa2->amount;
            }
            if ($camisa2->size == '6-8') {
                $z2 = $z2 + $camisa2->amount;
            }
            if ($camisa2->size == '10-12') {
                $z3 = $z3 + $camisa2->amount;
            }
            if ($camisa2->size == '14-16') {
                $z4 = $z4 + $camisa2->amount;
            }
            if ($camisa2->size == 'Small') {
                $z5 = $z5 + $camisa2->amount;
            }
            if ($camisa2->size == 'Medium') {
                $z6 = $z6 + $camisa2->amount;
            }
            if ($camisa2->size == 'Large') {
                $z7 = $z7 + $camisa2->amount;
            }
            if ($camisa2->size == 'X-Large') {
                $z8 = $z8 + $camisa2->amount;
            }
            if ($camisa2->size == 'XX-Large') {
                $z9 = $z9 + $camisa2->amount;
            }
            if ($camisa2->size == 'XXX-Large') {
                $z10 = $z10 + $camisa2->amount;
            }
            $a = 1;
        }
    }
}
$pdf->Ln(10);
$pdf->Cell(15, 5, '', 1, 0, 'R', true);
$pdf->Cell(30, 5, 'Size', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'Cantidad', 1, 1, 'C', true);
$x = 0;
if ($z1 > 0) {
    $x = $x + 1;
    $pdf->Cell(15, 5, $x, 1, 0, 'R', true);
    $pdf->Cell(30, 5, '2-4', 1, 0, 'L');
    $pdf->Cell(25, 5, $z1, 1, 1, 'C');
}
if ($z2 > 0) {
    $x = $x + 1;
    $pdf->Cell(15, 5, $x, 1, 0, 'R', true);
    $pdf->Cell(30, 5, '6-8', 1, 0, 'L');
    $pdf->Cell(25, 5, $z2, 1, 1, 'C');
}
if ($z3 > 0) {
    $x = $x + 1;
    $pdf->Cell(15, 5, $x, 1, 0, 'R', true);
    $pdf->Cell(30, 5, '10-12', 1, 0, 'L');
    $pdf->Cell(25, 5, $z3, 1, 1, 'C');
}
if ($z4 > 0) {
    $x = $x + 1;
    $pdf->Cell(15, 5, $x, 1, 0, 'R', true);
    $pdf->Cell(30, 5, '14-16', 1, 0, 'L');
    $pdf->Cell(25, 5, $z4, 1, 1, 'C');
}
if ($z5 > 0) {
    $x = $x + 1;
    $pdf->Cell(15, 5, $x, 1, 0, 'R', true);
    $pdf->Cell(30, 5, 'Small', 1, 0, 'L');
    $pdf->Cell(25, 5, $z5, 1, 1, 'C');
}
if ($z6 > 0) {
    $x = $x + 1;
    $pdf->Cell(15, 5, $x, 1, 0, 'R', true);
    $pdf->Cell(30, 5, 'Medium', 1, 0, 'L');
    $pdf->Cell(25, 5, $z6, 1, 1, 'C');
}
if ($z7 > 0) {
    $x = $x + 1;
    $pdf->Cell(15, 5, $x, 1, 0, 'R', true);
    $pdf->Cell(30, 5, 'Large', 1, 0, 'L');
    $pdf->Cell(25, 5, $z7, 1, 1, 'C');
}
if ($z8 > 0) {
    $x = $x + 1;
    $pdf->Cell(15, 5, $x, 1, 0, 'R', true);
    $pdf->Cell(30, 5, 'X-Large', 1, 0, 'L');
    $pdf->Cell(25, 5, $z8, 1, 1, 'C');
}
if ($z9 > 0) {
    $x = $x + 1;
    $pdf->Cell(15, 5, $x, 1, 0, 'R', true);
    $pdf->Cell(30, 5, 'XX-Large', 1, 0, 'L');
    $pdf->Cell(25, 5, $z9, 1, 1, 'C');
}
if ($z10 > 0) {
    $x = $x + 1;
    $pdf->Cell(15, 5, $x, 1, 0, 'R', true);
    $pdf->Cell(30, 5, 'XXX-Large', 1, 0, 'L');
    $pdf->Cell(25, 5, $z10, 1, 1, 'C');
}

$pdf->Output();
