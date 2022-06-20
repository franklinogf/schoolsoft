<?php
require_once '../../../../app.php';

use Classes\Lang;
use Classes\Mail;
use Classes\Util;

$lang = new Lang([
	["Tiene un correo nuevo de parte de", "You have a new email from"]
]);
$mail = new Mail(true, "Teacher");

$mail->body = $lang->translation("Tiene un correo nuevo de parte de") . " $schoolName";

if ($companie1) {
	$mail->addAddress(Util::phoneAddress($cellPhone1, $companie1));
}
if ($companie2) {
	$mail->addAddress(Util::phoneAddress($cellPhone2, $companie2));
}

if (!$mail->send()) {
	echo $mail->ErrorInfo;
}
