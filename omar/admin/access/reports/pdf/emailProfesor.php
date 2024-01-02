<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

$lang = new Lang([
    ["Lista Correos Profesor", "Teacher Mailing list"],
    ['Profesor', 'Teacher'],
    ['Nombre', 'Name'],
    ['Correo', 'E-Mail'],
    ['Contrase�a', 'Password'],
]);

$school = new School();
$year = $school->year();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista Correos Profesor") . " $year", true);
$pdf->Fill();

$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, $lang->translation("Lista Correos Profesor") . " $year", 0, 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(7, 5, '', 1, 0, 'C', true);
$pdf->Cell(50, 5, $lang->translation("Profesor"), 1, 0, 'C', true);
$pdf->Cell(67, 5, $lang->translation("correo"), 1, 0, 'C', true);
$pdf->Cell(67, 5, $lang->translation("correo"), 1, 1, 'C', true);
$pdf->ln(2);
$pdf->SetFont('Arial', '', 10);

$teachers = DB::table('profesor')->where([
    ['baja', ''],
    ['docente', 'Docente']
])->orderBy('apellidos')->get();
foreach ($teachers as $count => $teacher) {
    $pdf->Cell(7, 5, $count + 1, 0, 0, 'C');
    $pdf->Cell(50, 5, utf8_decode($teacher->apellidos).' '.utf8_decode($teacher->nombre));
    $pdf->Cell(67, 5, utf8_decode($teacher->email1), 0, 0, 'C');
    $pdf->Cell(67, 5, utf8_decode($teacher->email2), 0, 1, 'C');
}


$pdf->Output();
