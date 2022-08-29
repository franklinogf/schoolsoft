<?php 
global $id_estudiante;
$resultad1 = mysql_query("SELECT * FROM colegio WHERE usuario = '$usua'",$con);
$reg=mysql_fetch_array($resultad1);
$year = $reg['year2'];
//L para local, E para externo
$host = $reg['host'];
//el que envia, email del colegio
$correo = $reg["correo"];
//nombre del colegio
$colegio = $reg['colegio'];

$MAIL = new PHPMailer;
$MAIL->setLanguage('es', '../../../PHPMailer-master/language/');
//solo si es externo
if ($host == 'E') {
	$puerto = $reg['port'];
	$smtpHost = $reg['host_smtp'];
	$correo = $reg['email_smtp'];
	$clave = $reg['clave_email'];
	$MAIL->isSMTP();
	//$MAIL->SMTPDebug = 2;
	$MAIL->Debugoutput = 'html';
	$MAIL->Host = $smtpHost;
	$MAIL->Port = $puerto;
	$MAIL->SMTPSecure = 'tls';
	$MAIL->SMTPAuth = true;
	$MAIL->Username = $correo;
	$MAIL->Password = $clave;

}

//Set who the message is to be sent from
$MAIL->setFrom($correo, $colegio);
$MAIL->Subject = "Recibo de compra #$id_compra";
$MAIL->msgHTML("$estudiante ($id_estudiante) gastó $".number_format($compras->total,2)."\nBalance disponible $".$deposito->cantidad);

	
	
	if ($compa1) {
		if ($compa1=="AT&T"){$telefono1=$celular1."@txt.att.net";}
		if ($compa1=="T-Movil"){$telefono1=$celular1."@tmomail.net";}
		if ($compa1=="Sprint"){$telefono1=$celular1."@messaging.sprintpcs.com";}
		if ($compa1=="Open M."){$telefono1=$celular1."@email.openmobilepr.com";}
		if ($compa1=="Claro"){$telefono1=$celular1."@mms.claropr.com";}
		if ($compa1=="Verizon"){$telefono1=$celular1."@vtext.com";}
		if ($compa1=="Suncom"){$telefono1=$celular1."@tms.suncom.com";}
		if ($compa1=="Boost"){$telefono1=$celular1."@myboostmobile.com";}
		$MAIL->addAddress($telefono1);
	}
	if ($compa2) {
		
		if ($compa2=="AT&T"){$telefono2=$celular2."@txt.att.net";}
		if ($compa2=="T-Movil"){$telefono2=$celular2."@tmomail.net";}
		if ($compa2=="Sprint"){$telefono2=$celular2."@messaging.sprintpcs.com";}
		if ($compa2=="Open M."){$telefono2=$celular2."@email.openmobilepr.com";}
		if ($compa2=="Claro"){$telefono2=$celular2."@mms.claropr.com";}
		if ($compa2=="Verizon"){$telefono2=$celular2."@vtext.com";}
		if ($compa2=="Suncom"){$telefono2=$celular2."@tms.suncom.com";}
		if ($compa2=="Boost"){$telefono2=$celular2."@myboostmobile.com";}
		$MAIL->addAddress($telefono2);
	}
	// if($compa1 || $compa2){

	// 	$MAIL->addCC("recibos@colegiobautista.org");
	// }

	if (!$MAIL->send()) {
		// echo $MAIL->ErrorInfo;
	}
	$MAIL->clearAddresses();
    $MAIL->clearAttachments();