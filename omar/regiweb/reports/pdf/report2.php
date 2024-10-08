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

$teacher = new Teacher(Session::id());
Session::is_logged();
$data = DB::table('padres')->where([
    ['curso', $_class],
    ['year', $teacher->info('year')],
    ['id', Session::id()],
    ['verano', '']
])->orderBy('apellidos')->first();
$students = new Student();
$students = $students->findByClass($_class);
$amountOfStudents = count($students);
$notes = [
    'A' => 0,
    'B' => 0,
    'C' => 0,
    'D' => 0,
    'F' => 0,
    'O' => 0
];

$fields = [
    "Trimestre-1" => 1,
    "Trimestre-2" => 2,
    "Trimestre-3" => 3,
    "Trimestre-4" => 4
];
$lang->addTranslation([
    ["Promedio", "Average"],
    ["Escala", "Scale"],
    ["Total de","Total of"]
]);

$pdf = new PDF();
$pdf->footer = false;
$pdf->SetLeftMargin(5);
$pdf->AddPage();
$pdf->SetTitle($lang->translation("Notas") . ' 2');
$pdf->Fill();

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, $lang->translation("Notas") . ' 2', 0, 1, 'C');
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
$pdf->Cell(20, 5, number_format($teacher->classCredit($_class),2), 1, 0, 'C');
$pdf->Cell(20, 5, $amountOfStudents, 1, 0, 'C');
$pdf->Cell(25, 5, $lang->trimesterTranslation($_trimester), 1, 0, 'C');
$pdf->Cell(25, 5, Util::formatDate(Util::date()), 1, 1, 'C');

$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(50, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
$pdf->Cell(40, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
$pdf->Cell(15, 5, $lang->translation("Nota"), 1, 0, 'C', true);
$pdf->Cell(15, 5, __LANG === 'es' ? 'T-L' : 'HW', 1, 0, 'C', true);
$pdf->Cell(15, 5, __LANG === 'es' ? 'T-D' : 'D-W', 1, 0, 'C', true);
$pdf->Cell(15, 5, __LANG === 'es' ? 'P-C' : 'Quiz', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'TPA', 1, 0, 'C', true);
$pdf->Cell(15, 5, '%', 1, 0, 'C', true);
$pdf->Cell(20, 5, $lang->translation("Promedio"), 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 7);
foreach ($students as $index => $student) {
    $number = $fields[$_trimester];
    $pdf->Cell(50, 5, utf8_decode($student->apellidos), 1);
    $pdf->Cell(40, 5, utf8_decode($student->nombre), 1);
    $pdf->Cell(15, 5, $student->{"not{$number}0"}, 1, 0, 'C');
    $pdf->Cell(15, 5, $student->{"td{$number}"}, 1, 0, 'C');
    $pdf->Cell(15, 5, $student->{"tl{$number}"}, 1, 0, 'C');
    $pdf->Cell(15, 5, $student->{"pc{$number}"}, 1, 0, 'C');
    $pdf->Cell(15, 5, $student->{"tpa{$number}"}, 1, 0, 'C');
    $pdf->Cell(15, 5, $student->{"por{$number}"}, 1, 0, 'C');
    $pdf->Cell(10, 5, $student->{"nota{$number}"}, 1, 0, 'C');

    $thisNote = "";
    if ($student->{"nota{$number}"} !== "") {
        if ($student->{"nota{$number}"} >= 3.50) {
            $thisNote = "A";
            $notes['A']++;
        } else if ($student->{"nota{$number}"} >= 2.50) {
            $thisNote = "B";
            $notes['B']++;
        } else if ($student->{"nota{$number}"} >= 1.60) {
            $thisNote = "C";
            $notes['C']++;
        } else if ($student->{"nota{$number}"} >= 0.80) {
            $thisNote = "D";
            $notes['D']++;
        } else {
            $thisNote = "F";
            $notes['F']++;
        }
    }
    $pdf->Cell(10, 5, $thisNote, 1, 1, 'C');
}

$pdf->SetY(-45);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(22, 5, $lang->translation("Escala"), 1, 0, 'C', true);
$pdf->Cell(63, 5, $lang->translation("Distribución de notas"), 1, 1, 'C', true);

$pdf->Cell(22, 5, '4.00 - 3.50', 1, 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Total de") . ' A', 1, 0, 'L', true);
$pdf->Cell(16, 5, $notes["A"], 1, 0, 'C');
$pdf->Cell(17, 5, round(($notes["A"] / $amountOfStudents) * 100, 2) . '%', 1, 1, 'C');

$pdf->Cell(22, 5, '3.49 - 2.50', 1, 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Total de") . ' B', 1, 0, 'L', true);
$pdf->Cell(16, 5, $notes["B"], 1, 0, 'C');
$pdf->Cell(17, 5, round(($notes["B"] / $amountOfStudents) * 100, 2) . '%', 1, 1, 'C');

$pdf->Cell(22, 5, '2.49 - 1.60', 1, 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Total de") . ' C', 1, 0, 'L', true);
$pdf->Cell(16, 5, $notes["C"], 1, 0, 'C');
$pdf->Cell(17, 5, round(($notes["C"] / $amountOfStudents) * 100, 2) . '%', 1, 1, 'C');

$pdf->Cell(22, 5, '1.59 - 0.80', 1, 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Total de") . ' D', 1, 0, 'L', true);
$pdf->Cell(16, 5, $notes["D"], 1, 0, 'C');
$pdf->Cell(17, 5, round(($notes["D"] / $amountOfStudents) * 100, 2) . '%', 1, 1, 'C');

$pdf->Cell(22, 5, '0.79 - 0.00', 1, 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Total de") . ' F', 1, 0, 'L', true);
$pdf->Cell(16, 5, $notes["F"], 1, 0, 'C');
$pdf->Cell(17, 5, round(($notes["F"] / $amountOfStudents) * 100, 2) . '%', 1, 1, 'C');

$pdf->Output();
