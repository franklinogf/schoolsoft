<?php
require_once '../../../app.php';

use App\Models\ClassPlan;

use Classes\PDF;
use Classes\Session;

Session::is_logged();

// Get class plan ID from GET or POST
$planID = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$planID) {
    die('Error: ID de plan no proporcionado');
}


// Get class plan data
$plan = ClassPlan::find($planID);
if (!$plan) {
    die('Error: Plan de clase no encontrado');
}

$days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];


$pdf = new PDF();
$pdf->SetAutoPageBreak(true, 5);
$pdf->AddPage('L');
$pdf->useFooter(false);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, 'PLANIFICACIÓN POR UNIDAD', 0, 1, "C");
$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 280, $pdf->GetY());
$pdf->Ln();
$pdf->Cell($pdf->GetStringWidth('Materia:') + 3, 5, "Materia:");
$pdf->Cell(20, 5, $plan->materia, "B");
$pdf->Cell($pdf->GetStringWidth('Grado:') + 3, 5, "Grado:");
$pdf->Cell(10, 5, '', "B", 0, 'C');
$pdf->Cell($pdf->GetStringWidth('Maestro(a):') + 3, 5, "Maestro(a):");
$pdf->Cell(90, 5, $plan->profesor, 'B', 1);
$pdf->Ln(2);
$pdf->Cell($pdf->GetStringWidth('Tema de la unidad:') + 3, 5, "Tema de la unidad:");
$pdf->Cell(0, 5, $plan->tema, 'B', 1);
$pdf->Ln(2);
$pdf->Cell($pdf->GetStringWidth('Fecha:') + 3, 5, "Fecha:");
$pdf->Cell(25, 5, $plan->fecha, "B", 0, 'C');
$pdf->Cell($pdf->GetStringWidth('Duración:') + 3, 5, "Duración:");
$pdf->Cell(10, 5, $plan->duracion, "B", 0, 'C');
$pdf->Cell(10, 5, 'semanas', 0, 1);
$pdf->Ln(2);
$pdf->Cell($pdf->GetStringWidth('Estrategia') + 3, 5, "Estrategia");
$pdf->Cell(0, 5, $plan->estrategia, 'B', 1);
$pdf->Ln();
$pdf->SetFont('Arial', '', 10);
$col1 = 30;
$col2 = 120;
$col3 = 130;
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col1, 5);
$pdf->Cell($col1, 5, 'Etapa', 0, 0, 'C');
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col2, 5);
$pdf->Cell($col2, 5, 'Actividades para el logro de las tareas de desempeño (Visión Macro)', 0, 0, 'C');
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col3, 5);
$pdf->Cell($col3, 5, 'Tareas de Desempeño u otra evidencia', 0, 1, 'C');
$y = $pdf->GetY();
$x = $col1 + $col2 + 10;
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col1, 30);
$pdf->Cell($col1, 30, 'Antes', 0, 0, 'C');
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col2, 30);
$pdf->MultiCell($col2, 5, $plan->antes, 0, 'L');
$pdf->Ln(25);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col1, 30);
$pdf->Cell($col1, 30, 'Durante', 0, 0, 'C');
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col2, 30);
$pdf->MultiCell($col2, 5, $plan->durante, 0, 'L');
$pdf->Ln(25);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col1, 30);
$pdf->Cell($col1, 30, 'Después', 0, 0, 'C');
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col2, 30);
$pdf->MultiCell($col2, 5, $plan->despues, 0, 'L');
$pdf->Ln(25);
$Y = $pdf->GetY();
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col3, 90);
$pdf->Cell(5);
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.3, 2, 2, ($plan->tarea1 == 'si') ? 'DF' : '');
$pdf->Cell(2);
$pdf->Cell(0, 5, 'Prueba', 0, 1);
$pdf->SetX($x);
$pdf->Cell(5);
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.3, 2, 2, ($plan->tarea2 == 'si') ? 'DF' : '');
$pdf->Cell(2);
$pdf->Cell(0, 5, 'Quizz', 0, 1);
$pdf->SetX($x);
$pdf->Cell(5);
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.3, 2, 2, ($plan->tarea3 == 'si') ? 'DF' : '');
$pdf->Cell(2);
$pdf->Cell(0, 5, 'Proyecto', 0, 1);
$pdf->SetX($x);
$pdf->Cell(5);
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.3, 2, 2, ($plan->tarea4 == 'si') ? 'DF' : '');
$pdf->Cell(2);
$pdf->Cell(0, 5, 'Mapa de conceptos', 0, 1);
$pdf->SetX($x);
$pdf->Cell(5);
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.3, 2, 2, ($plan->tarea5 == 'si') ? 'DF' : '');
$pdf->Cell(2);
$pdf->Cell($pdf->GetStringWidth('Organizador gráfico') + 2, 5, 'Organizador gráfico');
$pdf->Cell(0, 5, $plan->t5, 'B', 1);
$pdf->SetX($x);
$pdf->Cell(5);
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.3, 2, 2, ($plan->tarea6 == 'si') ? 'DF' : '');
$pdf->Cell(2);
$pdf->Cell(0, 5, 'Ejercicios de práctica', 0, 1);
$pdf->SetX($x);
$pdf->Cell(5);
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.3, 2, 2, ($plan->tarea7 == 'si') ? 'DF' : '');
$pdf->Cell(2);
$pdf->Cell(0, 5, 'Tirilla cómica', 0, 1);
$pdf->SetX($x);
$pdf->Cell(5);
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.3, 2, 2, ($plan->tarea8 == 'si') ? 'DF' : '');
$pdf->Cell(2);
$pdf->Cell(0, 5, 'Pregunta abierta', 0, 1);
$pdf->SetX($x);
$pdf->Cell(5);
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.3, 2, 2, ($plan->tarea9 == 'si') ? 'DF' : '');
$pdf->Cell(2);
$pdf->Cell(0, 5, 'Laboratorio', 0, 1);
$pdf->SetX($x);
$pdf->Cell(5);
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.3, 2, 2, ($plan->tarea10 == 'si') ? 'DF' : '');
$pdf->Cell(2);
$pdf->Cell(0, 5, 'Construcción de modelos', 0, 1);
$pdf->SetX($x);
$pdf->Cell(5);
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.3, 2, 2, ($plan->tarea11 == 'si') ? 'DF' : '');
$pdf->Cell(2);
$pdf->Cell(0, 5, 'Debate', 0, 1);
$pdf->SetX($x);
$pdf->Cell(5);
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.3, 2, 2, ($plan->tarea12 == 'si') ? 'DF' : '');
$pdf->Cell(2);
$pdf->Cell(0, 5, 'Dibujo', 0, 1);
$pdf->SetX($x);
$pdf->Cell(5);
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.3, 2, 2, ($plan->tarea13 == 'si') ? 'DF' : '');
$pdf->Cell(2);
$pdf->Cell(0, 5, 'Trabajo Creativo ', 0, 1);
$pdf->SetX($x);
$pdf->Cell(5);
$pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.3, 2, 2, ($plan->tarea14 == 'si') ? 'DF' : '');
$pdf->Cell(2);
$pdf->Cell($pdf->GetStringWidth('Otros') + 2, 5, 'Otros');
$pdf->Cell(0, 5, $plan->t14, 'B', 1);

