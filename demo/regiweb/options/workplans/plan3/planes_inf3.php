<?php
require_once '../../../../app.php';

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
$pdf->Cell(0, 7, __("PLAN DE TRABAJO 3"), 0, 1, 'C');
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
$pdf->Cell(0, 5, implode(", ", $niveles), 0, 1);
$pdf->Ln(2);

// Tema y Pre-requisito
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(95, 6, __("Tema"), 1, 0, 'C', true);
$pdf->Cell(95, 6, __("Pre-requisito"), 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(95, 5, $workPlan->tema ?? '', 1, 0);
$pdf->Cell(95, 5, $workPlan->pre1 ?? '', 1, 1);
$pdf->Ln(2);

// Objetivos
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(0, 6, __("Objetivo"), 1, 1, 'C', true);

// Conceptual
if ($workPlan->obj1 || $workPlan->ent1 || $workPlan->ent2 || $workPlan->ent3 || $workPlan->ent4) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 5, __("Conceptual") . ":", 0, 1);
    $pdf->SetFont('Arial', '', 9);

    if ($workPlan->obj1) $pdf->MultiCell(0, 5, "- " . $workPlan->obj1);
    if ($workPlan->ent1) $pdf->MultiCell(0, 5, "- " . $workPlan->ent1);
    if ($workPlan->ent2) $pdf->MultiCell(0, 5, "- " . $workPlan->ent2);
    if ($workPlan->ent3) $pdf->MultiCell(0, 5, "- " . $workPlan->ent3);
    if ($workPlan->ent4) $pdf->MultiCell(0, 5, "- " . $workPlan->ent4);
    $pdf->Ln(1);
}

// Procedimental
if ($workPlan->obj2 || $workPlan->ent5 || $workPlan->ent6 || $workPlan->ent7 || $workPlan->ent8) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 5, __("Procedimental") . ":", 0, 1);
    $pdf->SetFont('Arial', '', 9);

    if ($workPlan->obj2) $pdf->MultiCell(0, 5, "- " . $workPlan->obj2);
    if ($workPlan->ent5) $pdf->MultiCell(0, 5, "- " . $workPlan->ent5);
    if ($workPlan->ent6) $pdf->MultiCell(0, 5, "- " . $workPlan->ent6);
    if ($workPlan->ent7) $pdf->MultiCell(0, 5, "- " . $workPlan->ent7);
    if ($workPlan->ent8) $pdf->MultiCell(0, 5, "- " . $workPlan->ent8);
    $pdf->Ln(1);
}

// Actitudinal
if ($workPlan->obj3 || $workPlan->ent9 || $workPlan->ent10 || $workPlan->ent11 || $workPlan->ent12) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 5, __("Actitudinal") . ":", 0, 1);
    $pdf->SetFont('Arial', '', 9);

    if ($workPlan->obj3) $pdf->MultiCell(0, 5, "- " . $workPlan->obj3);
    if ($workPlan->ent9) $pdf->MultiCell(0, 5, "- " . $workPlan->ent9);
    if ($workPlan->ent10) $pdf->MultiCell(0, 5, "- " . $workPlan->ent10);
    if ($workPlan->ent11) $pdf->MultiCell(0, 5, "- " . $workPlan->ent11);
    if ($workPlan->ent12) $pdf->MultiCell(0, 5, "- " . $workPlan->ent12);
    $pdf->Ln(1);
}

// Integración
if ($workPlan->integracion) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 5, __("Integración") . ":", 0, 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->MultiCell(0, 5, $workPlan->integracion);
}
$pdf->Ln(2);

$pdf->AddPage();
// Encabezados de las columnas
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(95, 6, __("SECUENCIA DE ACTIVIDADES"), 1, 0, 'C', true);
$pdf->Cell(95, 6, __("EVALUACION INFORMATIVA"), 1, 1, 'C', true);

// Contenido de las columnas - sin formato de tabla
$pdf->SetFont('Arial', '', 8);

// Actividad
if ($workPlan->act1 == 'Si') {
    $pdf->Cell(95, 4, __("Actividad"), 0, 0);
    $pdf->Cell(95, 4, "", 0, 1);
}

