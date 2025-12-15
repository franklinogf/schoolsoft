<?php
if ($_POST['tri'] == 15)
   {
   require_once('TarjetaNotas1d.php');
   exit;
   }

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

function NLetra($valor){
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
     $pdf->AddPage('');
     $a=$a+1;
     $gra='';
     $pdf->SetFont('Times','',11);
     $pdf->Cell(10,5,'',0,1,'L');

     $pdf->SetFont('Times','B',12);
     list($ss1, $ss2, $ss3) = explode("-",$estu->ss);
     $pdf->Cell(40,5,'Num. Inden '.$ss3,'TBL',0,'L',true);
     $pdf->Cell(49,5,utf8_encode('Salón Hogar ').$estu->grado,'TB',0,'C',true);
     $pdf->Cell(40,5,'Fecha '.date("m-d-Y"),'TB',0,'R',true);
     $pdf->Cell(50,5,utf8_encode('Año Academico ').$year,'TBR',1,'C',true);

     $pdf->Cell(16,5,' Curso ',1,0,'C');
     $pdf->Cell(55,5,utf8_encode(' Descripción'),1,0,'L');
     $pdf->Cell(20,5,'Q-1/Dic',1,0,'C');
     $pdf->Cell(20,5,'Q-2/Dic',1,0,'C');
     $pdf->Cell(20,5,'Q-3/Dic',1,0,'C');
//     $pdf->Cell(19,5,'Q-4/Dic',1,0,'C');
     $pdf->Cell(14,5,'70%',1,0,'C');
     $pdf->Cell(14,5,'Final',1,0,'C');
    $v=0;
 if ($tri < 11)
    {
    $pdf->Cell(20,5,'Sem 1',1,1,'C');
    $cursos = DB::table('padres')->where([
          ['year', $year],
          ['ss', $estu->ss],
          ['grado', $grade],
          ['curso', '!=', ''],
          ['curso', 'NOT LIKE', '%SER%']
        ])->orderBy('curso')->get();
    $v=0;
    }
 else
    {
    $pdf->Cell(20,5,'Sem 2',1,1,'C');
    $cursos = DB::table('padres4')->where([
          ['year', $year],
          ['ss', $estu->ss],
          ['grado', $grade],
          ['curso', '!=', ''],
          ['curso', 'NOT LIKE', '%SER%']
        ])->orderBy('curso')->get();
    $v=1;
    }

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
  IF ($curso->sem1 > 0)
     {
     $cr5=$cr5+1;
     $notas5=$notas5+$curso->sem1;
     }

     $pdf->SetFont('Times','',9);
     $pdf->Cell(16,5,$curso->curso,1,0,'C');
     IF($idi=='Ingles')
       {$pdf->Cell(55,5,$curso->descripcion,1,0);}
     ELSE
       {$pdf->Cell(55,5,$curso->descripcion,1,0);}
     $not1='';
     list($not,$n) = explode("-",$curso->curso.'-M');
     if ($not == 'D')
        {
        if (empty($curso->nota1))
           {
           $not1='A';
           }
        else
           {
//           $not1=$row[11];
           }
        }
//     $pdf->Cell(7,5,$row[11].$not1,1,0,'C');
     $nn1='';
     $nn2='';
     $not1='';
     list($not,$n) = explode("-",$curso->curso.'-A');
     if ($not == 'D')
        {
        if (empty($curso->nota2))
           {
           $not1='A';
           }
        else
           {
//           $not1=$row[12];
           }
        }

     if ($tri1 == 'Si')
        {
        $pdf->Cell(8,5,$curso->nota2.$not1,1,0,'C');
        $pdf->Cell(4,5,$curso->dip2,1,0,'C');
        $pdf->Cell(8,5,$curso->con2,1,0,'C');
        }
     else
        {
        $pdf->Cell(8,5,'',1,0,'C');
        $pdf->Cell(4,5,'',1,0,'C');
        $pdf->Cell(8,5,'',1,0,'C');
        }
    
     $nnf1='';

     $nn1='';
     $not1='';
     list($not,$n) = explode("-",$curso->curso.'-A');
     if ($not == 'D')
        {
        if (empty($curso->nota3))
           {
           $not1='A';
           }
        else
           {
//           $not1=$row[13];
           }
        }

     if ($tri2 == 'Si')
        {
        $pdf->Cell(8,5,$curso->nota3.$not1,1,0,'C');
        $pdf->Cell(4,5,$curso->dip3,1,0,'C');
        $pdf->Cell(8,5,$curso->con3,1,0,'C');
        }
     else
        {
        $pdf->Cell(8,5,'',1,0,'C');
        $pdf->Cell(4,5,'',1,0,'C');
        $pdf->Cell(8,5,'',1,0,'C');
        }

     $nn1='';
     $not1='';
     list($not,$n) = explode("-",$curso->curso.'-A');
     if ($not == 'D')
        {
        if (empty($curso->nota4))
           {
           $not1='A';
           }
        else
           {
//           $not1=$row[14];
           }
        }

     if ($tri3 == 'Si')
        {
        $pdf->Cell(8,5,$curso->nota4.$not1,1,0,'C');
        $pdf->Cell(4,5,$curso->dip4,1,0,'C');
        $pdf->Cell(8,5,$curso->con4,1,0,'C');
        }
     else
        {
        $pdf->Cell(8,5,'',1,0,'C');
        $pdf->Cell(4,5,'',1,0,'C');
        $pdf->Cell(8,5,'',1,0,'C');
        }

     $nnf1='';
//     IF ($row[19] == '0'){$nnf1='F';}
//     IF ($row[19] == 'P' OR $row[19] == 'p'){$nnf1='';}}
     IF($sem1=='Si' AND $curso->sem1 > 0 OR $sem1=='Si' AND $curso->nota4 > 0)
       {
       $nnf5=$curso->sem1;
       if ($curso->nota4=='P'){$nnf5=$curso->nota4;$nnf1='';}
       $pdf->Cell(14,5,$curso->nta70,1,0,'C');
       $pdf->Cell(14,5,$curso->nta30,1,0,'C');
       $pdf->Cell(12,5,$nnf5,1,0,'C');
       $pdf->Cell(8,5,NLetra($nnf5),1,1,'C');
       }
     else
       {
       $pdf->Cell(14,5,'',1,0,'C');
       $pdf->Cell(14,5,'',1,0,'C');
       $pdf->Cell(12,5,'',1,0,'C');
       $pdf->Cell(8,5,'',1,1,'C');
       }
  }
  $pdf->Cell(5,5,'',0,1,'C');
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
      if ($row7->fecha >= $colegio->ft1 AND $row7->fecha <= $colegio->ft2 and $v == 0)
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

      if ($row7->fecha >= $colegio->ft3 AND $row7->fecha <= $colegio->ft4 and $v == 0)
         {
         if ($row7->codigo == '14' or $row7->codigo == '17')
            {$ta2=$ta2+1;}
         else
            if ($row7->codigo == '1' or $row7->codigo == '11'){$au2=$au2+1;}
         }

      if ($row7->fecha >= $colegio->ft5 AND $row7->fecha <= $colegio->ft6 and $v == 0)
         {
         if ($row7->codigo == '14' or $row7->codigo == '17')
            {$ta3=$ta3+1;}
         else
            if ($row7->codigo == '1' or $row7->codigo == '11'){$au3=$au3+1;}
         }

      if ($row7->fecha >= $colegio->ft7 AND $row7->fecha <= $colegio->ft8 and $v == 0)
         {
         if ($row7->codigo == '14' or $row7->codigo == '17')
            {$ta4=$ta4+1;}
         else
            if ($row7->codigo == '1' or $row7->codigo == '11'){$au4=$au4+1;}
         }


      if ($row7->fecha >= $colegio->ft9 AND $row7->fecha <= $colegio->ft10 and $v == 1)
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

      if ($row7->fecha >= $colegio->ft11 AND $row7->fecha <= $colegio->ft12 and $v == 1)
         {
         if ($row7->codigo == '14' or $row7->codigo == '17')
            {$ta2=$ta2+1;}
         else
            if ($row7->codigo == '1' or $row7->codigo == '11'){$au2=$au2+1;}
         }

      if ($row7->fecha >= $colegio->ft13 AND $row7->fecha <= $colegio->ft14 and $v == 1)
         {
         if ($row7->codigo == '14' or $row7->codigo == '17')
            {$ta3=$ta3+1;}
         else
            if ($row7->codigo == '1' or $row7->codigo == '11'){$au3=$au3+1;}
         }

      if ($row7->fecha >= $colegio->ft15 AND $row7->fecha <= $colegio->ft16 and $v == 1)
         {
         if ($row7->codigo == '14' or $row7->codigo == '17')
            {$ta4=$ta4+1;}
         else
            if ($row7->codigo == '1' or $row7->codigo == '11'){$au4=$au4+1;}
         }

      }

   $au5=$au1+$au2+$au3+$au4;
   $ta5=$ta1+$ta2+$ta3+$ta4;
   $pdf->Cell(71,5,'Ausencias / Tardanzas: ',1,0,'R',true);
