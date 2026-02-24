<?php
require_once '../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ['PRESUPUESTO AÑO', 'YEAR BUDGET LIST'],
    ['NOMBRE', 'NAME'],
    ['CTA', 'ACCT'],
    ['PAGOS', 'PAYS'],
    ['FECHA P.', 'PAY DAY'],
    ['T. PAGO', 'TIPE PAY'],
    ['DESDE', 'FROM'],
    ['HASTA', 'TO'],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
]);

$school = new School(Session::id());
$year = $school->info('year2');

class nPDF extends PDF
{
    public function Header()
    {
        global $colegio;
        global $year;
        parent::header();
        $this->Ln(1);
        $this->SetFont('Arial', 'B', 12);
	$sp=120;
	$this->Ln(-5);
	$this->Cell($sp);
	$this->SetFont('Arial','B',11);
	$this->Cell(30,10,utf8_encode('PRESUPUESTO AÑO ').$year,0,1,'C');
	$this->Ln(5);
	$this->Cell(30,5,'Matricula',1,0,'C',true);
	$this->Cell(18,5,'Cant.',1,0,'C',true);
	$this->Cell(30,5,'Matr. 1er.',1,0,'C',true);
	$this->Cell(30,5,'Matr. 2do.',1,0,'C',true);
	$this->Cell(30,5,'Men. 1er.',1,0,'C',true);
	$this->Cell(30,5,'Men. 2do.',1,0,'C',true);
	$this->Cell(50,5,'Beca deporte 50%',1,0,'C',true);
	$this->Cell(50,5,'Beca deporte 25%',1,1,'C',true);

	$this->Cell(30,5,'Elemental',1,0,'C',true);
	$this->Cell(18,5,'',1,0,'C',true);
	$this->Cell(30,5,'Hijo 900.00',1,0,'C',true);
	$this->Cell(30,5,'Hijo 775.00',1,0,'C',true);
	$this->Cell(30,5,'Hijo 280.00',1,0,'C',true);
	$this->Cell(30,5,'Hijo 270.00',1,0,'C',true);
	$this->Cell(25,5,'Matr 307.50 ',1,0,'C',true);
	$this->Cell(25,5,'Men 95.00 ',1,0,'C',true);
	$this->Cell(25,5,'Matr 461.25 ',1,0,'C',true);
	$this->Cell(25,5,'Men 142.50 ',1,1,'C',true);
}
function Footer()
{
$this->SetY(-15);
$this->SetFont('Arial','I',8);
$this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}'.' / '.date('m-d-Y'),0,0,'C');

}
}

$pdf = new nPDF();
$pdf->SetTitle(utf8_encode($lang->translation('PRESUPUESTO AÑO')) . ' ' . $year);
$pdf->Fill();

$pdf->AliasNbPages();
$pdf->AddPage('L');
$pdf->SetFont('Times','',11);

