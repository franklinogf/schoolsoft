<?php
require_once '../../../app.php';

use App\Models\Admin;
use App\Models\Teacher;
use App\Models\UnitPlan;
use Classes\PDF;
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

$teacher = Teacher::find(Session::id());
$school = Admin::primaryAdmin();

// Verificar que el plan pertenece al maestro
if ($unitPlan->id_profesor != $teacher->id) {
    die('Error: No autorizado');
}

$pdf = new PDF();
$pdf->SetTitle(__("Plan de Unidad") . " - " . $unitPlan->titulo, true);
$pdf->Fill(0, 0, 0);
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 15);

// Título
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 7, __('PLAN DE UNIDAD'), 0, 1, 'C');
$pdf->Ln(3);

// Información General
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($pdf->GetStringWidth(__('TÍTULO DE LA UNIDAD:') . ' ') + 3, 6, __('TÍTULO DE LA UNIDAD:'), 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(70, 6, $unitPlan->titulo, 'B', 0);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($pdf->GetStringWidth(__('FECHA:') . ' ') + 3, 6, __('FECHA:'), 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(25, 6, $unitPlan->fecha ?? '', 'B', 0, 'C');

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($pdf->GetStringWidth(__('DURACIÓN:') . ' ') + 3, 6, __('DURACIÓN:'), 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(15, 6, $unitPlan->duracion . ' ' . __('semanas'), 'B', 1, 'C');

$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($pdf->GetStringWidth(__('MAESTRO(A):') . ' ') + 3, 6, __('MAESTRO(A):'), 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 6, $unitPlan->profesor, 'B', 0);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($pdf->GetStringWidth(__('MATERIA:') . ' ') + 3, 6, __('MATERIA:'), 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(30, 6, $unitPlan->materia, 'B', 0, 'C');

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($pdf->GetStringWidth(__('GRADO:') . ' ') + 3, 6, __('GRADO:'), 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(30, 6, substr($unitPlan->materia, 0, 3), 'B', 1, 'C');

$pdf->Ln(3);

// Helper function for checkboxes
function drawCheckbox($pdf, $checked, $label = '', $newLine = false)
{
    $pdf->Rect($pdf->GetX(), $pdf->GetY() + 1, 3, 3, $checked === 'si' ? 'DF' : 'D');
    $pdf->Cell(5, 5, '');
    if ($label) {
        $pdf->Cell($pdf->GetStringWidth($label) + 5, 5, $label, 0, $newLine ? 1 : 0);
    }
}

// Tema Transversal
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, __('TEMA TRANSVERSAL:'), 0, 1);
$pdf->SetFont('Arial', '', 9);

$transversales = [
    'Educar para el amor al prójimo',
    'Educar para la transcendencia',
    'Educación para la promoción de la vida',
    'Educar para el liderazgo moral',
    'Educación para la ciudadanía consciente y activa',
    'Educar para la comunión',
    'Educar para la conservación del medio ambiente'
];

foreach ($transversales as $i => $label) {
    $index = $i + 1;
    drawCheckbox($pdf, $unitPlan->{"transversal{$index}"}, $label);
    if (in_array($index, [2, 3, 4, 6, 7])) {
        $pdf->Ln();
    }
}

$pdf->Ln(3);

// Integración
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, __('INTEGRACIÓN:'), 0, 1);
$pdf->SetFont('Arial', '', 9);

$integraciones = [
    'Español',
    'Inglés',
    'Estudios Sociales',
    'Ciencia',
    'Matemáticas',
    'Bellas Artes',
    'Educación Física',
    'Salud Escolar',
    'Tecnología'
];

foreach ($integraciones as $i => $label) {
    $index = $i + 1;
    drawCheckbox($pdf, $unitPlan->{"integracion{$index}"}, $label);
    if ($index == 7) {
        $pdf->Ln();
    }
}

$pdf->Ln(5);

// Estándares
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell($pdf->GetStringWidth(__('Estándares: a)') . ' ') + 3, 5, __('Estándares: a)'), 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(60, 5, $unitPlan->estandar1 ?? '', 'B', 0);
$pdf->Cell(10);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell($pdf->GetStringWidth('b) ') + 3, 5, 'b)', 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(60, 5, $unitPlan->estandar2 ?? '', 'B', 1);

$pdf->Ln(2);

// Meta
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell($pdf->GetStringWidth(__('Meta:') . ' ') + 3, 5, __('Meta:'), 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 5, $unitPlan->meta ?? '', 'B', 1);

$pdf->Ln(5);

// ETAPA 1 - Resultados Esperados
$pdf->SetFillColor(200);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, __('ETAPA 1 - RESULTADOS ESPERADOS'), 1, 1, 'C', true);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, __('RESUMEN DE LA UNIDAD'), 1, 1, 'C', true);
$pdf->SetFillColor(255);

$resumenY = $pdf->GetY();
$pdf->Rect($pdf->GetX(), $resumenY, 190, 20);
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(190, 4, $unitPlan->resumen ?? '');
$pdf->SetY($resumenY + 20);

$pdf->Ln(2);

// Preguntas Esenciales y Entendimiento Duradero
$preguntasY = $pdf->GetY();
$pdf->SetFillColor(200);

// Columna izquierda - Preguntas Esenciales
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(95, 6, __('PREGUNTAS ESENCIALES'), 1, 0, 'C', true);

// Columna derecha - Entendimiento Duradero
$pdf->Cell(95, 6, __('ENTENDIMIENTO DURADERO'), 1, 1, 'C', true);
$pdf->SetFillColor(255);

$pdf->Rect(10, $pdf->GetY(), 95, 45);
$pdf->Rect(105, $preguntasY + 6, 95, 45);

$pdf->SetFont('Arial', '', 9);
for ($i = 1; $i <= 5; $i++) {
    $pdf->SetX(10);
    $pdf->Cell(10, 5, "PE{$i}:");
    $pdf->Cell(82, 5, $unitPlan->{"pe{$i}"} ?? '', 'B', 0);

    $pdf->SetX(105);
    $pdf->Cell(10, 5, "ED{$i}:");
    $pdf->Cell(82, 5, $unitPlan->{"ed{$i}"} ?? '', 'B', 1);
    $pdf->Ln(1);
}

$pdf->SetY($preguntasY + 51);
$pdf->Ln(2);

// Objetivos de Transferencia
$pdf->Rect(10, $pdf->GetY(), 190, 10);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell($pdf->GetStringWidth(__('OBJETIVOS DE TRANSFERENCIA (GENERAL):') . ' ') + 3, 5, __('OBJETIVOS DE TRANSFERENCIA (GENERAL):'), 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(0, 5, $unitPlan->objetivo_general ?? '');

$pdf->Ln(5);

// Objetivos de Adquisición
$pdf->Rect(10, $pdf->GetY(), 190, 30);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 5, __('Objetivos de Adquisición:'), 0, 1);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 4, __('Al finalizar esta unidad, el estudiante:'), 0, 1);
$pdf->MultiCell(0, 4, $unitPlan->objetivo_adquisicion ?? '');

// Nueva página para Etapa 2
$pdf->AddPage();

// ETAPA 2
$pdf->SetFillColor(200);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, __('ETAPA 2 - EVIDENCIA PARA EVALUAR APRENDIZAJE'), 1, 1, 'C', true);
$pdf->SetFillColor(255);

$etapa2Y = $pdf->GetY();

// Tareas de Desempeño Auténtico
$pdf->SetFillColor(200);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(63.3, 6, __('TAREAS DE DESEMPEÑO AUTÉNTICO'), 1, 0, 'C', true);

// Otra Evidencia
$pdf->Cell(63.3, 6, __('OTRA EVIDENCIA'), 1, 0, 'C', true);

// Actividades
$pdf->Cell(63.3, 6, __('ACTIVIDADES'), 1, 1, 'C', true);
$pdf->SetFillColor(255);

// Contenido
$pdf->Rect(10, $pdf->GetY(), 63.3, 100);
$pdf->Rect(73.3, $etapa2Y + 6, 63.3, 100);
$pdf->Rect(136.6, $etapa2Y + 6, 63.3, 100);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(10, $pdf->GetY());
$pdf->MultiCell(63.3, 4, $unitPlan->tareas ?? '');

$pdf->SetXY(73.3, $etapa2Y + 6);
$pdf->MultiCell(63.3, 4, $unitPlan->otra ?? '');

$pdf->SetXY(136.6, $etapa2Y + 6);
$pdf->MultiCell(63.3, 4, $unitPlan->actividades ?? '');

// Observaciones
$pdf->SetY($etapa2Y + 106);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(63.3, 5, __('Observaciones:'), 0, 0);
$pdf->SetX(73.3);
$pdf->Cell(63.3, 5, __('Observaciones:'), 0, 0);
$pdf->SetX(136.6);
$pdf->Cell(63.3, 5, __('Observaciones:'), 0, 1);

$pdf->Rect(10, $pdf->GetY(), 63.3, 50);
$pdf->Rect(73.3, $etapa2Y + 111, 63.3, 50);
$pdf->Rect(136.6, $etapa2Y + 111, 63.3, 50);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(10, $pdf->GetY());
$pdf->MultiCell(63.3, 4, $unitPlan->tareas_observaciones ?? '');

$pdf->SetXY(73.3, $etapa2Y + 111);
$pdf->MultiCell(63.3, 4, $unitPlan->otra_observaciones ?? '');

$pdf->SetXY(136.6, $etapa2Y + 111);
$pdf->MultiCell(63.3, 4, $unitPlan->actividades_observaciones ?? '');

// Nueva página para Etapa 3
$pdf->AddPage();

// ETAPA 3
$pdf->SetFillColor(200);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, __('ETAPA 3 - PLAN DE APRENDIZAJE'), 1, 1, 'C', true);
$pdf->SetFillColor(0);

