<?php
require_once '../../../app.php';

use Classes\Email;
use Classes\Route;
use Classes\Session;
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

$mail = new Email();
$subject = $lang === 'es' ? "Padre solicitando cita" : "Parent requesting an appointment";

if ($lang === 'es') {
  $body = "
<!DOCTYPE html>
<html lang='{$lang}'>
<head>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <title>Solicitar cita</title>
</head>
<body>
<center><h1>Padre solicitando cita</h1></center>
<h2>Información del padre que solicita la cita:</h2>
<p><b>Nombre:</b> $parentName</p>
<p><b>Teléfono:</b> $phone</p>
<p><b>Correo electronico:</b> $email</p>
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
} else {
  $body = "
<!DOCTYPE html>
<html lang='{$lang}'>
<head>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <title>Request an appointment</title>
</head>
<body>
<center><h1>Request an appointment</h1></center>
<h2>Information of the parent requesting the appointment:</h2>
<p><b>Name:</b> $parentName</p>
<p><b>Phone:</b> $phone</p>
<p><b>Email:</b> $email</p>
<p><b>Date:</b> $date Hora: $time</p>
<p><b>Class:</b> $teacherClass</p>
<p><b>Student:</b> $student</p>
<br>
<p><b>Appointment purpose:</b></p>
<p>{$message}</p>

<hr>
</body>
</html>
";
}
$to = [];
if ($teacher->email1 !== "") {
  $to[] = $teacher->email1;
}
if ($teacher->email2 !== "") {
  $to[] = $teacher->email2;
}
if ($teacher->info('email3') !== "") {
  $to[] = $teacher->info('email3');
}
if ($teacher->info('email5') !== "") {
  $to[] = $teacher->info('email5');
}

$mail->send(
  to: ['franklinomarflores@gmail.com'],
  subject: $subject,
  message: $body
);
Route::redirect('/options/appointment');
