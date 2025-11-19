<?php
require_once __DIR__ . '/../../../../app.php';
// TARJETA DE NOTAS IMEI
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

class nPDF extends PDF
{
  function Header()
  {
    parent::header();
	$this->Ln(15);
	$this->Cell(80);
	$this->SetFont('Arial','B',14);
   if ($_POST['tri']=='1'){$this->Cell(30,3,'INFORME DE NOTAS PERIODO 1 AGOSTO - OCTUBRE',0,0,'C');}
   if ($_POST['tri']=='2'){$this->Cell(30,3,'INFORME DE NOTAS PRIMER SEMESTRE',0,0,'C');}
   if ($_POST['tri']=='3'){$this->Cell(30,3,'INFORME DE NOTAS PERIODO 3 ENERO - MARZO',0,0,'C');}
   if ($_POST['tri']=='4'){$this->Cell(30,3,'INFORME DE NOTAS SEGUNDO SEMESTRE',0,0,'C');}
   $this->Ln(20);
}
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
if($idi=='Ingles'){
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
     $pdf->Cell(104,5,'Nombre:',1,0,'C', true);
     $pdf->Cell(20,5,'Grado:',1,0,'C', true);
     $pdf->Cell(40,5,'Grupo:',1,0,'C', true);
     $pdf->Cell(25,5,'Fecha:',1,1,'C', true);
     $pdf->Cell(104,5,$estu->apellidos.' '.$estu->nombre,1,0,'C');
     $pdf->Cell(20,5,$estu->grado,1,0,'C');
     $pdf->Cell(40,5,$estu->alias,1,0,'C');
     $pdf->Cell(25,5,date("m-d-Y"),1,1,'C');
     $pdf->Cell(1,5,' ',0,0,'C');
     $pdf->SetFont('Times','B',12);
     $pdf->Cell(28,5,'',0,0,'L');
     $pdf->Cell(28,5,'',0,0,'L');
     $pdf->SetFont('Times','',11);
     list($ss1, $ss2, $ss3) = explode("-",$estu->ss);
     $pdf->Cell(37,5,'',0,0,'C');
     $pdf->Cell(30,5,'',0,1,'C');
    
     $pdf->Cell(1,5,' ',0,0,'C');
     $pdf->Cell(56,5,'Asignatura',1,0,'C', true);
     $pdf->Cell(76,5,'Profesor(a)',1,0,'C', true);
     IF ($_POST['tri']=='15')
        {$pdf->Cell(19,5,'P-1',1,0,'C', true);
        $pdf->Cell(18,5,'P-2',1,0,'C', true);
        $pdf->Cell(19,5,'Sem-1',1,1,'C', true);
     }ELSE{
        $pdf->Cell(56,5,'Nota',1,1,'C', true);}

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
   $uni='';
   $rel='';
   $pp=0;
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
  IF (empty($con1) AND $_POST['tri']=='15'){$con1=$row->con2;}

  IF (empty($uni) AND $_POST['tri']=='1'){$uni=$row->un1;}
  IF (empty($uni) AND $_POST['tri']=='2'){$uni=$row->un2;}
  IF (empty($uni) AND $_POST['tri']=='3'){$uni=$row->un3;}
  IF (empty($uni) AND $_POST['tri']=='4'){$uni=$row->un4;}
  IF (empty($uni) AND $_POST['tri']=='15'){$uni=$row->un2;}

  IF (empty($rel) AND $_POST['tri']=='1'){$rel=$row->rel1;}
  IF (empty($rel) AND $_POST['tri']=='2'){$rel=$row->rel2;}
  IF (empty($rel) AND $_POST['tri']=='3'){$rel=$row->rel3;}
  IF (empty($rel) AND $_POST['tri']=='4'){$rel=$row->rel4;}
  IF (empty($rel) AND $_POST['tri']=='15'){$rel=$row->rel2;}

  IF ($row->nota1 > 0 AND $_POST['tri']=='1' AND $row->credito > 0 OR $row->nota1 == '0' AND $_POST['tri']=='1' AND $row->credito > 0 OR !empty($row->nota1) AND $_POST['tri']=='1' AND $row->credito > 0)
     {
     $cr=$cr+1;
     $notas=$notas+$row->nota1;
     $au=$au+$row->aus1;
     $ta=$ta+$row->tar1;
     }

  IF ($row->nota1 > 0 AND $_POST['tri']=='15' AND $row->credito > 0 OR $row->nota1 == '0' AND $_POST['tri']=='15' AND $row->credito > 0)
     {
     $cr=$cr+1;
     $notas=$notas+$row->nota1;
     $au=$au+$row->aus1;
     $ta=$ta+$row->tar1;
     }
  IF ($row->nota2 > 0 AND $_POST['tri']=='15' AND $row->credito > 0 OR $row->nota2 == '0' AND $_POST['tri']=='15' AND $row->credito > 0)
     {
     $cr2=$cr2+1;
     $notas2=$notas2+$row->nota2;
     $au=$au+$row->aus2;
     $ta=$ta+$row->tar2;
     }
  IF ($row->sem1 > 0 AND $_POST['tri']=='15' AND $row->credito > 0 OR $row->sem1 == '0' AND $_POST['tri']=='15' AND $row->credito > 0)
     {
     $cr3=$cr3+1;
     $notas3=$notas3+$row->sem1;
     }

  IF ($row->sem1 > 0 AND $_POST['tri']=='2' AND $row->credito > 0 OR $row->sem1 == '0' AND $_POST['tri']=='2' AND $row->credito > 0)
     {
     $cr=$cr+1;
     $notas=$notas+$row->sem1;
     }

  IF ($_POST['tri']=='2')
     {
     $au=$au+$row->aus2; 
     $ta=$ta+$row->tar2;
     }
     
  IF ($row->nota3 > 0 AND $_POST['tri']=='3' AND $row->credito > 0 OR $row->nota3 == '0' AND $_POST['tri']=='3' AND $row->credito > 0)
     {
     $cr=$cr+1;
     $notas=$notas+$row->nota3;
     }

  IF ($_POST['tri']=='3')
     {
     $au=$au+$row->aus3; 
     $ta=$ta+$row->tar3;
     }

  IF ($row->sem1 > 0 AND $_POST['tri']=='4' AND $row->credito > 0 OR $row->sem1 == '0' AND $_POST['tri']=='4' AND $row->credito > 0)
     {
     $cr=$cr+1;
     $notas=$notas+$row->sem1;
     }
  IF ($row->sem2 > 0 AND $_POST['tri']=='4' AND $row->credito > 0 OR $row->sem2 == '0' AND $_POST['tri']=='4' AND $row->credito > 0)
     {
     $cr1=$cr1+1;
     $notas1=$notas1+$row->sem2;
     }
  IF ($row->final > 0 AND $_POST['tri']=='4' AND $row->credito > 0 OR $row->final == '0' AND $_POST['tri']=='4' AND $row->credito > 0)
     {
     $cr3=$cr3+1;
     $notas3=$notas3+$row->final;
     $au=$au+$row->aus1; 
     $ta=$ta+$row->tar1;
     $au=$au+$row->aus2; 
     $ta=$ta+$row->tar2;
     $au=$au+$row->aus3; 
     $ta=$ta+$row->tar3;
     $au=$au+$row->aus4; 
     $ta=$ta+$row->tar4;
     }

     $pdf->SetFont('Times','',9);
     $pdf->Cell(1,5,'  ',0,0,'R');
     IF($idi=='Ingles')
       {$pdf->Cell(56,5,$row->desc2,1,0,'C');}
     ELSE
       {$pdf->Cell(56,5,$row->descripcion,1,0,'C');}
     $nn1='';
     IF ($_POST['tri']=='1'){$nn1=$row->nota1;
        IF ($row->nota1=='P' OR $row->nota1=='P ' OR $row->nota1=='EP' OR $row->nota2=='P' OR $row->nota2=='P ' OR $row->nota2=='EP'){$pp=1;$nn1='P';}
        }

     IF ($_POST['tri']=='2' OR $_POST['tri']=='15'){
        IF ($row->nota2 > 0 AND empty($row->nota2))
           {$nn1=$row->sem1;}ELSE{$nn1=$row->sem1;}
        IF ($row->nota1=='P' OR $row->nota1=='P ' OR $row->nota1=='EP' OR $row->nota2=='P' OR $row->nota2=='P ' OR $row->nota2=='EP'){$nn1='P';}
        IF ($row->credito > 0){
           IF ($row->nota1=='P' OR $row->nota1=='P ' OR $row->nota1=='EP' OR $row->nota2=='P' OR $row->nota2=='P ' OR $row->nota2=='EP'){$pp=1;$nn1='P';}}
           if ($row->nota2=='L1' or $row->nota2=='L2' or $row->nota2=='L3' or $row->nota2=='L4' or $row->nota2=='L5'){$nn1=$row->nota2;}
           }
     
     IF ($_POST['tri']=='3'){$nn1=$row->nota3;}
        IF ($row->nota3=='P' OR $row->nota3=='P ' OR $row->nota3=='EP'){$nn1='P';}
        IF ($row->credito > 0){
           IF ($row->nota3=='P' OR $row->nota3=='P ' OR $row->nota3=='EP'){$pp=1;$nn1='P';}
           }

     IF ($_POST['tri']=='4'){$nn1=$row->nota4;}

     IF ($_POST['tri']=='4'){
        IF ($row->nota2 > 0 AND empty($row->nota2))
           {$nn1=$row->sem1;}ELSE{$nn1=$row->sem1;}
        IF ($row->nota1=='P' OR $row->nota1=='P ' OR $row->nota1=='EP' OR $row->nota2=='P' OR $row->nota2=='P ' OR $row->nota2=='EP' OR $row->nota3=='P' OR $row->nota3=='P ' OR $row->nota3=='EP' OR $row->nota4=='P' OR $row->nota4=='P ' OR $row->nota4=='EP'){$nn1='P';}
        IF ($row->credito > 0){
           IF ($row->nota1=='P' OR $row->nota1=='P ' OR $row->nota1=='EP' OR $row->nota2=='P' OR $row->nota2=='P ' OR $row->nota2=='EP' OR $row->nota3=='P' OR $row->nota3=='P ' OR $row->nota3=='EP' OR $row->nota4=='P' OR $row->nota4=='P ' OR $row->nota4=='EP'){$pp=1;$nn1='P';}}
           }

     $pdf->Cell(76,5,'     '.$row->profesor,1,0,'C');
     IF ($_POST['tri']=='15'){
        $pdf->Cell(19,5,$row->nota1,1,0,'C');
        $pdf->Cell(18,5,$row->nota2,1,0,'C');
        $nn11=$row->sem1;
        IF ($row->nota1=='P' OR $row->nota1=='P ' OR $row->nota1=='EP' OR $row->nota2=='P' OR $row->nota2=='P ' OR $row->nota2=='EP'){$nn11='P';}
        if ($row->nota2=='L1' or $row->nota2=='L2' or$row->nota2=='L3' or $row->nota2=='L4' or $row->nota2=='L5'){$nn11=$row->nota2;}
        $pdf->Cell(19,5,$nn11,1,1,'C');
        }
        ELSE{$pdf->Cell(56,5,$nn1,1,1,'C');}
        }

