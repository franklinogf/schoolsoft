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

function NLetra1($valor){
    global $colegio;
    if ($valor == '') {
        return '';
    } else if ($valor <= '200' && $valor >= $colegio->vala) {
        return 'A';
    } else if ($valor < $colegio->vala && $valor >= $colegio->valb) {
        return 'B';
    } else if ($valor < $colegio->valb && $valor >= $colegio->valc) {
        return 'C';
    } else if ($valor < $colegio->valc && $valor >= $colegio->vald) {
        return 'D';
    } else  if ($valor < $colegio->vald && $valor >= $colegio->valf) {
        return 'F';
  } else  if ($valor == '') {
    return '';
  }
}

class nPDF extends PDF
{
  function Header()
  {
    parent::header();
	$this->Image('../../../../logo/logo3.gif',48,80,120);
	$this->Ln(5);
	$this->Cell(80);
	$this->SetFont('Arial','B',12);
    $this->Cell(30,3,'INFORME DE NOTAS',0,1,'C');
    $this->Ln(10);
}

function Footer()
{
}
}

//Creacin del objeto de la clase heredada
$school = new School(Session::id());
$teacherClass = new Teacher();
$studentClass = new Student();
$year = $school->info('year2');
$pdf = new nPDF();
$pdf->useFooter(false);
$pdf->SetTitle($lang->translation("Reporte de Notas") . " $year", true);
$pdf->Fill();

$grade = $_POST['grade'];
$men = $_POST['mensaje'];
$tri1 = $_POST['tri1'] ?? '';
$tri2 = $_POST['tri2'] ?? '';
$tri3 = $_POST['tri3'] ?? '';
$tri4 = $_POST['tri4'] ?? '';
$sem1 = $_POST['sem1'] ?? '';
$sem2 = $_POST['sem2'] ?? '';
$prof = $_POST['prof'] ?? '';
$tri = $_POST['tri'] ?? 0;
$ccr = $_POST['cr'] ?? '';
$fir = $_POST['fir'] ?? '';

$colegio = DB::table('colegio')->where([
    ['usuario', 'administrador']
])->orderBy('id')->first();

$pdf->AliasNbPages();
$mensaj = DB::table('codigos')->where([
       ['codigo', $men],
       ])->orderBy('codigo')->first();

$teacher = $teacherClass->findByGrade($grade);
$students = $studentClass->findByGrade($grade);

$idi='';
IF($idi=='Ingles'){
  $ye='SCHOOL YEAR:';
  $no='Name: ';
  $gr='Grade: ';
  $de='DESCRIPTION';
  $pr='AVG';
  $va='Assigned Value';
  $fe='Dates';
  $rr=20;
  $text1 = $mensaj->t1i ?? '';
  $text2 = $mensaj->t2i ?? '';
  $fi='YR';
  $f2='AVG';
  $se1='FIRST SEMESTER';
  $se2='SECOND SEMESTER';
  $qq1 ='   Q1     CO       Q2     CO';
  $qq2 ='   Q3     CO       Q4     CO';
  $pq='AVERAGE.';
  $asi='ABSENCE AND LATE';
}ELSE{
  $ye='AÑO ESCOLAR:';
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
  $f2='AÑO';
  $se1='PRIMER SEMESTRE';
  $se2='SEGUNDO SEMESTRE';
  $qq1 =' 1T     C1   2T    C2   S-1';
  $qq2 =' 3T     C3   4T    C4   S-2';
  $pq='PROMEDIO';
  $asi='AUSENCIAS Y TARDANZAS';
  }