# 2DA PAGINA - Guía Semanal del Maestro
$pdf->AddPage('L');
$pdf->Rect($pdf->GetX(), $pdf->GetY(), 280, 5);
$pdf->Cell(0, 5, 'Guía Semanal del Maestro', 0, 1, 'C');
$x = $pdf->GetX();
$pdf->Rect($pdf->GetX(), $pdf->GetY(), 280, 5);
$pdf->Cell(0, 5, "Fecha: $plan->fechaG");
$pdf->SetX($x);
$pdf->Cell(0, 5, "Duración: $plan->duracionG semanas", 0, 0, 'C');
$pdf->SetX($x);
$pdf->Cell(0, 5, "Valor de la semana: $plan->valorG", 0, 1, 'R');
$col1 = 30;
$cols = 50;
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col1, 5);
$pdf->Cell($col1, 5, "Días de la semana", 0, 0, 'C');
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 5);
$pdf->Cell($cols, 5, "Lunes", 0, 0, 'C');
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 5);
$pdf->Cell($cols, 5, "Martes", 0, 0, 'C');
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 5);
$pdf->Cell($cols, 5, "Miércoles", 0, 0, 'C');
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 5);
$pdf->Cell($cols, 5, "Jueves", 0, 0, 'C');
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 5);
$pdf->Cell($cols, 5, "Viernes", 0, 1, 'C');

