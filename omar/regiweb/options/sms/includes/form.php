<?php

require_once '../../../../app.php';

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
$messageTitle = utf8_decode($_REQUEST['title']);
$messageBody = utf8_decode($_REQUEST['message']);
$schoolName = $teacher->info('colegio');
$mail = new Mail(false, "Teacher");
$mail->isHTML(true);
$mail->Body    = "{$messageTitle}\n\n{$messageBody}";
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

if (isset($_POST['student'])) {
    $ss = $_POST['student'];
    $student = new Student($ss);


    $mother = DB::table('year')
        ->select('madre.re_mc_p, madre.re_mc_m, madre.cel_m, madre.cel_p, madre.cel_com_m, madre.cel_com_p, madre.madre, madre.padre')
        ->join('madre', 'year.id', '=', 'madre.id')
        ->where([
            ['year.year', $year],
            ['year.id', $student->id]
        ])->first();

    if ($mother->re_mc_m == "SI") {
        $mail->addAddress(phoneAddress($mother->cel_m, $mother->cel_com_m));
    }
    if ($mother->re_mc_p == "SI") {
        $mail->addAddress(phoneAddress($mother->cel_p, $mother->cel_com_p));
    }
    // send email
    if (!$mail->send()) {
        Session::set('smsSent', "Error al enviar el mensaje de texto ($mail->ErrorInfo)");
        echo $mail->ErrorInfo;
    } else {
        Session::set('smsSent', "Se ha enviado el mensaje de texto");
    }
    $mail->ClearAddresses();
} else if (isset($_POST['grade'])) {
    $grade = $_POST['grade'];
    $students = DB::table('padres')->where([
        ['id', $teacher->id],
        ['curso', $grade],
        ['year', $year],
        ['baja', '']
    ])->get();
    $messagesSent = 0;
    $totalStudents = sizeof($students);
    foreach ($students as $student) {
        $mother = DB::table('padres')
            ->select('padres.curso,madre.id,padres.id2,madre.re_mc_p, madre.re_mc_m, madre.cel_m, madre.cel_p, madre.cel_com_m,
        madre.cel_com_p, madre.madre, madre.padre')
            ->join('madre', 'padres.id2', '=', 'madre.id')
            ->where([
                ['padres.curso', $grade],
                ['padres.id', $teacher->id],
                ['padres.year', $year]
            ])->first();
        if ($mother->re_mc_m == "SI") {
            $mail->addAddress(phoneAddress($mother->cel_m, $mother->cel_com_m));
        }
        if ($mother->re_mc_p == "SI") {
            $mail->addAddress(phoneAddress($mother->cel_p, $mother->cel_com_p));
        }
        // send email
        if ($mail->send()) {
            $messagesSent++;
            $mail->ClearAddresses();
        }
    }
    if ($messagesSent === $totalStudents) {
        Session::set('smsSent', 'Se ha enviado el mensaje de texto a todos los estudiantes');
    } else {
        Session::set('smsSent', "Se ha enviado el mensaje de texto a $messagesSent de $totalStudents estudiantes ($mail->ErrorInfo)");
    }
} else if ($_POST['phoneNumber']) {
    $sms = new PHPMailer(true);
    $isSMTP = $teacher->host === 'E' ? true : false;
    $email = !$isSMTP ? $teacher->email1 : $teacher->info('email_smtp');
    $name = !$isSMTP ? $teacher->fullName() : $teacher->info('colegio');
    $replayToEmail = $teacher->email1;
    $replayToName = $teacher->fullName();
    $host = $teacher->info('host_smtp');
    $username = $teacher->info('email_smtp');
    $password = $teacher->info('clave_email');
    $port = $teacher->info('port');
    $phoneNumber = $_POST['phoneNumber'];
    $phoneCompany = $_POST['phoneCompany'];

    if ($isSMTP) {
        parent::__construct(true);
        if ($debug)  $sms->SMTPDebug = SMTP::DEBUG_SERVER;
        $sms->isSMTP();
        $sms->Host       = $host;
        $sms->SMTPAuth   = true;
        $sms->Username   = $username;
        $sms->Password   = $password;
        $sms->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $sms->Port       =  $port;
    }
    $sms->Body    = "{$messageTitle}\n\n{$messageBody}";
    $sms->setFrom($email, utf8_decode($name));
    $sms->addAddress(phoneAddress($phoneNumber, $phoneCompany));
    
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


function phoneAddress($phone, $company)
{
    $phoneAddress = preg_replace('/[^\d]/', '', $phone);
    if ($company == "AT&T") {
        $phoneAddress .= "@txt.att.net";
    }
    if ($company == "T-Movil") {
        $phoneAddress .= "@tmomail.net";
    }
    if ($company == "Sprint") {
        $phoneAddress .= "@messaging.sprintpcs.com";
    }
    if ($company == "Open M.") {
        $phoneAddress .= "@email.openmobilepr.com";
    }
    if ($company == "Claro") {
        $phoneAddress .= "@mms.claropr.com";
    }
    if ($company == "Verizon") {
        $phoneAddress .= "@vtext.com";
    }
    if ($company == "Suncom") {
        $phoneAddress .= "@tms.suncom.com";
    }
    if ($company == "Boost") {
        $phoneAddress .= "@myboostmobile.com";
    }
    return $phoneAddress;
}
Route::redirect("/options/sms/");
