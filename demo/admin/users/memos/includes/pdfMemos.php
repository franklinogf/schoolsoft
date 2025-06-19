<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Util;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;

Session::is_logged();
$ss = $_POST['student'];
$student = new Student($ss);
$year = $student->info('year');

$lang = new Lang([
    ['Lista de diciplina aplicada', 'Applied Discipline List'],
    ['Profesor', 'Teacher'],
    ['Titulo', 'Title'],
    ['Fecha', 'Date'],
    ['Demeritos', 'Demerits'],
    ['Nombre', 'Name'],
]);

$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista de diciplina aplicada"));
$pdf->Fill();
// $pdf->SetAutoPageBreak(true, 50);
// var_dump($allGrades);

$pdf->addPage();
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, $lang->translation("Lista de diciplina aplicada"), 0, 1, 'C');
$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(30, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
$pdf->Cell(70, 5, $student->fullName(), 1, 0, 'L');
$pdf->Cell(30, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
$pdf->Cell(50, 5, Util::date(), 1, 1, 'L');
$pdf->Cell(50, 5, '', 0, 1, 'C');

$pdf->Cell(80, 5, $lang->translation("Titulo"), 1, 0, 'C', true);
$pdf->Cell(50, 5, $lang->translation("Profesor"), 1, 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
$pdf->Cell(20, 5, $lang->translation("Demeritos"), 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 10);
$memos = DB::table('memos')->where([['ss', $ss], ['year', $year]])->get();
$array = [0, 0, 0];
foreach ($memos as $index => $memo) {
    list($teacherName, $teacherId) = explode(', ', $memo->profesor);
    $pdf->Cell(7, 5, $index + 1, 1, 0, 'R');
    $pdf->Cell(73, 5, $memo->titulo, 1, 0, 'L');
    $pdf->Cell(50, 5, $teacherName, 1, 0, 'L');
    $pdf->Cell(30, 5, $memo->fecha, 1, 0, 'C');
    $pdf->Cell(20, 5, $memo->demeritos, 1, 1, 'C');
    if ($memo->comentario !== '') {
        $pdf->Cell(7, 5, '', 'LTB', 0, 'R');
        $pdf->Cell(173, 5, $memo->comentario, 'RTB', 1, 'L');
    }
    if (strpos($memo->titulo, "Tallies") !== false) {
        $array[0] += $memo->demeritos;
    } else if (strpos($memo->titulo, "Merits") !== false) {
        $array[1] += $memo->demeritos;
    } else {
        $array[2] += $memo->demeritos;
    }
}

$pdf->Cell(7, 5, '', 0, 1,'R');
$pdf->Cell(40, 5, 'Tallies', 1, 0,'L',true);
$pdf->Cell(20, 5, $array[0], 1, 1,'R');
$pdf->Cell(40, 5, 'Merits', 1, 0,'L',true);
$pdf->Cell(20, 5,  $array[1], 1, 1,'R');
$pdf->Cell(40, 5, 'Demerits', 1, 0,'L',true);
$pdf->Cell(20, 5, $array[2], 1, 1,'R');


$pdf->Output();
