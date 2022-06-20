<?php

require_once '../../../../app.php';

use Classes\File;
use Classes\Lang;
use Classes\Mail;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();
Server::is_post();
$teacher = new Teacher(Session::id());
$lang = new Lang([
    ["Error al enviar el correo electrónico", "Failed to send email"],
    ["Se ha enviado el correo electrónico", "Email has been sent"],
    ["Se ha enviado el correo electrónico a todos los estudiantes", "Email has been sent to all students"],
    ["Se ha enviado el correo electrónico a", "The email has been sent to"],
    ["de", "of"],
    ["estudiantes", "students"],
    ["No tiene correo electrónico registrado", "No email registered"],
    ["Ningun estudiante tiene correo electrónico registrado", "None of the students has an email registered"],
]);
$__lang =  __LANG;
$messageTitle = utf8_decode($_REQUEST['title']);
$messageBody = nl2br(utf8_decode($_REQUEST['message']));
$messageSubject = utf8_decode($_REQUEST['subject']);
$schoolName = $teacher->info('colegio');
$mail = new Mail(false, "Teacher");
$mail->isHTML(true);
$mail->Subject = $messageSubject;
$mail->Body    = "
    <!DOCTYPE html>
    <html lang='{$__lang}'>
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
if (isset($_POST['studentsAmount'])) {
    $studentsAmount = (int)$_POST['studentsAmount'];
    $students = Session::get('students', true);
    $messagesSent = 0;
    foreach ($students as $mt) {
        $emailAmount = 0;
        $student = new Student($mt);
        $mother = DB::table('madre')->select('madre,padre,email_m,email_p,cel_com_m,cel_m,cel_com_p,cel_p')
            ->where([
                ['id', $student->id]
            ])->first();

        if ($mother->email_m !== "") {
            $mail->addAddress($mother->email_m);
            $emailAmount++;
        }
        if ($mother->email_p !== "") {
            $mail->addAddress($mother->email_p);
            $emailAmount++;
        }

        if ($emailAmount > 0) {
            // send email
            if ($mail->send()) {
                $messagesSent++;
                $mail->ClearAddresses();
            }
        }
    }

    if ($messagesSent === $studentsAmount) {
        if ($studentsAmount === 1) {
            Session::set('emailSent', $lang->translation("Se ha enviado el correo electrónico"));
        } else {
            Session::set('emailSent', $lang->translation("Se ha enviado el correo electrónico a todos los estudiantes"));
        }
    } else if ($messagesSent > 0) {
        Session::set('emailSent', $lang->translation("Se ha enviado el correo electrónico a") . " $messagesSent " . $lang->translation("de") . " $studentsAmount " . $lang->translation("estudiantes"));
    } else {
        if ($studentsAmount === 1) {
            if ($emailAmount > 0) {
                Session::set('emailSent', $lang->translation("Error al enviar el correo electrónico") . " ($mail->ErrorInfo)");
            } else {
                Session::set('emailSent', $lang->translation("No tiene correo electrónico registrado"));
            }
        } else {

            Session::set('emailSent', $lang->translation("Ningun estudiante tiene correo electrónico registrado"));
        }
    }
} else if (isset($_POST['classesAmount'])) {
    // $classesAmount = (int)$_POST['classesAmount'];
    $classes = Session::get('classes', true);
    $messagesSent = [];
    $totalStudents = [];
    foreach ($classes as $class) {
        $messagesSent[$class] = 0;
        $students = DB::table('padres')->where([
            ['id', $teacher->id],
            ['curso', $class],
            ['year', $teacher->info('year')],
            ['baja', '']
        ])->get();
        $totalStudents[$class] = sizeof($students);
        foreach ($students as $student) {
            $emailAmount = 0;
            $mother = DB::table('madre')->select('madre,padre,email_m,email_p,cel_com_m,cel_m,cel_com_p,cel_p')
                ->where('id', $student->id2)->first();

            if ($mother->email_m !== "") {
                $mail->addAddress($mother->email_m);
                $emailAmount++;
            }
            if ($mother->email_p !== "") {
                $mail->addAddress($mother->email_p);
                $emailAmount++;
            }
            // send email
            if ($emailAmount > 0) {
                if ($mail->send()) {
                    $messagesSent[$class]++;
                    $mail->ClearAddresses();
                }
            }
        }
    }
    foreach ($classes as $class) {
        if ($messagesSent[$class] === $totalStudents[$class]) {
            $emailSentSession .= $lang->translation("Se ha enviado el correo electrónico a todos los estudiantes") . '<br>';
        } else if ($messagesSent[$class] > 0) {
            $emailSentSession .= $class . ': ' . $lang->translation("Se ha enviado el correo electrónico a") . " $messagesSent[$class] " . $lang->translation("de") . " $totalStudents[$class] " . $lang->translation("estudiantes") . "<br>";
        } else {
            $emailSentSession .= $class . ': ' . $lang->translation("Ningun estudiante tiene correo electrónico registrado") . "<br>";
        }
    }
    Session::set('emailSent', $emailSentSession);
} else if ($_POST['admin']) {
    $adminUser = $_POST['admin'];
    $admin = new School($adminUser); //admin by user
    $mail->addAddress($admin->info('correo'));
    if (!$mail->send()) {
        Session::set('emailSent', $lang->translation("Error al enviar el correo electrónico") . " ($mail->ErrorInfo)");
    } else {
        Session::set('emailSent', $lang->translation("Se ha enviado el correo electrónico"));
    }
    $mail->ClearAddresses();
} else if ($_POST['deleteMessage']) {
    $messageId = $_POST['deleteMessage'];
    DB::table('T_correos_guardados')->where('id', $messageId)->delete();
}


// send and sms notification when it's marked
if (isset($_POST['sms'])) {
    $companie1 = $mother->cel_com_m;
    $cellPhone1 = $mother->cel_m;
    $companie2 = $mother->cel_com_p;
    $cellPhone2 = $mother->cel_p;
    $mail->ClearAddresses();
    include_once('sendSms.php');
}

Route::redirect("/options/email/");
