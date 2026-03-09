<?php
require_once '../../../../app.php';

use Classes\Controllers\School;
use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\DataBase\DB;
use Classes\Util;

Session::is_logged();

$lang = new Lang([
    ['Informe acumulativo de notas', 'Cumulative grade report'],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["Matricula:", "Tuition"],
    ["ENTREGADO", "DELIVERED"],
    ['Cuenta', 'Account'],
    ['Ning&#65533;n documento entregado', 'No document delivered'],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Total de estudiantes', 'Total students'],
    ['Fecha', 'Date'],
    ['Documentos sin entregar', 'Undelivered documents'],
    ['Masculinos', 'Males'],
    ['Femeninas', 'Females'],
]);

class nPDF extends PDF
{
    function header()
    {
        global $lang;
        global $year;
        parent::header();
    $this->SetFont('Arial', 'B', 13);
    $this->Cell(0, 5, utf8_decode('INFORME ACUMULATIVO DE NOTAS'), 0, 1, 'C');
    $this->SetFont('Arial', 'B', 11);

    $this->Ln(3);
  }

  public function footer()
  {
    $this->SetXY(10, -5);
    $this->SetMargins(10, -5);
    $this->SetFont('Arial', 'B', 10);
    $this->Cell(0, 5, utf8_encode('**La Persona que coteje el documento, escribe y firma su nombre sobre la línea correspondiente.'), 0, 0, 'C');
  }
}
function LN($valor)
{
  if ($valor == 'A') {
    return 4.00;
  } elseif ($valor == 'B') {
    return 3.00;
  } elseif ($valor == 'C') {
    return 2.00;
  } elseif ($valor == 'D') {
    return 1.00;
  } elseif ($valor == 'F') {
    return 0.00;
  }
}

function NL($valor)
{
  global $colegio;
  if ($valor === '') {
    return '';
  } else if ($valor >= 90 && $valor < 101) {
    return 'A';
  } else if ($valor >= 80 && $valor < 90) {
    return 'B';
  } else if ($valor >= 70 && $valor < 80) {
    return 'C';
  } else if ($valor >= 60 && $valor < 70) {
    return 'D';
  } elseif ($valor > 0 and $valor < 60) {
    return 'F';
  }
}

$school = new School(Session::id());
$year = $school->info('year2');
$teacherClass = new Teacher();
$studentClass = new Student();
$allGrades = $school->allGrades();

$colegio = DB::table('colegio')->where([
    ['usuario', 'administrador']
])->orderBy('id')->first();


$pdf = new nPDF();
$pdf->SetTitle($lang->translation("INFORME ACUMULATIVO DE NOTAS") . " $year", true);
$pdf->Fill();

