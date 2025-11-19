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

$lang = new Lang([['Información por grado', 'Information by grade'],
    ['Celular', 'Cell Phone'],
    ['Padres', 'Parents'],
    ['Grado ', 'Grade '],
    ['Ocupación', 'Occupation'],
    ['Nombre del estudiante', 'Student name'],
    ['Dirección', 'Addess'],
    ['Teléfono', 'Phone'],
]);
$grade = $_POST['grade'];

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
        $this->Cell(0, 5, utf8_encode($lang->translation("Información por grado")) . " $year " . $lang->translation("Grado ") . $grupo, 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
//        $this->Cell(10, 5, $lang->translation("Grado ").$grupo, 0, 1);
        $this->Cell(10, 5, '', 1, 0, 'C', true);
        $this->Cell(70, 5, $lang->translation("Nombre del estudiante"), 1, 0, 'C', true);
        $this->Cell(55, 5, $lang->translation("Padres"), 1, 0, 'C', true);
        $this->Cell(22, 5, utf8_encode($lang->translation("Celular")), 1, 0, 'C', true);
        $this->Cell(40, 5, utf8_encode($lang->translation("Ocupación")), 1, 0, 'C', true);
        $this->Cell(55, 5, utf8_encode($lang->translation("Dirección")), 1, 0, 'C', true);
        $this->Cell(28, 5, utf8_encode($lang->translation("Teléfono")), 1, 1, 'C', true);
        $this->SetFont('Arial', '', 10);
    }
}
$pdf = new nPDF();
$pdf->SetTitle(utf8_encode($lang->translation("Información por grado")) . " $year", true);
$pdf->Fill();

$unegrade = $_POST['grade'];

if ($unegrade !=='')
   {
   $allGrades = [$unegrade];
   }
else
   {
   $allGrades = $school->allGrades();
   }
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
           $pdf->AddPage('L');
           $pdf->SetFont('Arial', '', 10);
           }

         $pdf->Cell(10, 5, $count, 0, 0, 'C');
        $pdf->Cell(70, 5, $student->apellidos . ' ' . $student->nombre);
         $pdf->Cell(55, 5, $parent->madre);
         $pdf->Cell(22, 5, $parent->cel_m);
         $pdf->Cell(40, 5, $parent->posicion_m);
         $pdf->Cell(55, 5, $parent->dir1);
         $pdf->Cell(28, 5, $parent->tel_t_m, 0, 1, 'L');

         $pdf->Cell(10, 5, '', 0, 0, 'C');
         $pdf->Cell(70, 5, '');
         $pdf->Cell(55, 5, $parent->padre);
         $pdf->Cell(22, 5, $parent->cel_p);
         $pdf->Cell(40, 5, $parent->posicion_p);
         $pdf->Cell(55, 5, $parent->dir3);
         $pdf->Cell(28, 5, $parent->tel_t_p, 0, 1, 'L');

         $pdf->Cell(10, 5, '', 0, 0, 'C');
         $pdf->Cell(70, 5, '');
         $pdf->Cell(55, 5, '');
         $pdf->Cell(22, 5, '');
         $pdf->Cell(40, 5, '');
         $pdf->Cell(55, 5, $parent->pueblo1.' '.$parent->est1.' '.$parent->zip1);
         $pdf->Cell(28, 5, '', 0, 1, 'L');
         $count++;
    }
}

$pdf->Output();
