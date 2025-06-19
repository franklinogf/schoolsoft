<?php
require_once '../../../../app.php';

use Classes\Controllers\Teacher;
use Classes\Controllers\Student;
use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Mail;
use Classes\Route;
use Classes\Session;
use Classes\Util;

Session::is_logged();
$mail = new Mail();
$key = $_POST['key'];
$values = $_POST['values'];
$title = $_POST['title'];
$subject = $_POST['subject'];
$message = $_POST['message'];
$mail->Subject = $subject;
$emailsSent = 0;
$emailsError = 0;
$error = null;

if (isset($_FILES['file'])) {
    foreach ($_FILES['file']['name'] as $index => $fileName) {
        if ($fileName != '') {
            $file = $_FILES['file']['tmp_name'][$index];
            $mail->addAttachment($file, $fileName);
        }
    }
}

foreach ($values as $value) {
    $count = 0;
    if ($key === 'teachers') {
        $teacher = new Teacher($value);
        if ($teacher->email1 !== '') {
            $count++;
            $mail->addAddress($teacher->email1, "$teacher->nombre $teacher->apellidos");
        }
        if ($teacher->email2 !== '') {
            $count++;
            $mail->addAddress($teacher->email2, "$teacher->nombre $teacher->apellidos");
        }
    } else if ($key === 'students') {
        $student = new Student($value);
        $parents = DB::table('madre')->where('id', $student->id)->first();
        $emails = [
            ['correo' => $parents->email_p, 'nombre' => $parents->padre],
            ['correo' => $parents->email_m, 'nombre' => $parents->madre]
        ];
        foreach ($emails as $email) {
            if ($email['correo'] !== '') {
                $count++;
                $mail->addAddress($email['correo'], $email['nombre']);
            }
        }
    } else {
        $school = new School($value);
        $email = $school->info('correo');
        if ($email !== '') {
            $count++;
            $mail->addAddress($email, $value);
        }

    }



    $mail->isHTML(true);
    $mail->Body = "
   <!DOCTYPE html>
   <html lang='" . __LANG . "'>
   <head>
     <meta charset='UTF-8'>
     <meta name='viewport' content='width=device-width, initial-scale=1.0'>
     <title>{$title}</title>
   </head>
   <body>
   <center><h2>{$title}</h2></center>
   <br>
   <p>{$message}</p>  
   </body>
   </html>
   ";

    if ($count > 0) {
        if (!$mail->send()) {
            $emailsError++;
            $error = $mail->ErrorInfo;
        } else {
            $emailsSent++;
        }

    }
    $mail->ClearAddresses();
}

echo json_encode(["sent" => $emailsSent, "notSent" => $emailsError, 'error' => $error]);
