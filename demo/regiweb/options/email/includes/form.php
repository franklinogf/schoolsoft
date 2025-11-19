<?php

require_once __DIR__ . '/../../../../app.php';

use App\Models\Admin;
use App\Models\Student;
use App\Models\Teacher;
use Classes\Lang;
use Classes\Mail;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Email;

Session::is_logged();
Server::is_post();
$teacher = Teacher::find(Session::id());
$school  = Admin::primaryAdmin();
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

$messageTitle = $_REQUEST['title'];
$messageBody = $text = nl2br($_REQUEST['message']);
$schoolName = $school->colegio;
$subject = $_REQUEST['subject'];
$message = "
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
        'asunto' => $subject,
        'mensaje' => $messageBody,
        'year' => $school->year
    ]);
}

$files = [];
$to = [];

if (isset($_FILES['file'])) {
    $files = upload_attachment($_FILES['file'], 'messages/email');
}

if (isset($_POST['studentsAmount'])) {
    $students = Session::get('students', true);
    foreach ($students as $mt) {
        $to = [];
        $student = Student::find($mt);
        $family = $student->family;

        if ($family->email_m !== "") {
            $to[] = $family->email_m;
        }
        if ($family->email_p !== "") {
            $to[] = $family->email_p;
        }
        if (count($to) > 0) {
            try {
                $mail =  Email::to($to)
                    ->subject($subject)
                    ->body($message)
                    ->text($text);
                foreach ($files as $file) {
                    $mail->attach($file);
                }
                $mail->queue();
                // $emailsSent++;
            } catch (\Throwable $th) {
                $error = $th->getMessage();
                // $emailsError++;
            }
        }
        // else {
        //    $emailsError++;
        // }
    }
} else if (isset($_POST['classesAmount'])) {

    $classes = Session::get('classes', true);
    foreach ($classes as $class) {

        $students = DB::table('padres')->where([
            ['id', $teacher->id],
            ['curso', $class],
            ['year', $teacher->info('year')],
            ['baja', '']
        ])->get();
        foreach ($students as $student) {
            $to = [];
            $mother = DB::table('madre')->select('madre,padre,email_m,email_p,cel_com_m,cel_m,cel_com_p,cel_p')
                ->where('id', $student->id2)->first();

            if ($mother->email_m !== "") {
                $to[] = $mother->email_m;
            }
            if ($mother->email_p !== "") {
                $to[] = $mother->email_p;
            }
            if (count($to) > 0) {
                try {
                    $mail =  Email::to($to)
                        ->subject($subject)
                        ->body($message)
                        ->text($text);
                    foreach ($files as $file) {
                        $mail->attach($file);
                    }
                    $mail->queue();
                    // $emailsSent++;
                } catch (\Throwable $th) {
                    $error = $th->getMessage();
                    // $emailsError++;
                }
            }
            // else {
            //    $emailsError++;
            // }
        }
    }
} else if ($_POST['admin']) {
    $schoolUser = $_POST['admin'];
    $admin = Admin::user($schoolUser)->first();
    $to[] = $admin->correo;
    if (count($to) > 0) {
        try {
            $mail =  Email::to($to)
                ->subject($subject)
                ->body($message)
                ->text($text);
            foreach ($files as $file) {
                $mail->attach($file);
            }
            $mail->queue();
            // $emailsSent++;
        } catch (\Throwable $th) {
            $error = $th->getMessage();
            // $emailsError++;
        }
    }
    // else {
    //    $emailsError++;
    // }
} else if ($_POST['deleteMessage']) {
    $messageId = $_POST['deleteMessage'];
    DB::table('T_correos_guardados')->where('id', $messageId)->delete();
}



// send and sms notification when it's marked
// if (isset($_POST['sms'])) {
//     $companie1 = $mother->cel_com_m;
//     $cellPhone1 = $mother->cel_m;
//     $companie2 = $mother->cel_com_p;
//     $cellPhone2 = $mother->cel_p;
//     $mail->ClearAddresses();
//     include_once('sendSms.php');
// }

Route::redirect("/options/email/");
