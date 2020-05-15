<?php
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

$mail = new Mail();
$teacher = new Teacher(Session::id());
$homework = new Homework($id_homework);
$students = new Student();
$students = $students->findByClass($homework->curso);

foreach ($students as $student) {
   $parents = DB::table('madre')->where('id', $student->id)->first();
   $emails = [
      ['correo' => $parents->email_p, 'nombre' => $parents->padre],
      ['correo' => $parents->email_m, 'nombre' => $parents->madre]
   ];
   $emails = Util::toObject($emails);
   $count = 0;
   foreach ($emails as $email) {
      if ($email->correo !== '') {
         $count++;
         $mail->addAddress($email->correo, $email->nombre);
      }
   }


   $link = Route::url('/foro/', true);
   $schoolName = $teacher->info('colegio');
   $studentName = "{$student->id} {$student->nombre} {$student->apellidos}";
   $messageTitle = "Tarea del curso {$homework->curso}";

   $mail->isHTML(true);
   $mail->Subject = "Nueva tarea";
   $mail->Body    = "
<!DOCTYPE html>
<html lang='es'>
<head>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <title>Document</title>
</head>
<body>
   <center><h1>{$schoolName}</h1></center>
   <center><h2>{$messageTitle}</h2></center>
   <br>
   <br>
   <p>El estudiante: <b>$studentName tiene una nueva tarea de {$student->descripcion}</b></p>

   <p>Link: </b><a href='{$link}'>Acceso al Foro</a></p>
</body>
</html>
";

   if ($count > 0) {
      $mail->send();
   }
   $mail->ClearAddresses();
}
