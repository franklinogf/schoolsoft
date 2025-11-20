<?php
require '../../../app.php';

use App\Models\EnglishPlan;
use App\Pdfs\Plans\EnglishPlanPDF;
use Classes\Session;

Session::is_logged();

$planID = $_POST['id'] ?? $_GET['id'] ?? null;

if (!$planID) {
	die('No plan ID provided');
}

$plan = EnglishPlan::find($planID);
if (!$plan) {
	die('Plan not found');
}

$pdf = new EnglishPlanPDF($plan);
$pdf->generate();
$pdf->Output();
