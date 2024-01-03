<?php
require_once '../../../../app.php';

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
    ["Informe Acomodo Razonable", "Reasonable Accommodation Report"],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["Nombre del estudiante", "Student name"],
    ['Cuenta', 'Account'],
    ['Genero', 'Gender'],
    ['Impedimentos/Condisiones', 'Impediments/Conditions'],
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

$year = $school->year();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Informe Acomodo Razonable") . " $year", true);
$pdf->Fill();

$allGrades = DB::table('year')->select("distinct grado")->where([
    ['acomodo', 'Si'],
    ['year', $year],
])->orderBy('grado')->get();

foreach ($allGrades as $grade1) {
    $teacher = $teacherClass->findByGrade($grade1->grado);
    $students = DB::table('year')->where([
        ['acomodo', 'Si'],
        ['year', $year],
        ['grado', $grade1->grado],
    ])->orderBy('grado, apellidos')->get();

    $genderCount = ['M' => 0, 'F' => 0, 'T' => 0];
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Informe Acomodo Razonable") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->splitCells($lang->translation("Maestro(a):") . " $teacher->nombre $teacher->apellidos", $lang->translation("Grado:") . " $grade1->grado");

    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(100, 5, $lang->translation("Nombre del estudiante"), 1, 0, 'C', true);
    $pdf->Cell(90, 5, $lang->translation("Impedimentos/Condisiones"), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 11);
    $n = 0;
    foreach ($students as $count => $student) {
        $n = $n + 1;

        $code = DB::table('codigo_bajas')->where([
            ['codigo', $student->codigobaja],
        ])->orderBy('codigo')->first();

        $dia = date(j);
        $mes = date(n);
        $ano = date(Y);
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
        $pdf->Cell(5, 6, $n, 1, 0, 'R');
        $pdf->Cell(95, 6, $student->apellidos . ' ' . $student->nombre, 1);
        $pdf->Cell(90, 6, $student->imp1, 1, 1, 'C');
        if (!empty($student->imp2)) {
            $pdf->Cell(100, 6, '', 0, 0, 'R');
            $pdf->Cell(90, 6, $student->imp2, 1, 1, 'C');
        }
        if (!empty($student->imp3)) {
            $pdf->Cell(100, 6, '', 0, 0, 'R');
            $pdf->Cell(90, 6, $student->imp3, 1, 1, 'C');
        }
        if (!empty($student->imp4)) {
            $pdf->Cell(100, 6, '', 0, 0, 'R');
            $pdf->Cell(90, 6, $student->imp4, 1, 1, 'C');
        }
    }

    for ($i = $n; $i < 7; $i++) {
        $pdf->Cell(5, 6, $i + 1, 1, 0, 'R');
        $pdf->Cell(95, 6, '', 1, 0, 'L');
        $pdf->Cell(90, 6, '', 1, 1, 'C');
    }

    $pdf->Ln(2);
    $pdf->Cell(40, 5, $lang->translation("Total de estudiantes"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $genderCount['T'], 1, 1, 'C');
    $pdf->Cell(40, 5, $lang->translation("Masculinos"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $genderCount['M'], 1, 1, 'C');
    $pdf->Cell(40, 5, $lang->translation("Femeninas"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $genderCount['F'], 1, 1, 'C');


    $pdf->Ln(5);
}




$pdf->Output();
