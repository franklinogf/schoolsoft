<?
include('../control.php');
require('../fpdf16/fpdf.php');



//require('../../fpdf16/fpdf.php');
class PDF extends FPDF
{
function Header()
{
session_start();
$id=$_SESSION['id1'];
$usua=$_SESSION['usua1'];
    include('../control.php');
    $dat1 = "select * from colegio where usuario = '$usua'";
    $tab1 = mysql_query($dat1, $con) or die ("problema con query") ;
    $row1 = mysql_fetch_row($tab1);
    $dat = "select * from colegio where usuario = 'administrador'";
    $tab = mysql_query($dat, $con) or die ("problema con query") ;
    $row = mysql_fetch_row($tab);
	//Logo
	$this->Image('../logo/logo.gif',10,10,25);
	//Arial bold 15
	$this->SetFont('Arial','B',15);
	//Movernos a la derecha
	$this->Cell(80);
	//Ttulo
	$this->Cell(30,5,$row[0],0,1,'C');
	$this->SetFont('Arial','',9);
	//Movernos a la derecha
	$this->Ln(2);
	$this->Cell(80);
	$this->Cell(30,2,$row[1],0,0,'C');
	$this->Ln(3);
	$this->Cell(80);
	$this->Cell(30,2,$row[2],0,0,'C');
	$this->Ln(3);
	$this->Cell(80);
	$this->Cell(30,2,$row[3].', '.$row[4].' '.$row[5],0,0,'C');
	$this->Ln(3);
	$this->Cell(80);
	$this->SetFont('Arial','',8);
	$this->Cell(30,3,'Tel. '.$row[12].' Fax '.$row[13],0,0,'C');
	$this->Ln(3);
	$this->Cell(80);
	$this->Cell(30,3,$row[20],0,0,'C');
	//Salto de lnea
	$this->Ln(10);
	$this->Cell(80);
	$this->SetFont('Arial','B',12);
    $this->Cell(30,5,'INFORME BALANCES DEPOSITADOS A LOS ESTUDIANTES '.$row1[116],0,1,'C');
    $this->Cell(30,5,'',0,1,'C');
	$this->Cell(80);
    $this->Ln(8);
	$this->SetFont('Arial','B',10);
    $this->SetFillColor(230);
    $this->Cell(8,5,'#',1,0,'C',true);
    $this->Cell(20,5,'CTA.',1,0,'C',true);
    $this->Cell(70,5,'NOMBRE',1,0,'C',true);
    $this->Cell(22,5,'BALANCES',1,0,'C',true);
    $this->Cell(70,5,'COMENTARIOS',1,1,'C',true);
	$this->SetFont('Arial','',11);
}

//Pie de pgina
function Footer()
{
    $this->SetY(-15);

    //Arial italic 8
    $this->SetFont('Arial','I',8);
    //N&uacute;mero de p&aacute;gina
    $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}'.' / '.date('m-d-Y'),0,0,'C');
}
}

session_start();
$id=$_SESSION['id1'];
$usua=$_SESSION['usua1'];

//Creacin del objeto de la clase heredada
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',11);
include('../control.php');

$caja=$_POST['caja'];

$codigo=$_POST['codigo'];

$consult1 = "select * from colegio where usuario = '$usua'";
$resultad1 = mysql_query($consult1);
$row0=mysql_fetch_array($resultad1);

$q = "select * from year where year='$row0[116]' and cantidad <> 0 ORDER BY apellidos ";
$result=mysql_query($q);
$est=0;
$tot1=0;
$tot2=0;
$tot3=0;
$tot4=0;
$tot5=0;
$tot6=0;
$tot7=0;
$tot8=0;
$tot9=0;
$tot10=0;
$tot11=0;
$tot19=0;
    
  $est=0;
  while ($row2=mysql_fetch_array($result))
        {
        $q22 = "select DISTINCT * from year where ss='$row2[1]' ";
        $result22=mysql_query($q22);
        $row22=mysql_fetch_array($result22);
        $est=$est+1;

        $pdf->Cell(8,5,$est,0,0,'R');
        $pdf->Cell(20,5,$row2[5],0,0,'R');
        $pdf->Cell(70,5,$row2['apellidos'].' '.$row2['nombre'],0,0,'L');
       $tot11=$tot11+$row2['cantidad'];
       $tot0=$tot0+$row2['cantidad'];
       $pdf->Cell(22,5,$row2['cantidad'],0,0,'R');
       if ($row2['tipoDePago']=='Tarjeta'){$tdp='Tarj. Cr�dito';}
       if ($row2['tipoDePago']=='ACH'){$tdp='Cheque';}
       $pdf->Cell(70,5,'','B',1,'R');
  }

  $pdf->Cell(25,5,'',0,1,'R');
  $pdf->Cell(25,5,'',0,1,'R');
  $pdf->Cell(25,5,'',0,0,'R');
  $pdf->Cell(28,5,'=====================',0,1,'L');
  if ($tot1 > 0)
     {
     $pdf->Cell(25,5,'',0,0,'R');
     $pdf->Cell(27,5,'Tarjas Cr�dito: ',0,0,'L');
     $pdf->Cell(18,5,number_format($tot1, 2),0,1,'R');
     }
  if ($tot2 > 0)
     {
     $pdf->Cell(25,5,'',0,0,'R');
     $pdf->Cell(27,5,'Cheque: ',0,0,'L');
     $pdf->Cell(18,5,number_format($tot2, 2),0,1,'R');
     }

  $pdf->Cell(25,5,'',0,0,'R');
  $pdf->Cell(28,5,'=====================',0,1,'L');
  $pdf->Cell(25,5,'',0,0,'R');
  $pdf->Cell(27,5,'Gran Total: ',0,0,'L');
  $pdf->Cell(18,5,number_format($tot11, 2),0,1,'R');