# Estándares
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col1, 20);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell($col1, 8, "Estándares (PRCS)", 0, 'C');
$x += $col1;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->estandares1, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->estandares2, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->estandares3, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->estandares4, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->estandares5, 0, 'L');
$pdf->SetY($y + 20);

# Expectativa
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col1, 20);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Cell($col1, 20, "Expectativa", 0, 0, 'C');
$x += $col1;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->expectativa1, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->expectativa2, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->expectativa3, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->expectativa4, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->expectativa5, 0, 'L');
$pdf->SetY($y + 20);

# Objetivos
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col1, 20);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Cell($col1, 20, "Objetivos", 0, 0, 'C');
$x += $col1;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->objetivos1, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->objetivos2, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->objetivos3, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->objetivos4, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->objetivos5, 0, 'L');
$pdf->SetY($y + 20);

# Nivel de pensamiento
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col1, 25);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Cell($col1, 25, "Semana", 0, 0, 'C');
$x += $col1;

// Lunes
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 25);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 5);
$pdf->Cell($cols, 5, 'Actividades de Aprendizaje', 0, 1, 'C');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->pensamiento1_1 == 'si') ? 'DF' : '');
$pdf->Cell($cols, 5, '1 Memorístico', 0, 1, 'C');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->pensamiento1_2 == 'si') ? 'DF' : '');
$pdf->Cell($cols, 5, '2 Procesamiento', 0, 1, 'C');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->pensamiento1_3 == 'si') ? 'DF' : '');
$pdf->Cell($cols, 5, '3 Estratégico', 0, 1, 'C');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->pensamiento1_4 == 'si') ? 'DF' : '');
$pdf->Cell($cols, 5, '4 Extendido', 0, 1, 'C');
$x += $cols;

// Martes
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 25);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 5);
$pdf->Cell($cols, 5, 'Actividades de Aprendizaje', 0, 1, 'C');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->pensamiento2_1 == 'si') ? 'DF' : '');
$pdf->Cell($cols, 5, '1 Memorístico', 0, 1, 'C');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->pensamiento2_2 == 'si') ? 'DF' : '');
$pdf->Cell($cols, 5, '2 Procesamiento', 0, 1, 'C');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->pensamiento2_3 == 'si') ? 'DF' : '');
$pdf->Cell($cols, 5, '3 Estratégico', 0, 1, 'C');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->pensamiento2_4 == 'si') ? 'DF' : '');
$pdf->Cell($cols, 5, '4 Extendido', 0, 1, 'C');
$x += $cols;

// Miércoles
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 25);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 5);
$pdf->Cell($cols, 5, 'Actividades de Aprendizaje', 0, 1, 'C');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->pensamiento3_1 == 'si') ? 'DF' : '');
$pdf->Cell($cols, 5, '1 Memorístico', 0, 1, 'C');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->pensamiento3_2 == 'si') ? 'DF' : '');
$pdf->Cell($cols, 5, '2 Procesamiento', 0, 1, 'C');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->pensamiento3_3 == 'si') ? 'DF' : '');
$pdf->Cell($cols, 5, '3 Estratégico', 0, 1, 'C');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->pensamiento3_4 == 'si') ? 'DF' : '');
$pdf->Cell($cols, 5, '4 Extendido', 0, 1, 'C');
$x += $cols;

// Jueves
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 25);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 5);
$pdf->Cell($cols, 5, 'Actividades de Aprendizaje', 0, 1, 'C');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->pensamiento4_1 == 'si') ? 'DF' : '');
$pdf->Cell($cols, 5, '1 Memorístico', 0, 1, 'C');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->pensamiento4_2 == 'si') ? 'DF' : '');
$pdf->Cell($cols, 5, '2 Procesamiento', 0, 1, 'C');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->pensamiento4_3 == 'si') ? 'DF' : '');
$pdf->Cell($cols, 5, '3 Estratégico', 0, 1, 'C');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->pensamiento4_4 == 'si') ? 'DF' : '');
$pdf->Cell($cols, 5, '4 Extendido', 0, 1, 'C');
$x += $cols;

