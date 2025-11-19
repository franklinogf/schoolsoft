<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\Controllers\Parents;
use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();

$lang = new Lang([
    ['Carta Certificada', 'Registered letter'],
    ['Nombre del estudiante', 'Student name'],
    ['Grado', 'Grade'],
    ['Fecha', 'Date'],
    ['El ', 'The '],
    [', certifica que el/la/los/las', ', certifies that he/she/they'],
    ['estudiante(s) ha sido estudiante(s) regular(es) de', 'student(s) has been a regular student(s) of'],
    ['nuestro Colegio durante el curso escolar 20', 'our School during the school year 20'],
    ['Cualquier información favor de comunicarse a nuestras', 'Any information please contact our'],
    ['Oficinas de Finanzas.', 'Finance Offices.'],
]);
$grade = $_POST['grade'];
$opcion = $_POST['option'];
$id = $_POST['student'];
$school = new School(Session::id());
$year = $school->info('year2');

$teacherClass = new Teacher();
$studentClass = new Student();

$colegio = $school->info('colegio');
list($y1,$y2) = explode("-",$year);

$unegrade = $_POST['grade'];
if ($opcion == 'grade')
   {
   if ($unegrade !==''){$allGrades = [$unegrade];}
   else
      {$allGrades = $school->allGrades();}
   $pdf = new PDF();
   $pdf->SetTitle($lang->translation("Carta Certificada") . " $year", true);
   $pdf->Fill();
   $pdf->useFooter(false);

   foreach ($allGrades as $grade) {
       $teacher = $teacherClass->findByGrade($grade);
       $students = $studentClass->findByGrade($grade);
       foreach ($students as $student) {
       $students2 = DB::table('year')->where([
        ['activo', ''],
        ['year', $year],
        ['id', $student->id]
        ])->orderBy('apellidos')->get();
       $pdf->AddPage();
       $pdf->Ln(5);
       $pdf->SetFont('Arial', 'B', 15);
       $pdf->Cell(0, 5, $lang->translation("Carta Certificada") . " $year", 0, 1, 'C');
       $pdf->Ln(20);
       $pdf->SetFont('Arial', '', 12);
       $pdf->Cell(25, 7, '', 0, 0, 'C');
       $pdf->Cell(12, 5, $lang->translation("Fecha").':', 0, 0, 'L');
       $pdf->Cell(10, 5, date('m/d/Y'), 0, 1, 'L');
       $pdf->Ln(15);
       $pdf->Cell(35, 7, '', 0, 0, 'C');
       $pdf->Cell(150, 7, $lang->translation("El ").$colegio.$lang->translation(", certifica que el/la/los/las"), 0, 1, 'L');
       $pdf->Cell(25, 7, '', 0, 0, 'C');
       $pdf->Cell(150, 7, $lang->translation("estudiante(s) ha sido estudiante(s) regular(es) de"), 0, 1, 'L');
       $pdf->Cell(25, 7, '', 0, 0, 'C');
       $pdf->Cell(150, 7, $lang->translation("nuestro Colegio durante el curso escolar 20").$y1.'.', 0, 1, 'L');
       $pdf->Ln(15);
       $pdf->Cell(25, 5, '', 0, 0, 'C');
       $pdf->Cell(100, 5, $lang->translation("Nombre del estudiante"), 'B', 0, 'L');
       $pdf->Cell(30, 5, $lang->translation("Grado"), 'B', 1, 'L');
       $pdf->Ln(5);
       foreach ($students2 as $stud) {
           $pdf->Cell(25, 5, '', 0, 0, 'C');
                $pdf->Cell(100, 5, $stud->apellidos . ' ' . $stud->nombre);
           $pdf->Cell(30, 5, $stud->grado, 0, 1, 'L');
           }
       $pdf->Ln(10);
       $pdf->Cell(25, 7, '', 0, 0, 'C');
            $pdf->Cell(100, 7, utf8_encode($lang->translation("Cualquier información favor de comunicarse a nuestras")), 0, 1, 'L');
            $pdf->Cell(25, 7, 'ÑÑñ', 0, 0, 'C');
       $pdf->Cell(100, 7, $lang->translation("Oficinas de Finanzas."), 0, 1, 'L');
       }
     }
   }
else
   {
       $students2 = DB::table('year')->where([
        ['activo', ''],
        ['year', $year],
        ['id', $id]
        ])->orderBy('apellidos')->get();
       $pdf = new PDF();
       $pdf->SetTitle($lang->translation("Carta Certificada") . " $year", true);
       $pdf->Fill();
       $pdf->useFooter(false);
       $pdf->AddPage();
       $pdf->Ln(5);
       $pdf->SetFont('Arial', 'B', 15);
       $pdf->Cell(0, 5, $lang->translation("Carta Certificada") . " $year", 0, 1, 'C');
       $pdf->Ln(20);
       $pdf->SetFont('Arial', '', 12);
       $pdf->Cell(25, 7, '', 0, 0, 'C');
       $pdf->Cell(12, 5, $lang->translation("Fecha").':', 0, 0, 'L');
       $pdf->Cell(10, 5, date('m/d/Y'), 0, 1, 'L');
       $pdf->Ln(15);
       $pdf->Cell(35, 7, '', 0, 0, 'C');
       $pdf->Cell(150, 7, $lang->translation("El ").$colegio.$lang->translation(", certifica que el/la/los/las"), 0, 1, 'L');
       $pdf->Cell(25, 7, '', 0, 0, 'C');
       $pdf->Cell(150, 7, $lang->translation("estudiante(s) ha sido estudiante(s) regular(es) de"), 0, 1, 'L');
       $pdf->Cell(25, 7, '', 0, 0, 'C');
       $pdf->Cell(150, 7, $lang->translation("nuestro Colegio durante el curso escolar 20").$y1.'.', 0, 1, 'L');
       $pdf->Ln(15);
       $pdf->Cell(25, 5, '', 0, 0, 'C');
       $pdf->Cell(100, 5, $lang->translation("Nombre del estudiante"), 'B', 0, 'L');
       $pdf->Cell(30, 5, $lang->translation("Grado"), 'B', 1, 'L');
       $pdf->Ln(5);
       foreach ($students2 as $stud) {
           $pdf->Cell(25, 5, '', 0, 0, 'C');
        $pdf->Cell(100, 5, $stud->apellidos . ' ' . $stud->nombre);
           $pdf->Cell(30, 5, $stud->grado, 0, 1, 'L');
           }
       $pdf->Ln(10);
       $pdf->Cell(25, 7, '', 0, 0, 'C');
    $pdf->Cell(100, 7, utf8_encode($lang->translation("Cualquier información favor de comunicarse a nuestras")), 0, 1, 'L');
       $pdf->Cell(25, 7, '', 0, 0, 'C');
       $pdf->Cell(100, 7, $lang->translation("Oficinas de Finanzas."), 0, 1, 'L');
   }

$pdf->Output();
