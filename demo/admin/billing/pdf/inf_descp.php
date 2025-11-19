<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ["INFORME AÑO ", "YEAR REPORT"],
    ['Hasta', 'Until'],
    ['Pagina ', 'Page '],
    ['Código', 'Code'],
    ['Todos', 'All'],
    ['Selección', 'Select'],
    ['Desde', 'From'],
    ['Tipo de Fechas', 'Type of Dates'],
    ['Lista de codigos', 'Codes list'],
    ['Descripción', 'Description'],
    ['OPCIONES', 'OPTIONS'],
    ['Fechas de Pagos', 'Payment Dates'],
    ['Sin Matrícula', 'No registration'],
    ['Con Matrícula', 'With registration'],
    ['', ''],
    ['Opciones', 'Options'],
]);

$school = new School(Session::id());
$year = $school->info('year2');

$re1 = DB::table('pagos')->select("DISTINCT desc1, codigo")->where([
    ['year', $year],
    ['codigo', $_POST['desc1']]
])->orderBy('desc1')->first();

$re2 = DB::table('pagos')->select("DISTINCT desc1, codigo")->where([
    ['year', $year],
    ['codigo', $_POST['desc2']]
])->orderBy('desc1')->first();

$a2 = $re1->desc1;
$b2 = $re2->desc1;

class nPDF extends PDF
{

    function Header()
    {
        global $year;
        parent::header();
        global $lang;
        global $a2;
        global $b2;

        $sp = 80;
        $this->Cell($sp);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(30, 10, utf8_encode($lang->translation('INFORME AÑO ')) . $year, 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(6, 5, '', 1, 0, 'C', true);
        $this->Cell(40, 5, 'APELLIDOS', 1, 0, 'C', true);
        $this->Cell(40, 5, 'NOMBRES', 1, 0, 'C', true);
        $this->Cell(55, 5, strtoupper($a2), 1, 0, 'C', true);
        $this->Cell(55, 5, strtoupper($b2), 1, 1, 'C', true);
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
$pdf->Fill();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times', '', 10);

$tabla1 = DB::table('year')
    ->whereRaw("year='$year'")->orderBy('apellidos')->get();
$num_res = count($tabla1);

$a = 0;
$d1 = 0;
$d2 = 0;
$d3 = 0;
$dt1 = 0;
$dt2 = 0;
$dt3 = 0;

foreach ($tabla1 as $row7) {
    $d1 = 0;
    $d2 = 0;
    $d3 = 0;
    $tabla2 = DB::table('pagos')
        ->whereRaw("ss = '$row7->ss' AND year ='$year' and baja='' and desc1='$a2'")->get();
    foreach ($tabla2 as $row8) {
        $d1 = $d1 + $row8->deuda;
    }
    $tabla2 = DB::table('pagos')
        ->whereRaw("ss = '$row7->ss' AND year ='$year' and baja='' and desc1='$b2'")->get();
    foreach ($tabla2 as $row8) {
        $d2 = $d2 + $row8->deuda;
    }

    if ($d1 <> 0 or $d2 <> 0) {
        $a = $a + 1;
        $dt1 = $dt1 + $d1;
        $dt2 = $dt2 + $d2;
        $pdf->Cell(6, 5, $a, 1, 0, 'R');
        $pdf->Cell(40, 5, $row7->apellidos, 1, 0, 'L');
        $pdf->Cell(40, 5, $row7->nombre, 1, 0, 'L');
        $pdf->Cell(55, 5, number_format($d1, 2), 'LTB', 0, 'R');
        $pdf->Cell(55, 5, number_format($d2, 2), 'LTBR', 1, 'R');
    }
}
$pdf->Cell(86, 5, 'Gran Total:', 1, 0, 'R');
$pdf->Cell(55, 5, number_format($dt1, 2), 'LTB', 0, 'R');
$pdf->Cell(55, 5, number_format($dt2, 2), 'LTBR', 1, 'R');

$pdf->Output();
