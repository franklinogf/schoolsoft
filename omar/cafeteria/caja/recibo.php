<?php
session_start();
require_once("../../control.php");
require '../../../PHPMailer-master/PHPMailerAutoload.php';
$resultad1 = mysql_query("SELECT * FROM colegio WHERE usuario = 'administrador'", $con);
$reg=mysql_fetch_array($resultad1);
$year = $reg['year2'];
//L para local, E para externo
$host = $reg['host'];
//el que envia, email del colegio
$correo = $reg["correo"];
//nombre del colegio
$colegio = $reg['colegio'];
//end
$fecha = $_REQUEST['date'];
// echo "SELECT * FROM compra_cafeteria WHERE fecha = '$fecha' AND `year` = '$year' AND (tdp = '3' OR tdp = '4')";
$comprasRealizadas = mysql_query("SELECT * FROM compra_cafeteria WHERE fecha = '$fecha' AND `year` = '$year' AND (tdp = '3' OR tdp = '4')");
$mail = new PHPMailer;
$mail->setLanguage('es', '../../../PHPMailer-master/language/');
while ($compra = mysql_fetch_assoc($comprasRealizadas)) {

    $id_compra = $compra['id'];
    echo $estudiante = $compra['nombre']. ' '. $compra['apellido'];

    $correoTitulo = "Recibo de compra #{$id_compra}";
    $correoMensaje = "Recibo de compra del estudiante {$estudiante} el {$fecha}";

    $mensaje = "<center><h1>".$correoTitulo."</h1></center>\n\n";
    $mensaje .= $correoMensaje;

    $mail->addReplyTo($reg['correo'], $reg['director']);
    //solo si es externo
    if ($host == 'E') {
        $puerto = $reg['port'];
        $smtpHost = $reg['host_smtp'];
        $correo = $reg['email_smtp'];
        $clave = $reg['clave_email'];
        $mail->isSMTP();
        //$mail->SMTPDebug = 2;
        $mail->Debugoutput = 'html';
        $mail->Host = $smtpHost;
        $mail->Port = $puerto;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        $mail->Username = $correo;
        $mail->Password = $clave;
    }

    //Set who the message is to be sent from
    $mail->setFrom($correo, $colegio);

    //Set an alternative reply-to address
    // $mail->addReplyTo($correo, $colegio);

    //Set who the message is to be sent to
    //Set the subject line
    $mail->Subject = "Recibo de compra #$id_compra";

    //Read an HTML message body from an external file, convert referenced images to embedded,
    //convert HTML into a basic plain-text alternative body
    $mail->msgHTML($mensaje);

    //Replace the plain text body with one created manually
    // $mail->AltBody = 'This is a plain-text message body';

    require 'info_recibo.php';
    // $mail->addAttachment($documento,"Recibo de compra");
    $mail->AddAttachment($pdfoutputfile, "Recibo {$fecha}.pdf");
    //send the message, check for errors
    $m = mysql_query("SELECT madre,padre,email_m,email_p,cel_com_m,cel_com_p,cel_m,cel_p FROM madre  WHERE id='$id_estudiante'");
    $madre = mysql_fetch_object($m);

    if ($madre->email_m != '') {
        $mail->addAddress($madre->email_m, $madre->madre);
    }

    if ($madre->email_p != '') {
        $mail->addAddress($madre->email_p, $madre->padre);
    }
    if ($madre->email_m != '' || $madre->email_p != '') {
        // $mail->addCC("recibos@colegiobautista.org");
        if (!$mail->Send()) {
    		echo 'Mailer error: ' . $mail->ErrorInfo;
        }
    }

    // $mail->addAddress('franklinomarflores@gmail.com', 'Franklin');
    // $mail->addCC("alf_med@hotmail.com", 'Alfredo');

    $mail->clearAddresses();
    $mail->clearAttachments();

    if ($madre->cel_m != '' || $madre->cel_p != '') {
        $mail->ClearAddresses();
        $compa1 = $madre->cel_com_m;
        $celular1 = $madre->cel_m;
        $compa2 = $madre->cel_com_p;
        $celular2 = $madre->cel_p;
        include_once('correo_sms.php');
    }
}
// header("LOCATION: index.php");
