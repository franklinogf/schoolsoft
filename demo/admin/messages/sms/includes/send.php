<?php
require_once '../../../../app.php';

use Classes\Controllers\Teacher;
use Classes\Controllers\Student;
use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Mail;
use Classes\Session;
use Classes\Util;

Session::is_logged();
$mail = new Mail();
$key = $_POST['key'];
$values = $_POST['values'];
$title = $_POST['title'];
$message = $_POST['message'];
$emailsSent = 0;
$emailsError = 0;
$error = null;


foreach ($values as $value) {
    $count = 0;
    if ($key === 'teachers') {
        $teacher = new Teacher($value);
        $number = $teacher->cel;
        $comp = $teacher->comp;
        if ($number !== '' && !$comp !== '') {
            $cel = Util::phoneAddress($number, $comp);
            $count++;
            $mail->addAddress($cel, $value);
        }
    } else if ($key === 'students') {
        $student = new Student($value);
        $parents = DB::table('madre')->where('id', $student->id)->first();
        $numbers = [
            ['cel' => $parents->cel_m, 'nombre' => $parents->padre, "recibir" => $parents->re_mc_m, 'comp' => $parents->cel_com_m],
            ['cel' => $parents->cel_p, 'nombre' => $parents->madre, "recibir" => $parents->re_mc_p, 'comp' => $parents->cel_com_p]
        ];
        foreach ($numbers as $number) {
            if ($number['recibir'] === 'SI' && $number['cel'] !== '' && $number['comp'] !== '') {
                $cel = Util::phoneAddress($number['cel'], $number['comp']);
                $count++;
                $mail->addAddress($cel, $number['nombre']);
            }
        }
    } else {
        $school = new School($value);
        $number = $school->info('telefono');
        $comp = $school->info('email4');
        if ($number !== '' && !$comp !== '') {
            $cel = Util::phoneAddress($number, $comp);
            $count++;
            $mail->addAddress($cel, $value);
        }

    }



    $mail->msgHTML("
    <center><h2>{$title}</h2></center>
    <br>
    <p>{$message}</p> 
   ");

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
