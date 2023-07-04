<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;

Session::is_logged();

$lang = new Lang([
    ["Lista de teléfonos", "Phone list"],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["Nombre del estudiante", "Student name"],
    ['Cuenta', 'Account'],
    ['Genero', 'Gender'],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Casa', 'Home'],
    ['Celular', 'Cell Phone'],
    ['Trabajo', 'Job'],
    ['Padre: ', 'Father: '],
    ['Madre: ', 'Mother: '],

]);

$school = new School();
$teacherClass = new Teacher();
$studentClass = new Student();

$year = $school->info('year');
$allGrades = $school->allGrades();


class nPDF extends PDF
{
    function header()
    {
        global $lang;
        global $year;
        global $grade;
        global $teacher2;
        parent::header();       
    $this->SetFont('Arial', 'B', 15);
    $this->Cell(0, 5, $lang->translation("Lista de teléfonos") . " $year", 0, 1, 'C');
    $this->Ln(5);
    $this->SetFont('Arial', 'B', 12);
    $this->splitCells($lang->translation("Maestro(a):") . ' '.utf8_decode($teacher2), $lang->translation("Grado:") . " $grade");
    $this->SetX(5);
    $this->SetFont('Arial', 'B', 10);
    $this->Cell(5, 5, '', 0, 0 );
    $this->Cell(10, 5, '', 1, 0, 'C', true);
    $this->Cell(55, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
    $this->Cell(50, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
    $this->Cell(25, 5, $lang->translation("Casa"), 1, 0, 'C', true);
    $this->Cell(25, 5, $lang->translation("Celular"), 1, 0, 'C', true);
    $this->Cell(25, 5, $lang->translation("Trabajo"), 1, 1, 'C', true);    
        $this->SetFont('Arial', '', 10);
    }
}


$pdf = new nPDF();
$pdf->SetTitle($lang->translation("Lista de teléfonos"). " $year", true);
$pdf->Fill();

foreach ($allGrades as $grade) {
    $teacher = $teacherClass->findByGrade($grade);
    $teacher2 = $teacher->nombre.' '.$teacher->apellidos;
    $students = $studentClass->findByGrade($grade);
    $genderCount = ['M' => 0, 'F' => 0, 'T' => 0];
    $pdf->AddPage();
    foreach ($students as $count => $student) {
        $genderCount['T']++;
        $pdf->Cell(10, 5, $count + 1, 'TL', 0, 'C');
        $pdf->Cell(55, 5, utf8_decode($student->apellidos), 'T');
        $pdf->Cell(50, 5, $student->nombre, 'T', 0);
        $pdf->Cell(75, 5, '', 'TR', 1);
    $parents = DB::table('madre')->where([
        ['id', $student->id],
    ])->orderBy('id')->get();
    foreach ($parents as $parent) {
        $pdf->Cell(10, 5, '', 'L', 0, 'C');
        $pdf->Cell(15, 5, $lang->translation("Padre: "), 0, 0);
        $pdf->Cell(90, 5, utf8_decode($parent->padre), 0, 0);
        $pdf->Cell(25, 5, $parent->tel_p, 0, 0);
        $pdf->Cell(25, 5, $parent->cel_p, 0, 0);
        $pdf->Cell(25, 5, $parent->tel_t_p, 'R', 1);
        $pdf->Cell(10, 5, '', 'LB', 0, 'C');
        $pdf->Cell(15, 5, $lang->translation("Madre: "), 'B', 0);
        $pdf->Cell(90, 5, utf8_decode($parent->madre), 'B', 0);
        $pdf->Cell(25, 5, $parent->tel_p, 'B', 0);
        $pdf->Cell(25, 5, $parent->cel_p, 'B', 0);
        $pdf->Cell(25, 5, $parent->tel_t_p, 'BR', 1);
       }
    }
    $pdf->Ln(2);
}


$pdf->Output();
