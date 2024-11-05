<?php
require_once '../../../../app.php';

use Classes\Controllers\School;
use Classes\PDF;
use Classes\Lang;
use Classes\Session;
//use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\DataBase\DB;
use Classes\Util;

Session::is_logged();

$lang = new Lang([
    ['Informe de acceso de los profesores', 'Teacher Access Report'],
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

$from = $_POST['from'];
$to = $_POST['to'];
$school = new School(Session::id());
$year = $school->info('year2');

$pdf = new PDF();
$pdf->SetTitle($lang->translation("Informe de acceso de los profesores") . " $year", true);
$pdf->Fill();
$studentMt = $_POST['student'];
if ($studentMt != 'Todos') {
    $studentMt = $_POST['student'];
    $student = new Teacher($studentMt);
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Informe de acceso de los profesores") . " $year", 0, 1, 'C');
    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 5, $lang->translation("Desde") . ": $from / " . $lang->translation("Hasta") . ": $to", 0, 1, 'C');

    $pdf->Ln(5);
    $pdf->splitCells($lang->translation("Nombre") . ": " . utf8_encode($student->nombre . ' ' . $student->apellidos), $lang->translation("Grado") . ": $student->grado");
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
    $teachers = DB::table('profesor')->where([
        ['baja', ''],
        ['docente', 'Docente']
    ])->orderBy('apellidos')->get();
        
            $count = 1;
            $type = 'list';
                foreach ($teachers as $stud) {
        $attendances = DB::table('entradas')->where([
            ['id', $stud->id],
            ['fecha', '>=', $from],
            ['fecha', '<=', $to]
        ])->orderBy('fecha')->get();
        if (count($attendances) > 0) {
                $pdf->addPage();
                $pdf->SetFont('Arial', 'B', 15);
                $pdf->Cell(0, 5, $lang->translation("Informe de acceso de los profesores") . " $year", 0, 1, 'C');
                $pdf->SetFont('Arial', '', 12);
            $pdf->Ln(5);
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(0, 5, $lang->translation("Desde") . ": $from / " . $lang->translation("Hasta") . ": $to", 0, 1, 'C');
                $pdf->Ln(5);

                $pdf->Cell(20, 5, $lang->translation("Nombre"), 1, 0, 'L', true);
                $pdf->Cell(135, 5, $stud->apellidos.' '.$stud->nombre, 0, 0, 'L');
                $pdf->Cell(20, 5, $lang->translation("Grado"), 1, 0, 'C', true);
            $pdf->Cell(20, 5, $stud->grado, 0, 1, 'L');

                $pdf->Cell(20, 5, '', 1, 0, 'C', true);
                $pdf->Cell(20, 5, 'ID', 1, 0, 'C', true);
                $pdf->Cell(50, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
                $pdf->Cell(50, 5, $lang->translation("Hora"), 1, 0, 'C', true);
                $pdf->Cell(50, 5, $lang->translation("IP"), 1, 1, 'C', true);
            $pdf->SetFont('Arial', '', 12);
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
