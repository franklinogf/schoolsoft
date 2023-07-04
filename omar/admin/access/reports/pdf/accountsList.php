<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Util;

Session::is_logged();

$lang = new Lang([
    ["Lista de cuentas", "Accounts list"],
    ['Cuenta', 'Account'],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Grado', 'Grade'],
    ['Genero', 'Gender'],
    ['Fecha N.', 'DOB'],
]);

$school = new School();
$year = $school->info('year');
class nPDF extends PDF
{
    function header()
    {
        global $lang;
        global $year;
        parent::header();
        if ($this->pageNo() === 1) {
            $this->SetFont('Arial', 'B', 15);
            $this->Cell(0, 5, $lang->translation("Lista de cuentas") . " $year", 0, 1, 'C');
            $this->Ln(5);
        }
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(10, 5, '', 1, 0, 'C', true);
        $this->Cell(20, 5, utf8_decode($lang->translation("Cuenta")), 1, 0, 'C', true);
        $this->Cell(50, 5, utf8_decode($lang->translation("Apellidos")), 1, 0, 'C', true);
        $this->Cell(50, 5, utf8_decode($lang->translation("Nombre")), 1, 0, 'C', true);
        $this->Cell(15, 5, $lang->translation("Grado"), 1, 0, 'C', true);
        $this->Cell(15, 5, $lang->translation("Genero"), 1, 0, 'C', true);
        $this->Cell(25, 5, $lang->translation("Fecha N."), 1, 1, 'C', true);

        $this->SetFont('Arial', '', 10);
    }
}
$pdf = new nPDF();
$pdf->SetTitle($lang->translation("Lista de cuentas") . " $year", true);
$pdf->Fill();
$pdf->AddPage();

$students = DB::table('year')->where([
    ['activo', ''],
    ['year', $year]
])->orderBy('id,apellidos')->get();
$count = 1;
$prevId = '';
foreach ($students as $student) {
    if($prevId !== $student->id){
        $prevId = $student->id;
        $newCount = $count++;
    }else{
        $newCount = '';
    }
    $pdf->Cell(10, 5, $newCount, $newCount !== '' ? 'T' : 0, 0, 'C');
    $pdf->Cell(20, 5, $student->id, $newCount !== '' ? 'T' : 0, 0, 'R');
    $pdf->Cell(50, 5, utf8_decode($student->apellidos),$newCount !== '' ? 'T' : 0);
    $pdf->Cell(50, 5, utf8_decode($student->nombre),$newCount !== '' ? 'T' : 0);
    $pdf->Cell(15, 5, $student->grado, $newCount !== '' ? 'T' : 0, 0, 'C');
    $pdf->Cell(15, 5, Util::gender($student->genero), $newCount !== '' ? 'T' : 0, 0, 'C');
    $pdf->Cell(25, 5, Util::formatDate($student->fecha,"%Y-%m-%d"),$newCount !== '' ? 'T' : 0, 1, 'C');
}




$pdf->Output();
