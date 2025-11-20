<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\ClassPlan;
use App\Pdfs\Plans\ClassPlanPDF;
use Classes\Session;

Session::is_logged();

$planID = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$planID) {
    die('Error: ID de plan no proporcionado');
}

$plan = ClassPlan::find($planID);

if (!$plan) {
    die('Error: Plan de clase no encontrado');
}


// Generar PDF usando la clase ClassPlanPDF
$pdf = new ClassPlanPDF($plan);
$pdf->generate();
$pdf->Output();
