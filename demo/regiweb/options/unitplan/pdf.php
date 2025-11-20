<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\UnitPlan;
use App\Pdfs\Plans\UnitPlanPDF;
use Classes\Session;

Session::is_logged();

$planId = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$planId) {
    die('Error: ID de plan no proporcionado');
}

$unitPlan = UnitPlan::find($planId);

if (!$unitPlan) {
    die('Error: Plan de unidad no encontrado');
}
// Generar PDF usando la clase UnitPlanPDF
$pdf = new UnitPlanPDF($unitPlan);
$pdf->generate();
$pdf->Output();
