<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

$lang = new Lang([
    ['Lista de direcciones maestros', 'Teacher address List'],
    ['Profesor', 'Teacher'],
    ['Nombre', 'Name'],
    ['Correo', 'E-Mail'],
   ['Dirección', 'Address'],
]);
$prof = $_POST['prof'];
$school = new School();
$year = $school->year();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista de direcciones maestros") . " $year", true);
$pdf->Fill();

$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, $lang->translation("Lista de direcciones maestros") . " $year", 0, 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 5, '', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'ID', 1, 0, 'C', true);
$pdf->Cell(70, 5, $lang->translation("Profesor"), 1, 0, 'C', true);
$pdf->Cell(70, 5, $lang->translation("Dirección"), 1, 1, 'C', true);
$pdf->ln(2);
$pdf->SetFont('Arial', '', 10);

$teachers = DB::table('profesor')->where([
    ['baja', ''],
    ['docente', 'Docente']
])->orderBy('apellidos')->get();
foreach ($teachers as $count => $teacher) {
    $pdf->Cell(10, 5, $count + 1, 0, 0, 'C');
    $pdf->Cell(20, 5, $teacher->id, 0, 0, 'C');
   $pdf->Cell(70, 5, $teacher->apellidos . ' ' . $teacher->nombre);
    if ($prof=='R')
       {
      $pdf->Cell(70, 5, $teacher->dir1, 0, 1, 'L');
       if ($teacher->dir2 != '')
          {
          $pdf->Cell(100, 5, '', 0, 0, 'C');
         $pdf->Cell(70, 5, $teacher->dir2, 0, 1, 'L');
          }
       if ($teacher->pueblo1 != '')
          {
          $pdf->Cell(100, 5, '', 0, 0, 'C');
         $pdf->Cell(70, 5, $teacher->pueblo1 . ' ' . $teacher->esta1 . ' ' . $teacher->zip1, 0, 1, 'L');
          }
       }
    else
       {
      $pdf->Cell(70, 5, $teacher->dir3, 0, 1, 'L');
       if ($teacher->dir4 != '')
          {
          $pdf->Cell(100, 5, '', 0, 0, 'C');
         $pdf->Cell(70, 5, $teacher->dir4, 0, 1, 'L');
          }
       if ($teacher->pueblo2 != '')
          {
          $pdf->Cell(100, 5, '', 0, 0, 'C');
         $pdf->Cell(70, 5, $teacher->pueblo2 . ' ' . $teacher->esta2 . ' ' . $teacher->zip2, 0, 1, 'L');
          }
       }

}


$pdf->Output();