// Viernes
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 25);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 5);
$pdf->Cell($cols, 5, 'Actividades de Aprendizaje', 0, 1, 'C');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->pensamiento5_1 == 'si') ? 'DF' : '');
$pdf->Cell($cols, 5, '1 Memorístico', 0, 1, 'C');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->pensamiento5_2 == 'si') ? 'DF' : '');
$pdf->Cell($cols, 5, '2 Procesamiento', 0, 1, 'C');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->pensamiento5_3 == 'si') ? 'DF' : '');
$pdf->Cell($cols, 5, '3 Estratégico', 0, 1, 'C');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->pensamiento5_4 == 'si') ? 'DF' : '');
$pdf->Cell($cols, 5, '4 Extendido', 0, 1, 'C');

# Antes
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col1, 20);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Cell($col1, 20, "Antes", 0, 0, 'C');
$x += $col1;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->antes1, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->antes2, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->antes3, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->antes4, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->antes5, 0, 'L');
$pdf->SetY($y + 20);

# Durante
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col1, 20);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Cell($col1, 20, "Durante", 0, 0, 'C');
$x += $col1;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->durante1, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->durante2, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->durante3, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->durante4, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->durante5, 0, 'L');
$pdf->SetY($y + 20);

# Después
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col1, 20);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Cell($col1, 20, "Después", 0, 0, 'C');
$x += $col1;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->despues1, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->despues2, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->despues3, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->despues4, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->despues5, 0, 'L');
$pdf->SetY($y + 20);

# 3RA PAGINA
$pdf->AddPage('L');
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col1, 5);
$pdf->Cell($col1);

foreach ($days as $day) {
    $pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 5);
    $pdf->Cell($cols, 5, $day, 0, 0, 'C');
}

$pdf->Ln();

