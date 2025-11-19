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
    ['Lista de E/P/G/E/T', 'S/P/G/P/C list'],
    ['Nombre Estudiante', 'Student name'],
    ['Grado', 'Grade'],
    ['Correo', 'E-Mail'],
    ['Padres', 'Parents'],
    ['Teléfono', 'Phone'],
    ['Celular', 'Cell Phone'],
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
        $this->Cell(0, 5, $lang->translation("Lista de E/P/G/E/T") . " $year", 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(7, 5, '', 1, 0, 'C', true);
        $this->Cell(15, 5, 'ID', 1, 0, 'C', true);
        $this->Cell(70, 5, $lang->translation("Nombre Estudiante"), 1, 0, 'C', true);
        $this->Cell(55, 5, $lang->translation("Padres"), 1, 0, 'C', true);
        $this->Cell(15, 5, $lang->translation("Grado"), 1, 0, 'C', true);
        $this->Cell(65, 5, $lang->translation("Correo"), 1, 0, 'C', true);
        $this->Cell(25, 5, utf8_encode($lang->translation("Teléfono")), 1, 0, 'C', true);
        $this->Cell(25, 5, $lang->translation("Celular"), 1, 1, 'C', true);
        $this->SetFont('Arial', '', 10);
    }
}
$pdf = new nPDF();
$pdf->SetTitle($lang->translation("Lista de E/P/G/E/T") . " $year / " . $grupo, true);
$pdf->Fill();

$grade = $_POST['grade'] ?? '';
$est = $_POST['est'] ?? '';
$students = DB::table('year')->where([
    ['activo', ''],
    ['year', $year]
])->orderBy('grado, apellidos')->get();

$count = 1;
foreach ($students as $student) {

    $parent = DB::table('madre')->where([
        ['id', $student->id],
    ])->orderBy('id')->first();
    if ($grupo != $student->grado) {
        $grupo = $student->grado;
        $pdf->AddPage('L');
        $pdf->SetFont('Arial', '', 10);
    }
    $pdf->Cell(7, 5, $count, 0, 0, 'C');
    $pdf->Cell(15, 5, $student->id, 0, 0, 'C');
    $pdf->Cell(70, 5, $student->apellidos . ' ' . $student->nombre, 0, 0, 'L');
    $pdf->Cell(55, 5, $parent->madre, 0, 0, 'L');
    $pdf->Cell(15, 5, $student->grado, 0, 0, 'C');
    $pdf->Cell(65, 5, utf8_decode($parent->email_m), 0, 0, 'L');
    $pdf->Cell(25, 5, utf8_decode($parent->tel_m), 0, 0, 'L');
    $pdf->Cell(25, 5, utf8_decode($parent->cel_m), 0, 1, 'C');
    if ($parent->padre != '') {
        $pdf->Cell(92, 5, '', 0, 0, 'C');
        $pdf->Cell(55, 5, $parent->padre, 0, 0, 'L');
        $pdf->Cell(15, 5, '', 0, 0, 'C');
        $pdf->Cell(65, 5, utf8_decode($parent->email_p), 0, 0, 'L');
        $pdf->Cell(25, 5, utf8_decode($parent->tel_p), 0, 0, 'L');
        $pdf->Cell(25, 5, utf8_decode($parent->cel_p), 0, 1, 'C');
    }
    $count++;
}
$pdf->Output();
