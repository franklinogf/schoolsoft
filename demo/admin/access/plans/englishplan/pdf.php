<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\EnglishPlan;
use App\Pdfs\Plans\EnglishPlanPDF;
use Classes\Session;


Session::is_logged();


$planId = $_GET['id'] ?? null;

if (!$planId) {
    die('Plan ID no proporcionado');
}

$plan = EnglishPlan::find($planId);

if (!$plan) {
    die('Plan no encontrado o no autorizado');
}


$pdf = new EnglishPlanPDF($plan);
$pdf->generate();
$pdf->Output();