# Estrategia académica
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col1, 25);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell($col1, 9, "Estrategia académica", 0, 'C');
$x += $col1;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 25);
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->estrategia_a1_1 == 'si') ? 'DF' : '');
$pdf->Cell(4);
$pdf->MultiCell($cols - 4, 5, 'Aprendizaje basado en problemas', 0, 'L');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->estrategia_a1_2 == 'si') ? 'DF' : '');
$pdf->Cell(4);
$pdf->Cell($cols - 4, 5, 'Trabajo cooperativo', 0, 1, 'L');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->estrategia_a1_3 == 'si') ? 'DF' : '');
$pdf->Cell(4);
$pdf->Cell($cols - 4, 5, 'Ciclos de aprendizaje', 0, 1, 'L');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->estrategia_a1_4 == 'si') ? 'DF' : '');
$pdf->Cell(4);
$pdf->Cell($pdf->GetStringWidth('ECA:'), 5, 'ECA:', 0, 0, 'L');
$pdf->Cell($cols - $pdf->GetStringWidth('ECA:') - 4, 4, $plan->estrategia_a1_41, 'B', 1, 'C');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 25);
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->estrategia_a2_1 == 'si') ? 'DF' : '');
$pdf->Cell(4);
$pdf->MultiCell($cols - 4, 5, 'Aprendizaje basado en problemas', 0, 'L');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->estrategia_a2_2 == 'si') ? 'DF' : '');
$pdf->Cell(4);
$pdf->Cell($cols - 4, 5, 'Trabajo cooperativo', 0, 1, 'L');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->estrategia_a2_3 == 'si') ? 'DF' : '');
$pdf->Cell(4);
$pdf->Cell($cols - 4, 5, 'Ciclos de aprendizaje', 0, 1, 'L');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->estrategia_a2_4 == 'si') ? 'DF' : '');
$pdf->Cell(4);
$pdf->Cell($pdf->GetStringWidth('ECA:'), 5, 'ECA:', 0, 0, 'L');
$pdf->Cell($cols - $pdf->GetStringWidth('ECA:') - 4, 4, $plan->estrategia_a2_41, 'B', 1, 'C');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 25);
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->estrategia_a3_1 == 'si') ? 'DF' : '');
$pdf->Cell(4);
$pdf->MultiCell($cols - 4, 5, 'Aprendizaje basado en problemas', 0, 'L');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->estrategia_a3_2 == 'si') ? 'DF' : '');
$pdf->Cell(4);
$pdf->Cell($cols - 4, 5, 'Trabajo cooperativo', 0, 1, 'L');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->estrategia_a3_3 == 'si') ? 'DF' : '');
$pdf->Cell(4);
$pdf->Cell($cols - 4, 5, 'Ciclos de aprendizaje', 0, 1, 'L');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->estrategia_a3_4 == 'si') ? 'DF' : '');
$pdf->Cell(4);
$pdf->Cell($pdf->GetStringWidth('ECA:'), 5, 'ECA:', 0, 0, 'L');
$pdf->Cell($cols - $pdf->GetStringWidth('ECA:') - 4, 4, $plan->estrategia_a3_41, 'B', 1, 'C');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 25);
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->estrategia_a4_1 == 'si') ? 'DF' : '');
$pdf->Cell(4);
$pdf->MultiCell($cols - 4, 5, 'Aprendizaje basado en problemas', 0, 'L');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->estrategia_a4_2 == 'si') ? 'DF' : '');
$pdf->Cell(4);
$pdf->Cell($cols - 4, 5, 'Trabajo cooperativo', 0, 1, 'L');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->estrategia_a4_3 == 'si') ? 'DF' : '');
$pdf->Cell(4);
$pdf->Cell($cols - 4, 5, 'Ciclos de aprendizaje', 0, 1, 'L');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->estrategia_a4_4 == 'si') ? 'DF' : '');
$pdf->Cell(4);
$pdf->Cell($pdf->GetStringWidth('ECA:'), 5, 'ECA:', 0, 0, 'L');
$pdf->Cell($cols - $pdf->GetStringWidth('ECA:') - 4, 4, $plan->estrategia_a4_41, 'B', 1, 'C');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 25);
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->estrategia_a5_1 == 'si') ? 'DF' : '');
$pdf->Cell(4);
$pdf->MultiCell($cols - 4, 5, 'Aprendizaje basado en problemas', 0, 'L');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->estrategia_a5_2 == 'si') ? 'DF' : '');
$pdf->Cell(4);
$pdf->Cell($cols - 4, 5, 'Trabajo cooperativo', 0, 1, 'L');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->estrategia_a5_3 == 'si') ? 'DF' : '');
$pdf->Cell(4);
$pdf->Cell($cols - 4, 5, 'Ciclos de aprendizaje', 0, 1, 'L');
$pdf->SetX($x);
$pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 1.2, 2, 2, ($plan->estrategia_a5_4 == 'si') ? 'DF' : '');
$pdf->Cell(4);
$pdf->Cell($pdf->GetStringWidth('ECA:'), 5, 'ECA:', 0, 0, 'L');
$pdf->Cell($cols - $pdf->GetStringWidth('ECA:') - 4, 4, $plan->estrategia_a5_41, 'B', 1);
$pdf->Ln(1);

# Valores
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col1, 20);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Cell($col1, 20, "Valores", 0, 0, 'C');
$x += $col1;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->valores1, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->valores2, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->valores3, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->valores4, 0, 'L');
$x += $cols;
$pdf->SetXY($x, $y);
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 20);
$pdf->MultiCell($cols, 5, $plan->valores5, 0, 'L');
$pdf->SetY($y + 20);

# Estrategia de educación diferenciada
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col1, 70);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell($col1, 6, "Estrategia de educación diferenciada", 0, 'C');
$x += $col1;

