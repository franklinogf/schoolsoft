<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;

Session::is_logged();

$lang = new Lang([['CURSOS A MEJORAR', 'COURSES TO IMPROVE'],
  ['INFORME DE DEFICIENCIA', 'DEFICIENCY REPORT'],
  ['BOLETA DE NOTAS', 'REPORT CARD'],
  ["Maestro(a):", "Teacher:"],
  ["Grado:", "Grade:"],
  ["Nombre del estudiante:", "Student name:"],
  ["DESCRIPCION", "DESCRIPTION"],
  ['NOTAS', 'GRADES'],
  ['PROFESOR', 'TEACHER'],
  ['Observación:', 'Observation:'],
  ['PROMEDIO:', 'AVERAGE:'],
  ['Nombre:', 'Name:'],
  ['CREDITOS', 'CREDITS'],
  ['Fecha', 'Date'],
  ['T-1', 'Q-1'],
  ['T-2', 'Q-2'],
  ['T-3', 'Q-3'],
  ['T-4', 'Q-4'],
  ['S-1', 'S-1'],
  ['S-2', 'S-2'],
  ['Maestro', 'Maestro'],
  ['Padre/encargado', 'Parent/Guardian'],
  ['Registradora', 'Registrar'],
]);

function NLetra($valor)
{
  if ($valor == '') {
    return '';
  } else if ($valor <= '100' && $valor >= '90') {
    return 'A';
  } else if ($valor <= '89' && $valor >= '80') {
    return 'B';
  } else if ($valor <= '79' && $valor >= '70') {
    return 'C';
  } else if ($valor <= '69' && $valor >= '60') {
    return 'D';
  } else  if ($valor <= '59') {
    return 'F';
  }
}

