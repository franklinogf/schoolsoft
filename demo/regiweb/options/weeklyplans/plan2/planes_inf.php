<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\Teacher;
use App\Models\WeeklyPlan2;
use App\Pdfs\Plans\WeeklyPlan2PDF;
use Classes\Session;

Session::is_logged();

$teacher = Teacher::find(Session::id());
$planId = $_GET['plan'] ?? null;

if (!$planId) {
    die('Plan ID no proporcionado');
}

$weeklyPlan = WeeklyPlan2::find($planId);

if (!$weeklyPlan || $weeklyPlan->id != $teacher->id) {
    die('Plan no encontrado o no autorizado');
}

$pdf = new WeeklyPlan2PDF($weeklyPlan, $teacher);
$pdf->generate();
$pdf->Output('I', 'Plan_Semanal_2_' . $weeklyPlan->id2 . '.pdf');
