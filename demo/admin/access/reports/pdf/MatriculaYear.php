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
    ['Informe matrícula por grado', 'Enrollment Report by Grade'],
    ['Nombres padres', 'Parents Name'],
    ['Celular', 'Cel-Phone'],
    ['Teléfono', 'Phone'],
    ['Trabajos', 'Works'],
    ['Padres', 'Fathers'],
    ['Madres', 'Mothers'],
]);

function Grado($grado, $ss)
{
    $row = DB::table('year')
        ->whereRaw("ss = '$ss' and grado like '$grado%'")->orderBy('apellidos')->first();
    $s='';
    if(!empty($row->nombre ?? '')){$s='Si';}
    return $s ?? '';
}

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
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 5, $lang->translation(utf8_encode("Informe matrícula por grado")) . " $year", 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(10, 5, '', 1, 0, 'C', true);
        $this->Cell(70, 5, $lang->translation('Nombres del Estudiante'), 1, 0, 'C', true);
        $this->Cell(8, 5, 'PK', 1, 0, 'C', true);
        $this->Cell(8, 5, 'KG', 1, 0, 'C', true);
        $this->Cell(8, 5, '01', 1, 0, 'C', true);
        $this->Cell(8, 5, '02', 1, 0, 'C', true);
        $this->Cell(8, 5, '03', 1, 0, 'C', true);
        $this->Cell(8, 5, '04', 1, 0, 'C', true);
        $this->Cell(8, 5, '05', 1, 0, 'C', true);
        $this->Cell(8, 5, '06', 1, 0, 'C', true);
        $this->Cell(8, 5, '07', 1, 0, 'C', true);
        $this->Cell(8, 5, '08', 1, 0, 'C', true);
        $this->Cell(8, 5, '09', 1, 0, 'C', true);
        $this->Cell(8, 5, '10', 1, 0, 'C', true);
        $this->Cell(8, 5, '11', 1, 0, 'C', true);
        $this->Cell(8, 5, '12', 1, 1, 'C', true);
        $this->SetFont('Arial', '', 10);
    }
}

$pdf = new nPDF();
$pdf->SetTitle($lang->translation(utf8_encode("Informe matrícula por grado")) . " $year", true);
$pdf->Fill();
$pdf->AddPage('');
$pdf->SetFont('Arial', '', 10);

$grade = $_POST['grade'];
$students = DB::table('year')->where([
    ['activo', ''],
    ['year', $year],
    ['grado', $grade]
])->orderBy('apellidos')->get();

$count = 1;
$grupo = '';

$GRADOS = ['PK-','KG-','01-','02-','03-','04','05','06', '07', '08','09','10','11','12'];

foreach ($students as $student) {
      $pdf->Cell(10, 5, $count, 1, 0, 'R');
      $pdf->Cell(70, 5, $student->apellidos.' '.$student->nombre, 1, 0, 'L');
      foreach ($GRADOS as $i => $GRA ) {
              $pdf->Cell(8, 5, Grado($GRA, $student->ss), 1, ($i == count($GRADOS) - 1) ? 1 : 0, 'C');
              }
      $count++;
}

$pdf->Output();
