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
    ["Lista de descuentos", "Discount list"],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Grado', 'Grade'],
    ['Fecha', 'Date drop out'],
   ['Código', 'Code'],
    ['Descuentos', 'Discount'],
]);

$school = new School();
$year = $school->year();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista de descuentos") . " $year", true);
$pdf->Fill();

$pdf->AddPage('L');
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, $lang->translation("Lista de descuentos") . " $year", 0, 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(7, 5, '', 1, 0, 'C', true);
$pdf->Cell(10, 5, 'ID', 1, 0, 'C', true);
$pdf->Cell(55, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
$pdf->Cell(45, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
$pdf->Cell(12, 5, $lang->translation("Grado"), 1, 0, 'C', true);
$pdf->Cell(150, 5, $lang->translation("Descuentos"), 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 10);

$students = DB::table('year')->where([
    ['activo', ''],
    ['desc_men', '!=', ''],
    ['year', $year]
])->orderBy('apellidos')->get();
$count = 1;
$tcount = 0;

foreach ($students as $student) {
        $pdf->Cell(7, 5, $count, 0, 0, 'C');
        $pdf->Cell(10, 5, $student->id, 0, 0, 'C');
   $pdf->Cell(55, 5, $student->apellidos);
   $pdf->Cell(45, 5, $student->nombre);
        $pdf->Cell(12, 5, $student->grado, 0, 0, 'C');
   if ($student->desc_men != '')
           {
      $tcount = $tcount + number_format($student->desc_men, 2);
      $pdf->Cell(50, 5, $student->desc1 . ' ' . $student->desc_men, 0, 0, 'L');
           }
   if ($student->desc_mat != '')
           {
      $pdf->Cell(50, 5, $student->desc2 . ' ' . $student->desc_mat, 0, 0, 'L');
      $tcount = $tcount + number_format($student->desc_mat, 2);
           }
   if ($student->desc_otro1 != '')
           {
      $pdf->Cell(50, 5, $student->desc3 . ' ' . $student->desc_otro1, 0, 0, 'L');
      $tcount = $tcount + number_format($student->desc_otro1, 2);
           }
        $pdf->Cell(1, 5, '', 0, 1, 'L');
   $count++;
}
        $pdf->Cell(50, 5, '', 0, 1, 'L');
        $pdf->Cell(10, 5, '', 0, 0, 'L');
        $pdf->Cell(30, 5, 'Total: ', 1, 0, 'L', true);
        $pdf->Cell(30, 5, $tcount, 1, 1, 'R');


$pdf->Output();
