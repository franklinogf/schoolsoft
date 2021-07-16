<?php

require_once '../../../../app.php';

use Classes\Controllers\School;
use Classes\File;
use Classes\Mail;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Route;
use Classes\Util;

Session::is_logged();
Server::is_post();
$teacher = new Teacher(Session::id());
$messageTitle = utf8_decode($_REQUEST['title']);
$messageBody = utf8_decode($_REQUEST['message']);
$messageSubject = utf8_decode($_REQUEST['subject']);
$schoolName = $teacher->info('colegio');
$mail = new Mail(true, "Teacher");
$mail->isHTML(true);
$mail->Subject = $messageSubject;
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
    <p>{$messageBody}</p>    
    <hr>
    </body>
    </html>
    ";
// this is for saving the messasge when it's marked on the form
if (isset($_POST['saveMessage'])) {
    DB::table('T_correos_guardados')->insert([
        'colegio' => $teacher->usuario,
        'id_profesor' => $teacher->id,
        'titulo' => $messageTitle,
        'asunto' => $messageSubject,
        'mensaje' => $messageBody,
        'year' => $teacher->info('year')
    ]);
}

// Add every file as an attachment
$file = new File();
foreach ($file->files as $file) {
    $mail->addAttachment($file->tmp_name, $file->name);
}

if (isset($_POST['student'])) {
    $ss = $_POST['student'];
    $student = new Student($ss);

    $mother = DB::table('madre')->select('madre,padre,email_m,email_p,cel_com_m,cel_m,cel_com_p,cel_p')
        ->where([
            ['id', $student->id]
        ])->first();

    if ($mother->email_m !== "") {
        $mail->addAddress($mother->email_m);
    }
    if ($mother->email_p !== "") {
        $mail->addAddress($mother->email_p);
    }
    // send email
    if (!$mail->send()) {
        Session::set('emailSent', "Error al enviar el correo electronico ($mail->ErrorInfo)");
    } else {
        Session::set('emailSent', 'Se ha enviado el correo electronico');
    }
    $mail->ClearAddresses();
} else if (isset($_POST['grade'])) {
    $grade = $_POST['grade'];
    $students = DB::table('padres')->where([
        ['id', $teacher->id],
        ['curso', $grade],
        ['year', $teacher->info('year')],
        ['baja', '']
    ])->get();
    $messagesSent = 0;
    $totalStudents = sizeof($students);
    foreach ($students as $student) {
        $mother = DB::table('madre')->select('madre,padre,email_m,email_p,cel_com_m,cel_m,cel_com_p,cel_p')
            ->where('id', $student->id2)->first();
        if ($mother->email_m !== "") {
            $mail->addAddress($mother->email_m);
        }
        if ($mother->email_p !== "") {
            $mail->addAddress($mother->email_p);
        }
        // send email
        if ($mail->send()) {
            $messagesSent++;
            $mail->ClearAddresses();
        }
    }
    if ($messagesSent === $totalStudents) {
        Session::set('emailSent', 'Se ha enviado el correo electronico a todos los estudiantes');
    } else {
        Session::set('emailSent', "Se ha enviado el correo electronico a $messagesSent de $totalStudents estudiantes ($mail->ErrorInfo)");
    }
} else if($_POST['admin']) {
    $adminUser = $_POST['admin'];
    $admin = new School($adminUser); //admin by user
    $mail->addAddress($admin->info('correo'));
    if (!$mail->send()) {
        Session::set('emailSent', "Error al enviar el correo electronico ($mail->ErrorInfo)");
    } else {
        Session::set('emailSent', 'Se ha enviado el correo electronico');
    }
    $mail->ClearAddresses();
}else if($_POST['deleteMessage']){
    $messageId = $_POST['deleteMessage'];
    DB::table('T_correos_guardados')->where('id',$messageId)->delete();
}


// send and sms notification when it's marked
if (isset($_POST['sms'])) {
    $compa1 = $mother->cel_com_m;
    $celular1 = $mother->cel_m;
    $compa2 = $mother->cel_com_p;
    $celular2 = $mother->cel_p;
    $mail->ClearAddresses();
    include_once('sendSms.php');
}

Route::redirect("/options/email/");
