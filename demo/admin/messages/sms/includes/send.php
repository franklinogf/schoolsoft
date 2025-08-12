<?php
require_once '../../../../app.php';

use App\Enums\PhoneCompanyEnum;
use App\Models\Admin;
use App\Models\Student;
use App\Models\Teacher;
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
    if ($key === 'teachers') {
        $teacher = Teacher::find($value);
        $number = $teacher->cel;
        $comp = $teacher->comp;
        if ($number !== '' && !$comp !== '') {
            $company = PhoneCompanyEnum::tryFrom($comp);
            if ($company) {
                $cel = $company->createPhoneEmail($number);
                $mail->addAddress($cel, "$teacher->nombre $teacher->apellidos");
            }
        }
    } else if ($key === 'students') {
        $student = Student::find($value);
        $parents = $student->family;
        $numbers = [
            ['cel' => $parents->cel_m, 'nombre' => $parents->padre, "recibir" => $parents->re_mc_m, 'comp' => $parents->cel_com_m],
            ['cel' => $parents->cel_p, 'nombre' => $parents->madre, "recibir" => $parents->re_mc_p, 'comp' => $parents->cel_com_p]
        ];
        foreach ($numbers as $number) {
            if ($number['recibir'] === 'SI' && $number['cel'] !== '' && $number['comp'] !== '') {
                $company = PhoneCompanyEnum::tryFrom($number['comp']);
                if (!$company) {
                    continue;
                }
                $cel = Util::phoneAddress($number['cel'], $number['comp']);
                $mail->addAddress($cel, $number['nombre']);
            }
        }
    } else {
        $school = Admin::user($value)->first();
        $number = $school->telefono;
        $comp = $school->email4;
        if ($number !== '' && !$comp !== '') {
            $company = PhoneCompanyEnum::tryFrom($comp);

            if ($company) {
                $cel = $company->createPhoneEmail($number);
                $mail->addAddress($cel, $value);
            }
        }
    }



    $mail->msgHTML("
    <center><h2>{$title}</h2></center>
    <br>
    <p>{$message}</p> 
   ");
    $mail->Subject = $title;
    if (count($mail->getToAddresses()) > 0) {
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
