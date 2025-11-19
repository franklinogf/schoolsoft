<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;
use Classes\Util;

Session::is_logged();

$lang = new Lang([['Informe acumulativo de notas', 'Cumulative grade report'],
  ["Maestro(a):", "Teacher:"],
  ["Grado:", "Grade:"],
  ["Matricula:", "Tuition"],
  ["ENTREGADO", "DELIVERED"],
  ['Cuenta', 'Account'],
  ['Ningún documento entregado', 'No document delivered'],
  ['Apellidos', 'Surnames'],
  ['Nombre', 'Name'],
  ['Total de estudiantes', 'Total students'],
  ['Fecha', 'Date'],
  ['Documentos sin entregar', 'Undelivered documents'],
  ['Masculinos', 'Males'],
  ['Femeninas', 'Females'],
]);

function NL($valor)
{

  if ($valor === '') {
    return '';
  } else if ($valor >= 90 && $valor <= 100) {
    return 'A';
  } else if ($valor >= 80 && $valor <= 89) {
    return 'B';
  } else if ($valor >= 70 && $valor <= 79) {
    return 'C';
  } else if ($valor >= 60 && $valor <= 69) {
    return 'D';
  } elseif ($valor > 0 and $valor <= 59) {
    return 'F';
  }
}

$school = new School(Session::id());
$teacherClass = new Teacher();
$studentClass = new Student();

$year = $school->info('year2');
$allGrades = $school->allGrades();
$pdf = new PDF();
$pdf->useFooter(false);

//$pdf->Footer = true;
//$pdf->useFooter(false);

$pdf->SetTitle($lang->translation("Informe acumulativo de notas") . " $year", true);
$pdf->Fill();

$docs = $_POST['option'] ?? '';
$doc1 = DB::table('docu_entregados')->where([['codigo', $docs],
])->orderBy('codigo')->first();

