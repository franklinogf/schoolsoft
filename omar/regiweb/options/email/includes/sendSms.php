<?php
require_once '../../../../app.php';

use Classes\Lang;
use Classes\Mail;

$lang = new Lang([
	["Tiene un correo nuevo de parte de", "You have a new email from"]
]);
$mail = new Mail(true, "Teacher");

$mail->body = $lang->translation("Tiene un correo nuevo de parte de") . " $schoolName";

if ($compa1) {

	$mail->addAddress(phoneAddress($telefono1, $compa1));
}
if ($compa2) {
	$mail->addAddress(phoneAddress($telefono2, $compa2));
}

if (!$mail->send()) {
	echo $mail->ErrorInfo;
}

function phoneAddress($phone, $company)
{
	$phoneAddress = preg_replace('/[^\d]/', '', $phone);
	if ($company == "AT&T") {
		$phoneAddress .= "@txt.att.net";
	}
	if ($company == "T-Movil") {
		$phoneAddress .= "@tmomail.net";
	}
	if ($company == "Sprint") {
		$phoneAddress .= "@messaging.sprintpcs.com";
	}
	if ($company == "Open M.") {
		$phoneAddress .= "@email.openmobilepr.com";
	}
	if ($company == "Claro") {
		$phoneAddress .= "@mms.claropr.com";
	}
	if ($company == "Verizon") {
		$phoneAddress .= "@vtext.com";
	}
	if ($company == "Suncom") {
		$phoneAddress .= "@tms.suncom.com";
	}
	if ($company == "Boost") {
		$phoneAddress .= "@myboostmobile.com";
	}
	return $phoneAddress;
}
