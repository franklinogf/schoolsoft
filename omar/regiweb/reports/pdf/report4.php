<?php
require_once '../../app.php';

use Classes\Controllers\Student;
use Classes\PDF;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Teacher;
use Classes\Util;

global $_class;
global $_trimester;
global $_report;
global $_title;
global $_table;
$summer = ($_report === 'V-nota') ? '2' : false;
$teacher = new Teacher(Session::id());
Session::is_logged();
$data = DB::table($_table)->where([
    ['curso', $_class],
    ['year', $teacher->info('year')],
    ['id', Session::id()],
    ['verano', $summer]
])->orderBy('apellidos')->first();

$students = new Student();
$students = $students->findByClass($_class, $_table, $summer);

$columns = [
    "Semestre-1" => [
        ["nota1", "con1", "nota2", "con2", "ex1", "nota2"],
        ["nota1", "con1", "nota2", "con2", "ex1", "sem1"]
    ],
    "Semestre-2" => [
        ["nota3", "con3", "nota4", "con4", "ex2", "nota4"],
        ["nota3", "con3", "nota4", "con4", "ex2", "sem2"]
    ],
    "V-Nota" => ['not1', 'not2', 'not3', 'not4', 'not5', 'not6', 'not9', 'not10', 'tpa1', 'por1', 'nota1', 'con1', 'aus1', 'tar1', 'com1']
];

$titles = [
    "Semestre-1" => ["T-1", "C-1", "T-2", "C-2", 'Exa', $lang->translation('Nota')],
    "Semestre-2" => ["T-3", "C-3", "T-4", "C-4", 'Exa', $lang->translation('Nota')],
    "V-Nota" => ['N1', 'N2', 'N3', 'N4', 'N5', 'N6', 'N7', 'Bn', 'TPA', '%', 'Nta', 'Con', 'Au', 'Ta', 'Com']
];

$lang->AddTranslation([
    ["Semestre 1", "Semester 1"],
    ["Semestre 2", "Semester 2"],
]);

$pdf = new PDF();
$pdf->footer = false;
$pdf->SetLeftMargin(5);
$pdf->AddPage();
$pdf->SetTitle($lang->translation($_title));
$pdf->Fill();

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, $lang->translation($_title), 0, 1, 'C');
$pdf->Ln(3);
$pdf->Cell(50, 5, $lang->translation("Profesor"), 1, 0, 'C', true);
$pdf->Cell(18, 5, $lang->translation("Curso"), 1, 0, 'C', true);
$pdf->Cell(40, 5, $lang->translation("Descripción"), 1, 0, 'C', true);
$pdf->Cell(20, 5, $lang->translation("Creditos"), 1, 0, 'C', true);
$pdf->Cell(20, 5, $lang->translation("Total Est."), 1, 0, 'C', true);
$pdf->Cell(25, 5, $lang->translation("Trimestre"), 1, 0, 'C', true);
$pdf->Cell(25, 5, $lang->translation("Fecha"), 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 7);
$pdf->Cell(50, 5, utf8_decode($teacher->fullName()), 1, 0, 'C');
$pdf->Cell(18, 5, $data->curso, 1, 0, 'C');
$pdf->Cell(40, 5, utf8_decode($data->descripcion), 1, 0, 'C');
$pdf->Cell(20, 5, $teacher->credits($_class), 1, 0, 'C');
$pdf->Cell(20, 5, count($students), 1, 0, 'C');
$pdf->Cell(25, 5, $lang->trimesterTranslation($_trimester), 1, 0, 'C');
$pdf->Cell(25, 5, Util::formatDate(Util::date()), 1, 1, 'C');

$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(50, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
$pdf->Cell(40, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
foreach ($titles[$_report] as $index => $title) {
    if (count($titles[$_report]) === ($index + 1)) {
        $pdf->Cell(10, 5, $title, 1, 1, 'C', true);
    } else {
        $pdf->Cell(7, 5, $title, 1, 0, 'C', true);
    }
}

$pdf->SetFont('Arial', '', 7);
foreach ($students as $student) {
    $pdf->Cell(50, 5, utf8_decode($student->apellidos), 1);
    $pdf->Cell(40, 5, utf8_decode($student->nombre), 1);
    if ($_report === 'V-Nota') {
        foreach ($columns[$_report] as $index => $column) {
            if (count($columns[$_report]) === ($index + 1)) {
                $pdf->Cell(10, 5, $student->{$column}, 1, 1, 'C');
            } else {
                $pdf->Cell(7, 5, $student->{$column}, 1, 0, 'C');
            }
        }
    } else {
        $number = $teacher->info('sutri') === "NO" ? 0 : 1;
        foreach ($columns[$_report][$number] as $index => $column) {
            if (count($columns[$_report][$number]) === ($index + 1)) {
                $pdf->Cell(10, 5, $student->{$column}, 1, 1, 'C');
            } else {
                $pdf->Cell(7, 5, $student->{$column}, 1, 0, 'C');
            }
        }
    }
}

$pdf->SetY(-45);
$value = DB::table('valores')->where([
    ['curso', $_class],
    ['trimestre', $_trimester],
    ['nivel', $_report],
    ['year', $teacher->info('year')]
])->first();



if ($value) {
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(20, 4, $lang->translation("Fecha"), 1, 0, 'C', true);
    $pdf->Cell(70, 4, $lang->translation("Tema"), 1, 0, 'C', true);
    $pdf->Cell(10, 4, $lang->translation("Valor"), 1, 0, 'C', true);
    $pdf->Cell(20, 4, $lang->translation("Fecha"), 1, 0, 'C', true);
    $pdf->Cell(70, 4, $lang->translation("Tema"), 1, 0, 'C', true);
    $pdf->Cell(10, 4, $lang->translation("Valor"), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 7);
    for ($i = 1; $i <= 5; $i++) {
        $first = $i * 1;
        if ($value->{"val$first"}) {
            $pdf->Cell(20, 4, Util::formatDate($value->{"fec$first"}), 1, 0, 'C');
            $pdf->Cell(70, 4, utf8_decode($value->{"tema$first"}), 1, 0);
            $pdf->Cell(10, 4, $value->{"val$first"}, 1, 0, 'C');
        } else {
            $pdf->Cell(20, 4, '', 1, 0);
            $pdf->Cell(70, 4, '', 1, 0);
            $pdf->Cell(10, 4, '', 1, 0);
        }
        $second = $i * 2;
        if ($value->{"val$second"}) {
            $pdf->Cell(20, 4, Util::formatDate($value->{"fec$second"}), 1, 0, 'C');
            $pdf->Cell(70, 4, utf8_decode($value->{"tema$second"}), 1, 0);
            $pdf->Cell(10, 4, $value->{"val$second"}, 1, 1, 'C');
        } else {
            $pdf->Cell(20, 4, '', 1, 0);
            $pdf->Cell(70, 4, '', 1, 0);
            $pdf->Cell(10, 4, '', 1, 1);
        }
    }
}

$pdf->Output();
