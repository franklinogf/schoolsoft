<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();

$lang = new Lang([
    ['Lista de fracasados por grado', 'List of failures by grade'],
    ['Lista de fracasados por curso', 'List of failures by course'],
    ["Nombre", "Name"],
    ["Apellidos", "Surnames"],
    ["Año escolar:", "School year:"],
    ["T-1", "Q-1"],
    ["T-2", "Q-2"],
    ["T-3", "Q-3"],
    ["T-4", "Q-4"],
    ['PRIMER SEMESTRE', 'FIRST SEMESTER'],
    ['SEGUNDO SEMESTRE', 'SECOND SEMESTER'],
    ['PRO', 'AVE'],
    ['PROMEDIO:', 'AVERAGE:'],
    ['Nombre:', 'Name:'],
    ['Total de estudiantes', 'Total students'],
    ['Fecha:', 'Date:'],
    ['Documentos sin entregar', 'Undelivered documents'],
    ['Masculinos', 'Males'],
    ['Curso', 'Course'],
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
$school = new School();
$teacherClass = new Teacher();
$studentClass = new Student();

$year = $school->year();
$allGrades = $school->allGrades();
$pdf = new PDF();
//$pdf->useFooter(false);

$pdf->SetTitle($lang->translation("Lista de fracasados por ".$_POST['cursos']) . " $year", true);
$pdf->Fill();

$grade = $_POST['grade'];
$men = $_POST['mensaje'];
$cl = $_POST['conlinia'];
//$grade = '07-01';

//$mensaj = DB::table('codigos')->where([
//       ['codigo', $men],
//       ])->orderBy('codigo')->first();

//$teacher = $teacherClass->findByGrade($grade);
//$students = $studentClass->findByGrade($grade);

//$mensaj = DB::table('codigos')->where([
//       ['codigo', $men],
//       ])->orderBy('codigo')->get();


//    $pdf->AddPage('L');
//    $pdf->useFooter(false);
//    $pdf->SetFont('Arial', 'B', 15);
//    $pdf->Cell(0, 5, $lang->translation("Lista de fracasados por ".$_POST['cursos']). " $year", 0, 1, 'C');
//    $pdf->Ln(5);
//    $pdf->SetFont('Arial', '', 12);
//    $da = date("m-d-Y");
//    $pdf->splitCells("$da "," ");

    $materias = [];
    $cursos = [];
    $estudiantes = [];
//    $pdf->Cell(12, 5, '', 1, 0, 'L', true);
//    $pdf->Cell(70, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
//    $pdf->Cell(60, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
//    $pdf->Cell(20, 5, $lang->translation("Curso"), 1, 0, 'C', true);
//    $pdf->Cell(15, 5, $lang->translation("T-1"), 1, 0, 'C', true);
//    $pdf->Cell(15, 5, $lang->translation("T-2"), 1, 0, 'C', true);
//    $pdf->Cell(15, 5, 'S-1', 1, 0, 'C', true);
//    $pdf->Cell(15, 5, $lang->translation("T-3"), 1, 0, 'C', true);
//    $pdf->Cell(15, 5, $lang->translation("T-4"), 1, 0, 'C', true);
//    $pdf->Cell(15, 5, 'S-2', 1, 0, 'C', true);
//    $pdf->Cell(15, 5, 'Final', 1, 1, 'C', true);

    
    $pdf->SetFillColor(89, 171, 227);
    $valor = $_POST['valor'];
    $nota = $_POST['nota'];
    $orden = $_POST['cursos'];
    $gs = $_POST['gradossep'];
    $curso = $_POST['curso'];
if (empty($curso))
   {
   $cursos = DB::table('padres')->where([
          ['baja', ''],
          ['year', $year],
          [$nota, '>', ''],
          ['curso', '!=', ''],
          ['curso', 'NOT LIKE', '%AA-%']
        ])->orderBy($orden)->get();
   }
else
   {
   $cursos = DB::table('padres')->where([
          ['baja', ''],
          ['year', $year],
          [$nota, '>', ''],
          ['curso', '!=', ''],
          ['curso', 'LIKE', '%'.$curso.'%'],
          ['curso', 'NOT LIKE', '%AA-%']
        ])->orderBy($orden)->get();
   }
    $c = 0;
    $g = '';
    foreach ($cursos as $curso) {
    if ($curso->$nota < $valor )
       {
       if ($c == 24 or $c == 0 or $g != $curso->grado and $gs == '2')
          {
          $c = 0;
          $g = $curso->grado;
          $pdf->AddPage('L');
          $pdf->SetFont('Arial', 'B', 15);
          $pdf->Cell(0, 5, $lang->translation("Lista de fracasados por ".$_POST['cursos']). " $year", 0, 1, 'C');
          $pdf->Ln(5);
          $pdf->SetFont('Arial', '', 12);
          $da = date("m-d-Y");
          $pdf->splitCells("$da "," ");
          $pdf->Cell(12, 5, '', 1, 0, 'L', true);
          $pdf->Cell(70, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
          $pdf->Cell(60, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
          $pdf->Cell(20, 5, $lang->translation("Curso"), 1, 0, 'C', true);
          $pdf->Cell(15, 5, $lang->translation("T-1"), 1, 0, 'C', true);
          $pdf->Cell(15, 5, $lang->translation("T-2"), 1, 0, 'C', true);
          $pdf->Cell(15, 5, 'S-1', 1, 0, 'C', true);
          $pdf->Cell(15, 5, $lang->translation("T-3"), 1, 0, 'C', true);
          $pdf->Cell(15, 5, $lang->translation("T-4"), 1, 0, 'C', true);
          $pdf->Cell(15, 5, 'S-2', 1, 0, 'C', true);
          $pdf->Cell(15, 5, 'Final', 1, 1, 'C', true);
          }
       $g = $curso->grado;
       $c = $c + 1;
       $pdf->SetFont('Arial', '', 10);
       $pdf->Cell(12, 5, $c, $cl, 0, 'R');
       $pdf->Cell(70, 5, $curso->apellidos, $cl, 0, 'L');
       $pdf->Cell(60, 5, $curso->nombre, $cl, 0, 'L');
       $pdf->Cell(20, 5, $curso->curso, $cl, 0, 'L');
       $pdf->Cell(15, 5, $_POST['tri1b'] == 'Si' ? $curso->nota1 : '', $cl, 0, 'R');
       $pdf->Cell(15, 5, $_POST['tri2b'] == 'Si' ? $curso->nota2 : '', $cl, 0, 'R');
       $pdf->Cell(15, 5, $_POST['sem1b'] == 'Si' ? $curso->sem1 : '', $cl, 0, 'R');
       $pdf->Cell(15, 5, $_POST['tri3b'] == 'Si' ? $curso->nota3 : '', $cl, 0, 'R');
       $pdf->Cell(15, 5, $_POST['tri4b'] == 'Si' ? $curso->nota4 : '', $cl, 0, 'R');
       $pdf->Cell(15, 5, $_POST['sem2b'] == 'Si' ? $curso->sem2 : '', $cl, 0, 'R');
       $pdf->Cell(15, 5, $_POST['profb'] == 'Si' ? $curso->final : '', $cl, 0, 'R');
       $pdf->Cell(1, 5, '', 0, 1, 'R');

    }
//    $pdf->SetFont('Arial', '', 8);
      



        }
    $pdf->SetFont('Arial', '', 10);
//    $pdf->Cell(13, 5, number_format($crs,2), 1, 1, 'R', true);


$pdf->Output();