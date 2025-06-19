<?php
require_once '../../../../app.php';

use Classes\Controllers\Parents;
use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

$lang = new Lang([["Lista de cumpleaños", "Birthbay list"],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Grado', 'Grade'],
    ['Fecha', 'Birthbay'],
    ['', 'Code'],
    ['Descuentos', 'Discount'],
]);
$mes = $_POST['mes'];
$grade = $_POST['grade'];
$sg = $_POST['separatedGrade'] ?? '';

$school = new School();
$year = $school->year();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista de cumpleaños") . " $year", true);
$pdf->Fill();

$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, $lang->translation("Lista de cumpleaños") . " $year", 0, 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(8, 5, '', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'ID', 1, 0, 'C', true);
$pdf->Cell(55, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
$pdf->Cell(50, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
$pdf->Cell(20, 5, $lang->translation("Grado"), 1, 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Fecha"), 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 10);

if ($grade=='')
   {
$students = DB::table('year')->where([
    ['activo', ''],
    ['year', $year],
])->orderBy('apellidos')->get();
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
$tcount = 0;
foreach ($students as $student) {
        list($yy,$mon,$day) = explode("-",$student->fecha);
        if ($mon == $mes and $mon != '00' or $mes == '00' and $mon != '00')
           {
           $pdf->Cell(8, 5, $count, 0, 0, 'C');
           $pdf->Cell(15, 5, $student->id, 0, 0, 'C');
      $pdf->Cell(55, 5, $student->apellidos);
      $pdf->Cell(50, 5, $student->nombre);
           $pdf->Cell(20, 5, $student->grado, 0, 0, 'C');
           $pdf->Cell(30, 5, $student->fecha.$sg, 0, 1, 'L');
           $count++;
           }
}


$pdf->Output();
