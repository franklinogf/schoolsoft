<?php
require_once '../../../../app.php';

use App\Models\Admin;
use App\Models\Student;
use App\Models\Teacher;
use App\Queries\StudentQuery;
use Classes\PDF;
use Classes\Session;

// use Classes\Controllers\Student;

use Classes\Util;

Session::is_logged();






$year = Admin::primaryAdmin()->year();
$allGrades = StudentQuery::getAllGrades();
$pdf = new PDF();
$pdf->SetTitle(__("Lista de estudiantes por salón hogar") . " $year", true);
$pdf->Fill();

foreach ($allGrades as $grade) {
    $teacher = Teacher::byGrade($grade)->first();
    $students = Student::byGrade($grade)->get();
    $genderCount = ['M' => 0, 'F' => 0, 'T' => 0];
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, __("Lista de estudiantes por salón hogar") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $nom = $teacher->nombre ?? '';
    $ape = $teacher->apellidos ?? '';
    $pdf->splitCells(__("Maestro") . ": $nom $ape", __("Grado") . ": $grade");

    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 5, '', 1, 0, 'C', true);
    $pdf->Cell(20, 5, __("Cuenta"), 1, 0, 'C', true);
    $pdf->Cell(10, 5, 'ID', 1, 0, 'C', true);
    $pdf->Cell(15, 5, __("Genero"), 1, 0, 'C', true);
    $pdf->Cell(65, 5, __("Apellidos"), 1, 0, 'C', true);
    $pdf->Cell(65, 5, __("Nombre"), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);

    foreach ($students as $count => $student) {
        $gender = Util::gender($student->genero);
        if ($gender !== '') {
            $genderCount[$gender]++;
            $genderCount['T']++;
        }
        $pdf->Cell(10, 5, $count + 1, 1, 0, 'C');
        $pdf->Cell(20, 5, $student->id, 1, 0, 'C');
        $pdf->Cell(10, 5, Util::ssLast4Digits($student->ss), 1, 0, 'C');
        $pdf->Cell(15, 5, $gender, 1, 0, 'C');
        $pdf->Cell(65, 5, $student->apellidos, 1);
        $pdf->Cell(65, 5, $student->nombre, 1, 1);
    }
    $pdf->Ln(2);
    $pdf->Cell(40, 5, __("Total de estudiantes"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $genderCount['T'], 1, 1, 'C');
    $pdf->Cell(40, 5, __("Masculinos"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $genderCount['M'], 1, 1, 'C');
    $pdf->Cell(40, 5, __("Femeninas"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $genderCount['F'], 1, 1, 'C');
}




$pdf->Output();
