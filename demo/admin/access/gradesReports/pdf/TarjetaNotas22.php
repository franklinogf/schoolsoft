<?php
require_once __DIR__ . '/../../../../app.php';
// Colegio San Antonio
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
    ['Femeninas', 'Females'],
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
    } else  if ($valor <= '59') {
        return 'F';
    }
}

class nPDF extends PDF
{
    function Header()
    {
        parent::header();
	    $this->Image('../../../../logo/firma1.gif',88,270,30);    
        $this->Cell(80);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 3, 'INFORME DE NOTAS', 0, 0, 'C');
        $this->Ln(8);
	}

    function Footer() 
    {
$idi = '';

IF($idi=='Ingles'){
    $this->SetY(-70);
	$this->Cell(50,6,'GRADING SCALE',1,0,'C',true);
	$this->Cell(80,6,'LETTER DESCRIPTION',1,0,'C',true);
	$this->Cell(60,6,'LETTER DESCRIPTION',1,1,'C',true);
	$this->Cell(50,22,'',1,0,'C');
	$this->Cell(140,22,'',1,1,'C');
    $this->SetY(-70);
	$this->Cell(50,5,'A  =  90  - 100 , {3.50 - 4.00}       ',0,0,'R');
	$this->Cell(7,5,' ',0,0,'E');
	$this->Cell(65,5,' E = EXCELLENT',0,0,'E');
	$this->Cell(70,5,' INC = INCOMPLETE',0,1,'E');
	$this->Cell(50,5,'B  =  80  -   89 , {2.50 - 3.49}       ',0,0,'R');
	$this->Cell(7,5,' ',0,0,'E');
	$this->Cell(65,5,' B,G = GOOD',0,0,'E');
	$this->Cell(70,5,' P = PARTICIPATION',0,1,'E');
	$this->Cell(50,5,'C  =  70  -   79 , {1.50 - 2.49}       ',0,0,'R');
	$this->Cell(7,5,' ',0,0,'E');
	$this->Cell(65,5,' S = SATISFACTORY',0,0,'E');
	$this->Cell(70,5,' C1 = 1st. QUARTER',0,1,'E');
	$this->Cell(50,5,'D  =  60  -   69 , {0.80 - 1.49}       ',0,0,'R');
	$this->Cell(7,5,' ',0,0,'E');
	$this->Cell(65,5,' NM,NI = NEEDS IMPROVEMENT',0,0,'E');
	$this->Cell(70,5,' CO = CONDUCT',0,1,'E');
	$this->Cell(50,5,'F  =    0  -   59 , {0.00 - 0.79}       ',0,0,'R');
	$this->Cell(7,5,' ',0,0,'E');
	$this->Cell(65,5,' NS,U = UNSATISFACTORY',0,0,'E');
	$this->Cell(70,5,' CR = CREDITS',0,1,'E');
	$this->Cell(5,55,'  ',0,0,'R');
	$this->Cell(50,55,' ______________________________ ',0,0,'C');
	$this->Cell(65,55,'  ',0,0,'C');
	$this->Cell(65,55,' ______________________________ ',0,1,'C');
	$this->Cell(5,-47,'  ',0,0,'R');
	$this->Cell(50,-47,'Teacher / Authorized Signature',0,0,'C');
	$this->Cell(65,-47,'',0,0,'C');
	$this->Cell(65,-47,"Parent's Signature",0,0,'C');
}ELSE{
    $this->SetY(-75);
	$this->Cell(50,8,'ESCALA',1,0,'C',true);
	$this->Cell(80,8,'A los padres o encargado:',1,0,'C',true);
	$this->Cell(60,4,'Hace un trabajo deficiente',1,1,'C',true);
	$this->Cell(130,4,'',0,0,'C');
	$this->Cell(60,4,'por las causas indicadas:',1,1,'C',true);
	$this->Cell(50,35,'',1,0,'C');
	$this->Cell(80,35,'',1,0,'C');
	$this->Cell(60,35,'',1,1,'C');
    $this->SetY(-67);
	$this->Cell(50,5,'A  =  90  - 100 , {4.00 - 3.50}       ',0,0,'R');
	$this->Cell(2,5,' ',0,0,'E');
	$this->Cell(78,5,'Favor revisar este informe. Si tiene alguna duda sobre',0,0,'L');
	$this->Cell(70,5,'  ___ Se ausenta demasiado.',0,1,'E');
	$this->Cell(50,5,'B  =  80  -   89 , {3.49 - 2.50}       ',0,0,'R');
	$this->Cell(2,5,' ',0,0,'E');
	$this->Cell(78,5,utf8_encode('la calificación otorgada, deberá someter su reclamación'),0,0,'L');
	$this->Cell(70,5,'  ___ No hace el trabajo asignado.',0,1,'L');
	$this->Cell(50,5,'C  =  70  -   79 , {2.49 - 1.50}       ',0,0,'R');
	$this->Cell(2,5,' ',0,0,'E');
	$this->Cell(78,5,utf8_encode('por escrito al maestro(a) quien revisará los trabajos del'),0,0,'E');
	$this->Cell(70,5,'  ___ No estudia en casa.',0,1,'E');
	$this->Cell(50,5,'D  =  60  -   69 , {1.49 - 0.80}       ',0,0,'R');
	$this->Cell(2,5,' ',0,0,'E');
	$this->Cell(78,5,utf8_encode('estudiante, explicará los criterios de evaluación utilizados'),0,0,'E');
	$this->Cell(70,5,'  ___ No domina bastante el ingles.',0,1,'E');
	$this->Cell(50,5,'F  =    0  -   59 , {0.79 - 0.00}       ',0,0,'R');
	$this->Cell(2,5,' ',0,0,'E');
	$this->Cell(78,5,utf8_encode('y determinará si se justifica alguna enmienda. Exhortamos'),0,0,'E');
	$this->Cell(70,5,'  ___ Falta conocimiento fundamentales.',0,1,'E');
	$this->Cell(52,5,' ',0,0,'E');
	$this->Cell(78,5,'a los padres a integrarse en el proceso educativo de su',0,0,'E');
	$this->Cell(60,5,' ',0,1,'E');
	$this->Cell(52,5,' ',0,0,'E');
	$this->Cell(78,5,utf8_encode('hijo(a) y juntos lograr un mejor desempeño académico.'),0,0,'E');
	$this->Cell(60,5,' ',0,1,'E');




	$this->Cell(5,10,'  ',0,1,'R');

	$this->Cell(3,5,'  ',0,0,'R');
	$this->Cell(50,5,' ______________________________ ',0,0,'C');
	$this->Cell(20,5,'  ',0,0,'C');
	$this->Cell(50,5,' __________________________________ ',0,1,'C');
	$this->Cell(3,3,'  ',0,0,'R');
	$this->Cell(50,3,'Maestro(a) / Firma Autorizada',0,0,'C');
	$this->Cell(20,3,'',0,0,'C');
	$this->Cell(50,3,'Sra. Marisela Ortiz Principal',0,0,'C');
	$this->Cell(45,3,'SELLO',0,0,'R');

  }
}
}