$educationStrategies = [
    'Tiempo adicional',
    'Ubicación de pupitre',
    'Fragmentar trabajos',
    'Trabajo ind / grupal',
    'Material complementario',
    'Traducción de material',
    'Investigación grupal',
    'Tareas de inteligencias múltiples',
    'Diccionario',
    'Diversos modos de expresión',
    'Intrucciones claras y precisas',
    'Proveer ejemplos'
];
foreach ($days as $index => $dia) {

    $pdf->SetXY($x, $y);
    $pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 70);
    $dayIndex = $index + 1;
    foreach ($educationStrategies as $i => $estrategia) {
        $colIndex = $i + 1;
        $pdf->SetX($x);
        $field = "estrategia_e{$dayIndex}_{$colIndex}";
        $pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.2, 2, 2, ($plan->$field == 'si') ? 'DF' : '');
        $pdf->Cell(2.5);
        if ($pdf->GetStringWidth($estrategia) > 50) { // Tareas de inteligencias múltiples
            $pdf->MultiCell($cols - 4, 5, $estrategia, 0, 'L');
        } else {
            $pdf->Cell($cols - 4, 5, $estrategia, 0, 1, 'L');
        }
    }
    $pdf->SetX($x);
    $pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.2, 2, 2, ($plan->{"estrategia_e{$dayIndex}_13"} == 'si') ? 'DF' : '');
    $pdf->Cell(2.5);
    $pdf->Cell($pdf->GetStringWidth('Otros:'), 5, 'Otros:', 0, 0, 'L');
    $pdf->Cell($cols - $pdf->GetStringWidth('Otros:') - 4, 4, $plan->{"estrategia_e{$dayIndex}_13"}, 'B', 1, 'L');
    $x += $cols;
}

# 4TA PAGINA
$pdf->AddPage('L');

$concepts = [
    'Formula preguntas y define problemas.',
    'Desarrolla y usa modelos.',
    'Planifica y lleva a cabo experimentos e investigaciones.',
    'Analiza e interpreta datos.',
    'Usa pensamiento matemático y computacional.',
    'Propone explicaciones y diseña soluciones.',
    'Expone argumentos a partir de evidencia confiable.',
    'Obtiene, evalúa y comunica información.'
];

$crossThemes = [
    'Patrones.',
    'Causa y efecto',
    'Escala, proporción y cantidad',
    'Sistemas y modelos de sistemas',
    'Energía y materia',
    'Estructura y función',
    'Estabilidad y cambio',
    'Ética y valores en las ciencias'
];

# Conceptos y destrezas
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col1, 80);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell($col1, 6, "Conceptos y destrezas", 0, 'C');
$x += $col1;

foreach ($days as $index => $dia) {
    $pdf->SetXY($x, $y);
    $pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 80);
    $dayIndex = $index + 1;

    foreach ($concepts as $i => $concepto) {
        $colIndex = $i + 1;
        $pdf->SetX($x);
        $field = "conceptos{$dayIndex}_{$colIndex}";
        $pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.2, 2, 2, ($plan->$field == 'si') ? 'DF' : '');
        $pdf->Cell(2.5);
        if ($pdf->GetStringWidth($concepto) > 50) {
            $pdf->MultiCell($cols - 4, 5, $concepto, 0, 'L');
        } else {
            $pdf->Cell($cols - 4, 5, $concepto, 0, 1, 'L');
        }
    }
    $x += $cols;
}

# Temas transversales
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col1, 55);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell($col1, 6, "Temas transversales", 0, 'C');
$x += $col1;

foreach ($days as $index => $dia) {
    $pdf->SetXY($x, $y);
    $pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 55);
    $dayIndex = $index + 1;

    foreach ($crossThemes as $i => $tema) {
        $colIndex = $i + 1;
        $pdf->SetX($x);
        $field = "temas{$dayIndex}_{$colIndex}";
        $pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.2, 2, 2, ($plan->$field == 'si') ? 'DF' : '');
        $pdf->Cell(2.5);
        if ($pdf->GetStringWidth($tema) > 50) {
            $pdf->MultiCell($cols - 4, 5, $tema, 0, 'L');
        } else {
            $pdf->Cell($cols - 4, 5, $tema, 0, 1, 'L');
        }
    }
    $x += $cols;
}

# 5TA PAGINA
$pdf->AddPage('L');

$materials = [
    'Computadora y Proyector.',
    'Material Fotocopiado',
    'Libro:',
    'Equipo de:',
    'Video / Película:',
    'Manipulativos',
    'Otros:'
];

# Materiales o recursos
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col1, 60);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell($col1, 6, "Materiales o recursos", 0, 'C');
$x += $col1;

