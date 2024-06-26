<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

$lang = new Lang([
    ["Lista de cuentas incompletas", "List of incomplete accounts"],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Grado', 'Grade'],
    ['Usuario', 'Username'],
    ['Contraseña', 'Password'],
    ['Correo M', 'Email M'],
    ['Cel M', 'Cel M'],
    ['Correo P', 'Email F'],
    ['Cel P', 'Cel F'],
    ['Usado', 'Used'],
    ['Comp cel', 'Comp cel'],
]);

$school = new School();
$year = $school->year();
class nPDF extends PDF
{
    function header()
    {
        global $lang;
        global $year;
        parent::header();
        if ($this->pageNo() === 1) {
            $this->SetFont('Arial', 'B', 15);
            $this->Cell(0, 5, $lang->translation("Lista de cuentas incompletas") . " $year", 0, 1, 'C');
            $this->Ln(5);
        }
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(10, 5, '', 1, 0, 'C', true);
        $this->Cell(50, 5, utf8_decode($lang->translation("Apellidos")), 1, 0, 'C', true);
        $this->Cell(50, 5, utf8_decode($lang->translation("Nombre")), 1, 0, 'C', true);
        $this->Cell(15, 5, $lang->translation("Grado"), 1, 0, 'C', true);
        $this->Cell(20, 5, utf8_decode($lang->translation("Usuario")), 1, 0, 'C', true);
        $this->Cell(20, 5, utf8_decode($lang->translation("Contraseña")), 1, 0, 'C', true);
        $this->Cell(15, 5, utf8_decode($lang->translation("Correo M")), 1, 0, 'C', true);
        $this->Cell(15, 5, utf8_decode($lang->translation("Correo P")), 1, 0, 'C', true);
        $this->Cell(15, 5, utf8_decode($lang->translation("Cel M")), 1, 0, 'C', true);
        $this->Cell(20, 5, utf8_decode($lang->translation("Comp cel")), 1, 0, 'C', true);
        $this->Cell(15, 5, utf8_decode($lang->translation("Cel P")), 1, 0, 'C', true);
        $this->Cell(20, 5, utf8_decode($lang->translation("Comp cel")), 1, 0, 'C', true);
        $this->Cell(15, 5, utf8_decode($lang->translation("Usado")), 1, 1, 'C', true);
        $this->SetFont('Arial', '', 10);
    }
}
$pdf = new nPDF();
$pdf->SetTitle($lang->translation("Lista de cuentas incompletas") . " $year", true);
$pdf->Fill();
$pdf->AddPage("L");

$students = DB::table('year')->where([
    ['activo', ''],
    ['year', $year]
])->orderBy('grado,apellidos')->get();
$count = 1;

foreach ($students as $student) {
    $parent = DB::table('madre')->where([
        ['id', $student->id],
    ])->orderBy('id')->first();

    $pdf->Cell(10, 5, $count, 0, 0, 'C');
    $pdf->Cell(50, 5, utf8_decode($student->apellidos));
    $pdf->Cell(50, 5, utf8_decode($student->nombre));
    $pdf->Cell(15, 5, $student->grado, 0, 0, 'C');
    $pdf->Cell(20, 5, empty($parent->usuario) ? 'X' : '', 0, 0, 'C');
    $pdf->Cell(20, 5, empty($parent->clave) ? 'X' : '', 0, 0, 'C');
    $pdf->Cell(15, 5, empty($parent->email_m) ? 'X' : '', 0, 0, 'C');
    $pdf->Cell(15, 5, empty($parent->email_p) ? 'X' : '', 0, 0, 'C');
    $pdf->Cell(15, 5, empty($parent->cel_m) ? 'X' : '', 0, 0, 'C');
    $pdf->Cell(20, 5, empty($parent->cel_com_m) ? 'X' : '', 0, 0, 'C');
    $pdf->Cell(15, 5, empty($parent->cel_p) ? 'X' : '', 0, 0, 'C');
    $pdf->Cell(20, 5, empty($parent->cel_com_p) ? 'X' : '', 0, 0, 'C');
    $pdf->Cell(15, 5, empty($parent->ufecha) ? 'X' : '', 0, 1, 'C');
    $count++;
}




$pdf->Output();
