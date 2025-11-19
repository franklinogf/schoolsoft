<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\Controllers\Parents;
use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

$lang = new Lang([
    ["Lista de bajas", "Drop out list"],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Grado', 'Grade'],
    ['Fecha', 'Date drop out'],
    ['C�digo', 'Code'],
]);

$school = new School();
$year = $school->year();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista de bajas") . " $year", true);
$pdf->Fill();

$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, $lang->translation("Lista de bajas") . " $year", 0, 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 5, '', 1, 0, 'C', true);
$pdf->Cell(10, 5, 'ID', 1, 0, 'C', true);
$pdf->Cell(17, 5, $lang->translation("C�digo"), 1, 0, 'C', true);
$pdf->Cell(55, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
$pdf->Cell(45, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
$pdf->Cell(20, 5, $lang->translation("Grado"), 1, 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Fecha"), 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 10);

$students = DB::table('year')->where([
    ['activo', ''],
    ['codigobaja', '!=', 0],
    ['year', $year]
])->orderBy('apellidos')->get();
$count = 1;
foreach ($students as $student) {
        $pdf->Cell(10, 5, $count, 0, 0, 'C');
        $pdf->Cell(10, 5, $student->id, 0, 0, 'C');
        $pdf->Cell(17, 5, $student->codigobaja, 0, 0, 'C');
        $pdf->Cell(55, 5, utf8_decode($student->apellidos));
        $pdf->Cell(45, 5, utf8_decode($student->nombre));
        $pdf->Cell(20, 5, $student->grado, 0, 0, 'C');
        $pdf->Cell(30, 5, $student->fecha_baja, 0, 1, 'C');
        $count++;
}




$pdf->Output();