foreach ($days as $index => $dia) {
    $pdf->SetXY($x, $y);
    $pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 60);
    $dayIndex = $index + 1;

    foreach ($materials as $i => $material) {
        $colIndex = $i + 1;
        $pdf->SetX($x);
        $field = "materiales{$dayIndex}_{$colIndex}";
        $pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.2, 2, 2, ($plan->$field == 'si') ? 'DF' : '');
        $pdf->Cell(2.5);
        $pdf->Cell($cols - 4, 5, $material, 0, 1, 'L');

        // Campos de texto adicionales para Libro, Equipo, Video y Otros
        if (in_array($colIndex, [3, 4, 5, 7])) {
            $pdf->SetX($x);
            $textField = "materiales{$dayIndex}_{$colIndex}1";
            $width = ($colIndex == 5) ? $cols : ($cols - 4);
            $pdf->Cell($width, 5, $plan->$textField ?? '', 'B', 1, 'L');
        }
    }
    $x += $cols;
}

$pdf->Ln(5);

# Asignaciones / Tareas especiales (opcional)
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col1, 35);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell($col1, 6, "Asignaciones / Tareas especiales (opcional)", 0, 'C');
$x += $col1;

foreach ($days as $index => $dia) {
    $pdf->SetXY($x, $y);
    $pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 35);
    $dayIndex = $index + 1;

    $pdf->SetX($x);
    $pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.2, 2, 2, ($plan->{"tareas{$dayIndex}_1"} == 'si') ? 'DF' : '');
    $pdf->Cell(2.5);
    $pdf->Cell($cols - 4, 5, 'Práctica:', 0, 1, 'L');
    $pdf->SetX($x);
    $pdf->Cell($cols - 4, 5, $plan->{"tareas{$dayIndex}_11"} ?? '', 'B', 1, 'L');

    $pdf->SetX($x);
    $pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.2, 2, 2, ($plan->{"tareas{$dayIndex}_2"} == 'si') ? 'DF' : '');
    $pdf->Cell(2.5);
    $pdf->Cell($cols - 4, 5, 'Preparación', 0, 1, 'L');
    $pdf->SetX($x);
    $pdf->Cell($cols - 4, 5, $plan->{"tareas{$dayIndex}_21"} ?? '', 'B', 1, 'L');

    $pdf->SetX($x);
    $pdf->Rect($pdf->GetX() + 1, $pdf->GetY() + 1.2, 2, 2, ($plan->{"tareas{$dayIndex}_3"} == 'si') ? 'DF' : '');
    $pdf->Cell(2.5);
    $pdf->Cell($cols - 4, 5, 'Elaboración:', 0, 1, 'L');
    $pdf->SetX($x);
    $pdf->Cell($cols - 4, 5, $plan->{"tareas{$dayIndex}_31"} ?? '', 'B', 1, 'L');

    $x += $cols;
}

$pdf->Ln(5);

# Reflexión sobre la praxis
$pdf->Rect($pdf->GetX(), $pdf->GetY(), $col1, 35);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell($col1, 6, "Reflexión sobre la praxis", 0, 'C');
$x += $col1;

$reflexionText = 'Se cumplió con el plan Necesidad de re-enseñanza Maestra ausente Reunión profesional Inclemencias del tiempo';

foreach ($days as $index => $dia) {
    $pdf->SetXY($x, $y);
    $pdf->Rect($pdf->GetX(), $pdf->GetY(), $cols, 35);
    $pdf->MultiCell($cols, 5, $reflexionText, 0, 'L');
    $x += $cols;
}

# 6TA PAGINA - Actividades
$pdf->AddPage('L');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, 'Actividades', 0, 1, 'C');
$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 280, $pdf->GetY());
$pdf->Ln(2);