$grade = $_POST['grade'] ?? '';
$men = $_POST['mensaje'] ?? '';
$tri1 = $_POST['tri1'] ?? '';
$tri2 = $_POST['tri2'] ?? '';
$tri3 = $_POST['tri3'] ?? '';
$tri4 = $_POST['tri4'] ?? '';
$sem1 = $_POST['sem1'] ?? '';
$sem2 = $_POST['sem2'] ?? '';
$prof = $_POST['prof'] ?? '';
$ccr = $_POST['cr'] ?? '';

$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',11);

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
  $text1 = $mensaj->t1i ?? '';
  $text2 = $mensaj->t2i ?? '';
  $fi='YR';
  $f2='AVG';
  $se1='FIRST SEMESTER';
  $se2='SECOND SEMESTER';
  $qq1 ='   Q1     CO     Q2     CO';
  $qq2 ='   Q3     CO     Q4     CO';
  $pq='QUARTER AVG.';
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
  $text1 = $mensaj->t1e ?? '';
  $text2 = $mensaj->t2e ?? '';
  $fi='PRO';
  $f2=utf8_encode('AÑO');
  $se1='PRIMER SEMESTRE';
  $se2='SEGUNDO SEMESTRE';
  $qq1 ='   T1     CO     T2     CO';
  $qq2 ='   T3     CO     T4     CO';
  $pq='PROMEDIOS';
  $asi='AUSENCIAS Y TARDANZAS';
  }


