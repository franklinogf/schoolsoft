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

$teacher = new Teacher(Session::id());
Session::is_logged();
$data = DB::table($_table)->where([
    ['curso', $_class],
    ['year', $teacher->info('year')],
    ['id', Session::id()],
    ['verano', '']
])->orderBy('apellidos')->first();

$students = new Student();
$students = $students->findByClass($_class,$_table);

$notes = [
    'A' => 0,
    'B' => 0,
    'C' => 0,
    'D' => 0,
    'F' => 0,
    'O' => 0
];

$fields = [
    "Trimestre-1" => "nota1",
    "Trimestre-2" => "nota2",
    "Trimestre-3" => "nota3",
    "Trimestre-4" => "nota4"
];
$columns = [
    "nota1" => ["not1", "not2", "not3", "not4", "not5", "not6", "not7", "not8", "not9", "not10", "tpa1", "por1", "nota1"],
    "nota2" => ["not11", "not12", "not13", "not14", "not15", "not16", "not17", "not18", "not19", "not20", "tpa2", "por2", "nota2"],
    "nota3" => ["not21", "not22", "not23", "not24", "not25", "not26", "not27", "not28", "not29", "not30", "tpa3", "por3", "nota3"],
    "nota4" => ["not31", "not32", "not33", "not34", "not35", "not36", "not37", "not38", "not39", 'not40', "tpa4", "por4", "nota4"]
];

$pdf = new PDF();
$pdf->footer = false;
$pdf->SetLeftMargin(5);
$pdf->AddPage();
$pdf->SetTitle($_title);
$pdf->Fill();

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0,5,$_title,0,1,'C');
$pdf->Ln(3);
$pdf->Cell(50, 5, 'Profesor', 1, 0, 'C', true);
$pdf->Cell(18, 5, 'Curso', 1, 0, 'C', true);
$pdf->Cell(40, 5, utf8_decode('Descripción'), 1, 0, 'C', true);
$pdf->Cell(20, 5, 'Creditos', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'Total Est.', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'Trimestre', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'Fecha', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 7);
$pdf->Cell(50, 5, utf8_decode($teacher->fullName()), 1, 0, 'C');
$pdf->Cell(18, 5, $data->curso, 1, 0, 'C');
$pdf->Cell(40, 5, utf8_decode($data->descripcion), 1, 0, 'C');
$pdf->Cell(20, 5, $teacher->credits($_class), 1, 0, 'C');
$pdf->Cell(20, 5, count($students), 1, 0, 'C');
$pdf->Cell(25, 5, $_trimester, 1, 0, 'C');
$pdf->Cell(25, 5, Util::formatDate(Util::date()), 1, 1, 'C');

$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(50, 5, 'Apellidos', 1, 0, 'C', true);
$pdf->Cell(40, 5, 'Nombre', 1, 0, 'C', true);
$pdf->Cell(7, 5, 'N1', 1, 0, 'C', true);
$pdf->Cell(7, 5, 'N2', 1, 0, 'C', true);
$pdf->Cell(7, 5, 'N3', 1, 0, 'C', true);
$pdf->Cell(7, 5, 'N4', 1, 0, 'C', true);
$pdf->Cell(7, 5, 'N5', 1, 0, 'C', true);
$pdf->Cell(7, 5, 'N6', 1, 0, 'C', true);
$pdf->Cell(7, 5, 'N7', 1, 0, 'C', true);
$pdf->Cell(7, 5, 'N8', 1, 0, 'C', true);
$pdf->Cell(7, 5, 'N9', 1, 0, 'C', true);
$pdf->Cell(7, 5, 'N10', 1, 0, 'C', true);
$pdf->Cell(7, 5, 'TPA', 1, 0, 'C', true);
$pdf->Cell(7, 5, '%', 1, 0, 'C', true);
$pdf->Cell(10, 5, 'Nota', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 7);
foreach ($students as $student) {
    $number = $fields[$_trimester];
    $pdf->Cell(50, 5, utf8_decode($student->apellidos), 1);
    $pdf->Cell(40, 5, utf8_decode($student->nombre), 1);
    foreach ($columns[$number] as $index => $column) {
        if ($column === $number) {
            $pdf->Cell(10, 5, $student->{$column}, 1, 1, 'C');
        } else {
            $pdf->Cell(7, 5, $student->{$column}, 1, 0, 'C');
        }
    }

    if ($student->{$number} >= $teacher->info("vala")) {
        $notes['A']++;
    }
    if ($student->{$number} >= $teacher->info("valb") and $student->{$number} < $teacher->info("vala")) {
        $notes['B']++;
    }
    if ($student->{$number} >= $teacher->info("valc") and $student->{$number} < $teacher->info("valb")) {
        $notes['C']++;
    }
    if ($student->{$number} >= $teacher->info("vald") and $student->{$number} < $teacher->info("valc")) {
        $notes['D']++;
    }
    if ($student->{$number} >= $teacher->info("valf") and $student->{$number} < $teacher->info("vald")) {
        $notes['F']++;
    }
    if ($student->{$number} < 0 or $student->{$number} === '') {
        $notes['O']++;
    }
}

$pdf->SetY(-45);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 5, utf8_decode('Distribución de Notas:'), 1, 0, 'C', true);
$pdf->Cell(10, 5, 'A', 1, 0, 'C', true);
$pdf->Cell(12, 5, $notes['A'], 1, 0, 'C');
$pdf->Cell(10, 5, 'B', 1, 0, 'C', true);
$pdf->Cell(12, 5, $notes['B'], 1, 0, 'C');
$pdf->Cell(10, 5, 'C', 1, 0, 'C', true);
$pdf->Cell(12, 5, $notes['C'], 1, 0, 'C');
$pdf->Cell(10, 5, 'D', 1, 0, 'C', true);
$pdf->Cell(12, 5, $notes['D'], 1, 0, 'C');
$pdf->Cell(10, 5, 'F', 1, 0, 'C', true);
$pdf->Cell(12, 5, $notes['F'], 1, 0, 'C');
$pdf->Cell(15, 5, 'Otros', 1, 0, 'C', true);
$pdf->Cell(12, 5, $notes['O'], 1, 1, 'C');

$value = DB::table('valores')->where([
    ['curso', $_class],
    ['trimestre', $_trimester],
    ['nivel', $_report],
    ['year',$teacher->info('year')]
])->first();

if ($value) {
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(20, 4, 'Fecha', 1, 0, 'C', true);
    $pdf->Cell(70, 4, 'Tema', 1, 0, 'C', true);
    $pdf->Cell(10, 4, 'Valor', 1, 0, 'C', true);
    $pdf->Cell(20, 4, 'Fecha', 1, 0, 'C', true);
    $pdf->Cell(70, 4, 'Tema', 1, 0, 'C', true);
    $pdf->Cell(10, 4, 'Valor', 1, 1, 'C', true);

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
