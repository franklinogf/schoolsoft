<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\DataBase\DB;
use Classes\Util;

Session::is_logged();

$lang = new Lang([
    ["Movimiento de matrícula", "School enrollment movement"],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["Nombre del estudiante", "Student name"],
    ['Cuenta', 'Account'],
    ['Genero', 'Gender'],
    ['Dato Estadistico', 'Statistical Data'],
    ['(T1, T2, T3, Ect)', '(Q1, Q2, Q3, Ect)'],
    ['Total de estudiantes', 'Total students'],
    ['Fecha', 'Date'],
    ['TRASLADOS', 'TRANSFERS'],
    ['Masculinos', 'Males'],
    ['Femeninas', 'Females'],
    ['REMATRICULADOS', 'RE-ENROLLED
'],


]);

$school = new School();
$teacherClass = new Teacher();
//$studentClass = new Student();

$year = $school->year();
$allGrades = $school->allGrades();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Movimiento de matrícula") . " $year", true);
$pdf->Fill();

foreach ($allGrades as $grade) {
    $teacher = $teacherClass->findByGrade($grade);

    //    $students = $studentClass->findByGrade($grade);
    $students = DB::table('year')->where([
        ['fecha_baja', '!=', '0000-00-00'],
        ['grado', $grade],
        ['year', $year],
    ])->orderBy('apellidos')->get();


    $genderCount = ['M' => 0, 'F' => 0, 'T' => 0];
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Movimiento de matrícula") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $nom = $teacher->nombre ?? '';
    $ape = $teacher->apellidos ?? '';
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->splitCells($lang->translation("Maestro(a):") . " $nom $ape", $lang->translation("Grado:") . " $grade");

    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 5, $lang->translation("TRASLADOS"), 0, 1, 'C');
    $pdf->Ln(5);



    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(100, 5, $lang->translation("Nombre del estudiante"), 'LRT', 0, 'C', true);
    $pdf->Cell(50, 5, $lang->translation("Dato Estadistico"), 'LRT', 0, 'C', true);
    $pdf->Cell(40, 5, $lang->translation("Fecha"), 'LRT', 1, 'C', true);
    $pdf->Cell(100, 5, '', 'LRB', 0, 'C', true);
    $pdf->Cell(50, 5, $lang->translation("(T1, T2, T3, Ect)"), 'LRB', 0, 'C', true);
    $pdf->Cell(40, 5, '', 'LRB', 1, 'C', true);

    $pdf->SetFont('Arial', '', 11);
    $n = 0;
    foreach ($students as $count => $student) {
        $n = $n + 1;

        $code = DB::table('codigo_bajas')->where([
            ['codigo', $student->codigobaja],
        ])->orderBy('codigo')->first();

        $dia = date('j');
        $mes = date('n');
        $ano = date('Y');
        $fec = $student->fecha;
        list($anonaz, $mesnaz, $dianaz) = explode('-', $fec);
        if (($mesnaz == $mes) && ($dianaz > $dia)) {
            $ano = ($ano - 1);
        }
        if ($mesnaz > $mes) {
            $ano = ($ano - 1);
        }
        $edad = $ano - $anonaz;
        if ($edad > 20) {
            $edad = '';
        }
        $gender = Util::gender($student->genero);
        $genderCount[$gender]++;
        $genderCount['T']++;
        $pdf->Cell(5, 6, $n, 1, 0, 'L');
        $pdf->Cell(95, 6, $student->apellidos . ' ' . $student->nombre, 1);
        $pdf->Cell(50, 6, $code->id, 1, 0);
        $pdf->Cell(40, 6, $student->fecha_baja, 1, 1, 'C');
    }

    for ($i = $n; $i < 7; $i++) {
        $pdf->Cell(5, 6, $i + 1, 1, 0, 'L');
        $pdf->Cell(95, 6, '', 1, 0, 'L');
        $pdf->Cell(50, 6, '', 1, 0, 'C');
        $pdf->Cell(40, 6, '', 1, 1, 'C');
    }

    $pdf->Ln(2);
    $pdf->Cell(40, 5, $lang->translation("Total de estudiantes"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $genderCount['T'], 1, 1, 'C');
    $pdf->Cell(40, 5, $lang->translation("Masculinos"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $genderCount['M'], 1, 1, 'C');
    $pdf->Cell(40, 5, $lang->translation("Femeninas"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $genderCount['F'], 1, 1, 'C');


    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 5, $lang->translation("REMATRICULADOS"), 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(100, 5, $lang->translation("Nombre del estudiante"), 'LRT', 0, 'C', true);
    $pdf->Cell(50, 5, $lang->translation("Dato Estadistico"), 'LRT', 0, 'C', true);
    $pdf->Cell(40, 5, $lang->translation("Fecha"), 'LRT', 1, 'C', true);
    $pdf->Cell(100, 5, '', 'LRB', 0, 'C', true);
    $pdf->Cell(50, 5, $lang->translation("(T1, T2, T3, Ect)"), 'LRB', 0, 'C', true);
    $pdf->Cell(40, 5, '', 'LRB', 1, 'C', true);

    $pdf->SetFont('Arial', '', 11);

    for ($i = 0; $i < 7; $i++) {
        $pdf->Cell(5, 6, $i + 1, 1, 0, 'L');
        $pdf->Cell(95, 6, '', 1, 0, 'L');
        $pdf->Cell(50, 6, '', 1, 0, 'C');
        $pdf->Cell(40, 6, '', 1, 1, 'C');
    }
}

$pdf->Output();
