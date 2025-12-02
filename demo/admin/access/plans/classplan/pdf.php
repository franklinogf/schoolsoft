<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\ClassPlan;
use App\Pdfs\Plans\ClassPlanPDF;
use Classes\Session;


Session::is_logged();


$planId = $_GET['id'] ?? null;

if (!$planId) {
    die('Plan ID no proporcionado');
}

$plan = ClassPlan::find($planId);

if (!$plan) {
    die('Plan no encontrado o no autorizado');
}

$pdf = new ClassPlanPDF($plan);
$pdf->generate();
$pdf->Output();
