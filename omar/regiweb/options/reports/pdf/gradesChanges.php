<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;

Session::is_logged();
$teacher = new Teacher(Session::id());

$changes = DB::table('tarjeta_cambios')->where([
    ['id', $teacher->id],
    ['year', $teacher->info('year')]
])->OrderBy('fecha DESC')->get();
$cards = [];
foreach ($changes as $index => $change) {
    $student = new Student($change->ss);
    $cards[$index]['nombre'] = $student->nombre;
    $cards[$index]['apellidos'] = $student->apellidos;
    $cards[$index]['curso'] = $change->curso;
    $cards[$index]['fecha'] = $change->fecha;
    $cards[$index]['hora'] = $change->hora;
    $cards[$index]['ip'] = $change->ip;
    $cards[$index]['nt1'] = $change->nt1;
    $cards[$index]['nt2'] = $change->nt2;
    $cards[$index]['cual'] = $change->cual;
    $cards[$index]['tri'] = substr($change->tri, -1);
    $cards[$index]['pag'] = $change->pag;
}
// $columns = array_column($cards, 'fullName');
$names = array_map(function($element) {
    return $element['apellidos'];
  }, $cards);
$dates = array_map(function($element) {
    return $element['fecha'];
  }, $cards);
$times = array_map(function($element) {
    return $element['fecha'];
  }, $cards);
array_multisort($names,SORT_ASC,$dates,SORT_DESC,$times, SORT_DESC, $cards);
$cards = Util::toObject($cards);
// var_dump($cards);
// exit;
$pdf = new PDF();
$pdf->SetTitle("Informe de cambios de notas");
$pdf->AddPage('L');
$pdf->SetAutoPageBreak(true, 10);
$pdf->useFooter(10);
$pdf->Fill();
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 3, 'INFORME CAMBIOS DE NOTAS ' . $teacher->info('year'), 0, 1, 'C');
$pdf->Ln(8);
$pdf->SetFont('Times', '', 11);
$pdf->Cell(90, 5, 'NOMBRE', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'CURSO', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'FECHA', 1, 0, 'C', true);
$pdf->Cell(22, 5, 'HORA', 1, 0, 'C', true);
$pdf->Cell(30, 5, 'IP', 1, 0, 'C', true);
$pdf->Cell(17, 5, 'AHORA', 1, 0, 'C', true);
$pdf->Cell(17, 5, 'ANTES', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'NOTA', 1, 0, 'C', true);
$pdf->Cell(10, 5, 'TRI', 1, 0, 'C', true);
$pdf->Cell(35, 5, 'PAGINA', 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 11);
$count = 1;
foreach ($cards as $card) {
    $pdf->SetAutoPageBreak(true, 10);
    $pdf->Cell(10, 5, $count, 1, 0, 'R');
    $pdf->Cell(80, 5, "$card->nombre $card->apellidos", 1, 0);
    $pdf->Cell(20, 5, $card->curso, 1, 0, 'C');
    $pdf->Cell(25, 5, $card->fecha, 1, 0, 'C');
    $pdf->Cell(22, 5, $card->hora, 1, 0, 'C');
    $pdf->Cell(30, 5, $card->ip, 1, 0, 'C');
    $pdf->Cell(17, 5, $card->nt1, 1, 0, 'C');
    $pdf->Cell(17, 5, $card->nt2, 1, 0, 'C');
    $pdf->Cell(15, 5, $card->cual, 1, 0, 'C');
    $pdf->Cell(10, 5, substr($card->tri, -1), 1, 0, 'C');
    $pdf->Cell(35, 5, $card->pag, 1, 1, 'C');
    $count++;
}

$pdf->Output();
