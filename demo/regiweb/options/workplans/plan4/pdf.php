<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\Teacher;
use App\Models\WorkPlan4;
use App\Pdfs\Plans\WorkPlan4PDF;
use Classes\Session;

Session::is_logged();

$teacher = Teacher::find(Session::id());
$planId = $_GET['plan'] ?? null;

if (!$planId) {
    die('Plan ID no proporcionado');
}

$plan = WorkPlan4::find($planId);

if (!$plan || $plan->id_profesor != $teacher->id) {
    die('Plan no encontrado o no autorizado');
}

$pdf = new WorkPlan4PDF($plan);
$pdf->generate();
$pdf->Output();
