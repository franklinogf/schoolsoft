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
$pdf->SetTitle(__("Plan de trabajo 2") . " - " . $workPlan->plan, true);
$pdf->Fill();
$pdf->AddPage();

// Título
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 7, __("PLAN DE TRABAJO"), 0, 1, 'C');
$pdf->Ln(3);

// Plan de
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, __("PLAN DE") . ": " . ($workPlan->plan ?? ''), 0, 1);
$pdf->SetFont('Arial', '', 10);
if ($workPlan->estandares == 'Si') {
    $pdf->Cell(0, 5, __("Estándares de Contenido") . ": Si", 0, 1);
}
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

// Temas (plan2 tiene hasta 5)
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(45, 5, __("Temas") . ":", 0, 1);
$pdf->SetFont('Arial', '', 10);
if ($workPlan->tema1) $pdf->Cell(0, 4, $workPlan->tema1, 0, 1);
if ($workPlan->tema2) $pdf->Cell(0, 4, $workPlan->tema2, 0, 1);
if ($workPlan->tema3) $pdf->Cell(0, 4, $workPlan->tema3, 0, 1);
if ($workPlan->tema4) $pdf->Cell(0, 4, $workPlan->tema4, 0, 1);
if ($workPlan->tema5) $pdf->Cell(0, 4, $workPlan->tema5, 0, 1);
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
if ($workPlan->np5 == 'Si') $niveles[] = "Nivel 5";
if (!empty($niveles)) {
    $pdf->Cell(0, 5, implode(", ", $niveles), 0, 1);
}
$pdf->Ln(2);

// Unidad y Capítulo
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(95, 6, __("Unidad"), 1, 0, 'C', true);
$pdf->Cell(95, 6, __("Capítulo"), 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(95, 5, $workPlan->tema ?? '', 1, 0);
$pdf->Cell(95, 5, $workPlan->pre1 ?? '', 1, 1);
$pdf->Ln(2);

// Objetivos
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(0, 6, __("Objetivo"), 1, 1, 'C', true);

// Conceptual
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 5, __("Conceptual") . ":", 0, 1);
$pdf->SetFont('Arial', '', 8);
if ($workPlan->obj1) $pdf->Cell(0, 4, $workPlan->obj1, 0, 1);
if ($workPlan->ent1) $pdf->Cell(0, 4, $workPlan->ent1, 0, 1);
if ($workPlan->ent2) $pdf->Cell(0, 4, $workPlan->ent2, 0, 1);
if ($workPlan->ent3) $pdf->Cell(0, 4, $workPlan->ent3, 0, 1);
if ($workPlan->ent4) $pdf->Cell(0, 4, $workPlan->ent4, 0, 1);
$pdf->Ln(2);

// Procedimental
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 5, __("Procedimental") . ":", 0, 1);
$pdf->SetFont('Arial', '', 8);
if ($workPlan->obj2) $pdf->Cell(0, 4, $workPlan->obj2, 0, 1);
if ($workPlan->ent5) $pdf->Cell(0, 4, $workPlan->ent5, 0, 1);
if ($workPlan->ent6) $pdf->Cell(0, 4, $workPlan->ent6, 0, 1);
if ($workPlan->ent7) $pdf->Cell(0, 4, $workPlan->ent7, 0, 1);
if ($workPlan->ent8) $pdf->Cell(0, 4, $workPlan->ent8, 0, 1);
$pdf->Ln(2);

// Actitudinal
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 5, __("Actitudinal") . ":", 0, 1);
$pdf->SetFont('Arial', '', 8);
if ($workPlan->obj3) $pdf->Cell(0, 4, $workPlan->obj3, 0, 1);
if ($workPlan->ent9) $pdf->Cell(0, 4, $workPlan->ent9, 0, 1);
if ($workPlan->ent10) $pdf->Cell(0, 4, $workPlan->ent10, 0, 1);
if ($workPlan->ent11) $pdf->Cell(0, 4, $workPlan->ent11, 0, 1);
if ($workPlan->ent12) $pdf->Cell(0, 4, $workPlan->ent12, 0, 1);
$pdf->Ln(2);

