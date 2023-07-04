<?php
require_once '../../../../app.php';

use Classes\Lang;
use Classes\Mail;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;

Session::is_logged();
$lang = new Lang([
    ['Usuario y contraseña', 'Username and password'],
    ['Información de acceso', 'Access information'],
    ['Esta es la información de acceso para los padres del estudiante', 'This is the access information for the parents of the student'],
    ['Usuario', 'Username'],
    ['Contraseña', 'Password'],
    ['Acceso', 'Access'],
    ["Se ha enviado el correo electrónico", "Email has been sent"],
]);
$students = $_POST['students'];
$mail = new Mail();
foreach ($students as $ss) {
    $student = new Student($ss);
   
        $parents = DB::table('madre')->where(['id', $student->id])->first();
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

    $link = Route::url('/foro/login.php', true);
    $schoolName = $student->info('colegio');
    $studentName = "{$student->id} {$student->nombre} {$student->apellidos}";
    $username = $parents->usuario;
    $password = $parents->clave;
    $messageTitle = $lang->translation('Usuario y contraseña');
    $mail->isHTML(true);
    $mail->Subject = $lang->translation('Información de acceso');
    $_lang = __LANG;
   echo $mail->Body = "
   <!DOCTYPE html>
   <html lang='{$_lang}'>
   <head>
     <meta charset='UTF-8'>
     <meta name='viewport' content='width=device-width, initial-scale=1.0'>
     <title>{$messageTitle}</title>
   </head>
   <body>
   <center><h1>{$schoolName}</h1></center>
   <center><h2>{$messageTitle}</h2></center>
   <br>
   <br>
   <p>{$lang->translation('Esta es la información de acceso para los padres del estudiante')}: <b>{$studentName}</b></p>
   <ul style='list-style: none;'>
      <li>{$lang->translation('Usuario')}: <b>{$username}</b></li>
      <li style='margin-top: 10px;'>{$lang->translation('Contraseña')}: <b>{$password}</b></li><br>   
      <li style='margin-top: 10px;'><b>Link: </b><a href='{$link}' style='color: #FFFFFF; background-color: #FF3A00'>{$lang->translation('Acceso')}</a></li>
      <br><br>
   </ul>
   <hr>
   </body>
   </html>
   ";
    if ($count > 0) {
        if (!$mail->send()) {
            echo "Error " . $mail->ErrorInfo;
        }
    }
    Session::set('emailSent', $lang->translation("Se ha enviado el correo electrónico"));
    $mail->ClearAddresses();
}
Route::redirect("/users/email/sendUsers.php");
