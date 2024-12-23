<?php
require_once '../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ["Lista de deudores 30, 60, 90 ", "List of debtors 30, 60, 90 "],
    ['NOMBRE ESTUDIANTES', 'STUDENT NAMES'],
    ['Pagina ', 'Page '],
    ['Familia', 'Family'],
    ['Código', 'Code'],
    ['Descripción', 'Description'],
    ['Deudas', 'Debts'],
    ['Pagos', 'Payments'],
    ['Balances', 'Balances'],
    ['LISTA DE DEUDORES ', 'LIST OF DEBTORS '],
    ['TOTALES: ', 'TOTALS: '],
    ['Total', 'Total'],
    ['DESCRIPCION', 'DESCRIPTION'],
    ['CODIGO', 'CODE'],
    ['CANTIDAD', 'QUANTITY'],
    ['REPORTE DE PAGOS', 'PAYMENT REPORT'],
    ['RESUMEN POR TRANSACCION', 'SUMMARY BY TRANSACTION'],
    ['PROXIMO CURSO ESCOLAR', 'NEXT SCHOOL YEAR'],
    ['DESDE ', 'FROM '],
    ['HASTA ', 'TO '],
    ['Cantidad Pagada', 'Amount Paid'],
    ['ES', 'IN'],
]);

$school = new School(Session::id());
$year = $school->info('year2');

class nPDF extends PDF
{
    function Header()
    {
        global $year;
        global $lang;
        parent::header();
        $sp = 80;
        $len = $lang->translation('ES');
        $this->Cell($sp);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(30, 5, $lang->translation('REPORTE DE PAGOS ') . $year, 0, 1, 'C');
        list($y1, $y2) = explode("-", $year);
        $this->Ln(5);
        $this->Cell($sp);
        if ($len == 'ES') {
            $this->Cell(30, 5, $lang->translation('DESDE ') . '08/01/20' . $y1 . $lang->translation(' HASTA ') . '07/31/20' . $y2, 0, 1, 'C');
        } else {
            $this->Cell(30, 5, 'FROM ' . '20' . $y1 . '/08/01' . ' ' . 'TO ' . '20' . $y2 . '/07/31', 0, 1, 'C');
        }
        $this->Ln(10);
        $this->SetFont('Arial', '', 11);
        $this->Cell(90, 5, $lang->translation('RESUMEN POR TRANSACCION'), 0, 0, 'C');
        $this->Cell(15, 5, '', 0, 0, 'C');
        $this->Cell(80, 5, $lang->translation('PROXIMO CURSO ESCOLAR'), 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(5, 5, '', 0, 0, 'C', true);
        $this->Cell(20, 5, $lang->translation('CODIGO'), 1, 0, 'C', true);
        $this->Cell(70, 5, $lang->translation('DESCRIPCION'), 1, 0, 'C', true);
        $this->Cell(20, 5, $lang->translation('CANTIDAD'), 1, 0, 'C', true);
        $this->Cell(60, 5, $lang->translation('CANTIDAD'), 1, 1, 'C', true);
    }

    function Footer()
    {
        global $lang;
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, $lang->translation('Pagina ') . $this->PageNo() . '/{nb}' . ' / ' . date('m-d-Y'), 0, 0, 'C');
    }
}

$pdf = new nPDF("P");
$pdf->SetTitle($lang->translation('Lista de deudores 30, 60, 90 ') . '/ ' . $year);
$pdf->AliasNbPages();
$pdf->Fill();
$pdf->AddPage();
$pdf->SetFont('Times', '', 12);

list($y1, $y2) = explode("-", $year);
$y3 = $y2 + 1;
$y3 = $y2 . '-' . $y3;
$result = DB::table('presupuesto')->where([
    ['codigo', '>', 1],
    ['year', $year]
])->orderBy('codigo')->get();

$a = 0;
$pagt1 = 0;
$pagt2 = 0;
foreach ($result as $row) {
    $pag1 = 0;
    $pag2 = 0;
    $result2 = DB::table('pagos')->where([
        ['codigo', $row->codigo],
        ['year', $year]
    ])->orderBy('codigo')->get();
    foreach ($result2 as $row3) {
        $pag1 = $pag1 + $row3->pago;
    }
    $result3 = DB::table('pagos')->where([
        ['codigo', $row->codigo],
        ['year', $y3]
    ])->orderBy('codigo')->get();
    foreach ($result3 as $row4) {
        $pag2 = $pag2 + $row4->pago;
    }
    $pagt1 = $pagt1 + $pag1;
    $pagt2 = $pagt2 + $pag2;
    if ($pag1 > 0 or $pag2 > 0) {
        $a = $a + 1;
        $pdf->Cell(10, 5, $a, 0, 0, 'R');
        $pdf->Cell(15, 5, $row->codigo, 0, 0, 'R');
        $pdf->Cell(70, 5, $row->descripcion, 0, 0);
        $pdf->Cell(20, 5, number_format($pag1, 2), 0, 0, 'R');
        $pdf->Cell(20, 5, '', 0, 0);
        $pdf->Cell(20, 5, number_format($pag2, 2), 0, 1, 'R');
    }
}

$pdf->Cell(170, 5, '', 'B', 1, 'R');
$pdf->Cell(25, 5, '', 0, 0, 'R');
$pdf->Cell(70, 5, $lang->translation('Cantidad Pagada: '), 0, 0);
$pdf->Cell(20, 5, number_format($pagt1, 2), 0, 0, 'R');
$pdf->Cell(20, 5, '', 0, 0);
$pdf->Cell(20, 5, number_format($pagt2, 2), 0, 1, 'R');
$pdf->Output();
