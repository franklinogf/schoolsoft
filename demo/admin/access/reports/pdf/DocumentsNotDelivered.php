<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;
use Classes\DataBase\DB;

Session::is_logged();

$lang = new Lang([
    ['Documentos no entregados', 'Documents not delivered'],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["NO ENTREGADO", "UNDELIVERED"],
    ["ENTREGADO", "DELIVERED"],
    ['Cuenta', 'Account'],
    ['Ningún documento entregado', 'No document delivered'],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Total de estudiantes', 'Total students'],
    ['Fecha', 'Date'],
    ['Documentos sin entregar', 'Undelivered documents'],
    ['Masculinos', 'Males'],
    ['Femeninas', 'Females'],
]);
$school = new School(Session::id());
$teacherClass = new Teacher();
$studentClass = new Student();

$year = $school->info('year2');
$allGrades = $school->allGrades();
$pdf = new PDF();

$pdf->SetTitle($lang->translation("Documentos no entregados") . " $year", true);
$pdf->Fill();

$docs = $_POST['option'] ?? '';
$doc1 = DB::table('docu_entregados')->where([
    ['codigo', $docs]
])->orderBy('codigo')->first();

foreach ($allGrades as $grade) {
    $teacher = $teacherClass->findByGrade($grade);
    $students = $studentClass->findByGrade($grade);
    $genderCount = ['M' => 0, 'F' => 0, 'T' => 0];
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Documentos no entregados") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $nom = $teacher->nombre ?? '';
    $ape = utf8_encode($teacher->apellidos ?? '');
    $pdf->splitCells($lang->translation("Maestro(a):") . " $nom $ape", $lang->translation("Grado:") . " $grade");

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 5, '', 1, 0, 'C', true);
    $pdf->Cell(19, 5, $lang->translation("Cuenta"), 1, 0, 'C', true);
    $pdf->Cell(55, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
    $pdf->Cell(50, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
    $pdf->Cell(56, 5, $lang->translation("Documentos sin entregar"), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);

    foreach ($students as $count => $student) {
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

        $docs = DB::table('docu_estudiantes')->where([
            ['id', $student->id],
            ['ss', $student->ss],
            ['nap', '']
        ])->orderBy('codigo')->get();
        $cantidad = count($docs);

        $pdf->Cell(10, 5, $count + 1, 'TRL', 0, 'C');
        $pdf->Cell(19, 5, $student->id, 'TRL', 0, 'C');
        $pdf->Cell(55, 5, $student->apellidos, 'TRL', 0);
        $pdf->Cell(50, 5, $student->nombre, 'TRL', 0);
        $pdf->SetFont('Times', 'B', 10);
        if ($cantidad == 0) {
            $pdf->Cell(56, 5, utf8_encode($lang->translation("Ningún documento entregado")), 1, 1, 'C');
        }
        $cc = 0;
        $g = 'LRT';
        foreach ($docs as $doc) {

            if ($doc->entrego == "Si") {
                //           $pdf->Cell(56,5,$lang->translation("ENTREGADO").$cantidad,1,1,'C');
            } else {
                if ($cc != 0) {
                    $pdf->Cell(134, 5, '', 'L', 0, 'C');
                }
                $pdf->Cell(56, 5, $doc->desc1, $g, 1, 'C');
                $cc = 1;
                $g = 'LR';
            }
        }

        $pdf->SetFont('Times', '', 10);
    }
}

$pdf->Output();
