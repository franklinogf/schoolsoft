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
    list($y1, $y2) = explode("-",$year);
    $this->Cell(30,5,utf8_encode('AÑO ESCOLAR 20').$y1.'-20'.$y2,0,1,'C');
    $this->Ln(5);
}

//Pie de pgina
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
  $qq1 =' 1T     C1   2T    C2   S-1';
  $qq2 =' 3T     C3   4T    C4   S-2';
  $pq='PROMEDIO';
  $asi='AUSENCIAS Y TARDANZAS';
  }

$colegio = DB::table('colegio')->where([
    ['usuario', 'administrador']
])->orderBy('id')->first();
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
     $pdf->SetFont('Times','',11);
     $pdf->Cell(10,5,'',0,1,'L');
     $pdf->SetFont('Times','B',12);
     list($ss1, $ss2, $ss3) = explode("-",$estu->ss);
     $pdf->Cell(90,5,' Nombre del Estudiante ',1,0,'C',true);
     $pdf->Cell(58,5,'Profesor(a)',1,0,'C',true);
     $pdf->Cell(40,5,'Grado',1,1,'C',true);
     $pdf->SetFont('Times','',11);
     $pdf->Cell(90,5,$estu->apellidos.' '.$estu->nombre,1,0,'C');
     $pdf->Cell(58,5,$teacher->apellidos.' '.$teacher->nombre,1,0,'C');
     $pdf->Cell(40,5,$estu->grado,1,1,'C');
     $pdf->Cell(31,5,' ',0,1,'C');
     $pdf->Cell(31,5,' ',0,1,'C');
     $pdf->Cell(69,5,'Materias',1,0,'C',true);
     $pdf->Cell(55,5,'Maestros',1,0,'C',true);
     $pdf->Cell(48,5,'Promedio Acumulado (%)',1,0,'C',true);
     $pdf->Cell(18,5,'Asistencia',1,1,'C',true);
     $pdf->Cell(14,5,utf8_encode('Código'),1,0,'C');
     $pdf->Cell(55,5,utf8_encode('Descripción'),1,0,'C');
     $pdf->Cell(55,5,'',1,0,'C');
     $pdf->Cell(8,5,'10S',1,0,'C');
     $pdf->Cell(8,5,'20S',1,0,'C');
     $pdf->Cell(8,5,'30S',1,0,'C');
     $pdf->Cell(8,5,'40S',1,0,'C');
     $pdf->Cell(7,5,'NF',1,0,'C');
     $pdf->Cell(9,5,'CRT',1,0,'C');
     $pdf->Cell(9,5,'Aus',1,0,'C');
     $pdf->Cell(9,5,'Tar',1,1,'C');
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
 $notas8=0;
 $cr8=0; 
 $notas9=0;
 $cr9=0; 

