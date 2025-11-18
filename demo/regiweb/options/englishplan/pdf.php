<?php
require '../../../app.php';

use App\Models\Admin;
use App\Models\EnglishPlan;
use Classes\PDF;
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

$school = Admin::primaryAdmin();


$pdf = new PDF();
$pdf->SetTitle('English Plan');
$pdf->SetAutoPageBreak(true, 10);
$pdf->AddPage('L');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, 'ENGLISH PLAN', 0, 1, "C");
$pdf->Ln(10);
$pdf->Cell(20, 7, "Teacher:");
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(80, 7, $plan->teacher, "B");
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(45);
$pdf->Cell(25, 7, "Institution:");
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(80, 7, $school->colegio, "B");
$pdf->SetFont('Arial', 'B', 12);
$pdf->Ln();
$pdf->Cell(20, 7, "Grade:");
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(80, 7, $plan->grade, "B");
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(45);
$pdf->Cell(25, 7, "Dates:");
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(80, 7, $plan->dates, "B");
$pdf->SetFont('Arial', 'B', 12);
$pdf->Ln();
$pdf->Cell(20, 7, "Subject:");
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(80, 7, $plan->subject, "B");
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(45);
$pdf->Cell(25, 7, "Topic:");
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(80, 7, $plan->topic, "B");
$pdf->SetFont('Arial', 'B', 12);
$pdf->Ln(10);

// Standards and Strategy Section
$pdf->Rect($pdf->GetX(), $pdf->GetY(), 100, 7);
$pdf->Cell(100, 7, "Standards", 0, 0, "C");
$pdf->Rect($pdf->GetX(), $pdf->GetY(), 150, 7);
$pdf->Cell(150, 7, "Strategy", 0, 1, "C");
$pdf->Rect($pdf->GetX(), $pdf->GetY(), 100, 40);
$y = $pdf->GetY();

// Standards
$standards = [
	['field' => 'standard1', 'label' => 'Oral Communication'],
	['field' => 'standard2', 'label' => 'Written Communication'],
	['field' => 'standard3', 'label' => 'Communication Reading']
];

$pdf->SetFont('Arial', '', 11);
foreach ($standards as $standard) {
	$pdf->Cell(2);
	$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.3, 2, 2, ($plan->{$standard['field']} == "Si") ? "DF" : "");
	$pdf->Cell(3);
	$pdf->Cell(98, 5, $standard['label']);
	$pdf->Ln();
}

// Depth levels
$pdf->Ln(5);
$pdf->Cell($pdf->GetStringWidth("Depth level of knowledge:") + 2, 5, "Depth level of knowledge:");

$depthLevels = [
	['field' => 'depth1', 'label' => 'Rote', 'width' => 12],
	['field' => 'depth2', 'label' => 'Processing', 'width' => 25, 'ln' => true]
];

foreach ($depthLevels as $depth) {
	$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.3, 2, 2, ($plan->{$depth['field']} == "Si") ? "DF" : "");
	$pdf->Cell(2);
	$pdf->Cell($depth['width'], 5, $depth['label'], 0, isset($depth['ln']) ? 1 : 0);
}

$pdf->Cell($pdf->GetStringWidth("Depth level of knowledge:") + 2);
$depthLevels2 = [
	['field' => 'depth3', 'label' => 'Strategic', 'width' => 20],
	['field' => 'depth4', 'label' => 'Extended', 'width' => 25]
];

foreach ($depthLevels2 as $depth) {
	$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.3, 2, 2, ($plan->{$depth['field']} == "Si") ? "DF" : "");
	$pdf->Cell(2);
	$pdf->Cell($depth['width'], 5, $depth['label']);
}

// Strategy Section
$pdf->SetXY(110, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), 150, 40);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(20, 5, "Strategy:", 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(102);

$strategies = [
	['field' => 'strategy1', 'label' => 'ECA', 'width' => 12],
	['field' => 'strategy2', 'label' => 'Trilogy Literacy', 'width' => 30],
	['field' => 'strategy3', 'label' => 'Cycles of learning', 'width' => 25]
];

foreach ($strategies as $strategy) {
	$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.3, 2, 2, ($plan->{$strategy['field']} == "Si") ? "DF" : "");
	$pdf->Cell(2);
	$pdf->Cell($strategy['width'], 5, $strategy['label']);
}
$pdf->Ln(7);

