<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();

$lang = new Lang([
    ['Reporte de Notas', 'Grade Report'],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["A&#65533;o escolar:", "School year:"],
    ["DESCRIPCION", "DESCRIPTION"],
    ['PRIMER SEMESTRE', 'FIRST SEMESTER'],
    ['SEGUNDO SEMESTRE', 'SECOND SEMESTER'],
    ['PRO', 'AVE'],
    ['PROMEDIO:', 'AVERAGE:'],
    ['Nombre:', 'Name:'],
    ['Total de estudiantes', 'Total students'],
    ['Fecha:', 'Date:'],
    ['Documentos sin entregar', 'Undelivered documents'],
    ['Masculinos', 'Males'],
    ['Femeninas', 'Females'],
]);

function NLetra($valor)
{
    if ($valor == '') {
        return '';
    } else if ($valor <= '100' && $valor >= '90') {
        return 'A';
    } else if ($valor <= '89' && $valor >= '80') {
        return 'B';
    } else if ($valor <= '79' && $valor >= '70') {
        return 'C';
    } else if ($valor <= '69' && $valor >= '60') {
        return 'D';
    } else  if ($valor <= '59') {
        return 'F';
    }
}


$pdf = new PDF();
$pdf->useFooter(false);
$school = new School();
$teacherClass = new Teacher();
$studentClass = new Student();

$year = $school->year();
// $allGrades = $school->allGrades();
$pdf = new PDF();
$pdf->useFooter(false);

$pdf->SetTitle($lang->translation("Reporte de Notas") . " $year", true);
$pdf->Fill();

$grade = $_POST['grade'];
$men = $_POST['mensaje'];
$mensaj = DB::table('codigos')->where([
    ['codigo', $men],
])->orderBy('codigo')->first();

$teacher = $teacherClass->findByGrade($grade);
$students = $studentClass->findByGrade($grade);

foreach ($students as $estu) {
    $pdf->AddPage('');
    $pdf->useFooter(false);
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Reporte de Notas") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 12);
    $pdf->splitCells($lang->translation("A&#65533;o escolar:") . " $year", $lang->translation("Fecha:") . " " . date("m-d-Y"));

    $materias = [];
    $cursos = [];
    $estudiantes = [];
    $pdf->Cell(120, 5, $lang->translation("Nombre:") . " $estu->nombre $estu->apellidos", 1, 0, 'L', true);
    $pdf->Cell(40, 5, "S.S. XXX-XX-XXXX", 1, 0, 'L', true);
    $pdf->Cell(30, 5, $lang->translation("Grado:") . " $estu->grado", 1, 1, 'L', true);
    $pdf->Cell(60, 5, $lang->translation("DESCRIPCION"), 1, 0, 'C', true);
    $pdf->Cell(51, 5, $lang->translation("PRIMER SEMESTRE"), 1, 0, 'C', true);
    $pdf->Cell(51, 5, $lang->translation("SEGUNDO SEMESTRE"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $lang->translation("PRO"), 1, 0, 'C', true);
    $pdf->Cell(13, 5, "CRS", 1, 1, 'C', true);
    
    $pdf->SetFillColor(89, 171, 227);
    $cursos = DB::table('padres')->where([
        ['year', $year],
        ['ss', $estu->ss],
        ['grado', $grade],
        ['curso', '!=', ''],
        ['curso', 'NOT LIKE', '%AA-%']
    ])->orderBy('orden')->get();
    $crs = 0;
    foreach ($cursos as $curso) {
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(60, 5, $curso->descripcion, 1, 0, 'L');
        $pdf->Cell(10, 5, $curso->nota1, 1, 0, 'R');
        $pdf->Cell(7, 5, NLetra($curso->nota1), 1, 0, 'C');
        $pdf->Cell(10, 5, $curso->nota2, 1, 0, 'R');
        $pdf->Cell(7, 5, NLetra($curso->nota2), 1, 0, 'C');
        $pdf->Cell(10, 5, $curso->sem1, 1, 0, 'R');
        $pdf->Cell(7, 5, NLetra($curso->sem1), 1, 0, 'C');
        $pdf->Cell(10, 5, $curso->nota3, 1, 0, 'R');
        $pdf->Cell(7, 5, NLetra($curso->nota3), 1, 0, 'C');
        $pdf->Cell(10, 5, $curso->nota4, 1, 0, 'R');
        $pdf->Cell(7, 5, NLetra($curso->nota4), 1, 0, 'C');
        $pdf->Cell(10, 5, $curso->sem2, 1, 0, 'R');
        $pdf->Cell(7, 5, NLetra($curso->sem2), 1, 0, 'C');
        $pdf->Cell(9, 5, $curso->final, 1, 0, 'R');
        $pdf->Cell(6, 5, NLetra($curso->final), 1, 0, 'C');
        $pdf->Cell(13, 5, number_format($curso->credito, 2), 1, 1, 'R');
        $pdf->SetFont('Arial', '', 8);
        $crs = $crs + number_format($curso->credito, 2);


        $pdf->Cell(60, 5, "   " . $curso->profesor, 1, 0, 'L');
        $pdf->Cell(10, 5, '', 1, 0, 'R');
        $pdf->Cell(7, 5, '', 1, 0, 'R');
        $pdf->Cell(10, 5, '', 1, 0, 'R');
        $pdf->Cell(7, 5, '', 1, 0, 'R');
        $pdf->Cell(10, 5, '', 1, 0, 'R');
        $pdf->Cell(7, 5, '', 1, 0, 'R');
        $pdf->Cell(10, 5, '', 1, 0, 'R');
        $pdf->Cell(7, 5, '', 1, 0, 'R');
        $pdf->Cell(10, 5, '', 1, 0, 'R');
        $pdf->Cell(7, 5, '', 1, 0, 'R');
        $pdf->Cell(10, 5, '', 1, 0, 'R');
        $pdf->Cell(7, 5, '', 1, 0, 'R');
        $pdf->Cell(9, 5, '', 1, 0, 'R');
        $pdf->Cell(6, 5, '', 1, 0, 'R');
        $pdf->Cell(13, 5, '', 1, 1, 'R');
    }
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(60, 5, $lang->translation("PROMEDIO:"), 1, 0, 'R', true);
    $pdf->Cell(10, 5, '', 1, 0, 'R', true);
    $pdf->Cell(7, 5, '', 1, 0, 'R', true);
    $pdf->Cell(10, 5, '', 1, 0, 'R', true);
    $pdf->Cell(7, 5, '', 1, 0, 'R', true);
    $pdf->Cell(10, 5, '', 1, 0, 'R', true);
    $pdf->Cell(7, 5, '', 1, 0, 'R', true);
    $pdf->Cell(10, 5, '', 1, 0, 'R', true);
    $pdf->Cell(7, 5, '', 1, 0, 'R', true);
    $pdf->Cell(10, 5, '', 1, 0, 'R', true);
    $pdf->Cell(7, 5, '', 1, 0, 'R', true);
    $pdf->Cell(10, 5, '', 1, 0, 'R', true);
    $pdf->Cell(7, 5, '', 1, 0, 'R', true);
    $pdf->Cell(9, 5, '', 1, 0, 'R', true);
    $pdf->Cell(6, 5, '', 1, 0, 'R', true);
    $pdf->Cell(13, 5, number_format($crs, 2), 1, 1, 'R', true);
    $pdf->Ln(1);
    $pdf->Cell(190, 7, $mensaj->t1e, 'LRT', 1, 'L');
    $pdf->Cell(190, 7, $mensaj->t2e, 'LRB', 1, 'L');



}

$pdf->Output();