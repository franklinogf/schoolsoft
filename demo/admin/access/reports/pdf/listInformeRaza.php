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
    ["Lista de totales por grado", "List of totals by grade"],
    ['Maestro(a)', 'Teacher'],
    ['Nuevos', 'New'],
    ['Grado', 'Grade'],
    ['Masculinos', 'Males'],
    ['Femeninas', 'Females'],
    ['Total de estudiantes', 'Total students'],
    ['Sin G&#65533;neros', 'Without genders'],

]);

$school = new School(Session::id());
$year = $school->info('year2');


class nPDF extends PDF
{
function Header()
{
    global $year;
    parent::header();
   
	$this->Cell(80);
	$this->SetFont('Arial','B',11);
    $this->Cell(30,10,'LISTA TOTALES POR RELIGION '.$year,0,1,'C');
	$this->Ln(6);
    
	$this->Cell(10,5,'',1,0,'C',true);
	$this->Cell(22,5,'GRADO',1,0,'C',true);
   	$this->Cell(18,5,'Adve',1,0,'C',true);
    $this->Cell(18,5,'Baut',1,0,'C',true);
    $this->Cell(18,5,utf8_encode('Cató'),1,0,'C',true);
    $this->Cell(18,5,'Evan',1,0,'C',true);
    $this->Cell(18,5,'Mita',1,0,'C',true);
    $this->Cell(18,5,'Meto',1,0,'C',true);
    $this->Cell(18,5,'Pent',1,0,'C',true);
    $this->Cell(18,5,'Lute',1,0,'C',true);
    $this->Cell(15,5,'NADA',1,1,'C',true);
}

function Footer()
{

    $this->SetY(-15);
    $this->SetFont('Arial','I',8);
    $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}'.' / '.date('m-d-Y'),0,0,'C');
}
}

$pdf = new nPDF();
$pdf->AliasNbPages();
$pdf->SetTitle($lang->translation("Proyecciones por grado") . " $year", true);
$pdf->Fill();
$pdf->AddPage();

$pdf->SetFont('Times','',11);

$grados= DB::table('year')->select("DISTINCT grado")->where([
    ['grado', '!=', '99-99'],
    ['year', $year]
])->orderBy('grado')->get();
$x=0;
foreach ($grados as $grado) {
      $a1=0; $a2=0; $a3=0; $a4=0; $a5=0; $a6=0; $a7=0; $a8=0; $a9=0;
      $resultad1 = DB::table('year')->where([
       ['grado', $grado->grado],
       ['year', $year]
     ])->orderBy('apellidos')->get();

    foreach ($resultad1 as $row7) {
            if ($row7->rel==1){$a1=$a1+1;}
            if ($row7->rel==2){$a2=$a2+1;}
            if ($row7->rel==3){$a3=$a3+1;}
            if ($row7->rel==4){$a4=$a4+1;}
            if ($row7->rel==5){$a5=$a5+1;}
            if ($row7->rel==6){$a6=$a6+1;}
            if ($row7->rel==7){$a7=$a7+1;}
            if ($row7->rel==8){$a8=$a8+1;}
            if ($row7->rel==0){$a9=$a9+1;}
            }
   $x=$x+1;
	 
  $pdf->Cell(10,5,$x,0,0,'R');
  $pdf->Cell(22,5,$grado->grado,0,0,'C');
  $pdf->Cell(18,5,$a1,0,0,'C');
  $pdf->Cell(18,5,$a2,0,0,'C');
  $pdf->Cell(18,5,$a3,0,0,'C');
  $pdf->Cell(18,5,$a4,0,0,'C');
  $pdf->Cell(18,5,$a5,0,0,'C');
  $pdf->Cell(18,5,$a6,0,0,'C');
  $pdf->Cell(18,5,$a7,0,0,'C');
  $pdf->Cell(18,5,$a8,0,0,'C');
  $pdf->Cell(15,5,$a9,0,1,'C');
	
      }

$pdf->Output();

