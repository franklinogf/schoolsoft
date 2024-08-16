<?php
require_once '../../../../app.php';
// le falta el codigo de barra.
use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;
//use Classes\pdf_codabar;

Session::is_logged();

$lang = new Lang([
    ["Lista de estudiantes por salón hogar y fotos", "List of students by homeroom and photos"],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["Fotos", "Photos"],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Total de estudiantes', 'Total students'],
    ['Masculinos', 'Males'],
    ['Femeninas', 'Females'],
    ['Codigo de barra', 'Barcode']

]);

$school = new School();
$studentClass = new Student();
$teacherClass = new Teacher();

$year = $school->year();
$allGrades = $school->allGrades();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista de estudiantes por salón hogar y fotos") . " $year", true);
$pdf->Fill();

foreach ($allGrades as $grade) {
    $students = $studentClass->findByGrade($grade);
    $teacher = $teacherClass->findByGrade($grade);
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, utf8_encode($lang->translation("Lista de estudiantes por salón hogar y fotos")) . " $year", 0, 1, 'C');

    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 12);
    $nom = $teacher->nombre ?? '';
    $ape = $teacher->apellidos ?? '';
    $pdf->splitCells($lang->translation("Maestro(a):") . " $nom $ape", $lang->translation("Grado:") . " $grade");

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 5, '', 1, 0, 'C', true);
    $pdf->Cell(25, 5, $lang->translation("Fotos"), 1, 0, 'C', true);
    $pdf->Cell(55, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
    $pdf->Cell(45, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
    $pdf->Cell(55, 5, $lang->translation("Codigo de barra"), 1, 1, 'C', true);
    $pdf->ln(2);
    $pdf->SetFont('Arial', '', 10);

    foreach ($students as $count => $student) {

        $pdf->Cell(10, 10, $count + 1, 0, 0, 'C');
        if (empty($student->tipo)) {
            $foto = "../../../../../images/none.gif";
        } else {
            $foto = "../../../../picture/" . $student->tipo . ".jpg";
        }

        $pdf->Image($foto, $pdf->GetX() + 10, $pdf->GetY() + 1, 5, 10);
        $pdf->Cell(25, 10, '', 0, 0, 'C');
        $pdf->Cell(55, 10, $student->apellidos, 0);
        $pdf->Cell(45, 10, $student->nombre, 0);
        //        $pdf->Cell(55, 10, '___________________________', 0, 1,'C');

        if ($student->cbarra !== '') {
            $pdf->Line($pdf->GetX(), $pdf->GetY() + 15, $pdf->GetX() + 55, $pdf->GetY() + 15);
            $pdf->Codabar($pdf->GetX() + 5, $pdf->GetY() + 3, $student->cbarra, '*', '*', 0.29, 5);
            $pdf->Ln(15);
        } else {
            //            $pdf->Ln(10);
            $pdf->Cell(45, 5, 'N/A', 'B', 1);
            $pdf->Ln(5);
        }
    }
}




$pdf->Output();