foreach ($allGrades as $grade) {
  $materias = [];
  $cursos = [];
  $cursos2 = [];
  $estudiantes = [];
  $promedios = [];
  $pdf->SetFillColor(89, 171, 227);
  $cursos = DB::table('padres')->select('distinct curso, descripcion')->where([
    ['year', $year],
    ['grado', $grade],
    ['curso', '!=', ''],
    ['curso', 'NOT LIKE', '%AA-%'],
  ])->orderBy('orden')->get();

  foreach ($cursos as $curso) {
    if ($curso->descripcion == 'Espanol') {
      $materias[] = 'ESPAÑOL';
    } else {
      $materias[] = $curso->descripcion;
    }
    $cursos2[] = $curso->curso;
  }

  $teacher = $teacherClass->findByGrade($grade);
  $estus = $studentClass->findByGrade($grade);
  $genderCount = ['M' => 0, 'F' => 0, 'T' => 0];
  $pdf->useFooter(false);
  $pdf->AddPage('L', 'Legal');
  $pdf->useFooter(false);
  $pdf->SetFont('Arial', 'B', 15);
  $pdf->Cell(0, 5, $lang->translation("Informe acumulativo de notas") . " $year", 0, 1, 'C');
  $pdf->Ln(5);
  $pdf->SetFont('Arial', '', 12);
  $pdf->splitCells($teacher ? $lang->translation("Maestro(a):") . " $teacher->nombre $teacher->apellidos" : '', $lang->translation("Grado:") . " $grade");
  $f = 0;
  $m = 0;
  foreach ($estus as $estu) {
    $estudiantes[] = $estu;
    $promedios[$estu->ss] = [
      'sem1' => ['total' => 0, 'cantidad' => 0],
      'sem2' => ['total' => 0, 'cantidad' => 0],
    ];
    if ($estu->genero == 1 or $estu->genero == 'F') {
      $f = $f + 1;
    } else {
      $m = $m + 1;
    }
  }
  $students = $studentClass->findByGrade($grade);

  $pdf->Cell(20, 5, $lang->translation("Matricula:"));
  $pdf->Cell(37, 5, $f + $m, 'B', 1, 'C');
  $pdf->Ln(3);
  $pdf->Cell(12, 5, 'Fem.');
  $pdf->Cell(10, 5, $f, 'B', 0, 'C');
  $pdf->Cell(10);
  $pdf->Cell(12, 5, 'Masc.');
  $pdf->Cell(10, 5, $m, 'B', 1, 'C');
  $pdf->Ln(-13);
  $y = $pdf->GetY();
  $pdf->SetFont('Arial', '', 8);
  $pdf->Ln(15);
  $pdf->Cell(60, 5, 'NOMBRE DE ESTUDIANTE', 1, 1, 'C', true);

  for ($i = 0; $i < sizeof($estudiantes); $i++) {
    $pdf->Cell(6, 5, $i + 1, 1, 0, 'R');
    $pdf->Cell(54, 5, "{$estudiantes[$i]->nombre} {$estudiantes[$i]->apellidos}", 1, 1);
  }
  $largo = 28;

  $pdf->SetFont('Arial', 'B', 8);
  $pdf->SetFont('Arial', '', 10);

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
      $curso = $cursos2[$a];
      if (substr($grade, 0, 2) >= 7 && $cursos[$a] == 'SOC') {
        //                $curso = 'HIS';
      }
      if (substr($grade, 0, 2) >= 8 && $cursos[$a] == 'MAT') {
        //                $curso = 'ALG';
      }
      if (substr($grade, 0, 2) == 7 && $cursos[$a] == 'MAT') {
        //                $curso = 'PAL';
      }

      $ss = $estudiantes[$i]->ss;
      $padres = DB::table('padres')->where([['curso', $curso],
        ['ss', $ss],
        ['grado', $grade],
      ])->orderBy('orden')->first();

      $div1 = 0;
      $div2 = 0;
      $div11 = 0;
      $div22 = 0;
      if (substr($grade, 0, 2) <= 3 && substr($padres->curso, 0, 2) ?? '' == 'EF') {
        $pdf->Cell(($largo / 2) / 2, 5, $padres->nota2, 1, 0, 'C');
        $pdf->Cell(($largo / 2) / 2, 5, '', 1, 0, 'C');
        $pdf->Cell(($largo / 2) / 2, 5, $padres->nota4, 1, 0, 'C');
        $pdf->Cell(($largo / 2) / 2, 5, '', 1, 1, 'C');
      } else {
        if ($padres->nota1 ?? '' != '') {
          $div1++;
          $div11 = $div11 + $padres->nota1;
        }
        if ($padres->nota2 ?? '' != '') {
          $div1++;
          $div11 = $div11 + $padres->nota2;
        }
        if ($padres->nota3 ?? '' != '') {
          $div2++;
          $div22 = $div22 + $padres->nota3;
        }
        if ($padres->nota4 ?? '' != '') {
          $div2++;
          $div22 = $div22 + $padres->nota4;
        }
        //                $sem1 = ($div1 > 0) ? ($padres->nota1 + $padres->nota2) / $div1 : '';
        $sem1 = ($div1 > 0) ? $div11 / $div1 : '';
        //                $sem2 = ($div2 > 0) ? ($padres->nota3 + $padres->nota4) / $div2 : '';
        $sem2 = ($div2 > 0) ? $div22 / $div2 : '';

        $pdf->Cell(($largo / 2) / 2, 5, ($sem1 !== '') ? number_format((float) $sem1, 0) : '', 1, 0, 'C');
        $pdf->Cell(($largo / 2) / 2, 5, ($sem1 !== '') ? NL(round($sem1, 0)) : '', 1, 0, 'C');
        $pdf->Cell(($largo / 2) / 2, 5, ($sem2 !== '') ? number_format((float) $sem2, 0) : '', 1, 0, 'C');
        $pdf->Cell(($largo / 2) / 2, 5, ($sem2 !== '') ? NL(round($sem2, 0)) : '', 1, 1, 'C');

        $promedios[$estudiantes[$i]->ss]['sem1']['total'] += intval($sem1);
        if ($sem1 !== '') {
          $promedios[$estudiantes[$i]->ss]['sem1']['cantidad']++;
        }
        $promedios[$estudiantes[$i]->ss]['sem2']['total'] += intval($sem2);
        if ($sem2 !== '') {
          $promedios[$estudiantes[$i]->ss]['sem2']['cantidad']++;
        }
      }
    }
  }

  $pdf->SetFillColor(237, 125, 49);
  $pdf->SetXY(70 + ($largo * sizeof($materias)), $y);
  $pdf->SetMargins(70 + ($largo * sizeof($materias)), $y);
  $pdf->Cell(20, 5, 'Promedio', 'LTR', 1, 'C', true);
  $pdf->Cell(20, 5, 'General y', 'LR', 1, 'C', true);
  $pdf->Cell(20, 5, 'Nota Final', 'LBR', 1, 'C', true);
  $pdf->Cell(10, 5, 'PGF', 1, 0, 'C', true);
  $pdf->Cell(10, 5, 'NGF', 1, 1, 'C', true);

  for ($i = 0; $i < sizeof($estudiantes); $i++) {
    $prom1 = ($promedios[$estudiantes[$i]->ss]['sem1']['cantidad'] ?? 0 > 0) ? $promedios[$estudiantes[$i]->ss]['sem1']['total'] / $promedios[$estudiantes[$i]->ss]['sem1']['cantidad'] : '';
    $prom2 = ($promedios[$estudiantes[$i]->ss]['sem2']['cantidad'] ?? 0 > 0) ? $promedios[$estudiantes[$i]->ss]['sem2']['total'] / $promedios[$estudiantes[$i]->ss]['sem2']['cantidad'] : '';
    $div = 0;
    $div2 = 0;

    if ($prom1 > 0) {
      $div++;
      $div2 = $div2 + $prom1;
    }
    if ($prom2 > 0) {
      $div++;
      $div2 = $div2 + $prom2;
    }

    //        $final = ($div > 0) ? number_format((float) ($prom1 + $prom2) / $div, 0) : '';
    $final = ($div > 0) ? number_format((float) $div2 / $div, 0) : '';
    //        $pdf->Cell(10, 5, number_format((float) $final, 0), 1, 0, 'C');
    $pdf->Cell(10, 5, ($final > 0) ?  number_format((float) $final, 0) : '', 1, 0, 'C');
    $pdf->Cell(10, 5, NL($final), 1, 1, 'C');
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
  list($y1, $y2) = explode("-", $year);

  $pdf->Cell(50, 5, "Diciembre 20{$y1}", 0, 0, 'C');
  //  $pdf->Cell(50, 5, "Diciembre 20{$cole->year[0]}{$cole->year[1]}", 0, 0, 'C');
  $pdf->Cell(45);
  $pdf->Cell(50, 5, "Mayo 20{$y2}", 0, 1, 'C');
  //  $pdf->Cell(50, 5, "Mayo 20{$cole->year[3]}{$cole->year[4]}", 0, 1, 'C');

  $pdf->SetXY(10, -5);
  $pdf->SetMargins(10, -10);
  $pdf->SetFont('Arial', 'B', 9);
  $pdf->Cell(0, 4, utf8_encode('**La Persona que coteje el documento, escribe y firma su nombre sobre la línea correspondiente.'), 0, 0, 'C');

}

$pdf->Output();
