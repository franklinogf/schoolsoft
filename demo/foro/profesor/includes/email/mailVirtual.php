<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\Lang;
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
$mail = new Mail(false, 'Teacher');
$teacher = new Teacher(Session::id());
$virtualId = $_POST['id'];
$virtualClass = DB::table('virtual')->where('id', $virtualId)->first();
$subjectCode = $virtualClass->curso;
$students = new Student();
$students = $students->findByClass($subjectCode);
$lang = new Lang([
   ['Contrseña para entrar a la sala', 'Password to access the virtual classroom'],
   ['Salón virtual para', 'Virtual classroom for'],
   ['Información importante', 'Important information']
]);

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
   $messageTitle = utf8_decode($lang->translation("Salón virtual para") . ' ' . $subjectCode);
   $date = Util::formatDate($virtualClass->fecha);
   $time = Util::formatTime($virtualClass->hora);
   $password = $virtualClass->clave !== '' ? "<p>" . $lang->translation("Contraseña para entrar a la sala") . ": {$virtualClass->clave}</p>" : '';
   $information = $virtualClass->informacion !== '' ? utf8_decode("<p><b>" . $lang->translation("Información importante") . ":</b> <br> {$virtualClass->informacion}</p>") : '';
   $link =  Route::url('/foro/login.php', true);
   $studentFullName = utf8_decode("$student->nombre $student->apellidos");
   $teacherFullName = utf8_decode("$teacher->nombre $teacher->apellidos");
   $mail->isHTML(true);
   $mail->Subject = $messageTitle;
   $mail->Body = __LANG === 'es' ? "<!DOCTYPE html>
   <html lang='es'>
   <head>
     <meta charset='UTF-8'>
     <meta name='viewport' content='width=device-width, initial-scale=1.0'>
     <title>$messageTitle</title>
   </head>
      <body>
         <center><h2>$schoolName</h2></center>
         <center><h3>$messageTitle</h3></center>
         <h4>{$virtualClass->titulo}</h4>
         <p>$studentFullName</p>
         <p>Tiene una nueva clase virtual</p>
         <p><b>Profesor:</b> {$teacher->fullName(true)}</p>
         <hr>
         <p>Fecha: <b>$date</b></p>
         <p>Hora: <b>$time</b></p>
         $password
         $information
         <p><a href='$link'>Foro</a></p>
      </body>
   </html>
   " : "<!DOCTYPE html>
   <html lang='es'>
   <head>
     <meta charset='UTF-8'>
     <meta name='viewport' content='width=device-width, initial-scale=1.0'>
     <title>$messageTitle</title>
   </head>
      <body>
         <center><h2>$schoolName</h2></center>
         <center><h3>$messageTitle</h3></center>
         <h4>{$virtualClass->titulo}</h4>
         <p>$studentFullName</p>
         <p>You have a new virtual class</p>
         <p><b>Teacher:</b> {$teacher->fullName(true)}</p>
         <hr>
         <p>Date: <b>$date</b></p>
         <p>Time: <b>$time</b></p>
         $password
         $information
         <p><a href='$link'>Forum</a></p>
      </body>
   </html>
   ";

   if ($count > 0) {
      $mail->send();
   }

   $mail->ClearAddresses();
}