$pdf->SetAutoPageBreak(false);
$pdf->SetDrawColor(0);
$aa = 0;
foreach ($allGrades as $grade) {
  $materias = [];
  $cursos = [];
  $estudiantes = [];
    $mat = DB::table('padres')->select('distinct curso, descripcion')->where([
        ['year', $year],
        ['grado', $grade],
        ['curso', '!=', ''],
        ['curso', 'NOT LIKE', '%AA-%']
    ])->orderBy('orden')->get();
    foreach ($mat as $mate) {

    if ($mate->descripcion == 'Espanol') {
      $materias[] = 'ESPAÑOL';
    } else {
      $materias[] = $mate->descripcion;
    }
    $cursos[] = $mate->curso;
  }

  $promedios = array();
  $result2 = DB::table('year')->select('distinct nombre,apellidos,ss,genero')->where([
        ['year', $year],
        ['grado', $grade],
        ['codigobaja', 0]
    ])->orderBy('apellidos')->get();

  $f = 0;
  $m = 0;
  foreach ($result2 as $row) {
    $estudiantes[] = $row;
    if ($row->genero == 1) {
      $f = $f + 1;
    } else {
      $m = $m + 1;
    }
  }
  $profe = $teacherClass->findByGrade($grade);
  $estus = $studentClass->findByGrade($grade);

  $pdf->SetFont('Times', '', 10);
  $pdf->Fill();
  $pdf->AddPage('L', 'Legal');
  $pdf->Cell(27, 5, 'Maestro/a:');
  $nom = $profe->nombre ?? '';
  $ape = $profe->apellidos ?? '';
  $pdf->Cell(100, 5, "$nom $ape", 'B');
  $pdf->Cell(0, 5, 'Grado: ' . $grade, 0, 0, 'C');
  $pdf->Cell(0, 5, utf8_encode("Año 20{$year[0]}{$year[1]} - 20{$year[3]}{$year[4]}"), 0, 1, 'R');
  $pdf->Ln(2);

  $pdf->Cell(20, 5, 'Matricula:');
  $pdf->Cell(37, 5, $f + $m, 'B', 1, 'C');
  $pdf->Ln(3);
  $pdf->Cell(12, 5, 'Fem.');
  $pdf->Cell(10, 5, $f, 'B', 0, 'C');
  $pdf->Cell(10);
  $pdf->Cell(12, 5, 'Masc.');
  $pdf->Cell(10, 5, $m, 'B', 1, 'C');
  $pdf->Ln(-14);

  $pdf->SetFont('Times', '', 10);
  $y = $pdf->GetY();
  $pdf->Cell(60, 5, '', 0, 1);
  $pdf->Cell(60, 10, '', 0, 1);
  $pdf->Cell(60, 5, 'NOMBRE DE ESTUDIANTE', 1, 1, 'C', true);
  $pdf->SetFont('Times', '', 8);
  for ($i = 0; $i < sizeof($estudiantes); $i++) {
    $pdf->Cell(6, 5, $i + 1, 1, 0, 'R');
    $pdf->Cell(54, 5, "{$estudiantes[$i]->apellidos} {$estudiantes[$i]->nombre}", 1, 1);
        $promedios[$estudiantes[$i]->ss]['sem1']['total'] = 0;
        $promedios[$estudiantes[$i]->ss]['sem2']['total'] = 0;
        $promedios[$estudiantes[$i]->ss]['sem1']['cantidad'] = 0;
        $promedios[$estudiantes[$i]->ss]['sem2']['cantidad'] = 0;
  }
  $largo = 28;
  $pdf->SetFont('Times', '', 9);

  for ($a = 0; $a < sizeof($materias); $a++) {
    $pdf->SetXY(70 + ($largo * $a), $y);
    $pdf->SetMargins(70 + ($largo * $a), $y);
    $pdf->SetFont('Times', '', 6);
    $pdf->Cell($largo, 5, ucwords($materias[$a]), 1, 1, 'C', true);
    $pdf->SetFont('Times', '', 9);
    $pdf->Cell($largo / 2, 5, 'Final', 'LRT', 0, 'C');
    $pdf->Cell($largo / 2, 5, 'Final', 'LRT', 1, 'C');
    $pdf->Cell($largo / 2, 5, '1er Sem.', 'LRB', 0, 'C');
    $pdf->Cell($largo / 2, 5, '2do Sem.', 'LRB', 1, 'C');
    $pdf->Cell(($largo / 2) / 2, 5, 'P', 1, 0, 'C', true);
    $pdf->Cell(($largo / 2) / 2, 5, 'N', 1, 0, 'C', true);
    $pdf->Cell(($largo / 2) / 2, 5, 'P', 1, 0, 'C', true);
    $pdf->Cell(($largo / 2) / 2, 5, 'N', 1, 1, 'C', true);

    for ($i = 0; $i < sizeof($estudiantes); $i++) {
      $curso = $cursos[$a];
      if (substr($grade, 0, 2) >= 7 && $cursos[$a] == 'SOC') {
        $curso = 'HIS';
      }
      if (substr($grade, 0, 2) >= 8 && $cursos[$a] == 'MAT') {
        $curso = 'ALG';
      }
      if (substr($grade, 0, 2) == 7 && $cursos[$a] == 'MAT') {
        $curso = 'PAL';
      }
      $padres = DB::table('padres')->where([
          ['curso', 'like', "$curso%"],
          ['ss', $estudiantes[$i]->ss],
          ['grado', $grade]
       ])->orderBy('orden')->first();
      $div1 = 0;
      $div2 = 0;
      $s1 = 0;
      $s2 = 0;
      if (substr($padres->grado ?? '', 0, 2) <= 3 && substr($padres->curso ?? '', 0, 2) == 'EF') {
        $pdf->Cell(($largo / 2) / 2, 5, $padres->nota2, 1, 0, 'C');
        $pdf->Cell(($largo / 2) / 2, 5, '', 1, 0, 'C');
        $pdf->Cell(($largo / 2) / 2, 5, $padres->nota4, 1, 0, 'C');
        $pdf->Cell(($largo / 2) / 2, 5, '', 1, 1, 'C');
      } else {
        if ($padres->nota1 ?? '' > 0 and $padres->nota1 ?? '' < 150 and is_numeric($padres->nota1 ?? '')) {
          $div1++;
          $s1 = $s1 + $padres->nota1;
        }
        if ($padres->nota2 ?? '' > 0 and $padres->nota2 ?? '' < 150 and is_numeric($padres->nota2 ?? '')) {
          $div1++;
          $s1 = $s1 + $padres->nota2;
        }
        if ($padres->nota3 ?? '' > 0 and $padres->nota3 ?? '' < 150 and is_numeric($padres->nota3 ?? '')) {
          $div2++;
          $s2 = $s2 + $padres->nota3;
        }
        if ($padres->nota4 ?? '' > 0 and $padres->nota4 ?? '' < 150 and is_numeric($padres->nota4 ?? '')) {
          $div2++;
          $s2 = $s2 + $padres->nota4;
        }
        $sem1 = ($div1 > 0) ? $s1 / $div1 : '';
        $sem2 = ($div2 > 0) ? $s2 / $div2 : '';

        $pdf->Cell(($largo / 2) / 2, 5, ($sem1 !== '') ? number_format((float) $sem1, 0) : '', 1, 0, 'C');
        $pdf->Cell(($largo / 2) / 2, 5, ($sem1 > 0) ? NL(round($sem1, 0)) : '', 1, 0, 'C');
        $pdf->Cell(($largo / 2) / 2, 5, ($sem2 !== '') ? number_format((float) $sem2, 0) : '', 1, 0, 'C');
        $pdf->Cell(($largo / 2) / 2, 5, ($sem2 > 0) ? NL(round($sem2, 0)) : '', 1, 1, 'C');

//        if ($sem1 !== '' and is_numeric($sem1)) {
        if ($sem1 > 0 and is_numeric($sem1)) {
          $promedios[$estudiantes[$i]->ss]['sem1']['total'] += $sem1;
//          $promedios[$estudiantes[$i]->ss]['sem1']['total'] += number_format((float) $sem1, 0);
          $promedios[$estudiantes[$i]->ss]['sem1']['cantidad']++;
        }
        if ($sem2 > 0 and is_numeric($sem2)) {
//          $promedios[$estudiantes[$i]->ss]['sem2']['total'] += number_format((float) $sem2, 0);
          $promedios[$estudiantes[$i]->ss]['sem2']['total'] += $sem2;
          $promedios[$estudiantes[$i]->ss]['sem2']['cantidad']++;
        }
      }

    }
  }
  // echo "<pre>";
  // var_dump($promedios);

  $pdf->SetFillColor(237, 125, 49);
  $pdf->SetXY(70 + ($largo * sizeof($materias)), $y);
  $pdf->SetMargins(70 + ($largo * sizeof($materias)), $y);
  $pdf->Cell(20, 5, 'Promedio', 'LTR', 1, 'C', true);
  $pdf->Cell(20, 5, 'General y', 'LR', 1, 'C', true);
  $pdf->Cell(20, 5, 'Nota Final', 'LBR', 1, 'C', true);
  $pdf->Cell(10, 5, 'PGF', 1, 0, 'C', true);
  $pdf->Cell(10, 5, 'NGF', 1, 1, 'C', true);


  for ($i = 0; $i < sizeof($estudiantes); $i++) {
//    $prom1 = ($promedios[$estudiantes[$i]->ss]['sem1']['cantidad'] > 0) ? $promedios[$estudiantes[$i]->ss]['sem1']['total'] / $promedios[$estudiantes[$i]->ss]['sem1']['cantidad'] : '';
    $prom1 = 0;
    $prom2 = 0;
    $div = 0;

    if ($promedios[$estudiantes[$i]->ss]['sem1']['cantidad'] > 0) {
      $prom1 = ($promedios[$estudiantes[$i]->ss]['sem1']['cantidad'] > 0) ? $promedios[$estudiantes[$i]->ss]['sem1']['total'] / $promedios[$estudiantes[$i]->ss]['sem1']['cantidad'] : '';
      $div++;
    }
    if ($promedios[$estudiantes[$i]->ss]['sem2']['cantidad'] > 0) {
      $prom2 = ($promedios[$estudiantes[$i]->ss]['sem2']['cantidad'] > 0) ? $promedios[$estudiantes[$i]->ss]['sem2']['total'] / $promedios[$estudiantes[$i]->ss]['sem2']['cantidad'] : '';
      $div++;
    }

    $final = ($div > 0) ? (float) ($prom1 + $prom2) / $div : '';
    $pdf->Cell(10, 5, ($div > 0) ? round($final) : '', 1, 0, 'C');
    $pdf->Cell(10, 5, ($div > 0) ? NL(round($final)) : '', 1, 1, 'C');
  }
  $pdf->Ln(3);
  $pdf->SetX(10);
  $pdf->SetLeftMargin(10);
  if (in_array('Huerto*', $materias)) {
    $pdf->Cell(0, 5, '* Electiva Huerto: Siembra, Composta y Mariposario', 0, 1);
  }
  $pdf->Cell(0, 5, '* Documento oficial, se entrega estintado.', 0, 1);
  $pdf->Cell(80);
  $pdf->Cell(50, 5, '', 'B');
  $pdf->Cell(45);
  $pdf->Cell(50, 5, '', 'B', 1);
  $pdf->Cell(80);
  $pdf->Cell(50, 5, "Diciembre 20{$year[0]}{$year[1]}", 0, 0, 'C');
  $pdf->Cell(45);
  $pdf->Cell(50, 5, "Mayo 20{$year[3]}{$year[4]}", 0, 1, 'C');
}
$pdf->Output();
