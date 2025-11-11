<?php
require_once '../../../../app.php';

use App\Models\Admin;
use App\Models\Teacher;
use App\Models\WorkPlan4;
use Classes\PDF;
use Classes\Session;

Session::is_logged();

$teacher = Teacher::find(Session::id());
$school = Admin::primaryAdmin();

$planId = $_GET['plan'] ?? null;

if (!$planId) {
    die('Plan ID no proporcionado');
}

$plan = WorkPlan4::find($planId);

if (!$plan || $plan->id_profesor != $teacher->id) {
    die('Plan no encontrado o no autorizado');
}

$pdf = new PDF();
$pdf->SetTitle('Plan de Artes Visuales', true);
$pdf->AddPage();
$pdf->Fill(0, 0, 0);

// Header
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, 'PLAN DE TRABAJO - ARTES VISUALES', 0, 1, 'C');
$pdf->Ln(5);

// Basic Information
$pdf->SetFont('Arial', '', 10);
$pdf->Cell($pdf->GetStringWidth('Unidad: ') + 2, 6, 'Unidad:');
$pdf->Cell(100, 5, $plan->unidad ?? '', 'B');
$pdf->Cell(20);
$pdf->Cell($pdf->GetStringWidth('Fecha: ') + 2, 6, 'Fecha:');
$pdf->Cell(50, 5, ($plan->fecha1 != '0000-00-00' && $plan->fecha1) ? $plan->fecha1 : '', 'B', 1, 'C');

$pdf->Cell($pdf->GetStringWidth('Temas: ') + 2, 6, 'Temas:');
$pdf->Cell(85, 5, $plan->temas ?? '', 'B');
$pdf->Cell(35);
$pdf->Cell($pdf->GetStringWidth('Fecha: ') + 2, 6, 'Fecha:');
$pdf->Cell(50, 5, ($plan->fecha2 != '0000-00-00' && $plan->fecha2) ? $plan->fecha2 : '', 'B', 1, 'C');

// Additional dates
$spaceBefore = $pdf->GetStringWidth('Temas: ') + 2 + 85 + 35;
for ($i = 3; $i <= 5; $i++) {
    $pdf->Cell($spaceBefore);
    $pdf->Cell($pdf->GetStringWidth('Fecha: ') + 2, 6, 'Fecha:');
    $dateField = "fecha{$i}";
    $pdf->Cell(50, 5, ($plan->$dateField != '0000-00-00' && $plan->$dateField) ? $plan->$dateField : '', 'B', 1, 'C');
}

$pdf->Ln(5);

// Fase and Niveles - Two boxes side by side
$pdf->SetFont('Arial', '', 12);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), 90, 25);
$f = $pdf->GetStringWidth('Fase: ');
$y = $pdf->GetY();
$pdf->Cell($f, 5, 'Fase: ');
$pdf->Cell(1);

$faseLabels = ['Exploración', 'Antes', 'Enfocar'];
for ($i = 1; $i <= 3; $i++) {
    if ($i > 1) {
        $pdf->Cell($f);
        $pdf->Cell(1);
    }
    $checked = ($plan->{"fase$i"} == 'Si') ? 'DF' : 'D';
    $pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
    $pdf->Cell(2);
    $pdf->Cell(87 - $f, 5, $faseLabels[$i - 1], 0, 1);
}

// Niveles box
$pdf->Rect($pdf->GetX(), $y, 90, 25);
$pdf->SetY($y);
$pdf->Cell(90);
$pdf->Cell(90, 5, 'Niveles de profundidad de Conocimiento', 0, 1, 'C');
$pdf->Ln(5);

