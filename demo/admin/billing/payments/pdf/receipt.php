<?php

use Classes\PDF;
use Classes\Controllers\Student;
use Classes\DataBase\DB;
use Classes\Email;

require_once '../../../../app.php';
$fmt = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
$paymentTypes = [
    '1' => 'Efectivo',
    '2' => 'Cheque',
    '3' => 'ATH',
    '4' => 'Tarjeta Credito',
    '5' => 'Giro',
    '6' => 'Nomina',
    '7' => 'Banco',
    '8' => 'Pago Directo',
    '9' => 'Tele Pago',
    '10' => 'Paypal',
    '11' => 'Beca',
    '12' => 'ATH Movil',
    '13' => 'Credito a Cuenta',
    '14' => 'Virtual Terminal',
];
$months = __LANG === 'es' ?
    ['07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre', '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio']
    : ['07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December', '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June'];
$type = $_GET['type'] ?? null;
$transaction = $_GET['transaction'] ?? null;
$emails = [];
if (isset($_GET['email'])) {
    $emails = array_filter(explode(',', $_GET['email']));
}
if (isset($_GET['newEmail'])) {
    $emails[] = $_GET['newEmail'];
}
$student = new Student();
$title = "Recibo de pago";
$pdf = new PDF();

if ($type !== '1') {
    $pdf->useHeader(false);
    $pdf->useFooter(false);
    $pdf->AddPage('', [70, 120]);
    $pdf->SetMargins(2, 2);
} else {
    $pdf->AddPage();
}

$pdf->SetTitle($title);
$pdf->Fill();

$charge = DB::table('pagos')->where('mt', $transaction)->first();
$students = $student->findById($charge->id);

$pdf->SetFont('Times', 'B', $type !== '1' ? 8 : 13);
$pdf->Cell(0, 5, strtoupper($title), 0, 1, 'C');
$pdf->SetFont('Arial', 'B', $type !== '1' ? 5 : 12);
$pdf->Ln(5);
$pdf->Cell($type !== '1' ? 40 : 77, 5, 'Nombre del Estudiante', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'Grado', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', $type !== '1' ? 5 : 11);

foreach ($students as $student) {
    $pdf->Cell($type !== '1' ? 40 : 77, 5, "$student->apellidos $student->nombre", 0, 0);
    $pdf->Cell(15, 5, $student->grado, 0, 1, 'C');
}
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', $type !== '1' ? 5 : 10);
$pdf->Cell(0, 5, "Recibo #{$charge->rec}", 0, 1);
$pdf->Cell($type !== '1' ? 20 : 50, 5, 'Descripción', 1, 0, 'C', true);
$pdf->Cell($type !== '1' ? 7 : 15, 5, 'Grado', 1, 0, 'C', true);
$pdf->Cell($type !== '1' ? 10 : 15, 5, 'Pago', 1, 0, 'C', true);
$pdf->Cell($type !== '1' ? 9 : 20, 5, 'Fecha', 1, 0, 'C', true);
$pdf->Cell($type !== '1' ? 11 : 25, 5, 'Mes pagado', 1, 0, 'C', true);
$pdf->Cell($type !== '1' ? 10 : 29, 5, 'TDP', 1, 1, 'C', true);

$total = $charge->pago;



$pdf->SetFont('Arial', '', $type !== '1' ? 4 : 9);
$pdf->Cell($type !== '1' ? 20 : 50, 5, $charge->desc1, 1);
$pdf->Cell($type !== '1' ? 7 : 15, 5, $charge->grado, 1, 0, 'C');
$pdf->Cell($type !== '1' ? 10 : 15, 5, $charge->pago === 0 ? '' : $fmt->formatCurrency($charge->pago, 'USD'), 1, 0, 'R');
$pdf->Cell($type !== '1' ? 9 : 20, 5, $charge->fecha_p === '0000-00-00' ? '' : $charge->fecha_p, 1, 0, 'C');
$pdf->Cell($type !== '1' ? 11 : 25, 5, $months[date("m", strtotime($charge->fecha_d))], 1, 0, 'C');
$pdf->Cell($type !== '1' ? 10 : 29, 5, $charge->tdp !== '' ? $paymentTypes[$charge->tdp] : '', 1, 1, 'C');



if ($type !== '1') {
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 5, 'Total Pagado', 0, 1, 'C');
    $pdf->Cell(0, 5, $fmt->formatCurrency($total, 'USD'), 0, 1, 'C');
    $pdf->Ln(15);
    $pdf->Cell(12);
    $pdf->Cell(40, 5, 'Firma', 'T', 0, 'C');
} else {
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(65, 5, 'Total Pagado', 1, 0, 'R', true);
    $pdf->Cell(18, 5, $fmt->formatCurrency($total, 'USD'), 1, 1, 'R', true);
}

$pdf->Output();

if (empty($emails)) {
    exit;
}
$file = $pdf->Output('S');


Email::to($emails)
    ->subject('Recibo de pago')
    ->body("Adjunto el recibo de pago de la transacción $transaction")
    ->attach($file, "recibo_de_pago_$transaction.pdf")
    ->send();
