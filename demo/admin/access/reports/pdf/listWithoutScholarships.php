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
    ['Lista de no becados', 'List without scholarships'],
    ['Apellidos', 'Surnames'],
    ['Grado', 'Grade'],
    ['Nombre', 'Name'],
    ['Tel&#65533;fono', 'Phone'],
    ['Celular', 'Cell phone'],
    ['Madres', 'Mothers'],
]);
$grade = $_POST['grade'] ?? '';

$school = new School();
$year = $school->info('year');

$grupo = '';
class nPDF extends PDF
{
    function header()
    {
        global $lang;
        global $year;
        global $grupo;
        parent::header();
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 5, $lang->translation("Lista de no becados") . " $year", 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(10, 5, '', 1, 0, 'C', true);
        $this->Cell(15, 5, 'ID', 1, 0, 'C', true);
        $this->Cell(60, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
        $this->Cell(60, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
        $this->Cell(30, 5, $lang->translation("Grado"), 1, 1, 'C', true);
        $this->SetFont('Arial', '', 10);
    }
}
$pdf = new nPDF();
$pdf->SetTitle($lang->translation("Lista de no becados") . " $year", true);
$pdf->Fill();
$grupo = $grade;
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);
$count = 1;
$students = DB::table('year')->where([
    ['activo', ''],
    ['year', $year],
])->orderBy('apellidos')->get();

foreach ($students as $student) {

    $parent = DB::table('madre')->where([
        ['id', $student->id],
    ])->orderBy('id')->first();
    $pdf->Cell(10, 5, $count, 0, 0, 'C');
    $pdf->Cell(15, 5, $student->id, 0, 0, 'C');
    $pdf->Cell(60, 5, $student->apellidos, 0, 0, 'L');
    $pdf->Cell(60, 5, $student->nombre, 0, 0, 'L');
    $pdf->Cell(30, 5, $student->grado, 0, 1, 'L');
    $count++;
}

$pdf->Output();
