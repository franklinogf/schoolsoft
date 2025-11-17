<?php
require_once '../../../../app.php';

use App\Models\Admin;
use App\Models\Teacher;
use App\Models\WeeklyPlan2;
use Classes\PDF;
use Classes\Session;

Session::is_logged();

$teacher = Teacher::find(Session::id());
$school = Admin::primaryAdmin();

$planId = $_GET['plan'] ?? null;

if (!$planId) {
    die('Plan ID no proporcionado');
}

$weeklyPlan = WeeklyPlan2::find($planId);

if (!$weeklyPlan || $weeklyPlan->id != $teacher->id) {
    die('Plan no encontrado o no autorizado');
}

$pdf = new PDF();
$pdf->SetTitle(__("Plan Semanal 2") . " - " . $weeklyPlan->tema, true);
$pdf->Fill();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 15);

// Título
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, __("PLAN DE LECCIÓN BISEMANAL"), 0, 1, 'C');
$pdf->Ln(3);

// Información General
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, __("INFORMACIÓN GENERAL"), 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(63.33, 6, __("Nombre") . ": " . $teacher->nombre . ', ' . $teacher->apellidos, 1, 0);
$pdf->Cell(63.33, 6, __("Asignatura") . ": " . $weeklyPlan->asignatura, 1, 0);
$pdf->Cell(63.33, 6, __("Grado") . ": " . $weeklyPlan->grado, 1, 1);

$pdf->Cell(63.33, 6, __("Fecha") . ": " . $weeklyPlan->fecha, 1, 0);
$pdf->Cell(63.33, 6, __("Desde") . ": " . $weeklyPlan->desde, 1, 0);
$pdf->Cell(63.33, 6, __("Hasta") . ": " . $weeklyPlan->hasta, 1, 1);

$pdf->Ln(2);

// Tema
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 6, __("Tema") . ":", 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 6, $weeklyPlan->tema, 'B', 1);

$pdf->Ln(2);

// Estándares
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 6, __("ESTÁNDARES"), 0, 1);
$pdf->SetFont('Arial', '', 8);
if ($weeklyPlan->estandares1) $pdf->Cell(0, 5, "1. " . $weeklyPlan->estandares1, 'B', 1);
if ($weeklyPlan->estandares2) $pdf->Cell(0, 5, "2. " . $weeklyPlan->estandares2, 'B', 1);
if ($weeklyPlan->estandares3) $pdf->Cell(0, 5, "3. " . $weeklyPlan->estandares3, 'B', 1);

$pdf->Ln(2);

// Objetivos
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 6, __("OBJETIVOS"), 0, 1);
$pdf->SetFont('Arial', '', 8);
if ($weeklyPlan->objetivos1) $pdf->Cell(0, 5, "1. " . $weeklyPlan->objetivos1, 'B', 1);
if ($weeklyPlan->objetivos2) $pdf->Cell(0, 5, "2. " . $weeklyPlan->objetivos2, 'B', 1);
if ($weeklyPlan->objetivos3) $pdf->Cell(0, 5, "3. " . $weeklyPlan->objetivos3, 'B', 1);
if ($weeklyPlan->objetivos4) $pdf->Cell(0, 5, "4. " . $weeklyPlan->objetivos4, 'B', 1);

$pdf->Ln(2);

// Destrezas
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 6, __("DESTREZAS"), 0, 1);
$pdf->SetFont('Arial', '', 8);
if ($weeklyPlan->destrezas1) $pdf->Cell(90, 5, "1. " . $weeklyPlan->destrezas1, 'B', 0);
$pdf->Cell(5);
if ($weeklyPlan->destrezas3) $pdf->Cell(90, 5, "3. " . $weeklyPlan->destrezas3, 'B', 1);
else $pdf->Ln();

if ($weeklyPlan->destrezas2) $pdf->Cell(90, 5, "2. " . $weeklyPlan->destrezas2, 'B', 0);
$pdf->Cell(5);
if ($weeklyPlan->destrezas4) $pdf->Cell(90, 5, "4. " . $weeklyPlan->destrezas4, 'B', 1);
else $pdf->Ln();

$pdf->Ln(3);

