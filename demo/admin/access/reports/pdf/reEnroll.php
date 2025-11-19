<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

$lang = new Lang([
    ["Lista de rematricula", "Re-enrollment list"],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Grado', 'Grade'],
]);

$school = new School();
$year = $school->year();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista de rematricula") . " $year", true);
$pdf->Fill();

$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, $lang->translation("Lista de rematricula") . " $year", 0, 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 5, '', 1, 0, 'C', true);
$pdf->Cell(70, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
$pdf->Cell(70, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Grado"), 1, 1, 'C', true);
$pdf->ln(2);
$pdf->SetFont('Arial', '', 10);

$students = DB::table('year')->where([
    ['rema', 'SI'],
    ['activo', ''],
    ['year', $year]
])->orderBy('apellidos')->get();
foreach ($students as $count => $student) {
    $pdf->Cell(10, 5, $count + 1, 0, 0, 'C');
    $pdf->Cell(70, 5, utf8_decode($student->apellidos));
    $pdf->Cell(70, 5, utf8_decode($student->nombre));
    $pdf->Cell(30, 5, $student->grado, 0, 1, 'C');
}




$pdf->Output();
