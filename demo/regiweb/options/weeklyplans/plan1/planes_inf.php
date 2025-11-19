<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\Admin;
use App\Models\Teacher;
use App\Models\WeeklyPlan;
use Classes\PDF;
use Classes\Session;

Session::is_logged();

$teacher = Teacher::find(Session::id());
$school = Admin::primaryAdmin();

$planId = $_GET['plan'] ?? null;

if (!$planId) {
    die('Plan ID no proporcionado');
}

$weeklyPlan = WeeklyPlan::find($planId);

if (!$weeklyPlan || $weeklyPlan->id != $teacher->id) {
    die('Plan no encontrado o no autorizado');
}

$pdf = new PDF();
$pdf->SetTitle(__("Plan Semanal") . " - " . $weeklyPlan->tema, true);
$pdf->Fill();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 15);
// $pdf->useFooter(false);

// Título
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, __("PLAN SEMANAL"), 0, 1, 'C');
$pdf->Ln(3);

// Información General
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, __("INFORMACIÓN GENERAL"), 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(50, 6, __("Maestro(a)") . ": ", 1, 0);
$pdf->Cell(140, 6, $teacher->apellidos . ', ' . $teacher->nombre, 1, 1);

$pdf->Cell(50, 6, __("Institución") . ": ", 1, 0);
$pdf->Cell(140, 6, $school->colegio, 1, 1);

$pdf->Cell(50, 6, __("Clase") . ": ", 1, 0);
$pdf->Cell(140, 6, $weeklyPlan->clase, 1, 1);

$pdf->Cell(50, 6, __("Grado") . ": ", 1, 0);
$pdf->Cell(140, 6, $weeklyPlan->grado, 1, 1);

$pdf->Cell(50, 6, __("Tema") . ": ", 1, 0);
$pdf->Cell(140, 6, $weeklyPlan->tema, 1, 1);

$pdf->Cell(50, 6, __("Fechas") . ": ", 1, 0);
$pdf->Cell(140, 6, $weeklyPlan->fecha, 1, 1);

$pdf->Cell(50, 6, __("Lección") . ": ", 1, 0);
$pdf->Cell(140, 6, $weeklyPlan->leccion, 1, 1);

$pdf->Ln(2);

// Estándares
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, __("ESTÁNDARES"), 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(0, 5, $weeklyPlan->est, 1, 'L');

$pdf->Ln(2);

// Expectativas
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, __("EXPECTATIVAS"), 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(0, 5, $weeklyPlan->exp, 1, 'L');

$pdf->Ln(2);

// Objetivos Generales
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, __("OBJETIVOS GENERALES"), 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(0, 5, $weeklyPlan->obj_gen, 1, 'L');

$pdf->Ln(2);

// Objetivos Específicos
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, __("OBJETIVOS ESPECÍFICOS - VERBOS DE REFERENCIA"), 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 8);

for ($i = 1; $i <= 4; $i++) {
    $nivelField = "nivel{$i}";
    $listField = "lst_v{$i}";
    if (!empty($weeklyPlan->$nivelField) || !empty($weeklyPlan->$listField)) {
        $pdf->Cell(50, 5, __("Nivel") . " {$i}: " . $weeklyPlan->$nivelField, 1, 0);
        $pdf->Cell(140, 5, $weeklyPlan->$listField, 1, 1);
    }
}

$pdf->Ln(2);

// Actividades Semanales - Nueva página si es necesario
if ($pdf->GetY() > 150) {
    $pdf->AddPage();
}

$diaSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

for ($i = 1; $i <= 5; $i++) {
    $actField = "act{$i}";
    $matField = "mat{$i}";
    $iniField = "ini{$i}";
    $desField = "des{$i}";
    $cieField = "cie{$i}";
    $asseField = "asse{$i}";

    $matArray = $weeklyPlan->getMaterialsArray($matField);
    $iniArray = $weeklyPlan->getMaterialsArray($iniField);
    $desArray = $weeklyPlan->getMaterialsArray($desField);
    $cieArray = $weeklyPlan->getMaterialsArray($cieField);
    $asseArray = $weeklyPlan->getMaterialsArray($asseField);

    $otros_m = "otros_m{$i}";
    $otros_i = "otros_i{$i}";
    $otros_d = "otros_d{$i}";
    $otros_c = "otros_c{$i}";
    $otros_a = "otros_a{$i}";

    $materiales = implode(", ", $matArray);
    if (!empty($weeklyPlan->$otros_m)) {
        $materiales .= " | " . $weeklyPlan->$otros_m;
    }

    $inicio = implode(", ", $iniArray);
    if (!empty($weeklyPlan->$otros_i)) {
        $inicio .= " | " . $weeklyPlan->$otros_i;
    }

    $desarrollo = implode(", ", $desArray);
    if (!empty($weeklyPlan->$otros_d)) {
        $desarrollo .= " | " . $weeklyPlan->$otros_d;
    }

    $cierre = implode(", ", $cieArray);
    if (!empty($weeklyPlan->$otros_c)) {
        $cierre .= " | " . $weeklyPlan->$otros_c;
    }

    $assessment = implode(", ", $asseArray);
    if (!empty($weeklyPlan->$otros_a)) {
        $assessment .= " | " . $weeklyPlan->$otros_a;
    }

    // Día y Fase
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(0, 6, $diaSemana[$i - 1] . " - Fase: " . $weeklyPlan->$actField, 1, 1, 'C', true);

    // Materiales
    if (!empty($materiales)) {
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0, 5, __("MATERIALES"), 1, 1, 'L', true);
        $pdf->SetFont('Arial', '', 8);
        $pdf->MultiCell(0, 5, ltrim($materiales, ' | '), 1, 'L');
    }

    // Inicio
    if (!empty($inicio)) {
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0, 5, __("INICIO"), 1, 1, 'L', true);
        $pdf->SetFont('Arial', '', 8);
        $pdf->MultiCell(0, 5, ltrim($inicio, ' | '), 1, 'L');
    }

    // Desarrollo
    if (!empty($desarrollo)) {
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0, 5, __("DESARROLLO"), 1, 1, 'L', true);
        $pdf->SetFont('Arial', '', 8);
        $pdf->MultiCell(0, 5, ltrim($desarrollo, ' | '), 1, 'L');
    }

    // Cierre
    if (!empty($cierre)) {
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0, 5, __("CIERRE"), 1, 1, 'L', true);
        $pdf->SetFont('Arial', '', 8);
        $pdf->MultiCell(0, 5, ltrim($cierre, ' | '), 1, 'L');
    }

    // Assessment
    if (!empty($assessment)) {
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0, 5, __("ASSESSMENT"), 1, 1, 'L', true);
        $pdf->SetFont('Arial', '', 8);
        $pdf->MultiCell(0, 5, ltrim($assessment, ' | '), 1, 'L');
    }

    $pdf->Ln(3);
}

$pdf->Ln(2);

// Comentarios
if (!empty($weeklyPlan->coment)) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, __("COMENTARIOS"), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 8);
    $pdf->MultiCell(0, 5, $weeklyPlan->coment, 1, 'L');
}

$pdf->Output('I', 'Plan_Semanal_' . $weeklyPlan->id2 . '.pdf');
