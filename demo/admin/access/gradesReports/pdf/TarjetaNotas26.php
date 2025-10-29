<?php
require_once '../../../../app.php';
// HOGAR COLEGIO LA MILAGROSA

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


$school = new School(Session::id());
$year = $school->info('year2');
$teacherClass = new Teacher();
$studentClass = new Student();

$colegio = DB::table('colegio')->where([
    ['usuario', 'administrador']
])->orderBy('id')->first();

class nPDF extends PDF
{
function Header()
{   global $year;
    global $colegio;
	$this->SetFont('Arial','B',12);
	$this->Cell(0,24,'',1,1,'C');
	$this->Ln(-22);
	$this->Cell(0,5,$colegio->colegio,0,0,'C');
	$this->SetFont('Arial','',9);
	$this->Ln(5);
	$this->Cell(80);
	$this->Cell(30,2,$colegio->dir1,0,0,'C');
	$this->Ln(3);
	$this->Cell(80);
	$this->Cell(30,2,$colegio->dir2,0,0,'C');
	$this->Ln(3);
	$this->Cell(80);
	$this->Cell(30,2,$colegio->pueblo1.', '.$colegio->esta1.' '.$colegio->zip1,0,0,'C');
	$this->Ln(3);
	$this->Cell(80);
	$this->SetFont('Arial','',8);
	$this->Cell(30,3,'Tel. '.$colegio->telefono.' Fax '.$colegio->fax,0,0,'C');
	$this->Ln(3);
	$this->Cell(80);
	$this->Cell(30,3,$colegio->pagina,0,0,'C');


    $this->Ln(10);
	$this->Cell(80);
	$this->SetFont('Arial','B',12);
    $this->Cell(30,5,'INFORME DE NOTAS',0,1,'C');
    $this->Ln(5);
	$this->Cell(80);
    list($y1, $y2) = explode("-",$year);
    $this->Cell(30,5,utf8_encode('AÑO ESCOLAR 20').$y1.'-20'.$y2,0,1,'C');
    $this->Ln(5);
}

function Footer()
{
	$this->SetFont('Arial','',12);
    $this->SetY(-65);
    $this->Cell(60,5,'Promedio 1er. Semestre',0,0,'C');
    $this->Cell(68,5,'',0,0,'C');
    $this->Cell(55,5,'Promedio 2do. Semestre',0,0,'C');
    $this->Cell(58,2,'',0,1,'L');
    $this->Cell(77,2,'',0,0,'L');
    $this->Cell(58,5,'Promedio Anual',0,1,'L');

    $this->Cell(40,23,'',0,1,'C');

    $this->Cell(60,5,'Maestro(a)','T',0,'C');
    $this->Cell(68,5,'',0,0,'C');
    $this->Cell(55,5,'Directora','T',1,'C');

    $this->Cell(73,5,'',0,1,'C');
    $this->Cell(57,5,'',0,0,'C');
	$this->SetFont('Arial','',9);
	$this->Image('../../../../logo/logo.gif',85,190,40);
}

function RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = '')
 {
        $k = $this->k;
        $hp = $this->h;
        if($style=='F')
            $op='f';
        elseif($style=='FD' || $style=='DF')
            $op='B';
        else
            $op='S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));

        $xc = $x+$w-$r;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));
        if (strpos($corners, '2')===false)
            $this->_out(sprintf('%.2F %.2F l', ($x+$w)*$k,($hp-$y)*$k ));
        else
            $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);

        $xc = $x+$w-$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
        if (strpos($corners, '3')===false)
            $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-($y+$h))*$k));
        else
            $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);

        $xc = $x+$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
        if (strpos($corners, '4')===false)
            $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-($y+$h))*$k));
        else
            $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);

        $xc = $x+$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
        if (strpos($corners, '1')===false)
        {
            $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$y)*$k ));
            $this->_out(sprintf('%.2F %.2F l',($x+$r)*$k,($hp-$y)*$k ));
        }
        else
            $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
 }

function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
 {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
            $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
 }

