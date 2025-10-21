<?php
require_once '../../../app.php';

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
$promedio['09']=0;
$promedio['10']=0;
$promedio['11']=0;
$promedio['12']=0;
$promedioLetters = [];
$promedioLetters['09']=0;
$promedioLetters['10']=0;
$promedioLetters['11']=0;
$promedioLetters['12']=0;
$cant = [];
$cant['09']=0;
$cant['10']=0;
$cant['11']=0;
$cant['12']=0;
$creditos = [];
$creditos['09']=0;
$creditos['10']=0;
$creditos['11']=0;
$creditos['12']=0;

$CEP = $cep === 'Si' ? true : false;
function getAge($date)
{
  if ($date !== '' && $date !== '0000-00-00') {
    list($year, $month, $day) = explode("-", $date);
    $yearDifference  = date("Y") - $year;
    $monthDifference = date("m") - $month;
    $dayDifference   = date("d") - $day;
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
  } else  if ($valor <= '59') {
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

//**************
  function Year($grado, $ss)
  {
    $row = DB::table('acumulativa')->select("DISTINCT grado, year")
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

  function  Curso($grado, $cursos, $ss)
  {
    global $conducta;
    global $promedio;
    global $unidades;
    global $cant;
    global $colegio;
    global $CEP;
    global $creditos;
    foreach ($cursos as $curso) {
        $row = DB::table('acumulativa')
            ->whereRaw("ss = '$ss' and grado like '$grado%' and curso like '$curso%'")->first();

      if ($row->ss ?? 0 > 0) {
        $c = 0;
        $valor = '';
        $valor2 = 0;
        if (is_numeric($row->sem1)) {
          $c++;
          $valor2 = $valor2 + $row->sem1;
        }
        if (is_numeric($row->sem2)) {
          $c++;
          $valor2 = $valor2 + $row->sem2;
        }
        if ($c == 0) {
          $c = 1;
        }
        $valor = $valor2 / $c;

        if ($row->sem1 != '' || $row->sem2 != '') {
          if (!is_numeric($row->sem1) && !is_numeric($row->sem2)) {
            return $row->sem2;
          } else {
            $promedio[$grado] += $valor;
            $cant[$grado]++;
            $creditos[$grado] += $row->credito;
            return [$row->sem1,$row->sem2,$row->credito];
          }
        }else{
          return  '';
        }
      }
    }
  }
  $cursos = [
    'EDUC. CRIST.' => ['XNA'],
    'ESPAÑOL' => ['ESP'],
    'INGLÉS' => ['ING'],
    'ALGEBRA' => ['MAT','GEO','ALG'],
    'CIENCIA' => ['CIE'],
    'HISTORIA' => ['HIS','SOC'],
    'EDUCACIÓN FÍSICA' => ['EDF'],
    '*SALUD' => ['SLD'],
    '*TECNOLOGÍA' => ['COM'],
    '*PATERNIDAD RESP.' => ['PTR'],
    '*ARTE' => ['ART'],
    '*TEATRO' => ['TEA'],
    '*VIDA EN FAMILIA' => ['VFM'],
  ];
  #1
foreach ($students as $estu) {
$promedio = [];
$promedio['09']=0;
$promedio['10']=0;
$promedio['11']=0;
$promedio['12']=0;
$promedioLetters = [];
$promedioLetters['09']=0;
$promedioLetters['10']=0;
$promedioLetters['11']=0;
$promedioLetters['12']=0;
$cant = [];
$cant['09']=0;
$cant['10']=0;
$cant['11']=0;
$cant['12']=0;
$creditos = [];
$creditos['09']=0;
$creditos['10']=0;
$creditos['11']=0;
$creditos['12']=0;


    $pdf->AddPage('L');
    $pdf->SetMargins(8,8);

    $info1 = DB::table('year')->select("id, ss, dir1, grado, fecha")
        ->whereRaw("ss = '$estu->ss'")->orderBy('apellidos')->first();


   $fgra = $estu->fechagra ?? '';
   if ($fgra == '0000-00-0000'){$fgra='';}

    $info2 = DB::table('madre')->select("encargado")
        ->whereRaw("id = '$estu->id'")->first();

  $pdf->Ln(-3);
  $pdf->Cell(0, 5, utf8_encode('TRANSCRIPCIÓN DE CREDITOS'), 0, 1, 'C');
  $pdf->Cell(0, 5, utf8_encode('NIVEL SUPERIOR'), 0, 1, 'C');
  $pdf->Ln(5);

  $pdf->Cell(20, 5, 'Nombre:');
  $pdf->Cell(100, 5, "$estu->apellidos $estu->nombre", 'B');
  
  $pdf->Cell(0, 5, "Seguro Social: XXX-XX-" . substr($estu->ss, -4), 0, 1, 'L');
  $pdf->Ln(2);
//  $conducta  = [];
  $unidades = 0;
  $grados = DB::table('acumulativa')->select("DISTINCT grado")
        ->whereRaw("ss = '$estu->ss' and (grado not like '12%' and grado not like '11%' and grado not like '10%' and grado not like '09%')")->get();



  $GRADOS = ['09', '10', '11','12'];
  $WIDTH = 60;
  $pdf->Cell(40, 6, utf8_encode('AÑO ESCOLAR'), 1, 0, 'L', true);
  foreach ($GRADOS as $i => $GRA ) {
    $pdf->Cell($WIDTH, 6, Year($GRA, $estu->ss), 1, ($i == count($GRADOS) - 1) ? 1 : 0, 'C', true);
  }
  #2
  $pdf->SetFont('Arial', '', 10);
  $pdf->Cell(40, 6, '', 1, 0, 'L', true);
  $pdf->SetFont('Arial', 'B', 11);
  $pdf->Cell($WIDTH, 6, 'Noveno Grado', 1, 0, 'C', true);
  $pdf->Cell($WIDTH, 6, utf8_encode('Décimo Grado'), 1, 0, 'C', true);
  $pdf->Cell($WIDTH, 6, utf8_encode('Undécimo Grado'), 1, 0, 'C', true);
  $pdf->Cell($WIDTH, 6, utf8_encode('Duodécimo Grado'), 1, 1, 'C', true);
  $pdf->Cell(40, 5, 'Asignaturas', 1, 0, 'L', true);
  $pdf->SetFont('Times', '', 9.5);
  $pdf->Cell($WIDTH / 3, 5, 'I Semestre', 1, 0, 'C', true);
  $pdf->Cell($WIDTH / 3, 5, 'II Semestre', 1, 0, 'C', true);
  $pdf->Cell($WIDTH / 3, 5, 'Unidades', 1, 0, 'C', true);
  $pdf->Cell($WIDTH / 3, 5, 'I Semestre', 1, 0, 'C', true);
  $pdf->Cell($WIDTH / 3, 5, 'II Semestre', 1, 0, 'C', true);
  $pdf->Cell($WIDTH / 3, 5, 'Unidades', 1, 0, 'C', true);
  $pdf->Cell($WIDTH / 3, 5, 'I Semestre', 1, 0, 'C', true);
  $pdf->Cell($WIDTH / 3, 5, 'II Semestre', 1, 0, 'C', true);
  $pdf->Cell($WIDTH / 3, 5, 'Unidades', 1, 0, 'C', true);
  $pdf->Cell($WIDTH / 3, 5, 'I Semestre', 1, 0, 'C', true);
  $pdf->Cell($WIDTH / 3, 5, 'II Semestre', 1, 0, 'C', true);
  $pdf->Cell($WIDTH / 3, 5, 'Unidades', 1, 1, 'C', true);
  $pdf->SetFont('Arial', '', 10);

  foreach ($cursos as $nombre => $curso) {
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 5, utf8_encode($nombre), 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    foreach ($GRADOS as $i => $GRA ) {
      $grade = Curso($GRA, $curso, $estu->ss);
      if($grade !== NULL && $grade !== ''){
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell($WIDTH / 3, 5, $grade[0], 1, 0, 'C');
        $pdf->Cell($WIDTH / 3, 5, $grade[1], 1, 0, 'C');
        $pdf->Cell($WIDTH / 3, 5, $grade[2], 1, ($i == count($GRADOS) - 1) ? 1 : 0, 'C');
      }else{
        $pdf->SetFont('Arial', '', 20);
        $pdf->Cell($WIDTH / 3, 5, '-', 1, 0, 'C');
        $pdf->Cell($WIDTH / 3, 5, '-', 1, 0, 'C');
        $pdf->Cell($WIDTH / 3, 5, '-', 1, ($i == count($GRADOS) - 1) ? 1 : 0, 'C');
      }
    }
  }
  
  $pdf->SetFont('Arial', '', 10);
  $pdf->Cell(40, 6, 'PROMEDIO G.A', 1, 0, 'L', true);
  $pdf->SetFont('Arial', '', 11);
  $gpaProm = 0;
  $gpaCant = 0;
  
  foreach ($GRADOS as $i => $GRA ) {


    $prom = ($promedio[$GRA] == 0) ? 0 : round(($promedio[$GRA] / $cant[$GRA]),2);
    if ($prom > 0)
       {
       $gpaProm = $gpaProm + $prom;
       $gpaCant = $gpaCant + 1;
       }

   
    $pdf->Cell(($WIDTH / 3) * 2 , 6, $prom == 0 ? '' : number_format($prom,2) , 1, 0, 'C');
    $pdf->Cell($WIDTH / 3, 6, $creditos[$GRA], 1, ($i == count($GRADOS) - 1) ? 1 : 0, 'C');
  }

  // echo '<pre>';
  // var_dump($cant);
  // var_dump($promedio);
  // echo '</pre>';

  $gpa = $gpaCant > 0 ? number_format($gpaProm / $gpaCant, 2) : '';

  $pdf->Cell(0,5,utf8_encode("*Electiva"),0,1);
  $pdf->Ln(3);
  $Y2 = $pdf->GetY();
  $pdf->Cell(30, 5, "GPA: $gpa", 0, 0);
  $pdf->Cell(50, 5, utf8_encode("Fecha de Graduación: $fgra"), 0, 1);
 
  if ($CEP) {
    $pdf->Ln(3);
    $pdf->Cell(80, 5, utf8_encode("Número de unidades aprobados: $unidades"));
    $pdf->Cell(60, 5, utf8_encode("Unidades en curso"), 0, 1);
  }
  \setlocale(LC_ALL, 'es_ES');
  $newDate = strftime('%B %d, %Y', strtotime(date("Y-m-d")));  
  $pdf->MultiCell(120, 5, utf8_encode("Certifico que la información ofrecida en este documento es correcta según nuestro expediente académico."), 0, 1);
  $pdf->Cell(0, 5, "Observaciones:", 0, 1);
  $pdf->Ln(3);
  $pdf->Cell(0, 5, "Expedido hoy ".$newDate." en Gurabo, Puerto Rico", 0, 1);
  $pdf->Ln(10);
  $Y = $pdf->GetY();
  $pdf->Cell(50, 5, '', 'B');
  $pdf->Cell(30);
  $pdf->Cell(50, 5, '', 'B');
  $pdf->Cell(30);
  $pdf->Cell(50, 5, 'Sello', 0, 1);
  $pdf->Cell(50, 5, 'Firma Directora Interina', 0, 0, 'C');
  $pdf->Cell(30);
  $pdf->Cell(50, 5, 'Oficial de registro', 0, 0, 'C');

  if ($cofi=='Si')
     {
     $pdf->Image('../../../logo/firma_acumalativa_31_1.png', 15, $Y - 6, 45);
     $pdf->Image('../../../logo/firma_acumalativa_31_2.png', 100, $Y - 4, 30);
     }

  $pdf->SetXY(130,$Y2-5);
  $pdf->SetMargins(130,$Y2-5);
  $grados = [
    '9no' => ['Algebra II', 'Ciencias Terrestres', 'Historia Mundo'],
    '10mo' => ['Geometria', 'Ciencias Biología', 'Historia P.R.'],
    '11mo' => ['Pre Cálculo', 'Química', 'Historia E.U.'],
    '12mo' => ['Calculo', 'Ciencias Fisicas', 'Sociología / Ciencia Social']
  ];
  $pdf->Cell(0, 5, 'Materias/Grado', 0, 1);

  $pdf->Cell(15, 5, "Grado", 1);
  $pdf->Cell(50, 5, utf8_encode("Algébra"), 1);
  $pdf->Cell(50, 5, "Ciencia", 1);
  $pdf->Cell(50, 5, "Historia", 1, 1);
  $pdf->SetFont('Times', '', 11);

  foreach ($grados as $grado => $materias) {
    $pdf->Cell(15, 5, $grado, 1);
    foreach ($materias as $materia) {
      $pdf->Cell(50, 5, utf8_encode($materia), 1);
    }
    $pdf->Ln();
  }
  $pdf->SetFont('Arial', '', 11);

}
$pdf->Output();
