<?php
// COLEGIO ANGELES CUSTODIO

//if ($grado = $_POST['gra']=='KG-1')
//   {
//   include('tarjetadenotas13a.php');
//   exit;
//   }

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
    ['Reporte de Notas', 'Grade Report'],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["Año escolar:", "School year:"],
    ["DESCRIPCION", "DESCRIPTION"],
    ['PRIMER SEMESTRE', 'FIRST SEMESTER'],
    ['SEGUNDO SEMESTRE', 'SECOND SEMESTER'],
    ['PRO', 'AVE'],
    ['PROMEDIO:', 'AVERAGE:'],
    ['Nombre:', 'Name:'],
    ['Total de estudiantes', 'Total students'],
    ['Fecha:', 'Date:'],
    ['Documentos sin entregar', 'Undelivered documents'],
    ['Masculinos', 'Males'],
    ['AÑO', 'YEAR'],
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
    } else  if ($valor <= '59' && $valor >= '1') {
        return 'F';
    } else  if ($valor == '') {
        return '';
    }
}

class nPDF extends PDF
{
    function Header()
    {
    global $year;
    parent::header();
	$this->Ln(5);
	$this->Cell(80);
	$this->SetFont('Arial','B',12);

    global $nom;
    global $ss;
    global $msh;

    global $gen;
    global $fec;

    global $edad;
    global $ndr;
    global $grade;

    list($gr, $se) = explode("-",$grade);
	$this->Ln(-20);
	$this->Cell(235,3,'',0,0,'R');
	$this->Cell(20,7,utf8_encode('AÑO  ').$year,0,1,'R');
	$this->Cell(215,7,'',0,0,'R');
	$this->Cell(40,7,'CURVA OFICIAL',1,0,'C');
	$this->Cell(20,7,'NOTAS',1,1,'C');
	$this->Cell(185,7,'',0,0,'R');
	$this->Cell(30,7,'ALTO HONOR',1,0,'R');
	$this->Cell(20,7,'100.00-97',1,0,'R');
	$this->Cell(20,7,'4.00-3.85',1,0,'R');
	$this->Cell(20,7,'A',1,1,'C');
	$this->Cell(185,7,'',0,0,'R');
	$this->Cell(30,7,'HONOR',1,0,'R');
	$this->Cell(20,7,'96.99-90',1,0,'R');
	$this->Cell(20,7,'3.86-3.50',1,0,'R');
	$this->Cell(20,7,'A',1,1,'C');

	$this->SetFont('Arial','B',12);
	$this->Cell(30,7,'NUM. DE REGISTRO',0,0,'C');
	$this->Cell(20,7,'',0,0,'R');
	$this->Cell(70,7,'ESTUDIANTE',0,0,'C');
	$this->Cell(20,7,'',0,0,'R');
	$this->Cell(20,7,'SEG. SOC.',0,0,'C');
	$this->SetFont('Arial','',10);
	$this->Cell(55,7,'',0,0,'R');
	$this->Cell(20,7,'89.99-80',1,0,'R');
	$this->Cell(20,7,'3.49-3.00',1,0,'R');
	$this->Cell(20,7,'B',1,1,'C');

	$this->Cell(30,7,$ndr,0,0,'C');
	$this->Cell(20,7,'',0,0,'R');
	$this->Cell(70,7,$nom,0,0,'C');
	$this->Cell(20,7,'',0,0,'C');
	$this->Cell(20,7,$ss,0,0,'C');

	$this->SetFont('Arial','',10);
	$this->Cell(55,7,'',0,0,'R');
	$this->Cell(20,7,'79.99-70',1,0,'R');
	$this->Cell(20,7,'2.49-1.60',1,0,'R');
	$this->Cell(20,7,'C',1,1,'C');

	$this->SetFont('Arial','B',12);
	$this->Cell(15,7,'Sexo: ',0,0,'L');
	$this->SetFont('Arial','',10);
	$this->Cell(10,7,$gen,0,0,'L');

	$this->SetFont('Arial','B',12);
	$this->Cell(50,7,'Fecha de Nacimiento: ',0,0,'L');
	$this->SetFont('Arial','',10);
	$this->Cell(30,7,$fec,0,0,'L');

	$this->SetFont('Arial','B',12);
	$this->Cell(15,7,'Edad: ',0,0,'L');
	$this->SetFont('Arial','',10);
	$this->Cell(5,7,$edad,0,0,'L');
	$this->SetFont('Arial','B',12);
	$this->Cell(10,7,utf8_encode('Años '),0,0,'L');

	$this->SetFont('Arial','',10);
	$this->Cell(80,7,'',0,0,'R');
	$this->Cell(20,7,'69.99-60',1,0,'R');
	$this->Cell(20,7,'1.59-1.00',1,0,'R');
	$this->Cell(20,7,'D',1,1,'C');

	$this->SetFont('Arial','B',12);
	$this->Cell(18,7,'Grado: ',0,0,'L');
	$this->SetFont('Arial','',10);
	$this->Cell(7,7,$gr,0,0,'L');
	$this->SetFont('Arial','B',12);
	$this->Cell(18,7,'Grupo: ',0,0,'L');
	$this->SetFont('Arial','',10);
	$this->Cell(15,7,$se,0,0,'L');
	$this->SetFont('Arial','B',12);
	$this->Cell(52,7,utf8_encode('Maestro(a) Salón Hogar: '),0,0,'L');
	$this->SetFont('Arial','',10);
	$this->Cell(50,7,$msh,0,0,'L');
	$this->SetFont('Arial','',10);
	$this->Cell(55,7,'',0,0,'R');
	$this->Cell(20,7,'59.99-00',1,0,'R');
	$this->Cell(20,7,'0.59-0.00',1,0,'R');
	$this->Cell(20,7,'F',1,1,'C');
}

function Footer()
{
    $this->SetY(-25);
	$this->SetFont('Arial','B',10);
	$this->Cell(50,5,'_____________________________',0,0,'C');
	$this->Cell(35,5,'',0,0,'C');
	$this->Cell(50,5,'_____________________________',0,1,'C');
	$this->Cell(50,5,'Firma de Director Docente',0,0,'C');
	$this->Cell(35,5,'',0,0,'C');
	$this->Cell(50,5,'Firma Maestro(a)',0,0,'C');
	$this->Cell(45,5,'',0,0,'C');
    $this->Cell(50,5,'Fecha: '.date("m-d-Y"),0,0,'C');
}
}

