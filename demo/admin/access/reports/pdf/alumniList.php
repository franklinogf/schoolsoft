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
    ['Lista de exalumnos', 'Alumni list'],
    ['Nombre Estudiante', 'Student name'],
    ['Dirección', 'Address'],
    ['Nombre', 'Name'],
    ['Teláfono', 'Phone'],
    ['Celular', 'Cell phone'],
    ['Madres', 'Mothers'],
]);
$grade = $_POST['grade'] ?? '';

$school = new School(Session::id());
$year = $school->info('year2');

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
        $this->Cell(0, 5, $lang->translation("Lista de exalumnos") . " $year / " . $grupo, 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(10, 5, '', 1, 0, 'C', true);
        $this->Cell(15, 5, 'ID', 1, 0, 'C', true);
        $this->Cell(60, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
        $this->Cell(50, 5, utf8_encode($lang->translation("Dirección")), 1, 0, 'C', true);
        $this->Cell(30, 5, utf8_encode($lang->translation("Teléfono")), 1, 0, 'C', true);
        $this->Cell(25, 5, $lang->translation("Celular"), 1, 1, 'C', true);
        $this->SetFont('Arial', '', 10);
    }
}
$pdf = new nPDF();
$pdf->SetTitle($lang->translation("Lista de exalumnos") . " $year / " . $grupo, true);
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
    if ($parent->ex_m === 'SI') {
        $pdf->Cell(10, 5, $count, 0, 0, 'C');
        $pdf->Cell(15, 5, $student->id, 0, 0, 'C');
        $pdf->Cell(60, 5, $parent->madre, 0, 0, 'L');
        $pdf->Cell(50, 5, $parent->dir1, 0, 0, 'L');
        if (!empty($parent->dir3)) {
            $pdf->Cell(85, 5, '', 0, 0, 'C');
            $pdf->Cell(50, 5, $parent->dir3, 0, 1, 'L');
        }
        $pdf->Cell(30, 5, $parent->tel_m, 0, 0, 'L');
        $pdf->Cell(25, 5, $parent->cel_m, 0, 1, 'L');
        $pdf->Cell(85, 5, '', 0, 0, 'C');
        $pdf->Cell(50, 5, $parent->pueblo1 . ' ' . $parent->est1 . ' ' . $parent->zip1, 0, 1, 'L');
        $count++;
    }
    if ($parent->ex_p === 'SI') {
        $pdf->Cell(10, 5, $count, 0, 0, 'C');
        $pdf->Cell(15, 5, $student->id, 0, 0, 'C');
        $pdf->Cell(60, 5, $parent->padre, 0, 0, 'L');
        $pdf->Cell(50, 5, $parent->dir1, 0, 0, 'L');
        if (!empty($parent->dir3)) {
            $pdf->Cell(85, 5, '', 0, 0, 'C');
            $pdf->Cell(50, 5, $parent->dir3, 0, 1, 'L');
        }
        $pdf->Cell(30, 5, $parent->tel_p, 0, 0, 'L');
        $pdf->Cell(25, 5, $parent->cel_p, 0, 1, 'L');
        $pdf->Cell(85, 5, '', 0, 0, 'C');
        $pdf->Cell(50, 5, $parent->pueblo1 . ' ' . $parent->est1 . ' ' . $parent->zip1, 0, 1, 'L');
        $count++;
    }
}
$pdf->Output();
