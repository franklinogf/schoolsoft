<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

$lang = new Lang([["Lista Teléfono Profesor", "Teacher phone list"],
    ['Profesor', 'Teacher'],
    ['Casa', 'Home'],
    ['Celular', 'Cel Phone'],
    ['Contraseña', 'Password'],
]);

$school = new School();
$year = $school->year();
$pdf = new PDF();
$pdf->SetTitle(utf8_encode($lang->translation("Lista Teléfono Profesor")) . " $year", true);
$pdf->Fill();

$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, utf8_encode($lang->translation("Lista Teléfono Profesor")) . " $year", 0, 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 5, '', 1, 0, 'C', true);
$pdf->Cell(70, 5, $lang->translation("Profesor"), 1, 0, 'C', true);
$pdf->Cell(40, 5, $lang->translation("casa"), 1, 0, 'C', true);
$pdf->Cell(40, 5, $lang->translation("celular"), 1, 1, 'C', true);
$pdf->ln(2);
$pdf->SetFont('Arial', '', 10);

$teachers = DB::table('profesor')->where([
    ['baja', ''],
    ['docente', 'Docente']
])->orderBy('apellidos')->get();
foreach ($teachers as $count => $teacher) {
    $pdf->Cell(10, 5, $count + 1, 0, 0, 'C');
    $pdf->Cell(70, 5, $teacher->apellidos . ' ' . $teacher->nombre);
    $pdf->Cell(40, 5, utf8_decode($teacher->tel_res), 0, 0, 'C');
    $pdf->Cell(40, 5, utf8_decode($teacher->cel), 0, 1, 'C');
}


$pdf->Output();
