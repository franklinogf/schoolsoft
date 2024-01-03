<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

$lang = new Lang([
    ['Lista No docentes', 'Not teachers list'],
    ['Profesor', 'Teacher'],
    ['Nombre', 'Name'],
    ['Teléfonos', 'Phone'],
    ['Departamento', 'Deparment'],
]);
$prof = $_POST['prof'];
$school = new School();
$year = $school->year();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista No docentes") . " $year", true);
$pdf->Fill();

$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, $lang->translation("Lista No docentes") . " $year", 0, 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 5, '', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'ID', 1, 0, 'C', true);
$pdf->Cell(70, 5, $lang->translation("Profesor"), 1, 0, 'C', true);
$pdf->Cell(50, 5, $lang->translation("Teléfonos"), 1, 0, 'C', true);
$pdf->Cell(40, 5, $lang->translation("Departamento"), 1, 1, 'C', true);
$pdf->ln(2);
$pdf->SetFont('Arial', '', 10);

$teachers = DB::table('profesor')->where([
    ['baja', ''],
    ['docente', 'No Docente']
])->orderBy('apellidos')->get();
foreach ($teachers as $count => $teacher) {
    $pdf->Cell(10, 5, $count + 1, 0, 0, 'C');
    $pdf->Cell(20, 5, $teacher->id, 0, 0, 'C');
    $pdf->Cell(70, 5, utf8_decode($teacher->apellidos).' '.utf8_decode($teacher->nombre));
    $pdf->Cell(50, 5, $teacher->cel.' / '.$teacher->tel_res, 0, 0, 'L');
    $pdf->Cell(40, 5, utf8_decode($teacher->dep_des), 0, 1, 'L');

}


$pdf->Output();
