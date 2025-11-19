<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ['ESTADO DE CUENTAS', 'STATEMENT'],
    ['Primer aviso de cobro', 'First collection notice'],
    ['CUENTA', 'ACCOUNT'],
    ['PAGOS', 'PAYS'],
    ['es', 'in'],
    ["HEMOS REVISADO NUESTRAS CUENTAS A COBRAR Y ENCONTRAMOS QUE USTED A LA FECHA DE HOY ", "WE HAVE REVIEWED OUR ACCOUNTS RECEIVABLE AND FOUND THAT YOU TO THE TODAY'S DATE "],
    ['Padre, Madre o Encargado', 'Father, Mother or Guardian'],
    [' NO HA EFECTUADO EL PAGO CORRESPONDIENTE AL MES DE:', ' YOU HAVE NOT MADE THE PAYMENT FOR THE MONTH OF:'],
    ['Agosto', 'August'],
    ['Septiembre', 'September'],
    ['Octubre', 'October'],
    ['Noviembre', 'November'],
    ['Diciembre', 'December'],
    ['Enero', 'January'],
    ['Febrero', 'February'],
    ['Marzo', 'March'],
    ['Abril', 'Abril'],
    ['Mayo', 'May'],
    ['Junio', 'June'],
    ['Julio', 'July'],
    ['GRADO', 'GRADE'],
    ['Matri/Junio', 'Regis/June'],
    ['ESTUDIANTES', 'STUDENTS'],
    ['DESCRIPCION', 'DESCRIPTION'],
    ['NOTA IMPORTANTE:', 'IMPORTANT NOTE:'],
    ['1. DESPUES DEL DIA 10 DE CADA MES SE COBRARAN $', '1. AFTER THE 10TH OF EACH MONTH A $'],
    [' DE CARGOS POR DEMORA POR CUENTA.', ' LATE CHARGE WILL BE CHARGED FOR ACCOUNT.'],
    ['BALANCE', 'BALANCE SHEET'],
    ['Estado de cuenta', 'Statement'],
    ['BALANCE DEL ESTADO DE CUENTA:', 'TOTAL BALANCE SHEET:'],
    ['PAGO REQUERIDO:', 'PAYMENT REQUIRED:'],
    ['2. LOS PAGOS PUEDEN HACERSE MEDIANTE TARJETA DE CREDITO, ATH, ATHMOVIL BUSINESS, EFECTIVO, GIRO POSTAL.', '2. PAYMENTS CAN BE MADE BY CREDIT CARD, ATH, ATHMOVIL BUSINESS, CASH, MONEY ORDER.'],
    ['3. FAVOR DE HACER LOS ARREGLOS PERTINENTES PARA QUE LOS SERVICIOS EDUCATIVOS DE SU HIJO(A) NO SE VEAN AFECTADOS.', '3. PLEASE MAKE ARRANGEMENTS SO THAT THE EDUCATIONAL SERVICES OF YOUR CHILD WILL NOT BE AFFECTED.'],
    ['CORDIALMENTE', 'CORDIALLY'],
    ['OFICINA DE FINANZAS', 'FINANCE OFFICE'],
    ['Si usted ha realizado el pago antes mencionado, favor de hacer caso omiso a esta notificaci&#65533;n.', 'If you have made the aforementioned payment, please ignore this notification.'],
    ['', ''],
]);

$db = new DB();
$col = db::table('colegio')->whereRaw("usuario = 'administrador'")->first();
$colegio = $col->colegio;

$school = new School(Session::id());
$year = $school->info('year2');
$chk = $school->info('chk');
$reply_to = $school->info('correo');
$user = $school->info('usuario');

$mes = $_REQUEST['mes'];
list($y3, $y2) = explode("-", $year);
if ($mes < 6) {
	$y1 = '20' . $y2;
} else {
	$y1 = '20' . $y3;
}
list($ya, $yb, $yc) = explode("-", date('Y-m-d'));

$fecha = date('Y-m-d', mktime(0, 0, 0, $mes, 1, $ya));
$fechaFinal = "";

if ($mes > 6) {
	$year1 = "{$year[0]}{$year[1]}";
	$fechaFinal = "AND fecha_d>='$year1-07-01'";
}
class nPDF extends PDF
{
	function Header()
	{
        parent::header();
	}
	
}

$MES = array(
	'enero','febrero','marzo','abril','mayo','junio',
	'julio','agosto','septiembre','octubre','noviembre','diciembre'
	);