function foo($a,$b,$c,$d, $n1, $n2, $l1, $l2)
{
//    include('../control.php');
//    $dat = "select * from colegio where usuario = 'administrador'";
//    $tab = mysql_query($dat, $con) or die ("problema con query") ;
//    $row = mysql_fetch_row($tab);
$colegio = DB::table('colegio')->where([
    ['usuario', 'administrador']
])->orderBy('id')->first();

   $this->SetY(-100);
   $this->SetFont('Arial','',12);
$this->SetLineWidth(0.2);
$this->SetFillColor(40);
$this->RoundedRect(79, 235, 50, 12, 3.5, '1234', 'DF');
$this->RoundedRect(79, 251, 50, 8, 3.5, '1234', 'DF');
$this->RoundedRect(79, 263, 50, 8, 3.5, '1234', 'DF');

$this->SetLineWidth(0.2);
$this->SetFillColor(240);

$this->RoundedRect(10, 207, 65, 8, 3.5, '1234', 'DF');
$this->RoundedRect(133, 207, 65, 8, 3.5, '1234', 'DF');
$this->RoundedRect(10, 220, 65, 8, 3.5, '1234', 'DF');
$this->RoundedRect(133, 220, 65, 8, 3.5, '1234', 'DF');

$this->RoundedRect(78, 234, 50, 12, 3.5, '1234', 'DF');
$this->RoundedRect(78, 250, 50, 8, 3.5, '1234', 'DF');
$this->RoundedRect(78, 262, 50, 8, 3.5, '1234', 'DF');

     $this->Cell(15,10,'',0,1,'C');
     $this->Cell(58,8,' Ausencias Semestre 1:  '.number_format($a, 0),0,0,'L');
     $this->Cell(70,8,'',0,0,'C');
     $this->Cell(58,8,' Ausencias Semestre 2:  '.number_format($b, 0),0,1,'L');
     $this->Cell(15,5,'',0,1,'C');
     $this->Cell(58,8,' Tardanza Semestre 1:  '.number_format($c, 0),0,0,'L');
     $this->Cell(70,8,'',0,0,'C');
     $this->Cell(58,8,' Tardanza Semestre 2:  '.number_format($d, 0),0,1,'L');
$l3='';
$n3='';
if ($n2 > 0)
   {
   $n3 = ($n1+$n2) / 2;
   }
IF ($n3 >= $colegio->vala){$l3='A';}
IF ($n3 >= $colegio->valb AND $n3 < $colegio->vala){$l3='B';}
IF ($n3 >= $colegio->valc AND $n3 < $colegio->valb){$l3='C';}
IF ($n3 >= $colegio->vald AND $n3 < $colegio->valc){$l3='D';}
IF ($n3 >= $colegio->valf AND $n3 < $colegio->vald){$l3='F';}
IF ($n3 == '0'){$l3='F';}


$this->RoundedRect(20, 238, 40, 8, 3.5, '1234', 'DF');
$this->RoundedRect(145, 238, 40, 8, 3.5, '1234', 'DF');
     $this->Cell(58,10,'',0,1,'L');
     $this->Cell(20,8,'',0,0,'L');
     if ($n1 > 0)
        {
        $this->Cell(15,8,''.number_format($n1, 2),0,0,'C');
        $this->Cell(5,8,' / '.$l1,0,0,'C');
        }
     else
        {$this->Cell(20,8,'',0,0,'C');}
     $this->Cell(44,8,'',0,0,'L');
     if ($n3 > 0)
        {
        $this->Cell(15,8,''.number_format($n3, 2),0,0,'C');
        $this->Cell(5,8,' / '.$l3,0,0,'C');
        }
     else
        {$this->Cell(20,8,'',0,0,'C');}
     $this->Cell(40,8,'',0,0,'L');
     if ($n2 > 0)
        {
        $this->Cell(15,8,''.number_format($n2, 2),0,0,'C');
        $this->Cell(5,8,' / '.$l2,0,1,'C');
        }
     else
        {$this->Cell(20,8,'',0,1,'C');}
     $this->Cell(58,6,'',0,1,'L');
     $this->Cell(70,5,'',0,0,'L');
     $this->Cell(27,5,''.'Total de Ausencias: ',0,0,'L');
     $this->Cell(20,5,''.number_format($a+$b, 0),0,1,'R');
     $this->Cell(58,7,'',0,1,'L');
     $this->Cell(70,5,'',0,0,'L');
     $this->Cell(27,5,''.'Total de Tardanzas: ',0,0,'L');
     $this->Cell(20,5,''.number_format($c+$d, 0),0,1,'R');


}

}


