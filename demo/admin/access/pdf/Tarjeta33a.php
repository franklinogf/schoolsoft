<?php
require_once __DIR__ . '/../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;
use Classes\DataBase\DB;

Session::is_logged();

$lang = new Lang([
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],

]);

$school = new School(Session::id());
function getAge($date)
{
  if ($date !== '' && $date !== '0000-00-00') {
    list($year, $month, $day) = explode("-", $date);
    $yearDifference = date("Y") - $year;
    $monthDifference = date("m") - $month;
    $dayDifference = date("d") - $day;
    if ($dayDifference < 0 && $monthDifference <= 0 || date("m") < $month) {
      $yearDifference--;
    }
    return $yearDifference;
  } else {
    return '';
  }
}
function NumberToLetter($valor)
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
  } else if ($valor <= '59') {
    return 'F';
  }
}

function Year($grado, $ss)
{
    $row = DB::table('acumulativa')
        ->whereRaw("ss = '$ss' and grado like '$grado%'")->orderBy('apellidos')->first();
    return $row->year ?? '';

}

function Maestro($grado, $ss)
{
    if (Year($grado, $ss)) {
        $row = DB::table('profesor')
            ->whereRaw("grado like '$grado%'")->orderBy('apellidos')->first();

        return $row->nombre ?? '';
    } else {
        return '';
    }
}
function Con($valor)
{
  if ($valor == 'A') {
    return 4;
  } elseif ($valor == 'B') {
    return 3;
  } elseif ($valor == 'C') {
    return 2;
  } elseif ($valor == 'D') {
    return 1;
  } elseif ($valor == 'F') {
    return 0;
  } elseif ($valor == '') {
    return '';
  }
}
function Conducta($valor)
{
  if ($valor >= 3.5 && $valor <= 4) {
    return 'A';
  } elseif ($valor >= 2.5 && $valor <= 3.49) {
    return 'B';
  } elseif ($valor >= 1.5 && $valor <= 2.49) {
    return 'C';
  } elseif ($valor >= 0.8 && $valor <= 1.49) {
    return 'D';
  } elseif ($valor >= 0 && $valor <= 0.79) {
    return 'F';
  }
}

function Curso($grado, $cursos, $ss)
{
  global $promedio;
  global $promedioLetters;
  global $cant;
  foreach ($cursos as $curso) {
    $row = DB::table('acumulativa')
            ->whereRaw("ss = '$ss' and grado like '$grado%' and curso like '$curso%'")->first();
    if ($row->grado ?? 0 > 0) {
      if ($row->semestre1 != '' || $row->semestre2 != '') {
        if (!is_numeric($row->semestre1) || !is_numeric($row->semestre2)) {
          return [$row->semestre1, ''];
        } else {
          if ($row->semestre1 != '') {
            $promedio[$grado] += $row->semestre1;
            $promedioLetters[$grado] += Con(NumberToletter(round($row->semestre1)));
            $cant[$grado]++;
          }
          if ($row->semestre2 != '') {
            $promedioLetters[$grado] += Con(NumberToletter(round($row->semestre2)));
            $promedio[$grado] += $row->semestre2;
            $cant[$grado]++;
          }

          return ["$row->semestre1 " . NumberToletter($row->semestre1), "$row->semestre2 " . NumberToletter($row->semestre2)];
        }
      } else {
        return '';
      }
    }
  }
}

class nPDF extends PDF
{
  function Header()
  {
    parent::header();
    $this->Ln(-5);
  }

  function Footer()
  {
    $this->SetY(-50);

    $this->Cell(80, 5, '', 'B', 1);
    $this->Cell(80, 5, utf8_encode('Enid M. Rodriguez Roldán'), 0, 0, 'C');
    $this->Cell(70);
    $this->Cell(50, 5, 'SELLO', 0, 1);
    $this->Cell(80, 5, 'Registradora', 0, 1, 'C');
  }
}
$pdf = new nPDF();
$pdf->Fill();
$pdf->AliasNbPages();
//$pdf->SetFillColor(240);
$pdf->SetFont('Arial', '', 11);
if ($opcion == '2') {
    $students = DB::table('year')
        ->whereRaw("year = '$Year' and grado = '$grados' and activo = ''")->orderBy('apellidos')->get();
} else {
    $students = DB::table('acumulativa')->select("DISTINCT ss, nombre, apellidos")
        ->whereRaw("ss = '$estu'")->orderBy('apellidos')->get();
}

