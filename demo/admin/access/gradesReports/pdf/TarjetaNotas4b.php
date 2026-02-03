<?php
// TARJETA DE NOTAS IMEI

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
  ['A&#65533;O', 'YEAR'],
]);

class nPDF extends PDF
{
  function Header()
  {
    parent::header();

	$this->Ln(10);
	$this->Cell(80);
	$this->SetFont('Arial','B',14);
    $this->Cell(30,3,'INFORME DE NOTAS',0,0,'C');
    $this->Ln(15);
}
//Pie de pgina
function Footer()
{
    $this->SetY(-60);
	$this->Cell(40,3,'Leyenda',0,0,'C');
	$this->Cell(50,3,'MS - Muy Satisfactorio',0,0,'L');
	$this->Cell(50,3,'S - Satisfactorio',0,0,'L');
	$this->Cell(50,3,'D - Domina',0,1,'L');
	$this->Cell(40,3,'',0,0,'C');
	$this->Cell(50,3,'NM - Necesita Mejorar',0,0,'L');
	$this->Cell(50,3,'P - Pendiente',0,0,'L');
	$this->Cell(50,3,'ND - No Domina',0,1,'L');
	$this->Cell(40,3,'',0,0,'C');
	$this->Cell(50,3,'EP - En Progreso',0,1,'L');

	$this->Cell(1,5,'',0,1,'C');
	$this->Cell(1,5,'',0,0,'C');
	$this->Cell(60,5,'_______________________________',0,0,'C');
	$this->Cell(60,5,'',0,0,'C');
	$this->Cell(60,5,'_______________________________',0,1,'C');
	$this->Cell(1,3,'',0,0,'C');
	$this->Cell(60,3,'FIRMA DEL MAESTRO(A)',0,0,'C');
	$this->Cell(60,3,'',0,0,'C');
	$this->Cell(60,3,'FIRMA DEL COORDINADOR',0,1,'C');
	$this->Cell(1,5,'',0,0,'C');
	$this->Cell(60,5,'',0,0,'C');
	$this->Cell(60,5,'_______________________________',0,1,'C');
	$this->Cell(1,3,'',0,0,'C');
	$this->Cell(60,3,'',0,0,'C');
	$this->Cell(60,3,'FIRMA DEL PADRE O TUTOR',0,0,'C');
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
$ccr = $_POST['cr'] ?? '';

$pdf->AliasNbPages();
$pdf->SetFont('Times','',11);
$mensaj = DB::table('codigos')->where([
       ['codigo', $men],
       ])->orderBy('codigo')->first();

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
  $ye='Curso Escolar:';
  $no='Nombre: ';
  $gr='Grado: ';
  $de='DESCRIPCION';
  $pr='PRO';
  $va='Valor Asignado';
  $fe='Fecha';
  $rr=0;
  $text1 = $mensaj->t1e ?? '';
  $text2 = $mensaj->t2e ?? '';
  $fi='PRO';
  $f2='AÑO';
  $se1='PRIMER SEMESTRE';
  $se2='SEGUNDO SEMESTRE';
  $qq1 ='   1T     C1     2T       C2    SEM-1';
  $qq2 ='   3T     C3     4T       C4    SEM-2';
  $pq='PROMEDIO';
  $asi='AUSENCIAS Y TARDANZAS';
  }

$teacher = DB::table('profesor')->where([
    ['grado', $grade],
])->orderBy('id')->first();

if ($grade=='alias')
   {
   $ss =$_POST['estu'];
$result = DB::table('year')->where([
    ['ss', $ss],
    ['year', $year],
])->orderBy('apellidos')->get();

   }
 else
   {
$result = DB::table('year')->where([
    ['alias', $grade],
    ['year', $year],
])->orderBy('apellidos')->get();
   }
$a=0;
foreach ($result as $estu) {
     $pdf->AddPage();
     $a=$a+1;
     $gra='';
     $pdf->SetFont('Times','',11);
     $pdf->Cell(1,5,'',0,0,'R');
     $pdf->Cell(28,5,$ye,1,0,'L',true);
     $pdf->Cell(25,5,$year,1,0,'C',true);
     $pdf->Cell(25,5,'Fecha:',1,0,'L',true);
     $pdf->Cell(25,5,date("m-d-Y"),1,0,'C',true);
     $pdf->Cell(55,5,utf8_encode('Número de Estudiante:'),1,0,'C',true);
     $pdf->Cell(30,5,$estu->cta,1,1,'C',true);
     $pdf->Cell(1,5,'',0,0,'R');
     $pdf->Cell(28,5,'Nombre:',1,0,'L',true);
     $pdf->Cell(160,5,$estu->apellidos.' '.$estu->nombre,1,1,'L',true);

     $pdf->Cell(1,5,'',0,0,'');
     $pdf->Cell(28,5,'Grado:',1,0,'',true);
     list($ss1, $ss2, $ss3) = explode("-",$estu->ss);
     $pdf->Cell(25,5,$estu->grado,1,0,'C',true);
     $pdf->Cell(50,5,'',1,0,'L',true);
     $pdf->Cell(35,5,'Grupo:',1,0,'C',true);
     $pdf->Cell(50,5,$estu->alias,1,1,'C',true);

     $pdf->Cell(1,5,' ',0,0,'C');
     $pdf->SetFont('Times','B',12);
     $pdf->Cell(28,5,'',0,0,'L');
     $pdf->Cell(28,5,'',0,0,'L');
     $pdf->SetFont('Times','',11);
     list($ss1, $ss2, $ss3) = explode("-",$estu->ss);
     $pdf->Cell(37,5,'',0,0,'C');
     $pdf->Cell(30,5,'',0,1,'C');
     
     $pdf->Cell(1,5,' ',0,0,'C');
     $pdf->Cell(49,5,'Asignatura',1,0,'C',true);
     $pdf->Cell(55,5,'Profesor(a)',1,0,'C',true);
     IF ($_POST['tri']=='15')
        {
        $pdf->Cell(12,5,'P-1',1,0,'C',true);
        $pdf->Cell(12,5,'P-2',1,0,'C',true);
        $pdf->Cell(12,5,'Sem-1',1,0,'C',true);
        $pdf->Cell(12,5,'P-3',1,0,'C',true);
        $pdf->Cell(12,5,'P-4',1,0,'C',true);
        $pdf->Cell(12,5,'Sem-2',1,0,'C',true);
        $pdf->Cell(12,5,'Final',1,1,'C',true);
     }ELSE{
        $pdf->Cell(56,5,'Nota',1,1,'C');}


$result2 = DB::table('padres')->where([
    ['grado', $estu->grado],
    ['year', $year],
    ['ss', $estu->ss],
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
 $con1='';
 
   $au = 0;
   $ta = 0;
   $au2 = 0;
   $ta2 = 0;
   $au3 = 0;
   $ta3 = 0;
   $au4 = 0;
   $ta4 = 0;
   $au5 = 0;
   $ta5 = 0;
   $au6 = 0;
   $ta6 = 0;
   $uni='';
   $rel='';
   $pp=0;
   $pp11=0;
   $pp12=0;
   $pp13=0;
   $pp14=0;
   $pp15=0;
   $pp16=0;
   $pp17=0;
   $pp18=0;
foreach ($result2 as $row) {
  $V5 = 0;
  $V6 = 0;
  $tot1t = "";
  $v7 = 0;
  $v8 = 0;
  $tot1t1 = "";
  IF (empty($con1) AND $_POST['tri']=='1'){$con1=$row->con1;}
  IF (empty($con1) AND $_POST['tri']=='2'){$con1=$row->con2;}
  IF (empty($con1) AND $_POST['tri']=='3'){$con1=$row->con3;}
  IF (empty($con1) AND $_POST['tri']=='4'){$con1=$row->con4;}
  IF (empty($con1) AND $_POST['tri']=='15'){$con1=$row->con4;}

  IF (empty($uni) AND $_POST['tri']=='1'){$uni=$row->un1;}
  IF (empty($uni) AND $_POST['tri']=='2'){$uni=$row->un2;}
  IF (empty($uni) AND $_POST['tri']=='3'){$uni=$row->un3;}
  IF (empty($uni) AND $_POST['tri']=='4'){$uni=$row->un4;}
  IF (empty($uni) AND $_POST['tri']=='15'){$uni=$row->un4;}

  IF (empty($rel) AND $_POST['tri']=='1'){$rel=$row->rel1;}
  IF (empty($rel) AND $_POST['tri']=='2'){$rel=$row->rel2;}
  IF (empty($rel) AND $_POST['tri']=='3'){$rel=$row->rel3;}
  IF (empty($rel) AND $_POST['tri']=='4'){$rel=$row->rel4;}
  IF (empty($rel) AND $_POST['tri']=='15'){$rel=$row->rel4;}

  IF (is_numeric($row->nota1) AND $_POST['tri']=='1' AND $row->credito > 0 OR $row->nota1 == '0' AND $_POST['tri']=='1' AND $row->credito > 0)
     {
     $cr=$cr+1;
     $notas=$notas+$row->nota1;
     $au=$au+$row[78];
     $ta=$ta+$row[82];
     }

  IF (is_numeric($row->nota1) AND $_POST['tri']=='15' AND $row->credito > 0 OR $row->nota1 == '0' AND $_POST['tri']=='15' AND $row->credito > 0)
     {
     $cr=$cr+1;
     $notas=$notas+$row->nota1;
     }
  IF (is_numeric($row->nota2) AND $_POST['tri']=='15' AND $row->credito > 0 OR $row->nota2 == '0' AND $_POST['tri']=='15' AND $row->credito > 0)
     {
     $cr2=$cr2+1;
     $notas2=$notas2+$row->nota2;
     }
  IF (is_numeric($row->sem1) AND $_POST['tri']=='15' AND $row->credito > 0 OR $row->sem1 == '0' AND $_POST['tri']=='15' AND $row->credito > 0)
     {
     $cr3=$cr3+1;
     $notas3=$notas3+$row->sem1;
     }

  IF (is_numeric($row->nota3) AND $_POST['tri']=='15' AND $row->credito > 0 OR $row->nota3 == '0' AND $_POST['tri']=='15' AND $row->credito > 0)
     {
     $cr4=$cr4+1;
     $notas4=$notas4+$row->nota3;
     }
  IF (is_numeric($row->nota4) AND $_POST['tri']=='15' AND $row->credito > 0 OR $row->nota4 == '0' AND $_POST['tri']=='15' AND $row->credito > 0)
     {
     $cr5=$cr5+1;
     $notas5=$notas5+$row->nota4;
     }
  IF (is_numeric($row->sem2) AND $_POST['tri']=='15' AND $row->credito > 0 OR $row->sem2 == '0' AND $_POST['tri']=='15' AND $row->credito > 0)
     {
     $cr6=$cr6+1;
     $notas6=$notas6+$row->sem2;
     }
  IF (is_numeric($row->final) AND $_POST['tri']=='15' AND $row->credito > 0 OR $row->final == '0' AND $_POST['tri']=='15' AND $row->credito > 0)
     {
     $cr7=$cr7+1;
     $notas7=$notas7+$row->final;
     }

  IF ($_POST['tri']=='15')
     {
     if(is_numeric($row->aus4)){$au=$au+$row->aus4;}
     if(is_numeric($row->tar4)){$ta=$ta+$row->tar4;}
     }

  IF (is_numeric($row->sem1) AND $_POST['tri']=='2' AND $row->credito > 0 OR $row->sem1 == '0' AND $_POST['tri']=='2' AND $row->credito > 0)
     {
     $cr=$cr+1;
     $notas=$notas+$row[19];
     }
     
  IF (is_numeric($row->nota3) AND $_POST['tri']=='3' AND $row->credito > 0 OR $row->nota3 == '0' AND $_POST['tri']=='3' AND $row->credito > 0)
     {
     $cr=$cr+1;
     $notas=$notas+$row->nota3;
     $au=$au+$row[80]; 
     $ta=$ta+$row[84];
     }

  IF (is_numeric($row->sem1) AND $_POST['tri']=='4' AND $row->credito > 0 OR $row->sem1 == '0' AND $_POST['tri']=='4' AND $row->credito > 0)
     {
     $cr=$cr+1;
     $notas=$notas+$row->sem1;
     }
  IF (is_numeric($row->sem2) AND $_POST['tri']=='4' AND $row->credito > 0 OR $row->sem2 == '0' AND $_POST['tri']=='4' AND $row->credito > 0)
     {
     $cr1=$cr1+1;
     $notas1=$notas1+$row->sem2;
     }
  IF (is_numeric($row->final) AND $_POST['tri']=='4' AND $row->credito > 0 OR $row->final == '0' AND $_POST['tri']=='4' AND $row->credito > 0)
     {
     $cr3=$cr3+1;
     $notas3=$notas3+$row->final;
     $au=$au+$row[78]; 
     $ta=$ta+$row[82];
     $au=$au+$row[79]; 
     $ta=$ta+$row[83];
     $au=$au+$row[80]; 
     $ta=$ta+$row[84];
     $au=$au+$row[81]; 
     $ta=$ta+$row[85];
     }


     $pdf->SetFont('Times','',9);
     $pdf->Cell(1,5,'  ',0,0,'R');
     IF($idi=='Ingles')
       {$pdf->Cell(49,5,$row->desc2,1,0,'C');}
     ELSE
       {$pdf->Cell(49,5,$row->descripcion,1,0,'C');}

   $nn1='';
   $pp=0;
   $pp11=0;
   $pp12=0;
   $pp13=0;

     $pdf->Cell(55,5,$row->profesor,1,0,'C');
        $pdf->Cell(12,5,$row->nota1,1,0,'C');
        $pdf->Cell(12,5,$row->nota2,1,0,'C');
        $nn11=$row->sem1;

     if ($row->nota1=='P' OR $row->nota1=='P ' OR $row->nota1=='EP' OR $row->nota1=='p' OR $row->nota1==' p'){$nn1='P';}
     if ($row->credito > 0)
        {
        if ($row->nota1=='P' OR $row->nota1=='P ' OR $row->nota1=='EP' OR $row->nota1=='p' OR $row->nota1==' p'){$pp=1;$pp14=1;$nn1='P';}
        }

     if ($row->nota2=='P' OR $row->nota2=='P ' OR $row->nota2=='EP' OR $row->nota2=='p' OR $row->nota2==' p'){$nn1='P';}
     if ($row->credito > 0)
        {
        if ($row->nota2=='P' OR $row->nota2=='P ' OR $row->nota2=='EP' OR $row->nota2==' p' OR $row->nota2=='p '){$pp11=1;$pp15=1;$nn1='P';}
        }
        if ($pp > 0 OR $pp11 > 0){$nn11='P';}
        $pdf->Cell(12,5,$nn11,1,0,'C');

        $pdf->Cell(12,5,$row->nota3,1,0,'C');
        $pdf->Cell(12,5,$row->nota4,1,0,'C');
        $nn12=$row->sem2;

     IF ($row->nota3=='P' OR $row->nota3=='P ' OR $row->nota3=='EP' OR $row->nota3=='p' OR $row->nota3=='p'){$nn12='P';}
     IF ($row->credito > 0){
        IF ($row->nota3=='P' OR $row->nota3=='P ' OR $row->nota3=='EP' OR $row->nota3=='p' OR $row->nota3=='p'){$pp12=1;$pp16=1;$nn12='P';}
        }

     IF ($row->nota4=='P' OR $row->nota4=='P ' OR $row->nota4=='EP' OR $row->nota4=='p' OR $row->nota4=='p'){$nn12='P';}
     IF ($row->credito > 0){
        IF ($row->nota4=='P' OR $row->nota4=='P ' OR $row->nota4=='EP' OR $row->nota4==' p' OR $row->nota4=='p'){$pp13=1;$pp17=1;$nn12='P';}
        }
        IF ($pp12 > 0 OR $pp13 > 0){$nn12='P';}
        $pdf->Cell(12,5,$nn12,1,0,'C');
        $nn13=$row->final;
        IF ($pp > 0 OR $pp11 > 0 OR $pp12 > 0 OR $pp13 > 0){$nn13='P';}
        $pdf->Cell(12,5,$nn13,1,1,'C');
}
     IF ($_POST['tri']=='15'){$de1='Promedio Final';}

     $pdf->Cell(1,5,'  ',0,0,'R');
     $pdf->Cell(49,5,'',1,0,'R',true);
     $pdf->Cell(55,5,$de1,1,0,'C',true);
           
           IF ($cr > 0)
              {
              IF ($pp14==1)
                 {$pdf->Cell(12,5,'P',1,0,'C',true);}ELSE{$pdf->Cell(12,5,round($notas/$cr,0),1,0,'C',true);}
              }
           ELSE
              {
              $pdf->Cell(12,5,' ',1,0,'C',true);
              }

           IF ($cr2 > 0)
              {
              IF ($pp15==1)
                 {$pdf->Cell(12,5,'P',1,0,'C',true);}ELSE{$pdf->Cell(12,5,round($notas2/$cr2,0),1,0,'C',true);}
              }
           ELSE
              {
              $pdf->Cell(12,5,' ',1,0,'C',true);
              }

           IF ($cr3 > 0)
              {
              IF ($pp14+$pp15 > 0)
                 {$pdf->Cell(12,5,'P',1,0,'C',true);}ELSE{$pdf->Cell(12,5,round($notas3/$cr3,0),1,0,'C',true);}
              }
           ELSE
              {
              $pdf->Cell(12,5,' ',1,0,'C',true);
              }

           IF ($cr4 > 0)
              {
              IF ($pp16==1)
                 {$pdf->Cell(12,5,'P',1,0,'C',true);}ELSE{$pdf->Cell(12,5,round($notas4/$cr4,0),1,0,'C',true);}
              }
           ELSE
              {
              $pdf->Cell(12,5,' ',1,0,'C',true);
              }

           IF ($cr5 > 0)
              {
              IF ($pp17==1)
                 {$pdf->Cell(12,5,'P',1,0,'C',true);}ELSE{$pdf->Cell(12,5,round($notas5/$cr5,0),1,0,'C',true);}
              }
           ELSE
              {
              $pdf->Cell(12,5,' ',1,0,'C',true);
              }

           IF ($cr6 > 0)
              {
              IF ($pp16+$pp17 > 0)
                 {$pdf->Cell(12,5,'P',1,0,'C',true);}ELSE{$pdf->Cell(12,5,round($notas6/$cr6,0),1,0,'C',true);}
              }
           ELSE
              {
              $pdf->Cell(12,5,' ',1,0,'C',true);
              }

           IF ($cr7 > 0)
              {
              IF ($pp14+$pp15+$pp16+$pp17 > 0)
                 {$pdf->Cell(12,5,'P',1,0,'C',true);}ELSE{$pdf->Cell(12,5,round($notas7/$cr7,0),1,0,'C',true);}
              }
           ELSE
              {
              $pdf->Cell(12,5,' ',1,0,'C',true);
              }

     $pdf->Cell(1,5,'  ',0,0,'R');
     $pdf->Cell(56,5,'',0,0,'R');
     $pdf->Cell(76,5,'',0,0,'R');
     $pdf->Cell(56,5,'',0,1,'C');

     $pdf->Cell(1,5,'  ',0,0,'R');
     $pdf->Cell(188,5,'CUMPLIMIENTO A LAS NORMAS DEL REGLAMENTO',0,1,'C');

     $pdf->Cell(1,5,'',0,0,'R');
     $pdf->Cell(132,5,'Conducta',1,0,'C');
     $pdf->Cell(56,5,$con1,1,1,'C');
     $pdf->Cell(1,5,'',0,0,'R');
     $pdf->Cell(132,5,'Ausencias',1,0,'C');
     $pdf->Cell(56,5,$au,1,1,'C');
     $pdf->Cell(1,5,'',0,0,'R');
     $pdf->Cell(132,5,'Tardanzas',1,0,'C');
     $pdf->Cell(56,5,$ta,1,1,'C');
     $pdf->Cell(1,5,'',0,0,'R');
     $pdf->Cell(132,5,'Uniforme',1,0,'C');
     $pdf->Cell(56,5,$uni,1,1,'C');
     $pdf->Cell(1,5,'',0,0,'R');
     $pdf->Cell(132,5,'Relaciones Interpersonales',1,0,'C');
     $pdf->Cell(56,5,$rel,1,1,'C');

  $pdf->Cell(1,10,'',0,0,'R');
  $pdf->Cell(188,10,'',1,1,'L');
  $pdf->Cell(1,-15,'',0,0,'R');
  $pdf->Cell(188,-15,$text1,0,1,'C');
  $pdf->Cell(1,23,'',0,0,'R');
  $pdf->Cell(188,23,$text2,0,1,'C');

}
$pdf->Output();
?>