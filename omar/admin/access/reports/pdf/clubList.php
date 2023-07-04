<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

$lang = new Lang([
    ["Lista de club", "Club list"],
    ['Moderador', 'Moderator'],
    ['ID', 'ID'],
    ['Club', 'Club'],
    ['Presidente', 'President'],
    ['Vice-Presidente', 'Vice President'],
    ['Secretario(a)', 'Secretary'],
]);

$school = new School();
$year = $school->info('year');
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista de clud") . " $year", true);
$pdf->Fill();

$pdf->AddPage('L');
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, $lang->translation("Lista de club") . " $year", 0, 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(7, 5, '', 1, 0, 'C', true);
//$pdf->Cell(15, 5, $lang->translation("ID"), 1, 0, 'C', true);
$pdf->Cell(60, 5, $lang->translation("Moderador"), 1, 0, 'C', true);
$pdf->Cell(50, 5, $lang->translation("Club"), 1, 0, 'C', true);
$pdf->Cell(55, 5, $lang->translation("Presidente"), 1, 0, 'C', true);
$pdf->Cell(55, 5, $lang->translation("Vice-Presidente"), 1, 0, 'C', true);
$pdf->Cell(55, 5, $lang->translation("Secretario(a)"), 1, 1, 'C', true);
$pdf->ln(2);
$pdf->SetFont('Arial', '', 10);

$teachers = DB::table('profesor')->where([
    ['baja', ''],
    ['club1', '!=' , ''],
    ['docente', 'Docente']
])->orderBy('apellidos')->get();
foreach ($teachers as $count => $teacher) {
    $pdf->Cell(7, 5, $count + 1, 0, 0, 'C');
//    $pdf->Cell(15, 5, $teacher->id, 0, 0, 'C');
    $pdf->Cell(60, 5, utf8_decode($teacher->apellidos).' '.utf8_decode($teacher->nombre));
    $pdf->Cell(50, 5, utf8_decode($teacher->club1));
    $pdf->Cell(55, 5, utf8_decode($teacher->pre1));
    $pdf->Cell(55, 5, utf8_decode($teacher->vi1));
    $pdf->Cell(55, 5, utf8_decode($teacher->se1), 0, 1);
    if ($teacher->club2 !='')
       {
       $pdf->Cell(67, 5, '', 0, 0, 'C');
       $pdf->Cell(50, 5, utf8_decode($teacher->club2));
       $pdf->Cell(55, 5, utf8_decode($teacher->pre2));
       $pdf->Cell(55, 5, utf8_decode($teacher->vi2));
       $pdf->Cell(55, 5, utf8_decode($teacher->se2), 0, 1);
       }
    if ($teacher->club3 !='')
       {
       $pdf->Cell(67, 5, '', 0, 0, 'C');
       $pdf->Cell(50, 5, utf8_decode($teacher->club3));
       $pdf->Cell(55, 5, utf8_decode($teacher->pre3));
       $pdf->Cell(55, 5, utf8_decode($teacher->vi3));
       $pdf->Cell(55, 5, utf8_decode($teacher->se3), 0, 1);
       }
    if ($teacher->club4 !='')
       {
       $pdf->Cell(67, 5, '', 0, 0, 'C');
       $pdf->Cell(50, 5, utf8_decode($teacher->club4));
       $pdf->Cell(55, 5, utf8_decode($teacher->pre4));
       $pdf->Cell(55, 5, utf8_decode($teacher->vi4));
       $pdf->Cell(55, 5, utf8_decode($teacher->se4), 0, 1);
       }
    if ($teacher->club5 !='')
       {
       $pdf->Cell(67, 5, '', 0, 0, 'C');
       $pdf->Cell(50, 5, utf8_decode($teacher->club5));
       $pdf->Cell(55, 5, utf8_decode($teacher->pre5));
       $pdf->Cell(55, 5, utf8_decode($teacher->vi5));
       $pdf->Cell(55, 5, utf8_decode($teacher->se5), 0, 1);
       }


}


$pdf->Output();
