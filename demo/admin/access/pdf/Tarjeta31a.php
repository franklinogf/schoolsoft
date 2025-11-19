<?php
require_once __DIR__ . '/../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;
use Classes\DataBase0\DB;

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

$promedio = [];
$promedio['01']=0;
$promedio['02']=0;
$promedio['03']=0;
$promedio['04']=0;
$promedio['05']=0;
$promedioLetters = [];
$promedioLetters['01']=0;
$promedioLetters['02']=0;
$promedioLetters['03']=0;
$promedioLetters['04']=0;
$promedioLetters['05']=0;
$cant = [];
$cant['01']=0;
$cant['02']=0;
$cant['03']=0;
$cant['04']=0;
$cant['05']=0;
$creditos = [];
$creditos['01']=0;
$creditos['02']=0;
$creditos['03']=0;
$creditos['04']=0;
$creditos['05']=0;

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
function Grado($valor)
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


class nPDF extends PDF
{
    function Header()
    {
        parent::header();
    }
}


$pdf = new nPDF();
$pdf->Fill();

$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(false);
$pdf->useFooter(false);
$pdf->SetFont('Arial', '', 11);
if ($opcion == '2') {
    $students = DB::table('year')
        ->whereRaw("year = '$Year' and grado = '$grados' and activo = ''")->orderBy('apellidos')->get();
} else {
    $students = DB::table('acumulativa')->select("DISTINCT ss, nombre, apellidos")
        ->whereRaw("ss = '$estu'")->orderBy('apellidos')->get();
}
//******************

  function Year($grado, $ss)
  {
    $row = DB::table('acumulativa')->select("DISTINCT grado")
        ->whereRaw("ss = '$ss' and grado like '$grado%'")->first();

    return $row->year ?? '';
  }

  function Maestro($grado, $ss)
  {
    if (Year($grado, $ss)) {
      $row = DB::table('profesor')->select("nombre")
        ->whereRaw("grado like '$grado%'")->first();
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
    global $conducta;
    global $promedio;
    global $promedioLetters;
    global $cant;
    // if (Year($grado, $ss)) {
    foreach ($cursos as $curso) {
       $row = DB::table('acumulativa')
            ->whereRaw("ss = '$ss' and grado like '$grado%' and curso like '$curso%'")->first();
      if ($row->ss ?? 0 > 0) {
        $c = 0;
        if ($row->sem1 != '') {
          $c++;
        }
        if ($row->sem2 != '') {
          $c++;
        }
        if ($c == 0) {
          $c = 1;
        }
        $valor = (is_numeric($row->sem1) + is_numeric($row->sem2)) / $c;

        if ($row->sem1 != '' || $row->sem2 != '') {
          if (!is_numeric($row->sem1) && !is_numeric($row->sem2)) {
            return $row->sem2;
          } else {
            $promedio[$grado] += $valor;
            $cant[$grado]++;
            return round($valor);
          }
        } else {
          return '-';
        }
      } else {
        return '-';
      }
    }
  }
  $cursos = [
    'EDUC. CRIST.' => ['XNA'],
    'ESPAÑOL' => ['ESP'],
    'INGLÉS' => ['ING'],
    'MATEMÁTICAS' => ['MAT'],
    'SALUD' => ['SLD'],
    'CIENCIA' => ['CIE'],
    'ESTUDIOS SOCIALES' => ['SOC'],
    'EDUCACIÓN FÍSICA' => ['EDF'],
    'ARTE' => ['ART'],
    'CONV. ENGLISH' => ['CEN'],
    'TECNOLOGÍA' => ['COM'],
    'MUSICA' => ['MUS'],
  ];
  #1


foreach ($students as $estu) {
    $pdf->AddPage();
    $info1 = DB::table('year')->select("id, ss, dir1, grado, fecha")
        ->whereRaw("ss = '$estu->ss'")->orderBy('apellidos')->first();

    $info2 = DB::table('madre')->select("encargado")
        ->whereRaw("id = '$info1->id'")->first();

  $pdf->Ln(10);
  $pdf->Cell(0, 5, utf8_encode('TRANSCRIPCIÓN DE CREDITOS'), 0, 1, 'C');
  $pdf->Cell(0, 5, utf8_encode('NIVEL ELEMENTAL'), 0, 1, 'C');
  $pdf->Ln(10);

  $pdf->Cell(20, 5, 'Nombre:');
  $pdf->Cell(100, 5, "$estu->apellidos $estu->nombre", 'B');

  $pdf->Cell(0, 5, "Seguro Social: XXX-XX-" . substr($estu->ss, -4), 0, 1, 'L');

  $pdf->Ln(4);
  $pdf->Cell(40, 5, 'Fecha de nacimiento:');
  $pdf->Cell(35, 5, $info1->fecha === '0000-00-00' ? 'N/A' : $info1->fecha, 'B', 0, 'C');

  $pdf->Cell(15, 5, 'Edad:', 0, 0, 'R');
  $pdf->Cell(10, 5, getAge($info1->fecha), 'B', 1, 'C');

  $pdf->Ln(5);



$promedio = [];
$promedio['01']=0;
$promedio['02']=0;
$promedio['03']=0;
$promedio['04']=0;
$promedio['05']=0;
$promedioLetters = [];
$promedioLetters['01']=0;
$promedioLetters['02']=0;
$promedioLetters['03']=0;
$promedioLetters['04']=0;
$promedioLetters['05']=0;
$cant = [];
$cant['01']=0;
$cant['02']=0;
$cant['03']=0;
$cant['04']=0;
$cant['05']=0;
$creditos = [];
$creditos['01']=0;
$creditos['02']=0;
$creditos['03']=0;
$creditos['04']=0;
$creditos['05']=0;
  $conducta = [];
  $grados = DB::table('acumulativa')->select("DISTINCT grado")
        ->whereRaw("ss = '$estu->ss' and (grado not like '06%' and grado not like '07%' and grado not like '08%' and grado not like '09%' and grado not like '10%' and grado not like '11%' and grado not like '12%')")->get();


  $GRADOS = ['01', '02', '03', '04', '05'];
  $WIDTH = 30;
  $pdf->Cell(40, 6, utf8_encode('AÑO ESCOLAR'), 1, 0, 'L', true);
  foreach ($GRADOS as $i => $GRA) {
    $pdf->Cell($WIDTH, 6, Year($GRA, $estu->ss), 1, ($i == count($GRADOS) - 1) ? 1 : 0, 'C', true);
  }
  #2
  $pdf->SetFont('Arial', '', 10);
  $pdf->Cell(40, 6, 'Asignaturas', 1, 0, 'L', true);
  $pdf->SetFont('Arial', 'B', 11);
  $pdf->Cell($WIDTH, 6, 'Primero', 1, 0, 'C', true);
  $pdf->Cell($WIDTH, 6, 'Segundo', 1, 0, 'C', true);
  $pdf->Cell($WIDTH, 6, 'Tercero', 1, 0, 'C', true);
  $pdf->Cell($WIDTH, 6, 'Cuarto', 1, 0, 'C', true);
  $pdf->Cell($WIDTH, 6, 'Quinto', 1, 1, 'C', true);
  $pdf->SetFont('Arial', '', 10);

  foreach ($cursos as $nombre => $curso) {
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, utf8_encode($nombre), 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    foreach ($GRADOS as $i => $GRA) {
      $grade = Curso($GRA, $curso, $estu->ss);
      if ($grade === '-') {
        $pdf->SetFont('Arial', '', 20);
      } else {
        $pdf->SetFont('Arial', '', 11);
      }
      $pdf->Cell($WIDTH, 6, $grade, 1, ($i == count($GRADOS) - 1) ? 1 : 0, 'C');
    }
  }

  $pdf->SetFont('Arial', '', 10);
  $pdf->Cell(40, 6, 'PROMEDIO', 1, 0, 'L', true);
  $pdf->SetFont('Arial', '', 11);

  foreach ($GRADOS as $i => $GRA) {
    $prom = ($promedio[$GRA] == 0) ? 0 : round(($promedio[$GRA] / $cant[$GRA]));
    $pdf->Cell($WIDTH, 6, $prom == 0 ? '' : $prom, 1, ($i == count($GRADOS) - 1) ? 1 : 0, 'C');
  }

  \setlocale(LC_ALL, 'es_ES');
  $newDate = strftime('%B %d, %Y', strtotime(date("Y-m-d")));

  $pdf->Ln(10);
  $pdf->Cell(0, 5, utf8_encode("Certifico que la información ofrecida en este documento es correcta según nuestro expediente académico."), 0, 1);
  $pdf->Cell(0, 5, "Observaciones:", 0, 1);
  $pdf->Ln(10);
  $pdf->Cell(0, 5, "Expedido hoy " . $newDate . " en Gurabo, Puerto Rico", 0, 1);
  $pdf->Ln(15);

  $Y = $pdf->GetY();
  $pdf->Cell(50, 5, '', 'B');
  $pdf->Cell(30);
  $pdf->Cell(50, 5, '', 'B');
  $pdf->Cell(30);
  $pdf->Cell(50, 5, 'Sello', 0, 1);

  $pdf->Cell(50, 5, 'Firma Directora Interina', 0, 0, 'C');
  $pdf->Cell(30);
  $pdf->Cell(50, 5, 'Oficial de registro', 0, 0, 'C');
  $pdf->Image('../../../logo/firma_acumalativa_31_1.png', 15, $Y - 6, 45);
  $pdf->Image('../../../logo/firma_acumalativa_31_2.png', 100, $Y - 4, 30);
}
$pdf->Output();

