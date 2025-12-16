<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;

Session::is_logged();

$lang = new Lang([
    ['Informes Académicos', 'Academic Reports'],
    ['Informe de aprovechamiento académico', 'Academic achievement report'],
    ['Maestro', 'Teacher'],
    ['Semestre', 'Semester'],
    ['Opción', 'Option'],
    ['Continuar', 'Continue'],
    ['Semestre 1', 'Semester 1'],
    ['Semestre 2', 'Semester 2'],
    ['Trimestre 1', 'Quarter 1'],
    ['Trimestre 2', 'Quarter 2'],
    ['Trimestre 3', 'Quarter 3'],
    ['Trimestre 4', 'Quarter 4'],
    ['Notas para Sumar', 'Notes to Add'],
    ['Firmas', 'Signature'],
    ['Grados Separados:', 'Separate grades:'],
    ['Atr&#65533;s', 'Go back'],
    ['Grado', 'Grade'],
    ['Notas para ver:', 'Notes to see:'],
    ['Maestro', 'Maestro'],
    ['Padre/encargado', 'Parent/Guardian'],
    ['Nota porciento', 'Percentage score'],
    ['Promedio final', 'Final average'],
    ['CURSOS A MEJORAR', 'COURSES TO IMPROVE'],
    ['INFORME DE DEFICIENCIA', 'DEFICIENCY REPORT'],
   
]);


$id=$_POST['profe'];
$nota = $_POST['opt'];
$sem = $_POST['sem'];
if ($nota == 1) {
  if ($sem == 1) {
      $campos = "nota1,nota2";
  }else{
      $campos = "nota3,nota4";
  }
}else{
  $campos = "sem$sem";
}
  $campos = $sem. ' AS notas ';

class nPDF extends PDF
{
  function Header()
  {
    parent::header();
  }
}

$school = new School(Session::id());
$year = $school->info('year2');

$colegio = DB::table('colegio')->where([
    ['usuario', 'administrador']
])->orderBy('id')->first();


$profe = DB::table('profesor')->where([
        ['id', $id]
    ])->orderBy('id')->first();


$res = DB::table('padres')->select("DISTINCT curso")->where([
        ['year', $school->info('year2')],
        ['id', $id],
        ['curso', 'NOT LIKE', '%AA-%']
    ])->orderBy('curso')->get();

$cursos = array();
foreach ($res as $row) {
  if ($row->curso != "") {
    $cursos[] = $row->curso;
  }
}
$cursos = json_decode(json_encode($cursos));
$pdf = new PDF();
$pdf->SetAutoPageBreak(true,5);
$pdf->SetFont('Arial','B',14);
$pdf->AddPage('L');
$pdf->Cell(0,5,utf8_encode("Informe de Aprovechamiento Académico"),0,1,'C');
$pdf->Ln(10);
$pdf->SetFont('Arial','',12);
$pdf->Fill();

$pdf->Cell(110);
$pdf->Cell(10,5,($sem == "nota1")?"X":"","B",0,'C');
$pdf->Cell(25,6,"Octubre",0,0);
$pdf->Cell(10,5,($sem == "nota2")?"X":"","B",0,'C');
$pdf->Cell(25,6,"Diciembre",0,0);
$pdf->Cell(10,5,($sem == "sem1")?"X":"","B",0,'C');
$pdf->Cell(55,6,"Agosto - Diciembre",0,1);
$pdf->Cell(110);
$pdf->Cell(10,5,($sem == "nota3")?"X":"","B",0,'C');
$pdf->Cell(25,6,"Marzo",0,0);
$pdf->Cell(10,5,($sem == "nota4")?"X":"","B",0,'C');
$pdf->Cell(25,6,"Mayo",0,0);
$pdf->Cell(10,5,($sem == "sem2")?"X":"","B",0,'C');
$pdf->Cell(55,6,"Enero - Mayo",0,0);
$pdf->Cell(10,5,($sem == "final")?"X":"","B",0,'C');
$pdf->Cell(25,6,"Final",0,1);
$pdf->Ln(5);

