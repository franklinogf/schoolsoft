<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
//use Classes\Controllers\Teacher;
use Classes\Controllers\Parents;
use Classes\Util;

Session::is_logged();

$lang = new Lang([
    ['Información Usuario', 'User Information'],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["Nombre del estudiante", "Student name"],
    ['Cuenta', 'Account'],
    ['Usuario: ', 'User: '],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Total de estudiantes', 'Total students'],
    ['Fecha', 'Date'],
    ['Clave: ', 'Password: '],
    ['Masculinos', 'Males'],
    ['Femeninas', 'Females'],

]);

$school = new School();
//$teacherClass = new Teacher();
$studentClass = new Student();
$parentsClass = new Parents();

$year = $school->info('year');

$unegrade = $_POST['grade'];

if ($unegrade !=='')
   {
   $allGrades = [$unegrade];
   }
else
   {
   $allGrades = $school->allGrades();
   }


class nPDF extends PDF
{
    function header()
    {
        global $lang;
        global $year;
        global $grupo;
        parent::header();
		$this->InFooter = true;
    }
function Footer1()
{
	$this->InFooter = true;

}
}


$link = $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI];
$escaped_link = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');
list($l1, $l2, $l3, $l4) = explode("/",$escaped_link);
$link = "https://www.schoolsoftpr.org/".$l2;

$pdf = new nPDF();
$pdf->useHeader(false);
$pdf->useFooter(false);
$pdf->Footer1(true);

$count=0;
foreach ($allGrades as $grade) {
//    $teacher = $teacherClass->findByGrade($grade);
    $students = $studentClass->findByGrade($grade);

    $pdf->AddPage();
    $col=1;$wo2=5;
    $count=0;
    foreach ($students as $count => $student) {
      $count=$count+1;
      if ($count==31)
         {
         $pdf->AddPage();
         $count=1;
         $col=1;$wo2=5;
         }

    $parents = DB::table('madre')->where([
        ['id', $student->id],
    ])->orderBy('id')->first();
      $pdf->SetY($wo2);
      $pdf->SetFont('Arial', '', 10);
      if ($col==1){$pdf->SetX(15);}
      if ($col==2){$pdf->SetX(80);}
      if ($col==3){$pdf->SetX(145);}
      $pdf->Cell(60, 5, $lang->translation("Información Usuario"), 'LRT',1,'C');
      if ($col==1){$pdf->SetX(15);}
      if ($col==2){$pdf->SetX(80);}
      if ($col==3){$pdf->SetX(145);}
      $pdf->SetFont('Arial', '', 8);
      $pdf->Cell(60, 5, utf8_decode($student->nombre.' '.$student->apellidos), 'RL', 1);
      $pdf->SetFont('Arial', '', 10);
      if ($col==1){$pdf->SetX(15);}
      if ($col==2){$pdf->SetX(80);}
      if ($col==3){$pdf->SetX(145);}
      $pdf->Cell(60, 5, $lang->translation("Usuario: ").$parents->usuario, 'RL', 1);
      if ($col==1){$pdf->SetX(15);}
      if ($col==2){$pdf->SetX(80);}
      if ($col==3){$pdf->SetX(145);}
      $pdf->Cell(60, 5, $lang->translation("Clave: ").$parents->clave, 'RLB', 1);
      if ($col==1){$pdf->SetX(15);}
      if ($col==2){$pdf->SetX(80);}
      if ($col==3){$pdf->SetX(145);}
      $pdf->SetFont('Arial', '', 9);
      $pdf->Cell(60, 5, 'link: '.$link, 'RLB', 1);

      $pdf->SetY($wo2);
      if ($col==3){$col=0;$wo2=$wo2+29;}
      $col=$col+1;
    }
}

$pdf->Output();