$nivelesLabels = ['Memorístico', 'Estratégico', 'Procesamiento', 'Extendido'];
$pdf->Cell(95);
$checked = ($plan->niveles1 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
$pdf->Cell(2);
$pdf->Cell(35, 5, $nivelesLabels[0], 0, 0);
$pdf->Cell(5);
$checked = ($plan->niveles2 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
$pdf->Cell(2);
$pdf->Cell(35, 5, $nivelesLabels[1], 0, 1);
$pdf->Cell(95);
$checked = ($plan->niveles3 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
$pdf->Cell(2);
$pdf->Cell(35, 5, $nivelesLabels[2], 0, 0);
$pdf->Cell(5);
$checked = ($plan->niveles4 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
$pdf->Cell(2);
$pdf->Cell(35, 5, $nivelesLabels[3], 0, 1);

$pdf->Ln(5);

// Estándares and Expectativas - Two boxes side by side
$pdf->Rect($pdf->GetX(), $pdf->GetY(), 90, 40);
$y = $pdf->GetY();
$pdf->Cell(90, 5, 'Estándares:', 0, 1);
$pdf->Ln(5);

$estandaresLabels = [
    'Educación Estética',
    'Investigación Histórica, Social y Cultura',
    'Expresión Creativa',
    'Juicio Estético'
];

for ($i = 1; $i <= 4; $i++) {
    $pdf->Cell(5);
    $checked = ($plan->{"estandares$i"} == 'Si') ? 'DF' : 'D';
    $pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
    $pdf->Cell(2);
    $pdf->Cell(83, 5, $estandaresLabels[$i - 1], 0, 1);
}

// Expectativas box (códigos por día)
$pdf->Rect($pdf->GetX(), $y, 90, 40);
$pdf->SetY($y);
$pdf->Cell(90);
$pdf->Cell(90, 5, 'Estándares y Expectativas Códigos', 0, 1, 'C');
$pdf->Ln(5);

$dias = ['Lunes:', 'Martes:', 'Miércoles:', 'Jueves:', 'Viernes:'];
for ($i = 1; $i <= 5; $i++) {
    $pdf->Cell(95);
    $pdf->Cell(20, 5, $dias[$i - 1]);
    $pdf->Cell(62, 5, $plan->{"expectativas$i"} ?? '', 'B', 1);
}

$pdf->Ln(10);

// Objetivos and Avalúo - Two large boxes
$pdf->Cell(5);
$pdf->Cell(90, 5, 'Objetivos');
$pdf->Cell(5);
$x = $pdf->GetX();
$pdf->Cell(90, 5, 'Avalúo', 0, 1);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), 95, 120);
$pdf->Rect($pdf->GetX() + 95, $pdf->GetY(), 95, 120);
$pdf->SetFont('Arial', '', 10);
$y = $pdf->GetY();

// Left side: Conceptual
$pdf->Cell(95, 5, 'Conceptual (Conceptos, Principios, datos, hechos):', 0, 1);
$pdf->MultiCell(95, 5, $plan->conceptual ?? '', 0, 'L');

// Right side: Conceptual avalúo items
$pdf->SetXY($x, $y);
$checked = ($plan->avaluo1 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
$pdf->Cell(2);
$pdf->Cell(90, 5, 'Preguntas y Respuestas', 0, 1);
$pdf->SetX($x);
$checked = ($plan->avaluo2 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
$pdf->Cell(2);
$pdf->Cell(90, 5, 'Preguntas abiertas', 0, 1);
$pdf->SetX($x);
$checked = ($plan->avaluo3 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
$pdf->Cell(2);
$pdf->Cell(90, 5, 'Rúbrica', 0, 1);
$pdf->SetX($x);
$checked = ($plan->avaluo4 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
$pdf->Cell(2);
$pdf->Cell(90, 5, 'Portafolio', 0, 1);
$pdf->SetX($x);
$checked = ($plan->avaluo5 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
$pdf->Cell(2);
$pdf->Cell(90, 5, 'Ensayo', 0, 1);
$pdf->SetX($x);
$checked = ($plan->avaluo6 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
$pdf->Cell(2);
$pdf->Cell(90, 5, 'Tareas de ejecución', 0, 1);
$pdf->SetX($x);
$checked = ($plan->avaluo7 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
$pdf->Cell(2);
$pdf->Cell(90, 5, 'Tareas escritas', 0, 1);
$pdf->Ln(5);

// Procedimental section
$y2 = $pdf->GetY();
$pdf->SetX(10);
$pdf->SetFont('Arial', '', 9.5);
$pdf->Cell(95, 5, 'Procedimental (Procesos, habilidades, estrategias, destrezas):', 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(95, 5, $plan->procedimental ?? '', 0, 'L');

// Procedimental avalúo
$pdf->SetXY($x, $y2);
$checked = ($plan->avaluo8 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
$pdf->Cell(2);
$pdf->Cell(90, 5, 'Prueba Diagnóstica', 0, 1);
$pdf->SetX($x);
$checked = ($plan->avaluo9 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
$pdf->Cell(2);
$pdf->Cell(90, 5, 'Prueba Corta', 0, 1);
$pdf->SetX($x);
$checked = ($plan->avaluo10 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
$pdf->Cell(2);
$pdf->Cell(90, 5, 'Examen', 0, 1);
$pdf->SetX($x);
$checked = ($plan->avaluo11 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
$pdf->Cell(2);
$pdf->Cell(90, 5, 'Propuesta de la Investigación', 0, 1);
$pdf->SetX($x);
$checked = ($plan->avaluo12 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
$pdf->Cell(2);
$pdf->Cell(90, 5, 'Proyecto', 0, 1);
$pdf->SetX($x);
$checked = ($plan->avaluo13 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
$pdf->Cell(2);
$pdf->Cell(90, 5, 'Informe oral o escrito', 0, 1);
$pdf->Ln(5);

// Actitudinal section
$y3 = $pdf->GetY();
$pdf->SetX(10);
$pdf->Cell(95, 5, 'Actitudinal (Actitudes, Valores, Normas):', 0, 1);
$pdf->MultiCell(95, 5, $plan->actitudinal ?? '', 0, 'L');

// Actitudinal avalúo
$pdf->SetXY($x, $y3);
$checked = ($plan->avaluo14 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
$pdf->Cell(2);
$pdf->Cell(90, 5, 'Dibujo', 0, 1);
$pdf->SetX($x);
$checked = ($plan->avaluo15 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
$pdf->Cell(2);
$pdf->Cell(90, 5, 'Pintura', 0, 1);
$pdf->SetX($x);
$checked = ($plan->avaluo16 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
$pdf->Cell(2);
$pdf->Cell(90, 5, 'Escultura', 0, 1);
$pdf->SetX($x);
$checked = ($plan->avaluo17 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
$pdf->Cell(2);
$pdf->Cell(90, 5, 'Grabado', 0, 1);
$pdf->SetX($x);
$checked = ($plan->avaluo18 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
$pdf->Cell(2);
$pdf->Cell(90, 5, 'Fotografía', 0, 1);
$pdf->SetX($x);
$checked = ($plan->avaluo19 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
$pdf->Cell(2);
$pdf->Cell(90, 5, 'Tirilla cómica', 0, 1);
$pdf->SetX($x);
$checked = ($plan->avaluo20 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.2, 2, 2, $checked);
$pdf->Cell(2);
$pdf->Cell(10, 5, 'Otros:');
$pdf->Cell(75, 5, $plan->avaluo201 ?? '', 'B', 1);

// Add second page for Metodologías and Actividades
$pdf->AddPage();

// Metodologías Section - Four columns
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, 'Metodologías:', 0, 1);
$pdf->Ln(1);

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(47.5, 5, 'Comprensión Lectora', 1, 0, 'C');
$pdf->Cell(47.5, 5, 'Aprendizaje Cooperativo', 1, 0, 'C');
$pdf->Cell(47.5, 5, 'Aprendizaje Basado en Problema', 1, 0, 'C');
$pdf->Cell(47.5, 5, 'Integración Curricular', 1, 1, 'C');

$pdf->SetFont('Arial', '', 8);

// Comprensión items
$comprensionItems = [
    'Lectura en voz alta',
    'Lectura Dirigida',
    'Lectura compartida',
    'Escritura Interactiva'
];

// Calculate cell height based on content
$cellHeight = 4;
$numRows = 4; // All sections have up to 4 rows

// Store starting Y position
$startY = $pdf->GetY();

// Column 1: Comprensión Lectora
$pdf->SetXY(10, $startY);
foreach ($comprensionItems as $i => $item) {
    $fieldNum = $i + 1;
    $checked = ($plan->{"comprension$fieldNum"} == 'Si') ? 'DF' : 'D';
    $pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 0.8, 2, 2, $checked);
    $pdf->Cell(2.5);
    $pdf->Cell(44, $cellHeight, $item, 0, 1);
    $pdf->SetX(10);
}

// Column 2: Aprendizaje Cooperativo
$pdf->SetXY(57.5, $startY);
$checked = ($plan->aprendizaje == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 0.8, 2, 2, $checked);
$pdf->Cell(2.5);
$pdf->Cell(44, $cellHeight, 'Tutoría entre pares', 0, 0);

// Column 3: Aprendizaje Basado en Problema
$pdf->SetXY(105, $startY);
$checked = ($plan->aprendizaje_problema == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 0.8, 2, 2, $checked);
$pdf->Cell(2.5);
$pdf->MultiCell(44, $cellHeight, 'Enseñanza Contextualizada', 0, 'L');

// Column 4: Integración Curricular
$integrationItems = [
    ['label' => 'Integración a la tecnología', 'field' => 'integracion1'],
    ['label' => 'Integración Curricular', 'field' => 'integracion2'],
    ['label' => 'Español', 'field' => 'integracion3'],
    ['label' => 'Matemáticas', 'field' => 'integracion4'],
    ['label' => 'Inglés', 'field' => 'integracion5'],
    ['label' => 'Ciencias', 'field' => 'integracion6'],
    ['label' => 'Estudios Sociales', 'field' => 'integracion7']
];

$pdf->SetXY(152.5, $startY);
foreach ($integrationItems as $i => $item) {
    if ($i == 2) {
        // Español and Matemáticas on same row
        $checked = ($plan->{$item['field']} == 'Si') ? 'DF' : 'D';
        $pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 0.8, 2, 2, $checked);
        $pdf->Cell(2.5);
        $pdf->Cell(20, $cellHeight, $item['label'], 0, 0);

        // Matemáticas
        $item = $integrationItems[3];
        $checked = ($plan->{$item['field']} == 'Si') ? 'DF' : 'D';
        $pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 0.8, 2, 2, $checked);
        $pdf->Cell(2.5);
        $pdf->Cell(20, $cellHeight, $item['label'], 0, 1);
        $pdf->SetX(152.5);
    } elseif ($i == 4) {
        // Inglés and Ciencias on same row
        $checked = ($plan->{$item['field']} == 'Si') ? 'DF' : 'D';
        $pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 0.8, 2, 2, $checked);
        $pdf->Cell(2.5);
        $pdf->Cell(20, $cellHeight, $item['label'], 0, 0);

        // Ciencias
        $item = $integrationItems[5];
        $checked = ($plan->{$item['field']} == 'Si') ? 'DF' : 'D';
        $pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 0.8, 2, 2, $checked);
        $pdf->Cell(2.5);
        $pdf->Cell(20, $cellHeight, $item['label'], 0, 1);
        $pdf->SetX(152.5);
    } elseif ($i < 2 || $i == 6) {
        // Normal single item rows
        $checked = ($plan->{$item['field']} == 'Si') ? 'DF' : 'D';
        $pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 0.8, 2, 2, $checked);
        $pdf->Cell(2.5);
        $pdf->Cell(44, $cellHeight, $item['label'], 0, 1);
        $pdf->SetX(152.5);
    }
}

// Otras field
$pdf->SetX(10);
$checked = ($plan->integracion8 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 0.8, 2, 2, $checked);
$pdf->Cell(2.5);
$pdf->Cell(10, 4, 'Otras:', 0, 0);
$pdf->Cell(177.5, 4, $plan->integracion81 ?? '', 'B', 1);

$pdf->Ln(3);

// Actividades Section - Three columns
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, 'Actividades:', 0, 1);
$pdf->Ln(1);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(63, 5, 'Inicio', 1, 0, 'C');
$pdf->Cell(63, 5, 'Desarrollo', 1, 0, 'C');
$pdf->Cell(64, 5, 'Cierre', 1, 1, 'C');

$pdf->SetFont('Arial', '', 8);

// Inicio items
$inicioItems = [
    'Actividad de Rutina (Bienvenida, Saludo, Canción)',
    'Presentación del tema',
    'Discusión de Asignación',
    'Canción',
    'Juego',
    'Problema del Día',
    'Adivinanza',
    'Discusión de Noticia, Asignación o tema de actualidad',
    'Repaso Conceptos Discutidos',
    'Observación y Estudio',
    'Reflexión'
];

// Desarrollo items
$desarrolloItems = [
    'Introducción de Vocabulario',
    'Estudio Supervisado o dirigido',
    'Práctica',
    'Lectura y análisis',
    'Informe Oral',
    'Formular y/o contestar preguntas',
    'Resolver ejercicios de práctica',
    'Prueba Corta',
    'Competencia',
    'Debate',
    'Examen'
];

// Cierre items
$cierreItems = [
    'Resumir Material Estudio',
    'Discusión del trabajo asignado',
    'Trabajo en grupo',
    'Corrección y evaluación del trabajo',
    'Aclarar dudas de la destreza',
    'Copiar material en la libreta',
    'Ejercicios o actividades para comprobar aprendizaje'
];

// Determine max rows
$maxActividadRows = max(count($inicioItems) + 1, count($desarrolloItems) + 1, count($cierreItems) + 1); // +1 for "Otro"

$startY = $pdf->GetY();
$currentY = 0;

for ($i = 0; $i < $maxActividadRows; $i++) {
    $rowHeight = 4; // Default row height

    // Check if any item in this row needs more height
    if ($i < count($inicioItems) && $pdf->GetStringWidth($inicioItems[$i]) > 55) {
        $rowHeight = 8;
    }
    if ($i < count($desarrolloItems) && $pdf->GetStringWidth($desarrolloItems[$i]) > 55) {
        $rowHeight = 8;
    }
    if ($i < count($cierreItems) && $pdf->GetStringWidth($cierreItems[$i]) > 55) {
        $rowHeight = 8;
    }

    // Column 1: Inicio
    $pdf->SetXY(10, $startY + $currentY);
    if ($i < count($inicioItems)) {
        $fieldNum = $i + 1;
        $checked = ($plan->{"inicio$fieldNum"} == 'Si') ? 'DF' : 'D';
        $pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 0.8, 2, 2, $checked);
        $pdf->Cell(2.5);
        if ($pdf->GetStringWidth($inicioItems[$i]) > 55) {
            $pdf->MultiCell(60, 4, $inicioItems[$i], 0, 'L');
        } else {
            $pdf->Cell(60, 4, $inicioItems[$i], 0, 0);
        }
    } elseif ($i == count($inicioItems)) {
        $checked = ($plan->inicio12 == 'Si') ? 'DF' : 'D';
        $pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 0.8, 2, 2, $checked);
        $pdf->Cell(2.5);
        $pdf->Cell(10, 4, 'Otro:', 0, 0);
        $pdf->Cell(50, 4, $plan->inicio121 ?? '', 'B', 0);
    }

    // Column 2: Desarrollo
    $pdf->SetXY(73, $startY + $currentY);
    if ($i < count($desarrolloItems)) {
        $fieldNum = $i + 1;
        $checked = ($plan->{"desarrollo$fieldNum"} == 'Si') ? 'DF' : 'D';
        $pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 0.8, 2, 2, $checked);
        $pdf->Cell(2.5);
        if ($pdf->GetStringWidth($desarrolloItems[$i]) > 55) {
            $pdf->MultiCell(60, 4, $desarrolloItems[$i], 0, 'L');
        } else {
            $pdf->Cell(60, 4, $desarrolloItems[$i], 0, 0);
        }
    } elseif ($i == count($desarrolloItems)) {
        $checked = ($plan->desarrollo12 == 'Si') ? 'DF' : 'D';
        $pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 0.8, 2, 2, $checked);
        $pdf->Cell(2.5);
        $pdf->Cell(10, 4, 'Otro:', 0, 0);
        $pdf->Cell(50, 4, $plan->desarrollo121 ?? '', 'B', 0);
    }

    // Column 3: Cierre
    $pdf->SetXY(136, $startY + $currentY);
    if ($i < count($cierreItems)) {
        $fieldNum = $i + 1;
        $checked = ($plan->{"cierre$fieldNum"} == 'Si') ? 'DF' : 'D';
        $pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 0.8, 2, 2, $checked);
        $pdf->Cell(2.5);
        if ($pdf->GetStringWidth($cierreItems[$i]) > 55) {
            $pdf->MultiCell(61, 4, $cierreItems[$i], 0, 'L');
        } else {
            $pdf->Cell(61, 4, $cierreItems[$i], 0, 0);
        }
    } elseif ($i == count($cierreItems)) {
        $checked = ($plan->cierre8 == 'Si') ? 'DF' : 'D';
        $pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 0.8, 2, 2, $checked);
        $pdf->Cell(2.5);
        $pdf->Cell(10, 4, 'Otro:', 0, 0);
        $pdf->Cell(51, 4, $plan->cierre81 ?? '', 'B', 0);
    }

    $currentY += $rowHeight;
}

// Move Y position after activities
$pdf->SetY($startY + $currentY + 3);

// Acomodo Razonable Section
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, 'Acomodo Razonable:', 0, 1);
$pdf->SetFont('Arial', '', 8);

$acomodoItems = [
    'Tiempo y Medio',
    'Ubicación Pupitre',
    'Adaptar a la institución',
    'Servicio Suplementario de Apoyo',
    'Fragmentar trabajos y/o exámenes'
];

for ($i = 1; $i <= count($acomodoItems); $i++) {
    $checked = ($plan->{"acomodo$i"} == 'Si') ? 'DF' : 'D';
    $pdf->Rect($pdf->GetX(), $pdf->GetY() + 0.8, 2, 2, $checked);
    $pdf->Cell(2.5);
    $pdf->Cell(90, 4, $acomodoItems[$i - 1], 0, 1);
}

$checked = ($plan->acomodo6 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 0.8, 2, 2, $checked);
$pdf->Cell(2.5);
$pdf->Cell(10, 4, 'Otro:', 0, 0);
$pdf->Cell(177.5, 4, $plan->acomodo61 ?? '', 'B', 1);

$pdf->Ln(2);

// Materiales Section
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, 'Materiales:', 0, 1);
$pdf->SetFont('Arial', '', 8);

$materialesItems = [
    'Libreta',
    'Crayolas',
    'Tijera',
    'Pega',
    'Delantal',
    'Pinceles',
    'Tempera',
    'Cartulinas de colores',
    'Lápices #2B o 2B-6B',
    'Saca puntas',
    'Borra',
    'Libreta de dibujo multiuso',
    'Libreta de acuarela',
    'Carboncillo comprimido',
    'Lápices de colores',
    'Pasteles a color con aceite y sin aceite'
];

// Display in two columns
$halfCount = ceil(count($materialesItems) / 2);
$startY = $pdf->GetY();

for ($i = 0; $i < $halfCount; $i++) {
    // Left column
    $pdf->SetXY(10, $startY + ($i * 4));
    $fieldNum = $i + 1;
    $checked = ($plan->{"materiales$fieldNum"} == 'Si') ? 'DF' : 'D';
    $pdf->Rect($pdf->GetX(), $pdf->GetY() + 0.8, 2, 2, $checked);
    $pdf->Cell(2.5);
    $pdf->Cell(92.5, 4, $materialesItems[$i], 0, 0);

    // Right column
    if (($i + $halfCount) < count($materialesItems)) {
        $fieldNum = $i + $halfCount + 1;
        $checked = ($plan->{"materiales$fieldNum"} == 'Si') ? 'DF' : 'D';
        $pdf->Rect($pdf->GetX(), $pdf->GetY() + 0.8, 2, 2, $checked);
        $pdf->Cell(2.5);
        $pdf->Cell(92.5, 4, $materialesItems[$i + $halfCount], 0, 1);
    } else {
        $pdf->Ln(4);
    }
}

// Otros field
$checked = ($plan->materiales17 == 'Si') ? 'DF' : 'D';
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 0.8, 2, 2, $checked);
$pdf->Cell(2.5);
$pdf->Cell(10, 4, 'Otro:', 0, 0);
$pdf->Cell(177.5, 4, $plan->materiales171 ?? '', 'B', 1);

$pdf->Output();
