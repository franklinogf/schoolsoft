<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\WeeklyPlan;
use App\Models\WeeklyPlan2;
use App\Models\WeeklyPlan3;
use App\Pdfs\Plans\WeeklyPlan1PDF;
use App\Pdfs\Plans\WeeklyPlan2PDF;
use App\Pdfs\Plans\WeeklyPlan3PDF;
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

if (!in_array($planNumber, ['1', '2', '3'])) {
    die('Plan no válido.');
}



$weeklyPlan =
    match ($planNumber) {
        '1' => WeeklyPlan::find($planId),
        '2' => WeeklyPlan2::find($planId),
        '3' => WeeklyPlan3::find($planId),
        default => null,
    };


if (!$weeklyPlan) {
    die('Plan no encontrado o no autorizado');
}

$planPdf =  match ($planNumber) {
    '1' => WeeklyPlan1PDF::class,
    '2' => WeeklyPlan2PDF::class,
    '3' => WeeklyPlan3PDF::class,
    default => null,
};


if (!$planPdf) {
    die('PDF del plan no encontrado o no autorizado');
}


$pdf = new $planPdf($weeklyPlan);
$pdf->generate();
$pdf->Output();
