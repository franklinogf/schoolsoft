<?php

use Classes\PDF;
use Classes\Controllers\Student;
use Classes\DataBase\DB;
use Classes\Email;

require_once __DIR__ . '/../../../../app.php';
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
    '15' => 'Acuden-Contigo',
    '16' => 'Acuden-Vales',
    '17' => 'VA Prog',
    '18' => 'Colegio',
    '19' => 'Pago APP',
];
$type = $_GET['type'] ?? null;
$accountId = $_GET['accountId'] ?? null;
$month = $_GET['month'] ?? date('m');
$student = new Student();
$title = $type === '5' ? 'Estado de depositos' : 'Estado de cuentas';
$emails = [];
if (isset($_GET['email'])) {
    $emails = array_filter(explode(',', $_GET['email']));
}
if (isset($_GET['newEmail'])) {
    $emails[] = $_GET['newEmail'];
}

$pdf = new PDF();
$pdf->SetTitle($title);
$pdf->Fill();

$students = $student->findById($accountId);
$where = [
    ['id', $accountId],
    ['year', $student->year()]
];
if ($type === '5') {
    $deposits = DB::table('depositos')->where($where)->orderBy('ss, fecha')->get();
} else {

    $orderBy = '';
    if ($type === '1') {
        $orderBy = 'fecha_d, fecha_p, grado';
    } elseif ($type === '2') {
        array_push($where, ['pago', '>', 0]);
        $orderBy = 'fecha_d, grado';
    } elseif ($type === '3') {
        $orderBy = 'fecha_d, codigo, grado';
        array_push($where, ['MONTH(fecha_d)', $month]);
    } elseif ($type === '4') {
        $orderBy = 'codigo, grado, fecha_d, fecha_p';
    }
    $charges = DB::table('pagos')->where($where)->orderBy($orderBy)->get();
}



$pdf->AddPage();
$pdf->SetFont('Times', 'B', 13);
$pdf->Cell(0, 5, strtoupper($title), 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Ln(5);
$pdf->Cell(77, 5, 'Nombre del Estudiante', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'Grado', 1, 0, 'C', true);
$pdf->Cell(35, 5, 'Número de Cta:', 1, 0, 'L', true);
$pdf->Cell(20, 5, $accountId, 1, 0, 'C');
$pdf->Cell(17, 5, 'Fecha', 1, 0, 'L', true);
$pdf->Cell(25, 5, date("m/d/Y"), 1, 1, 'C');
$pdf->SetFont('Arial', '', 11);

foreach ($students as $student) {
    $pdf->Cell(77, 5, "$student->apellidos $student->nombre", 0, 0);
    $pdf->Cell(15, 5, $student->grado, 0, 1, 'C');
}
$pdf->Ln(5);
if ($type === '5') {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(15, 5, '#', 1, 0, 'C', true);
    $pdf->Cell(35, 5, 'GRADO', 1, 0, 'C', true);
    $pdf->Cell(35, 5, 'FECHA', 1, 0, 'C', true);
    $pdf->Cell(35, 5, 'CANTIDAD', 1, 1, 'C', true);
} else {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(8, 5, 'ID', 1, 0, 'C', true);
    $pdf->Cell(15, 5, 'GRADO', 1, 0, 'C', true);
    $pdf->Cell(50, 5, 'DESCRIPCIÓN', 1, 0, 'C', true);
    $pdf->Cell(18, 5, 'FECHA D.', 1, 0, 'C', true);
    $pdf->Cell(18, 5, 'DEUDA', 1, 0, 'C', true);
    $pdf->Cell(18, 5, 'FECHA P.', 1, 0, 'C', true);
    $pdf->Cell(18, 5, 'PAGO', 1, 0, 'C', true);
    $pdf->Cell(15, 5, 'RECIBO', 1, 0, 'C', true);
    $pdf->Cell(29, 5, 'TDP', 1, 1, 'C', true);
}
$totals = [
    'debt' => 0,
    'payments' => 0
];
if ($type === '5') {
    foreach ($deposits as $index => $deposit) {
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(15, 5, $index + 1, 1, 0, 'C');
        $pdf->Cell(35, 5, $deposit->grado, 1, 0, 'C');
        $pdf->Cell(35, 5, $deposit->fecha, 1, 0, 'C');
        $pdf->Cell(35, 5, $deposit->cantidad === 0 ? '' : $fmt->formatCurrency($deposit->cantidad, 'USD'), 1, 1, 'C');
    }
} else {

    foreach ($charges as $charge) {
        $totals['debt'] += $charge->deuda;
        $totals['payments'] += $charge->pago;
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(8, 5, $charge->codigo, 1, 0, 'C');
        $pdf->Cell(15, 5, $charge->grado, 1, 0, 'C');
        $pdf->Cell(50, 5, $charge->desc1, 1);
        $pdf->Cell(18, 5, $charge->fecha_d === '0000-00-00' ? '' : $charge->fecha_d, 1, 0, 'C');
        $pdf->Cell(18, 5, $charge->deuda === 0 ? '' : $fmt->formatCurrency($charge->deuda, 'USD'), 1, 0, 'R');
        $pdf->Cell(18, 5, $charge->fecha_p === '0000-00-00' ? '' : $charge->fecha_p, 1, 0, 'C');
        $pdf->Cell(18, 5, $charge->pago === 0 ? '' : $fmt->formatCurrency($charge->pago, 'USD'), 1, 0, 'R');
        $pdf->Cell(15, 5, $charge->rec === 0 ? '' : $charge->rec, 1, 0, 'C');
        $pdf->Cell(29, 5, $charge->tdp !== '' ? $paymentTypes[$charge->tdp] : '', 1, 1, 'C');
    }
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(91, 5, 'Gran total', 1, 0, 'R', true);
    $pdf->Cell(18, 5, $fmt->formatCurrency($totals['debt'], 'USD'), 1, 0, 'C', true);
    $pdf->Cell(18, 5, 'Total', 1, 0, 'C', true);
    $pdf->Cell(18, 5, $fmt->formatCurrency($totals['payments'], 'USD'), 1, 0, 'C', true);
    $pdf->Cell(15, 5, 'Balance', 1, 0, 'C', true);
    $pdf->Cell(29, 5, $fmt->formatCurrency($totals['debt'] - $totals['payments'], 'USD'), 1, 1, 'C', true);
}


$pdf->Output();


if (empty($emails)) {
    exit;
}

$file = $pdf->saveAsAttachment('statements');


Email::to($emails)
    ->subject('Estado de cuenta')
    ->body("Adjunto el estado de cuenta de la cuenta $accountId")
    ->attach($file, "estado_de_cuenta_$accountId.pdf")
    ->send();
