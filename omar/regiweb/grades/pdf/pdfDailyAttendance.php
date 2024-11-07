<?php
require_once '../../../app.php';

use Classes\Controllers\Student;
use Classes\PDF;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Teacher;
use Classes\Lang;
use Classes\Util;

Session::is_logged();

$teacher = new Teacher(Session::id());
$year = $teacher->info('year');

$_date1 = $_POST['date1'];
$_date2 = $_POST['date2'];
$_type = $_POST['type'];
$_option = $_POST['option'];
$_grade = $teacher->grado;


// $codigos = [
//     // ausencias
//     "1" => 'Situación en el hogar',
//     "2" => 'Determinación del hogar (viaje)',
//     "3" => 'Actividad con padres (open house)',
//     "4" => 'Enfermedad',
//     "5" => 'Cita',
//     "6" => 'Actividad educativa del colegio',
//     "7" => 'Sin excusa del hogar',
//     // tardanzas
//     "8" => 'Sin excusa del hogar',
//     "9" => 'Situación en el hogar',
//     "10" => 'Problema en la transportación',
//     "11" => 'Enfermedad',
//     "12" => 'Cita'
// ];
$lang = new Lang([
    ["Informe de asistencias diarias", "Daily attendance report"],
    ["Informe de asistencias diarias en lista", "Daily attendance report list"],
    ["Informe de asistencias diarias en resumen", "Daily attendance report summary"],
    ["Desde:", "From:"],
    ["Hasta:", "To:"],
    ['Apellidos', 'Surnames'],
    ["Nombre", "Name"],
    ["Fecha", "Date"],
    ["Asistencia", "Attendance"],
    ["Ausencias", "Absence"],
    ["Tardanzas", "Tardy"],
    ["Grado", "Grade"]
]);



$pdf = new PDF();
$pdf->footer = false;
$pdf->SetLeftMargin(5);
$pdf->AddPage();
$pdf->SetTitle($lang->translation('Informe de asistencias diarias'));
$pdf->Fill();

$pdf->SetFont('Arial', 'B', 12);
if ($_option === 'home') {
    if ($_type === 'list') {
        $pdf->Cell(0, 10, $lang->translation("Informe de asistencias diarias en lista") . " $year", 0, 1, 'C');
    } else {
        $pdf->Cell(0, 10, $lang->translation("Informe de asistencias diarias en resumen") . " $year", 0, 1, 'C');
    }
} else {
    $pdf->Cell(0, 10, $lang->translation("Informe de asistencias diarias") . " $year", 0, 1, 'C');
}
$pdf->Cell(0, 10, $lang->translation("Desde:") . " " . Util::formatDate($_date1) . ' / ' . $lang->translation("Hasta:") . ' ' . Util::formatDate($_date2), 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 10);
$count = 1;
if ($_option === 'home') {
    $studentsSS = DB::table('asispp')
        ->select('DISTINCT ss')
        ->where([
            ['grado', $_grade],
            ['year', $year],
            ['fecha', '>=', $_date1],
            ['fecha', '<=', $_date2],
            ['codigo', '>', 0]
        ])->orderBy('apellidos, nombre, fecha')->get();
    if ($_type === 'resum')
        $pdf->Cell(15);
    $pdf->Cell(10, 5, '', 'LTB', 0, 'C', true);
    $pdf->Cell(50, 5, $lang->translation("Apellidos"), 'RTB', 0, 'C', true);
    $pdf->Cell(50, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
    if ($_type === 'list') {
        $pdf->Cell(25, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
        $pdf->Cell(60, 5, $lang->translation("Asistencia"), 1, 1, 'C', true);
    } else {
        $pdf->Cell(30, 5, $lang->translation("Ausencias"), 1, 0, 'C', true);
        $pdf->Cell(30, 5, $lang->translation("Tardanzas"), 1, 1, 'C', true);
    }

    $pdf->SetFont('Arial', '', 10);
    foreach ($studentsSS as $studentss) {
        $students = DB::table('asispp')
            ->where([
                ['grado', $_grade],
                ['year', $year],
                ['fecha', '>=', $_date1],
                ['fecha', '<=', $_date2],
                ['codigo', '>', 0],
                ['ss', $studentss->ss]
            ])->orderBy('apellidos, nombre, fecha')->get();

        if ($_type == 'list') {
            foreach ($students as $student) {
                $pdf->Cell(10, 5, $count, 1, 0, 'C');
                $pdf->Cell(50, 5, $student->apellidos, 1);
                $pdf->Cell(50, 5, $student->nombre, 1);
                $pdf->Cell(25, 5, $student->fecha, 1, 0, 'C');
                $pdf->Cell(60, 5, Util::$attendanceCodes[$student->codigo]['description'][__LANG], 1, 1);
                $count++;
            }
        } else {
            $aus = 0;
            $tar = 0;
            foreach ($students as $student) {
                $aus += $student->codigo <= 7 ? 1 : 0;
                $tar += $student->codigo >= 8 ? 1 : 0;
                $fileName = $student->nombre;
                $lastName = $student->apellidos;
            }
            $pdf->Cell(15);
            $pdf->Cell(10, 5, $count, 1, 0, 'C');
            $pdf->Cell(50, 5, $lastName, 1);
            $pdf->Cell(50, 5, $fileName, 1);
            $pdf->Cell(30, 5, $aus, 1, 0, 'C');
            $pdf->Cell(30, 5, $tar, 1, 1, 'C');
            $count++;
        }
    }
} else {
    $ss = $_POST['ss'];
    $asis = DB::table('asispp')
        ->where([
            ['grado', $_grade],
            ['year', $year],
            ['fecha', '>=', $_date1],
            ['fecha', '<=', $_date2],
            ['codigo', '>', 0],
            ['ss', $ss]
        ])->orderBy('fecha')->get();
    $pdf->splitCells($lang->translation("Nombre") . utf8_encode(": {$asis[0]->apellidos}, {$asis[0]->nombre}"), $lang->translation("Grado") . ": {$asis[0]->grado}");
    $pdf->Ln(10);
    $pdf->Cell(40);
    $pdf->Cell(10, 5, '', 'LTB', 0, 'C', true);
    $pdf->Cell(35, 5, $lang->translation("Fecha"), 'RTB', 0, 'C', true);
    $pdf->Cell(70, 5, $lang->translation("Asistencia"), 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 10);
    foreach ($asis as $asi) {
        $pdf->Cell(40);
        $pdf->Cell(10, 5, $count, 1, 0, 'C');
        $pdf->Cell(35, 5, $asi->fecha, 1, 0, 'C');
        $pdf->Cell(70, 5, Util::$attendanceCodes[$asi->codigo]['description'][__LANG], 1, 1);
        $count++;
    }
}
$pdf->Output();
