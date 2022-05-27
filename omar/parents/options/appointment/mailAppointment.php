<?php
require_once '../../../app.php';

use Classes\Mail;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Parents;
use Classes\Controllers\Teacher;

Session::is_logged();

$parents = new Parents(Session::id());
$teacherId = $_POST['teacherId'];
$parent = $_POST['parent'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$teacherClass = $_POST['teacherClass'];
$message = $_POST['message'];
$date = $_POST['date'];
$time = $_POST['time'];
$student = $_POST['student'];
$lang = __LANG;
$parentName = $parents->{$parent};
$teacher = new Teacher($teacherId);

$mail = new Mail(true, "Parents");
$mail->setFrom($email, $parentName);
$mail->isHTML(true);
$mail->Subject = "Padre solicitando cita";
$mail->Body = "
<!DOCTYPE html>
<html lang='{$lang}'>
<head>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <title>Solicitar cita</title>
</head>
<body>
<center><h1>Padre solicitando cita</h1></center>
<h2>Información de la persona que solicita la cita:</h2>
<p><b>Nombre:</b> $parentName</p>
<p><b>Teléfono:</b> $phone</p>
<p><b>Email:</b> $email</p>
<p><b>Fecha:</b> $date Hora: $time</p>
<p><b>Curso:</b> $teacherClass</p>
<p><b>Estudiante:</b> $student</p>
<br>
<p><b>Proposito de la cita:</b></p>
<p>{$message}</p>

<hr>
</body>
</html>
";
if ($teacher->email1 != "") $mail->addAddress($teacher->email1, "$teacher->nombre $teacher->apellidos");
if ($teacher->email2 != "") $mail->addAddress($teacher->email2, "$teacher->nombre $teacher->apellidos");
if ($teacher->info('email3') != "") $mail->addAddress($teacher->info('email3'));
if ($teacher->info('email5') != "") $mail->addAddress($teacher->info('email5'));
$mail->addAddress('franklinomarflores@gmail.com', 'Franklin Gonzalez');
$mail->send();

Route::redirect('/options/appointment');