// Appraisal Section
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(102);
$pdf->Cell(20, 5, "Appraisal:", 0, 1);
$pdf->SetFont('Arial', '', 11);

$appraisals = [
	// Row 1
	[
		['field' => 'appraisal1', 'label' => 'Diagnostic Test', 'width' => 30],
		['field' => 'appraisal2', 'label' => 'Whirlwind of ideas', 'width' => 35, 'spacing' => 2.5],
		['field' => 'appraisal3', 'label' => 'Targeted List', 'width' => 30]
	],
	// Row 2
	[
		['field' => 'appraisal4', 'label' => 'Concept Map', 'width' => 27],
		['field' => 'appraisal5', 'label' => 'Concrete Poem', 'width' => 30],
		['field' => 'appraisal6', 'label' => 'Comics', 'width' => 17],
		['field' => 'appraisal7', 'label' => 'Open Question', 'width' => 30]
	],
	// Row 3
	[
		['field' => 'appraisal8', 'label' => 'Reflective Journal', 'width' => 35],
		['field' => 'appraisal9', 'label' => 'Test', 'width' => 12],
		['field' => 'appraisal10', 'label' => 'Interviews', 'width' => 20],
		['field' => 'appraisal11', 'label' => 'Quiz', 'width' => 12],
		['field' => 'appraisal12', 'label' => 'Review', 'width' => 17],
		['field' => 'appraisal13', 'label' => 'Draft', 'width' => 10]
	],
	// Row 4
	[
		['field' => 'appraisal14', 'label' => 'Other', 'width' => 15]
	]
];

foreach ($appraisals as $row) {
	$pdf->Cell(102);
	foreach ($row as $appraisal) {
		$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.3, 2, 2, ($plan->{$appraisal['field']} == "Si") ? "DF" : "");
		$pdf->Cell(2);
		if (isset($appraisal['spacing'])) {
			$pdf->Cell($appraisal['spacing']);
		}
		$pdf->Cell($appraisal['width'], 5, $appraisal['label']);
	}
	$pdf->Ln();
}
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, "General Objectives:", 0, 1);
$pdf->Ln(5);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), 250, 40);
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(250, 5, $plan->general);


$pdf->AddPage("L");
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(125, 5, "Specific Objectives:", 0, 0, "C");
$pdf->Cell(125, 5, "Use Norman Webb Verbs List:", 0, 1, "C");