// Actividades "Antes"
$actividadesAntes = [
    ['texto' => 'Actividad de rutina / saludo', 'campo' => 'actividad_antes1'],
    ['texto' => 'Repasar la clase anterior', 'campo' => 'actividad_antes2', 'extra' => 'actividad_antes21'],
    ['texto' => 'Introducción al tema o destreza', 'campo' => 'actividad_antes3'],
    ['texto' => 'Discusión de la asignación', 'campo' => 'actividad_antes4'],
    ['texto' => 'Discusión de:', 'campo' => 'actividad_antes5', 'extra' => 'actividad_antes51'],
    ['texto' => 'Preguntas abiertas', 'campo' => 'actividad_antes6'],
    ['texto' => 'Reflexión', 'campo' => 'actividad_antes7'],
    ['texto' => 'Otro:', 'campo' => 'actividad_antes8', 'extra' => 'actividad_antes81']
];

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, 'Antes', 0, 1);
$pdf->SetFont('Arial', '', 10);
foreach ($actividadesAntes as $actividad) {
    $pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.3, 2, 2, ($plan->{$actividad['campo']} == 'si') ? 'DF' : '');
    $pdf->Cell(4);
    if (isset($actividad['extra'])) {
        $pdf->Cell($pdf->GetStringWidth($actividad['texto']), 5, $actividad['texto']);
        $pdf->Cell(3);
        $extraValue = $plan->{$actividad['extra']} ?? '';
        $pdf->Cell($pdf->GetStringWidth($extraValue) + 5, 5, $extraValue, 'B', 1);
    } else {
        $pdf->Cell(0, 5, $actividad['texto'], 0, 1);
    }
}

$pdf->Ln(2);

// Actividades "Durante"
$actividadesDurante = [
    ['texto' => 'Discusión detallada del tema en Power Point.', 'campo' => 'actividad_durante1'],
    ['texto' => 'Contestación y discusión de ejercicios de práctica.', 'campo' => 'actividad_durante2'],
    ['texto' => 'Lectura y discusión de lectura', 'campo' => 'actividad_durante3'],
    ['texto' => 'Trabajo en grupo', 'campo' => 'actividad_durante4'],
    ['texto' => 'Presentación de un informe oral por el estudiante', 'campo' => 'actividad_durante5'],
    ['texto' => 'Laboratorio', 'campo' => 'actividad_durante6'],
    ['texto' => 'Debate', 'campo' => 'actividad_durante7'],
    ['texto' => 'Prueba o Quizz', 'campo' => 'actividad_durante8'],
    ['texto' => 'Otro:', 'campo' => 'actividad_durante9', 'extra' => 'actividad_durante91']
];

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, 'Durante', 0, 1);
$pdf->SetFont('Arial', '', 10);
foreach ($actividadesDurante as $actividad) {
    $pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.3, 2, 2, ($plan->{$actividad['campo']} == 'si') ? 'DF' : '');
    $pdf->Cell(4);
    if (isset($actividad['extra'])) {
        $pdf->Cell($pdf->GetStringWidth($actividad['texto']), 5, $actividad['texto']);
        $pdf->Cell(3);
        $extraValue = $plan->{$actividad['extra']} ?? '';
        $pdf->Cell($pdf->GetStringWidth($extraValue) + 5, 5, $extraValue, 'B', 1);
    } else {
        $pdf->Cell(0, 5, $actividad['texto'], 0, 1);
    }
}

$pdf->Ln(2);

// Actividades "Después"
$actividadesDespues = [
    ['texto' => 'Resumen de la clase', 'campo' => 'actividad_despues1'],
    ['texto' => 'Discusión de ejercicios', 'campo' => 'actividad_despues2'],
    ['texto' => 'Trabajo en la libreta', 'campo' => 'actividad_despues3'],
    ['texto' => 'Instrucciones para el día siguiente', 'campo' => 'actividad_despues4'],
    ['texto' => 'Técnica de avaluó', 'campo' => 'actividad_despues5'],
    ['texto' => 'Aclarar dudas', 'campo' => 'actividad_despues6'],
    ['texto' => 'Otro:', 'campo' => 'actividad_despues7', 'extra' => 'actividad_despues71']
];

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, 'Después', 0, 1);
$pdf->SetFont('Arial', '', 10);
foreach ($actividadesDespues as $actividad) {
    $pdf->Rect($pdf->GetX(), $pdf->GetY() + 1.3, 2, 2, ($plan->{$actividad['campo']} == 'si') ? 'DF' : '');
    $pdf->Cell(4);
    if (isset($actividad['extra'])) {
        $pdf->Cell($pdf->GetStringWidth($actividad['texto']), 5, $actividad['texto']);
        $pdf->Cell(3);
        $extraValue = $plan->{$actividad['extra']} ?? '';
        $pdf->Cell($pdf->GetStringWidth($extraValue) + 5, 5, $extraValue, 'B', 1);
    } else {
        $pdf->Cell(0, 5, $actividad['texto'], 0, 1);
    }
}

$pdf->Output();