$pdf->Ln(2);

// Expectativa, Estrategia, Objetivos
$pdf->Rect(10, $pdf->GetY(), 190, 20);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell($pdf->GetStringWidth(__('Expectativa o indicador:') . ' ') + 3, 5, __('Expectativa o indicador:'), 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 5, $unitPlan->expectativa ?? '', 'B', 1);
$pdf->Ln(1);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell($pdf->GetStringWidth(__('Estrategia general:') . ' ') + 3, 5, __('Estrategia general:'), 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 5, $unitPlan->estrategia ?? '', 'B', 1);
$pdf->Ln(1);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell($pdf->GetStringWidth(__('Objetivos:') . ' ') + 3, 5, __('Objetivos:'), 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 5, $unitPlan->objetivos ?? '', 'B', 1);

$pdf->Ln(3);

// Tabla semanal
$dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
$niveles = ['Memorístico', 'Procesamiento', 'Estratégico', 'Extendido'];
$acomodos = [
    'Ubicación adecuada del pupitre',
    'Tiempo adicional',
    'Ayuda individualizada',
    'Tareas y exámenes mas cortos',
    'Refuerzo positivo',
    'Otro'
];

$pdf->SetFillColor(220);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(25, 5, __('Día'), 1, 0, 'C', true);
foreach ($dias as $dia) {
    $pdf->Cell(33, 5, $dia, 1, 0, 'C', true);
}
$pdf->Ln();

