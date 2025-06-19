<?php
require_once '../../../../app.php';

use Classes\Controllers\Parents;
use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

$lang = new Lang([
    ['Lista trabajo de padres', 'Parents work List'],
    ['Nombres padres', 'Parents Name'],
    ['Celular', 'Cel-Phone'],
    ['Teléfono', 'Phone'],
    ['Trabajos', 'Works'],
    ['Padres', 'Fathers'],
    ['Madres', 'Mothers'],
]);
$grade = $_POST['grade'];
$school = new School(Session::id());
$year = $school->info('year2');

class nPDF extends PDF
{
    function header()
    {
        global $lang;
        global $year;
        parent::header();
        if ($this->pageNo() === 1) {
            $this->SetFont('Arial', 'B', 15);
            $this->Cell(0, 5, $lang->translation("Lista trabajo de padres") . " $year", 0, 1, 'C');
            $this->Ln(5);
        }
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(10, 5, '', 1, 0, 'C', true);
        $this->Cell(15, 5, 'ID', 1, 0, 'C', true);
        $this->Cell(70, 5, $lang->translation("Nombres padres"), 1, 0, 'C', true);
        $this->Cell(35, 5, $lang->translation("Celular"), 1, 0, 'C', true);
        $this->Cell(35, 5, $lang->translation("Teléfono"), 1, 0, 'C', true);
        $this->Cell(80, 5, $lang->translation("Trabajos"), 1, 1, 'C', true);
//        $this->Cell(65, 5, $lang->translation("Padres"), 1, 0, 'C', true);
//        $this->Cell(65, 5, $lang->translation("Madres"), 1, 1, 'C', true);
        $this->SetFont('Arial', '', 10);
    }
}
$pdf = new nPDF();
$pdf->SetTitle($lang->translation("Lista trabajo de padres") . " $year", true);
$pdf->Fill();

$grade = $_POST['grade'];
if ($grade=='')
   {
$students = DB::table('year')->where([
    ['activo', ''],
    ['year', $year]
])->orderBy('grado, apellidos')->get();
   }
else
   {
$students = DB::table('year')->where([
    ['activo', ''],
    ['year', $year],
    ['grado', $grade]
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

        if ($parent->trabajo_m != '' or $parent->trabajo_p != '')
           {
           $pdf->Cell(10, 5, $count, 0, 0, 'C');
           $pdf->Cell(15, 5, $student->id, 0, 0, 'C');
        $pdf->Cell(70, 5, $parent->madre, 0, 0, 'L');
           $pdf->Cell(35, 5, $parent->cel_m, 0, 0, 'L');
           $pdf->Cell(35, 5, $parent->tel_t_m, 0, 0, 'L');
        $pdf->Cell(70, 5, $parent->trabajo_m, 0, 1, 'L');
           $pdf->Cell(25, 5, '', 0, 0, 'C');
        $pdf->Cell(70, 5, $parent->padre, 0, 0, 'L');
           $pdf->Cell(35, 5, $parent->cel_p, 0, 0, 'L');
           $pdf->Cell(35, 5, $parent->tel_t_p, 0, 0, 'L');
        $pdf->Cell(70, 5, $parent->trabajo_p, 0, 1, 'L');
           $count++;
           }
}


$pdf->Output();
