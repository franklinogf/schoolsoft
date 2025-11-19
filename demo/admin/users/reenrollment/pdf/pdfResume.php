<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;

Session::is_logged();

$students = new Student();
$year1 = $students->info('year');
$year2 = (($year1[0] . $year1[1]) + 1) . '-' . (($year1[3] . $year1[4]) + 1);
$grades = DB::table("year")->select('DISTINCT grado')->where('year', $year1)->orderBy('grado')->get();
$allGrades = array_values(array_filter($grades, function ($grade) {
    [$g1] = explode('-', $grade->grado);

    return $g1 !== '12';
}));
$lang = new Lang([
    ['Lista de estudiantes no matriculados en resumen', 'List of students without enrollment in summary'],
    ['Grado', 'Grade'],
    ['Femenino', 'Female'],
    ['Masculino', 'Male'],
    ['Totales', 'Total'],
]);

$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista de estudiantes no matriculados en resumen"));
$pdf->Fill();
// $pdf->SetAutoPageBreak(true, 50);
// var_dump($allGrades);

$pdf->addPage();
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, $lang->translation("Lista de estudiantes no matriculados en resumen")." $year1", 0, 1, 'C');
$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30);
$pdf->Cell(15, 7, '#', 1, 0, 'C', true);
$pdf->Cell(30, 7, $lang->translation("Grado"), 1, 0, 'C', true);
$pdf->Cell(30, 7, $lang->translation("Femenino"), 1, 0, 'C', true);
$pdf->Cell(30, 7, $lang->translation("Masculino"), 1, 0, 'C', true);
$pdf->Cell(30, 7, "Total", 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 10);
$_M = 0;
$_F = 0;
foreach ($allGrades as $index => $grade) {
        $allStudents = DB::table('year')->where([
            ['year', $year1],
            ['grado', $grade->grado],
        ])->get();

    $M = 0;
    $F = 0;
    foreach ($allStudents as $student) {
        $data = DB::table('year')->where([
            ['year', $year2],
            ['ss', $student->ss]
        ])->first();
        $si = $data->nombre ?? 'no';
        if ($si == 'no')
           {

        if ($student->genero === 'M' || $student->genero == '2') {
            $M++;
            $_M++;
        } else if ($student->genero === 'F' || $student->genero == '1') {
            $F++;
            $_F++;
        }
      }
    }
    $pdf->Cell(30);
    $pdf->Cell(15, 7, $index + 1, 1, 0, 'C');
    $pdf->Cell(30, 7, $grade->grado, 1, 0, 'C');
    $pdf->Cell(30, 7, $F, 1, 0, 'C');
    $pdf->Cell(30, 7, $M, 1, 0, 'C');
    $pdf->Cell(30, 7, $F + $M, 1, 1, 'C');
}
$pdf->Cell(30);
$pdf->Cell(45, 7, $lang->translation("Totales"), 1, 0, 'R', true);
$pdf->Cell(30, 7, $_F, 1, 0, 'C', true);
$pdf->Cell(30, 7, $_M, 1, 0, 'C', true);
$pdf->Cell(30, 7, $_F + $_M, 1, 1, 'C', true);

$pdf->Output();