$pdf = new nPDF();
//$result = mysql_query("SELECT DISTINCT id FROM pagos WHERE fecha_d <= '$fecha' AND year = '$year'",$con);
//while ($r = mysql_fetch_object($result)) {
$result = DB::table('pagos')->select("DISTINCT id")
        ->whereRaw("fecha_d <= '$fecha' AND year = '$year' and baja=''")->orderBy('id')->get();

foreach ($result as $r) {
        $pdf->SetFont('Arial','',12);
//		$rs = mysql_query("SELECT DISTINCT fecha_d FROM pagos WHERE id='$r->id' AND fecha_d <= '$fecha' AND year = '$year' $fechaFinal ORDER BY fecha_d DESC",$con);
//		if (mysql_num_rows($rs) > 0) {
//			while ($rd = mysql_fetch_object($rs)) {

	    $rs = DB::table('pagos')->select("DISTINCT fecha_d")
	        ->whereRaw("id='$r->id' AND fecha_d <= '$fecha' AND year = '$year' $fechaFinal and baja=''")->orderBy('fecha_d')->get();
		$total = 0;
		if (count($rs) > 0) {
	      foreach ($rs as $rd) {
			$deudas = 0;
			$pagos = 0;
//			$p = mysql_query("SELECT * FROM pagos WHERE fecha_d='$rd->fecha_d' AND id='$r->id' AND fecha_d <= '$fecha' AND year = '$year' $fechaFinal",$con);
//			while ($row = mysql_fetch_object($p)) {
            $p = DB::table('pagos')
               ->whereRaw("fecha_d='$rd->fecha_d' AND id='$r->id' AND year = '$year' AND fecha_d <= '$fecha' $fechaFinal and baja=''")->get();
          foreach ($p as $row) {
				$deudas+=$row->deuda;
				$pagos+=$row->pago;
			}
			$total = $deudas-$pagos;						
		}
		if($total != 0){
			if($_POST['opcion'] == 'deudores'){
				$TOTAL = 0;
				mysql_data_seek($rs, 0);
				while ($rd = mysql_fetch_object($rs)) {
					$deudas = 0;
					$pagos = 0;
					$p = mysql_query("SELECT * FROM pagos WHERE fecha_d='$rd->fecha_d' AND id='$r->id' AND fecha_d <= '$fecha' AND year = '$year' $fechaFinal",$con);
					while ($row = mysql_fetch_object($p)) {
						$deudas+=$row->deuda;
						$pagos+=$row->pago;
					}
					$total = $deudas-$pagos;
					$TOTAL += $total;			

				}
				if ($TOTAL != 0 ) {			
				
				$pdf->AddPage();
				$pdf->Cell(0,5,$_POST['mensajeTitulo'],0,1,'C');
				// $pdf->Ln(5);
				$pdf->Cell(0,5,$_POST['mensajeSaludo'],0,1);
				// $pdf->Ln(5);
				$fechaHoy = date('j').' de '.$MES[date('n')-1].' de '.date('Y');
				$pdf->Cell(0,5,$fechaHoy,0,1);
				$pdf->Ln(5);
				$pdf->Cell(0,5,'ESTUDIANTE (S)',0,1);
				$pdf->Ln(3);
				$pdf->SetFont('Arial','B',12);
				$e = mysql_query("SELECT * FROM year WHERE id='$r->id' AND year='$year'",$con);
					while ($estu = mysql_fetch_object($e)) {
						$pdf->Cell(0,5,"$estu->nombre $estu->apellidos  $estu->grado",0,1);
						
					}	
				$pdf->Ln(3);
				$pdf->SetFont('Arial','',12);
				$pdf->Cell(0,5,'CUENTA # '.$r->id,0,1);
				$pdf->Ln(5);
				$pdf->MultiCell(0,6,$_POST['mensaje']);
				$pdf->Ln(5);
				$pdf->SetFont('Arial','B',12);
				
				
				$pdf->Cell(37,5,'BALANCE');
				$pdf->Cell(100,5,'.......................................................................');
				$pdf->Cell(0,5,number_format($TOTAL,2),0,1);
				$pdf->Ln(5);
				if ($_POST['mensajeDespedida'] != '') {
					$pdf->Cell(0,5,'CORDIALMENTE,',0,1);
					$pdf->Ln(5);
					$pdf->Cell(0,5,$_POST['mensajeDespedida']);
				}
			}
		}
		}else{

				$pdf->AddPage();
				$pdf->Cell(0,5+$_POST['pt'],$_POST['mensajeTitulo'],0,1,'C');
				// $pdf->Ln(5);
				$pdf->Cell(0,5+$_POST['ps'],$_POST['mensajeSaludo'],0,1);
				// $pdf->Ln(5);
				$fechaHoy = date('j').' de '.$MES[date('n')-1].' de '.date('Y');
				$pdf->Cell(0,5,$fechaHoy,0,1);
				$pdf->Ln(5);
				$pdf->Cell(0,5,'ESTUDIANTE (S)',0,1);
				$pdf->Ln(3);
				$pdf->SetFont('Arial','B',12);
				$e = mysql_query("SELECT * FROM year WHERE id='$r->id' AND year='$year'",$con);
					while ($estu = mysql_fetch_object($e)) {
						$pdf->Cell(0,5,"$estu->nombre $estu->apellidos  $estu->grado",0,1);
						
					}	
				$pdf->Ln(3);
				$pdf->SetFont('Arial','',12);
				$pdf->Cell(0,5,'CUENTA # '.$r->id,0,1);
				$pdf->Ln(5);
				$pdf->MultiCell(0,5+$_POST['pm'],$_POST['mensaje']);
				$pdf->Ln(5);
				if ($_POST['mensajeDespedida'] != '') {
					$pdf->Cell(0,5,'CORDIALMENTE,',0,1);
					$pdf->Ln(5);
					$pdf->Cell(0,5+$_POST['pd'],$_POST['mensajeDespedida']);
				}
		}
	}
}

