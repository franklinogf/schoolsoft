<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;
use Classes\DataBase\DB;

Session::is_logged();

$lang = new Lang([
    ["Pruebas diagnósticas", "Diagnostic tests"],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["Nombre del estudiante", "Student name"],
    ['Asignatura', 'Subject'],
    ['Matrícula', 'School enrollment'],
    ['NO DOMINA', 'DO NOT DOMINATE'],
    ['DOMINA', 'DOMINATE'],
    ['Nombre', 'Name'],
    ['Total de estudiantes', 'Total students'],
    ['Fecha', 'Date'],
    ['Inglés', 'English'],
    ['Español', 'Spanish'],
    ['Matemáticas', 'Math'],

]);

$school = new School();
$teacherClass = new Teacher();
$studentClass = new Student();

$year = $school->info('year');
$allGrades = $school->allGrades();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Pruebas diagnósticas") . " $year", true);
$pdf->Fill();

foreach ($allGrades as $grade) {
    $teacher = $teacherClass->findByGrade($grade);

    $students = DB::table('padres')->where([
        ['grado', $grade],
        ['year', $year]
    ])->orderBy('curso')->get();

    $genderCount = ['M' => 0, 'F' => 0, 'T' => 0];
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Pruebas diagnósticas") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->splitCells($lang->translation("Maestro(a):") . " $teacher->nombre $teacher->apellidos", $lang->translation("Grado:") . " $grade");

    $pdf->SetFont('Arial', 'B', 10);
    //    $pdf->Cell(10, 10, '', 1, 0, 'C', true);
    $pdf->Cell(50, 10, $lang->translation("Asignatura"), 1, 0, 'C', true);
    $pdf->Cell(65, 10, $lang->translation("Matrícula"), 1, 0, 'C', true);
    $pdf->Cell(38, 5, $lang->translation("DOMINA"), 1, 0, 'C', true);
    $pdf->Cell(38, 5, $lang->translation("NO DOMINA"), 1, 0, 'C', true);


    $pdf->Ln(5);
    $pdf->Cell(115);
    //    $pdf->Cell(65, 5, 'TOTAL', 'LBR', 0, 'C', true);
    $pdf->Cell(38, 5, '# %', 1, 0, 'C', true);
    $pdf->Cell(38, 5, '# %', 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 10);
    $esp = 0;
    $esp1 = 0;
    $esp2 = 0;
    $mat = 0;
    $ing = 0;
    foreach ($students as $student) {
        if (substr($student->curso, 0, 3) == 'ESP') {
            $esp = $esp + 1;
            $a = 0;
            $b = 0;
            $c = 0;
            if ($student->sem1 > 0) {
                $a = $a + 1;
                $b = $b + $student->sem1;
            }
            if ($student->sem2 > 0) {
                $a = $a + 1;
                $b = $b + $student->sem2;
            }
            if ($a > 0) {
                $c = $b / $a;
                if (round($c, 0) > 69) {
                    $esp1 = $esp1 + 1;
                }
                if (round($c, 0) > 0 and round($c, 0) < 70) {
                    $esp2 = $esp2 + 1;
                }
            }
        }
        if (substr($student->curso, 0, 3) == 'MAT') {
            $mat = $mat + 1;
        }
        if (substr($student->curso, 0, 3) == 'ING') {
            $ing = $ing + 1;
        }
    }
    $pdf->Cell(50, 5, $lang->translation("Español"), 1, 0, 'C');
    $pdf->Cell(65, 5, $esp, 1, 0, 'C');
    $pdf->Cell(38, 5, '', 1, 0, 'C');
    $pdf->Cell(38, 5, '', 1, 1, 'C');


    $pdf->Cell(50, 5, $lang->translation("Inglés"), 1, 0, 'C');
    $pdf->Cell(65, 5, $ing, 1, 0, 'C');
    $pdf->Cell(38, 5, '', 1, 0, 'C');
    $pdf->Cell(38, 5, '', 1, 1, 'C');

    $pdf->Cell(50, 5, $lang->translation("Matemáticas"), 1, 0, 'C');
    $pdf->Cell(65, 5, $mat, 1, 0, 'C');
    $pdf->Cell(38, 5, '', 1, 0, 'C');
    $pdf->Cell(38, 5, '', 1, 1, 'C');
}




$pdf->Output();