// Actividades Exploración/Conceptualización/Aplicación
$actividades = [];
if ($workPlan->act2 == 'Si') $actividades[] = __("Exploración");
if ($workPlan->act3 == 'Si') $actividades[] = __("Conceptualización");
if ($workPlan->act4 == 'Si') $actividades[] = __("Aplicación");
if (!empty($actividades)) {
    $pdf->Cell(95, 4, implode(", ", $actividades), 0, 0);
    $pdf->Cell(95, 4, "", 0, 1);
}

// 1. Inicio (columna izquierda) | 4. Aplicaciones (columna derecha)
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(95, 4, "1. " . __("Inicio") . ":", 0, 0);
$pdf->Cell(95, 4, "4. " . __("Aplicaciones") . ":", 0, 1);
$pdf->SetFont('Arial', '', 8);

// Primera línea de aplicaciones
$aplicaciones = [];
if ($workPlan->eva1 == 'Si') $aplicaciones[] = __("Aplicaciones");
if ($workPlan->eva2 == 'Si') $aplicaciones[] = __("Texto");
if ($workPlan->eva3 == 'Si') $aplicaciones[] = __("Cuaderno");
if ($workPlan->eva4 == 'Si') $aplicaciones[] = __("Fichas");

$lineaAplicaciones = !empty($aplicaciones) ? implode(", ", $aplicaciones) : "";

// Items de Inicio
$inicios = [];
if ($workPlan->ini1 == 'Si') $inicios[] = "  - " . __("Inicio");
if ($workPlan->ini2 == 'Si') $inicios[] = "  - " . __("Repaso clase anterior");
if ($workPlan->ini3 == 'Si') $inicios[] = "  - " . __("Corrección de asignación");
if ($workPlan->ini4 == 'Si') $inicios[] = "  - " . __("Refuerzo");
if ($workPlan->ini5 == 'Si') $inicios[] = "  - " . __("Introducción al tema");
if ($workPlan->ini6 == 'Si') $inicios[] = "  - " . __("Torbellino de ideas");
if ($workPlan->ini7 == 'Si') $inicios[] = "  - " . __("Uso de manipulativo");

$inicioIndex = 0;

// Primera línea de inicio con aplicaciones
if ($inicioIndex < count($inicios)) {
    $pdf->Cell(95, 4, $inicios[$inicioIndex], 0, 0);
    $inicioIndex++;
} else {
    $pdf->Cell(95, 4, "", 0, 0);
}
$pdf->Cell(95, 4, $lineaAplicaciones, 0, 1);

// Tabla de Prácticas (columna derecha) - 3 líneas
// Continuar con inicios mientras se muestra la tabla
for ($i = 0; $i < 3; $i++) {
    if ($inicioIndex < count($inicios)) {
        $pdf->Cell(95, 3, $inicios[$inicioIndex], 0, 0);
        $inicioIndex++;
    } else {
        $pdf->Cell(95, 3, "", 0, 0);
    }

    // Tabla derecha
    $pdf->SetFont('Arial', 'B', 7);
    if ($i == 0) {
        $pdf->Cell(19, 3, "", 1, 0, 'C');
        $pdf->Cell(15, 3, __("Pág."), 1, 0, 'C');
        $pdf->Cell(25, 3, __("Ejercicios"), 1, 0, 'C');
        $pdf->Cell(18, 3, __("Impares"), 1, 0, 'C');
        $pdf->Cell(18, 3, __("Pares"), 1, 1, 'C');
    } else if ($i == 1) {
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(19, 3, __("Prácticas"), 1, 0);
        $pdf->Cell(15, 3, $workPlan->tab1 ?? '', 1, 0);
        $pdf->Cell(25, 3, $workPlan->tab3 ?? '', 1, 0);
        $pdf->Cell(18, 3, $workPlan->tab5 ?? '', 1, 0);
        $pdf->Cell(18, 3, $workPlan->tab7 ?? '', 1, 1);
    } else {
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(19, 3, __("Asignación"), 1, 0);
        $pdf->Cell(15, 3, $workPlan->tab2 ?? '', 1, 0);
        $pdf->Cell(25, 3, $workPlan->tab4 ?? '', 1, 0);
        $pdf->Cell(18, 3, $workPlan->tab6 ?? '', 1, 0);
        $pdf->Cell(18, 3, $workPlan->tab8 ?? '', 1, 1);
    }
    $pdf->SetFont('Arial', '', 8);
}

