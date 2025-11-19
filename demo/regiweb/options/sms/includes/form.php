<?php

require_once __DIR__ . '/../../../../app.php';

use Classes\Lang;
use Classes\Mail;
use Classes\Util;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\Mail\SMTP;
use Classes\DataBase\DB;
use Classes\Mail\PHPMailer;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();
Server::is_post();
$teacher = new Teacher(Session::id());
$year = $teacher->info('year');
$lang = new Lang([
    ["Error al enviar el mensaje de texto", "Failed to send sms"],
    ["Se ha enviado el mensaje de texto", "Sms has been sent"],
    ["Se ha enviado el mensaje de texto a todos los estudiantes", "Sms has been sent to all students"],
    ["Se ha enviado el mensaje de texto a", "The sms has been sent to"],
    ["de", "of"],
    ["estudiantes", "students"],
    ["No tiene número de celular registrado", "No cell phone number registered"],
    ["Ningun estudiante tiene número de celular registrado", "None of the students has an cell phone number registered"],
]);
$messageTitle = utf8_decode($_REQUEST['title']);
$messageBody = nl2br(utf8_decode($_REQUEST['message']));
$schoolName = $teacher->info('colegio');
$sms = new Mail(false, "Teacher");
$sms->msgHTML("{$messageTitle}\n\n{$messageBody}");
// this is for saving the messasge when it's marked on the form
if (isset($_POST['saveMessage'])) {
    DB::table('T_sms_guardados')->insert([
        'enviado_por' => $teacher->id,
        'titulo' => $messageTitle,
        'mensaje' => $messageBody,
        'fecha' => Util::date(),
        'hora' => Util::time(),
        'year' => $year
    ]);
}



if (isset($_POST['studentsAmount'])) {
    $studentsAmount = (int)$_POST['studentsAmount'];
    $students = Session::get('students', true);
    $messagesSent = 0;
    foreach ($students as $mt) {
        $cellPhoneAmount = 0;
        $student = new Student($mt);
        $mother = DB::table('year')
            ->select('madre.re_mc_p, madre.re_mc_m, madre.cel_m, madre.cel_p, madre.cel_com_m, madre.cel_com_p, madre.madre, madre.padre')
            ->join('madre', 'year.id', '=', 'madre.id')
            ->where([
                ['year.year', $year],
                ['year.id', $student->id]
            ])->first();
        if ($mother->re_mc_m === "SI" && $mother->cel_m !== "") {
            $sms->addAddress(Util::phoneAddress($mother->cel_m, $mother->cel_com_m));
            $cellPhoneAmount++;
        }
        if ($mother->re_mc_p === "SI" && $mother->cel_p !== "") {
            $sms->addAddress(Util::phoneAddress($mother->cel_p, $mother->cel_com_p));
            $cellPhoneAmount++;
        }
        if ($cellPhoneAmount > 0) {
            if ($sms->send()) {
                $messagesSent++;
                $sms->ClearAddresses();
            }
        }
    }
    if ($messagesSent === $studentsAmount) {
        if ($studentsAmount === 1) {
            Session::set('emailSent', $lang->translation("Se ha enviado el mensaje de texto"));
        } else {
            Session::set('emailSent', $lang->translation("Se ha enviado el mensaje de texto a todos los estudiantes"));
        }
    } else if ($messagesSent > 0) {
        Session::set('emailSent', $lang->translation("Se ha enviado el mensaje de texto a") . " $messagesSent " . $lang->translation("de") . " $studentsAmount " . $lang->translation("estudiantes"));
    } else {
        if ($studentsAmount === 1) {
            if ($cellPhoneAmount > 0) {
                Session::set('emailSent', $lang->translation("Error al enviar el mensaje de texto") . " ($mail->ErrorInfo)");
            } else {
                Session::set('emailSent', $lang->translation("No tiene número de celular registrado"));
            }
        } else {
            Session::set('emailSent', $lang->translation("Ningun estudiante tiene número de celular registrado"));
        }
    }
} else if (isset($_POST['classesAmount'])) {
    // $classesAmount = (int)$_POST['classesAmount'];
    $classes = Session::get('classes',true);
    $messagesSent = [];
    $totalStudents = [];
    foreach ($classes as $class) {
        $messagesSent[$class] = 0;
        $students = DB::table('padres')->where([
            ['id', $teacher->id],
            ['curso', $class],
            ['year', $year],
            ['baja', '']
        ])->get();
        $totalStudents[$class] = sizeof($students);
        foreach ($students as $student) {
            $cellPhoneAmount = 0;
            $mother = DB::table('year')
                ->select('madre.re_mc_p, madre.re_mc_m, madre.cel_m, madre.cel_p, madre.cel_com_m, madre.cel_com_p, madre.madre, madre.padre')
                ->join('madre', 'year.id', '=', 'madre.id')
                ->where([
                    ['year.year', $year],
                    ['year.id', $student->id2]
                ])->first();
            if ($mother->re_mc_m === "SI" && $mother->cel_m !== "") {
                $sms->addAddress(Util::phoneAddress($mother->cel_m, $mother->cel_com_m));
                $cellPhoneAmount++;
            }
            if ($mother->re_mc_p === "SI" && $mother->cel_p !== "") {
                $sms->addAddress(Util::phoneAddress($mother->cel_p, $mother->cel_com_p));
                $cellPhoneAmount++;
            }
            if ($cellPhoneAmount > 0) {
                if ($sms->send()) {
                    $messagesSent[$class]++;
                    $sms->ClearAddresses();
                }
            }
        }
    }
    foreach ($classes as $class) {
        if ($messagesSent[$class] === $totalStudents[$class]) {
            $smsSentSession .= $lang->translation("Se ha enviado el mensaje de texto a todos los estudiantes") . '<br>';
        } else if ($messagesSent[$class] > 0) {
            $smsSentSession .= $class . ': ' . $lang->translation("Se ha enviado el mensaje de texto a") . " $messagesSent[$class] " . $lang->translation("de") . " $totalStudents[$class] " . $lang->translation("estudiantes") . "<br>";
        } else {
            $smsSentSession .= $class . ': ' . $lang->translation("Ningun estudiante tiene número de celular registrado") . "<br>";
        }
    }
    Session::set('emailSent', $smsSentSession);
} else if ($_POST['phoneNumber']) {
    $phoneNumber = $_POST['phoneNumber'];
    $phoneCompany = $_POST['phoneCompany'];
    $sms->addAddress(Util::phoneAddress($phoneNumber, $phoneCompany));

    if (!$sms->send()) {
        Session::set('smsSent', "Error al enviar el mensaje de texto ($sms->ErrorInfo)");
    } else {
        Session::set('smsSent', 'Se ha enviado el mensaje de texto');
    }
    $sms->ClearAddresses();
} else if ($_POST['deleteMessage']) {

    $messageId = $_POST['deleteMessage'];
    DB::table('T_sms_guardados')->where('id', $messageId)->delete();
}



Route::redirect("/options/sms/");