// Helper function for checkboxes
function drawCheckbox(PDf $pdf, string $value, bool $border = true): void
{
    $fill = ($value == 'Si') ? 'DF' : '';
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->Rect($x + 3, $y + 1.5, 3, 3, $fill);
    $pdf->Cell(10, 6, '', $border ? 1 : 0);
}

// Estándares Comunes
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(45, 6, __("ESTÁNDARES COMUNES"), 'LTR', 0);
drawCheckbox($pdf, $weeklyPlan->estand_comun1);
$pdf->Cell(60, 6, 'Vida laboral y universitaria', 1);
drawCheckbox($pdf, $weeklyPlan->estand_comun2);
$pdf->Cell(60, 6, 'Rigurosidad académica', 1, 1);

$pdf->Cell(45, 6, 'Common Core Standards', 'LBR', 0);
drawCheckbox($pdf, $weeklyPlan->estand_comun3);
$pdf->Cell(60, 6, 'Integración internacional', 1);
drawCheckbox($pdf, $weeklyPlan->estand_comun4);
$pdf->Cell(60, 6, 'Investigación basada en evidencia', 1, 1);

$pdf->Ln(3);

// Apoyo Didáctico, Integración y Estrategias
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(90, 12, __("APOYO DIDÁCTICO"), 1, 0, "C");
$pdf->Cell(50, 12, __("INTEGRACIÓN"), 1, 0, "C");
$pdf->Cell(50, 6, __("ESTRATEGIAS DE"), 'LTR', 1, "C");
$pdf->Cell(140);
$pdf->Cell(50, 6, __("ENSEÑANZA - APRENDIZAJE"), 'LBR', 1, "C");

$pdf->SetFont('Arial', '', 8);

$apoyoData = [
    ['apoyo1', 'Calculadora', 'apoyo2', 'DVD/VCR', 'integracion1', 'Artes', 'estrategias1', 'Grupo Cooperativo'],
    ['apoyo3', 'Dibujo', 'apoyo4', 'Radio CD/Ipod', 'integracion2', 'Música', 'estrategias2', 'Informe Oral'],
    ['apoyo5', 'Diccionario', 'apoyo6', 'Tecnología', 'integracion3', 'Religión', 'estrategias3', 'Informe Escrito'],
    ['apoyo7', 'Filminas', 'apoyo8', 'Computadora', 'integracion4', 'Español', 'estrategias4', 'Demostración'],
    ['apoyo9', 'Grabadoras', 'apoyo10', 'Teatro', 'integracion5', 'Ciencias', 'estrategias5', 'Conferencia'],
    ['apoyo11', 'Láminas', 'apoyo12', 'Biblioteca', 'integracion6', 'Estudio Sociales', 'estrategias6', 'Proyecto de investigación'],
    ['apoyo13', 'Películas', 'apoyo14', 'Música', 'integracion7', 'Inglés', 'estrategias7', 'Mapa de Conceptos'],
    ['apoyo15', 'Biblia', 'apoyo16', 'Hoja Fotocopia', 'integracion8', 'Matemáticas', 'estrategias8', 'Experiencia de Campo'],
    ['apoyo17', 'Pizarra - Electrónica', 'apoyo18', 'Mapas', 'integracion9', 'Educación Física', 'estrategias9', 'Entrevista'],
    ['apoyo19', 'Franjas', 'apoyo20', 'Power Point', 'integracion10', 'Teatro', 'estrategias10', 'Debate'],
    ['apoyo21', 'Juegos', 'apoyo22', 'Excel', 'integracion11', 'Salud', 'estrategias11', 'Repaso'],
    ['apoyo23', 'Texto', 'apoyo24', 'Word', 'integracion12', 'Computadoras', 'estrategias12', 'Canción'],
    ['apoyo25', 'Carteles', 'apoyo26', 'Publisher', '', '', 'estrategias13', 'Laboratorio'],
    ['apoyo27', 'Proyector', '', '', '', '', 'estrategias14', 'Tirillas Cómicas'],
    ['', '', '', '', '', '', 'estrategias15', 'Observaciones'],
];

