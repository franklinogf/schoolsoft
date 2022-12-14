<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Parents;
use Classes\Controllers\Student;

Session::is_logged();

$students = new Student();
$year1 = $students->info('year');
$year2 = (($year1[0] . $year1[1]) + 1) . '-' . (($year1[3] . $year1[4]) + 1);
$grades = DB::table("year")->select('DISTINCT grado')->where('year', $year1)->orderBy('grado')->get();
$allGrades = array_values(array_filter($grades, function ($value) {
    list($g1, $g2) = explode('-', $value->grado);

    return $g1 !== '12';
}));

$lang = new Lang([
    ['Lista de estudiantes no matriculados', 'List of students without enrollment'],
    ['Lista de estudiantes no matriculados del grado', 'List of students without enrollment in grade'],
    ['Nombre del estudiante', 'Student name'],
    ['Si V / NO V', 'Yes V / No V'],
    ['Telefonos', 'Telephones'],
    ['Comentario', 'Comment'],
]);

$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista de estudiantes no matriculados"));
$pdf->Fill();
// $pdf->SetAutoPageBreak(true, 50);
// var_dump($allGrades);
foreach ($allGrades as $grade) {
    $allStudents = array_values(array_filter($students->findByGrade($grade->grado), function ($value) use ($year2) {

        list($g1, $g2) = explode('-', $value->grado);
        $data = DB::table('year')->where([
            ['year', $year2],
            ['ss', $value->ss]
        ])->first();
        return $g1 !== '12' && !$data;
    }));

    $pdf->addPage();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, $lang->translation("Lista de estudiantes no matriculados del grado") . " $grade->grado", 0, 1, 'C');
    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 7, '#', 1, 0, 'C', true);
    $pdf->Cell(60, 7, $lang->translation("Nombre del estudiante"), 1, 0, 'C', true);
    $pdf->Cell(25, 7, $lang->translation("Si V / NO V"), 1, 0, 'C', true);
    $pdf->Cell(50, 7, $lang->translation("Telefonos"), 1, 0, 'C', true);
    $pdf->Cell(50, 7, $lang->translation("Comentario"), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 8);
    foreach ($allStudents as $index => $student) {
        $mother = new Parents($student->id);
        $pdf->Cell(10, 7, $index + 1, 1, 0, 'C');
        $pdf->Cell(60, 7, strtoupper("$student->apellidos $student->nombre"), 1);
        $pdf->Cell(25, 7, "[   ] / [   ]", 1, 0, 'C');
        $phones = '';
        if ($mother->tel_m !== '') {
            $phones = "M: $mother->tel_m";
        }
        if ($mother->tel_p !== '') {
            $phones .= " P: $mother->tel_p";
        }
        $pdf->Cell(50, 7, $phones, 1, 0, 'C');
        $pdf->Cell(50, 7, "", 1, 1, 'C');
    }
}

$pdf->Output();
