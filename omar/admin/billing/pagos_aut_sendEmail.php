<?php
require_once '../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\Mail;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ['es', 'in'],
    ['', ''],
]);

$reg = DB::table('colegio')->whereRaw("usuario = 'administrador'")->first();
$colegio = $reg->colegio;

$school = new School(Session::id());
$year = $school->info('year2');

//require_once "../../control.php";
//require_once '../../../PHPMailer-master/PHPMailerAutoload.php';

//$result = mysql_query("SELECT * FROM colegio WHERE usuario = 'administrador'", $con);
//$reg = mysql_fetch_object($result);

//$year = $reg->year2;

$host = $reg->host;
//el que envia, email del colegio
$correo = $reg->correo;
//nombre del colegio
$colegio = utf8_decode($reg->colegio);

//$mail = new PHPMailer;
$mail = new Mail();
$mail->CharSet = 'UTF-8';
$mail->setLanguage('es', '../../../PHPMailer-master/language/');
//solo si es externo
// $mail->SMTPDebug = 1;

$title = $lang->translation('Primer aviso de cobro');
$subject = $lang->translation('Primer aviso de cobro');
$mail->Subject = $subject;
$emailsSent = 0;
$emailsError = 0;
$error = null;

//Set who the message is to be sent from
$mail->setFrom($correo, utf8_decode($colegio));
$mail->Subject = "Recibo";
$mail->isHTML(true);

$schoolName = $colegio;
ob_start();
include 'pagos_aut_emailTemplate.php';
//$mail->msgHTML(ob_get_contents());
$mail->Body = ob_get_contents();
ob_end_clean();
$mail->addAddress('franklinomarflores@gmail.com', $fullName);
//$mail->addAddress($customerEmail, $fullName);
$mail->addAddress("alf_med@hotmail.com", 'Alfredo Medina');
//$mail->addBCC($reg->correo);
if (!$mail->send()) {
    echo $mail->ErrorInfo;
}