$pdf->Cell(20,10,"Grupo",1,0,'C',true);
$pdf->SetFont('Arial','',11);
$pdf->Cell(25,5,"Estudiantes","LTR",0,'C',true);
$pdf->SetFont('Arial','',12);
$pdf->Cell(16,10,"A",1,0,'C',true);
$pdf->Cell(16,10,"%",1,0,'C',true);
$pdf->Cell(16,10,"B",1,0,'C',true);
$pdf->Cell(16,10,"%",1,0,'C',true);
$pdf->Cell(16,10,"C",1,0,'C',true);
$pdf->Cell(16,10,"%",1,0,'C',true);
$pdf->Cell(16,10,"D",1,0,'C',true);
$pdf->Cell(16,10,"%",1,0,'C',true);
$pdf->Cell(16,10,"F",1,0,'C',true);
$pdf->Cell(16,10,"%",1,0,'C',true);
$pdf->Cell(16,5,"Total","LTR",0,'C',true);
$pdf->Cell(16,10,"% ABC",1,0,'C',true);
$pdf->Cell(16,5,"Total","LTR",0,'C',true);
$pdf->Cell(16,10,"% DF",1,0,'C',true);
$pdf->Ln(5);
$pdf->Cell(20);
$pdf->SetFont('Arial','',11);
$pdf->Cell(25,5,"Matriculados","LBR",0,'C',true);
$pdf->SetFont('Arial','',12);
$pdf->Cell(16*10);
$pdf->Cell(16,5,"ABC","LBR",0,'C',true);
$pdf->Cell(16);
$pdf->Cell(16,5,"DF","LBR",1,'C',true);
$A=0;$B=0;$C=0;$D=0;$F=0;$ABC=0;$DF=0;
$CANT = 0;
for ($i=0; $i < sizeof($cursos); $i++) { 
  $a=0;$b=0;$c=0;$d=0;$f=0;$abc=0;$df=0;$w="";
    $camp = $campos;
    $res = DB::table('padres')->select($campos)->where([
      ['id', $id],
      ['year', $year],
      ['curso', $cursos[$i]],
      ['baja', ''],
      ['curso', 'NOT LIKE', '%AA-%']
    ])->orderBy('apellidos, grado')->get();
  $cant = count($res);

foreach ($res as $row) {
    if ($nota == 1) {
        $valor = $row->notas;
        if ($valor >= 3.50 ) {
          $a++;
        }elseif($valor >= 2.50 && $valor < 3.49){
          $b++;
        }elseif($valor >= 1.60 && $valor < 2.49){
          $c++;
        }elseif($valor >= 1.00 && $valor < 1.59){
          $d++;
        }elseif($valor < 1.00 && $valor >= 0.00){
          $f++;
        }
    }else{
          if ($row->notas >= $colegio->vala ) {
            $a++;
          }elseif($row->notas >= $colegio->valb && $row->notas < $colegio->vala){
            $b++;
          }elseif($row->notas >= $colegio->valc && $row->notas < $colegio->valb){
            $c++;
          }elseif($row->notas >= $colegio->vald && $row->notas < $colegio->valc){
            $d++;
          }elseif($row->notas < $colegio->vald && $row->notas >= 0){
            $f++;
          }
    }
  }

  $pdf->Cell(20,7,$cursos[$i],1,0,'C');
  $pdf->SetFont('Arial','B',11);
  $pdf->Cell(25,7,$cant,1,0,'C');
  $pdf->SetFont('Arial','',12);
  $pdf->Cell(16,7,$a,1,0,'C');
  $pdf->Cell(16,7,($cant == 0)?'0%':round(($a/$cant)*100).'%',1,0,'C');
  $pdf->Cell(16,7,$b,1,0,'C');
  $pdf->Cell(16,7,($cant==0)?'0%':round(($b/$cant)*100).'%',1,0,'C');
  $pdf->Cell(16,7,$c,1,0,'C');
  $pdf->Cell(16,7,($cant==0)?'0%':round(($c/$cant)*100).'%',1,0,'C');
  $pdf->Cell(16,7,$d,1,0,'C');
  $pdf->Cell(16,7,($cant==0)?'0%':round(($d/$cant)*100).'%',1,0,'C');
  $pdf->Cell(16,7,$f,1,0,'C');
  $pdf->Cell(16,7,($cant==0)?'0%':round(($f/$cant)*100).'%',1,0,'C');
  $abc = $a+$b+$c;
  $pdf->Cell(16,7,$abc,1,0,'C');
  $pdf->Cell(16,7,($cant==0)?'0%':round(($abc/$cant)*100).'%',1,0,'C');
  $df = $d+$f;
  $pdf->Cell(16,7,$df,1,0,'C');
  $pdf->Cell(16,7,($cant==0)?'0%':round(($df/$cant)*100).'%',1,1,'C');
  $A+=$a;$B+=$b;$C+=$c;$D+=$d;$F+=$f;$ABC+=$abc;$DF+=$df;
  $CANT+=$cant;
  }
  $pdf->SetFont('Arial','B',11);
  $pdf->Cell(20,7,"Totales",1,0,'C');
  $pdf->Cell(25,7,$CANT,1,0,'C');
  $pdf->Cell(16,7,$A,1,0,'C');
  $pdf->Cell(16,7,($cant==0)?'0%':round(($A/$CANT)*100).'%',1,0,'C');
  $pdf->Cell(16,7,$B,1,0,'C');
  $pdf->Cell(16,7,($cant==0)?'0%':round(($B/$CANT)*100).'%',1,0,'C');
  $pdf->Cell(16,7,$C,1,0,'C');
  $pdf->Cell(16,7,($cant==0)?'0%':round(($C/$CANT)*100).'%',1,0,'C');
  $pdf->Cell(16,7,$D,1,0,'C');
  $pdf->Cell(16,7,($cant==0)?'0%':round(($D/$CANT)*100).'%',1,0,'C');
  $pdf->Cell(16,7,$F,1,0,'C');
  $pdf->Cell(16,7,($cant==0)?'0%':round(($F/$CANT)*100).'%',1,0,'C');
  $ABC = $A+$B+$C;
  $pdf->Cell(16,7,$ABC,1,0,'C');
  $pdf->Cell(16,7,($cant==0)?'0%':round(($ABC/$CANT)*100).'%',1,0,'C');
  $DF = $D+$F;
  $pdf->Cell(16,7,$DF,1,0,'C');
  $pdf->Cell(16,7,($cant==0)?'0%':round(($DF/$CANT)*100).'%',1,1,'C');

$pdf->Ln(8);
$pdf->Cell(50,5,"Nombre maestro(a):");
$pdf->Cell(80,5,"$profe->nombre $profe->apellidos","B",1);
$pdf->Ln(8);
$pdf->Cell(20,5,"Firma:");
$pdf->Cell(80,5,"","B");
$pdf->Cell(50);
$fecha = array("","enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
$pdf->Cell(20,5,"Fecha:");
$pdf->Cell(80,5,date("d") . " " . $fecha[intval(date("m"))] . " " . date("Y"),"B");

$pdf->Output();
?>
