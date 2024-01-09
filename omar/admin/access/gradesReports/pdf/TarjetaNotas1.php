<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();

$lang = new Lang([
    ['Reporte de Notas', 'Grade Report'],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["Matricula:", "Tuition"],
    ["ENTREGADO", "DELIVERED"],
    ['Cuenta', 'Account'],
    ['Ningún documento entregado', 'No document delivered'],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Total de estudiantes', 'Total students'],
    ['Fecha', 'Date'],
    ['Documentos sin entregar', 'Undelivered documents'],
    ['Masculinos', 'Males'],
    ['Femeninas', 'Females'],
]);

$pdf = new PDF();
$pdf->useFooter(false);
$school = new School();
$teacherClass = new Teacher();
$studentClass = new Student();

$year = $school->year();
// $allGrades = $school->allGrades();
$pdf = new PDF();
$pdf->useFooter(false);

//$pdf->Footer = true;
//$pdf->useFooter(false);

$pdf->SetTitle($lang->translation("Reporte de Notas") . " $year", true);
$pdf->Fill();

$grade = $_POST['grade'];

$teacher = $teacherClass->findByGrade($grade);
$students = $studentClass->findByGrade($grade);

foreach ($students as $estu) {
    $pdf->AddPage('L');
    $pdf->useFooter(false);
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Reporte de Notas") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 12);
    $pdf->splitCells($lang->translation("Maestro(a):") . " $teacher->nombre $teacher->apellidos", $lang->translation("Grado:") . " $grade");

    $materias = [];
    $cursos = [];
    $estudiantes = [];
    $pdf->SetFillColor(89, 171, 227);
    //        $cursos = DB::table('padres')->select('distinct curso, descripcion')->where([
//          ['year', $year],
//          ['grado', $grade],
//          ['curso', '!=', ''],
//          ['curso', 'NOT LIKE', '%AA-%']
//        ])->orderBy('orden')->get();

    //foreach ($cursos as $curso) {
//        }


}

$pdf->Output();