<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\DataBase\DB;

Session::is_logged();
$teacher = new Teacher();

$lang = new Lang([
    ['Informe de acceso de los administradores', 'Administrator Access Report'],
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
$students = new Student();
$allStudents = $students->all();
$school = new School();
$grades = $school->allGrades();

$from = $_POST['from'];
$to = $_POST['to'];
$ida = $_POST['student'];

$pdf = new PDF();
$pdf->SetTitle($lang->translation("Informe de acceso de los administradores") . " $year", true);
$pdf->Fill();
if ($option != 'all') {

    $admin1 = DB::table('colegio')->where([
        ['id', $ida]
    ])->orderBy('usuario')->get();


    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Informe de acceso de los administradores") . " $year", 0, 1, 'C');
    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 5, $lang->translation("Desde") . ": $from / " . $lang->translation("Hasta") . ": $to", 0, 1, 'C');

    $pdf->Ln(5);
    $pdf->Cell(70, 5, $lang->translation("Nombre") . ' ' . $admin1->usuario, 0, 1, 'L');
    $pdf->SetFont('Arial', 'B', 10);

    //    $pdf->Cell(10);
    $pdf->Cell(20, 5, '', 1, 0, 'C', true);
    $pdf->Cell(20, 5, 'ID', 1, 0, 'C', true);
    $pdf->Cell(50, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
    $pdf->Cell(50, 5, $lang->translation("Hora"), 1, 0, 'C', true);
    $pdf->Cell(50, 5, $lang->translation("IP"), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);

    $attendances = DB::table('entradas')->where([
        ['id', $ida],
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
    //    $school = new School();
    //    $year = $school->info('year');
    $grade = $_POST['grade'];
    //    $separatedGrade = $_POST['separatedGrade'] === 'si' ? true : false;


    $admins1 = DB::table('colegio')->orderBy('usuario')->get();


    $type = $_POST['type'];
    //    $grades = $grade !== '' ? [$grade] : $school->allGrades();
    //        foreach ($admins as $admin) {
    //        $students = DB::table('colegio')->where([
    //            ['grado', $grade],
    //        ])->orderBy("usuario")->get();

    $count = 1;
    $type = 'list';
    foreach ($admins1 as $admin2) {
        $pdf->addPage();
        $pdf->SetFont('Arial', 'B', 15);
        $pdf->Cell(0, 5, $lang->translation("Informe de acceso de los padres") . " $year", 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, $lang->translation("Desde") . ": $from / " . $lang->translation("Hasta") . ": $to", 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->Cell(20, 5, $lang->translation("Nombre"), 1, 0, 'L', true);
        $pdf->Cell(135, 5, $admin2->usuario, 0, 0, 'L');
        $pdf->Cell(20, 5, $lang->translation("Grado"), 1, 0, 'C', true);
        $pdf->Cell(20, 5, '', 0, 1, 'L');
        $pdf->Ln(5);

        $pdf->Cell(20, 5, '', 1, 0, 'C', true);
        $pdf->Cell(20, 5, 'ID', 1, 0, 'C', true);
        $pdf->Cell(50, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
        $pdf->Cell(50, 5, $lang->translation("Hora"), 1, 0, 'C', true);
        $pdf->Cell(50, 5, $lang->translation("IP"), 1, 1, 'C', true);
        $pdf->SetFont('Arial', '', 12);
        $attendances = DB::table('entradas')->where([
            ['id', $admin2->id],
            ['fecha', '>=', $from],
            ['fecha', '<=', $to]
        ])->orderBy('fecha')->get();
        foreach ($attendances as $attendance) {
            $pdf->Cell(20, 5, $count, 1, 0, 'C');
            $pdf->Cell(20, 5, $attendance->id, 1);
            $pdf->Cell(50, 5, $attendance->fecha, 1);
            $pdf->Cell(50, 5, $attendance->hora, 1, 0, 'C');
            $pdf->Cell(50, 5, $attendance->ip, 1, 1, 'C');
            $count++;
        }
    }
    //      }
}

$pdf->Output();
