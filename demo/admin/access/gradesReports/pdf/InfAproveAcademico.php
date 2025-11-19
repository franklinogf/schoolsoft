<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();

$lang = new Lang([['Información sobre el rendimiento académico', 'Information on academic performance'],
    ["Profesor", "Teacher:"],
    ["Grado", "Grade"],
  ["Año escolar:", "School year:"],
  ["Descripción", "Description"],
    ['Apellidos', 'Lasname'],
    ['Nombre', 'Name'],
    ['Curso', 'Course'],
  ['Crédito', 'Credit'],
    ['Trimestre', 'Quarter'],
    ['NF', 'FN'],
    ['T-D', 'DW'],
    ['T-L', 'HW'],
    ['P-C', 'QZ'],
    ['TPA', 'TAP'],
    ['PROMEDIO:', 'AVERAGE:'],
  ['Matrícula', 'Tuition'],
    ['Trabajos Diarios', 'Daily Homework'],
    ['Trabajos Libreta', 'Homework'],
    ['Fecha', 'Date'],
    ['Tema', 'Topic'],
    ['Valor', 'Value'],
    ['Pruebas Cortas', 'Quiz'],

    ['T-1', 'Q-1'],
    ['T-2', 'Q-2'],
    ['T-3', 'Q-3'],
    ['T-4', 'Q-4'],
    ['', ''],
    ['', ''],

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
  } else  if ($valor <= '59' && $valor >= '0') {
    return 'F';
  }
}

$school = new School(Session::id());

$year = $school->info('year2');
$pdf = new PDF();
$pdf->useFooter(false);
$pdf->AddPage('');
$pdf->SetTitle(utf8_encode($lang->translation("Información sobre el rendimiento académico")) . " $year", true);
$pdf->Fill();

$id = $_POST['teacher'];
$nota = $_POST['nota'];

$cursos = DB::table('padres')->select("distinct curso, descripcion, credito, grado")->where([
       ['id', $id],
       ['year', $year],
       ])->orderBy('curso')->get();

    $pdf->useFooter(false);
    $pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, utf8_encode($lang->translation("Información sobre el rendimiento académico")) . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 10);

    $pdf->Cell(15, 10, $lang->translation("Grado"), 1, 0, 'C', true);
$pdf->Cell(17, 10, $lang->translation("Curso"), 1, 0, 'C', true);
$pdf->Cell(22, 10, utf8_encode($lang->translation("Matrícula")), 1, 0, 'C', true);
    $pdf->Cell(8, 10, 'A', 1, 0, 'C', true);
    $pdf->Cell(10, 10, '%', 1, 0, 'C', true);
    $pdf->Cell(8, 10, 'B', 1, 0, 'C', true);
    $pdf->Cell(10, 10, '%', 1, 0, 'C', true);
    $pdf->Cell(8, 10, 'C', 1, 0, 'C', true);
    $pdf->Cell(10, 10, '%', 1, 0, 'C', true);
    $pdf->Cell(8, 10, 'D', 1, 0, 'C', true);
    $pdf->Cell(10, 10, '%', 1, 0, 'C', true);
    $pdf->Cell(8, 10, 'F', 1, 0, 'C', true);
    $pdf->Cell(10, 10, '%', 1, 0, 'C', true);
    $pdf->Cell(12, 5, 'Total', 'LTR', 0, 'C', true);
    $pdf->Cell(12, 10, '%ABC', 1, 0, 'C', true);
    $pdf->Cell(12, 5, 'Total', 'LTR', 0, 'C', true);
    $pdf->Cell(12, 10, '%DF', 1, 0, 'C', true);
    $pdf->Ln(5);
$pdf->Cell(144);
$pdf->Cell(12,5,"ABC","LBR",0,'C', true);
$pdf->Cell(12,5,"",0,0,'C');
$pdf->Cell(12,5,"DF","LBR",0,'C', true);
$pdf->Ln(5);
    
    $pdf->SetFont('Arial', '', 10);
    foreach ($cursos as $curso) {
    $students = DB::table('padres')->where([
          ['year', $year],
          ['curso', $curso->curso],
        ])->orderBy('apellidos, nombre')->get();
    $crs = 0;
    $te = 0;
    $a=0;$b=0;$c=0;$d=0;$f=0;
    foreach ($students as $estu) 
            {
            if ($estu->$nota > 89){$a=$a+1;$te=$te+1;}
            else
               if ($estu->$nota > 79){$b=$b+1;$te=$te+1;}
               else
                  if ($estu->$nota > 69){$c=$c+1;$te=$te+1;}
                  else
                     if ($estu->$nota > 59){$d=$d+1;$te=$te+1;}
                     else
                        if ($estu->$nota > 1){$f=$f+1;$te=$te+1;}
            }

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(15, 5, $curso->grado, 1, 0, 'L');
  $pdf->Cell(17, 5, $curso->curso, 1, 0, 'L');
    $pdf->Cell(22, 5, $te, 1, 0, 'C');
    $pdf->Cell(8, 5, $a, 1, 0, 'C');
    $p=0;
    if ($a > 0){$p=round(($a/$te)*100,2);}
    $pdf->Cell(10, 5, $p, 1, 0, 'C');
    $pdf->Cell(8, 5, $b, 1, 0, 'C');
    $p=0;
    if ($b > 0){$p=round(($b/$te)*100,2);}
    $pdf->Cell(10, 5, $p, 1, 0, 'C');
    $pdf->Cell(8, 5, $c, 1, 0, 'C');
    $p=0;
    if ($c > 0){$p=round(($c/$te)*100,2);}
    $pdf->Cell(10, 5, $p, 1, 0, 'C');
    $pdf->Cell(8, 5, $d, 1, 0, 'C');
    $p=0;
    if ($d > 0){$p=round(($d/$te)*100,2);}
    $pdf->Cell(10, 5, $p, 1, 0, 'C');
    $pdf->Cell(8, 5, $f, 1, 0, 'C');
    $p=0;
    if ($f > 0){$p=round(($f/$te)*100,2);}
    $pdf->Cell(10, 5, $p, 1, 0, 'C');


    $pdf->Cell(12, 5, $a+$b+$c, 1, 0, 'C');
    $p=0;
    if ($a+$b+$c > 0){$p=round((($a+$b+$c)/$te)*100,2);}
    $pdf->Cell(12, 5, $p, 1, 0, 'C');


    $pdf->Cell(12, 5, $d+$f, 1, 0, 'C');
    $p=0;
    if ($d+$f > 0){$p=round((($d+$f)/$te)*100,2);}
    $pdf->Cell(12, 5, $p, 1, 1, 'C');

  }


$pdf->Output();