<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\Admin;
use App\Models\Homework;
use App\Models\Student;
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
$homework = Homework::findOrFail($id_homework);
$students = Student::byClass($homework->curso)->get();


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
   $schoolName = Admin::primaryAdmin()->colegio;
   $studentName = mb_convert_encoding("{$student->id} {$student->nombre} {$student->apellidos}", 'UTF-8');
   $messageTitle = mb_convert_encoding(__LANG === 'es' ? 'Tarea de ' : 'Homework of ' . $homework->curso, 'UTF-8');

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
      <p>El estudiante <b>$studentName tiene una nueva tarea de " . mb_convert_encoding($homework->descripcion, 'UTF-8') . "</b></p>
   
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
      <p>The student <b>$studentName has a new homework of " . $homework->descripcion . "</b></p>
   
      <p>Link: <a href='$link'>Foro</a></p>
   </body>
   </html>
   ";

   if ($count > 0) {
      $mail->send();
   }
   $mail->ClearAddresses();
}