$pdf = new PDF();
$pdf->useFooter(false);
$school = new School(Session::id());
$studentClass = new Student();
$year = $school->info('year2');
$grade = $_POST['grade'];
$est = 'N';
$titulo = $_POST['titulo'];
$nota = $_POST['nota'];
$valor = $_POST['valor'] ?? '70';
$pdf = new PDF();
$pdf->useFooter(false);
if ($est == 'N') {
  $students = DB::table('padres')->select("distinct nombre, apellidos, ss, grado")->where([
    ['year', $year],
    ['grado', $grade],
    ['verano', '!=', ''],
  ])->orderBy('apellidos')->get();
} else {
  if ($est == 'T') {
    $students = DB::table('padres')->select("distinct nombre, apellidos, ss, grado")->where([
      ['year', $year],
      ['verano', '!=', ''],
    ])->orderBy('apellidos, grado')->get();
  } else {
    $students = DB::table('year')->where([
      ['ss', $est],
      ['year', $year],
    ])->orderBy('ss')->get();
  }
}
$students = DB::table('padres')->where([
  ['year', $year],
  ['grado', $grade],
  [$nota, '!=', ''],
  ['verano', '']
])->orderBy('apellidos, ss')->get();
$debts = [];
$a = 0;
$ss = '';
foreach ($students as $estu) {
  if ($estu->$nota <= $valor and $estu->ss != $ss) {
    $ss = $estu->ss;
    $a = $a + 1;
    $debts[$a][0] = $estu->ss;
    $debts[$a][1] = $estu->nombre . ' ' . $estu->apellidos;
    $debts[$a][2] = $estu->grado;
  }
}
foreach ($debts as $estu) {
  $pdf->AddPage('');
  $pdf->SetTitle($titulo . " $year", true);
  $pdf->Fill();
  $pdf->useFooter(false);
  $pdf->SetFont('Arial', 'B', 15);
  $pdf->Cell(0, 5, $titulo, 0, 1, 'C');
  $pdf->Ln(5);
  $pdf->Ln(5);
  $pdf->SetFont('Arial', '', 12);
  $pdf->Cell(70, 5, $lang->translation("Nombre del estudiante:"), 1, 1, 'C', true);
  $pdf->SetFont('Arial', '', 11);
  $pdf->Cell(70, 5, "$estu[1]", 1, 1, 'L');
  $pdf->Ln(5);
  $pdf->SetFont('Arial', '', 12);
  $pdf->Cell(35, 5, $lang->translation("Grado:"), 1, 0, 'C', true);
  $pdf->Cell(35, 5, $lang->translation("Fecha"), 1, 1, 'C', true);

  $pdf->SetFont('Arial', '', 11);
  $pdf->Cell(35, 5, $estu[2], 1, 0, 'C');
  $pdf->Cell(35, 5, date('d-m-Y'), 1, 1, 'C');
  $pdf->Ln(10);
  $pdf->SetFillColor(89, 171, 227);
  $cursos = DB::table('padres')->where([
    ['year', $year],
    ['ss', $estu[0]],
    ['grado', $estu[2]],
    ['curso', '!=', ''],
    ['verano', '']
  ])->orderBy('orden')->get();
  $crs = 0;
  $pdf->Cell(60, 5, $lang->translation("DESCRIPCION"), 1, 0, 'C', true);
  $pdf->Cell(15, 5, $lang->translation("T-1"), 1, 0, 'C', true);
  $pdf->Cell(15, 5, $lang->translation("T-2"), 1, 0, 'C', true);
  $pdf->Cell(15, 5, $lang->translation("S-1"), 1, 0, 'C', true);
  $pdf->Cell(15, 5, $lang->translation("T-3"), 1, 0, 'C', true);
  $pdf->Cell(15, 5, $lang->translation("T-4"), 1, 0, 'C', true);
  $pdf->Cell(15, 5, $lang->translation("S-2"), 1, 0, 'C', true);
  $pdf->Cell(15, 5, "FINAL", 1, 0, 'C', true);
  $pdf->Cell(25, 5, $lang->translation("CREDITOS"), 1, 1, 'C', true);
  foreach ($cursos as $curso) {
    $pdf->SetFont('Arial', '', 11);
    if ($curso->$nota <= $valor and $curso->$nota > 0) {
      $pdf->Cell(60, 5, $curso->descripcion, 1, 0, 'L');
      $pdf->Cell(15, 5, $_POST['tri1b'] ?? '' == 'Si' ? $curso->nota1 : '', 1, 0, 'C');
      $pdf->Cell(15, 5, $_POST['tri2b'] ?? '' == 'Si' ? $curso->nota2 : '', 1, 0, 'C');
      $pdf->Cell(15, 5, $_POST['sem1b'] ?? '' == 'Si' ? $curso->sem1 : '', 1, 0, 'C');
      $pdf->Cell(15, 5, $_POST['tri3b'] ?? '' == 'Si' ? $curso->nota3 : '', 1, 0, 'C');
      $pdf->Cell(15, 5, $_POST['tri4b'] ?? '' == 'Si' ? $curso->nota4 : '', 1, 0, 'C');
      $pdf->Cell(15, 5, $_POST['sem2b'] ?? '' == 'Si' ? $curso->sem1 : '', 1, 0, 'C');
      $pdf->Cell(15, 5, $_POST['profb'] ?? '' == 'Si' ? $curso->final : '', 1, 0, 'C');
      $pdf->Cell(25, 5, number_format($curso->credito, 2), 1, 1, 'C');
    }
  }
  $pdf->Ln(15);
  $pdf->Cell(70, 5, $_POST['firm1'] ?? '' == 'Si' ? $lang->translation("Maestro") : '', 'T', 1, 'C');
  $pdf->Ln(10);
  $pdf->Cell(70, 5, $_POST['firm2'] ?? '' == 'Si' ? $lang->translation("Padre/encargado") : '', 'T', 1, 'C');
  $pdf->Ln(10);
  $pdf->Cell(70, 5, $_POST['firm3'] ?? '' == 'Si' ? $lang->translation("Registradora") : '', 'T', 1, 'C');
  $pdf->Ln(15);
  $pdf->Cell(25, 7, $lang->translation("Observación:"), 0, 0, 'L');
  $pdf->Cell(160, 7, '', 'B', 1, 'L');
  $pdf->Cell(185, 7, '', 'B', 1, 'L');
  $pdf->Cell(185, 7, '', 'B', 1, 'L');
  $pdf->Cell(185, 7, '', 'B', 1, 'L');
}
$pdf->Output();