foreach ($apoyoData as $row) {
    if (!empty($row[0])) {
        drawCheckbox($pdf, $weeklyPlan->{$row[0]});
        $pdf->Cell(35, 6, $row[1], 1);
    } else {
        $pdf->Cell(10, 6, '', 1);
        $pdf->Cell(35, 6, '', 1);
    }

    if (!empty($row[2])) {
        drawCheckbox($pdf, $weeklyPlan->{$row[2]});
        $pdf->Cell(35, 6, $row[3], 1);
    } else {
        $pdf->Cell(10, 6, '', 1);
        $pdf->Cell(35, 6, '', 1);
    }

    if (!empty($row[4])) {
        drawCheckbox($pdf, $weeklyPlan->{$row[4]});
        $pdf->Cell(40, 6, $row[5], 1);
    } else {
        $pdf->Cell(10, 6, '', 1);
        $pdf->Cell(40, 6, '', 1);
    }

    if (!empty($row[6])) {
        drawCheckbox($pdf, $weeklyPlan->{$row[6]});
        $pdf->Cell(40, 6, $row[7], 1, 1);
    } else {
        $pdf->Cell(10, 6, '', 1);
        $pdf->Cell(40, 6, '', 1, 1);
    }
}

$pdf->Ln(3);
$pdf->AddPage();
// Avalúo y Evaluación
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(190, 6, __("AVALÚO Y EVALUACIÓN REALIZADO POR LOS ESTUDIANTES"), 1, 1, 'C');
$pdf->Cell(47.5, 6, 'Portafolio', 1, 0, 'C');
$pdf->Cell(47.5, 6, 'Prueba Corta', 1, 0, 'C');
$pdf->Cell(47.5, 6, 'Proyecto Especial', 1, 0, 'C');
$pdf->Cell(47.5, 6, 'Contestar Preguntas', 1, 1, 'C');

$pdf->SetFont('Arial', '', 8);
drawCheckbox($pdf, $weeklyPlan->portafolio1);
$pdf->Cell(37.5, 6, 'Examen Escrito', 1);
drawCheckbox($pdf, $weeklyPlan->prueba1);
$pdf->Cell(37.5, 6, 'Informe Escrito', 1);
drawCheckbox($pdf, $weeklyPlan->proyecto1);
$pdf->Cell(37.5, 6, 'Mapa Conceptual', 1);
drawCheckbox($pdf, $weeklyPlan->contestar1);
$pdf->Cell(37.5, 6, 'Trabajo Cooperativo', 1, 1);

drawCheckbox($pdf, $weeklyPlan->portafolio2);
$pdf->Cell(37.5, 6, 'Examen Oral', 1);
drawCheckbox($pdf, $weeklyPlan->prueba2);
$pdf->Cell(37.5, 6, 'Informe Oral', 1);
drawCheckbox($pdf, $weeklyPlan->proyecto2);
$pdf->Cell(37.5, 6, 'Diario Reflexivo', 1);
drawCheckbox($pdf, $weeklyPlan->contestar2);
$pdf->Cell(37.5, 6, 'Discusión Socializada', 1, 1);

$pdf->Ln(3);

// Nueva página para Valores y Acomodos
if ($pdf->GetY() > 200) {
    $pdf->AddPage();
}

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(95, 6, __("VALORES"), 0, 0, 'C');
$pdf->Cell(95, 5, __("ACOMODOS RAZONABLES"), 0, 1, 'C');
$pdf->Cell(95);
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(95, 5, 'Favor de referirse a tabla de acomodos razonables e indicar solo el número.', 0, 1, 'C');

$Y = $pdf->GetY();
$pdf->SetFont('Arial', '', 8);

// Valores (columna izquierda)
$valoresData = [
    ['valores1', 'Amor', 'valores12', 'Solidaridad'],
    ['valores2', 'Paz', 'valores13', 'Entrega'],
    ['valores3', 'Perdón', 'valores14', 'Tolerancia'],
    ['valores4', 'Respeto', 'valores15', 'Justicia'],
    ['valores5', 'Trabajo', 'valores16', 'Generosidad'],
    ['valores6', 'Fe', 'valores17', 'Servicio'],
    ['valores7', 'Armonía', 'valores18', 'Esperanza'],
    ['valores8', 'Honestidad', 'valores19', 'Comunicación'],
    ['valores9', 'Alegría', 'valores20', 'Responsabilidad'],
    ['valores10', 'Dignidad', 'valores21', 'Caridad'],
    ['valores11', 'Libertad', 'valores22', 'Esfuerzo'],
];

