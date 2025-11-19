<?php
require_once __DIR__ . '/../../../app.php';

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
$lang->addTranslation([
    ["Notas en decimal", "Decimal grades"],
]);
$pdf = new PDF();
$pdf->footer = false;
$pdf->SetLeftMargin(5);
$pdf->AddPage("L");
$pdf->SetTitle($lang->translation("Notas en decimal"));
$pdf->Fill();

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, $lang->translation("Notas en decimal"), 0, 1, 'C');
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
$pdf->Cell(20, 5, count($students), 1, 0, 'C');
$pdf->Cell(25, 5, $lang->trimesterTranslation($_trimester), 1, 0, 'C');
$pdf->Cell(25, 5, Util::formatDate(Util::date()), 1, 1, 'C');

$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(50, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
$pdf->Cell(40, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
$pdf->Cell(15, 5, __LANG === 'es' ? 'N1' : 'G1', 1, 0, 'C', true);
$pdf->Cell(15, 5, __LANG === 'es' ? 'N2' : 'G2', 1, 0, 'C', true);
$pdf->Cell(15, 5, __LANG === 'es' ? 'N3' : 'G3', 1, 0, 'C', true);
$pdf->Cell(15, 5, __LANG === 'es' ? 'N4' : 'G4', 1, 0, 'C', true);
$pdf->Cell(15, 5, __LANG === 'es' ? 'N5' : 'G5', 1, 0, 'C', true);
$pdf->Cell(15, 5, __LANG === 'es' ? 'N6' : 'G6', 1, 0, 'C', true);
$pdf->Cell(15, 5, __LANG === 'es' ? 'N7' : 'G7', 1, 0, 'C', true);
$pdf->Cell(15, 5, __LANG === 'es' ? 'N8' : 'G8', 1, 0, 'C', true);
$pdf->Cell(15, 5, __LANG === 'es' ? 'N9' : 'G9', 1, 0, 'C', true);
$pdf->Cell(15, 5, __LANG === 'es' ? 'T-L' : 'HW', 1, 0, 'C', true);
$pdf->Cell(15, 5, __LANG === 'es' ? 'T-D' : 'D-W', 1, 0, 'C', true);
$pdf->Cell(15, 5, __LANG === 'es' ? 'P-C' : 'Quiz', 1, 0, 'C', true);
$pdf->Cell(15, 5, $lang->translation("Nota"), 1, 1, 'C', true);

foreach ($students as $index => $student) {
    if ($index === 14 or $index === 28) $pdf->AddPage('L');
    $pdf->SetFont('Arial', '', 7);
    $notes = [];
    $grades = [];
    $totalNotes = 0;
    $totalValues = 0;
    $val = 1;
    $tl = 0;
    for ($i = $notesNumber[$number][0]; $i <= $notesNumber[$number][1]; $i++) {
        if (!empty($student->{"not{$i}"}) && $student->{"not{$i}"} > 0) {
            if ($value->{"val{$val}"} > 0 && $value->{"val{$val}"} < 100) {
                $notes[$i] = round(($student->{"not{$i}"} / $value->{"val{$val}"}) * 100);
            } else {
                $notes[$i] = $student->{"not{$i}"};
            }

            if ($notes[$i] >= 90) {
                $grades[$i] = 'A';
                $totalNotes += 4;
                $totalValues++;
            } else if ($notes[$i] >= 80 and $notes[$i] < 90) {
                $grades[$i] = 'B';
                $totalNotes += 3;
                $totalValues++;
            } else if ($notes[$i] >= 70 and $notes[$i] < 80) {
                $grades[$i] = 'C';
                $totalNotes += 2;
                $totalValues++;
            } else if ($notes[$i] >= 60 and $notes[$i] < 70) {
                $grades[$i] = 'D';
                $totalNotes += 1;
                $totalValues++;
            } else if ($notes[$i] > 0) {
                $grades[$i] = 'F';
                $totalNotes += 0;
                $totalValues++;
            }
        }
        $val++;
    }

    if ($student->{"ptl{$number}"} <> 100 && $student->{"ptl{$number}"} !== "" && $student->{"tl{$number}"} > 0) {
        $tl = round(($student->{"tl{$number}"} / $student->{"ptl{$number}"}) * 100);
    } else {
        $tl = $student->{"tl{$number}"};
    }

    if ($tl >= 90) {
        $tl = 'A';
        $totalNotes += 4;
        $totalValues++;
    } else if ($tl >= 80 and $tl < 90) {
        $tl = 'B';
        $totalNotes += 3;
        $totalValues++;
    } else if ($tl >= 70 and $tl < 80) {
        $tl = 'C';
        $totalNotes += 2;
        $totalValues++;
    } else if ($tl >= 60 and $tl < 70) {
        $tl = 'D';
        $totalNotes += 1;
        $totalValues++;
    } else if ($tl > 0) {
        $tl = 'F';
        $totalNotes += 0;
        $totalValues++;
    }

    if ($student->{"ptd{$number}"} <> 100 && $student->{"ptd{$number}"} !== "" && $student->{"td{$number}"} > 0) {
        $td = round(($student->{"td{$number}"} / $student->{"ptd{$number}"}) * 100);
    } else {
        $td = $student->{"td{$number}"};
    }
    if ($td >= 90) {
        $td = 'A';
        $totalNotes += 4;
        $totalValues++;
    } else if ($td >= 80 and $td < 90) {
        $td = 'B';
        $totalNotes += 3;
        $totalValues++;
    } else if ($td >= 70 and $td < 80) {
        $td = 'C';
        $totalNotes += 2;
        $totalValues++;
    } else if ($td >= 60 and $td < 70) {
        $td = 'D';
        $totalNotes += 1;
        $totalValues++;
    } else if ($td > 0) {
        $td = 'F';
        $totalNotes += 0;
        $totalValues++;
    }

    if ($student->{"tpc{$number}"} <> 100 && $student->{"tpc{$number}"} !== "" && $student->{"pc{$number}"} > 0) {
        $pc = round(($student->{"pc{$number}"} / $student->{"tpc{$number}"}) * 100);
    } else {
        $pc = $student->{"pc{$number}"};
    }
    if ($pc >= 90) {
        $pc = 'A';
        $totalNotes += 4;
        $totalValues++;
    } else if ($pc >= 80 and $pc < 90) {
        $pc = 'B';
        $totalNotes += 3;
        $totalValues++;
    } else if ($pc >= 70 and $pc < 80) {
        $pc = 'C';
        $totalNotes += 2;
        $totalValues++;
    } else if ($pc >= 60 and $pc < 70) {
        $pc = 'D';
        $totalNotes += 1;
        $totalValues++;
    } else if ($pc > 0) {
        $pc = 'F';
        $totalNotes += 0;
        $totalValues++;
    }

    $TOTAL = ($totalValues > 0) ? number_format(($totalNotes / $totalValues), 2) : 0;
    $pdf->Cell(50, $lineHight, utf8_decode($student->apellidos), 1);
    $pdf->Cell(40, $lineHight, utf8_decode($student->nombre), 1);
    $pdf->SetFont('Arial', '', 8);
    for ($i = $notesNumber[$number][0]; $i <= $notesNumber[$number][1]; $i++) {
        $pdf->Cell(15, $lineHight / 2, (isset($notes[$i]) ? $grades[$i] : ''), "LTR", 0, 'C');
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

    if ($TOTAL >= 3.50) {
        $grade = 'A';
    } else if ($TOTAL >= 2.50 and $TOTAL < 3.50) {
        $grade = 'B';
    } else if ($TOTAL >= 1.60 and $TOTAL < 2.50) {
        $grade = 'C';
    } else if ($TOTAL >= 0.80 and $TOTAL < 1.60) {
        $grade = 'D';
    } else if ($TOTAL >= 0 and $TOTAL < 0.80) {
        $grade = 'F';
    }

    $pdf->Cell(15, $lineHight / 2, ($TOTAL > 0) ? $grade : '', "LBR", 1, 'R');
}

$pdf->SetY(-45);

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
