<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;
use Classes\DataBase\DB;

Session::is_logged();

$lang = new Lang([
    ['Informe de documentos', 'Document report'],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["NO ENTREGADO", "UNDELIVERED"],
    ["ENTREGADO", "DELIVERED"],
    ['Cuenta', 'Account'],
    ['Genero', 'Gender'],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Total de estudiantes', 'Total students'],
    ['Fecha', 'Date'],
    ['Entregado Si / No', 'Delivered Yes / No'],
    ['Masculinos', 'Males'],
    ['Femeninas', 'Females'],

]);

$school = new School(Session::id());
$teacherClass = new Teacher();
$studentClass = new Student();

$year = $school->info('year2');
$allGrades = $school->allGrades();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Informe de documentos"). " $year", true);
$pdf->Fill();
$docs = $_POST['option'];
$doc = DB::table('docu_entregados')->where([
      ['codigo', $docs]
     ])->orderBy('codigo')->first();

foreach ($allGrades as $grade) {
    $teacher = $teacherClass->findByGrade($grade);
    $students = $studentClass->findByGrade($grade);
    $genderCount = ['M' => 0, 'F' => 0, 'T' => 0];
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Informe de documentos").' / '.$doc->desc1. " / $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->splitCells($lang->translation("Maestro(a):") . " $teacher->nombre $teacher->apellidos", $lang->translation("Grado:") . " $grade");

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 5, '', 1, 0, 'C', true);
    $pdf->Cell(19, 5, $lang->translation("Cuenta"), 1, 0, 'C', true);
    $pdf->Cell(55, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
    $pdf->Cell(50, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
    $pdf->Cell(56, 5, $lang->translation("Entregado Si / No"), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);

    foreach ($students as $count => $student) {
        $dia=date(j);
        $mes=date(n);
        $ano=date(Y);
        $fec=$student->fecha; 
        list($anonaz, $mesnaz, $dianaz) = explode('-', $fec);
        if (($mesnaz == $mes) && ($dianaz > $dia)) {$ano=($ano-1);}
        if ($mesnaz > $mes) {$ano=($ano-1);}
        $edad=$ano-$anonaz;
        if ($edad > 20){$edad='';}
        $gender = Util::gender($student->genero);
        $genderCount[$gender]++;
        $genderCount['T']++;

        $doc = DB::table('docu_estudiantes')->where([
          ['ss', $student->ss],
          ['codigo', $docs]
        ])->orderBy('codigo')->first();

        $pdf->Cell(10, 5, $count + 1, 1, 0, 'C');
        $pdf->Cell(19, 5, $student->id, 1, 0, 'C');
        $pdf->Cell(55, 5, $student->apellidos, 1);
        $pdf->Cell(50, 5, $student->nombre, 1, 0);
//        $pdf->Cell(56, 5, $edad, 1, 1, 'C');
		$pdf->SetFont('Times','B',10);						
        if ($doc->codigo == $docs)
           {
           $pdf->Cell(56,5,$lang->translation("ENTREGADO"),1,1,'C');
           }
        else
           {
           $pdf->Cell(56,5,$lang->translation("NO ENTREGADO"),1,1,'C');
           }
		$pdf->SetFont('Times','',10);

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