$school = new School(Session::id());
$teacherClass = new Teacher();
$studentClass = new Student();
$year = $school->info('year2');
$pdf = new nPDF();
$pdf->useFooter(false);
$pdf->SetTitle($lang->translation("Reporte de Notas") . " $year", true);
$pdf->Fill();

$pdf->AliasNbPages();
$grade = $_POST['grade'];
$men = $_POST['mensaje'];
$tri1 = $_POST['tri1'] ?? '';
$tri2 = $_POST['tri2'] ?? '';
$tri3 = $_POST['tri3'] ?? '';
$tri4 = $_POST['tri4'] ?? '';
$sem1 = $_POST['sem1'] ?? '';
$sem2 = $_POST['sem2'] ?? '';
$prof = $_POST['prof'] ?? '';
$crs = $_POST['cr'] ?? '';

$mensaj = DB::table('codigos')->where([
    ['codigo', $men],
])->orderBy('codigo')->first();

$idi = '';


IF($idi=='Ingles'){
  $ye='SCHOOL YEAR:';
  $no='Name: ';
  $gr='Grade: ';
  $de='DESCRIPTION';
  $pr='AVG';
  $va='Assigned Value';
  $fe='Dates';
  $rr=20;
  $text1=$row11[3];
  $text2=$row11[4];
  $fi='YR';
  $f2='AVG';
  $se1='FIRST SEMESTER';
  $se2='SECOND SEMESTER';
  $qq1 ='    Q-1        Q-2          AVER.';
  $qq2 ='    Q-3        Q-4          AVER.';
  $pq='AVERAGE.';
  $asi='ABSENCE AND LATE';
  
}ELSE{
  $ye='A&#65533;O ESCOLAR:';
  $no='Nombre: ';
  $gr='Grado: ';
  $de='DESCRIPCION';
  $pr='PRO';
  $va='Valor Asignado';
  $fe='Fechas';
  $rr=0;
  $text1 = $mensaj->t1e ?? '';
  $text2 = $mensaj->t2e ?? '';
  $fi='PRO';
  $f2='A&#65533;O';
  $se1='PRIMER SEMESTRE';
  $se2='SEGUNDO SEMESTRE';
  $qq1 ='    T-1        T-2          PROM.';
  $qq2 ='    T-3        T-4          PROM.';
  $pq='PROMEDIO';
  $asi='AUSENCIAS Y TARDANZAS';
  }

