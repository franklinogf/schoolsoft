<?php
require_once __DIR__ . '/../../../../app.php';

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
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 5, $lang->translation("Lista de cuentas incompletas") . " $year", 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(10, 5, '', 1, 0, 'C', true);
        $this->Cell(50, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
        $this->Cell(50, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
        $this->Cell(15, 5, $lang->translation("Grado"), 1, 0, 'C', true);
        $this->Cell(20, 5, utf8_encode($lang->translation("Usuario")), 1, 0, 'C', true);
        $this->Cell(20, 5, utf8_encode($lang->translation("Contraseña")), 1, 0, 'C', true);
        $this->Cell(15, 5, utf8_encode($lang->translation("Correo M")), 1, 0, 'C', true);
        $this->Cell(15, 5, utf8_encode($lang->translation("Correo P")), 1, 0, 'C', true);
        $this->Cell(15, 5, utf8_encode($lang->translation("Cel M")), 1, 0, 'C', true);
        $this->Cell(20, 5, utf8_encode($lang->translation("Comp cel")), 1, 0, 'C', true);
        $this->Cell(15, 5, utf8_encode($lang->translation("Cel P")), 1, 0, 'C', true);
        $this->Cell(20, 5, utf8_encode($lang->translation("Comp cel")), 1, 0, 'C', true);
        $this->Cell(15, 5, utf8_encode($lang->translation("Usado")), 1, 1, 'C', true);
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
$count2 = 0;

foreach ($students as $student) {
    $parent = DB::table('madre')->where([['id', $student->id]
    ])->orderBy('id')->first();
    $count2 = $count2 + 1;
    if ($count2 == 25) {
        $count2 = 0;
        $pdf->AddPage("L");
    }

    $pdf->Cell(10, 5, $count, 0, 0, 'C');
    $pdf->Cell(50, 5, $student->apellidos);
    $pdf->Cell(50, 5, $student->nombre);
    $pdf->Cell(15, 5, $student->grado, 0, 0, 'C');
    $pdf->Cell(20, 5, $parent->usuario ?? '', 0, 0, 'C');
    $pdf->Cell(20, 5, $parent->clave ?? '', 0, 0, 'C');
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