//$pdf->Output();

$consult1 = "select * from colegio where usuario = '$usua'";
$resultad1 = mysql_query($consult1);
$row2=mysql_fetch_array($resultad1);


$fileatt_name = "balances.pdf";
$dir='';

$pdf->Output($dir.$fileatt_name);

//....................

$file = $pdf->Output("", "S");

$email_from = $row2[11]; // Who the email is from
$email_subject = "Estado de Pagos realizados del Colegio"; // The Subject of the email
$email_to = $miVariable5; // Who the email is to

$semi_rand = md5(time());

$encoded = chunk_split(base64_encode($data));
$fileatt_type = "application/pdf"; // File Type

$seperator = md5(uniqid(time()));
$email_to = $row2[11];

$dat = "select * from colegio where usuario = 'administrador'";
$tab = mysql_query($dat, $con) or die ("problema con query") ;
$row = mysql_fetch_row($tab);

	$correo = $email_to;
	$colegio = $row->colegio;

   require '../../PHPMailer-master/PHPMailerAutoload.php';
   $mail = new PHPMailer;
   $mail->setLanguage('es', '../../PHPMailer-master/language/');
	$mail->setFrom($correo, $colegio);
	$mail->Subject = 'Informe de balances';
	$mail->msgHTML('<center><h1>Informe de balances de los estudiantes</h1></center>');
	$mail->AddStringAttachment($file,'balances.pdf');
      $mail->addAddress($correo,'Administracion');
      $correo ='alf_med@hotmail.com';
      $mail->addAddress($correo,'Administracion');
      $mail->send();


?>


<!DOCTYPE html>
<html lang="ES">
<meta charset="utf-8">
<head>
	<title>Ajuste de cuentas</title>
	<script type="text/javascript" src="calendar/calendar.js"></script>
	<script type="text/javascript" src="calendar/calendar-setup.js"></script>
	<script type="text/javascript" src="calendar/lang/calendar-es.js"></script>
	<link rel="stylesheet" href="calendar/calendar-win2k-cold-1.css">
<link href="../../jv/botones.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.gris {
	background-color: #CCCCCC;
}
.color {
	background-color: #FFFFCC;
}
.table{
	margin-top: 150px;
}
.myButton{
	width: 180px;
}

</style>

</head>
<body>
	<form action="<?php echo $_REQUEST['pdf'] ?>.php" method="post" target="<?php echo $_REQUEST['pdf'] ?>">
		<table class="table" align="center" align="center" cellpadding="2" cellspacing="0" style="width: 40%">
			<tr class="gris">
				<th colspan="2">Seleccionar fechas</th>
			</tr>
			<tr class="gris">
				<th>Desde:</th>
				<th>Hasta:</th>
			</tr>
			<tr class="color">
				<td><center><input type="text" readonly="" id="fecha1" name="fecha1" value="<?php echo date("Y-m-d") ?>"><button type='submit' id='cal-btn-1'>...</button></center></td>
				<td><center><input type="text" readonly="" id="fecha2" name="fecha2" value="<?php echo date('Y-m-d') ?>"><button type='submit' id='cal-btn-2'>...</button></center></td>
			</tr>
			<?php if ($_REQUEST['pdf'] == 'info_cuadre'): ?>
				<tr class="color">
				<td colspan="2">
					<label><input checked="checked" type="radio" name="opcion" value="1"> Detallada</label>
					<label><input type="radio" name="opcion" value="2"> Resumen</label>
				</td>
			</tr>
			<?php endif ?>
			<tr class="gris">
				<td colspan="2">
					<center><input type="submit" class="myButton" name="aceptar" value="Continuar"><a href="menu.php" class="myButton">Atrás</a></center>
				</td>
			</tr>

		</table>
	</form>
	
	<script type="text/javascript" src="/js/jquery-2.1.1.min.js"></script>
	<script type="text/javascript">
	$(function() {	


            Calendar.setup({
              inputField    : "fecha1",
              button        : "cal-btn-1"
            });
            Calendar.setup({
              inputField    : "fecha2",
              button        : "cal-btn-2"
            });
            
    
	})
	</script>
</body>
</html>