$colegio = DB::table('colegio')->where([
    ['usuario', 'administrador']
])->orderBy('id')->first();
$teacher = $teacherClass->findByGrade($grade);
$students = $studentClass->findByGrade($grade);
$msh=$teacher->apellidos.' '.$teacher->nombre;
$a=0;
$aa=0;
foreach ($students as $estu) {
  list($ss1, $ss2, $ss3) = explode("-",$estu->ss);
  $nom=$estu->apellidos.' '.$estu->nombre;
  $ss=$ss3;
  $ndr=$estu->nuref;
  if ($estu->genero==1){$gen='F';}
  if ($estu->genero==2){$gen='M';}
  $fec=$estu->fecha;
  list($ano,$mes,$dia) = explode("-",$estu->fecha);
  $ano_diferencia = date("Y") - $ano;
  $mes_diferencia = date("m") - $mes;
  $dia_diferencia = date("d") - $dia;
  if ($dia_diferencia < 0 || $mes_diferencia < 0)
     {$ano_diferencia--;}
     $edad=$ano_diferencia;
     $pdf->AddPage('L');
    $padres = DB::table('madre')->where([
        ['id', $estu->id]
    ])->orderBy('id')->first();
   $a=$a+1;

$gra='';
$pdf->SetFont('Times','',11);
   
$pdf->Cell(65,5,'Asignatura',1,0,'C');
$pdf->Cell(40,5,'Trimestre',1,0,'C');
$pdf->Cell(25,5,'Semestre',1,0,'C');
$pdf->Cell(40,5,'Trimestre',1,0,'C');
$pdf->Cell(25,5,'Semestre',1,0,'C');
$pdf->Cell(20,5,'Prom.',1,0,'C');     
$pdf->Cell(20,5,'Nota',1,0,'C');
$pdf->Cell(20,5,'Total de',1,0,'C');
$pdf->Cell(20,5,'',1,1,'C');

$pdf->Cell(65,5,'',1,0,'C');
$pdf->Cell(12,5,'1',1,0,'C');
$pdf->Cell(8,5,'N',1,0,'C');
$pdf->Cell(12,5,'2',1,0,'C');
$pdf->Cell(8,5,'N',1,0,'C');
$pdf->Cell(15,5,'1',1,0,'C');
$pdf->Cell(10,5,'N',1,0,'C');

$pdf->Cell(12,5,'3',1,0,'C');
$pdf->Cell(8,5,'N',1,0,'C');
$pdf->Cell(12,5,'4',1,0,'C');
$pdf->Cell(8,5,'N',1,0,'C');
$pdf->Cell(15,5,'2',1,0,'C');
$pdf->Cell(10,5,'N',1,0,'C');

$pdf->Cell(20,5,'Clase',1,0,'C');     
$pdf->Cell(20,5,'N',1,0,'C');
$pdf->Cell(20,5,utf8_encode('Créditos'),1,0,'C');
$pdf->Cell(20,5,'',1,1,'C');
     $cursos = DB::table('padres')->where([
        ['year', $year],
        ['ss', $estu->ss],
        ['grado', $grade],
        ['curso', '!=', ''],
        ['curso', 'NOT LIKE', '%AA-%']
    ])->orderBy('orden')->get();

 $notas=0;
 $cr=0; 
 $au=0; 
 $ta=0;

 $notas2=0;
 $cr2=0; 
 $au2=0; 
 $ta2=0;

 $notas3=0;
 $cr3=0; 
 $au3=0; 
 $ta3=0;

 $notas4=0;
 $cr4=0; 
 $au4=0; 
 $ta4=0;
 $notas5=0;
 $cr5=0; 
 $notas6=0;
 $cr6=0; 
 $notas7=0;
 $cr7=0; 
 $cn1='';
 $cn2='';
 $cn3='';
 $cn4='';
 $cn11=0;
 $cn22=0;
 $cn33=0;
 $cn44=0;
 $de1=0;
 $de2=0;
 $de3=0;
 $de4=0;
foreach ($cursos as $curso) {

  $V5 = 0;
  $V6 = 0;
  $tot1t = "";
  $v7 = 0;
  $v8 = 0;
  $tot1t1 = "";
  IF (is_numeric($curso->nota1) OR $curso->nota1 == '0')
     {
     $cr=$cr+1;
     $notas=$notas+$curso->nota1;
     }

  IF (is_numeric($curso->nota2) OR $curso->nota2 == '0')
     {
     $cr2=$cr2+1;
     $notas2=$notas2+$curso->nota2;
     }
     
  IF (is_numeric($curso->nota3) OR $curso->nota3 == '0')
     {
     $cr3=$cr3+1;
     $notas3=$notas3+$curso->nota3;
     }

  IF (is_numeric($curso->nota4) OR $curso->nota4 == '0')
     {
     $cr4=$cr4+1;
     $notas4=$notas4+$curso->nota4;
     }
  IF (is_numeric($curso->sem1) > 0)
     {
     }
  IF (is_numeric($curso->sem2) > 0)
     {
     $cr6=$cr6+1;
     $notas6=$notas6+is_numeric($curso->sem2);
     }
  IF (is_numeric($curso->final) > 0)
     {
     $cr7=$cr7+1;
     $notas7=$notas7+$curso->final;
     }

            if ($curso->aus1 > 0) {
                $au = $au + number_format($curso->aus1, 0);
            }
            if ($curso->tar1 > 0) {
                $ta = $ta + $curso->tar1;
            }
            if ($curso->aus2 > 0) {
                $au2 = $au2 + number_format($curso->aus2, 0);
            }
            if ($curso->tar2 > 0) {
                $ta2 = $ta2 + $curso->tar2;
            }
            if ($curso->aus3 > 0) {
                $au3 = $au3 + number_format($curso->aus3, 0);
            }
            if ($curso->tar3 > 0) {
                $ta3 = $ta3 + $curso->tar3;
            }
            if ($curso->aus4 > 0) {
                $au4 = $au4 + number_format($curso->aus4, 0);
            }
            if ($curso->tar4 > 0) {
                $ta4 = $ta4 + $curso->tar4;
            }

  IF (is_numeric($curso->con1))
     {
     $cn1=$cn1+$curso->con1;
     $cn11=$cn11+1;
     }
  IF (is_numeric($curso->con2))
     {
     $cn2=$cn2+$curso->con2;
     $cn22=$cn22+1;
     }
  IF (is_numeric($curso->con3))
     {
     $cn3=$cn3+$curso->con3;
     $cn33=$cn33+1;
     }
  IF (is_numeric($curso->con4))
     {
     $cn4=$cn4+$curso->con4;
     $cn44=$cn44+1;
     }

 if (is_numeric($curso->de1)) 
    {
    $de1=$de1+$curso->de1;
    }
 if (is_numeric($curso->de2)) 
    {
    $de2=$de2+$curso->de2;
    }
 if (is_numeric($curso->de3)) 
    {
    $de3=$de3+$curso->de3;
    }
 if (is_numeric($curso->de4)) 
    {
    $de4=$de4+$curso->de4;
    }
     $pdf->SetFont('Times','',9);
     IF($idi=='Ingles')
       {$pdf->Cell(65,5,$curso->desc2,1,0);}
     ELSE
       {$pdf->Cell(65,5,$curso->descripcion,1,0);}

     $nn1='';
     $nn2='';

     $pdf->Cell(12,5,$curso->nota1,1,0,'C');
     $pdf->Cell(8,5,NLetra($curso->nota1),1,0,'C');
     $pdf->Cell(12,5,$curso->nota2,1,0,'C');
     $pdf->Cell(8,5,NLetra($curso->nota2),1,0,'C');
     $n1=0;
     $n2=0;
     if (is_numeric($curso->nota1))
        {
        $n1=$n1+$curso->nota1;
        $n2=$n2+1;
        }
     if (is_numeric($curso->nota2))
        {
        $n1=$n1+$curso->nota2;
        $n2=$n2+1;
        }
        $not5='';
     IF($sem1=='Si' and $n2 > 0)
       {
       $cr5=$cr5+1;
       $notas5=$notas5+round($n1/$n2,0);
       $not5=round($n1/$n2,0);
       $pdf->Cell(15,5,round($n1/$n2,0),1,0,'C');}
     ELSE
       {
       $pdf->Cell(15,5,'',1,0,'C');}

     $nn3='';

     IF($sem1=='Si' and $n2 > 0)
       {
       $pdf->Cell(10,5,NLetra(round($n1/$n2,0)),1,0,'C');}
     ELSE
       {
       $pdf->Cell(10,5,'',1,0,'C');}
    
     $nn1='';
     $nn2='';
     $pdf->Cell(12,5,$curso->nota3,1,0,'C');
     $pdf->Cell(8,5,NLetra($curso->nota3),1,0,'C');
     $pdf->Cell(12,5,$curso->nota4,1,0,'C');
     $pdf->Cell(8,5,NLetra($curso->nota4),1,0,'C');

     $nos=0;
     $nos2=0;
     $nos3='';
     if (is_numeric($curso->nota3))
        {
        $nos=$nos+$curso->nota3;
        $nos2=$nos2+1;
        }
     if (is_numeric($curso->nota4))
        {
        $nos=$nos+$curso->nota4;
        $nos2=$nos2+1;
        }
     if ($nos2 > 0)
        {
        $nos3=round($nos/$nos2,0);
        }
     IF($sem2=='Si')
       {
       $pdf->Cell(15,5,$nos3,1,0,'C');}
     ELSE
       {
       $pdf->Cell(15,5,'',1,0,'C');}

     $nn4='';
     IF($sem2=='Si')
       {
       $pdf->Cell(10,5,NLetra($nos3),1,0,'C');}
     ELSE
       {
       $pdf->Cell(10,5,'',1,0,'C');}
     $nos4=0;
     $nos5=0;
     $nos3='';
     if (is_numeric($curso->sem1) and $n2 > 0)
        {
        $nos4=$nos4+round($n1/$n2,0);
        $nos5=$nos5+1;
        }
     if (is_numeric($curso->sem2) > 0 and $nos2 > 0)
        {
        $nos4=$nos4+round($nos/$nos2,0);
        $nos5=$nos5+1;
        }
     if ($nos5 > 0)
        {
        $nos3=round($nos4/$nos5,0);
        }
     $pdf->Cell(20,5,$nos3,1,0,'C');
     
     $nn5='';
     $pdf->Cell(20,5,NLetra($nos3),1,0,'C');

     $cr1='';
     IF($crs=='Si'){$cr1=$curso->credito;}
     $pdf->Cell(20,5,$cr1,1,0,'R');
     $pdf->Cell(20,5,'',1,1,'C');
  }

     $pdf->Cell(65,5,'PROMEDIO',1,0,'C');
     $nt=0;
     IF($cr > 0){
     $pdf->Cell(12,5,round($notas/$cr,0),1,0,'C');
     $nt=round($notas/$cr,0);
     }ELSE{$pdf->Cell(12,5,'',1,0,'C');}
     $nn5='';
     $pdf->Cell(8,5,NLetra($nt),1,0,'C');
     
     $nt=0;
     IF($cr2 > 0){
     $pdf->Cell(12,5,round($notas2/$cr2,0),1,0,'C');
     $nt=round($notas2/$cr2,0);
     }ELSE{$pdf->Cell(12,5,'',1,0,'C');}

     $nn5='';
     $pdf->Cell(8,5,NLetra($nt),1,0,'C');
     $nt=0;
     IF($cr5 > 0){
     $pdf->Cell(15,5,round($notas5/$cr5,0),1,0,'C');
     $nt=round($notas5/$cr5,0);
     }ELSE{$pdf->Cell(15,5,'',1,0,'C');}

     $nn5='';
     $pdf->Cell(10,5,NLetra($nt),1,0,'C');
     $nt=0;
     IF($cr3 > 0){
     $pdf->Cell(12,5,round($notas3/$cr3,0),1,0,'C');
     $nt=round($notas3/$cr3,0);
     }ELSE{$pdf->Cell(12,5,'',1,0,'C');}
     $nn5='';
     $pdf->Cell(8,5,NLetra($nt),1,0,'C');
     $nt=0;
     IF($cr4 > 0){
     $pdf->Cell(12,5,round($notas4/$cr4,0),1,0,'C');
     $nt=round($notas4/$cr4,0);
        }ELSE{$pdf->Cell(12,5,'',1,0,'C');}
     $nn5='';
     $pdf->Cell(8,5,NLetra($nt),1,0,'C');
     $nt=0;
     IF($cr6 > 0){
     $pdf->Cell(15,5,round($notas6/$cr6,0),1,0,'C');
     $nt=round($notas6/$cr6,0);
     }ELSE{$pdf->Cell(15,5,'',1,0,'C');}
     $nn6='';
     $pdf->Cell(10,5,NLetra($nt),1,0,'C');
     $nt=0;
     IF($cr7 > 0){
     $pdf->Cell(20,5,round($notas7/$cr7,0),1,0,'C');
     $nt=round($notas7/$cr7,0);
     }ELSE{$pdf->Cell(20,5,'',1,0,'C');}

     $nn7='';
     $pdf->Cell(20,5,NLetra($nt),1,0,'C');
     IF($crs=='Si'){
     $pdf->Cell(20,5,number_format($cr, 2, '.', ''),1,0,'R');
     }ELSE{
     $pdf->Cell(20,5,'',1,0,'R');
     }
     IF($crs=='Si'){
     $pdf->Cell(20,5,number_format($cr, 2, '.', ''),1,1,'R');
     }ELSE{
     $pdf->Cell(20,5,'',1,1,'R');
     }

//IF ($row2[70] =='B'){
IF ($row2 ?? '' =='B11'){
    $dat2 = "select * from asispp where ss = '$row1[0]' AND year='$row2[116]'";
    $tab2 = mysql_query($dat2, $con) or die ("problema con query 7") ;
    $result7=mysql_query($dat2);

    $au = 0;
    $ta = 0;
    $au2 = 0;
    $ta2 = 0;
    $au3 = 0;
    $ta3 = 0;
    $au4 = 0;
    $ta4 = 0;
while ($row7=mysql_fetch_array($result7))
   {

   IF ($row7[10] == 1 AND $row7[3] >= $row2[29] AND $row7[3] <= $row2[30])
      {
      $au = $au +1;
      }
   IF ($row7[10] == 6 AND $row7[3] >= $row2[29] AND $row7[3] <= $row2[30])
      {
      $au = $au +1;
      }
   IF ($row7[10] > 1 AND $row7[10] < 6 AND $row7[3] >= $row2[29] AND $row7[3] <= $row2[30])
      {
      $ta = $ta +1;
      }
   IF ($row7[10] == 1 AND $row7[3] >= $row2[31] AND $row7[3] <= $row2[32] AND $cr4 > 0 AND $_POST[sem2]=='Si')
      {
      $au2 = $au2 +1;
      }
   IF ($row7[10] == 6 AND $row7[3] >= $row2[31] AND $row7[3] <= $row2[32] AND $cr4 > 0 AND $_POST[sem2]=='Si')
      {
      $au2 = $au2 +1;
      }
   IF ($row7[10] > 1  AND $row7[10] < 6 AND $row7[3] >= $row2[31] AND $row7[3] <= $row2[32] AND $cr4 > 0 AND $_POST[sem2]=='Si')
      {
      $ta2 = $ta2 +1;
      }

   IF ($row7[10] == 1 AND $row7[3] >= $row2[33] AND $row7[3] <= $row2[34])
      {
      $au3 = $au3 +1;
      }
   IF ($row7[10] == 6 AND $row7[3] >= $row2[33] AND $row7[3] <= $row2[34])
      {
      $au3 = $au3 +1;
      }
   IF ($row7[10] > 1 AND $row7[10] < 6 AND $row7[3] >= $row2[33] AND $row7[3] <= $row2[34])
      {
      $ta3 = $ta3 +1;
      }

   IF ($row7[10] == 1 AND $row7[3] >= $row2[33] AND $row7[3] <= $row2[36] AND $cr4 > 0 AND $_POST[sem2]=='Si')
      {
      $au4 = $au4 +1;
      }
   IF ($row7[10] == 6 AND $row7[3] >= $row2[33] AND $row7[3] <= $row2[36] AND $cr4 > 0 AND $_POST[sem2]=='Si')
      {
      $au4 = $au4 +1;
      }
   IF ($row7[10] > 1  AND $row7[10] < 6 AND $row7[3] >= $row2[33] AND $row7[3] <= $row2[36] AND $cr4 > 0 AND $_POST[sem2]=='Si')
      {
      $ta4 = $ta4 +1;
      }
  
  }
     }     

     $pdf->Cell(65,5,'Ausencias',1,0,'L');
     $pdf->Cell(12,5,$au,1,0,'C');
     $pdf->Cell(8,5,'',1,0,'C');
     $pdf->Cell(12,5,$au2,1,0,'C');
     $pdf->Cell(8,5,'',1,0,'C');
     $pdf->Cell(15,5,'',1,0,'C');
     $pdf->Cell(10,5,'',1,0,'C');
    
     $pdf->Cell(12,5,$au3,1,0,'C');
     $pdf->Cell(8,5,'',1,0,'C');
     $pdf->Cell(12,5,$au4,1,0,'C');
     $pdf->Cell(8,5,'',1,0,'C');
     $pdf->Cell(15,5,'',1,0,'R');
     $pdf->Cell(10,5,'',1,0,'C');
     $pdf->Cell(20,5,'',1,0,'C');
     $pdf->Cell(20,5,'',1,0,'C');
     $pdf->Cell(20,5,'Ausencias',1,0,'L');
     $pdf->Cell(20,5,$au+$au2+$au3+$au4,1,1,'R');
  
     $pdf->Cell(65,5,'Tardanzas',1,0,'L');
     $pdf->Cell(12,5,$ta,1,0,'C');
     $pdf->Cell(8,5,'',1,0,'C');
     $pdf->Cell(12,5,$ta2,1,0,'C');
     $pdf->Cell(8,5,'',1,0,'C');
     $pdf->Cell(15,5,'',1,0,'C');
     $pdf->Cell(10,5,'',1,0,'C');
    
     $pdf->Cell(12,5,$ta3,1,0,'C');
     $pdf->Cell(8,5,'',1,0,'C');
     $pdf->Cell(12,5,$ta4,1,0,'C');
     $pdf->Cell(8,5,'',1,0,'C');
     $pdf->Cell(15,5,'',1,0,'R');
     $pdf->Cell(10,5,'1SEM',1,0,'C');
     $nn5='';
     if ($cr5 > 0)
        {
        $pdf->Cell(20,5,round($notas5/$cr5,0),1,0,'C');
        $nt=round($notas5/$cr5,0);
        $pdf->Cell(20,5,NLetra($nt),1,0,'C');
        }
     else
        {
        $pdf->Cell(20,5,'',1,0,'C');
        $pdf->Cell(20,5,'',1,0,'C');
        }

     $pdf->Cell(20,5,'Tardanzas',1,0,'L');
     $pdf->Cell(20,5,$ta+$ta2+$ta3+$ta4,1,1,'R');

     $pdf->Cell(65,5,utf8_encode('Deméritos'),1,0,'L');
     $pdf->Cell(12,5,$de1,1,0,'C');
     $pdf->Cell(8,5,'',1,0,'C');
     $pdf->Cell(12,5,$de2,1,0,'C');
     $pdf->Cell(8,5,'',1,0,'C');
     $pdf->Cell(15,5,'',1,0,'C');
     $pdf->Cell(10,5,'',1,0,'C');
    
     $pdf->Cell(12,5,$de3,1,0,'C');
     $pdf->Cell(8,5,'',1,0,'C');
     $pdf->Cell(12,5,$de4,1,0,'C');
     $pdf->Cell(8,5,'',1,0,'C');
     $pdf->Cell(15,5,'',1,0,'R');
     $pdf->Cell(10,5,'Prom.',1,0,'L');
     $pdf->Cell(20,5,'Anual',1,0,'C');
     $pdf->Cell(20,5,'Nota',1,0,'C');
     $pdf->Cell(20,5,utf8_encode('Deméritos'),1,0,'L');
     $pdf->Cell(20,5,$de1+$de2+$de3+$de4,1,1,'R');

     $pdf->Cell(65,5,'Suspenciones',1,0,'L');
     $pdf->Cell(12,5,'',1,0,'C');
     $pdf->Cell(8,5,'',1,0,'C');
     $pdf->Cell(12,5,'',1,0,'C');
     $pdf->Cell(8,5,'',1,0,'C');
     $pdf->Cell(15,5,'',1,0,'C');
     $pdf->Cell(10,5,'',1,0,'C');
    
     $pdf->Cell(12,5,'',1,0,'C');
     $pdf->Cell(8,5,'',1,0,'C');
     $pdf->Cell(12,5,'',1,0,'C');
     $pdf->Cell(8,5,'',1,0,'C');
     $pdf->Cell(15,5,'',1,0,'R');
     $pdf->Cell(10,5,'',1,0,'C');

     IF($cr7 > 0){
     $pdf->Cell(20,5,round($notas7/$cr7,0),1,0,'C');
     }ELSE{$pdf->Cell(20,5,'',1,0,'C');}

     $pdf->Cell(20,5,$nn7,1,0,'C');
     $pdf->Cell(20,5,utf8_encode('Suspención'),1,0,'L');
     $pdf->Cell(20,5,'',1,1,'R');
     $cna='';
     $cs1=0;
     $cs2=0; 
     $cnb1 = 0;
     $cnb2 = 0;
     $cnb='';
     $cnc='';
     $cnd='';

     $pdf->Cell(65,5,'Conducta',1,0,'L');
     IF ($cn1 > 0)
        {
        $cna=round($cn1/$cn11,0);$cs1=1;
        $pdf->Cell(12,5,$cna,1,0,'C');
        $pdf->Cell(8,5,NLetra($cna),1,0,'C');
        }
     else
        {
        $pdf->Cell(12,5,'',1,0,'C');
        $pdf->Cell(8,5,'',1,0,'C');
        }

     $nn5='';
     $nn5='';
     IF ($cn2 > 0){$cnb=round($cn2/$cn22,0);
        $cs1=$cs1+1;
        $cnb1=round(($cna+$cnb)/$cs1,0);
        $pdf->Cell(12,5,$cnb,1,0,'C');
        $pdf->Cell(8,5,NLetra($cnb),1,0,'C');
        $pdf->Cell(15,5,$cnb1,1,0,'C');
        $pdf->Cell(10,5,NLetra($cnb1),1,0,'C');
        }
     else
        {
        $pdf->Cell(12,5,'',1,0,'C');
        $pdf->Cell(8,5,'',1,0,'C');
        $pdf->Cell(15,5,'',1,0,'C');
        $pdf->Cell(10,5,'',1,0,'C');
        }
     IF ($cn3 > 0)
        {
        $cnc=round($cn3/$cn33,0);$cs2=1;
        $pdf->Cell(12,5,$cnc,1,0,'C');
        $pdf->Cell(8,5,NLetra($cnc),1,0,'C');
        }
     else
        {
        $pdf->Cell(12,5,'',1,0,'C');
        $pdf->Cell(8,5,'',1,0,'C');
        }
    
     $nn5='';
     IF ($cn4 > 0)
        {
        $cnd=round($cn4/$cn44,0);
        $cs2=$cs2+1;
        $cnb2=round(($cnc+$cnd)/$cs2,0);
        $pdf->Cell(12,5,$cnd,1,0,'C');
        $pdf->Cell(8,5,NLetra($cnd),1,0,'C');
        $pdf->Cell(15,5,$cnb2,1,0,'C');
        $pdf->Cell(10,5,NLetra($cnb2),1,0,'C');
        }
     else
        {
        $pdf->Cell(12,5,'',1,0,'C');
        $pdf->Cell(8,5,'',1,0,'C');
        $pdf->Cell(15,5,'',1,0,'C');
        $pdf->Cell(10,5,'',1,0,'C');
        }

     $nn5='';
     $nn5='';
     $cnf='';
     $cs3='';
     $cnb3='';
     IF ($cnb1 > 0 and $cnb2 > 0){$cnf=$cnf+$cnb1;$cs3=1;}
     $cnd='';
     IF ($cnb2 > 0){$cnf=$cnf+$cnb2;
        $cs3=$cs3+1;
        $cnb3=round(($cnf)/$cs3,0);
        $pdf->Cell(20,5,$cnb3,1,0,'C');
        $pdf->Cell(20,5,NLetra($cnb3),1,0,'C');
        $pdf->Cell(20,5,'Conducta',1,0,'L');
        $pdf->Cell(20,5,$cnb3,1,1,'R');
        }
     else
        {
        $pdf->Cell(20,5,'',1,0,'C');
        $pdf->Cell(20,5,'',1,0,'C');
        $pdf->Cell(20,5,'Conducta',1,0,'L');
        $pdf->Cell(20,5,'',1,1,'R');
     }
     $nn5='';
  $pdf->Cell(170,5,'',0,0,'R');
  $pdf->Cell(45,5,utf8_encode('Distinción'),1,0,'L');
  $pdf->Cell(20,5,'',1,0,'R');
  $pdf->Cell(20,5,'Traslado',1,0,'C');
  $pdf->Cell(20,5,'',1,0,'R');
}

$pdf->Output();
?>