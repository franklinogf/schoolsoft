<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\Controllers\Parents;
use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;

Session::is_logged();

$lang = new Lang([
    ['Lista personas autorizadas', 'List of authorized persons'],
    ['Celular', 'Cell Phone'],
    ['Padres', 'Parents'],
    ['Grado ', 'Grade '],
    ['Nombre del estudiante', 'Student name'],
    ['Personas Autorizadas', 'Authorized persons'],
    ['Parentesco', 'Relationship'],
]);
$grade = $_POST['grade'] ?? '';

$school = new School();
$year = $school->year();
$studentClass = new Student();

class nPDF extends PDF
{
    function header()
    {
        global $lang;
        global $year;
        global $grupo;
        parent::header();
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 5, $lang->translation("Lista personas autorizadas") . " $year ". $lang->translation("Grado ").$grupo, 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(10, 5, '', 1, 0, 'C', true);
        $this->Cell(70, 5, $lang->translation("Nombre del estudiante"), 1, 0, 'C', true);
        $this->Cell(60, 5, $lang->translation("Personas Autorizadas"), 1, 0, 'C', true);
        $this->Cell(30, 5, $lang->translation("Parentesco"), 1, 0, 'C', true);
        $this->Cell(25, 5, $lang->translation("Celular"), 1, 1, 'C', true);
        $this->SetFont('Arial', '', 10);
    }
}
$pdf = new nPDF();
$pdf->SetTitle($lang->translation("Lista personas autorizadas") . " $year", true);
$pdf->Fill();
$allGrades = $school->allGrades();
foreach ($allGrades as $grade) {
    $students = $studentClass->findByGrade($grade);
    $pdf->SetFont('Arial', 'B', 15);

$count = 1;
$grupo = '';
foreach ($students as $student) {

$parent = DB::table('madre')->where([
    ['id', $student->id],
])->orderBy('id')->first();
        if ($grupo != $student->grado)
           {
           $grupo = $student->grado;
           $pdf->AddPage('');
            $pdf->SetFont('Arial', '', 9);
           }
         if (!empty($parent->per1) or !empty($parent->per2) or !empty($parent->per3) or !empty($parent->per4))
            {
            $pdf->Cell(10, 5, $count, 0, 0, 'C');
            $pdf->Cell(70, 5, $student->apellidos . ' ' . $student->nombre);
            $pdf->Cell(60, 5, $parent->per1);
            $pdf->Cell(30, 5, $parent->rel1);
            $pdf->Cell(25, 5, $parent->cel1, 0, 1, 'L');
            $v=0;
            for ($i = 2; $i <= 4; $i++) {
                if (!empty($parent->{"per$i"}))
                   {
                   $pdf->Cell(10, 5, '', 0, 0, 'C');
                   $pdf->Cell(70, 5, '');
                    $pdf->Cell(60, 5, $parent->{"per$i"});
                   $pdf->Cell(30, 5, $parent->{"rel$i"});
                    $pdf->Cell(25, 5, $parent->{"cel$i"}, 0, 1, 'L');
                   }
                }
            $count++;
            }

    }
}


$pdf->Output();