$pdf->Output();

if ($_POST['ipo'] == 'email') {	
require '../../../PHPMailer-master/PHPMailerAutoload.php';
$result = mysql_query("SELECT DISTINCT id FROM pagos WHERE fecha_d <= '$fecha' AND year = '$year'",$con);
while ($r = mysql_fetch_object($result)) {
	$pdf = new PDF();
	$pdf->SetFont('Arial','',12);
	$rs = mysql_query("SELECT DISTINCT fecha_d FROM pagos WHERE id='$r->id' AND fecha_d <= '$fecha' AND year = '$year' $fechaFinal ORDER BY fecha_d DESC",$con);
	if (mysql_num_rows($rs) > 0) {
		while ($rd = mysql_fetch_object($rs)) {
			$deudas = 0;
			$pagos = 0;
			$p = mysql_query("SELECT * FROM pagos WHERE fecha_d='$rd->fecha_d' AND id='$r->id' AND fecha_d <= '$fecha' AND year = '$year' $fechaFinal",$con);
			while ($row = mysql_fetch_object($p)) {
				$deudas+=$row->deuda;
				$pagos+=$row->pago;
			}
			$total = $deudas-$pagos;						
		}
		if($total != 0){
		$pdf->AddPage();
		$pdf->Cell(0,5,'PRIMER AVISO DE COBRO',0,1);
		$pdf->Cell(0,5,'20 AL 25 DE CADA MES',0,1);
		$pdf->Ln(5);
		$fechaHoy = date('j').' de '.$MES[date('n')-1].' de '.date('Y');
		$pdf->Cell(0,5,$fechaHoy,0,1);
		$pdf->Ln(5);
		$pdf->Cell(0,5,'ESTUDIANTE (S)',0,1);
		$pdf->Ln(3);
		$pdf->SetFont('Arial','B',12);
		$e = mysql_query("SELECT * FROM year WHERE id='$r->id' AND year='$year'",$con);
			while ($estu = mysql_fetch_object($e)) {
				$pdf->Cell(0,5,"$estu->nombre $estu->apellidos  $estu->grado",0,1);
				
			}	
		$pdf->Ln(3);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(0,5,'CUENTA # '.$r->id,0,1);
		$pdf->Ln(5);
		$pdf->MultiCell(0,6,"HEMOS REVISADO NUESTRAS CUENTAS A COBRAR Y ENCONTRAMOS QUE USTED A LA FECHA DE HOY $fechaHoy NO HA EFECTUADO EL PAGO CORRESPONDIENTE AL MES DE:");
		$pdf->Ln(5);
		$pdf->SetFont('Arial','B',12);
		$TOTAL=0;
		mysql_data_seek($rs, 0);
		while ($rd = mysql_fetch_object($rs)) {
			$deudas = 0;
			$pagos = 0;
			$p = mysql_query("SELECT * FROM pagos WHERE fecha_d='$rd->fecha_d' AND id='$r->id' AND fecha_d <= '$fecha' AND year = '$year' $fechaFinal",$con);
			while ($row = mysql_fetch_object($p)) {
				$deudas+=$row->deuda;
				$pagos+=$row->pago;
			}
			$total = $deudas-$pagos;
			$TOTAL += $total;
			if ($total != 0) {
				$pdf->Cell(37,5,strtoupper($MES[date('n',strtotime($rd->fecha_d))-1]));
				$pdf->Cell(100,5,'.................................................................');
				$pdf->Cell(0,5,number_format($total,2),0,1);
			}
		}
		$pdf->Cell(37,5,'BALANCE');
		$pdf->Cell(100,5,'.......................................................................');
		$pdf->Cell(0,5,number_format($TOTAL,2),0,1);
		$pdf->SetFont('Arial','',10);
		$pdf->Ln(5);
		$pdf->Cell(0,5,'RECUERDE INCLUIR:',0,1);
		$pdf->Ln(5);
		$pdf->Cell(0,7,'1. DESPUES DEL DIA 15 DE CADA MES $10.00 POR ESTUDIANTE.',0,1);
		$pdf->Cell(0,7,'2. DESPUES DEL DIA 30 DE CADA MES $25.00 POR ESTUDIANTE.',0,1);
		$pdf->Cell(0,7,'3. TODO CHEQUE DEVUELTO TIENE UN CARGO DE $25.00.',0,1);
		$pdf->Ln(5);
		$pdf->Cell(0,5,'NOTA IMPORTANTE:',0,1);
		$pdf->Ln(5);
		$pdf->MultiCell(0,5,'1. RECUERDE QUE NO SE ACEPTAN NI SE CONCEDEN PROMESAS DE PAGO EN ESTA OFICINA PARA NINGUN ESTUDIANTE.');
		$pdf->Ln(5);
		$pdf->MultiCell(0,5,'FAVOR DE HACER LOS ARREGLOS NECESARIOS PARA QUE SU HIJO (A) NO SE VEA AFECTADO (A)');
		$pdf->Ln(5);
		$pdf->Cell(0,5,'CORDIALMENTE,',0,1);
		$pdf->Ln(5);
		$pdf->Cell(0,5,'OFICINA DE COBRO Y SERVICIO A LOS PADRES');
		$pdf->Ln(10);
		$pdf->Cell(0,5,'Si usted ha realizado el pago antes mencionado, favor de hacer caso omiso a esta notificaci&#65533;n.');
	}



	$file = $pdf->Output('','S');

	$host = $reg->host;
	//el que envia, email del colegio
	$correo = $reg->correo;
	//nombre del colegio
	$colegio = $reg->colegio;


	$mail = new PHPMailer;
	$mail->setLanguage('es', '../../../PHPMailer-master/language/');
	//solo si es externo
	if ($host == 'E') {
		$puerto = $reg->port;
		$smtpHost = $reg->host_smtp;
		$correo = $reg->email_smtp;
		$clave = $reg->clave_email;
		$mail->isSMTP();
		$mail->SMTPDebug = 3;
		$mail->Debugoutput = 'html';
		$mail->Host = $smtpHost;
		$mail->Port = $puerto;
		$mail->SMTPSecure = 'tls';
		$mail->SMTPAuth = true;
		$mail->Username = $correo;
		$mail->Password = $clave;

	}

	$mail->setFrom($correo, $colegio);
	$mail->Subject = 'PRIMER AVISO DE COBRO';
	$mail->msgHTML('<center><h1>PRIMER AVISO DE COBRO</h1></center>');
	$mail->AddStringAttachment($file,'Primer aviso de cobro.pdf');
	$p = mysql_query("SELECT madre,padre,email_m,email_p FROM madre where id='$r->id'",$con);
	$pa = mysql_fetch_object($p);
	if ($pa->email_p != '') {
		$mail->addAddress($pa->email_p,$pa->padre);		
	}
	if ($pa->email_m != '') {
		$mail->addAddress($pa->email_m,$pa->madre);		
	}
	$mail->send();

		 
	// 		$mail->ClearAddresses();
	// 		$mail->ClearAttachments();
		

}
}
}