     IF ($_POST['tri']=='1'){$de1=utf8_encode('Promedio del Periódo 1');}
     IF ($_POST['tri']=='2'){$de1='Promedio de Primer Semestre';}
     IF ($_POST['tri']=='3'){$de1=utf8_encode('Promedio del Periódo 3');}
     IF ($_POST['tri']=='4'){$de1='Promedio Final';}
     IF ($_POST['tri']=='15'){$de1='Promedio de Primer Semestre';}
     $pdf->Cell(1,5,'  ',0,0,'R');
     $pdf->Cell(56,5,'',1,0,'R');
     $pdf->Cell(76,5,$de1,1,0,'C');
     IF ($cr > 0){
        IF ($pp==1){$pdf->Cell(56,5,'P',1,1,'C');}
           ELSE
              {
              IF ($_POST['tri']=='15'){
                  $pdf->Cell(19,5,round($notas/$cr,0),1,0,'C');
                  $pdf->Cell(18,5,round($notas2/$cr2,0),1,0,'C');
                  $pdf->Cell(19,5,round($notas3/$cr3,0),1,1,'C');
                 }
              ELSE
                  {
                  $pdf->Cell(56,5,round($notas/$cr,0),1,1,'C');
                  }
               }
            }
     ELSE{$pdf->Cell(56,5,'',1,1,'C');}
     $pdf->Cell(1,5,'  ',0,0,'R');
     $pdf->Cell(56,5,'',0,0,'R');
     $pdf->Cell(76,5,'',0,0,'R');
     $pdf->Cell(56,5,'',0,1,'C');

     $pdf->Cell(1,5,'  ',0,0,'R');
     $pdf->Cell(188,5,'CUMPLIMIENTO A LAS NORMAS DEL REGLAMENTO',0,1,'C');

     $pdf->Cell(1,5,'',0,0,'R');
     $pdf->Cell(132,5,'Conducta',1,0,'C', true);
     $pdf->Cell(56,5,$con1,1,1,'C', true);
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