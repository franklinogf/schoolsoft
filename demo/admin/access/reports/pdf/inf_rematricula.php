<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Util;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();

$lang = new Lang([
    ["LISTA DE REMATRICULA ", "RE-ENROLLMENT LIST "],
    ['Maestro(a)', 'Teacher'],
    ['Nuevos', 'New'],
    ['Grado', 'Grade'],
    ['Masculinos', 'Males'],
    ['Femeninas', 'Females'],
    ['Total de estudiantes', 'Total students'],

]);

$school = new School(Session::id());
$year = $school->info('year2');

list($y1, $y2) = explode("-",$year);

$year2 = '20'.$y2.'-20'.$y2+1;

class nPDF extends PDF
{
  function Header()
  {
    global $year2;
    parent::header();
    $this->SetFont('Arial','B',12);
    $this->Cell(50);
    $this->Cell(100,10,'LISTA DE REMATRICULA '.$year2,0,1,'C');
	$this->Ln(5);
	$this->SetFont('Arial','B',10);
	$this->Cell(4,5,'    ',0,0,'C',true);
	$this->Cell(10,5,'CTA',1,0,'C',true);
	$this->Cell(40,5,'Apellidos',1,0,'C',true);
	$this->Cell(31,5,'Nombre',1,0,'C',true);
	$this->Cell(12,5,'Grado',1,0,'C',true);
	$this->Cell(12,5,'Deuda',1,0,'C',true);
	$this->Cell(11,5,'60%',1,0,'C',true);
	$this->Cell(16,5,'Fecha',1,0,'C',true);
  	$this->Cell(11,5,'40%',1,0,'C',true);
	$this->Cell(16,5,'Fecha',1,0,'C',true);
  	$this->Cell(11,5,'Total',1,0,'C',true);
	$this->Cell(5,5,'PC',1,0,'C',true);
	$this->Cell(16,5,'Fecha',1,0,'C',true);
    $this->Ln(5);
}

function Footer()
{
    $this->SetY(-15);
    $this->SetFont('Arial','I',8);
    $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
}
}


$pdf = new nPDF();
$pdf->useFooter(false);
$pdf->SetTitle($lang->translation("LISTA DE REMATRICULA ") . " $year2", true);
$pdf->Fill();

$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',9);

$students = DB::table('year')->where([
       ['year', $year],
       ['pago', 'OK'],
       ['pm1_pago', 'OK'],
       ])->orderBy('datem')->get();
$a=0;
$b1=0;
$b2=0;
$b3=0;
$b4=0;
foreach ($students as $row) {
  $a=$a+1;
  $pdf->SetFont('Times','',11);
  $pdf->Cell(4,5,$a,0,0,'R');
  $pdf->Cell(10,5,$row->id,0,0);
  $pdf->Cell(40,5,$row->apellidos,0,0);
  $pdf->Cell(31,5,$row->nombre,0,0);
  $pdf->Cell(12,5,$row->grado,0,0,'C');
  $pdf->Cell(12,5,$row->tmat,0,0,'R');
  $p1='';
  $p2='';
  $p=0;
  if ($row->pm1_pago=='OK'){$p1=$row->pm1;$p2=$row->pm1_fecha;$b1=$b1+$row['pm1'];$p=$p+$row->pm1;}
  $pdf->Cell(11,5,$p1,0,0,'R');
  $pdf->Cell(16,5,$p2,0,0,'R');
  $p1='';
  $p2='';
  if ($row->pm2_pago=='OK'){$p1=$row->pm2;$p2=$row->pm2_fecha;$b2=$b2+$row['pm2'];$p=$p+$row->pm2;}
  $pdf->Cell(11,5,$p1,0,0,'R');
  $pdf->Cell(16,5,$p2,0,0,'R');
  $p3='';
  $pc='No';
  $pdf->Cell(12,5,number_format($p,2),0,0,'R');
  if ($row->tmat == $p){$pc='Si';}
  $pdf->Cell(5,5,$pc,0,0,'R');

  $pdf->Cell(16,5,$row->datem,0,1,'C');
  }

$pdf->Cell(15,5,'',0,1,'C');
$pdf->SetFont('Times','B',11);
$pdf->Cell(50,5,'Totales',1,0,'C',true);
$pdf->Cell(20,5,'60%',1,0,'C',true);
$pdf->Cell(20,5,'40%',1,0,'C',true);
$pdf->Cell(20,5,'Total',1,1,'C',true);
$pdf->SetFont('Times','',11);

$pdf->Cell(50,5,'',0,0,'R');
$pdf->Cell(20,5,number_format($b1,2),0,0,'R');
$pdf->Cell(20,5,number_format($b2,2),0,0,'R');
$pdf->Cell(20,5,number_format($b1+$b2,2),0,1,'R');

$pdf->Output();
?>