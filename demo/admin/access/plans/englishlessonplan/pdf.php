<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\EnglishLessonPlan;
use App\Pdfs\Plans\EnglishLessonPlanPDF;
use Classes\Session;


Session::is_logged();


$planId = $_GET['id'] ?? null;

if (!$planId) {
    die('Plan ID no proporcionado');
}

$plan = EnglishLessonPlan::find($planId);

if (!$plan) {
    die('Plan no encontrado o no autorizado');
}

$pdf = new EnglishLessonPlanPDF($plan);
$pdf->generate();
$pdf->Output();
