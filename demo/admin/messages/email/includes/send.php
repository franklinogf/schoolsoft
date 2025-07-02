<?php
require_once '../../../../app.php';

use App\Models\Admin;
use App\Models\Family;
use App\Models\Teacher;
use Classes\Email;
use Classes\Session;

Session::is_logged();

$key = $_POST['key'];
$values = $_POST['values'];
$title = $_POST['title'];
$subject = $_POST['subject'];
$message = $_POST['message'];
$files = [];

$emailsSent = 0;
$emailsError = 0;
$files = [];

if (isset($_FILES['file'])) {
    $files = upload_attachment($_FILES['file']);
}

$to = [];

foreach ($values as $value) {
    $count = 0;
    if ($key === 'teachers') {
        $teacher = Teacher::find($value);
        if ($teacher->email1 !== '') {
            $to[] = $teacher->email1;
        }
        if ($teacher->email2 !== '') {
            $to[] = $teacher->email2;
        }
    } else if ($key === 'students') {

        $family = Family::find($value);
        $emails = [
            ['correo' => $family->email_p, 'nombre' => $family->padre],
            ['correo' => $family->email_m, 'nombre' => $family->madre]
        ];
        foreach ($emails as $email) {
            if ($email['correo'] !== '') {
                $to[] = $email['correo'];
            }
        }
    } else {
        $admin = Admin::user($value)->first();
        $email = $admin->correo;
        if ($email !== '') {
            $to = [$email];
        }
    }
    $body = "<!DOCTYPE html>
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
   </html>";

    if (count($to) > 0) {
        try {
            $email =  Email::to($to)
                ->subject($subject)
                ->body($body)
                ->text($message);
            foreach ($files as $file) {
                $email->attach($file);
            }
            $email->queue();
            $emailsSent++;
        } catch (\Throwable $th) {
            $error = $th->getMessage();
            $emailsError++;
        }
    } else {
        $emailsError++;
    }
}

echo json_encode(["sent" => $emailsSent, "notSent" => $emailsError, "error" => $error ?? '']);