// Continuar con Inicio y Selección
$selecciones = [];
if ($workPlan->sel1 == 'Si') $selecciones[] = "- " . __("Quiz");
if ($workPlan->sel2 == 'Si') $selecciones[] = "- " . __("Examen");
if ($workPlan->sel3 == 'Si') $selecciones[] = "- " . __("Informes");
if ($workPlan->sel4 == 'Si') {
    $selecciones[] = "- " . __("Proyecto del día") . " " . ($workPlan->pro1 ?? '') . " " . __("al") . " " . ($workPlan->pro2 ?? '') . " " . __("del mes");
}
if ($workPlan->sel5 == 'Si' && $workPlan->otro) {
    $selecciones[] = "- " . __("Otros") . ": " . $workPlan->otro;
}

$seleccionIndex = 0;
$maxLines = max(count($inicios) - $inicioIndex, count($selecciones));

for ($i = 0; $i < $maxLines; $i++) {
    if ($inicioIndex < count($inicios)) {
        $pdf->Cell(95, 4, $inicios[$inicioIndex], 0, 0);
        $inicioIndex++;
    } else {
        $pdf->Cell(95, 4, "", 0, 0);
    }

    if ($seleccionIndex < count($selecciones)) {
        $pdf->Cell(95, 4, $selecciones[$seleccionIndex], 0, 1);
        $seleccionIndex++;
    } else {
        $pdf->Cell(95, 4, "", 0, 1);
    }
}

// Otros - Inicio
if ($workPlan->ot1 == 'Si') {
    $pdf->Cell(95, 4, "  - " . __("Otros"), 0, 0);
    if ($seleccionIndex < count($selecciones)) {
        $pdf->Cell(95, 4, $selecciones[$seleccionIndex], 0, 1);
        $seleccionIndex++;
    } else {
        $pdf->Cell(95, 4, "", 0, 1);
    }

    if ($workPlan->otr1) {
        $pdf->Cell(95, 4, "    " . $workPlan->otr1, 0, 0);
        if ($seleccionIndex < count($selecciones)) {
            $pdf->Cell(95, 4, $selecciones[$seleccionIndex], 0, 1);
            $seleccionIndex++;
        } else {
            $pdf->Cell(95, 4, "", 0, 1);
        }
    }
    if ($workPlan->otr2) {
        $pdf->Cell(95, 4, "    " . $workPlan->otr2, 0, 0);
        if ($seleccionIndex < count($selecciones)) {
            $pdf->Cell(95, 4, $selecciones[$seleccionIndex], 0, 1);
            $seleccionIndex++;
        } else {
            $pdf->Cell(95, 4, "", 0, 1);
        }
    }
}

// Completar selecciones restantes
while ($seleccionIndex < count($selecciones)) {
    $pdf->Cell(95, 4, "", 0, 0);
    $pdf->Cell(95, 4, $selecciones[$seleccionIndex], 0, 1);
    $seleccionIndex++;
}

// 2. Desarrollo | 5. Assessment
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(95, 4, "2. " . __("Desarrollo") . ":", 0, 0);
$pdf->Cell(95, 4, "5. " . __("Assessment") . ":", 0, 1);
$pdf->SetFont('Arial', '', 8);

$desarrollos = [];
if ($workPlan->des1 == 'Si') $desarrollos[] = "  - " . __("Desarrollo");
if ($workPlan->des2 == 'Si') $desarrollos[] = "  - " . __("Presentación de la temática");
if ($workPlan->des3 == 'Si') $desarrollos[] = "  - " . __("Definición y presentación de los conceptos");
if ($workPlan->des4 == 'Si') $desarrollos[] = "  - " . __("Listados o requisitos de un vocabulario");
if ($workPlan->des5 == 'Si') $desarrollos[] = "  - " . __("Listados de propiedades, características, detalles, etc.");
if ($workPlan->des6 == 'Si') $desarrollos[] = "  - " . __("Ejemplicación sobre los procesos");
if ($workPlan->des7 == 'Si') $desarrollos[] = "  - " . __("Uso de la tecnología");

