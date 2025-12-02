<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\UnitPlan;
use App\Pdfs\Plans\UnitPlanPDF;
use Classes\Session;


Session::is_logged();


$planId = $_GET['id'] ?? null;

if (!$planId) {
    die('Plan ID no proporcionado');
}

$plan = UnitPlan::find($planId);

if (!$plan) {
    die('Plan no encontrado o no autorizado');
}

$pdf = new UnitPlanPDF($plan);
$pdf->generate();
$pdf->Output();
