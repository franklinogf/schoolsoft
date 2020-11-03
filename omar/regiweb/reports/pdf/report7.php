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

$value = DB::table('valores')->where([
    ['curso', $_class],
    ['trimestre', $_trimester],
    ['nivel', 'Notas'],
    ['year', $teacher->info('year')]
])->first();

$fields = [
    "Trimestre-1" => "1",
    "Trimestre-2" => "2",
    "Trimestre-3" => "3",
    "Trimestre-4" => "4"
];

$notesNumber = [
    "1" => [1, 9],
    "2" => [10, 18],
    "3" => [21, 29],
    "4" => [31, 39]
];

$number = $fields[$_trimester];

$lineHight = 7;

$pdf = new PDF();
$pdf->footer = false;
$pdf->SetLeftMargin(5);
$pdf->AddPage("L");
$pdf->SetTitle('Notas en porciento');
$pdf->Fill();

$pdf->SetFont('Arial', 'B', 10);
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
$pdf->Cell(15, 5, 'N1', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'N2', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'N3', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'N4', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'N5', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'N6', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'N7', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'N8', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'N9', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'T-L', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'T-D', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'P-C', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'Nota', 1, 1, 'C', true);

foreach ($students as $index => $student) {
    if ($index === 14 or $index === 28) $pdf->AddPage('L');
    $pdf->SetFont('Arial', '', 7);
    $notes = [];
    $totalNotes = 0;
    $totalValues = 0;
    $val = 1;
    $tl = 0;
    for ($i = $notesNumber[$number][0]; $i <= $notesNumber[$number][1]; $i++) {
        if (!empty($student->{"not{$i}"}) && $student->{"not{$i}"} > 0) {
            if ($value->{"val{$val}"} > 0 && $value->{"val{$val}"} < 100) {
                $notes[$i] = round(($student->{"not{$i}"} / $value->{"val{$val}"}) * 100);
                $totalNotes += (int) $student->{"not{$i}"};
                $totalValues += (int) $value->{"val{$val}"};
            } else {
                $notes[$i] = $student->{"not{$i}"};
                $totalNotes += $notes[$i];
                $totalValues += (int) $value->{"val{$val}"};
            }
        }
        $val++;
    }

    if ($student->{"ptl{$number}"} <> 100 && $student->{"tl{$number}"} > 0) {
        $tl = round(($student->{"tl{$number}"} / $student->{"ptl{$number}"}) * 100);
        $totalNotes += (int) $student->{"tl{$number}"};
        $totalValues += (int) $student->{"ptl{$number}"};
    } else {
        $tl = $student->{"tl{$number}"};
        if ($student->{"tl{$number}"} > 0 or $student->{"tl{$number}"} === '0') {
            $totalNotes += $tl;
            $totalValues += (int) $student->{"ptl{$number}"};
        }
    }

    if ($student->{"ptd{$number}"} <> 100 && $student->{"td{$number}"} > 0) {
        $td = round(($student->{"td{$number}"} / $student->{"ptd{$number}"}) * 100);
        $totalNotes += (int) $student->{"td{$number}"};
        $totalValues += (int) $student->{"ptd{$number}"};
    } else {
        $td = $student->{"td{$number}"};
        if ($student->{"td{$number}"} > 0 or $student->{"td{$number}"} === '0') {
            $totalNotes += $td;
            $totalValues += (int) $student->{"ptd{$number}"};
        }
    }

    if ($student->{"tpc{$number}"} <> 100 && $student->{"pc{$number}"} > 0) {
        $pc = round(($student->{"pc{$number}"} / $student->{"tpc{$number}"}) * 100);
        $totalNotes += (int) $student->{"pc{$number}"};
        $totalValues += (int) $student->{"tpc{$number}"};
    } else {
        $pc = $student->{"pc{$number}"};
        if ($student->{"pc{$number}"} > 0 or $student->{"pc{$number}"} === '0') {
            $totalNotes += $pc;
            $totalValues += (int) $student->{"tpc{$number}"};
        }
    }


    $TOTAL = ($totalValues > 0) ? round(($totalNotes / $totalValues) * 100) : 0;
    $pdf->Cell(50, $lineHight, utf8_decode($student->apellidos), 1);
    $pdf->Cell(40, $lineHight, utf8_decode($student->nombre), 1);
    $pdf->SetFont('Arial', '', 8);
    for ($i = $notesNumber[$number][0]; $i <= $notesNumber[$number][1]; $i++) {
        $pdf->Cell(15, $lineHight / 2, (isset($notes[$i]) ? $notes[$i] : ''), "LTR", 0, 'C');
    }
    $pdf->Cell(15, $lineHight / 2, $tl, "LTR", 0, 'C');
    $pdf->Cell(15, $lineHight / 2, $td, "LTR", 0, 'C');
    $pdf->Cell(15, $lineHight / 2, $pc, "LTR", 0, 'C');
    $pdf->Cell(15, $lineHight / 2, ($TOTAL > 0) ? $TOTAL : '', "LTR", 1, 'C');

    /* ------------------------------- SECOND LINE ------------------------------ */

    $pdf->Cell(50);
    $pdf->Cell(40);
    $valCount = 1;
    for ($i = $notesNumber[$number][0]; $i <= $notesNumber[$number][1]; $i++) {
        $pdf->Cell(15, $lineHight / 2, (isset($notes[$i]) ? $notes[$i] . "/" . $value->{"val{$valCount}"} : ''), "LBR", 0, 'C');
        $valCount++;
    }
    $pdf->Cell(15, $lineHight / 2, ($tl !== "") ? $student->{"tl{$number}"} . "/" . $student->{"ptl{$number}"} : '', "LBR", 0, 'C');
    $pdf->Cell(15, $lineHight / 2, ($td !== "") ? $student->{"td{$number}"} . "/" . $student->{"ptd{$number}"} : '', "LBR", 0, 'C');
    $pdf->Cell(15, $lineHight / 2, ($pc !== "") ? $student->{"pc{$number}"} . "/" . $student->{"tpc{$number}"} : '', "LBR", 0, 'C');

    $grade = "";

    if ($TOTAL >= $teacher->info("vala")) {
        $grade = 'A';
    } else if ($TOTAL >= $teacher->info("valb") and $TOTAL < $teacher->info("vala")) {
        $grade = 'B';
    } else if ($TOTAL >= $teacher->info("valc") and $TOTAL < $teacher->info("valb")) {
        $grade = 'C';
    } else if ($TOTAL >= $teacher->info("vald") and $TOTAL < $teacher->info("valc")) {
        $grade = 'D';
    } else if ($TOTAL >= $teacher->info("valf") and $TOTAL < $teacher->info("vald")) {
        $grade = 'F';
    } else if ($TOTAL < 0 or $TOTAL == '') {
        $grade = 'O';
    }

    $pdf->Cell(15, $lineHight / 2, ($TOTAL > 0) ? $grade : '', "LBR", 1, 'R');
}

$pdf->SetY(-45);

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
