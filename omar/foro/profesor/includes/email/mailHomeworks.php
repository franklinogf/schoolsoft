<?php
require_once '../../../app.php';

use Classes\Controllers\Homework;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\DataBase\DB;
use Classes\Mail;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\Util;

Session::is_logged();
Server::is_post();
global $id_homework;

$mail = new Mail(false, 'Teacher');
$teacher = new Teacher(Session::id());
$homework = new Homework($id_homework);
$students = new Student();
$students = $students->findByClass($homework->curso);

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


   $link =  Route::url('/foro/login.php', true);
   $schoolName = $teacher->info('colegio');
   $studentName = utf8_decode("{$student->id} {$student->nombre} {$student->apellidos}");
   $messageTitle = utf8_decode(__LANG === 'es' ? 'Tarea de ' : 'Homework of ' . $homework->curso);

   $mail->isHTML(true);
   $mail->Subject = __LANG === 'es' ? 'Nueva tarea' : 'New homework';
   $mail->Body = __LANG === 'es' ? "
   <!DOCTYPE html>
   <html lang='es'>
   <head>
     <meta charset='UTF-8'>
     <meta name='viewport' content='width=device-width, initial-scale=1.0'>
     <title>Tarea</title>
   </head>
   <body>
      <center><h1>{$schoolName}</h1></center>
      <center><h2>$messageTitle</h2></center>
      <br>
      <br>
      <p>El estudiante <b>$studentName tiene una nueva tarea de " . utf8_decode($student->descripcion) . "</b></p>
   
      <p>Link: <a href='$link'>Forum</a></p>
   </body>
   </html>
   " : "
   <!DOCTYPE html>
   <html lang='en'>
   <head>
     <meta charset='UTF-8'>
     <meta name='viewport' content='width=device-width, initial-scale=1.0'>
     <title>Homework</title>
   </head>
   <body>
      <center><h1>{$schoolName}</h1></center>
      <center><h2>$messageTitle</h2></center>
      <br>
      <br>
      <p>The student <b>$studentName has a new homework of " . utf8_decode($student->descripcion) . "</b></p>
   
      <p>Link: <a href='$link'>Foro</a></p>
   </body>
   </html>
   ";

   if ($count > 0) {
      $mail->send();
   }
   $mail->ClearAddresses();
}
