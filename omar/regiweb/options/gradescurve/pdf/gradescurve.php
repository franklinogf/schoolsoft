<?php

require_once '../../../../app.php';
use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\PDF;
use Classes\Session;

$course = $_GET['course'] ?? null;
$trimester = $_GET['trimester'] ?? null;
$value = $_GET['value'] ?? null;
$school = new School();
$notes = DB::table('padres')
    ->where([['baja', ''], ['year', $school->year()], ['curso', $course], ['id', Session::id()]])
    ->orderBy('apellidos')
    ->get();
$values = DB::table('valores')
    ->where([['trimestre', $trimester], ['year', $school->year()], ['nivel', 'Notas'], ['curso', $course]])
    ->first();
$curvePoints = $values->{"p{$value}"};

$pdf = new PDF();
$pdf->useFooter(false);
$pdf->Fill();
$pdf->AddPage();
$pdf->Cell(0, 5, ' INFORME DE CURVAS DEL EXAMEN ', 0, 1, 'C');
$pdf->Cell(50, 5, 'Fecha: ' . date('m/d/Y'), 0, 0, 'L');
$pdf->Cell(100, 5, '', 0, 0, 'L');
$pdf->Cell(30, 5, $school->year(), 0, 1, 'R');
$pdf->Cell(70, 5, 'APELLIDOS', 1, 0, 'C', true);
$pdf->Cell(60, 5, 'NOMBRE', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'NOTA', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'CURVA', 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 10);
$gradesLetters = [
    'A' => 0,
    'B' => 0,
    'AB' => 0,
    'C' => 0,
    'ABC' => 0,
    'D' => 0,
    'DF' => 0,
    'O' => 0,
];
$curveGradesLetters = [
    'A' => 0,
    'B' => 0,
    'AB' => 0,
    'C' => 0,
    'ABC' => 0,
    'D' => 0,
    'DF' => 0,
    'O' => 0,
];
function updateGradesArray($grade, &$array)
{
    global $school;
    if ($grade >= $school->info('vala')) {
        $array['A']++;
        $array['AB']++;
        $array['ABC']++;
    } elseif ($grade >= $school->info('valb')) {
        $array['B']++;
        $array['AB']++;
        $array['ABC']++;
    } elseif ($grade >= $school->info('valc')) {
        $array['C']++;
        $array['ABC']++;
    } elseif ($grade >= $school->info('vald')) {
        $array['D']++;
        $array['DF']++;
    } elseif ($grade >= $school->info('valf')) {
        $array['DF']++;
    } else {
        $array['O']++;
    }

}

foreach ($notes as $note) {
    $grade = intval($note->{"not$value"});
    $curveGrade = $grade !== 0 ? intval($grade) - intval($curvePoints) : '';
    updateGradesArray($grade, $gradesLetters);
    updateGradesArray($curveGrade, $curveGradesLetters);
    $pdf->Cell(70, 5, $note->apellidos, 1, 0, 'C');
    $pdf->Cell(60, 5, $note->nombre, 1, 0, 'C');
    $pdf->Cell(25, 5, $curveGrade, 1, 0, 'C');
    $pdf->Cell(25, 5, $grade !== 0 ? $grade : '', 1, 1, 'C');
}

$pdf->Cell(15, 10, '', 0, 1, 'C');
$pdf->Cell(65, 5, 'Puntos aplicados a la curva: ', 1, 0, 'C', true);
$pdf->Cell(15, 5, $curvePoints, 1, 0, 'C');
$pdf->Cell(45, 5, $values->{"f{$value}"}, 1, 1, 'C');
$pdf->Cell(20, 5, 'Antes', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'A', 1, 0, 'C', true);
$pdf->Cell(15, 5, '%A', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'B', 1, 0, 'C', true);
$pdf->Cell(15, 5, '%AB', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'C', 1, 0, 'C', true);
$pdf->Cell(15, 5, '%ABC', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'D', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'F', 1, 0, 'C', true);
$pdf->Cell(15, 5, '%DF', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'O', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'Total', 1, 1, 'C', true);

$pdf->Cell(20);
$pdf->Cell(15, 5, $curveGradesLetters['A'], 1, 0, 'C');
$pdf->Cell(15, 5, number_format(($curveGradesLetters['A'] / count($notes)) * 100, 2), 1, 0, 'C');
$pdf->Cell(15, 5, $curveGradesLetters['B'], 1, 0, 'C');
$pdf->Cell(15, 5, number_format(($curveGradesLetters['AB'] / count($notes)) * 100, 2), 1, 0, 'C');
$pdf->Cell(15, 5, $curveGradesLetters['C'], 1, 0, 'C');
$pdf->Cell(15, 5, number_format(($curveGradesLetters['ABC'] / count($notes)) * 100, 2), 1, 0, 'C');
$pdf->Cell(15, 5, $curveGradesLetters['D'], 1, 0, 'C');
$pdf->Cell(15, 5, $curveGradesLetters['DF'], 1, 0, 'C');
$pdf->Cell(15, 5, number_format(($curveGradesLetters['DF'] / count($notes)) * 100, 2), 1, 0, 'C');
$pdf->Cell(15, 5, $curveGradesLetters['O'], 1, 0, 'C');
$pdf->Cell(15, 5, count($notes), 1, 1, 'C');
$pdf->Cell(20, 5, 'Despues', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'A', 1, 0, 'C', true);
$pdf->Cell(15, 5, '%A', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'B', 1, 0, 'C', true);
$pdf->Cell(15, 5, '%AB', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'C', 1, 0, 'C', true);
$pdf->Cell(15, 5, '%ABC', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'D', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'F', 1, 0, 'C', true);
$pdf->Cell(15, 5, '%DF', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'O', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'Total', 1, 1, 'C', true);

$pdf->Cell(20);
$pdf->Cell(15, 5, $gradesLetters['A'], 1, 0, 'C');
$pdf->Cell(15, 5, number_format(($gradesLetters['A'] / count($notes)) * 100, 2), 1, 0, 'C');
$pdf->Cell(15, 5, $gradesLetters['B'], 1, 0, 'C');
$pdf->Cell(15, 5, number_format(($gradesLetters['AB'] / count($notes)) * 100, 2), 1, 0, 'C');
$pdf->Cell(15, 5, $gradesLetters['C'], 1, 0, 'C');
$pdf->Cell(15, 5, number_format(($gradesLetters['ABC'] / count($notes)) * 100, 2), 1, 0, 'C');
$pdf->Cell(15, 5, $gradesLetters['D'], 1, 0, 'C');
$pdf->Cell(15, 5, $gradesLetters['DF'], 1, 0, 'C');
$pdf->Cell(15, 5, number_format(($gradesLetters['DF'] / count($notes)) * 100, 2), 1, 0, 'C');
$pdf->Cell(15, 5, $gradesLetters['O'], 1, 0, 'C');
$pdf->Cell(15, 5, count($notes), 1, 1, 'C');

$pdf->Output();