$grade = $_POST['grade'];
$men = $_POST['mensaje'];
$tri1 = $_POST['tri1'] ?? '';
$tri2 = $_POST['tri2'] ?? '';
$tri3 = $_POST['tri3'] ?? '';
$tri4 = $_POST['tri4'] ?? '';
$sem1 = $_POST['sem1'] ?? '';
$sem2 = $_POST['sem2'] ?? '';
$prof = $_POST['prof'] ?? '';
$ccr = $_POST['cr'] ?? '';
$tri = $_POST['tri'] ?? '';

$pdf = new nPDF();
$pdf->useFooter(false);
$pdf->SetTitle($lang->translation("Reporte de Notas") . " $year", true);
$pdf->Fill();

$pdf->AliasNbPages();

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
  $qq1 ='   Q1     CO       Q2     CO';
  $qq2 ='   Q3     CO       Q4     CO';
  $pq='AVERAGE.';
  $asi='ABSENCE AND LATE';
  
}ELSE{
  $ye=utf8_encode('AÑO ESCOLAR:');
  $no='Nombre: ';
  $gr='Grado: ';
  $de='DESCRIPCION';
  $pr='PRO';
  $va='Valor Asignado';
  $fe='Fechas';
  $rr=0;
//  $text1=$row11[1];
//  $text2=$row11[2];
  $text1 = $mensaj->t1e ?? '';
  $text2 = $mensaj->t2e ?? '';
  $fi='PRO';
  $f2=utf8_encode('AÑO');
  $se1='PRIMER SEMESTRE';
  $se2='SEGUNDO SEMESTRE';
  $qq1 =' 1T     C1   2T    C2   S-1';
  $qq2 =' 3T     C3   4T    C4   S-2';
  $pq='PROMEDIO';
  $asi='AUSENCIAS Y TARDANZAS';
  }

$teacher = $teacherClass->findByGrade($grade);
$students = $studentClass->findByGrade($grade);
$a=0;

