<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\Student;
use App\Models\WeeklyPlan3;
use App\Pdfs\Plans\WeeklyPlan3PDF;
use Classes\Session;

Session::is_logged();

$planId = $_GET['id'] ?? null;

if (!$planId) {
    die('Plan ID required');
}

$weeklyPlan = WeeklyPlan3::find($planId);

if (!$weeklyPlan) {
    die('Plan not found');
}


$pdf = new WeeklyPlan3PDF($weeklyPlan);
$pdf->generate();
$pdf->Output();