$pdf->SetFont('Arial', '', 11);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), 15, 7);
$pdf->Rect($pdf->GetX() + 15, $pdf->GetY(), 100, 7);
$pdf->Rect($pdf->GetX() + 115, $pdf->GetY(), 135, 7);
$pdf->Cell(15, 7, "Level 1");
$pdf->Cell(100, 7, $plan->level1_1);
$pdf->Cell(135, 7, $plan->level1_2, 0, 1);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), 15, 7);
$pdf->Rect($pdf->GetX() + 15, $pdf->GetY(), 100, 7);
$pdf->Rect($pdf->GetX() + 115, $pdf->GetY(), 135, 7);
$pdf->Cell(15, 7, "Level 2");
$pdf->Cell(100, 7, $plan->level2_1);
$pdf->Cell(135, 7, $plan->level2_2, 0, 1);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), 15, 7);
$pdf->Rect($pdf->GetX() + 15, $pdf->GetY(), 100, 7);
$pdf->Rect($pdf->GetX() + 115, $pdf->GetY(), 135, 7);
$pdf->Cell(15, 7, "Level 3");
$pdf->Cell(100, 7, $plan->level3_1);
$pdf->Cell(135, 7, $plan->level3_2, 0, 1);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), 15, 7);
$pdf->Rect($pdf->GetX() + 15, $pdf->GetY(), 100, 7);
$pdf->Rect($pdf->GetX() + 115, $pdf->GetY(), 135, 7);
$pdf->Cell(15, 7, "Level 4");
$pdf->Cell(100, 7, $plan->level4_1);
$pdf->Cell(135, 7, $plan->level4_2, 0, 1);

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$X = 41.6;
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $X, 7);
$pdf->Rect($pdf->GetX() + $X, $pdf->GetY(), $X, 7);
$pdf->Rect($pdf->GetX() + $X * 2, $pdf->GetY(), $X, 7);
$pdf->Rect($pdf->GetX() + $X * 3, $pdf->GetY(), $X, 7);
$pdf->Rect($pdf->GetX() + $X * 4, $pdf->GetY(), $X, 7);
$pdf->Rect($pdf->GetX() + $X * 5, $pdf->GetY(), $X, 7);
$pdf->Cell($X, 7, "Activities", 0, 0, "C");
$pdf->Cell($X, 7, "Materials", 0, 0, "C");
$pdf->Cell($X, 7, "Home", 0, 0, "C");
$pdf->Cell($X, 7, "Development", 0, 0, "C");
$pdf->Cell($X, 7, "Closing", 0, 0, "C");
$pdf->Cell($X, 7, "Assessment", 0, 1, "C");
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $X, 70);
$pdf->Rect($pdf->GetX() + $X, $pdf->GetY(), $X, 70);
$pdf->Rect($pdf->GetX() + $X * 2, $pdf->GetY(), $X, 70);
$pdf->Rect($pdf->GetX() + $X * 3, $pdf->GetY(), $X, 70);
$pdf->Rect($pdf->GetX() + $X * 4, $pdf->GetY(), $X, 70);
$pdf->Rect($pdf->GetX() + $X * 5, $pdf->GetY(), $X, 70);

$Y = $pdf->GetY();
$pdf->SetFont('Arial', '', 10);
$pdf->Cell($X, 5, "Fases:", 0, 1);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->activities1 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Exploration", 0, 1);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->activities2 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Conceptualization", 0, 1);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->activities3 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Implementation", 0, 1);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->activities4 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Before reading", 0, 1);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->activities5 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "During read", 0, 1);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->activities6 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "After reading", 0, 1);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->activities7 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Focus", 0, 1);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->activities8 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Scan", 0, 1);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->activities9 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Reflect", 0, 1);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->activities10 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Apply", 0, 1);

$pdf->SetY($Y);
$pdf->Cell($X);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->materials1 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Copy", 0, 1);
$pdf->Cell($X);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->materials2 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Book", 0, 1);
$pdf->Cell($X);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->materials3 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Slate", 0, 1);
$pdf->Cell($X);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->materials4 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Newspaper", 0, 1);
$pdf->Cell($X);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->materials5 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Calculator", 0, 1);
$pdf->Cell($X);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->materials6 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Computer", 0, 1);
$pdf->Cell($X);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->materials7 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Crayons", 0, 1);
$pdf->Cell($X);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->materials8 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Graph paper", 0, 1);
$pdf->Cell($X);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->materials9 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Construction paper", 0, 1);
$pdf->Cell($X);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->materials10 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Conveyor or rule", 0, 1);
$pdf->Cell($X);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->materials11 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Transparency", 0, 1);
$pdf->Cell($X);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->materials12 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Manipulatives", 0, 1);
$pdf->Cell($X);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->materials13 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Mimeographed sheet", 0, 1);
$pdf->Cell($X);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->materials14 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Other", 0, 1);

$pdf->SetY($Y);
$pdf->Cell($X * 2);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->home1 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Reflection", 0, 1);
$pdf->Cell($X * 2);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->home2 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Poem", 0, 1);
$pdf->Cell($X * 2);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->home3 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Song", 0, 1);
$pdf->Cell($X * 2);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->home4 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Game", 0, 1);
$pdf->Cell($X * 2);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->home5 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Discussion of the", 0, 1);
$pdf->Cell($X * 2);
$pdf->Cell($X, 5, "Allocation", 0, 1);
$pdf->Cell($X * 2);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->home6 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Questions on you to", 0, 1);
$pdf->Cell($X * 2);
$pdf->Cell($X, 5, "study", 0, 1);
$pdf->Cell($X * 2);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->home7 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Review concepts", 0, 1);
$pdf->Cell($X * 2);
$pdf->Cell($X, 5, "discussed", 0, 1);
$pdf->Cell($X * 2);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->home8 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Observation and study", 0, 1);
$pdf->Cell($X * 2);
$pdf->Cell($X, 5, "of: sheets, tables, graphs", 0, 1);
$pdf->Cell($X * 2);
$pdf->Cell($X, 5, "and/or books.", 0, 1);

