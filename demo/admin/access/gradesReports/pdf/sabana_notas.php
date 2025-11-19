<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();


$lang = new Lang([
    ['Sabana de Notas', 'Sheet of Notes'],
    ['Reporte de Notas', 'Grade Report'],
    ['Nombre del estudiante', 'Student name'],
    ['Valor menor < que: ', 'Value less than: '],
    ['Opción', 'Option'],
    ['Continuar', 'Continue'],
    ['Semestre 1', 'Semester 1'],
    ['Semestre 2', 'Semester 2'],
    ['Trimestre 1', 'Quarter 1'],
    ['Trimestre 2', 'Quarter 2'],
    ['Trimestre 3', 'Quarter 3'],
    ['Trimestre 4', 'Quarter 4'],
    ['Notas para Sumar', 'Notes to Add'],
    ['Firmas', 'Signature'],
    ['Grados Separados:', 'Separate grades:'],
    ['Atrás', 'Go back'],
    ['Grado', 'Grade'],
    ['Notas para ver:', 'Notes to see:'],
    ['Maestro', 'Maestro'],
    ['Padre/encargado', 'Parent/Guardian'],
    ['Registradora', 'Registrar'],
    ['Promedio final', 'Final average'],
    ['CURSOS A MEJORAR', 'COURSES TO IMPROVE'],
    ['INFORME DE DEFICIENCIA', 'DEFICIENCY REPORT'],
    
]);
$school = new School(Session::id());
$year = $school->info('year2');

$allGrades = DB::table('padres')->select("DISTINCT grado")->where([
    ['year', $year]
])->orderBy('grado')->get();

function LN($valor)
{
    if ($valor == 'A') {
        return 4;
    } elseif ($valor == 'B') {
        return 3;
    } elseif ($valor == 'C') {
        return 2;
    } elseif ($valor == 'D') {
        return 1;
    } elseif ($valor == 'F') {
        return 0;
    }
}

function NL($valor)
{
    if ($valor == '') {
        return '';
    } else if ($valor >= 88) {
        return 'A';
    } else if ($valor >= 78 && $valor <= 87) {
        return 'B';
    } else if ($valor >= 68 && $valor <= 77) {
        return 'C';
    } else if ($valor >= 60 && $valor <= 67) {
        return 'D';
    } else  if ($valor < 60) {
        return 'F';
    }
}

function NN($valor)
{
    if ($valor == '') {
        return '';
    } else if ($valor >= 88) {
        return 4;
    } else if ($valor >= 78 && $valor <= 87) {
        return 3;
    } else if ($valor >= 68 && $valor <= 77) {
        return 2;
    } else if ($valor >= 60 && $valor <= 67) {
        return 1;
    } else  if ($valor < 60) {
        return 0;
    }
}

$cursos = [
    'ESP' => 'Español',
    'ING' => 'Inglés',
    'MAT' => 'Matemáticas',
    'CIE' => 'Ciencias',
    'SOC' => 'Estudios Sociales',
    'REL' => 'Religión',
    'EDF' => 'Edu. Fícica'
];
$pdf = new PDF();
$pdf->SetLeftMargin(20);
$pdf->Fill();

foreach ($allGrades as $grade) {
    $grado = $grade->grado;
    $pdf->AddPage('L');
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 5, $lang->translation("Sabana de Notas") . ' / ' . $grado . " / $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 11);

    //tabla
    $pdf->Cell(60, 10, $lang->translation('Nombre del estudiante'), 1, 0, 'C', true);
    $pdf->SetFont('Arial', 'B', 6.5);
    foreach ($cursos as $key => $value) {
        $pdf->Cell(26, 5, ucwords(strtolower($value)), 1, 0, 'C', true);
    }
    $pdf->Ln();
    $pdf->Cell(60);
    $pdf->SetFont('Arial', 'B', 8);
    for ($i = 0; $i < sizeof($cursos); $i++) {
        $pdf->Cell(26 / 3, 5, 'N1', 1, 0, 'C', true);
        $pdf->Cell(26 / 3, 5, 'N2', 1, 0, 'C', true);
        $pdf->Cell(26 / 3, 5, 'S1', 1, 0, 'C', true);
    }
    $pdf->Ln();
    $estus = DB::table('padres')->select("DISTINCT nombre, apellidos, ss")->where([
        ['year', $year],
        ['grado', $grado],
    ])->orderBy('apellidos')->get();

    foreach ($estus as $estu) {
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(60, 5, ucwords(strtolower("$estu->apellidos, $estu->nombre")), 1);
        foreach ($cursos as $key => $value) {
            $nota = DB::table('padres')->select("nota1, nota2, sem1")->whereRaw("year='$year' and grado='$grado' and curso LIKE '$key%' and ss='$estu->ss'")->orderBy('apellidos')->first();
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(26 / 3, 5, $nota->nota1 ?? '', 1, 0, 'C');
            $pdf->Cell(26 / 3, 5, $nota->nota2 ?? '', 1, 0, 'C');
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(26 / 3, 5, $nota->sem1 ?? '', 1, 0, 'C');
        }
        $pdf->Ln();
    }
}

$pdf->Output();
