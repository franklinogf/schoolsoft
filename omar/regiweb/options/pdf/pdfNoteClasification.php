<?php
require_once '../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();


$teacher = new Teacher(Session::id());
$student = new Student;
$students = $student->findByGrade($teacher->grado);
$gradesNumbers = [[1, 10], [11, 20], [21, 30], [31, 40]];


$lang = new Lang([
    ["Clasificación de notas", "Grades classification"],
    ["Informe de Clasificación de Notas", "Grades classification report"],
    ["GRADO", "GRADE"],
    ["AÑO", "YEAR"],
    ["Nombre del estudiante", "Student name"],

]);


$pdf = new PDF();
$pdf->SetTitle($lang->translation("Clasificación de notas"), true);
$pdf->AddPage('L');
$pdf->SetLeftMargin(5);
$pdf->SetAutoPageBreak(true, 10);
$pdf->Fill();

$pdf->SetFont('Arial', 'B', 17);
$pdf->Cell(0, 5, $lang->translation("Informe de Clasificación de Notas"), 0, 1, 'C');
$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, $lang->translation("GRADO") . " $teacher->grado / " . $lang->translation("AÑO") . " " . $teacher->info('year'), 0, 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(110, 7, $lang->translation("Nombre del estudiante"), 1, 0, 'C', true);
$pdf->Cell(8, 7, 'A-1', 1, 0, 'C', true);
$pdf->Cell(8, 7, 'B-1', 1, 0, 'C', true);
$pdf->Cell(8, 7, 'C-1', 1, 0, 'C', true);
$pdf->Cell(8, 7, 'D-1', 1, 0, 'C', true);
$pdf->Cell(8, 7, 'F-1', 1, 0, 'C', true);
$pdf->Cell(8, 7, 'A-2', 1, 0, 'C', true);
$pdf->Cell(8, 7, 'B-2', 1, 0, 'C', true);
$pdf->Cell(8, 7, 'C-2', 1, 0, 'C', true);
$pdf->Cell(8, 7, 'D-2', 1, 0, 'C', true);
$pdf->Cell(8, 7, 'F-2', 1, 0, 'C', true);
$pdf->Cell(8, 7, 'A-3', 1, 0, 'C', true);
$pdf->Cell(8, 7, 'B-3', 1, 0, 'C', true);
$pdf->Cell(8, 7, 'C-3', 1, 0, 'C', true);
$pdf->Cell(8, 7, 'D-3', 1, 0, 'C', true);
$pdf->Cell(8, 7, 'F-3', 1, 0, 'C', true);
$pdf->Cell(8, 7, 'A-4', 1, 0, 'C', true);
$pdf->Cell(8, 7, 'B-4', 1, 0, 'C', true);
$pdf->Cell(8, 7, 'C-4', 1, 0, 'C', true);
$pdf->Cell(8, 7, 'D-4', 1, 0, 'C', true);
$pdf->Cell(8, 7, 'F-4', 1, 0, 'C', true);
$pdf->Cell(15, 7, 'PROM', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 10);
foreach ($students as $student) {
    $finalGrade = $totalFinal = $totalFinalAmount = 0;
    $fathers = DB::table('padres')->where([
        ['ss', $student->ss],
        ['year', $teacher->info('year')]
    ])->get();
    $grades = [];
    // echo "$student->ss <br>";
    foreach ($fathers as $father) {
        $final = $finalAmount = 0;
        for ($i = 1; $i <= 4; $i++) {
            // echo " $father->curso - nota$i = ";
            // echo $father->{"nota$i"};
            if ($father->{"nota$i"} >= $teacher->info('vala')) {
                $grades[$i]['A']++;
                $final += $father->{"nota$i"};
                $finalAmount++;
                // echo " -> A";
            } else if ($father->{"nota$i"} >= $teacher->info('valb')) {
                $grades[$i]['B']++;
                $final += $father->{"nota$i"};
                $finalAmount++;
                // echo " -> B";
            } else if ($father->{"nota$i"} >= $teacher->info('valc')) {
                $grades[$i]['C']++;
                $final += $father->{"nota$i"};
                $finalAmount++;
                // echo " -> C";
            } else if ($father->{"nota$i"} >= $teacher->info('vald')) {
                $grades[$i]['D']++;
                $final += $father->{"nota$i"};
                $finalAmount++;
                // echo " -> D";
            } else if ($father->{"nota$i"} >= $teacher->info('valf')) {
                $grades[$i]['F']++;
                $final += $father->{"nota$i"};
                $finalAmount++;
                // echo " -> F";
            }
            // echo "<br>";
        }
        if ($finalAmount > 0) {
            $totalFinal += round($final / $finalAmount);
            $totalFinalAmount++;
            // echo "Tota Final -> $final / $finalAmount = ". round($final / $finalAmount);
            // echo "<br>";
        }
    }

    if ($totalFinalAmount > 0) {
        $finalGrade = round($totalFinal / $totalFinalAmount);
        // echo "Final Grade -> $totalFinal / $totalFinalAmount = $finalGrade";
        // echo "<br>";
    }
    DB::table('year')->where([
        ['ss', $student->ss],
        ['year', $teacher->info('year')],
    ])->update([
                'fin' => $finalGrade
            ]);
    // echo "<hr>";

    $pdf->Cell(110, 7, "$student->nombre $student->apellidos", 1);
    $pdf->Cell(8, 7, isset($grades[1]) ?? $grades[1]['A'], 1, 0, 'C');
    $pdf->Cell(8, 7, isset($grades[1]) ?? $grades[1]['B'], 1, 0, 'C');
    $pdf->Cell(8, 7, isset($grades[1]) ?? $grades[1]['C'], 1, 0, 'C');
    $pdf->Cell(8, 7, isset($grades[1]) ?? $grades[1]['D'], 1, 0, 'C');
    $pdf->Cell(8, 7, isset($grades[1]) ?? $grades[1]['F'], 1, 0, 'C');
    $pdf->Cell(8, 7, isset($grades[2]) ?? $grades[2]['A'], 1, 0, 'C');
    $pdf->Cell(8, 7, isset($grades[2]) ?? $grades[2]['B'], 1, 0, 'C');
    $pdf->Cell(8, 7, isset($grades[2]) ?? $grades[2]['C'], 1, 0, 'C');
    $pdf->Cell(8, 7, isset($grades[2]) ?? $grades[2]['D'], 1, 0, 'C');
    $pdf->Cell(8, 7, isset($grades[2]) ?? $grades[2]['F'], 1, 0, 'C');
    $pdf->Cell(8, 7, isset($grades[3]) ?? $grades[3]['A'], 1, 0, 'C');
    $pdf->Cell(8, 7, isset($grades[3]) ?? $grades[3]['B'], 1, 0, 'C');
    $pdf->Cell(8, 7, isset($grades[3]) ?? $grades[3]['C'], 1, 0, 'C');
    $pdf->Cell(8, 7, isset($grades[3]) ?? $grades[3]['D'], 1, 0, 'C');
    $pdf->Cell(8, 7, isset($grades[3]) ?? $grades[3]['F'], 1, 0, 'C');
    $pdf->Cell(8, 7, isset($grades[4]) ?? $grades[4]['A'], 1, 0, 'C');
    $pdf->Cell(8, 7, isset($grades[4]) ?? $grades[4]['B'], 1, 0, 'C');
    $pdf->Cell(8, 7, isset($grades[4]) ?? $grades[4]['C'], 1, 0, 'C');
    $pdf->Cell(8, 7, isset($grades[4]) ?? $grades[4]['D'], 1, 0, 'C');
    $pdf->Cell(8, 7, isset($grades[4]) ?? $grades[4]['F'], 1, 0, 'C');
    $pdf->Cell(15, 7, $finalGrade, 1, 1, 'C');
}



$pdf->Output();