$gdo = array("PP","PK","KG","01-", "02-", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
$gd2 = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

$xa1=0;$xa2=0;$xa3=0;$xa4=0;$xa5=0;$xa6=0;$xa7=0;$xa8=0;
$xb1=0;$xb2=0;$xb3=0;$xb4=0;$xb5=0;$xb6=0;$xb7=0;$xb8=0;

for ($i = 0; $i <= 14; $i++) 
    {
    $x1=0;$x2=0;$x3=0;$x4=0;$x5=0;$x6=0;$x7=0;$x8=0;
    $tabla1 = DB::table('year')
        ->whereRaw("activo='' and grado LIKE '%".$gdo[$i]."%' AND year ='$year'")->orderBy('grado')->get();
    $num_res = count($tabla1);

  foreach ($tabla1 as $row7) {
          if ($row7->desc_men==0 and $row7->desc_mat==0 and $row7->desc_otro1==0 and $row7->desc_otro2==0){$x1=$x1+1;}
          for ($a = 1; $a <= 4; $a++)
              {
$d='';
if ($a==1){$d='men';}
if ($a==2){$d='mat';}
if ($a==3){$d='otro1';}
if ($a==4){$d='otro2';}

              
              if ($i < 9)
                 {
                 // elemental
                
                 if ($row7->{"desc_$d"}==125.00){$x2=$x2+1;}
                 if ($row7->{"desc_$d"}== 10.00){$x3=$x3+1;}
                 if ($row7->{"desc_$d"}==307.50){$x5=$x5+1;}
                 if ($row7->{"desc_$d"}== 95.00){$x6=$x6+1;}
                 if ($row7->{"desc_$d"}==153.75){$x7=$x7+1;}
                 if ($row7->{"desc_$d"}== 47.50){$x8=$x8+1;}
                 }
              
              if ($i > 5 and $i < 11)
                 {
                 // intermedia
//                 if ($row7[$a]==125.00){$x2=$x2+1;}
//                 if ($row7[$a]== 10.00){$x3=$x3+1;}
//                 if ($row7[$a]==337.50){$x5=$x5+1;}
//                 if ($row7[$a]==100.00){$x6=$x6+1;}
//                 if ($row7[$a]==168.75){$x7=$x7+1;}
//                 if ($row7[$a]== 50.00){$x8=$x8+1;}
                 }
              
              if ($i > 8)
                 {
                 //superior
                 if ($row7->{"desc_$d"}==125.00){$x2=$x2+1;}
                 if ($row7->{"desc_$d"}== 75.00){$x2=$x2+1;}
                 if ($row7->{"desc_$d"}== 10.00){$x3=$x3+1;}
                 if ($row7->{"desc_$d"}==357.50){$x5=$x5+1;}
                 if ($row7->{"desc_$d"}==110.00){$x6=$x6+1;}
                 if ($row7->{"desc_$d"}==178.75){$x7=$x7+1;}
                 if ($row7->{"desc_$d"}== 55.00){$x8=$x8+1;}
                 }
              }
          }

$pdf->Cell(30,5,$gdo[$i],1,0,'C');
$pdf->Cell(18,5,$num_res,1,0,'C');
$pdf->Cell(30,5,$x1,1,0,'C');
$pdf->Cell(30,5,$x2,1,0,'C');
$pdf->Cell(30,5,$x1,1,0,'C');
$pdf->Cell(30,5,$x3,1,0,'C');
$pdf->Cell(25,5,$x5,1,0,'C');
$pdf->Cell(25,5,$x6,1,0,'C');
$pdf->Cell(25,5,$x7,1,0,'C');
$pdf->Cell(25,5,$x8,1,1,'C');
$xa4=$xa4+$num_res;
$xa1=$xa1+$x1;
$xa2=$xa2+$x2;
$xa3=$xa3+$x3;
$xa5=$xa5+$x5;
$xa6=$xa6+$x6;
$xa7=$xa7+$x7;
$xa8=$xa8+$x8;

$xb4=$xb4+$num_res;
$xb1=$xb1+$x1;
$xb2=$xb2+$x2;
$xb3=$xb3+$x3;
$xb5=$xb5+$x5;
$xb6=$xb6+$x6;
$xb7=$xb7+$x7;
$xb8=$xb8+$x8;

if ($gdo[$i]=='05')
   {
   $pdf->Cell(30,5,'Total elem',1,0,'C');
   $pdf->Cell(18,5,$xa4,1,0,'C');
   $pdf->Cell(30,5,$xa1,1,0,'C');
   $pdf->Cell(30,5,$xa2,1,0,'C');
   $pdf->Cell(30,5,$xa1,1,0,'C');
   $pdf->Cell(30,5,$xa3,1,0,'C');
   $pdf->Cell(25,5,$xa5,1,0,'C');
   $pdf->Cell(25,5,$xa6,1,0,'C');
   $pdf->Cell(25,5,$xa7,1,0,'C');
   $pdf->Cell(25,5,$xa8,1,1,'C');
   
   $pdf->Cell(30,5,'Elemental',1,0,'C');
   $pdf->Cell(18,5,'',1,0,'C');
   $pdf->Cell(30,5,'950',1,0,'C');
   $pdf->Cell(30,5,'825',1,0,'C');
   $pdf->Cell(30,5,'280',1,0,'C');
   $pdf->Cell(30,5,'270',1,0,'C');
   $pdf->Cell(25,5,'337.50',1,0,'C');
   $pdf->Cell(25,5,'100.00',1,0,'C');
   $pdf->Cell(25,5,'506.25',1,0,'C');
   $pdf->Cell(25,5,'150.00',1,1,'C');
   }

if ($gdo[$i]=='06')
   {

   $pdf->Cell(30,5,'Total elem',1,0,'C');
   $pdf->Cell(18,5,$xa4,1,0,'C');
   $pdf->Cell(30,5,$xa1,1,0,'C');
   $pdf->Cell(30,5,$xa2,1,0,'C');
   $pdf->Cell(30,5,$xa1,1,0,'C');
   $pdf->Cell(30,5,$xa3,1,0,'C');
   $pdf->Cell(25,5,$xa5,1,0,'C');
   $pdf->Cell(25,5,$xa6,1,0,'C');
   $pdf->Cell(25,5,$xa7,1,0,'C');
   $pdf->Cell(25,5,$xa8,1,1,'C');

   $pdf->Cell(30,5,'Intermedia',1,0,'C');
   $pdf->Cell(18,5,'',1,0,'C');
   $pdf->Cell(30,5,'950',1,0,'C');
   $pdf->Cell(30,5,'825',1,0,'C');
   $pdf->Cell(30,5,'300',1,0,'C');
   $pdf->Cell(30,5,'290',1,0,'C');
   $pdf->Cell(25,5,'337.50',1,0,'C');
   $pdf->Cell(25,5,'100.00',1,0,'C');
   $pdf->Cell(25,5,'506.25',1,0,'C');
   $pdf->Cell(25,5,'150.00',1,1,'C');

   $xa1=0;$xa2=0;$xa3=0;$xa4=0;$xa5=0;$xa6=0;$xa7=0;$xa8=0;
   }

if ($gdo[$i]=='09')
   {
   $pdf->Cell(30,5,'Total inter.',1,0,'C');
   $pdf->Cell(18,5,$xa4,1,0,'C');
   $pdf->Cell(30,5,$xa1,1,0,'C');
   $pdf->Cell(30,5,$xa2,1,0,'C');
   $pdf->Cell(30,5,$xa1,1,0,'C');
   $pdf->Cell(30,5,$xa3,1,0,'C');
   $pdf->Cell(25,5,$xa5,1,0,'C');
   $pdf->Cell(25,5,$xa6,1,0,'C');
   $pdf->Cell(25,5,$xa7,1,0,'C');
   $pdf->Cell(25,5,$xa8,1,1,'C');

   $pdf->Cell(30,5,'Superior',1,0,'C');
   $pdf->Cell(18,5,'',1,0,'C');
   $pdf->Cell(30,5,'950',1,0,'C');
   $pdf->Cell(30,5,'825',1,0,'C');
   $pdf->Cell(30,5,'300',1,0,'C');
   $pdf->Cell(30,5,'290',1,0,'C');
   $pdf->Cell(25,5,'357.50',1,0,'C');
   $pdf->Cell(25,5,'110.00',1,0,'C');
   $pdf->Cell(25,5,'536.50',1,0,'C');
   $pdf->Cell(25,5,'165.00',1,1,'C');

   $xa1=0;$xa2=0;$xa3=0;$xa4=0;$xa5=0;$xa6=0;$xa7=0;$xa8=0;
   }


if ($gdo[$i]=='12')
   {
   $pdf->Cell(30,5,'Total Super.',1,0,'C');
   $pdf->Cell(18,5,$xa4,1,0,'C');
   $pdf->Cell(30,5,$xa1,1,0,'C');
   $pdf->Cell(30,5,$xa2,1,0,'C');
   $pdf->Cell(30,5,$xa1,1,0,'C');
   $pdf->Cell(30,5,$xa3,1,0,'C');
   $pdf->Cell(25,5,$xa5,1,0,'C');
   $pdf->Cell(25,5,$xa6,1,0,'C');
   $pdf->Cell(25,5,$xa7,1,0,'C');
   $pdf->Cell(25,5,$xa8,1,1,'C');
   $pdf->Cell(30,5,'Gran Total',1,0,'C');
   $pdf->Cell(18,5,$xb4,1,0,'C');
   $pdf->Cell(30,5,$xb1,1,0,'C');
   $pdf->Cell(30,5,$xb2,1,0,'C');
   $pdf->Cell(30,5,$xb1,1,0,'C');
   $pdf->Cell(30,5,$xb3,1,0,'C');
   $pdf->Cell(25,5,$xb5,1,0,'C');
   $pdf->Cell(25,5,$xb6,1,0,'C');
   $pdf->Cell(25,5,$xb7,1,0,'C');
   $pdf->Cell(25,5,$xb8,1,1,'C');

   $xa1=0;$xa2=0;$xa3=0;$xa4=0;$xa5=0;$xa6=0;$xa7=0;$xa8=0;
   }



}
$pdf->Output();
?>

