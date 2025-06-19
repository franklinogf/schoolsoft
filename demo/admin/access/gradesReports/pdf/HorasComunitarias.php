<?php
// 
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
    ['Horas comunitarias', 'Communitary hours'],
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
    ['T-1', 'Q-1'],
    ['T-2', 'Q-2'],
    ['T-3', 'Q-3'],
    ['A partir de', 'As of'],

]);

$school = new School();
$teacherClass = new Teacher();
$studentClass = new Student();
$tda = $_POST['asis'] ?? '';
$grado = $_POST['grade'] ?? '';
$year = $school->info('year2');
$fa = [];

$gd1 = array("", "12", "11", "10", "09");
$gd2 = array('','COSV12','COSV11','COSV10','COSV09');
$gd3 = array('','Seniors ','Juniors ','Sophomores ','Freshmen ');




$allGrades = $school->allGrades();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Horas comunitarias") . " $year", true);
$pdf->Fill();

for ($i = 1; $i <= 4; $i++) 
    {
    $pdf->AddPage();
    $pdf->SetFont('Times','',11);
    $grade = $gd1[$i];
    $gra = 'LIKE %'.$grade.'%';
    $students = DB::table('year')->where([
             ['year', $year],
             ['grado', 'LIKE', '%'.$grade.'%']
            ])->orderBy('apellidos')->get();


//foreach ($allGrades as $grade) {
//    $teacher = $teacherClass->findByGrade($grade);
//    $students = $studentClass->findByGrade($grade);
    $genderCount = ['M' => 0, 'F' => 0, 'T' => 0];
//    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Horas comunitarias") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $nom = $teacher->nombre ?? '';
    $ape = $teacher->apellidos ?? '';
    $pdf->splitCells($lang->translation("Maestro(a):") . " $nom $ape", $lang->translation("Grado:") . " $grade");

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(10, 5, '', 1, 0, 'C', true);
    $pdf->Cell(53, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
    $pdf->Cell(45, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
    $pdf->Cell(10, 5, '09', 1, 0, 'C', true);
    $pdf->Cell(10, 5, '10', 1, 0, 'C', true);
    $pdf->Cell(10, 5, '11', 1, 0, 'C', true);
    $pdf->Cell(10, 5, '12', 1, 0, 'C', true);
    $pdf->Cell(10, 5, 'Total', 1, 0, 'C', true);
    $pdf->Cell(34, 5, $lang->translation("A partir de").' '.date('m/d/Y'), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);
    $hrs1=0;$hrs2=0;$hrs3=0;$hrs4=0;
    $a=0;
    foreach ($students as $student) {
            $a=$a+1;
            $pdf->Cell(10,5,$a,1,0,'R');
            $pdf->Cell(53,5,$student->apellidos,1,0);
            $pdf->Cell(45,5,$student->nombre,1,0);
            $pdf->Cell(10,5,$hrs1,1,0,'C');
            $pdf->Cell(10,5,$hrs2,1,0,'C');
            $pdf->Cell(10,5,$hrs3,1,0,'C');
            $pdf->Cell(10,5,$hrs4,1,0,'C');
            $pdf->Cell(10,5,$hrs1+$hrs2+$hrs3+$hrs4,1,0,'R');
            $pdf->Cell(34,5,100-($hrs1+$hrs2+$hrs3+$hrs4),1,1,'R');

//        list($ss1, $ss2, $ss3) = explode("-", $student->ss);
//           foreach ($asistencia as $asis) 
//                   {
//                   }
           }

//    }
    $pdf->Ln(2);
    $pdf->Cell(40, 5, $lang->translation("Total de estudiantes"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $genderCount['T'], 1, 1, 'C');
    $pdf->Cell(40, 5, $lang->translation("Masculinos"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $genderCount['M'], 1, 1, 'C');
    $pdf->Cell(40, 5, $lang->translation("Femeninas"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $genderCount['F'], 1, 1, 'C');
}




$pdf->Output();
