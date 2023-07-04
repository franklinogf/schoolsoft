<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

$lang = new Lang([
    ["Preparaci�n de Maestros", "Teachers preparation"],
    ['Profesor', 'Teacher'],
    ['ID', 'ID'],
    ['Celular', 'Cel Phone'],
    ['Preparaci�n', 'Preparation'],
]);

$school = new School();
$year = $school->info('year');
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Preparaci�n de Maestros") . " $year", true);
$pdf->Fill();

$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, $lang->translation("Preparaci�n de Maestros") . " $year", 0, 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 5, '', 1, 0, 'C', true);
$pdf->Cell(10, 5, $lang->translation("ID"), 1, 0, 'C', true);
$pdf->Cell(60, 5, $lang->translation("Profesor"), 1, 0, 'C', true);
$pdf->Cell(110, 5, $lang->translation("Preparaci�n"), 1, 1, 'C', true);
$pdf->ln(2);
$pdf->SetFont('Arial', '', 10);

$teachers = DB::table('profesor')->where([
    ['baja', ''],
    ['grado', '!=', ''],
    ['docente', 'Docente']
])->orderBy('apellidos')->get();
foreach ($teachers as $count => $teacher) {
    $pdf->Cell(10, 5, $count + 1, 0, 0, 'C');
    $pdf->Cell(10, 5, $teacher->id, 0, 0, 'C');
    $pdf->Cell(60, 5, utf8_decode($teacher->apellidos).' '.utf8_decode($teacher->nombre));
    $pdf->Cell(55, 5, utf8_decode($teacher->preparacion1), 0, 0, 'L');
    $pdf->Cell(55, 5, utf8_decode($teacher->preparacion2), 0, 1, 'L');
}


$pdf->Output();
