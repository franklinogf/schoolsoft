<?php
require_once '../../../../app.php';

use Classes\Controllers\School;
use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\Student;
use Classes\DataBase\DB;
use Classes\Util;

Session::is_logged();

$lang = new Lang([
    ['Informe de acceso de los padres', 'Parents Access Report'],
    ['Desde', 'From'],
    ['Hasta', 'To'],
    ['Nombre', 'Name'],
    ['Apellidos', 'Surnames'],
    ['Grado', 'Grade'],
    ['Fecha', 'Date'],
    ['Hora', 'Hour'],
    ['IP', 'IP'],
    ['Nombre', 'Name'],
]);

$school = new School(Session::id());
$year = $school->year('year2');


$from = $_POST['from'];
$to = $_POST['to'];
$option = $_POST['option'];

$pdf = new PDF();
$pdf->SetTitle($lang->translation("Informe de acceso de los padres") . " $year", true);
$pdf->Fill();
if ($option === 'student') {
    $studentMt = $_POST['student'];
    $student = new Student($studentMt);

    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Informe de acceso de los padres") . " $year", 0, 1, 'C');
    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 5, $lang->translation("Desde") . ": $from / " . $lang->translation("Hasta") . ": $to", 0, 1, 'C');

    $pdf->Ln(5);
    $pdf->splitCells($lang->translation("Nombre") . ": " . utf8_encode($student->fullName()), $lang->translation("Grado") . ": $student->grado");
    $pdf->SetFont('Arial', 'B', 10);

    $pdf->Cell(20, 5, '', 1, 0, 'C', true);
    $pdf->Cell(20, 5, 'ID', 1, 0, 'C', true);
    $pdf->Cell(50, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
    $pdf->Cell(50, 5, $lang->translation("Hora"), 1, 0, 'C', true);
    $pdf->Cell(50, 5, $lang->translation("IP"), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);

    $attendances = DB::table('entradas')->where([
        ['id', $student->id],
        ['fecha', '>=', $from],
        ['fecha', '<=', $to]
    ])->orderBy('fecha')->get();

    foreach ($attendances as $count => $attendance) {
        $pdf->Cell(20, 5, $count + 1, 1, 0, 'C');
        $pdf->Cell(20, 5, $attendance->id, 1, 0, 'C');
        $pdf->Cell(50, 5, $attendance->fecha, 1, 0, 'C');
        $pdf->Cell(50, 5, $attendance->hora, 1, 0, 'C');
        $pdf->Cell(50, 5, $attendance->ip, 1, 1, 'C');
    }
} else {
    $school = new School(Session::id());
    $year = $school->year('year2');
    $grade = $_POST['grade'];
    $type = $_POST['type'] ?? 'list';
    $grades = $grade !== '' ? [$grade] : $school->allGrades();
        foreach ($grades as $grade) {
        $students = DB::table('year')->where([
            ['grado', $grade],
            ['year', $year],
        ])->orderBy("grado, apellidos, nombre")->get();
        
            $count = 1;
            $type = 'list';
                foreach ($students as $stud) {
                $pdf->addPage();
                $pdf->SetFont('Arial', 'B', 15);
                $pdf->Cell(0, 5, $lang->translation("Informe de acceso de los padres") . " $year", 0, 1, 'C');
                $pdf->SetFont('Arial', '', 12);
                $pdf->Ln(10);
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(0, 5, $lang->translation("Desde") . ": $from / " . $lang->translation("Hasta") . ": $to", 0, 1, 'C');
                $pdf->Ln(5);

                $pdf->Cell(20, 5, $lang->translation("Nombre"), 1, 0, 'L', true);
                $pdf->Cell(135, 5, $stud->apellidos.' '.$stud->nombre, 0, 0, 'L');
                $pdf->Cell(20, 5, $lang->translation("Grado"), 1, 0, 'C', true);
                $pdf->Cell(20, 5, $stud->grado, 0, 1, 'L');
                $pdf->Ln(5);

                $pdf->Cell(20, 5, '', 1, 0, 'C', true);
                $pdf->Cell(20, 5, 'ID', 1, 0, 'C', true);
                $pdf->Cell(50, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
                $pdf->Cell(50, 5, $lang->translation("Hora"), 1, 0, 'C', true);
                $pdf->Cell(50, 5, $lang->translation("IP"), 1, 1, 'C', true);
                $pdf->SetFont('Arial', '', 12);
                $attendances = DB::table('entradas')->where([
                  ['id', $stud->id],
                  ['fecha', '>=', $from],
                  ['fecha', '<=', $to]
               ])->orderBy('fecha')->get();
               foreach ($attendances as $attendance) 
                       {
                       $pdf->Cell(20, 5, $count, 1, 0, 'C');
                       $pdf->Cell(20, 5, $attendance->id, 1);
                       $pdf->Cell(50, 5, $attendance->fecha, 1);
                       $pdf->Cell(50, 5, $attendance->hora, 1, 0, 'C');
                       $pdf->Cell(50, 5, $attendance->ip, 1, 1, 'C');
                       $count++;
                       }
               }
      }
  }

$pdf->Output();
