<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;

Session::is_logged();

$lang = new Lang([
    ['BOLETA DE NOTAS', 'REPORT CARD'],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["Nombre del estudiante:", "Student name:"],
    ["DESCRIPCION", "DESCRIPTION"],
    ['NOTAS', 'GRADES'],
    ['PROFESOR', 'TEACHER'],
    ['PRO', 'AVE'],
    ['PROMEDIO:', 'AVERAGE:'],
    ['Nombre:', 'Name:'],
    ['CREDITOS', 'CREDITS'],
    ['Fecha', 'Date'],
    ['Documentos sin entregar', 'Undelivered documents'],
    ['Masculinos', 'Males'],
    ['VERANO', 'SUMMER'],
]);

function NLetra($valor){
  if($valor == ''){
    return '';
  }else if ($valor <= '100' && $valor >= '90') {
    return 'A';
  }else if ($valor <= '89' && $valor >= '80') {
    return 'B';
  }else if ($valor <= '79' && $valor >= '70') {
    return 'C';
  }else if ($valor <= '69' && $valor >= '60') {
    return 'D';
  }else  if ($valor <= '59') {
    return 'F';
  }
}


$pdf = new PDF();
$pdf->useFooter(false);
$school = new School(Session::id());
$studentClass = new Student();

$year = $school->info('year2');

$pdf = new PDF();
$pdf->useFooter(false);

$pdf->SetTitle($lang->translation("BOLETA DE NOTAS") . " $year", true);
$pdf->Fill();

$grade = $_POST['grade'];
$est = $_POST['estu'];


if ($est == 'N')
   {
   $students = DB::table('padres')->select("distinct nombre, apellidos, ss, grado")->where([
          ['year', $year],
          ['grado', $grade],
          ['verano', '!=', ''],
        ])->orderBy('apellidos')->get();
   }
else
   {
   if ($est == 'T')
      {
      $students = DB::table('padres')->select("distinct nombre, apellidos, ss, grado")->where([
          ['year', $year],
          ['verano', '!=', ''],
        ])->orderBy('apellidos, grado')->get();
      }
   else
      {
      $students = DB::table('year')->where([
         ['ss', $est],
         ['year', $year],
         ])->orderBy('ss')->get();
      }
   }


foreach ($students as $estu) {
    $pdf->AddPage('');
    $pdf->useFooter(false);
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("BOLETA DE NOTAS"), 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->Cell(0, 5, $lang->translation("VERANO") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 12);

    $pdf->Cell(70, 5, $lang->translation("Nombre del estudiante:"), 1, 1, 'C',true);
    $pdf->Cell(70, 5, "$estu->nombre $estu->apellidos", 1, 1, 'L');
    $pdf->Ln(5);
    $pdf->Cell(35, 5, $lang->translation("Grado:"), 1, 0, 'C', true);
    $pdf->Cell(35, 5, $lang->translation("Fecha"), 1, 1, 'C', true);
    $pdf->Cell(35, 5, "$estu->grado", 1, 0, 'C');
    $pdf->Cell(35, 5, date('d-m-Y'), 1, 1, 'C');

    $pdf->Ln(15);
  
    $pdf->SetFillColor(89, 171, 227);
    $cursos = DB::table('padres')->where([
          ['year', $year],
          ['ss', $estu->ss],
          ['grado', $estu->grado],
          ['curso', '!=', ''],
          ['verano', '!=', '']
        ])->orderBy('orden')->get();
    $crs = 0;
    $pdf->Cell(60, 5, $lang->translation("DESCRIPCION"), 1, 0, 'C',true);
    $pdf->Cell(30, 5, $lang->translation("NOTAS"), 1, 0, 'C',true);
    $pdf->Cell(30, 5, $lang->translation("CREDITOS"), 1, 0, 'C',true);
    $pdf->Cell(60, 5, $lang->translation("PROFESOR"), 1, 1, 'C',true);
    $pdf->Ln(5);

    foreach ($cursos as $curso) {
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(60, 5, $curso->descripcion, 'B', 0, 'L');
    $pdf->Cell(30, 5, $curso->nota1, 'B', 0, 'C');
    $pdf->Cell(30, 5, number_format($curso->credito,2), 'B', 0, 'C');
    $pdf->Cell(60, 5, $curso->profesor, 'B', 1, 'L');
    $pdf->Ln(5);
        }



}

$pdf->Output();