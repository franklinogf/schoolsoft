<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\WorkPlan;
use App\Models\WorkPlan4;
use App\Pdfs\Plans\WorkPlan1PDF;
use App\Pdfs\Plans\WorkPlan2PDF;
use App\Pdfs\Plans\WorkPlan3PDF;
use App\Pdfs\Plans\WorkPlan4PDF;
use Classes\Session;

Session::is_logged();


$planId = $_GET['id'] ?? null;
$planNumber = $_GET['plan'] ?? null;

if (!$planId) {
    die('Plan ID no proporcionado');
}

if (!$planNumber) {
    die('No ha seleccionado ningún plan.');
}

if (!in_array($planNumber, ['1', '2', '3', '4'])) {
    die('Plan no válido.');
}



$workPlan = $planNumber === '4' ? WorkPlan4::find($planId) : WorkPlan::find($planId);


if (!$workPlan) {
    die('Plan no encontrado o no autorizado');
}

$plans = [
    '1' => WorkPlan1PDF::class,
    '2' => WorkPlan2PDF::class,
    '3' => WorkPlan3PDF::class,
    '4' => WorkPlan4PDF::class,
];

$pdfClass = $plans[$planNumber];
$pdf = new $pdfClass($workPlan);
$pdf->generate();
$pdf->Output();
