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
    ['Distri/Notas maestros', 'Teacher Notes Distribution'],
    ["Profesor", "Teacher:"],
    ["Grado", "Grade"],
  ["Año escolar:", "School year:"],
  ["Descripción", "Description"],
    ['Apellidos', 'Lasname'],
    ['Nombre', 'Name'],
    ['Curso', 'Course'],
  ['Crédito', 'Credit'],
    ['Trimestre', 'Quarter'],
  ['Trimestre 1', 'Quarter 1'],
  ['Trimestre 2', 'Quarter 2'],
  ['Trimestre 3', 'Quarter 3'],
  ['Trimestre 4', 'Quarter 4'],
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
  ['Semestre 1', 'Semester 1'],
  ['Semestre 2', 'Semester 2'],
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
  }else  if ($valor <= '59') {
    return 'F';
  }
}


//$pdf = new PDF();
//$pdf->useFooter(false);
$school = new School(Session::id());

$year = $school->info('year2');
$pdf = new PDF();
$pdf->useFooter(false);
$pdf->AddPage('');
$pdf->SetTitle($lang->translation("Distri/Notas maestros") . " $year", true);
$pdf->Fill();

$id = $_POST['teacher'];
$nota = $_POST['nota'];
$not = '';
if ($nota == 'nota1') {
  $not = 'Trimestre 1';
}
if ($nota == 'nota2') {
  $not = 'Trimestre 2';
}
if ($nota == 'nota3') {
  $not = 'Trimestre 3';
}
if ($nota == 'nota4') {
  $not = 'Trimestre 4';
}
if ($nota == 'sem1') {
  $not = 'Semestre 1';
}
if ($nota == 'sem2') {
  $not = 'Semestre 2';
}
if ($nota == 'final') {
  $not = 'final';
}
$teacher = DB::table('profesor')->where([
       ['id', $id],
       ])->orderBy('id')->first();

$cursos = DB::table('padres')->select("distinct curso, descripcion, credito, grado")->where([
       ['id', $id],
       ['year', $year],
])->orderBy('curso')->get();
    $pdf->useFooter(false);
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Distri/Notas maestros") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 10);

$nom = $teacher->nombre ?? '';
$ape = utf8_encode($teacher->apellidos ?? '');

    $pdf->Cell(30, 5, $lang->translation("Profesor"), 1, 0, 'C', true);
$pdf->Cell(70, 5, $nom . ' ' . $ape, 0, 0, 'L');
    $pdf->Cell(30, 5, '', 0, 0, 'C');
$pdf->Cell(15, 5, $lang->translation($not), 0, 1, 'L');
    $pdf->Cell(10, 5, '', 1, 0, 'C', true);
    $pdf->Cell(15, 5, $lang->translation("Grado"), 1, 0, 'C', true);
    $pdf->Cell(18, 5, $lang->translation("Curso"), 1, 0, 'C', true);
$pdf->Cell(50, 5, utf8_encode($lang->translation("Descripción")), 1, 0, 'C', true);
    $pdf->Cell(14, 5, 'A', 1, 0, 'C', true);
    $pdf->Cell(14, 5, 'B', 1, 0, 'C', true);
    $pdf->Cell(14, 5, 'C', 1, 0, 'C', true);
    $pdf->Cell(14, 5, 'D', 1, 0, 'C', true);
    $pdf->Cell(14, 5, 'F', 1, 0, 'C', true);
    $pdf->Cell(14, 5, 'Total', 1, 0, 'C', true);
    $pdf->Ln(5);
    $r = 0;
  
    $pdf->SetFont('Arial', '', 10);
    foreach ($cursos as $curso) {
    $students = DB::table('padres')->where([
          ['year', $year],
          ['curso', $curso->curso],
        ])->orderBy('apellidos, nombre')->get();
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
    $r = $r +1;
    $pdf->Cell(10, 5, $r, 1, 0, 'R');
    $pdf->Cell(15, 5, $curso->grado, 1, 0, 'L');
    $pdf->Cell(18, 5, $curso->curso, 1, 0, 'L');

    $pdf->Cell(50, 5, $curso->descripcion, 1, 0, 'L');

    $pdf->Cell(14, 5, $a, 1, 0, 'C');
    $pdf->Cell(14, 5, $b, 1, 0, 'C');
    $pdf->Cell(14, 5, $c, 1, 0, 'C');
    $pdf->Cell(14, 5, $d, 1, 0, 'C');
    $pdf->Cell(14, 5, $f, 1, 0, 'C');
  $pdf->Cell(14, 5, $te, 1, 1, 'C');
  }

$pdf->Output();