foreach ($cursos as $curso) {
  $V5 = 0;
  $V6 = 0;
  $tot1t = "";
  $v7 = 0;
  $v8 = 0;
  $tot1t1 = "";
  $notas8=0;
  $cr8=0; 
  $au=0; 
  $ta=0;

  IF (is_numeric($curso->nota1) AND $curso->credito > 0 OR $curso->nota1 == '0' AND $curso->credito > 0)
     {
     $cr=$cr+$curso->credito;
     $notas=$notas+$curso->nota1*$curso->credito;
     $cr8=$cr8+1;
     $notas8=$notas8+$curso->nota1;
     }

  IF (is_numeric($curso->nota2) AND $curso->credito > 0 OR $curso->nota2 == '0' AND $curso->credito > 0 )
     {
     $cr2=$cr2+$curso->credito;
     $notas2=$notas2+$curso->nota2*$curso->credito;
     $cr8=$cr8+1;
     $notas8=$notas8+$curso->nota2;
     }
     
  IF (is_numeric($curso->nota3) AND $curso->credito > 0 OR $curso->nota3 == '0' AND $curso->credito > 0 )
     {
     $cr3=$cr3+$curso->credito;
     $notas3=$notas3+$curso->nota3*$curso->credito;
     $cr8=$cr8+1;
     $notas8=$notas8+$curso->nota3;
     }

  IF (is_numeric($curso->nota4) AND $curso->credito > 0 OR $curso->nota4 == '0'AND $curso->credito > 0)
     {
     $cr4=$cr4+$curso->credito;
     $notas4=$notas4+$curso->nota4*$curso->credito;
     $cr8=$cr8+1;
     $notas8=$notas8+$curso->nota4;
     }

  IF ($cr8 > 0)
     {
     $cr9=$cr9+1;
     $notas9=$notas9+round($notas8/$cr8,0);
     }

  IF (is_numeric($curso->sem1) AND $curso->credito > 0 )
     {
     $cr5=$cr5+$curso->credito;
     $notas5=$notas5+$curso->sem1*$curso->credito;
     }
  IF (is_numeric($curso->sem2) AND $curso->credito > 0 )
     {
     $cr6=$cr6+$curso->credito;
     $notas6=$notas6+$curso->sem2*$curso->credito;
     }
  IF (is_numeric($curso->final) AND $curso->credito > 0 )
     {
     $notas7=$notas7+$curso->final*$curso->credito;
     }
     $cr7=$cr7+$curso->credito;
            if ($curso->aus1 > 0) {
                $au = $au + number_format($curso->aus1, 0);
            }
            if ($curso->tar1 > 0) {
                $ta = $ta + $curso->tar1;
            }
            if ($curso->aus2 > 0) {
                $au = $au + number_format($curso->aus2, 0);
            }
            if ($curso->tar2 > 0) {
                $ta = $ta + $curso->tar2;
            }
            if ($curso->aus3 > 0) {
                $au = $au + number_format($curso->aus3, 0);
            }
            if ($curso->tar3 > 0) {
                $ta = $ta + $curso->tar3;
            }
            if ($curso->aus4 > 0) {
                $au = $au + number_format($curso->aus4, 0);
            }
            if ($curso->tar4 > 0) {
                $ta = $ta + $curso->tar4;
            }

     $pdf->SetFont('Times','',9);
     $pdf->Cell(14,5,$curso->curso,1,0,'C');
     IF($idi=='Ingles')
       {$pdf->Cell(55,5,$curso->desc2,1,0);}
     ELSE
       {$pdf->Cell(55,5,$curso->descripcion,1,0);}
     $pdf->Cell(55,5,$curso->profesor,1,0,'L');

     $nnt2='';
     IF($tri1=='Si'){$nnt2=$curso->nota1;}
     $pdf->Cell(8,5,$nnt2,1,0,'C');

     $nn1='';
     IF ($curso->nota1 == '0'){$nn1='F';}
     IF ($curso->nota1 == 'P' OR $curso->nota1 == 'p' OR $curso->nota1 == 'INC' OR $curso->nota1 == 'inc'){$nn1='';}
     $nnt2='';
     IF($tri1=='Si'){$nnt2=NLetra($curso->nota1);}
     $nnt2='';
     IF($tri2=='Si'){$nnt2=$curso->nota2;}
     $pdf->Cell(8,5,$nnt2,1,0,'C');

     $nn2='';
     IF ($curso->nota2 == '0'){$nn2='F';}
     IF ($curso->nota2 == 'P' OR $curso->nota2 == 'p' OR $curso->nota2 == 'INC' OR $curso->nota2 == 'inc'){$nn2='';}
     $nnt2='';
     IF($tri2=='Si'){$nnt2=NLetra($curso->nota2);}
     $nnt2='';
     IF($tri3=='Si'){$nnt2=$curso->nota3;}
     $pdf->Cell(8,5,$nnt2,1,0,'C');
     $nn1='';
     IF ($curso->nota3 == '0'){$nn1='F';}
     IF ($curso->nota3 == 'P' OR $curso->nota3 == 'p'){$nn1='';}
     $nnt2='';
     IF($tri3=='Si'){$nnt2=NLetra($curso->nota3);}
     $nnt2='';
     IF($tri4=='Si'){$nnt2=$curso->nota4;}
     $pdf->Cell(8,5,$nnt2,1,0,'C');
     $nn1='';
     IF ($curso->nota4 == '0'){$nn1='F';}
     IF ($curso->nota4 == 'P' OR $curso->nota4 == 'p'){$nn1='';}
     $nnt2='';
     IF($tri4=='Si'){$nnt2=NLetra($curso->nota4);}
     $pdf->Cell(7,5,$nnt2,1,0,'C');
     $nnta22='';
     $nnf1='';
     IF ($nnta22 == '0'){$nnf1='F';}
     IF ($nnta22 == 'P' OR $nnta22 == 'p'){$nnf1='';}
     $pdf->Cell(9,5,$curso->credito,1,0,'R');
     $pdf->Cell(9,5,$au,1,0,'C');
     $pdf->Cell(9,5,$ta,1,1,'C');
  }
     $pdf->Cell(15,5,'',0,1,'C');
     $pg='';
     $pdf->Cell(85,5,'',0,0,'R');
     $pdf->Cell(39,5,'Promedio General: ',1,0,'R',true);
     if ($cr > 0 and $tri1=='Si')
        {
        $pdf->Cell(8,5,number_format(round($notas/$cr,0), 0),1,0,'C',true);
        }
     else
        {
        $pdf->Cell(8,5,'',1,0,'C',true);
        }
     if ($cr2 > 0 and $tri2=='Si')
        {
        $pdf->Cell(8,5,number_format(round($notas2/$cr2,0), 0),1,0,'C',true);
        }
     else
        {
        $pdf->Cell(8,5,'',1,0,'C',true);
        }
     if ($cr3 > 0 and $tri3=='Si')
        {
        $pdf->Cell(8,5,number_format(round($notas3/$cr3,0), 0),1,0,'C',true);
        }
     else
        {
        $pdf->Cell(8,5,'',1,0,'C',true);
        }
     if ($cr4 > 0 and $tri4=='Si')
        {
        $pdf->Cell(8,5,number_format(round($notas4/$cr4,0), 0),1,0,'C',true);
        }
     else
        {
        $pdf->Cell(8,5,'',1,0,'C',true);
        }
     $pdf->Cell(7,5,'',1,0,'C',true);
     $pdf->Cell(9,5,number_format($cr7,2),1,1,'R',true);
     $pdf->Cell(20,25,'',0,1,'C');
     $pdf->Cell(190,5,utf8_encode('Favor de examinar cuidadosamente cada una de las partes de este informe. De tener alguna duda o pregunta, favor referirse al maestro de Salón'),'LRT',1,'L');
     $pdf->Cell(190,5,utf8_encode('Hogar para la orientación correspondiente. Borrones o tachaduras invalidan este informe de notas y no es oficial sin el sello en original del colegio.'),'LRB',1,'L');
     $pdf->Cell(20,25,'',0,1,'C');
     $pdf->Cell(70,5,utf8_encode('Firma del Maestro de Salón Hogar'),'T',1,'L');
     $pdf->Cell(20,10,'',0,1,'C');
     $pdf->Cell(70,5,'Firma del Director','T',1,'L');
     $pdf->SetFont('Times','',10);
     $pdf->Cell(20,10,'',0,1,'C');
     $pdf->Cell(20,5,'Fecha del Reporte: '.date('m/d/Y'),0,1,'L');
     $pdf->Ln(-40);
     $pdf->Cell(120,5,'',0,0,'C');
     $pdf->Cell(70,5,'Escala de Porcientos',1,1,'C',true);
     $pdf->Cell(120,5,'',0,0,'C');
     $pdf->Cell(25,5,'(%)',1,0,'C',true);
     $pdf->Cell(31,5,utf8_encode('Clasificación'),1,0,'C',true);
     $pdf->Cell(14,5,'Nota',1,1,'C',true);
     $pdf->Cell(120,5,'',0,0,'C');
     $pdf->Cell(25,5,'100% a 90%',1,0,'C');
     $pdf->Cell(31,5,'Excelente',1,0,'C');
     $pdf->Cell(14,5,'A',1,1,'C');
     $pdf->Cell(120,5,'',0,0,'C');
     $pdf->Cell(25,5,'89% a 80%',1,0,'C');
     $pdf->Cell(31,5,'Bueno',1,0,'C');
     $pdf->Cell(14,5,'B',1,1,'C');
     $pdf->Cell(120,5,'',0,0,'C');
     $pdf->Cell(25,5,'79% a 70%',1,0,'C');
     $pdf->Cell(31,5,'Satisfactorio',1,0,'C');
     $pdf->Cell(14,5,'C',1,1,'C');
     $pdf->Cell(120,5,'',0,0,'C');
     $pdf->Cell(25,5,'69% a 60%',1,0,'C');
     $pdf->Cell(31,5,'Deficiente',1,0,'C');
     $pdf->Cell(14,5,'D',1,1,'C');
     $pdf->Cell(120,5,'',0,0,'C');
     $pdf->Cell(25,5,'59% a  0%',1,0,'C');
     $pdf->Cell(31,5,'Fracaso',1,0,'C');
     $pdf->Cell(14,5,'F',1,1,'C');

//Favor de examinar cuidadosamente cada una de las partes de este informe.
//De tener alguna duda o pregunta, favor referirse al maestro de Sal&#65533;n Hogar
//para la orientaci&#65533;n correspondiente. Borrones o tachaduras invalidan este 
//informe de notas y no es oficial sin el sello en original del colegio.
     $pdf->Cell(5,20,'',0,1,'R');
     $pdf->Cell(190,10,'Sello Oficial',0,1,'C');
     $l1=0;
}

$pdf->Output();
?>