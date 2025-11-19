<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();

$lang = new Lang([
    ['Asistencia semanal', 'Weekly attendance'],
    ["Profesor", "Teacher:"],
    ["Grado:", "Grade:"],
    ['Apellidos', 'Lasname'],
    ['Nombre', 'Name'],
    ['Curso', 'Course'],
    ['Trimestre', 'Quarter'],
    ['Promedio', 'Average'],
    ['Nota B', 'Note B'],
    ['Nota C', 'Note C'],
    ['Nota D', 'Note D'],
    ['Nota F', 'Note F'],
    ['Otros', 'Other'],
    ['Total', 'Total'],
    ['P-C', 'QZ'],
    ['TPA', 'TAP'],
    ['PROMEDIO:', 'AVERAGE:'],
    ['Nombre:', 'Name:'],
    ['Trabajos Diarios', 'Daily Homework'],
    ['Trabajos Libreta', 'Homework'],
    ['Fecha', 'Date'],
    ['Tema', 'Topic'],
    ['Valor', 'Value'],
    ['Pruebas Cortas', 'Quiz'],
    ['Lun', 'Mon'],
    ['Mar', 'Tue'],
    ['Mie', 'Wed'],
    ['Jue', 'Thu'],
    ['Vie', 'Fri'],
    ['A', 'A'],
    ['T', 'L'],

]);

$pdf = new PDF();

$school = new School(Session::id());
$grado = $_POST['grade'] ?? '';
$nota1 = $_POST['nota'] ?? '-';
$divicion = $_POST['divicion'] ?? '';
list($nota, $tt) = explode("-", $nota1);

$cl = $_POST['cl'] ?? '';
$notar = $_POST['notar'] ?? '';

$year = $school->info('year2');
//$pdf = new nPDF();
$pdf = new PDF();

$allGrades = $school->allGrades();

foreach ($allGrades as $grade) 
        {
        $pdf->AddPage('');
        $pdf->Cell(0, 5, $lang->translation("Asistencia semanal").' / '.$grade." / $tt / $year", 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Fill();
        $pdf->Cell(10, 10, '', 1, 0, 'C', true);
        $pdf->Cell(55, 10, $lang->translation("Apellidos"), 1, 0, 'C', true);
        $pdf->Cell(45, 10, $lang->translation("Nombre"), 1, 0, 'C', true);
        $pdf->Cell(16, 5, $lang->translation("Lun"), 1, 0, 'C', true);
        $pdf->Cell(16, 5, $lang->translation("Mar"), 1, 0, 'C', true);
        $pdf->Cell(16, 5, $lang->translation("Mie"), 1, 0, 'C', true);
        $pdf->Cell(16, 5, $lang->translation("Jue"), 1, 0, 'C', true);
        $pdf->Cell(16, 5, $lang->translation("Vie"), 1, 1, 'C', true);
        $pdf->Cell(10, 5, '', 0, 0, 'C');
        $pdf->Cell(55, 5, '', 0, 0, 'C');
        $pdf->Cell(45, 5, '', 0, 0, 'C');
        $pdf->Cell(8, 5, $lang->translation("A"), 1, 0, 'C', true);
        $pdf->Cell(8, 5, $lang->translation("T"), 1, 0, 'C', true);
        $pdf->Cell(8, 5, $lang->translation("A"), 1, 0, 'C', true);
        $pdf->Cell(8, 5, $lang->translation("T"), 1, 0, 'C', true);
        $pdf->Cell(8, 5, $lang->translation("A"), 1, 0, 'C', true);
        $pdf->Cell(8, 5, $lang->translation("T"), 1, 0, 'C', true);
        $pdf->Cell(8, 5, $lang->translation("A"), 1, 0, 'C', true);
        $pdf->Cell(8, 5, $lang->translation("T"), 1, 0, 'C', true);
        $pdf->Cell(8, 5, $lang->translation("A"), 1, 0, 'C', true);
        $pdf->Cell(8, 5, $lang->translation("T"), 1, 1, 'C', true);



        $n = 0;
        $materias = [];
        $curs = [];
        $estudiantes = [];
        $c = 0;
//**********************


         $students = DB::table('year')->where([
                  ['year', $year],
                  ['codigobaja', 0],
                  ['grado', $grade],
                  ])->orderBy('Apellidos','desc')->get();
        $t=0;
        foreach ($students as $student) 
                {
                $t=$t+1;
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(10, 5, $t, 1, 0, 'R');
                $pdf->Cell(55, 5, $student->apellidos, 1, 0, 'L');
                $pdf->Cell(45, 5, $student->nombre, 1, 0, 'L');
                $pdf->SetFont('Arial', '', 9);
                $a=0;$b=0;
                $pdf->Cell(8, 5, '', 1, 0, 'R');
                $pdf->Cell(8, 5, '', 1, 0, 'R');
                $pdf->Cell(8, 5, '', 1, 0, 'R');
                $pdf->Cell(8, 5, '', 1, 0, 'R');
                $pdf->Cell(8, 5, '', 1, 0, 'R');
                $pdf->Cell(8, 5, '', 1, 0, 'R');
                $pdf->Cell(8, 5, '', 1, 0, 'R');
                $pdf->Cell(8, 5, '', 1, 0, 'R');
                $pdf->Cell(8, 5, '', 1, 0, 'R');
                $pdf->Cell(8, 5, '', 1, 1, 'R');
                }



//**********************
         }


$pdf->Output();