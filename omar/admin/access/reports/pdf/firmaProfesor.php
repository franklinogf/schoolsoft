<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

$lang = new Lang([
    ["Lista de firmas Profesores", "List oe signatures Teachers"],
    ['Profesor', 'Teacher'],
    ['ID', 'ID'],
    ['Celular', 'Cel Phone'],
    ['Firma', 'Signatur'],
]);

$school = new School();
$year = $school->year();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista de firmas Profesores") . " $year", true);
$pdf->Fill();

$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, $lang->translation("Lista de firmas Profesores") . " $year", 0, 1, 'C');
$pdf->Cell(0, 7, '', 'B', 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 5, '', 1, 0, 'C', true);
$pdf->Cell(20, 5, $lang->translation("ID"), 1, 0, 'C', true);
$pdf->Cell(70, 5, $lang->translation("Profesor"), 1, 0, 'C', true);
$pdf->Cell(70, 5, $lang->translation("Firma"), 1, 1, 'C', true);
$pdf->ln(2);
$pdf->SetFont('Arial', '', 10);

$teachers = DB::table('profesor')->where([
    ['baja', ''],
    ['docente', 'Docente']
])->orderBy('apellidos')->get();
foreach ($teachers as $count => $teacher) {
    $pdf->Cell(10, 7, $count + 1, 0, 0, 'C');
    $pdf->Cell(20, 7, $teacher->id, 0, 0, 'C');
    $pdf->Cell(70, 7, $teacher->apellidos . ' ' . $teacher->nombre);
    $pdf->Cell(70, 7, '', 'B', 1, 'C');
}


$pdf->Output();
