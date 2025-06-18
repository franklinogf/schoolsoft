<?php
require_once '../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ['T-Shirts Pagadas', 'Paid T-Shirts'],
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
        $this->Cell(0, 5, 'T-Shirts Pagadas ' . $year, 0, 1, 'C');
        $this->Ln(15);
    }
}

$pdf = new nPDF();
$pdf->AddPage();
$pdf->SetTitle($lang->translation('T-Shirts Pagadas') . ' ' . $year);
$pdf->Fill();
$pdf->AliasNbPages();


$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(20, 5, '', 1, 0, 'C', true);
$pdf->Cell(40, 5, 'Size', 1, 0, 'C', true);
$pdf->Cell(30, 5, 'Cantidad', 1, 0, 'C', true);
$pdf->Cell(30, 5, 'Precio', 1, 0, 'C', true);
$pdf->Cell(30, 5, 'Gran Total', 1, 1, 'C', true);

$students7 = DB::table('compras_detalle')->select("DISTINCT size")->where([
    ['price', 12.00],
    ['year', $year]
])->orderBy('size')->get();


$c = 0;
$gt = 0;
foreach ($students7 as $camisa) {
    $students2 = DB::table('compras_detalle')->where([
        ['price', 12.00],
        ['size', $camisa->size],
        ['year', $year]
    ])->orderBy('size')->get();
    $ct = 0;
    foreach ($students2 as $student) {
        $ct = $ct + $student->amount;
    }
    $c = $c + 1;
    $gt = $gt + $ct;
    $pdf->SetFont('Times', '', 11);
    $pdf->Cell(20, 5, $c, 1, 0, 'R');
    $pdf->Cell(40, 5, $camisa->size, 1, 0, 'L');
    $pdf->Cell(30, 5, $ct, 1, 0, 'R');
    $pdf->Cell(30, 5, '$12.00', 1, 0, 'R');
    $pdf->Cell(30, 5, number_format($ct * 12, 2), 1, 1, 'R');
}

$pdf->Cell(20, 5, '', 0, 0, 'R');
$pdf->Cell(40, 5, 'Gran Total:', 1, 0, 'R', true);
$pdf->Cell(30, 5, $gt, 1, 0, 'R');
$pdf->Cell(30, 5, '', 0, 0, 'R');
$pdf->Cell(30, 5, number_format($gt * 12, 2), 1, 1, 'R');

$pdf->Output();
