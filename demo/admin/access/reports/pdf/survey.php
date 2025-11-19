<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

$lang = new Lang([
    ["Informe de encuestas", "Survey report"],
    ['Nombre', 'Name'],
    ['Fecha', 'Date'],
    ['Respuesta', 'Answer'],
    ['SI', 'YES'],
    ['Indeciso', 'Undecided'],
]);
$school = new School();
$year = $school->year();
$code = $_POST['code'];
$survey = DB::table("estadisticas")->where(['codigo', $code])->first();
$surveyAnswers = DB::table("respuestas")->where(['codigo', $code])->orderBy('apellidos')->get();

$pdf = new PDF();
$pdf->SetTitle($lang->translation("Informe de encuestas") . " $year", true);
$pdf->Fill();
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, $lang->translation("Informe de encuestas") . " $year", 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, $survey->titulo, 0, 1, 'C');
$pdf->Ln(1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 5, '', 1, 0, 'L', true);
$pdf->Cell(100, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Respuesta"), 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 10);
$totals = ['YES' => 0, 'NO' => 0, 'OTHERS' => 0];
foreach ($surveyAnswers as $count => $answer) {
    $pdf->Cell(10, 5, $count + 1, 0, 0, 'C');
    $pdf->Cell(100, 5, "$answer->nombre $answer->apellidos", 0);
    $pdf->Cell(30, 5, $answer->fecha, 0, 0, 'C');
    $pdf->Cell(30, 5, $answer->dijo, 0, 1, 'C');
    $pdf->Ln(1);
    $pdf->MultiCell(170, 5, $answer->comentario);
    $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 170, $pdf->GetY());
    if ($answer->dijo === 'SI') {
        $totals['YES']++;
    } else if ($answer->dijo === 'NO') {
        $totals['NO']++;
    } else {
        $totals['OTHERS']++;
    }
}

$pdf->Ln(5);
$pdf->Cell(50, 5, 'Total', 1, 1, 'C', true);
$pdf->Cell(30, 5, $lang->translation("SI"), 1, 0, 'C', true);
$pdf->Cell(20, 5, $totals['YES'], 1, 1, 'C');
$pdf->Cell(30, 5, 'NO', 1, 0, 'C', true);
$pdf->Cell(20, 5, $totals['NO'], 1, 1, 'C');
$pdf->Cell(30, 5, $lang->translation("Indeciso"), 1, 0, 'C', true);
$pdf->Cell(20, 5, $totals['OTHERS'], 1, 1, 'C');




$pdf->Output();