//   $pdf->Cell(20,5,$au1.' / '.$ta1,1,0,'C',true);
   $pdf->Cell(20,5,$au2.' / '.$ta2,1,0,'C',true);
   $pdf->Cell(20,5,$au3.' / '.$ta3,1,0,'C',true);
   $pdf->Cell(20,5,$au4.' / '.$ta4,1,0,'C',true);
   $pdf->Cell(48,5,$au5.' / '.$ta5,1,1,'C',true);

$AP=2;
if ($AP==1)
   {
     $pdf->Cell(75,5,'Promedios: ',1,0,'R',true);
     IF ($cr > 0){
        $pdf->Cell(14,5,round($notas/$cr,0),1,0,'C',true);
        $nnt=0;
        $nnt=round($notas/$cr,0);
        IF ($nnt == '0'){$nnt='F';}
        IF ($nnt == 'P' OR $nnt == 'p'){$nnt='';}
        $pdf->Cell(8,5,NLetra($nnt),1,0,'C',true);
        }
     ELSE
        {
        $pdf->Cell(14,5,'',1,0,'R',true);
        $pdf->Cell(8,5,'',1,0,'R',true);
        }
     IF ($cr2 > 0 AND $sem1=='Si'){
        $pdf->Cell(14,5,round($notas2/$cr2,0),1,0,'C',true);
        $nnt=round($notas2/$cr2,0);
        IF ($nnt == '0'){$nnt='F';}
        IF ($nnt == 'P' OR $nnt == 'p'){$nnt='';}
        $pdf->Cell(8,5,NLetra($nnt),1,0,'C',true);
        }
     ELSE
        {
        $pdf->Cell(14,5,'',1,0,'R',true);
        $pdf->Cell(8,5,'',1,0,'R',true);
        }
     $nn1='';
     IF ($cr3 > 0){
        $pdf->Cell(14,5,round($notas3/$cr3,0),1,0,'C',true);
        $nnt=round($notas3/$cr3,0);
        IF ($nnt == '0'){$nnt='F';}
        IF ($nnt == 'P' OR $nnt == 'p'){$nnt='';}
        $pdf->Cell(8,5,NLetra($nnt),1,0,'R',true);
        }
     ELSE
        {
        $pdf->Cell(14,5,'',1,0,'R',true);
        $pdf->Cell(8,5,'',1,0,'R',true);
        }
     IF ($cr4 > 0){
        $pdf->Cell(14,5,round($notas4/$cr4,0),1,0,'C',true);
        $nnt=round($notas4/$cr4,0);
        IF ($nnt == '0'){$nnt='F';}
        IF ($nnt == 'P' OR $nnt == 'p'){$nnt='';}
        $pdf->Cell(8,5,NLetra($nnt),1,0,'R',true);
        }
     ELSE
        {
        $pdf->Cell(14,5,'',1,0,'R',true);
        $pdf->Cell(8,5,'',1,0,'R',true);
        }
     IF ($cr5 > 0 AND $sem1=='Si'){
        $pdf->Cell(20,5,round($notas5/$cr5,0),1,0,'C',true);
        $nnt=round($notas5/$cr5,0);
        IF ($nnt == '0'){$nnt='F';}
        IF ($nnt == 'P' OR $nnt == 'p'){$nnt='';}
        $pdf->Cell(8,5,NLetra($nnt),1,1,'R',true);
        }
     ELSE
        {
        $pdf->Cell(20,5,'',1,0,'R',true);
        $pdf->Cell(8,5,'',1,1,'R',true);
        }
     }
     $pdf->Cell(10,15,'',0,1,'R');
     $pdf->Cell(74,27,'',1,0,'R',true);
     $pdf->Ln(1);

     $pdf->SetFont('Times','B',10);

     $pdf->Cell(50,5,'Notas: E/A=Excelente, B=Bueno, ',0,1,'L');
     $pdf->Cell(50,5,'S/C=Satisfactorio, D=Deficiente,',0,1,'L');
     $pdf->Cell(50,5,'F=No Satisfactorio, NM=Necesita Mejorar',0,1,'L');

     $pdf->Cell(50,5,utf8_encode('Escala de Evaluación:'),0,1,'L');
     $pdf->Cell(50,5,'100-90 A. 89-80 B, 79-70 C, 69-65 D,64-0 F',0,1,'L');
     $pdf->SetFont('Times','',11);

     $pdf->Ln(-26);

     $pdf->Cell(72,-25,'',0,0,'R');
     $pdf->Cell(5,15,'',0,0,'R');
     $pdf->SetFont('Times','B',12);
     $pdf->Cell(103,12,$estu->nombre.' '.$estu->apellidos,1,1,'C',true);
     $pdf->SetFont('Times','',11);
     $pdf->Cell(145,-20,'Nombre del Estudiante',0,0,'R');

     $pdf->Ln(7);
  
  $pdf->Cell(180,5,' Maestro: __________________________________',0,1,'R');
  $pdf->Cell(180,4,'',0,1,'R');
  $pdf->Cell(180,5,'Principal: __________________________________',0,1,'R');
  $pdf->Cell(180,4,'',0,1,'R');
  $pdf->Cell(180,5,'Encargado: __________________________________',0,1,'R');

  $pdf->Ln(3);
  $pdf->Cell(30,5,utf8_encode('Observación #'),1,0,'C',true);
  $pdf->Cell(90,5,utf8_encode('Descripción'),1,1,'C',true);

  $pdf->Cell(30,5,'1',1,0,'C');
  $pdf->Cell(90,5,utf8_encode('Rendimiento Académico bajo.'),1,1,'L');
  $pdf->Cell(30,5,'2',1,0,'C');
  $pdf->Cell(90,5,'Asignaciones sin entregar o incompletas.',1,1,'L');
  $pdf->Cell(30,5,'3',1,0,'C');
  $pdf->Cell(90,5,'Trabajos en clase incompletos y/o no entregados.',1,1,'L');
  $pdf->Cell(30,5,'4',1,0,'C');
  $pdf->Cell(90,5,'Comportamiento inadecuado.',1,1,'L');
  $pdf->Cell(30,5,'5',1,0,'C');
  $pdf->Cell(90,5,'Ausencias frecuentes .',1,1,'L');
  $pdf->Cell(30,5,'6',1,0,'C');
  $pdf->Cell(90,5,'Requiere Entrevista con los Padres.',1,1,'L');
  $l1=1;
}
$pdf->Output();
?>