$pdf->SetY($Y);
$pdf->Cell($X * 3);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->development1 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Oral Reading", 0, 1);
$pdf->Cell($X * 3);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->development2 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Reading and analySis", 0, 1);
$pdf->Cell($X * 3);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->development3 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Definition of concepts", 0, 1);
$pdf->Cell($X * 3);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->development4 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Demostration and", 0, 1);
$pdf->Cell($X * 3);
$pdf->Cell($X, 5, "examples aimed", 0, 1);
$pdf->Cell($X * 3);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->development5 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Work practice: book,", 0, 1);
$pdf->Cell($X * 3);
$pdf->Cell($X, 5, "blackboard or paper", 0, 1);
$pdf->Cell($X * 3);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->development6 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Oral Report", 0, 1);
$pdf->Cell($X * 3);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->development7 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Fil analySis", 0, 1);
$pdf->Cell($X * 3);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->development8 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Competition", 0, 1);
$pdf->Cell($X * 3);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->development9 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Test", 0, 1);
$pdf->Cell($X * 3);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->development10 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Test cut", 0, 1);

$pdf->SetY($Y);
$pdf->Cell($X * 4);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->closing1 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Clarifying concepts", 0, 1);
$pdf->Cell($X * 4);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->closing2 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Discussion of the work", 0, 1);
$pdf->Cell($X * 4);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->closing3 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "To compare the work", 0, 1);
$pdf->Cell($X * 4);
$pdf->Cell($X, 5, "done", 0, 1);

$pdf->SetY($Y);
$pdf->Cell($X * 5);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->assessment1 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Diagnostic test", 0, 1);
$pdf->Cell($X * 5);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->assessment2 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Whirlwind of ideas", 0, 1);
$pdf->Cell($X * 5);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->assessment3 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Targeted list", 0, 1);
$pdf->Cell($X * 5);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->assessment4 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Concept map", 0, 1);
$pdf->Cell($X * 5);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->assessment5 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Concrete poem", 0, 1);
$pdf->Cell($X * 5);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->assessment6 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Comics", 0, 1);
$pdf->Cell($X * 5);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->assessment7 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Draft", 0, 1);
$pdf->Cell($X * 5);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->assessment8 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Open question", 0, 1);
$pdf->Cell($X * 5);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->assessment9 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Reflective journal", 0, 1);
$pdf->Cell($X * 5);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->assessment10 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Test", 0, 1);
$pdf->Cell($X * 5);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->assessment11 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Interviews", 0, 1);
$pdf->Cell($X * 5);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->assessment12 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Quiz", 0, 1);
$pdf->Cell($X * 5);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->assessment13 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Review", 0, 1);
$pdf->Cell($X * 5);
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.3, 2, 2, ($plan->assessment14 == "Si") ? "DF" : "");
$pdf->Cell(3);
$pdf->Cell($X, 5, "Other", 0, 1);

