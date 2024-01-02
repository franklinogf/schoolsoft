<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

$lang = new Lang([
    ["Lista de usuarios de padres", "List of parents users"],
    ['Madre', 'Mother'],
    ['Padre', 'Father'],
    ['Usuario', 'Username'],
    ['Contraseña', 'Password'],
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
            $this->Cell(0, 5, $lang->translation("Lista de usuarios de padres") . " $year", 0, 1, 'C');
            $this->Ln(5);
        }
        $this->SetX(5);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(10, 5, '', 1, 0, 'C', true);
        $this->Cell(15, 5, 'ID', 1, 0, 'C', true);
        $this->Cell(60, 5, $lang->translation("Madre"), 1, 0, 'C', true);
        $this->Cell(60, 5, $lang->translation("Padre"), 1, 0, 'C', true);
        $this->Cell(30, 5, $lang->translation("Usuario"), 1, 0, 'C', true);
        $this->Cell(25, 5, utf8_decode($lang->translation("Contraseña")), 1, 1, 'C', true);
        $this->SetFont('Arial', '', 10);
    }
}
$pdf = new nPDF();
$pdf->SetTitle($lang->translation("Lista de usuarios de padres") . " $year", true);
$pdf->Fill();
$pdf->AddPage();
$pdf->SetLeftMargin(5);
$pdf->SetX(5);

$students = DB::table('year')->select('DISTINCT id')->where([
    ['activo', ''],
    ['year', $year]
])->orderBy('id')->get();
$count = 1;

foreach ($students as $student) {
    $parents = DB::table('madre')->where([
        ['id', $student->id],
    ])->orderBy('id')->get();

    foreach ($parents as $parent) {
        $pdf->Cell(10, 5, $count, 0, 0, 'C');
        $pdf->Cell(15, 5, $parent->id, 0, 0, 'C');
        $pdf->Cell(60, 5, utf8_decode($parent->madre));
        $pdf->Cell(60, 5, utf8_decode($parent->padre));
        $pdf->Cell(30, 5, utf8_decode($parent->usuario), 0, 0, 'C');
        $pdf->Cell(25, 5, $parent->clave, 0, 1, 'C');
        $count++;
    }
}




$pdf->Output();
