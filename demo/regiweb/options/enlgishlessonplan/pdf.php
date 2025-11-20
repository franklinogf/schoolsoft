<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\EnglishLessonPlan;
use Classes\PDF\EnglishLessonPlanPDF;
use Classes\Session;

Session::is_logged();

$planID = $_POST['id'] ?? $_GET['id'] ?? null;

if (!$planID) {
    die('Plan ID is required');
}

$plan = EnglishLessonPlan::findOrFail($planID);

// Create PDF and generate
$pdf = new EnglishLessonPlanPDF($plan);
$pdf->generate();
$pdf->Output();
