<?php
require_once '../../../../app.php';

use Classes\Mail;
use Classes\Util;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();
Server::is_post();
$mail = new Mail();
$teacher = new Teacher(Session::id());
$virtualId = $_POST['id'];
$virtualClass = DB::table('virtual')->where('id', $virtualId)->first();
$subjectCode = $virtualClass->curso;
$students = new Student();
$students = $students->findByClass($subjectCode);

foreach ($students as $student) {
   if (__COSEY) {
      $emails = [
         ['correo' => $student->emailp, 'nombre' => $student->nombre_padre]
      ];
   } else {
      $parents = DB::table('madre')->where('id', $student->id)->first();
      $emails = [
         ['correo' => $parents->email_p, 'nombre' => $parents->padre],
         ['correo' => $parents->email_m, 'nombre' => $parents->madre]
      ];
   }
   $emails = Util::toObject($emails);
   $count = 0;
   foreach ($emails as $email) {
      if ($email->correo !== '') {
         $count++;
         $mail->addAddress($email->correo, $email->nombre);
      }
   }


   $schoolName = $teacher->info('colegio');
   $messageTitle = utf8_decode("Salón virtual para $subjectCode");
   $date = Util::formatDate($virtualClass->fecha);
   $time = Util::formatDate($virtualClass->hora);
   $link = (__COSEY) ? 'https://www.cosey.org'. Route::url('/foro/login.php')  : 'https://www.schoolsoft.com'.Route::url('/foro/login.php');

   $mail->isHTML(true);
   $mail->Subject = utf8_decode("Salón virtual para $subjectCode");
   $mail->Body    = "<!DOCTYPE html>
<html lang='es'>
<head>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <title>Salón Virtual</title>
</head>
<body>
<center><h2>$schoolName</h2></center>
<center><h3>$messageTitle</h3></center>
<h4>{$virtualClass->titulo}</h4>
<p>Tiene una nueva clase virtual</a></p>
<hr>
<p>Fecha: $date</p>
<p>Hora: $time</p>
<p><a href='$link'>Acceder al Foro</a></p>
</body>
</html>
";

   if ($count > 0) {
      $mail->send();
   }
   $mail->ClearAddresses();
}
