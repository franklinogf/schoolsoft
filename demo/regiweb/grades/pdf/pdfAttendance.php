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
$lang = new Lang([
    ["Informe de asistencia", "Attendance report"],
    ["Año escolar", "School year"],
    ["Informe de estudiantes con problemas de tardanzas y ausentismo por horario de clase", "Report of students with tardiness and absenteeism problems by class time"],
    ["Nombre del estudiante", "Student name"],
    ["Horario", "Schedule"],
    ["Ausencias", "Absence"],
    ["Tardanzas", "Tardy"],
    ["Observaciones", "Observations"],
    ["Maestro(a):", "Teacher:"],
    ['Grado:', 'Grade:'],
    ["Asignatura:","Subject:"],

]);

$teacher = new Teacher(Session::id());
$year = $teacher->info('year');
list($f1, $f2, $f3) = explode(',', $_POST['semester']);
$_class = $_POST['class'];
$date1 = $teacher->info("asis$f1");
$date2 = $teacher->info("asis$f2");

list($year1, $year2) = explode('-', $year);
$students = new Student();

if (__LANG === "es") {
    $Mes = array('10' => 'Octubre', '12' => 'Diciembre', '03' => 'Marzo', '05' => 'Mayo');
} else {
    $Mes = array('10' => 'October', '12' => 'December', '03' => 'March', '05' => 'May');
}


$pdf = new PDF();
$pdf->footer = false;
$pdf->SetLeftMargin(5);
$pdf->AddPage();
$pdf->SetTitle($lang->translation('Informe de asistencia'));
$pdf->Fill();

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, $lang->translation("Año escolar"), 0, 1, 'C');
$pdf->Cell(0, 5, '20' . $year1 . '-20' . $year2, 0, 1, 'C');
if ($f3 < 6) {
    $yea = '20' . $year2 . '-' . $f3 . '-31';
    $pdf->Cell(0, 5, $Mes[$f3] . ' 20' . $year2, 0, 1, 'C');
} else {
    $yea = '20' . $year1 . '-' . $f3 . '-31';
    $pdf->Cell(0, 5, $Mes[$f3] . ' 20' . $year1, 0, 1, 'C');
}
$pdf->Cell(0, 5, $lang->translation('Informe de estudiantes con problemas de tardanzas y ausentismo por horario de clase'), 0, 1, 'C');
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 5, $lang->translation('Maestro(a):'));
$pdf->Cell(75, 5, utf8_decode($teacher->fullName()), 'B');

if ($_class === 'grado') {
    $pdf->Cell(20, 5, $lang->translation("Grado:"), 0, 0, 'C');
    $pdf->Cell(15, 5, "$teacher->grado", 'B', 1, 'C');
} else {
    $grade = DB::table('cursos')
        ->select('desc1')
        ->where([
            ['curso', $_class],
            ['year', $year],
        ])->first();
    $pdf->Cell(25, 5, $lang->translation("Asignatura:"), 0, 0, 'C');
    $pdf->Cell(0, 5, "$_class - $grade->desc1", 'B', 1);
}

$pdf->Ln();
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 5, '', 1);
$pdf->Cell(70, 5, $lang->translation('Nombre del estudiante'), 1, 0, 'C');
$pdf->Cell(20, 5, $lang->translation("Horario"), 1, 0, 'C');
$pdf->Cell(25, 5, $lang->translation("Ausencias"), 1, 0, 'C');
$pdf->Cell(25, 5, $lang->translation("Tardanzas"), 1, 0, 'C');
$pdf->Cell(50, 5, $lang->translation("Observaciones"), 1, 1, 'C');
$pdf->SetFont('Arial', '', 9);
if ($_class === 'grado') {
    $students = DB::table('padres')
        ->select('DISTINCT ss, nombre, apellidos')
        ->where([
            ['grado', $teacher->grado],
            ['year', $year],
        ])->orderBy('apellidos')->get();
} else {
    $students = DB::table('padres')
        ->where([
            ['curso', $_class],
            ['year', $year],
        ])->orderBy('apellidos')->get();
}


foreach ($students as $index => $student) {
    $attendances = DB::table('asispp')
        ->where([
            ['ss', $student->ss],
            ['year', $year],
            ['fecha', '>=', $date1],
            ['fecha', '<=', $date2],
        ])->orderBy('apellidos')->get();
    $attended = 0;
    $late = 0;
    foreach ($attendances as $attendance) {
        if ($attendance->codigo <= 7) {
            $attended++;
        } elseif ($attendance->codigo >= 8) {
            $late++;
        }
    }
    $pdf->Cell(10, 5, $index + 1, 1);
    $pdf->Cell(70, 5, utf8_decode("$student->nombre $student->apellidos"), 1);
    $pdf->Cell(20, 5, '', 1, 0, 'C');
    $pdf->Cell(25, 5, $attended, 1, 0, 'C');
    $pdf->Cell(25, 5, $late, 1, 0, 'C');
    $pdf->Cell(50, 5, '', 1, 1);
}
$pdf->Output();
