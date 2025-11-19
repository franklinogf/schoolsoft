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
    ['Lista de padres', 'Parents List'],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Grado', 'Grade'],
    ['Padres', 'Fathers'],
    ['Madres', 'Mothers'],
]);
$grade = $_POST['grade'];
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
            $this->Cell(0, 5, $lang->translation("Lista de padres") . " $year", 0, 1, 'C');
            $this->Ln(5);
        }
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(8, 5, '', 1, 0, 'C', true);
        $this->Cell(15, 5, 'ID', 1, 0, 'C', true);
        $this->Cell(55, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
        $this->Cell(50, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
        $this->Cell(20, 5, $lang->translation("Grado"), 1, 0, 'C', true);
        $this->Cell(65, 5, $lang->translation("Padres"), 1, 0, 'C', true);
        $this->Cell(65, 5, $lang->translation("Madres"), 1, 1, 'C', true);
        $this->SetFont('Arial', '', 10);
    }
}
$pdf = new nPDF();
$pdf->SetTitle($lang->translation("Lista de padres") . " $year", true);
$pdf->Fill();

$grade = $_POST['grade'];
if ($grade=='')
   {
$students = DB::table('year')->where([
    ['activo', ''],
    ['year', $year],
])->orderBy('grado, apellidos')->get();
   }
else
   {
$students = DB::table('year')->where([
    ['activo', ''],
    ['year', $year],
    ['grado', $grade],
])->orderBy('apellidos')->get();
   }

$count = 1;
$grupo = '';
foreach ($students as $student) {

$parent = DB::table('madre')->where([
    ['id', $student->id],
])->orderBy('id')->first();
        if ($grupo != $student->grado)
           {
           $grupo = $student->grado;
           $pdf->AddPage('L');
           $pdf->SetFont('Arial', '', 10);
           }

        if ($parent->email_m != '' or $parent->email_p != '')
           {
           $pdf->Cell(8, 5, $count, 0, 0, 'C');
           $pdf->Cell(15, 5, $student->id, 0, 0, 'C');
        $pdf->Cell(55, 5, $student->apellidos);
        $pdf->Cell(50, 5, $student->nombre);
           $pdf->Cell(20, 5, $student->grado, 0, 0, 'C');
        $pdf->Cell(65, 5, $parent->padre, 0, 0, 'L');
        $pdf->Cell(65, 5, $parent->madre, 0, 1, 'L');
           $count++;
           }
}


$pdf->Output();