foreach ($students as $estu) {
    $pdf->AddPage();
    $pdf->SetFont('Times','',11);
    $padres = DB::table('madre')->where([
        ['id', $estu->id]
    ])->orderBy('id')->first();
     $a=$a+1;
     $gra='';
     $pdf->SetFont('Times','B',11);
     $pdf->Cell(70,5,'Apellidos',1,0,'C',true);
     $pdf->Cell(50,5,'Nombre',1,0,'C',true);
     $pdf->Cell(30,5,'Num Estudiante',1,0,'C',true);
     $pdf->Cell(22,5,'Num ID',1,0,'C',true);
     $pdf->Cell(17,5,'Grado',1,1,'C',true);
     $pdf->SetFont('Times','',11);

     list($ss1, $ss2, $ss3) = explode("-",$estu->ss);

     $pdf->Cell(70,5,$estu->apellidos,1,0,'C');
     $pdf->Cell(50,5,$estu->nombre,1,0,'C');
     $pdf->Cell(30,5,'XXX-XX-'.$ss3,1,0,'C');
     $pdf->Cell(22,5,$estu->id,1,0,'C');
     $pdf->Cell(17,5,$estu->grado,1,1,'C');

     $pdf->Cell(40,5,'',0,1,'C');

     $pdf->SetFont('Times','B',12);
     $pdf->Cell(16,5,'Curso',1,0,'C',true);
     $pdf->Cell(45,5,utf8_encode('Descripción'),1,0,'C',true);
     $pdf->Cell(50,5,'Maestro(a)',1,0,'C',true);

     $pdf->Cell(15,5,'Sem 1',1,0,'C',true);
     $pdf->Cell(12,5,'Cond.',1,0,'C',true);
     $pdf->Cell(15,5,'Sem 2',1,0,'C',true);
     $pdf->Cell(12,5,'Cond.',1,0,'C',true);
     $pdf->Cell(15,5,'Final',1,0,'C',true);
     $pdf->Cell(9,5,'Cr.',1,1,'C',true);
     $pdf->SetFont('Times','',11);
     $cursos = DB::table('padres')->where([
        ['year', $year],
        ['ss', $estu->ss],
        ['grado', $grade],
        ['curso', '!=', ''],
        ['curso', 'NOT LIKE', '%AA-%']
    ])->orderBy('orden')->get();

 $notas=0;
 $cr=0; 
 $au1=0; 
 $ta1=0;

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
 $notas8=0;
 $cr8=0; 
 $notas9='';
 $cr9=0; 
 $crt=0; 

foreach ($cursos as $curso) {
  $V5 = 0;
  $V6 = 0;
  $tot1t = "";
  $v7 = 0;
  $v8 = 0;
  $tot1t1 = "";
  $notas5=0;
  $cr5=0; 
  $notas6=0;
  $cr6=0; 
  $notas7=0;
  $cr7=0; 
  $notas8='';
  $cr8=0; 
  $notas9=0;
  $cr9=0; 
  $crt=$crt+$curso->email;

  IF ($curso->sem1 > 0 )
     {
     $cr=$cr+1;
     $notas=$notas+$curso->sem1;
     $cr5=$cr5+1;
     $notas5=$notas5+$curso->sem1;
     }

  IF ($curso->sem2 > 0)
     {
     $cr2=$cr2+1;
     $notas2=$notas2+$curso->sem2;
     $cr5=$cr5+1;
     $notas5=$notas5+$curso->sem2;
     }
     $pdf->SetFont('Times','',9);
     $pdf->Cell(16,7,$curso->curso,1,0,'C');
     IF($idi=='Ingles')
       {$pdf->Cell(45,7,$curso->desc2,1,0);}
     ELSE
       {$pdf->Cell(45,7,$curso->descripcion,1,0);}
     $pdf->Cell(50,7,$curso->profesor,1,0,'L');

     $nnt2='';
    
     IF($sem1=='Si')
       {
       $pdf->Cell(10,7,$curso->sem1,1,0,'C');}
     ELSE
       {$pdf->Cell(10,7,'',1,0,'C');}
     $nnf1='';
     IF ($curso->sem1 >= $colegio->vala){$nnf1='A';}
     IF ($curso->sem1 >= $colegio->valb AND $curso->sem1 < $colegio->vala){$nnf1='B';}
     IF ($curso->sem1 >= $colegio->valc AND $curso->sem1 < $colegio->valb){$nnf1='C';}
     IF ($curso->sem1 >= $colegio->vald AND $curso->sem1 < $colegio->valc){$nnf1='D';}
     IF ($curso->sem1 >= $colegio->valf AND $curso->sem1 < $colegio->vald){$nnf1='F';}
     IF ($curso->sem1 == '0'){$nnf1='F';}
     IF ($curso->sem1 == 'P' OR $curso->sem1 == 'p'){$nnf1='';}
     IF($sem1=='Si')
       {$pdf->Cell(5,7,$nnf1,1,0,'C');}
     ELSE
       {$pdf->Cell(5,7,'',1,0,'C');}

     $pdf->Cell(12,7,$curso->con2,1,0,'C');


     IF($sem2=='Si')
       {
       $pdf->Cell(10,7,$curso->sem2,1,0,'C');
       }
     ELSE
       {
       $pdf->Cell(10,7,'',1,0,'C');
       }
     $nnf1='';
     IF ($curso->sem2 >= $colegio->vala){$nnf1='A';}
     IF ($curso->sem2 >= $colegio->valb AND $curso->sem2 < $colegio->vala){$nnf1='B';}
     IF ($curso->sem2 >= $colegio->valc AND $curso->sem2 < $colegio->valb){$nnf1='C';}
     IF ($curso->sem2 >= $colegio->vald AND $curso->sem2 < $colegio->valc){$nnf1='D';}
     IF ($curso->sem2 >= $colegio->valf AND $curso->sem2 < $colegio->vald){$nnf1='F';}
     IF ($curso->sem2 == '0'){$nnf1='F';}
     IF ($curso->sem2 == 'P' OR $curso->sem2 == 'p'){$nnf1='';}

     IF($sem2=='Si')
       {$pdf->Cell(5,7,$nnf1,1,0,'C');}
     ELSE
       {$pdf->Cell(5,7,'',1,0,'C');}

     $pdf->Cell(12,7,$curso->con4,1,0,'C');

     if ($cr5 > 0)
        {
        $r1=round($notas5/$cr5,0);
        $pdf->Cell(10,7,$r1,1,0,'C');
        IF ($r1 >= $colegio->vala){$nnf1='A';}
        IF ($r1 >= $colegio->valb AND $r1 < $colegio->vala){$nnf1='B';}
        IF ($r1 >= $colegio->valc AND $r1 < $colegio->valb){$nnf1='C';}
        IF ($r1 >= $colegio->vald AND $r1 < $colegio->valc){$nnf1='D';}
        IF ($r1 >= $colegio->valf AND $r1 < $colegio->vald){$nnf1='F';}
        IF ($r1 == '0'){$nnf1='F';}
        $pdf->Cell(5,7,$nnf1,1,0,'C');
        }
     else
        {
        $pdf->Cell(15,7,'',1,0,'C');
        }

     $pdf->Cell(9,7,$curso->credito,1,1,'R');

  }
	$pdf->SetFont('Arial','',9);
    $pdf->Cell(05,5,'',0,1,'C');
    $pdf->Cell(15,5,'Conducta:',0,0,'C');
    $pdf->Cell(05,5,'',0,0,'C');
    $pdf->Cell(40,5,'MS= Muy Satisfactorio',0,0,'C');
    $pdf->Cell(40,5,'S= Satisfactorio',0,0,'C');
    $pdf->Cell(40,5,'PS= Poco Satisfactorio',0,0,'C');
    $pdf->Cell(40,5,'NS= No Satisfactorio',0,0,'C');

$n1='';
$n2='';
     if ($cr > 0)
        {
        $n1=round($notas/$cr,2);
        }
     if ($cr2 > 0)
        {
        $n2=round($notas2/$cr2,2);
        }

$l1='';
$l2='';


IF ($n1 >= $colegio->vala){$l1='A';}
IF ($n1 >= $colegio->valb AND $n1 < $colegio->vala){$l1='B';}
IF ($n1 >= $colegio->valc AND $n1 < $colegio->valb){$l1='C';}
IF ($n1 >= $colegio->vald AND $n1 < $colegio->valc){$l1='D';}
IF ($n1 >= $colegio->valf AND $n1 < $colegio->vald){$l1='F';}
IF ($n1 == '0'){$l1='F';}


IF ($n2 >= $colegio->vala){$l2='A';}
IF ($n2 >= $colegio->valb AND $n2 < $colegio->vala){$l2='B';}
IF ($n2 >= $colegio->valc AND $n2 < $colegio->valb){$l2='C';}
IF ($n2 >= $colegio->vald AND $n2 < $colegio->valc){$l2='D';}
IF ($n2 >= $colegio->valf AND $n2 < $colegio->vald){$l2='F';}
IF ($n2 == '0'){$l2='F';}
   $result7 = DB::table('asispp')->where([
          ['ss', $estu->ss],
          ['baja', ''],
          ['year', $year],
      ])->get();

list($y1,$y2) = explode("-",$year);
$yy1='20'.$y1.'-12-24';
$yy2='20'.$y2.'-01-01';
$yy3='20'.$y2.'-05-31';
foreach ($result7 as $reg) {
      if ($reg->codigo < 8 and $yy1 > $reg->fecha)
         {
         $au1=$au1+1;
         }
      if ($reg->codigo > 7 and $yy1 > $reg->fecha)
         {
         $ta1=$ta1+1;
         }
      if ($reg->codigo < 8 and $reg->fecha > $yy2 and $reg->fecha < $yy3)
         {
         $au2=$au2+1;
         }
      if ($reg->codigo > 7 and $reg->fecha > $yy2 and $reg->fecha < $yy3)
         {
         $ta2=$ta2+1;
         }
      }

  $pdf->foo($au1, $au2, $ta1, $ta2, $n1, $n2, $l1, $l2);
  $pdf->SetFont('Times','',11);
  }
$pdf->Output();
?>