// Integración
if ($workPlan->integracion) {
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(45, 5, __("Integración") . ":", 0, 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->MultiCell(0, 5, $workPlan->integracion);
    $pdf->Ln(2);
}

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
if ($workPlan->ini2 == 'Si') $inicio[] = __("Repaso clase anterior");
if ($workPlan->ini3 == 'Si') $inicio[] = __("Corrección de asignación");
if ($workPlan->ini4 == 'Si') $inicio[] = __("Refuerzo");
if ($workPlan->ini5 == 'Si') $inicio[] = __("Introducción al tema");
if ($workPlan->ini6 == 'Si') $inicio[] = __("Torbellino de ideas");
if ($workPlan->ini7 == 'Si') $inicio[] = __("Uso de manipulativo");

if (!empty($inicio)) {
    $leftContent[] = ['type' => 'section', 'text' => "1. " . __("Inicio") . ":"];
    foreach ($inicio as $item) {
        $leftContent[] = ['type' => 'item', 'text' => "  " . $item];
    }
}

// Desarrollo
$desarrollo = [];
if ($workPlan->des2 == 'Si') $desarrollo[] = __("Presentación de la temática");
if ($workPlan->des3 == 'Si') $desarrollo[] = __("Definición y presentación de los conceptos");
if ($workPlan->des4 == 'Si') $desarrollo[] = __("Listados o requisitos de un vocabulario");
if ($workPlan->des5 == 'Si') $desarrollo[] = __("Listados de propiedades, características, detalles, etc.");
if ($workPlan->des6 == 'Si') $desarrollo[] = __("Ejemplicación sobre los procesos");
if ($workPlan->des7 == 'Si') $desarrollo[] = __("Uso de la tecnología");

if (!empty($desarrollo)) {
    $leftContent[] = ['type' => 'section', 'text' => "2. " . __("Desarrollo") . ":"];
    foreach ($desarrollo as $item) {
        $leftContent[] = ['type' => 'item', 'text' => "  " . $item];
    }
}

// Cierre
$cierre = [];
if ($workPlan->cie2 == 'Si') $cierre[] = __("Resumir materias discutido");
if ($workPlan->cie3 == 'Si') $cierre[] = __("Aclarar dudas");
if ($workPlan->cie4 == 'Si') $cierre[] = __("Llegar a conclusiones");
if ($workPlan->cie5 == 'Si') $cierre[] = __("Discusión del trabajo asignados");

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
if ($workPlan->eva2 == 'Si') $aplicaciones[] = __("Texto");
if ($workPlan->eva3 == 'Si') $aplicaciones[] = __("Cuaderno");
if ($workPlan->eva4 == 'Si') $aplicaciones[] = __("Fichas");

if (!empty($aplicaciones)) {
    $rightContent[] = ['type' => 'header', 'text' => "4. " . __("Aplicaciones") . ": " . implode(", ", $aplicaciones)];
}

// Tabla de prácticas y asignación
if ($workPlan->tab1 || $workPlan->tab3 || $workPlan->tab5 || $workPlan->tab7) {
    $rightContent[] = ['type' => 'section', 'text' => __("Prácticas") . ": " . __("Pág.") . " " . ($workPlan->tab1 ?? '') . " " . __("Ejercicios") . " " . ($workPlan->tab3 ?? '')];
    if ($workPlan->tab5) $rightContent[] = ['type' => 'item', 'text' => "  " . __("Impares") . ": " . $workPlan->tab5];
    if ($workPlan->tab7) $rightContent[] = ['type' => 'item', 'text' => "  " . __("Pares") . ": " . $workPlan->tab7];
}

if ($workPlan->tab2 || $workPlan->tab4 || $workPlan->tab6 || $workPlan->tab8) {
    $rightContent[] = ['type' => 'section', 'text' => __("Asignación") . ": " . __("Pág.") . " " . ($workPlan->tab2 ?? '') . " " . __("Ejercicios") . " " . ($workPlan->tab4 ?? '')];
    if ($workPlan->tab6) $rightContent[] = ['type' => 'item', 'text' => "  " . __("Impares") . ": " . $workPlan->tab6];
    if ($workPlan->tab8) $rightContent[] = ['type' => 'item', 'text' => "  " . __("Pares") . ": " . $workPlan->tab8];
}

// Selección
$rightContent[] = ['type' => 'section', 'text' => "* " . __("seleccionados")];

$seleccion = [];
if ($workPlan->sel1 == 'Si') $seleccion[] = __("Quiz");
if ($workPlan->sel2 == 'Si') $seleccion[] = __("Examen");
if ($workPlan->sel3 == 'Si') $seleccion[] = __("Informes");
if ($workPlan->sel4 == 'Si') {
    $proyecto = __("Proyecto del día");
    if ($workPlan->pro1 || $workPlan->pro2) {
        $proyecto .= ": " . ($workPlan->pro1 ?? '') . " " . __("al") . " " . ($workPlan->pro2 ?? '') . " " . __("del mes");
    }
    $seleccion[] = $proyecto;
}
if ($workPlan->sel5 == 'Si' && $workPlan->otro) {
    $seleccion[] = __("Otros") . ": " . $workPlan->otro;
}

foreach ($seleccion as $item) {
    $rightContent[] = ['type' => 'item', 'text' => "  " . $item];
}

// Assessment
$assessment = [];
if ($workPlan->as2 == 'Si') $assessment[] = __("Lista de cotejo");
if ($workPlan->as3 == 'Si') $assessment[] = __("Tirilla cómica");
if ($workPlan->as4 == 'Si') $assessment[] = __("Diario reflexivo");
if ($workPlan->as5 == 'Si') $assessment[] = __("Mapa de concepto");
if ($workPlan->as6 == 'Si') $assessment[] = __("Organizador gráfico");
if ($workPlan->as7 == 'Si') $assessment[] = __("Aprendizaje cooperativo");
if ($workPlan->as8 == 'Si') $assessment[] = __("Porfolio");

if (!empty($assessment)) {
    $rightContent[] = ['type' => 'section', 'text' => "5. " . __("Assessment") . ":"];
    foreach ($assessment as $item) {
        $rightContent[] = ['type' => 'item', 'text' => "  " . $item];
    }
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

// Autoevaluación
if ($workPlan->autoeva) {
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 5, __("Autoevaluación o observaciones") . ":", 0, 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->MultiCell(0, 4, $workPlan->autoeva);
}

$pdf->Output();