$a=0;
foreach ($students as $estu) {
     $pdf->AddPage();
     $a=$a+1;
     $gra='';
     $pdf->SetFont('Times','',11);
     $pdf->Cell(10,5,'',0,1,'L');
     $pdf->Cell(191,10,' ',1,1,'C',true);
     $pdf->SetFont('Times','B',12);
     $pdf->SetFont('Times','',11);
     list($ss1, $ss2, $ss3) = explode("-",$estu->ss);
     $pdf->Cell(20,-5,' Curso ',1,0,'C');
     $pdf->Cell(60,-5,utf8_encode(' Descripción'),1,0,'L');
     $pdf->Cell(28,-5,'Sem 1',1,0,'C');
     $pdf->Cell(28,-5,'Sem 2',1,0,'C');
     $pdf->Cell(28,-5,'Final',1,0,'C');
     $pdf->Cell(27,-5,utf8_encode('Créditos'),1,1,'C');

     $pdf->Cell(40,-5,'Num. Inden '.$ss3,0,0,'L');
     $pdf->Cell(60,-5,utf8_encode('Salón Hogar ').$estu->grado,0,0,'C');
     $pdf->Cell(40,-5,'Fecha '.date("m-d-Y"),0,0,'R');
     $pdf->Cell(60,-5,utf8_encode('Año Academico  ').$year,0,1,'C');
     $pdf->Cell(1,5,' ',0,0,'C');
     $pdf->Cell(31,5,' ',0,1,'C');
     $pdf->Cell(31,5,' ',0,1,'C');
     $pdf->Cell(31,5,' ',0,1,'C');
     $cursos = DB::table('padres')->where([
          ['year', $year],
          ['ss', $estu->ss],
          ['grado', $grade],
          ['curso', '!=', ''],
          ['curso', 'NOT LIKE', '%SER%']
        ])->orderBy('curso')->get();
 
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

foreach ($cursos as $curso) {
    $curso2 = DB::table('padres4')->where([
          ['curso', $curso->curso],
          ['year', $year],
          ['ss', $estu->ss],
          ['grado', $grade],
          ['curso', '!=', ''],
          ['curso', 'NOT LIKE', '%SER%']
        ])->orderBy('curso')->first();

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
     $pdf->SetFont('Times','',9);
     $pdf->Cell(20,5,$curso->curso,1,0,'C');
     IF($idi=='Ingles')
       {$pdf->Cell(60,5,$curso->descripcion,1,0);}
     ELSE
       {$pdf->Cell(60,5,$curso->descripcion,1,0);}
     $not1='';
     $not2=0;
     $not3=0;
     $nd1=0;
     list($not,$n) = explode("-",$curso->curso.'-M');
     if ($not == 'D')
        {
        if (empty($curso->nota1) OR $curso->nota1=='A'){$not2=$not2+4;$not3=$not3+1;}
        if ($curso->nota1=='B'){$not2=$not2+3;$not3=$not3+1;}
        if ($curso->nota1=='C'){$not2=$not2+2;$not3=$not3+1;}
        if ($curso->nota1=='D'){$not2=$not2+1;$not3=$not3+1;}
        if ($curso->nota1=='F'){$not2=$not2+0;$not3=$not3+1;}

        if (empty($curso->nota2) OR $curso->nota2=='A'){$not2=$not2+4;$not3=$not3+1;}
        if ($curso->nota2=='B'){$not2=$not2+3;$not3=$not3+1;}
        if ($curso->nota2=='C'){$not2=$not2+2;$not3=$not3+1;}
        if ($curso->nota2=='D'){$not2=$not2+1;$not3=$not3+1;}
        if ($curso->nota2=='F'){$not2=$not2+0;$not3=$not3+1;}

        if (empty($curso->nota3) OR $curso->nota3=='A'){$not2=$not2+4;$not3=$not3+1;}
        if ($curso->nota3=='B'){$not2=$not2+3;$not3=$not3+1;}
        if ($curso->nota3=='C'){$not2=$not2+2;$not3=$not3+1;}
        if ($curso->nota3=='D'){$not2=$not2+1;$not3=$not3+1;}
        if ($curso->nota3=='F'){$not2=$not2+0;$not3=$not3+1;}

        if (empty($curso->nota4) OR $curso->nota4=='A'){$not2=$not2+4;$not3=$not3+1;}
        if ($curso->nota4=='B'){$not2=$not2+3;$not3=$not3+1;}
        if ($curso->nota4=='C'){$not2=$not2+2;$not3=$not3+1;}
        if ($curso->nota4=='D'){$not2=$not2+1;$not3=$not3+1;}
        if ($curso->nota4=='F'){$not2=$not2+0;$not3=$not3+1;}
        $nd1=0;$nd1=0;
        $nd2=0;
        if ($not3 > 0)
           {
           $not1=round($not2/$not3,0);
           if ($not1 > 3.49){$not1='A';$nd1=4; $nd2=$nd2+1;}
              else
              if ($not1 > 2.99){$not1='B';$nd1=3; $nd2=$nd2+1;}
                 else
                 if ($not1 > 1.99){$not1='C';$nd1=2; $nd2=$nd2+1;}
                    else
                    if ($not1 > 0.99){$not1='D';$nd1=1; $nd2=$nd2+1;}
                       else
                       if ($not1 > 0.00){$not1='F';$nd2=$nd2+1;}
           }
        }
     $pdf->Cell(20,5,$curso->sem1.$not1,1,0,'C');
     $nn1='';
     IF ($curso->sem1 > 0 AND $curso->sem1 < 150){
     IF ($curso->sem1 >= $colegio->vala){$nn1='A';$notas5=$notas5+(4*$curso->credito);$cr5=$cr5+$curso->credito;}
     IF ($curso->sem1 >= $colegio->valb AND $curso->sem1 < $colegio->vala){$nn1='B';$notas5=$notas5+(3*$curso->credito);$cr5=$cr5+$curso->credito;}
     IF ($curso->sem1 >= $colegio->valc AND $curso->sem1 < $colegio->valb){$nn1='C';$notas5=$notas5+(2*$curso->credito);$cr5=$cr5+$curso->credito;}
     IF ($curso->sem1 >= $colegio->vald AND $curso->sem1 < $colegio->valc){$nn1='D';$notas5=$notas5+(1*$curso->credito);$cr5=$cr5+$curso->credito;}
     IF ($curso->sem1 >= $colegio->valf AND $curso->sem1 < $colegio->vald){$nn1='F';$cr5=$cr5+$curso->credito;}
     IF ($curso->sem1 == '0'){$nn1='F';}
     IF ($curso->sem1 == 'P' OR $curso->sem1 == 'p'){$nn1='';}}
     $pdf->Cell(8,5,$nn1,1,0,'C');

     $not1='';
     $not2=0;
     $not3=0;
     list($not,$n) = explode("-",$curso->curso.'-M');
     if ($not == 'D')
        {
        if (empty($curso2->nota1) OR $curso2->nota1=='A'){$not2=$not2+4;$not3=$not3+1;}
        if ($curso2->nota1=='B'){$not2=$not2+3;$not3=$not3+1;}
        if ($curso2->nota1=='C'){$not2=$not2+2;$not3=$not3+1;}
        if ($curso2->nota1=='D'){$not2=$not2+1;$not3=$not3+1;}
        if ($curso2->nota1=='F'){$not2=$not2+0;$not3=$not3+1;}

        if (empty($curso2->nota2) OR $curso2->nota2=='A'){$not2=$not2+4;$not3=$not3+1;}
        if ($curso2->nota2=='B'){$not2=$not2+3;$not3=$not3+1;}
        if ($curso2->nota2=='C'){$not2=$not2+2;$not3=$not3+1;}
        if ($curso2->nota2=='D'){$not2=$not2+1;$not3=$not3+1;}
        if ($curso2->nota2=='F'){$not2=$not2+0;$not3=$not3+1;}

        if (empty($curso2->nota3) OR $curso2->nota3=='A'){$not2=$not2+4;$not3=$not3+1;}
        if ($curso2->nota3=='B'){$not2=$not2+3;$not3=$not3+1;}
        if ($curso2->nota3=='C'){$not2=$not2+2;$not3=$not3+1;}
        if ($curso2->nota3=='D'){$not2=$not2+1;$not3=$not3+1;}
        if ($curso2->nota3=='F'){$not2=$not2+0;$not3=$not3+1;}

        if (empty($curso2->nota4) OR $curso2->nota4=='A'){$not2=$not2+4;$not3=$not3+1;}
        if ($curso2->nota4=='B'){$not2=$not2+3;$not3=$not3+1;}
        if ($curso2->nota4=='C'){$not2=$not2+2;$not3=$not3+1;}
        if ($curso2->nota4=='D'){$not2=$not2+1;$not3=$not3+1;}
        if ($curso2->nota4=='F'){$not2=$not2+0;$not3=$not3+1;}
        if ($not3 > 0)
           {
           $not1=round($not2/$not3,0);
           if ($not1 > 3.49){$not1='A';$nd1=$nd1+4; $nd2=$nd2+1;}
              else
              if ($not1 > 2.99){$not1='B';$nd1=$nd1+3; $nd2=$nd2+1;}
                 else
                 if ($not1 > 1.99){$not1='C';$nd1=$nd1+2; $nd2=$nd2+1;}
                    else
                    if ($not1 > 0.99){$not1='D';$nd1=$nd1+1; $nd2=$nd2+1;}
                       else
                       if ($not1 > 0.00){$not1='F'; $nd2=$nd2+1;}
           }
        }

     $nn2='';
     $pdf->Cell(20,5,$curso2->sem1.$not1,1,0,'C');
     IF ($curso2->sem1 > 0 AND $curso2->sem1 < 150){
     IF ($curso2->sem1 >= $colegio->vala){$nn2='A';$notas6=$notas6+(4*$curso2->credito);$cr6=$cr6+$curso2->credito;}
     IF ($curso2->sem1 >= $colegio->valb AND $curso2->sem1 < $colegio->vala){$nn2='B';$notas6=$notas6+(3*$curso2->credito);$cr6=$cr6+$curso2->credito;}
     IF ($curso2->sem1 >= $colegio->valc AND $curso2->sem1 < $colegio->valb){$nn2='C';$notas6=$notas6+(2*$curso2->credito);$cr6=$cr6+$curso2->credito;}
     IF ($curso2->sem1 >= $colegio->vald AND $curso2->sem1 < $colegio->valc){$nn2='D';$notas6=$notas6+(1*$curso2->credito);$cr6=$cr6+$curso2->credito;}
     IF ($curso2->sem1 >= $colegio->valf AND $curso2->sem1 < $colegio->vald){$nn2='F';$cr6=$cr6+$curso2->credito;}
     IF ($curso2->sem1 == '0'){$nn2='F';}
     IF ($curso2->sem1 == 'P' OR $curso2->sem1 == 'p'){$nn2='';}}
     $pdf->Cell(8,5,$nn2,1,0,'C');
     $not1='';
     list($not,$n) = explode("-",$curso->curso.'-M');
     if ($not == 'D')
        {
        if (empty($curso->nota2))
           {
           $not1='A';
           }
        }
     $nf=0;
     $nfc=0;
     $nnf1='';
     if ($curso->sem1 > 0)
        {
        $nf=$nf+$curso->sem1;
        $nfc=$nfc+1;
        }
     if ($curso2->sem1 > 0)
        {
        $nf=$nf+$curso2->sem1;
        $nfc=$nfc+1;
        }
     if ($prof=='Si' and $nfc > 0)
        {
        $nnf1=round($nf/$nfc,0);
        }
     IF ($nnf1 > 0 AND $nnf1 < 150){
     IF ($nnf1 >= $colegio->vala){$nn2='A';}
     IF ($nnf1 >= $colegio->valb AND $nnf1 < $colegio->vala){$nn2='B';}
     IF ($nnf1 >= $colegio->valc AND $nnf1 < $colegio->valb){$nn2='C';}
     IF ($nnf1 >= $colegio->vald AND $nnf1 < $colegio->valc){$nn2='D';}
     IF ($nnf1 >= $colegio->valf AND $nnf1 < $colegio->vald){$nn2='F';}
     IF ($nnf1 == '0'){$nn2='F';}}
     if ($nd1 > 0 and $not == 'D')
        {
        $nn2='';
        $not1=round($nd1/$nd2,0);
        if ($not1 > 3.49){$nnf1='A';}
           else
           if ($not1 > 2.99){$nnf1='B';}
              else
              if ($not1 > 1.99){$nnf1='C';}
                 else
                 if ($not1 > 0.99){$nnf1='D';}
                    else
                    if ($not1 > 0.00){$nnf1='F';}
        }

     IF($prof=='Si' and $nfc > 0 or $prof=='Si' and $nd1 > 0)
       {
       $pdf->Cell(20,5,$nnf1,1,0,'C');
       $pdf->Cell(8,5,$nn2,1,0,'C');
       }
     else
       {
       $pdf->Cell(20,5,'',1,0,'C');
       $pdf->Cell(8,5,'',1,0,'C');
       }

     IF($ccr=='Si')
       {
       $pdf->Cell(27,5,$curso->credito,1,1,'C');
       }
     else
       {
       $pdf->Cell(27,5,''.$ccr,1,1,'C');
       }
  }
  $pdf->Cell(5,5,'',0,1,'C');
$pg='';
if ($pg=='Si' and $cr5 > 0)
   {
   $cr7=0;
   if ($cr5 > 0){$notas7=round($notas5/$cr5,2);$cr7=$cr7+1;}
   if ($cr6 > 0){$notas7=$notas7+round($notas6/$cr6,2);$cr7=$cr7+1;}
   if ($cr7 > 0){$pg=round($notas7/$cr7,2);}
   $pdf->Cell(80,5,'',0,0,'C');
   $pdf->Cell(28,5,'Promedio Anual',1,0,'C', true);
   $pdf->SetFont('Times','',12);
   $pdf->Cell(28,5,number_format($pg,2),1,1,'C',true);
   $pdf->Cell(28,5,number_format($cr5,2),1,1,'C',true);
   $pdf->Cell(20,5,'',0,1,'C');
   $pdf->SetFont('Times','',11);
   }

$au1=0;
$au2=0;
$au3=0;
$au4=0;
$ta1=0;
$ta2=0;
$ta3=0;
$ta4=0;
    $result7 = DB::table('asispp')->where([
        ['ss', $estu->ss],
        ['year', $year]
    ])->orderBy('fecha')->get();

foreach ($result7 as $row7) {
      if ($row7->fecha >= $colegio->ft1 AND $row7->fecha <= $colegio->ft2)
         {
         if ($row7->codigo == '14' or $row7->codigo == '17')
            {
            //$ta1=$ta1+1; 
            }
         else
            if ($row7->codigo == '1' or $row7->codigo == '11'){
//            $au1=$au1+1;
            }
         }

      if ($row7->fecha >= $colegio->ft3 AND $row7->fecha <= $colegio->ft4)
         {
         if ($row7->codigo == '14' or $row7->codigo == '17')
            {$ta2=$ta2+1;}
         else
            if ($row7->codigo == '1' or $row7->codigo == '11'){$au2=$au2+1;}
         }

      if ($row7->fecha >= $colegio->ft5 AND $row7->fecha <= $colegio->ft6)
         {
         if ($row7->codigo == '14' or $row7->codigo == '17')
            {$ta3=$ta3+1;}
         else
            if ($row7->codigo == '1' or $row7->codigo == '11'){$au3=$au3+1;}
         }

      if ($row7->fecha >= $colegio->ft7 AND $row7->fecha <= $colegio->ft8)
         {
         if ($row7->codigo == '14' or $row7->codigo == '17')
            {$ta4=$ta4+1;}
         else
            if ($row7->codigo == '1' or $row7->codigo == '11'){$au4=$au4+1;}
         }



      }
  $aua=$au1+$au2+$au3+$au4;
  $taa=$ta1+$ta2+$ta3+$ta4;

$au1=0;
$au2=0;
$au3=0;
$au4=0;
$ta1=0;
$ta2=0;
$ta3=0;
$ta4=0;

    $result7 = DB::table('asispp')->where([
        ['ss', $estu->ss],
        ['year', $year]
    ])->orderBy('fecha')->get();

foreach ($result7 as $row7) {
      if ($row7->fecha >= $colegio->ft9 AND $row7->fecha <= $colegio->ft10)
         {
         if ($row7->codigo == '14' or $row7->codigo == '17')
            {
            //$ta1=$ta1+1; 
            }
         else
            if ($row7->codigo == '1' or $row7->codigo == '11'){
//            $au1=$au1+1;
            }
         }

      if ($row7->fecha >= $colegio->ft11 AND $row7->fecha <= $colegio->ft12)
         {
         if ($row7->codigo == '14' or $row7->codigo == '17')
            {$ta2=$ta2+1;}
         else
            if ($row7->codigo == '1' or $row7->codigo == '11'){$au2=$au2+1;}
         }

      if ($row7->fecha >= $colegio->ft13 AND $row7->fecha <= $colegio->ft14)
         {
         if ($row7->codigo == '14' or $row7->codigo == '17')
            {$ta3=$ta3+1;}
         else
            if ($row7->codigo == '1' or $row7->codigo == '11'){$au3=$au3+1;}
         }

      if ($row7->fecha >= $colegio->ft15 AND $row7->fecha <= $colegio->ft16)
         {
         if ($row7->codigo == '14' or $row7->codigo == '17')
            {$ta4=$ta4+1;}
         else
            if ($row7->codigo == '1' or $row7->codigo == '11'){$au4=$au4+1;}
         }
  }

   $aub=$au1+$au2+$au3+$au4;
   $tab=$ta1+$ta2+$ta3+$ta4;

   $au5=$aua+$aub;
   $ta5=$taa+$tab;
   $pdf->Cell(80,5,'Ausencias / Tardanzas: ',1,0,'R',true);
   $pdf->Cell(28,5,$aua.' / '.$taa,1,0,'C',true);
   $pdf->Cell(28,5,$aub.' / '.$tab,1,0,'C',true);
   $pdf->Cell(28,5,$au5.' / '.$ta5,1,1,'C',true);

   $rowa = DB::table('padres')->where([
          ['year', $year],
          ['ss', $estu->ss],
          ['grado', $grade],
          ['curso', '!=', ''],
          ['curso', 'LIKE', '%SERC%']
        ])->orderBy('curso')->first();
   
   $rowb = DB::table('padres4')->where([
          ['year', $year],
          ['ss', $estu->ss],
          ['grado', $grade],
          ['curso', '!=', ''],
          ['curso', 'LIKE', '%SERC%']
        ])->orderBy('curso')->first();

   if (substr($rowa->curso, 0, 4) == 'SERC') 
      {
      $pdf->Cell(80,5,'Servicio Comunitario: ',1,0,'R',true);
      $pdf->Cell(28,5,'  ',1,0,'C',true);
      $pdf->Cell(28,5,$rowb->not31,1,0,'C',true);
      $pdf->Cell(28,5,$rowb->not31,1,1,'C',true);
      }
   $rowa = DB::table('padres')->where([
          ['year', $year],
          ['ss', $estu->ss],
          ['grado', $grade],
          ['curso', '!=', ''],
          ['curso', 'LIKE', '%SERV%']
        ])->orderBy('curso')->first();

   $rowb = DB::table('padres4')->where([
          ['year', $year],
          ['ss', $estu->ss],
          ['grado', $grade],
          ['curso', '!=', ''],
          ['curso', 'LIKE', '%SERV%']
        ])->orderBy('curso')->first();

   if (substr($rowa->curso, 0, 4) == 'SERV') 
      {
      $pdf->Cell(80,5,'Servicio Comunitario Ambiental: ',1,0,'R',true);
      $pdf->Cell(28,5,'  ',1,0,'C',true);
      $pdf->Cell(28,5,$rowb->not31,1,0,'C',true);
      $pdf->Cell(28,5,$rowb->not31,1,1,'C',true);
      }

     $pdf->Cell(10,15,'',0,1,'R');
     $pdf->Cell(74,32,'',1,0,'R',true);
     $pdf->Ln(1);

     $pdf->Cell(35,5,' ',0,0,'L');
     $pdf->Cell(35,5,' ',0,1,'L');
     $pdf->SetFont('Times','B',10);

     $pdf->Cell(50,5,'Notas: E/A=Excelente, B=Bueno, ',0,1,'L');
     $pdf->Cell(50,5,'S/C=Satisfactorio, D=Deficiente,',0,1,'L');
     $pdf->Cell(50,5,'F=No Satisfactorio, NM=Necesita Mejorar',0,1,'L');

     $pdf->Cell(50,5,utf8_encode('Escala de Evaluación:'),0,1,'L');
     $pdf->Cell(50,5,'100-90 A. 89-80 B, 79-70 C, 69-65 D,64-0 F',0,1,'L');
     $pdf->SetFont('Times','',11);

     $pdf->Ln(-26);

     $pdf->Cell(72,-25,'',0,0,'R');
     $pdf->Cell(5,20,'',0,0,'R');
     $pdf->SetFont('Times','B',12);
     $pdf->Cell(116,15,$estu->nombre.' '.$estu->apellidos,1,1,'C',true);
     $pdf->SetFont('Times','',11);
     $pdf->Cell(155,-25,'Nombre del Estudiante',0,0,'R');

     $pdf->Ln(10);
  
  $pdf->Cell(180,5,' Maestro: __________________________________',0,1,'R');
  $pdf->Cell(180,5,'',0,1,'R');
  $pdf->Cell(180,5,'Principal: __________________________________',0,1,'R');
  $pdf->Cell(180,5,'',0,1,'R');
  $pdf->Cell(180,5,'Encargado: __________________________________',0,1,'R');
  $pdf->Ln(10);
  $l1=1;
}

$pdf->Output();
?>