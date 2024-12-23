<?php
require_once '../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ["LISTA PRESUPUESTO AÑO", "YEAR BUDGET LIST"],
    ['DESCRIPCION', 'DESCRIPTION'],
    ['CODIGO', 'CODE'],
    ['PAGOS', 'PAYS'],
    ['DEUDAS', 'DEBTS'],
    ['BALANCES', 'BALANCES'],
    ['Pagina ', 'Page '],
    ['T. PAGO', 'TIPE PAY'],
    ['DESDE', 'FROM'],
    ['HASTA', 'TO'],
    ['GRAN TOTAL:', 'GRAND TOTAL:'],
    ['COMENTARIO', 'COMMENT'],
    ['APELLIDOS', 'SURNAME'],
    ['NOMBRE', 'NAME'],
    ['Grado: ', 'Grade: '],
    ['GRADO', 'GRADE'],
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
//require('../../fpdf16/fpdf.php');
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
        $this->Cell(30, 10, $lang->translation('LISTA PRESUPUESTO AÑO') . ' ' . $year, 0, 1, 'C');
        $this->Ln(6);
        $a2 = $_POST['desc1'] ?? '';
        $b2 = $_POST['desc2'] ?? '';
        $c2 = $_POST['desc3'] ?? '';

        $tabla1 = DB::table('presupuesto')->where([
            ['year', $year],
            ['codigo', $a2]
        ])->orderBy('codigo')->first();
        $tabla2 = DB::table('presupuesto')->where([
            ['year', $year],
            ['codigo', $b2]
        ])->orderBy('codigo')->first();
        $tabla3 = DB::table('presupuesto')->where([
            ['year', $year],
            ['codigo', $c2]
        ])->orderBy('codigo')->first();

        $this->Cell(10, 5, '', 1, 0, 'C', true);
        $this->Cell(50, 5, $lang->translation('APELLIDOS'), 1, 0, 'C', true);
        $this->Cell(40, 5, $lang->translation('NOMBRES'), 1, 0, 'C', true);
        $this->Cell(18, 5, $lang->translation('GRADO'), 1, 0, 'C', true);
        $this->Cell(50, 5, $tabla1->descripcion ?? '', 1, 0, 'C', true);
        $this->Cell(50, 5, $tabla2->descripcion ?? '', 1, 0, 'C', true);
        $this->Cell(50, 5, $tabla3->descripcion ?? '', 1, 1, 'C', true);
    }
    function Footer()
    {
        global $lang;
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, $lang->translation('Pagina ') . $this->PageNo() . '/{nb} ' . date('m-d-Y'), 0, 0, 'C');
    }
}

$pdf = new nPDF();
$pdf->Fill();
$pdf->SetTitle($lang->translation('LISTA PRESUPUESTO AÑO') . ' ' . $year);
$pdf->AliasNbPages();
$pdf->AddPage('L');
$pdf->SetFont('Times', '', 11);
$tabla1 = DB::table('year')->where('year', $year)->orderBy('apellidos, nombre DESC')->get();
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
    $a1 = $_POST['desc1'] ?? '';
    $b1 = $_POST['desc2'] ?? '';
    $c1 = $_POST['desc3'] ?? '';

    $tabla2 = DB::table('pagos')->where([
        ['ss', $row7->ss],
        ['year', $year],
        ['baja', '']
    ])->orderBy('id')->get();
    foreach ($tabla2 as $row8) {
        if ($row8->codigo == $a1 or $row8->codigo == $_POST['ca1'] or $row8->codigo == $_POST['ca2'] or $row8->codigo == $_POST['ca3'] or $row8->codigo == $_POST['ca4']) {
            $d1 = $d1 + $row8->deuda;
        }
        if ($row8->codigo == $b1 or $row8->codigo == $_POST['cb1'] or $row8->codigo == $_POST['cb2'] or $row8->codigo == $_POST['cb3'] or $row8->codigo == $_POST['cb4']) {
            $d2 = $d2 + $row8->deuda;
        }
        if ($row8->codigo == $c1 or $row8->codigo == $_POST['cc1'] or $row8->codigo == $_POST['cc2'] or $row8->codigo == $_POST['cc3'] or $row8->codigo == $_POST['cc4']) {
            $d3 = $d3 + $row8->deuda;
        }
    }
    if ($d1 > 0 or $d2 > 0 or $d3 > 0) {
        $a = $a + 1;
        $dt1 = $dt1 + $d1;
        $dt2 = $dt2 + $d2;
        $dt3 = $dt3 + $d3;
        $pdf->Cell(10, 5, $a, 1, 0, 'R');
        $pdf->Cell(50, 5, $row7->apellidos, 1, 0, 'L');
        $pdf->Cell(40, 5, $row7->nombre, 1, 0, 'L');
        $pdf->Cell(18, 5, $row7->grado, 1, 0, 'C');
        $pdf->Cell(32, 5, number_format($d1, 2), 'LTB', 0, 'R');
        $pdf->Cell(18, 5, '', 'RTB', 0, 'C');
        $pdf->Cell(32, 5, number_format($d2, 2), 'LTB', 0, 'R');
        $pdf->Cell(18, 5, '', 'RTB', 0, 'C');
        $pdf->Cell(32, 5, number_format($d3, 2), 'LTB', 0, 'R');
        $pdf->Cell(18, 5, '', 'RTB', 1, 'C');
    }
}
$pdf->Cell(118, 5, $lang->translation('GRAN TOTAL:'), 1, 0, 'R');
$pdf->Cell(32, 5, number_format($dt1, 2), 'LTB', 0, 'R');
$pdf->Cell(18, 5, '', 'RTB', 0, 'C');
$pdf->Cell(32, 5, number_format($dt2, 2), 'LTB', 0, 'R');
$pdf->Cell(18, 5, '', 'RTB', 0, 'C');
$pdf->Cell(32, 5, number_format($dt3, 2), 'LTB', 0, 'R');
$pdf->Cell(18, 5, '', 'RTB', 1, 'C');
$pdf->Output();
