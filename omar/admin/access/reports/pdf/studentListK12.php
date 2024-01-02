<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;

Session::is_logged();

$lang = new Lang([
    ["Lista esrudiuantes grados", "Student Grade List"],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["Nombre del estudiante", "Student name"],
    ['Cuenta', 'Account'],
    ['Genero', 'Gender'],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Total de estudiantes', 'Total students'],
    ['Fecha', 'Date'],
    ['Edad', 'Age'],
    ['Masculinos', 'Males'],
    ['Femeninas', 'Females'],
    ['S.S.', 'S.S.'],

]);

$school = new School();
$teacherClass = new Teacher();
$studentClass = new Student();

$year = $school->year();
$allGrades = $school->allGrades();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista esrudiuantes grados"). " $year", true);
$pdf->Fill();

foreach ($allGrades as $grade) {
    $teacher = $teacherClass->findByGrade($grade);
    $students = $studentClass->findByGrade($grade);
    $genderCount = ['M' => 0, 'F' => 0, 'T' => 0];
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Lista esrudiuantes grados") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->splitCells($lang->translation("Maestro(a):") . " $teacher->nombre $teacher->apellidos", $lang->translation("Grado:") . " $grade");

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 5, '', 1, 0, 'C', true);
    $pdf->Cell(18, 5, $lang->translation("Cuenta"), 1, 0, 'C', true);
    $pdf->Cell(55, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
    $pdf->Cell(45, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
    $pdf->Cell(5, 5, "12", 1, 0, 'C', true);
    $pdf->Cell(5, 5, "11", 1, 0, 'C', true);
    $pdf->Cell(5, 5, "10", 1, 0, 'C', true);
    $pdf->Cell(5, 5, "09", 1, 0, 'C', true);
    $pdf->Cell(5, 5, "08", 1, 0, 'C', true);
    $pdf->Cell(5, 5, "07", 1, 0, 'C', true);
    $pdf->Cell(5, 5, "06", 1, 0, 'C', true);
    $pdf->Cell(5, 5, "05", 1, 0, 'C', true);
    $pdf->Cell(5, 5, "04", 1, 0, 'C', true);
    $pdf->Cell(5, 5, "03", 1, 0, 'C', true);
    $pdf->Cell(5, 5, "02", 1, 0, 'C', true);
    $pdf->Cell(5, 5, "01", 1, 0, 'C', true);
    $pdf->Cell(6, 5, "KG", 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);

    foreach ($students as $count => $student) {
        $dia=date(j);
        $mes=date(n);
        $ano=date(Y);
        list($ss1,$ss2,$ss3) = explode("-",$student->ss);

        $fec=$student->fecha; 
        list($anonaz, $mesnaz, $dianaz) = explode('-', $fec);
        if (($mesnaz == $mes) && ($dianaz > $dia)) {$ano=($ano-1);}
        if ($mesnaz > $mes) {$ano=($ano-1);}
        $edad=$ano-$anonaz;
        if ($edad > 20){$edad='';}
        $gender = Util::gender($student->genero);
        $genderCount[$gender]++;
        $genderCount['T']++;
        $pdf->Cell(10, 5, $count + 1, 1, 0, 'C');
        $pdf->Cell(18, 5, $student->id, 1, 0, 'C');
        $pdf->Cell(55, 5, $student->apellidos, 1);
        $pdf->Cell(45, 5, $student->nombre, 1, 0);
        $pdf->Cell(5, 5, '', 1, 0, 'C');
        $pdf->Cell(5, 5, '', 1, 0, 'C');
        $pdf->Cell(5, 5, '', 1, 0, 'C');
        $pdf->Cell(5, 5, '', 1, 0, 'C');
        $pdf->Cell(5, 5, '', 1, 0, 'C');
        $pdf->Cell(5, 5, '', 1, 0, 'C');
        $pdf->Cell(5, 5, '', 1, 0, 'C');
        $pdf->Cell(5, 5, '', 1, 0, 'C');
        $pdf->Cell(5, 5, '', 1, 0, 'C');
        $pdf->Cell(5, 5, '', 1, 0, 'C');
        $pdf->Cell(5, 5, '', 1, 0, 'C');
        $pdf->Cell(5, 5, '', 1, 0, 'C');
        $pdf->Cell(6, 5, '', 1, 1, 'C');
    }
    $pdf->Ln(2);
    $pdf->Cell(40, 5, $lang->translation("Total de estudiantes"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $genderCount['T'], 1, 1, 'C');
    $pdf->Cell(40, 5, $lang->translation("Masculinos"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $genderCount['M'], 1, 1, 'C');
    $pdf->Cell(40, 5, $lang->translation("Femeninas"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $genderCount['F'], 1, 1, 'C');
}




$pdf->Output();