$pdf->Cell(25, 5, __('Fecha'), 1, 0, 'C', true);
foreach (range(1, 5) as $i) {
    $fecha = $unitPlan->{"fecha{$i}"} ?? '';
    if ($fecha) {
        $fecha = date('d/m/Y', strtotime($fecha));
    }
    $pdf->Cell(33, 5, $fecha, 1, 0, 'C', true);
}
$pdf->Ln();
$pdf->SetFillColor(0);

// Nivel de Profundidad
$pdf->SetFont('Arial', 'B', 7);
$nivelY = $pdf->GetY();
$pdf->MultiCell(25, 5, __('Nivel de') . "\n" . __('Profundidad'), 1, 'C');
$pdf->SetXY(10, $nivelY);
$pdf->Cell(25, 20, '', 1, 0, 'C');
$pdf->SetFont('Arial', '', 7);

foreach (range(1, 5) as $day) {
    $x = 10 + 25 + (($day - 1) * 33);
    $y = $nivelY;
    $pdf->Rect($x, $y, 33, 20);

    foreach (range(1, 4) as $level) {
        $pdf->SetXY($x + 0.5, $y + ($level - 1) * 5 + 0.5);
        drawCheckbox($pdf, $unitPlan->{"nivel{$day}_{$level}"});
        $pdf->SetX($x + 5);
        $pdf->Cell(27, 5, $niveles[$level - 1]);
    }
}
$pdf->SetY($nivelY + 20);

