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
    ["Lista de cuentas que acceden", "List of accounts accessing"],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Grado', 'Grade'],
    ['Fecha', 'Date'],
]);

$school = new School();
$year = $school->year();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista de cuentas que acceden") . " $year", true);
$pdf->Fill();

$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, $lang->translation("Lista de cuentas que acceden") . " $year", 0, 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 5, '', 1, 0, 'C', true);
$pdf->Cell(60, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
$pdf->Cell(60, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Grado"), 1, 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Fecha"), 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 10);

$students = DB::table('year')->where([
    ['activo', ''],
    ['year', $year]
])->orderBy('apellidos')->get();
$count = 1;
foreach ($students as $student) {
    $parents = DB::table('madre')->where([
        ['id', $student->id],
    ])->orderBy('id')->get();
    foreach ($parents as $parent) {
       if($parent->ufecha !== '0000-00-00'){
        $pdf->Cell(10, 5, $count, 0, 0, 'C');
            $pdf->Cell(60, 5, $student->apellidos);
            $pdf->Cell(60, 5, $student->nombre);
        $pdf->Cell(30, 5, $student->grado, 0, 0, 'C');
        $pdf->Cell(30, 5, $parent->ufecha, 0, 1, 'C');
        $count++;
       }
    }
}




$pdf->Output();
