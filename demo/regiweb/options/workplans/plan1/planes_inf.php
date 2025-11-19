<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\Admin;
use App\Models\Teacher;
use App\Models\WorkPlan;
use Classes\PDF;
use Classes\Session;

Session::is_logged();

$teacher = Teacher::find(Session::id());
$school = Admin::primaryAdmin();
$year = $school->year;

$planId = $_GET['plan'] ?? null;

if (!$planId) {
    die('Plan ID no proporcionado');
}

$workPlan = WorkPlan::find($planId);

if (!$workPlan || $workPlan->id != $teacher->id) {
    die('Plan no encontrado o no autorizado');
}



$pdf = new PDF();
$pdf->SetTitle(__("Plan de trabajo") . " - " . $workPlan->plan, true);
$pdf->Fill();
$pdf->AddPage();

// Título
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 7, __("PLAN DE TRABAJO"), 0, 1, 'C');
$pdf->Ln(3);

// Plan de
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, __("Plan de") . ": " . ($workPlan->plan ?? ''), 0, 1);
$pdf->SetFont('Arial', '', 10);

$pdf->Cell(0, 5, __("Estándares de Contenido") . ": {$workPlan->estandares}", 0, 1);

$pdf->Ln(2);

// Información básica
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(45, 5, __("Grado") . ":", 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, $workPlan->grado ?? '', 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(45, 5, __("Asignatura Específica") . ":", 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, $workPlan->asignatura ?? '', 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(45, 5, __("Fecha/Semana") . ":", 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, __("mes") . ": " . ($workPlan->mes ?? '') . " " . __("día") . " " . ($workPlan->dia1 ?? '') . " " . __("al día") . " " . ($workPlan->dia2 ?? ''), 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(45, 5, __("Temas") . ":", 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 5, ($workPlan->tema1 ?? '') . ($workPlan->tema2 ? "\n" . $workPlan->tema2 : ''));
$pdf->Ln(2);

// Enfoque
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(0, 6, "1. " . __("Enfocar") . "   2. " . __("Explorar") . "   3. " . __("Reflexionar") . "   4. " . __("Aplicación"), 1, 1, 'C', true);
$pdf->Ln(2);

// Estándares y Expectativas
if ($workPlan->espectativas) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 5, __("Estándares y Espectativas") . ":", 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->MultiCell(0, 5, $workPlan->espectativas);
    $pdf->Ln(2);
}

// Nivel de Profundidad
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, __("Nivel de Profundidad de Conocimiento") . ":", 0, 1);
$pdf->SetFont('Arial', '', 9);
$niveles = [];
if ($workPlan->np1 == 'Si') $niveles[] = __("Memorístico");
if ($workPlan->np2 == 'Si') $niveles[] = __("Procesamiento");
if ($workPlan->np3 == 'Si') $niveles[] = __("Estratégico");
if ($workPlan->np4 == 'Si') $niveles[] = __("Extendido");
$pdf->Cell(0, 5, implode(", ", $niveles), 0, 1);
$pdf->Ln(2);

// EduSystem
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(95, 6, "EduSystem", 1, 0, 'C', true);
$pdf->Cell(95, 6, __("Tema"), 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(45, 5, __("Unidad") . ":", 1, 0);
$pdf->Cell(50, 5, $workPlan->unidad ?? '', 1, 0);
$pdf->Cell(95, 5, $workPlan->tema ?? '', 1, 1);

$pdf->Cell(45, 5, __("Lección") . ":", 1, 0);
$pdf->Cell(50, 5, $workPlan->leccion ?? '', 1, 0);
$pdf->Cell(95, 5, __("Pre-requisito") . ": " . ($workPlan->pre1 ?? ''), 1, 1);

$pdf->Cell(45, 5, __("Código") . ":", 1, 0);
$pdf->Cell(50, 5, $workPlan->codigo ?? '', 1, 0);
$pdf->Cell(95, 5, '', 1, 1);
$pdf->Ln(2);

// Objetivos
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(0, 6, __("Objetivo"), 1, 1, 'C', true);

if ($workPlan->obj1) {
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(45, 5, __("Conceptual") . ":", 0, 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->MultiCell(0, 5, $workPlan->obj1);
}

if ($workPlan->obj2) {
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(45, 5, __("Procedimental") . ":", 0, 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->MultiCell(0, 5, $workPlan->obj2);
}

if ($workPlan->obj3) {
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(45, 5, __("Actitudinal") . ":", 0, 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->MultiCell(0, 5, $workPlan->obj3);
}

if ($workPlan->integracion) {
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(45, 5, __("Integración") . ":", 0, 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->MultiCell(0, 5, $workPlan->integracion);
}
$pdf->Ln(2);

// Secuencia de actividades y Evaluación - Preparar datos
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(95, 6, __("SECUENCIA DE ACTIVIDADES"), 1, 0, 'C', true);
$pdf->Cell(95, 6, __("EVALUACION INFORMATIVA"), 1, 1, 'C', true);

// Preparar datos de columna izquierda
$leftContent = [];

// Actividad
$actividades = [];
if ($workPlan->act1 == 'Si') $actividades[] = __("Actividad");
if ($workPlan->act2 == 'Si') $actividades[] = __("Exploración");
if ($workPlan->act3 == 'Si') $actividades[] = __("Conceptualización");
if ($workPlan->act4 == 'Si') $actividades[] = __("Aplicación");
if (!empty($actividades)) {
    $leftContent[] = ['type' => 'header', 'text' => __("Actividad") . ": " . implode(", ", $actividades)];
}

// Inicio
$inicio = [];
if ($workPlan->ini1 == 'Si') $inicio[] = "✓";
if ($workPlan->ini2 == 'Si') $inicio[] = __("Repaso clase anterior");
if ($workPlan->ini3 == 'Si') $inicio[] = __("Exploración conocimientos previos");
if ($workPlan->ini4 == 'Si') $inicio[] = __("Presentación de objetivos");
if ($workPlan->ini5 == 'Si') $inicio[] = __("Motivación o inicio");
if ($workPlan->ini6 == 'Si') $inicio[] = __("Presentación vocabulario");
if ($workPlan->ini7 == 'Si') $inicio[] = __("Presentación del tema");

if (!empty($inicio)) {
    $leftContent[] = ['type' => 'section', 'text' => "1. " . __("Inicio") . ":"];
    foreach ($inicio as $item) {
        $leftContent[] = ['type' => 'item', 'text' => "  " . $item];
    }
}

// Desarrollo
$desarrollo = [];
if ($workPlan->des1 == 'Si') $desarrollo[] = __("Estrategia ECA");
if ($workPlan->des2 == 'Si') $desarrollo[] = __("Trabajo cooperativo");
if ($workPlan->des3 == 'Si') $desarrollo[] = __("Laboratorio");
if ($workPlan->des4 == 'Si') $desarrollo[] = __("Discusión socializada");
if ($workPlan->des5 == 'Si') $desarrollo[] = __("Centros de aprendizaje");
if ($workPlan->des6 == 'Si') $desarrollo[] = __("Conferencia");
if ($workPlan->des7 == 'Si') $desarrollo[] = __("Otra");

if (!empty($desarrollo)) {
    $leftContent[] = ['type' => 'section', 'text' => "2. " . __("Desarrollo") . ":"];
    foreach ($desarrollo as $item) {
        $leftContent[] = ['type' => 'item', 'text' => "  " . $item];
    }
}

// Cierre
$cierre = [];
if ($workPlan->cie1 == 'Si') $cierre[] = __("Repaso clase anterior");
if ($workPlan->cie2 == 'Si') $cierre[] = __("Trabajo cooperativo");
if ($workPlan->cie3 == 'Si') $cierre[] = __("Discusión socializada");
if ($workPlan->cie4 == 'Si') $cierre[] = __("Centros de aprendizaje");
if ($workPlan->cie5 == 'Si') $cierre[] = __("Otra");

if (!empty($cierre)) {
    $leftContent[] = ['type' => 'section', 'text' => "3. " . __("Cierre") . ":"];
    foreach ($cierre as $item) {
        $leftContent[] = ['type' => 'item', 'text' => "  " . $item];
    }
}

// Preparar datos de columna derecha
$rightContent = [];

// Aplicaciones
$aplicaciones = [];
if ($workPlan->eva1 == 'Si') $aplicaciones[] = __("Texto");
if ($workPlan->eva2 == 'Si') $aplicaciones[] = __("Cuaderno");
if ($workPlan->eva3 == 'Si') $aplicaciones[] = __("Fichas");

if (!empty($aplicaciones)) {
    $rightContent[] = ['type' => 'header', 'text' => "4. " . __("Aplicaciones") . ": " . implode(", ", $aplicaciones)];
}

if ($workPlan->tab1 == 'Si' || $workPlan->tab2) {
    $rightContent[] = ['type' => 'item', 'text' => __("Pruebas cortas o Pruebas") . ": " . ($workPlan->tab2 ?? '')];
}

if ($workPlan->tab3 == 'Si' || $workPlan->tab4) {
    $rightContent[] = ['type' => 'item', 'text' => __("Proyectos o tareas de desempeño") . ": " . ($workPlan->tab4 ?? '')];
}

if ($workPlan->tab5 == 'Si' || $workPlan->tab6) {
    $rightContent[] = ['type' => 'item', 'text' => __("Tareas") . ": " . ($workPlan->tab6 ?? '')];
}

if ($workPlan->tab7 == 'Si' || $workPlan->tab8) {
    $rightContent[] = ['type' => 'item', 'text' => __("Portafolio") . ": " . ($workPlan->tab8 ?? '')];
}

// Selección
$seleccion = [];
if ($workPlan->sel1 == 'Si') $seleccion[] = __("Llenar blancos");
if ($workPlan->sel2 == 'Si') $seleccion[] = __("Pareo");
if ($workPlan->sel3 == 'Si') $seleccion[] = __("Cierto falso");
if ($workPlan->sel4 == 'Si') $seleccion[] = __("Informes orales o escritos");
if ($workPlan->sel5 == 'Si') $seleccion[] = __("Otro");

if (!empty($seleccion)) {
    $rightContent[] = ['type' => 'section', 'text' => __("Selección") . ":"];
    foreach ($seleccion as $item) {
        $rightContent[] = ['type' => 'item', 'text' => "  " . $item];
    }
}

if ($workPlan->pro1 == 'Si') {
    $rightContent[] = ['type' => 'item', 'text' => __("Prontuarios")];
}

if ($workPlan->pro2 == 'Si') {
    $rightContent[] = ['type' => 'item', 'text' => __("Diarios reflexivos")];
}

if ($workPlan->otro) {
    $rightContent[] = ['type' => 'item', 'text' => __("Otro") . ": " . $workPlan->otro];
}

if ($workPlan->autoeva) {
    $rightContent[] = ['type' => 'item', 'text' => __("Autoevaluación") . ": " . $workPlan->autoeva];
}

// Renderizar ambas columnas fila por fila
$maxRows = max(count($leftContent), count($rightContent));

for ($i = 0; $i < $maxRows; $i++) {
    $leftItem = $leftContent[$i] ?? null;
    $rightItem = $rightContent[$i] ?? null;

    // Determinar altura de la fila
    $height = 4;
    if (($leftItem && $leftItem['type'] == 'header') || ($rightItem && $rightItem['type'] == 'header')) {
        $height = 5;
    }
    if (($leftItem && $leftItem['type'] == 'section') || ($rightItem && $rightItem['type'] == 'section')) {
        $height = 5;
    }

    // Columna izquierda
    $pdf->SetX(10);
    if ($leftItem) {
        if ($leftItem['type'] == 'header' || $leftItem['type'] == 'section') {
            $pdf->SetFont('Arial', 'B', 9);
        } else {
            $pdf->SetFont('Arial', '', 8);
        }
        $pdf->Cell(95, $height, $leftItem['text'], 1, 0);
    } else {
        $pdf->Cell(95, $height, '', 1, 0);
    }

    // Columna derecha
    if ($rightItem) {
        if ($rightItem['type'] == 'header' || $rightItem['type'] == 'section') {
            $pdf->SetFont('Arial', 'B', 9);
        } else {
            $pdf->SetFont('Arial', '', 8);
        }
        $pdf->Cell(95, $height, $rightItem['text'], 1, 1);
    } else {
        $pdf->Cell(95, $height, '', 1, 1);
    }
}

// Acomodos razonables
$pdf->Ln(5);
$acomodos = array_filter([
    $workPlan->as1,
    $workPlan->as2,
    $workPlan->as3,
    $workPlan->as4,
    $workPlan->as5,
    $workPlan->as6,
    $workPlan->as7,
    $workPlan->as8
]);

if (!empty($acomodos)) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 5, __("Acomodos razonables") . ":", 0, 1);
    $pdf->SetFont('Arial', '', 8);

    // Dividir en dos columnas
    $totalAcomodos = count($acomodos);
    $mitad = ceil($totalAcomodos / 2);
    $acomodosArray = array_values($acomodos);

    for ($i = 0; $i < $mitad; $i++) {
        // Columna izquierda
        $pdf->SetX(10);
        $pdf->Cell(95, 4, '- ' . $acomodosArray[$i], 0, 0);

        // Columna derecha (si existe)
        if (isset($acomodosArray[$i + $mitad])) {
            $pdf->Cell(95, 4, '- ' . $acomodosArray[$i + $mitad], 0, 1);
        } else {
            $pdf->Ln();
        }
    }
}

$pdf->Output();