$pdf = new nPDF();
$pdf->useFooter(false);
$school = new School(Session::id());
$teacherClass = new Teacher();
$studentClass = new Student();

$year = $school->info('year2');
$pdf->SetTitle($lang->translation("Reporte de Notas") . " $year", true);
$pdf->Fill();

$teacher = $teacherClass->findByGrade($grade);
$students = $studentClass->findByGrade($grade);

$colegio = DB::table('colegio')->where([
    ['usuario', 'administrador']
])->orderBy('id')->first();

$a = 0;

foreach ($students as $estu) {

    $pdf->AddPage();

    $gra = '';
    $padres = DB::table('madre')->where([
        ['id', $estu->id]
    ])->orderBy('id')->first();

     $pdf->SetFont('Times','',11);
     $pdf->Cell(1,5,'',0,0,'R');
     $pdf->Cell(30,5,$ye,0,0,'L');
     $pdf->Cell(70,5,$year,0,0,'c');
     $pdf->Cell(7,5,'ID: ',0,0,'L');
     $pdf->Cell(10,5,' ',0,0,'L');
     $pdf->Cell(75,5,date("m-d-Y"),0,1,'R');

     $pdf->Cell(1,5,' ',0,0,'C');
     $pdf->Cell(125,5,$no.' '.$estu->apellidos . ' ' . $estu->nombre, 1, 0, 'L', true);
     list($ss1, $ss2, $ss3) = explode("-", $estu->ss);
     $pdf->Cell(37,5,'',1,0,'C',true);
     $pdf->Cell(30,5,$gr.' '.$grade,1,1,'C',true);
     
     $pdf->Cell(1,5,' ',0,0,'C');
     $pdf->Cell(56,5,$de,1,0,'C',true);
     $pdf->Cell(56,5,$se1,1,0,'C',true);
     $pdf->Cell(56,5,$se2,1,0,'C',true);
     $pdf->Cell(14,5,$fi,1,0,'C',true);
     $pdf->Cell(10,5,'CR',1,1,'C',true);
     $pdf->Cell(1,5,'  ',0,0,'R');
     $pdf->Cell(56,5,'',1,0,'R',true);
     $pdf->Cell(56,5,$qq1,1,0,'L',true);
     $pdf->Cell(56,5,$qq2,1,0,'L',true);
     $pdf->Cell(14,5,$f2,1,0,'C',true);
     $pdf->Cell(10,5,'',1,1,'C',true);

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
 $cr8=0; 
 $cr88=0; 

  foreach ($cursos as $curso) {
  $a=$a+1;
  $V5 = 0;
  $V6 = 0;
  $tot1t = "";
  $v7 = 0;
  $v8 = 0;
  $tot1t1 = "";
  IF ($curso->credito > 0 AND $curso->nota1 > 0)
     {
     $cr=$cr+$curso->credito;
     $notas=$notas+($curso->nota1*$curso->credito);
     }
    $cr88=$cr88+$curso->credito; 

  IF ($curso->nota1 > 0 OR $curso->nota2 > 0)
     {}
  ELSE{
     IF ($curso->nota1=='E' OR $curso->nota1=='e')
        {
        $V5 = $V5 + 4;
        $V6 = $V6 + 1;
        }
     IF ($curso->nota1=='G' OR $curso->nota1=='g' OR $curso->nota1=='B' OR $curso->nota1=='b')
        {
        $V5 = $V5 + 3;
        $V6 = $V6 + 1;
        }
     IF ($curso->nota1=='S' OR $curso->nota1=='s')
        {
        $V5 = $V5 + 2;
        $V6 = $V6 + 1;
        }
     IF ($curso->nota1=='NM' OR $curso->nota1=='nm' OR $curso->nota1=='NI' OR $curso->nota1=='ni')
        {
        $V5 = $V5 + 1;
        $V6 = $V6 + 1;
        }
     IF ($curso->nota1=='NS' OR $curso->nota1=='ns' OR $curso->nota1=='U' OR $curso->nota1=='u')
        {
        $V6 = $V6 + 1;
        }
     IF ($curso->nota2=='E' OR $curso->nota2=='e')
        {
        $V5 = $V5 + 4;
        $V6 = $V6 + 1;
        }
     IF ($curso->nota2=='G' OR $curso->nota2=='g' OR $curso->nota2=='B' OR $curso->nota2=='b')
        {
        $V5 = $V5 + 3;
        $V6 = $V6 + 1;
        }
     IF ($curso->nota2=='S' OR $curso->nota2=='s')
        {
        $V5 = $V5 + 2;
        $V6 = $V6 + 1;
        }
     IF ($curso->nota2=='NM' OR $curso->nota2=='nm' OR $curso->nota2=='NI' OR $curso->nota2=='ni')
        {
        $V5 = $V5 + 1;
        $V6 = $V6 + 1;
        }
     IF ($curso->nota2=='NS' OR $curso->nota2=='ns' OR $curso->nota2=='U' OR $curso->nota2=='u')
        {
        $V6 = $V6 + 1;
        }
     IF ($V6 > 0){
        IF ($V5 / $V6 >= 3.5)
           {$tot1t = "E";}
        ELSE{
           IF ($V5 / $V6 >= 2.5)
              {$tot1t = "G";}
           ELSE{
              IF ($V5 / $V6 >= 1.5)
                 {$tot1t = "S";}
              ELSE{
                 IF ($V5 / $V6 >= 0.8)
                    {$tot1t = "NI";}
                 ELSE
                    {$tot1t = "NS";}
                 }
              }
           }
        }
     }

  IF ($curso->nota3 > 0 OR $curso->nota4 > 0)
     {}
  ELSE{
     IF ($curso->nota3=='E' OR $curso->nota3=='e')
        {
        $v7 = $v7 + 4;
        $v8 = $v8 + 1;
        }
     IF ($curso->nota3=='G' OR $curso->nota3=='g' OR $curso->nota3=='B' OR $curso->nota3=='b')
        {
        $v7 = $v7 + 3;
        $v8 = $v8 + 1;
        }
     IF ($curso->nota3=='S' OR $curso->nota3=='s')
        {
        $v7 = $v7 + 2;
        $v8 = $v8 + 1;
        }
     IF ($curso->nota3=='NM' OR $curso->nota3=='nm' OR $curso->nota3=='NI' OR $curso->nota3=='ni')
        {
        $v7 = $v7 + 1;
        $v8 = $v8 + 1;
        }
     IF ($curso->nota3=='NS' OR $curso->nota3=='ns' OR $curso->nota3=='U' OR $curso->nota3=='u')
        {
        $v8 = $v8 + 1;
        }
     IF ($curso->nota4=='E' OR $curso->nota4=='e')
        {
        $v7 = $v7 + 4;
        $v8 = $v8 + 1;
        }
     IF ($curso->nota4=='G' OR $curso->nota4=='g' OR $curso->nota4=='B' OR $curso->nota4=='b')
        {
        $v7 = $v7 + 3;
        $v8 = $v8 + 1;
        }
     IF ($curso->nota4=='S' OR $curso->nota4=='s')
        {
        $v7 = $v7 + 2;
        $v8 = $v8 + 1;
        }
     IF ($curso->nota4=='NM' OR $curso->nota4=='nm' OR $curso->nota4=='NI' OR $curso->nota4=='ni')
        {
        $v7 = $v7 + 1;
        $v8 = $v8 + 1;
        }
     IF ($curso->nota4=='NS' OR $curso->nota4=='ns' OR $curso->nota4=='U' OR $curso->nota4=='u')
        {
        $v8 = $v8 + 1;
        }
     IF ($v8 > 0){
        IF ($v7 / $v8 >= 3.5)
           {$tot1t1 = "E";}
        ELSE{
           IF ($v7 / $v8 >= 2.5)
              {$tot1t1 = "G";}
           ELSE{
              IF ($v7 / $v8 >= 1.5)
                 {$tot1t1 = "S";}
              ELSE{
                 IF ($v7 / $v8 >= 0.8)
                    {$tot1t1 = "NI";}
                 ELSE
                    {$tot1t1 = "NS";}
                 }
              }
           }
        }
     }
  
  IF ($curso->credito > 0 AND $curso->nota2 > 0)
     {
     $cr2=$cr2+$curso->credito;
     $notas2=$notas2+($curso->nota2*$curso->credito);
     }
     
  IF ($curso->credito > 0 AND $curso->nota3 > 0)
     {
     $cr3=$cr3+$curso->credito;
     $notas3=$notas3+($curso->nota3*$curso->credito);
     }
  IF ($curso->credito > 0 AND $curso->nota4 > 0)
     {
     $cr4=$cr4+$curso->credito;
     $notas4=$notas4+($curso->nota4*$curso->credito);
     }
  IF ($curso->credito > 0 AND $curso->sem1 > 0)
     {
     $cr5=$cr5+$curso->credito;
     $notas5=$notas5+($curso->sem1*$curso->credito);
     }
  IF ($curso->credito > 0 AND $curso->sem2 > 0)
     {
     $cr6=$cr6+$curso->credito;
     $notas6=$notas6+($curso->sem2*$curso->credito);
     }
  IF ($curso->credito > 0 AND $curso->final > 0)
     {
//     $cr7=$cr7+$row[21];
//     $notas7=$notas7+($row[22]*$row[21]);
     }
  IF ($colegio->asis=='A'){
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
     }

     $pdf->SetFont('Times','',9);
     $pdf->Cell(1,5,'  ',0,0,'R');
     IF($idi=='Ingles')
       {$pdf->Cell(56,5,$row->desc2,1,0);}
     ELSE
       {$pdf->Cell(56,5,$curso->descripcion,1,0);}
     IF($tri1=='Si')
       {
       $pdf->Cell(12,5,$curso->nota1,1,0,'C');
       $pdf->Cell(8,5,$curso->con1,1,0,'C');
       }
    else
       {
       $pdf->Cell(12,5,'',1,0,'C');
       $pdf->Cell(8,5,'',1,0,'C');
       }
     IF($tri2=='Si')
       {
       $pdf->Cell(12,5,$curso->nota2,1,0,'C');
       $pdf->Cell(8,5,$curso->con2,1,0,'C');
       }
    else
       {
       $pdf->Cell(12,5,'',1,0,'C');
       $pdf->Cell(8,5,'',1,0,'C');
       }
     $msf=0;
     $msf1=0;
     IF($sem1=='Si')
       {
       if ($curso->nota1 > 0 or $curso->nota2 > 0)
          {
          $msf1=$msf1+1;
          $msf=$msf+$curso->sem1;
          $pdf->Cell(16,5,$curso->sem1,1,0,'C');
          }
       else
          {
          $pdf->Cell(16,5,'',1,0,'C');
          }
       }
     ELSE
       {$pdf->Cell(16,5,'',1,0,'C');}
    
     IF($tri3=='Si')
       {
       $pdf->Cell(12,5,$curso->nota3,1,0,'C');
       $pdf->Cell(8,5,$curso->con3,1,0,'C');
       }
    else
       {
       $pdf->Cell(12,5,'',1,0,'C');
       $pdf->Cell(8,5,'',1,0,'C');
       }
     IF($tri4=='Si')
       {
       $pdf->Cell(12,5,$curso->nota4,1,0,'C');
       $pdf->Cell(8,5,$curso->con4,1,0,'C');
       }
    else
       {
       $pdf->Cell(12,5,'',1,0,'C');
       $pdf->Cell(8,5,'',1,0,'C');
       }
     IF($sem2=='Si')
       {
       if ($curso->nota3 > 0 or $curso->nota4 > 0)
          {
          $msf1=$msf1+1;
          $msf=$msf+$curso->sem2;
          $pdf->Cell(16,5,$curso->sem2,1,0,'C');
          }
       else
          {
          $pdf->Cell(16,5,'',1,0,'C');
          }
       }
     ELSE
       {$pdf->Cell(16,5,'',1,0,'C');}
     IF($prof=='Si' and $curso->sem2 > 0 or $prof=='Si' and $msf1 > 0)
       {
       $msf2=round($msf/$msf1,0);
       $cr7=$cr7+$curso->credito;
       $notas7=$notas7+($msf2*$curso->credito);
       $pdf->Cell(14,5,$msf2,1,0,'C');}
     ELSE
       {$pdf->Cell(14,5,'',1,0,'C');}

     $cr1='';
     IF($ccr=='Si'){$cr1=$curso->credito;}
     $pdf->Cell(10,5,$cr1,1,1,'R');
     $pdf->Cell(1,5,'  ',0,0,'R');
     $pdf->Cell(56,5,'     '.$curso->profesor,1,0);
     $pdf->Cell(12,5,'',1,0,'C');
     $pdf->Cell(8,5,'',1,0,'C');
     $pdf->Cell(12,5,'',1,0,'C');
     $pdf->Cell(8,5,'',1,0,'C');
     $pdf->Cell(16,5,'',1,0,'C');
    
     $pdf->Cell(12,5,'',1,0,'C');
     $pdf->Cell(8,5,'',1,0,'C');
     $pdf->Cell(12,5,'',1,0,'C');
     $pdf->Cell(8,5,'',1,0,'C');
     $pdf->Cell(16,5,'',1,0,'R');
     $pdf->Cell(14,5,'',1,0,'R');
     $pdf->Cell(10,5,'',1,1,'R');
  }

     $pdf->Cell(1,5,'  ',0,0,'R');
     $pdf->Cell(56,5,$pq,1,0,'R',true);
     IF($cr > 100){
     $pdf->Cell(12,5,round($notas/$cr,0),1,0,'C',true);}ELSE{
     $pdf->Cell(12,5,'',1,0,'C',true);}
     $pdf->Cell(8,5,'',1,0,'C',true);
     IF($cr2 > 100){
     $pdf->Cell(12,5,round($notas2/$cr2,0),1,0,'C',true);}ELSE{
     $pdf->Cell(12,5,'',1,0,'C',true);}
     $pdf->Cell(8,5,'',1,0,'C',true);
     $notas7=0;$cr7=0;
     IF($sem1=='Si' AND $cr5 > 0)
       {
       $notas7=$notas7+round($notas5/$cr5,0);$cr7=$cr7+1;
       $pdf->Cell(16,5,round($notas5/$cr5,0),1,0,'C',true);
       }ELSE
       {$pdf->Cell(16,5,'',1,0,'C',true);}

     IF($cr3 > 100){
     $pdf->Cell(12,5,round($notas3/$cr3,0),1,0,'C',true);}ELSE{
     $pdf->Cell(12,5,'',1,0,'C',true);}
     $pdf->Cell(8,5,'',1,0,'C',true);
     IF($cr4 > 100){
     $pdf->Cell(12,5,round($notas4/$cr4,0),1,0,'C',true);}ELSE{
     $pdf->Cell(12,5,'',1,0,'C',true);}
     $pdf->Cell(8,5,'',1,0,'C',true);
     IF($sem2=='Si' AND $cr6 > 0)
       {
       $notas7=$notas7+round($notas6/$cr6,0);$cr7=$cr7+1;
       $pdf->Cell(16,5,round($notas6/$cr6,0),1,0,'C',true);}ELSE
       {$pdf->Cell(16,5,'',1,0,'C',true);}

     IF($prof=='Si' AND $cr7 > 0)
       {
       $cr8=0;
       $pdf->Cell(14,5,round($notas7/$cr7,0),1,0,'C',true);
       }ELSE
       {$pdf->Cell(14,5,'',1,0,'C',true);}

     IF($ccr=='Si'){
     $pdf->Cell(10,5,number_format($cr88, 2, '.', ''),1,1,'R',true);
     }ELSE{
     $pdf->Cell(10,5,'',1,1,'R',true);
     }
     
     $pdf->Cell(1,5,'  ',0,0,'R',true);
     $pdf->Cell(56,5,$asi,1,0,'R',true);
     $result7 = DB::table('asispp')->where([
            ['ss', $estu->ss],
            ['year', $year],
        ])->get();

foreach ($result7 as $row7) {
            if ($row7->codigo == 1 and $row7->fecha >= $colegio->asis1 and $row7->fecha <= $colegio->asis2) {
                $au = $au + 1;
            }
            if ($row7->codigo > 1 and $row7->fecha >= $colegio->asis1 and $row7->fecha <= $colegio->asis2) {
                $ta = $ta + 1;
            }
            if ($row7->codigo == 1 and $row7->fecha >= $colegio->asis3 and $row7->fecha <= $colegio->asis4) {
                $au2 = $au2 + 1;
            }
            if ($row7->codigo > 1 and $row7->fecha >= $colegio->asis3 and $row7->fecha <= $colegio->asis4) {
                $au2 = $au2 + 1;
            }

            if ($row7->codigo == 1 and $row7->fecha >= $colegio->asis5 and $row7->fecha <= $colegio->asis6) {
                $au3 = $au3 + 1;
            }
            if ($row7->codigo > 1 and $row7->fecha >= $colegio->asis5 and $row7->fecha <= $colegio->asis6) {
                $ta3 = $ta3 + 1;
            }

            if ($row7->codigo == 1 and $row7->fecha >= $colegio->asis7 and $row7->fecha <= $colegio->asis8) {
                $au4 = $au4 + 1;
            }
            if ($row7->codigo > 1 and $row7->fecha >= $colegio->asis7 and $row7->fecha <= $colegio->asis8) {
                $ta4 = $ta4 + 1;
            }
  
  }

     $pdf->Cell(12,5,$au.'  /  '.$ta,1,0,'C',true);
     $pdf->Cell(8,5,'',1,0,'C',true);
     $pdf->Cell(12,5,$au2.'  /  '.$ta2,1,0,'C',true);
     $pdf->Cell(8,5,'',1,0,'C',true);
     $pdf->Cell(16,5,'',1,0,'C',true);
    
     $pdf->Cell(12,5,$au3.'  /  '.$ta3,1,0,'C',true);
     $pdf->Cell(8,5,'',1,0,'C',true);
     $pdf->Cell(12,5,$au4.'  /  '.$ta4,1,0,'C',true);
     $pdf->Cell(8,5,'',1,0,'C',true);
     $pdf->Cell(16,5,'',1,0,'R',true);
     $pdf->Cell(14,5,'',1,0,'R',true);
     $pdf->Cell(10,5,'',1,1,'R',true);
  
  $pdf->Cell(1,10,'',0,0,'R');
  $pdf->Cell(192,10,'',1,1,'L');
  $pdf->Cell(1,-15,'',0,0,'R');
  $pdf->Cell(192,-15,$text1,0,1,'C');
  $pdf->Cell(1,23,'',0,0,'R');
  $pdf->Cell(192,23,$text2,0,1,'C');
}

$pdf->Output();
?>