$assessments = [];
if ($workPlan->as1 == 'Si') $assessments[] = "  - " . __("Assessment");
if ($workPlan->as2 == 'Si') $assessments[] = "  - " . __("Lista de cotejo");
if ($workPlan->as3 == 'Si') $assessments[] = "  - " . __("Tirilla cómica");
if ($workPlan->as4 == 'Si') $assessments[] = "  - " . __("Diario reflexivo");
if ($workPlan->as5 == 'Si') $assessments[] = "  - " . __("Mapa de concepto");
if ($workPlan->as6 == 'Si') $assessments[] = "  - " . __("Organizador gráfico");
if ($workPlan->as7 == 'Si') $assessments[] = "  - " . __("Aprendizaje cooperativo");
if ($workPlan->as8 == 'Si') $assessments[] = "  - " . __("Porfolio");

$maxLines = max(count($desarrollos), count($assessments));

for ($i = 0; $i < $maxLines; $i++) {
    $left = $i < count($desarrollos) ? $desarrollos[$i] : "";
    $right = $i < count($assessments) ? $assessments[$i] : "";
    $pdf->Cell(95, 4, $left, 0, 0);
    $pdf->Cell(95, 4, $right, 0, 1);
}

// Otros - Desarrollo y Assessment
$otrosDesarrollo = [];
if ($workPlan->ot2 == 'Si') {
    $otrosDesarrollo[] = "  - " . __("Otros");
    if ($workPlan->otr3) $otrosDesarrollo[] = "    " . $workPlan->otr3;
    if ($workPlan->otr4) $otrosDesarrollo[] = "    " . $workPlan->otr4;
}

$otrosAssessment = [];
if ($workPlan->ot4 == 'Si') {
    $otrosAssessment[] = "  - " . __("Otros");
    if ($workPlan->otr7) $otrosAssessment[] = "    " . $workPlan->otr7;
    if ($workPlan->otr8) $otrosAssessment[] = "    " . $workPlan->otr8;
}

$maxLines = max(count($otrosDesarrollo), count($otrosAssessment));

for ($i = 0; $i < $maxLines; $i++) {
    $left = $i < count($otrosDesarrollo) ? $otrosDesarrollo[$i] : "";
    $right = $i < count($otrosAssessment) ? $otrosAssessment[$i] : "";
    $pdf->Cell(95, 4, $left, 0, 0);
    $pdf->Cell(95, 4, $right, 0, 1);
}

// 3. Cierre
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(95, 4, "3. " . __("Cierre") . ":", 0, 0);
$pdf->Cell(95, 4, "", 0, 1);
$pdf->SetFont('Arial', '', 8);

$cierres = [];
if ($workPlan->cie1 == 'Si') $cierres[] = "  - " . __("Cierre");
if ($workPlan->cie2 == 'Si') $cierres[] = "  - " . __("Resumir materias discutido");
if ($workPlan->cie3 == 'Si') $cierres[] = "  - " . __("Aclarar dudas");
if ($workPlan->cie4 == 'Si') $cierres[] = "  - " . __("Llegar a conclusiones");
if ($workPlan->cie5 == 'Si') $cierres[] = "  - " . __("Discusión del trabajo asignados");

foreach ($cierres as $cierre) {
    $pdf->Cell(95, 4, $cierre, 0, 0);
    $pdf->Cell(95, 4, "", 0, 1);
}

// Otros - Cierre
if ($workPlan->ot3 == 'Si') {
    $pdf->Cell(95, 4, "  - " . __("Otros"), 0, 0);
    $pdf->Cell(95, 4, "", 0, 1);

    if ($workPlan->otr5) {
        $pdf->Cell(95, 4, "    " . $workPlan->otr5, 0, 0);
        $pdf->Cell(95, 4, "", 0, 1);
    }
    if ($workPlan->otr6) {
        $pdf->Cell(95, 4, "    " . $workPlan->otr6, 0, 0);
        $pdf->Cell(95, 4, "", 0, 1);
    }
}

$pdf->Ln(3);

// Autoevaluación
if ($workPlan->autoeva) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 5, __("Autoevaluación o observaciones") . ":", 0, 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->MultiCell(0, 5, $workPlan->autoeva);
}

$pdf->Output();