foreach ($valoresData as $row) {
    drawCheckbox($pdf, $weeklyPlan->{$row[0]}, false);
    $pdf->Cell(37.5, 6, $row[1]);
    if (count($row) > 2) {
        drawCheckbox($pdf, $weeklyPlan->{$row[2]}, false);
        $pdf->Cell(37.5, 6, $row[3], 0, 1);
    } else {
        $pdf->Ln();
    }
}

// Acomodos (columna derecha)
$X = 95;
$pdf->SetY($Y);
$pdf->SetX($X);

$acomodosData = [
    ['acomodo1', 'Atención', 'acomodo4', 'Evaluación'],
    ['acomodo2', 'Conducta', 'acomodo5', 'Ambiente y Lugar'],
    ['acomodo3', 'Presentación', 'acomodo6', 'Tiempo e Itinerario'],
];

foreach ($acomodosData as $row) {
    $pdf->SetX($X);
    drawCheckbox($pdf, $weeklyPlan->{$row[0]}, false);
    $pdf->Cell(37.5, 6, $row[1]);
    drawCheckbox($pdf, $weeklyPlan->{$row[2]}, false);
    $pdf->Cell(37.5, 6, $row[3], 0, 1);

    $pdf->SetX($X);
    $pdf->Cell(45, 6, $weeklyPlan->{$row[0] . '_1'}, 'B');
    $pdf->Cell(5);
    $pdf->Cell(45, 6, $weeklyPlan->{$row[2] . '_1'}, 'B', 1);

    $pdf->SetX($X);
    $pdf->Cell(45, 6, $weeklyPlan->{$row[0] . '_2'}, 'B');
    $pdf->Cell(5);
    $pdf->Cell(45, 6, $weeklyPlan->{$row[2] . '_2'}, 'B', 1);

    $pdf->Ln(2);
}

$pdf->Ln(10);

// Trabajo Semanal
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, __("TRABAJO SEMANAL"), 0, 1);

$pdf->SetFont('Arial', '', 9);
$dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

for ($i = 1; $i <= 10; $i++) {
    $diaIndex = ($i - 1) % 5;

    if ($pdf->GetY() > 250) {
        $pdf->AddPage();
    }

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(63.3, 6, $dias[$diaIndex], 1, 0);
    $pdf->Cell(63.3, 6, "Fase:", 1, 0);
    $pdf->Cell(63.3, 6, "Acomodo:", 1, 1);

    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(63.3, 6, "", 1, 0);
    $pdf->Cell(63.3, 6, $weeklyPlan->{"semanal{$i}_1"}, 'TRB', 0);
    $pdf->Cell(63.3, 6, $weeklyPlan->{"semanal{$i}_2"}, 1, 1);

    $pdf->Cell(190, 6, "Tarea: " . $weeklyPlan->{"semanal{$i}_3"}, 1, 1);
}

$pdf->Ln(3);

// Revisión
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(63.3, 6, "Revisado por: " . $weeklyPlan->revisado1, 1, 0);
$pdf->Cell(63.3, 6, "Fecha: " . $weeklyPlan->revisado2, 1, 0);
$pdf->Cell(17, 6, "Aprobado Si", 'LTB', 0);

$pdf->Rect($pdf->GetX() + 3, $pdf->GetY() + 1.5, 3, 3, ($weeklyPlan->revisado3 == 'Si') ? 'DF' : '');
$pdf->Cell(10, 6, '', 'TB', 0);
$pdf->Cell(5, 6, 'No', 'TB', 0);
$pdf->Rect($pdf->GetX() + 3, $pdf->GetY() + 1.5, 3, 3, ($weeklyPlan->revisado4 == 'Si') ? 'DF' : '');
$pdf->Cell(10, 6, '', 'TB', 0);
$pdf->Cell(21.3, 6, '', 'TBR', 1);

$pdf->Cell(190, 6, "Comentario: " . $weeklyPlan->revisado5, 1);

$pdf->Output('I', 'Plan_Semanal_2_' . $weeklyPlan->id2 . '.pdf');
