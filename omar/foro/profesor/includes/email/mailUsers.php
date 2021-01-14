<?php
require_once '../../../../app.php';

use Classes\Controllers\Teacher;
use Classes\DataBase\DB;
use Classes\Mail;
use Classes\Route;
use Classes\Session;
use Classes\Util;

Session::is_logged();
$mail = new Mail(true, "Teacher");
$teacher = new Teacher(Session::id());

$students = DB::table('year')->where([
   ['grado', $teacher->grado],
   ['year', $teacher->info('year')],
   ['fecha_baja', '0000-00-00'],
   ['usuario', '!=', ''],
])->orderBy('apellidos')->get();

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

   $link = Route::url('/foro/login.php', true);
   $schoolName = $teacher->info('colegio');
   $studentName = "{$student->id} {$student->nombre} {$student->apellidos}";
   $studentUser = $student->usuario;
   $studentPassword = $student->clave;
   $messageTitle = "Usuario y Clave";

   $mail->isHTML(true);
   $mail->Subject = "Acceso al foro";
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
<p>Esta es la información de acceso para el estudiante: <b>{$studentName}</b></p>
<ul style='list-style: none;'>
	<li>Usuario: <b>{$studentUser}</b></li>
   <li style='margin-top: 10px;'>Clave: <b>{$studentPassword}</b></li><br>   
   <li style='margin-top: 10px;'><b>Link: </b><a href='{$link}' style='color: #FFFFFF; background-color: #FF3A00'>Acceso al Foro</a></li>
   <br><br>
</ul>
<hr>
</body>
</html>
";

   if ($count > 0) {
      if (!$mail->send()) {
         echo "Error " .$mail->ErrorInfo;
      }
   }
   $mail->ClearAddresses();
}