foreach ($students as $estu) {
        $pdf->AddPage();
$promedio = [];
$promedio['01']=0;
$promedio['02']=0;
$promedio['03']=0;
$promedio['04']=0;
$promedio['05']=0;
$promedio['06']=0;
$promedio['07']=0;
$promedio['08']=0;
$promedioLetters = [];
$promedioLetters['01']=0;
$promedioLetters['02']=0;
$promedioLetters['03']=0;
$promedioLetters['04']=0;
$promedioLetters['05']=0;
$promedioLetters['06']=0;
$promedioLetters['07']=0;
$promedioLetters['08']=0;
$cant = [];
$cant['01']=0;
$cant['02']=0;
$cant['03']=0;
$cant['04']=0;
$cant['05']=0;
$cant['06']=0;
$cant['07']=0;
$cant['08']=0;
$creditos = [];
$creditos['01']=0;
$creditos['02']=0;
$creditos['03']=0;
$creditos['04']=0;
$creditos['05']=0;
$creditos['06']=0;
$creditos['07']=0;
$creditos['08']=0;

  $info1 = DB::table('year')->select("id, ss, dir1, grado, fecha")
        ->whereRaw("ss = '$estu->ss'")->orderBy('apellidos')->first();

  $info2 = DB::table('madre')->select("encargado")
        ->whereRaw("id = '$info1->id'")->first();

  $pdf->Ln(8);
  $pdf->Cell(0, 5, utf8_encode('TRANSCRIPCIÓN DE CRÉDITOS'), 0, 1, 'C');
  $pdf->Cell(0, 5, 'ESCUELA ELEMENTAL', 0, 1, 'C');
  $pdf->Ln(8);

  $pdf->Cell(20, 5, 'Nombre:');
  $pdf->Cell(100, 5, "$estu->apellidos $estu->nombre", 'B');

  $pdf->Cell(13, 5, 'Edad:');
  $pdf->Cell(10, 5, getAge($info1->fecha), 'B', 1, 'C');

  $pdf->Ln(4);
  $pdf->Cell(40, 5, "SS: XXX-XX-" . substr($estu->ss, -4), 0, 1, 'L');
  $pdf->Ln(5);

  $grados = DB::table('acumulativa')->select("DISTINCT grado")
        ->whereRaw("ss = '$estu->ss' and (grado not like '12%' and grado not like '11%' and grado not like '10%' and grado not like '09%')")->get();

  $cursos = [
    'MATEMÁTICAS' => ['MAT'],
    'INGLÉS' => ['ING'],
    'ESPAÑOL' => ['ESP'],
    'CIENCIA' => ['CIE'],
    'ESTUDIOS SOCIALES' => ['SOC'],
    'EDUCACIÓN FÍSICA' => ['EF'],
    'EDUC. CRIST.' => ['EC'],
    'SALUD' => ['SAL'],
    'BELLAS ARTES' => ['BA'],
    'TECNOLOGÍA' => ['TEC'],
    'STEM' => ['SCM', 'COA', 'STE'],

  ];
  #1
  $GRADOS = ['01', '02', '03','04','05'];
  $WIDTH = 30;
  $pdf->Cell(40, 6, utf8_encode('AÑO ESCOLAR'), 1, 0, 'L', true);
  foreach ($GRADOS as $i => $GRA ) {
        $pdf->Cell($WIDTH, 6, Year($GRA, $estu->ss), 1, ($i == count($GRADOS) - 1) ? 1 : 0, 'C', true);
    }
  #2
  $pdf->Cell(40, 6, '', 1, 0, 'L', true);
  $pdf->SetFont('Arial', 'B', 11);
  $pdf->Cell(30, 6, 'Primero', 1, 0, 'C', true);
  $pdf->Cell(30, 6, 'Segundo', 1, 0, 'C', true);
  $pdf->Cell(30, 6, 'Tercero', 1, 0, 'C', true);
  $pdf->Cell(30, 6, 'Cuarto', 1, 0, 'C', true);
  $pdf->Cell(30, 6, 'Quinto', 1, 1, 'C', true);
  $pdf->Cell(40, 6, 'Asignaturas', 1, 0, 'L', true);
  $pdf->SetFont('Arial', '', 10);
  $pdf->Cell(15, 6, 'Sem 1', 1, 0, 'C', true);
  $pdf->Cell(15, 6, 'Sem 2', 1, 0, 'C', true);
  $pdf->Cell(15, 6, 'Sem 1', 1, 0, 'C', true);
  $pdf->Cell(15, 6, 'Sem 2', 1, 0, 'C', true);
  $pdf->Cell(15, 6, 'Sem 1', 1, 0, 'C', true);
  $pdf->Cell(15, 6, 'Sem 2', 1, 0, 'C', true);
  $pdf->Cell(15, 6, 'Sem 1', 1, 0, 'C', true);
  $pdf->Cell(15, 6, 'Sem 2', 1, 0, 'C', true);
  $pdf->Cell(15, 6, 'Sem 1', 1, 0, 'C', true);
  $pdf->Cell(15, 6, 'Sem 2', 1, 1, 'C', true);
  $pdf->SetFont('Arial', '', 10);
  foreach ($cursos as $nombre => $curso) {
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, utf8_encode($nombre), 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    foreach ($GRADOS as $i => $GRA ) {
      $grade = Curso($GRA, $curso, $estu->ss);
      $pdf->Cell(15, 6, $grade[0] ?? '', 1, 0, 'C');
      $pdf->Cell(15, 6, $grade[1] ?? '', 1, ($i == count($GRADOS) - 1) ? 1 : 0, 'C');
    }
  }
  $pdf->SetFont('Arial', '', 10);
  $pdf->Cell(40, 6, 'PROMEDIO', 1, 0, 'L', true);
  $pdf->SetFont('Arial', 'B', 11);
  foreach ($GRADOS as $i => $GRA ) {
    $prom = ($promedio[$GRA] == 0) ? 0 : round(($promedio[$GRA] / $cant[$GRA]));
    $pdf->Cell(30, 6, $prom == 0 ? '' : round($prom) . ' ' . NumberToLetter(round($prom)), 1, ($i == 5) ? 1 : 0, 'C');
  }

  // Segunda parte
   $pdf->Ln(3);
  // var_dump($cant);
  // var_dump($promedio);
  // echo '</pre>';
  $pdf->SetFont('Arial', '', 10);
  $gpaProm = array_sum($promedio);
  $gpaCant = array_sum($cant);
  $gpaProm2 = array_sum($promedioLetters);
  $gpa = $gpaCant > 0 ? round($gpaProm / $gpaCant) : '';
  $gpa2 = $gpaCant > 0 ? number_format($gpaProm2 / $gpaCant, 2) : '';
  $gpa3 = $gpa . '.00';
  $nota1 = DB::table('tablas')
        ->whereRaw("valor = '$gpa3'")->first();
  $punto = number_format($nota1->punto,2) ?? '';

  $pdf->Ln(5);
  $pdf->Cell(0, 5, "GPA: $gpa / $punto", 0, 1);
  $pdf->Cell(27, 5, "Comentarios:", 0, 1);
  $pdf->Cell(0, 5, $memsa1 ?? '', 'B', 1);
  $pdf->Cell(0, 5, $memsa2 ?? '', 'B', 1);
  $pdf->Ln(5);
  $pdf->Cell(0, 5, "Expedido en Juncos P.R. hoy " . date('Y-m-d'), 0, 1);
  $pdf->Ln(5);
  $pdf->Cell(0, 5, 'MS - Muy Satisfactorio', 0, 1);
  $pdf->Cell(0, 5, 'P - Pendiente', 0, 1);
  $pdf->Ln(5);
  $pdf->Cell(0, 5, utf8_encode('La información se obtuvo del récord acumulativo de la oficina del director(a). No tiene borrones ni tachaduras.'), 0, 1);
  $pdf->Ln(5);
}
$pdf->Output();