// Inicio
$pdf->SetFont('Arial', 'B', 8);
$inicioY = $pdf->GetY();
$pdf->Cell(25, 25, __('Inicio'), 1, 0, 'C');
$pdf->SetFont('Arial', '', 7);

foreach (range(1, 5) as $i) {
    $x = 10 + 25 + (($i - 1) * 33);
    $y = $inicioY;
    $pdf->Rect($x, $y, 33, 25);
    $pdf->SetXY($x + 0.5, $y + 0.5);
    $pdf->MultiCell(32, 3, $unitPlan->{"inicio{$i}"} ?? '');
}
$pdf->SetY($inicioY + 25);

// Desarrollo
$pdf->SetFont('Arial', 'B', 8);
$desarrolloY = $pdf->GetY();
$pdf->Cell(25, 25, __('Desarrollo'), 1, 0, 'C');
$pdf->SetFont('Arial', '', 7);

foreach (range(1, 5) as $i) {
    $x = 10 + 25 + (($i - 1) * 33);
    $y = $desarrolloY;
    $pdf->Rect($x, $y, 33, 25);
    $pdf->SetXY($x + 0.5, $y + 0.5);
    $pdf->MultiCell(32, 3, $unitPlan->{"desarrollo{$i}"} ?? '');
}
$pdf->SetY($desarrolloY + 25);

// Cierre
$pdf->SetFont('Arial', 'B', 8);
$cierreY = $pdf->GetY();
$pdf->Cell(25, 25, __('Cierre'), 1, 0, 'C');
$pdf->SetFont('Arial', '', 7);

foreach (range(1, 5) as $i) {
    $x = 10 + 25 + (($i - 1) * 33);
    $y = $cierreY;
    $pdf->Rect($x, $y, 33, 25);
    $pdf->SetXY($x + 0.5, $y + 0.5);
    $pdf->MultiCell(32, 3, $unitPlan->{"cierre{$i}"} ?? '');
}
$pdf->SetY($cierreY + 25);

// Acomodo Razonable
$pdf->SetFont('Arial', 'B', 7);
$acomodoY = $pdf->GetY();
$pdf->MultiCell(25, 5, __('Acomodo') . "\n" . __('Razonable'), 1, 'C');
$pdf->SetXY(10, $acomodoY);
$pdf->Cell(25, 40, '', 1, 0, 'C');
$pdf->SetFont('Arial', '', 6);

foreach (range(1, 5) as $day) {
    $x = 10 + 25 + (($day - 1) * 33);
    $y = $acomodoY;
    $pdf->Rect($x, $y, 33, 40);

    foreach (range(1, 6) as $acomodoIdx) {
        $pdf->SetXY($x + 0.5, $y + ($acomodoIdx - 1) * 6.5 + 0.5);
        drawCheckbox($pdf, $unitPlan->{"acomodo{$day}_{$acomodoIdx}"});
        $pdf->SetX($x + 4.5);
        if ($acomodoIdx == 6 && !empty($unitPlan->{"otro{$day}"})) {
            $pdf->Cell(28, 3, $unitPlan->{"otro{$day}"});
        } else {
            $pdf->MultiCell(28, 3, $acomodos[$acomodoIdx - 1]);
        }
    }
}

$pdf->Output('I', 'Plan_Unidad_' . $unitPlan->id . '.pdf');
