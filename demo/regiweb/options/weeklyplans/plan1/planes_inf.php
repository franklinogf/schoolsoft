<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\WeeklyPlan;
use App\Pdfs\Plans\WeeklyPlan1PDF;
use Classes\Session;

Session::is_logged();

$planId = $_GET['plan'] ?? null;

if (!$planId) {
    die('Plan ID no proporcionado');
}

$weeklyPlan = WeeklyPlan::find($planId);

if (!$weeklyPlan) {
    http_response_code(404);
    exit('Plan no encontrado');
}

$pdf = new WeeklyPlan1PDF($weeklyPlan);
$pdf->generate();
$pdf->Output();
