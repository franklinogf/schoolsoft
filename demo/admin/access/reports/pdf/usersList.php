<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

$lang = new Lang([
    ["Lista de usuarios", "Users list"],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Usuario', 'Username'],
    ['Contraseña', 'Password'],
]);

$school = new School();
$year = $school->year();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista de usuarios") . " $year", true);
$pdf->Fill();

$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, $lang->translation("Lista de usuarios") . " $year", 0, 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 5, '', 1, 0, 'C', true);
$pdf->Cell(60, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
$pdf->Cell(60, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Usuario"), 1, 0, 'C', true);
$pdf->Cell(30, 5, utf8_encode($lang->translation("Contraseña")), 1, 1, 'C', true);
$pdf->ln(2);
$pdf->SetFont('Arial', '', 10);

$teachers = DB::table('profesor')->where([
    ['baja', ''],
    ['docente', 'Docente']
])->orderBy('apellidos')->get();
foreach ($teachers as $count => $teacher) {
    $pdf->Cell(10, 5, $count + 1, 0, 0, 'C');
    $pdf->Cell(60, 5, $teacher->apellidos);
    $pdf->Cell(60, 5, $teacher->nombre);
    $pdf->Cell(30, 5, $teacher->usuario, 0, 0, 'C');
    $pdf->Cell(30, 5, $teacher->clave, 0, 1, 'C');
}

$pdf->Output();