$pdf->AddPage("L");
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $X, 30);
$pdf->Rect($pdf->GetX() + $X, $pdf->GetY(), $X, 30);
$pdf->Rect($pdf->GetX() + $X * 2, $pdf->GetY(), $X, 30);
$pdf->Rect($pdf->GetX() + $X * 3, $pdf->GetY(), $X, 30);
$pdf->Rect($pdf->GetX() + $X * 4, $pdf->GetY(), $X, 30);
$pdf->Rect($pdf->GetX() + $X * 5, $pdf->GetY(), $X, 30);
$pdf->SetFont('Arial', 'B', 12);
$Y = $pdf->GetY();
$pdf->Cell($X, 5, "Tuesday", 0, 1);
$pdf->Cell($X, 5, "Fase:", 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell($X, 6, $plan->tuesday, "B");
$pdf->SetXY(51.6, $Y);
$pdf->Cell($X, 5, $plan->tuesday1);
$pdf->Cell($X, 5, $plan->tuesday2);
$pdf->Cell($X, 5, $plan->tuesday3);
$pdf->Cell($X, 5, $plan->tuesday4);
$pdf->Cell($X, 5, $plan->tuesday5);
$pdf->Ln(30);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $X, 30);
$pdf->Rect($pdf->GetX() + $X, $pdf->GetY(), $X, 30);
$pdf->Rect($pdf->GetX() + $X * 2, $pdf->GetY(), $X, 30);
$pdf->Rect($pdf->GetX() + $X * 3, $pdf->GetY(), $X, 30);
$pdf->Rect($pdf->GetX() + $X * 4, $pdf->GetY(), $X, 30);
$pdf->Rect($pdf->GetX() + $X * 5, $pdf->GetY(), $X, 30);
$pdf->SetFont('Arial', 'B', 12);
$Y = $pdf->GetY();
$pdf->Cell($X, 5, "Wednesday", 0, 1);
$pdf->Cell($X, 5, "Fase:", 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell($X, 6, $plan->wednesday, "B");
$pdf->SetXY(51.6, $Y);
$pdf->Cell($X, 5, $plan->wednesday1);
$pdf->Cell($X, 5, $plan->wednesday2);
$pdf->Cell($X, 5, $plan->wednesday3);
$pdf->Cell($X, 5, $plan->wednesday4);
$pdf->Cell($X, 5, $plan->wednesday5);
$pdf->Ln(30);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $X, 30);
$pdf->Rect($pdf->GetX() + $X, $pdf->GetY(), $X, 30);
$pdf->Rect($pdf->GetX() + $X * 2, $pdf->GetY(), $X, 30);
$pdf->Rect($pdf->GetX() + $X * 3, $pdf->GetY(), $X, 30);
$pdf->Rect($pdf->GetX() + $X * 4, $pdf->GetY(), $X, 30);
$pdf->Rect($pdf->GetX() + $X * 5, $pdf->GetY(), $X, 30);
$pdf->SetFont('Arial', 'B', 12);
$Y = $pdf->GetY();
$pdf->Cell($X, 5, "Thursday", 0, 1);
$pdf->Cell($X, 5, "Fase:", 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell($X, 6, $plan->thursday, "B");
$pdf->SetXY(51.6, $Y);
$pdf->Cell($X, 5, $plan->thursday1);
$pdf->Cell($X, 5, $plan->thursday2);
$pdf->Cell($X, 5, $plan->thursday3);
$pdf->Cell($X, 5, $plan->thursday4);
$pdf->Cell($X, 5, $plan->thursday5);
$pdf->Ln(30);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $X, 30);
$pdf->Rect($pdf->GetX() + $X, $pdf->GetY(), $X, 30);
$pdf->Rect($pdf->GetX() + $X * 2, $pdf->GetY(), $X, 30);
$pdf->Rect($pdf->GetX() + $X * 3, $pdf->GetY(), $X, 30);
$pdf->Rect($pdf->GetX() + $X * 4, $pdf->GetY(), $X, 30);
$pdf->Rect($pdf->GetX() + $X * 5, $pdf->GetY(), $X, 30);
$pdf->SetFont('Arial', 'B', 12);
$Y = $pdf->GetY();
$pdf->Cell($X, 5, "Friday", 0, 1);
$pdf->Cell($X, 5, "Fase:", 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell($X, 6, $plan->friday, "B");
$pdf->SetXY(51.6, $Y);
$pdf->Cell($X, 5, $plan->friday1);
$pdf->Cell($X, 5, $plan->friday2);
$pdf->Cell($X, 5, $plan->friday3);
$pdf->Cell($X, 5, $plan->friday4);
$pdf->Cell($X, 5, $plan->friday5);


$pdf->Output();
