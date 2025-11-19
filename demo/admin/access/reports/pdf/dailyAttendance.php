<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\Controllers\School;
use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\Student;
use Classes\DataBase\DB;
use Classes\Util;

Session::is_logged();

$lang = new Lang([
    ["Asistencia diaria", "Daily Attendance"],
    ['Desde', 'From'],
    ['Hasta', 'To'],
    ['Nombre', 'Name'],
    ['Apellidos', 'Surnames'],
    ['Grado', 'Grade'],
    ['Fecha', 'Date'],
    ['Asistencia', 'Attendance'],
    ['Ausencias', 'Absences'],
    ['Tardanzas', 'Tardy'],
]);

$from = $_POST['from'];
$to = $_POST['to'];
$option = $_POST['option'];

$pdf = new PDF();
$pdf->SetTitle($lang->translation("Asistencia diaria") . " $year", true);
$pdf->Fill();
if ($option === 'student') {
    $studentMt = $_POST['student'];
    $student = new Student($studentMt);
    $year = $student->info('year');

    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Asistencia diaria") . " $year", 0, 1, 'C');
    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 5, $lang->translation("Desde") . ": $from / " . $lang->translation("Hasta") . ": $to", 0, 1, 'C');

    $pdf->Ln(5);
    //    $pdf->splitCells($lang->translation("Nombre") . ": " . $student->fullName(), $lang->translation("Grado") . ": $student->grado");
    $pdf->splitCells($lang->translation("Nombre") . ": " . utf8_encode($student->apellidos . ' ' . $student->nombre), $lang->translation("Grado") . ": $student->grado");
    $pdf->SetFont('Arial', 'B', 10);

    $pdf->Cell(30);
    $pdf->Cell(10, 5, '', 1, 0, 'C', true);
    $pdf->Cell(30, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
    $pdf->Cell(90, 5, $lang->translation("Asistencia"), 1, 1, 'C', true);




    $pdf->SetFont('Arial', '', 10);

    $attendances = DB::table('asispp')->where([
        ['grado', $student->grado],
        ['ss', $student->ss],
        ['year', $year],
        ['fecha', '>=', $from],
        ['fecha', '<=', $to],
        ['codigo', '>', '0'],
    ])->orderBy('fecha')->get();

    foreach ($attendances as $count => $attendance) {
        $pdf->Cell(30);
        $pdf->Cell(10, 5, $count + 1, 1, 0, 'C');
        $pdf->Cell(30, 5, $attendance->fecha, 1, 0, 'C');
        $pdf->Cell(90, 5, "(" . Util::$attendanceCodes[$attendance->codigo]['type'] . ") " . Util::$attendanceCodes[$attendance->codigo]['description'][__LANG], 1, 1);
    }
} else {
    $school = new School();
    $year = $school->year();
    $grade = $_POST['grade'];
    $separatedGrade = $_POST['separatedGrade'] === 'si' ? true : false;
    $type = $_POST['type'];
    $grades = $grade !== '' ? [$grade] : $school->allGrades();
    if (!$separatedGrade) {
        $pdf->addPage('L');
        $pdf->SetFont('Arial', 'B', 15);
        $pdf->Cell(0, 5, $lang->translation("Asistencia diaria") . " $year", 0, 1, 'C');
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, $lang->translation("Desde") . ": $from / " . $lang->translation("Hasta") . ": $to", 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->Cell(10, 5, '', 1, 0, 'C', true);
        $pdf->Cell(65, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
        $pdf->Cell(65, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
        if ($type === 'list') {
            $pdf->Cell(30, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
            $pdf->Cell(90, 5, $lang->translation("Asistencia"), 1, 0, 'C', true);
            $pdf->Cell(15, 5, $lang->translation("Grado"), 1, 1, 'C', true);
        } else {
            $pdf->Cell(15, 5, $lang->translation("Grado"), 1, 0, 'C', true);
            $pdf->Cell(30, 5, $lang->translation("Ausencias"), 1, 0, 'C', true);
            $pdf->Cell(30, 5, $lang->translation("Tardanzas"), 1, 1, 'C', true);
        }
        $count = 1;
    }

    foreach ($grades as $grade) {
        $attendances = DB::table('asispp')->where([
            ['grado', $grade],
            ['year', $year],
            ['fecha', '>=', $from],
            ['fecha', '<=', $to],
            ['codigo', '>', '0'],
        ])->orderBy("grado, apellidos, nombre, fecha")->get();
        
        if (sizeof($attendances) > 0) {
            if ($separatedGrade) {
                
                $pdf->addPage('L');

                $pdf->SetFont('Arial', 'B', 15);
                $pdf->Cell(0, 5, $lang->translation("Asistencia diaria") . " $year", 0, 1, 'C');
                $pdf->Ln(2);
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(0, 5, $lang->translation("Desde") . ": $from / " . $lang->translation("Hasta") . ": $to", 0, 1, 'C');
                $pdf->Ln(5);

                $pdf->Cell(10, 5, '', 1, 0, 'C', true);
                $pdf->Cell(65, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
                $pdf->Cell(65, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
                if ($type === 'list') {
                    $pdf->Cell(30, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
                    $pdf->Cell(90, 5, $lang->translation("Asistencia"), 1, 0, 'C', true);
                    $pdf->Cell(15, 5, $lang->translation("Grado"), 1, 1, 'C', true);
                } else {
                    $pdf->Cell(15, 5, $lang->translation("Grado"), 1, 0, 'C', true);
                    $pdf->Cell(30, 5, $lang->translation("Ausencias"), 1, 0, 'C', true);
                    $pdf->Cell(30, 5, $lang->translation("Tardanzas"), 1, 1, 'C', true);
                }
                $count = 1;
            }
            $pdf->SetFont('Arial', '', 10);
            if ($type === 'list') {
                foreach ($attendances as $attendance) {
                    $student = new Student($attendance->ss);
                    $pdf->Cell(10, 5, $count, 1, 0, 'C');
                    $pdf->Cell(65, 5, $student->apellidos, 1);
                    $pdf->Cell(65, 5, $student->nombre, 1);
                    $pdf->Cell(30, 5, $attendance->fecha, 1, 0, 'C');
                    $pdf->Cell(90, 5, "(" . Util::$attendanceCodes[$attendance->codigo]['type'] . ") " . Util::$attendanceCodes[$attendance->codigo]['description'][__LANG], 1);
                    $pdf->Cell(15, 5, $attendance->grado, 1, 1, 'C');
                    $count++;
                }
            } else {
                $dailys = [];
                foreach ($attendances as $attendance) {
                    $dailys[$attendance->ss]['apellidos'] = $attendance->apellidos;
                    $dailys[$attendance->ss]['nombre'] = $attendance->nombre;
                    $dailys[$attendance->ss]['grado'] = $attendance->grado;
                    $dailys[$attendance->ss]['A'] += Util::$attendanceCodes[$attendance->codigo]['type'] === 'A' ? 1 : 0;
                    $dailys[$attendance->ss]['T'] += Util::$attendanceCodes[$attendance->codigo]['type'] === 'T' ? 1 : 0;
                }
                foreach ($dailys as $daily) {

                    $pdf->Cell(10, 5, $count, 1, 0, 'C');
                    $pdf->Cell(65, 5, $daily['apellidos'], 1);
                    $pdf->Cell(65, 5, $daily['nombre'], 1);
                    $pdf->Cell(15, 5, $daily['grado'], 1, 0, 'C');
                    $pdf->Cell(30, 5, $daily['A'], 1, 0, 'C');
                    $pdf->Cell(30, 5, $daily['T'], 1, 1, 'C');
                }
            }
        }
    }
}

$pdf->Output();
