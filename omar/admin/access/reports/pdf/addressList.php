<?php
require_once '../../../../app.php';

use Classes\Controllers\Parents;
use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

$lang = new Lang([
    ['Lista de dirección postal', 'Postal address List'],
    ['Nombre Estudiante', 'Student name'],
    ['Dirección', 'Address'],
    ['Trabajos', 'Works'],
    ['Padres', 'Fathers'],
    ['Madres', 'Mothers'],
]);
$grade = $_POST['grade'];

$school = new School();
$year = $school->info('year');
$school = new School();
$year = $school->info('year');
$grupo = '';
class nPDF extends PDF
{
    function header()
    {
        global $lang;
        global $year;
        global $grupo;
        parent::header();
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 5, $lang->translation("Lista de dirección postal") . " $year / ".$grupo, 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(10, 5, '', 1, 0, 'C', true);
        $this->Cell(15, 5, 'ID', 1, 0, 'C', true);
        $this->Cell(70, 5, $lang->translation("Nombre Estudiante"), 1, 0, 'C', true);
        $this->Cell(90, 5, $lang->translation("Dirección"), 1, 1, 'C', true);
        $this->SetFont('Arial', '', 10);
    }
}
$pdf = new nPDF();
$pdf->SetTitle($lang->translation("Lista de dirección postal") . " $year / ".$grupo, true);
$pdf->Fill();

$grade = $_POST['grade'];
$est = $_POST['est'];
if ($grade=='')
   {
$students = DB::table('year')->where([
    ['activo', ''],
    ['year', $year]
])->orderBy('grado, apellidos')->get();
   }
else
   {
$students = DB::table('year')->where([
    ['activo', ''],
    ['year', $year],
    ['grado', $grade]
])->orderBy('apellidos')->get();
   }

$count = 1;
foreach ($students as $student) {

$parent = DB::table('madre')->where([
    ['id', $student->id],
])->orderBy('id')->first();
        if ($student->nuevo == 'Si' and $est =='N')
           {
           if ($grupo != $student->grado)
              {
              $grupo = $student->grado;
              $pdf->AddPage();
              $pdf->SetFont('Arial', '', 10);
              }
           $pdf->Cell(10, 5, $count, 0, 0, 'C');
           $pdf->Cell(15, 5, $student->id, 0, 0, 'C');
           $pdf->Cell(70, 5, utf8_decode($student->apellidos.' '.$student->nombre), 0, 0, 'L');
           $pdf->Cell(70, 5, utf8_decode($parent->dir1), 0, 1, 'L');
           if ($parent->dir3 !='')
              {
              $pdf->Cell(95, 5, '', 0, 0, 'C');
              $pdf->Cell(70, 5, utf8_decode($parent->dir3), 0, 1, 'L');
              }
           $pdf->Cell(95, 5, '', 0, 0, 'C');
           $pdf->Cell(70, 5, utf8_decode($parent->pueblo1).' '.$parent->est1.' '.$parent->zip1, 0, 1, 'L');
           $count++;
           }
        if ($est =='T')
           {
           if ($grupo != $student->grado)
              {
              $grupo = $student->grado;
              $pdf->AddPage();
              $pdf->SetFont('Arial', '', 10);
              }
           $pdf->Cell(10, 5, $count, 0, 0, 'C');
           $pdf->Cell(15, 5, $student->id, 0, 0, 'C');
           $pdf->Cell(70, 5, utf8_decode($student->apellidos.' '.$student->nombre), 0, 0, 'L');
           $pdf->Cell(70, 5, utf8_decode($parent->dir1), 0, 1, 'L');
           if ($parent->dir3 !='')
              {
              $pdf->Cell(95, 5, '', 0, 0, 'C');
              $pdf->Cell(70, 5, utf8_decode($parent->dir3), 0, 1, 'L');
              }
           $pdf->Cell(95, 5, '', 0, 0, 'C');
           $pdf->Cell(70, 5, utf8_decode($parent->pueblo1).' '.$parent->est1.' '.$parent->zip1, 0, 1, 'L');
           $count++;
           }
           
}


$pdf->Output();
