<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\Teacher;
use App\Models\WorkPlan;
use App\Pdfs\Plans\WorkPlan3PDF;
use Classes\Session;

Session::is_logged();

$teacher = Teacher::find(Session::id());
$planId = $_GET['plan'] ?? null;

if (!$planId) {
    die('Plan ID no proporcionado');
}

$workPlan = WorkPlan::find($planId);

if (!$workPlan || $workPlan->id != $teacher->id) {
    die('Plan no encontrado o no autorizado');
}

$pdf = new WorkPlan3PDF($workPlan);
$pdf->generate();
$pdf->Output();
