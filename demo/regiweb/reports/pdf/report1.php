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

$notes = [
    'A' => 0,
    'B' => 0,
    'C' => 0,
    'D' => 0,
    'F' => 0,
    'O' => 0
];
$_info = [
    'Trimestre-1' => [
        'totalGrade' => 'nota1',
        'grades' => [1, 10],
        'values' => [
            'tdia' => 'td1',
            'tlib' => 'tl1',
            'pcor' => 'pc1',
            'tpa' => 'tpa1',
            'tdp' => 'por1'
        ]
    ],
    'Trimestre-2' => [
        'totalGrade' => 'nota2',
        'grades' => [11, 20],
        'values' => [
            'tdia' => 'td2',
            'tlib' => 'tl2',
            'pcor' => 'pc2',
            'tpa' => 'tpa2',
            'tdp' => 'por2'
        ]
    ],
    'Trimestre-3' => [
        'totalGrade' => 'nota3',
        'grades' => [21, 30],
        'values' => [
            'tdia' => 'td3',
            'tlib' => 'tl3',
            'pcor' => 'pc3',
            'tpa' => 'tpa3',
            'tdp' => 'por3'
        ]
    ],
    'Trimestre-4' => [
        'totalGrade' => 'nota4',
        'grades' => [31, 40],
        'values' => [
            'tdia' => 'td4',
            'pcor' => 'pc4',
            'tlib' => 'tl4',
            'tpa' => 'tpa4',
            'tdp' => 'por4'
        ]
    ]


];

$fields = [
    "Trimestre-1" => "nota1",
    "Trimestre-2" => "nota2",
    "Trimestre-3" => "nota3",
    "Trimestre-4" => "nota4"
];
$thisReport = $_info[$_trimester];

$pdf = new PDF();
$pdf->footer = false;
$pdf->SetLeftMargin(5);
$pdf->AddPage();
$pdf->SetTitle($lang->translation("Notas"));
$pdf->Fill();

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, $lang->translation("Notas"), 0, 1, 'C');
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
$pdf->Cell(20, 5, number_format($teacher->classCredit($_class), 2), 1, 0, 'C');
$pdf->Cell(20, 5, count($students), 1, 0, 'C');
$pdf->Cell(25, 5, $lang->trimesterTranslation($_trimester), 1, 0, 'C');
$pdf->Cell(25, 5, Util::formatDate(Util::date()), 1, 1, 'C');

$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(50, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
$pdf->Cell(35, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
$pdf->Cell(7, 5, __LANG === 'es' ? 'N1' : 'G1', 1, 0, 'C', true);
$pdf->Cell(7, 5, __LANG === 'es' ? 'N2' : 'G2', 1, 0, 'C', true);
$pdf->Cell(7, 5, __LANG === 'es' ? 'N3' : 'G3', 1, 0, 'C', true);
$pdf->Cell(7, 5, __LANG === 'es' ? 'N4' : 'G4', 1, 0, 'C', true);
$pdf->Cell(7, 5, __LANG === 'es' ? 'N5' : 'G5', 1, 0, 'C', true);
$pdf->Cell(7, 5, __LANG === 'es' ? 'N6' : 'G6', 1, 0, 'C', true);
$pdf->Cell(7, 5, __LANG === 'es' ? 'N7' : 'G7', 1, 0, 'C', true);
$pdf->Cell(7, 5, __LANG === 'es' ? 'N8' : 'G8', 1, 0, 'C', true);
$pdf->Cell(7, 5, __LANG === 'es' ? 'N9' : 'G9', 1, 0, 'C', true);
$pdf->Cell(7, 5, 'Bon', 1, 0, 'C', true);
$pdf->Cell(7, 5, __LANG === 'es' ? 'T-L' : 'HW', 1, 0, 'C', true);
$pdf->Cell(7, 5, __LANG === 'es' ? 'T-D' : 'D-W', 1, 0, 'C', true);
$pdf->Cell(7, 5, __LANG === 'es' ? 'P-C' : 'Quiz', 1, 0, 'C', true);
$pdf->Cell(7, 5, 'TPA', 1, 0, 'C', true);
$pdf->Cell(7, 5, 'TDP', 1, 0, 'C', true);
$pdf->Cell(10, 5, $lang->translation("Nota"), 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 7);
foreach ($students as $student) {
    $pdf->Cell(50, 5, utf8_decode($student->apellidos), 1);
    $pdf->Cell(35, 5, utf8_decode($student->nombre), 1);
    for ($i = $thisReport['grades'][0]; $i <= $thisReport['grades'][1]; $i++) {
        $pdf->Cell(7, 5, $student->{"not$i"}, 1, 0, 'C');
    }
    $pdf->Cell(7, 5, $student->{$thisReport['values']['tlib']}, 1, 0, 'C');
    $pdf->Cell(7, 5, $student->{$thisReport['values']['tdia']}, 1, 0, 'C');
    $pdf->Cell(7, 5, $student->{$thisReport['values']['pcor']}, 1, 0, 'C');
    $pdf->Cell(7, 5, $student->{$thisReport['values']['tpa']}, 1, 0, 'C');
    $pdf->Cell(7, 5, $student->{$thisReport['values']['tdp']}, 1, 0, 'C');
    $pdf->Cell(10, 5, $student->{$thisReport['totalGrade']}, 1, 1, 'C');

    if ($student->{$fields[$_trimester]} >= $teacher->info("vala")) {
        $notes['A']++;
    }
    if ($student->{$fields[$_trimester]} >= $teacher->info("valb") and $student->{$fields[$_trimester]} < $teacher->info("vala")) {
        $notes['B']++;
    }
    if ($student->{$fields[$_trimester]} >= $teacher->info("valc") and $student->{$fields[$_trimester]} < $teacher->info("valb")) {
        $notes['C']++;
    }
    if ($student->{$fields[$_trimester]} >= $teacher->info("vald") and $student->{$fields[$_trimester]} < $teacher->info("valc")) {
        $notes['D']++;
    }
    if ($student->{$fields[$_trimester]} >= $teacher->info("valf") and $student->{$fields[$_trimester]} < $teacher->info("vald")) {
        $notes['F']++;
    }
    if ($student->{$fields[$_trimester]} < 0 or $student->{$fields[$_trimester]} == '') {
        $notes['O']++;
    }
}

$pdf->SetY(-55);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 5, $lang->translation("Distribución de Notas"), 1, 0, 'C', true);
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
$pdf->Cell(15, 5, $lang->translation('Otros'), 1, 0, 'C', true);
$pdf->Cell(12, 5, $notes['O'], 1, 1, 'C');

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
