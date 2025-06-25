<?php

namespace Classes;

// use Classes\Controllers\School;

use App\Models\Admin;
use App\Models\School;
use App\Models\Teacher;
// use Classes\Controllers\Teacher;
use Classes\DataBase\DB;
use Classes\Mail\PHPMailer;
use Classes\Mail\SMTP;

class Mail extends PHPMailer
{

    public function __construct($debug = false, $type = 'School')
    {
        $this->CharSet = 'UTF-8';
        $school = School::current();


        $admin = Admin::primaryAdmin()->first();
        // $isSMTP = $admin->host === 'E' ? true : false;
        $name = $admin->colegio;
        $replayToEmail = $admin->correo;
        $replayToName = $name;

        if ($type === 'Teacher') {
            $teacher = Teacher::find(Session::id())->first();
            $replayToEmail = $teacher->email1;
            $replayToName = $teacher->fullName;
        }

        // if ($isSMTP) {
        parent::__construct($debug);
        $this->isSMTP();
        $this->SMTPDebug = $debug ? SMTP::DEBUG_SERVER : SMTP::DEBUG_OFF;
        $this->Host = $school->data['smtp_host'];
        $this->SMTPAuth = true;
        $this->Username = $school->data['smtp_username'];
        $this->Password = $school->data['smtp_password'];
        $this->SMTPSecure = $school->data['smtp_encryption'];
        $this->Port = $school->data['smtp_port'];
        // }

        $this->setFrom($school->data['default_mail_from'], $name);
        $this->addReplyTo($replayToEmail, $replayToName);
    }

    public static function queue(string $from, string $replyTo, array $to, string $subject, string $message, string | null $text = null, array $attachments = [])
    {
        if (count($to) === 0) {
            return;
        }

        DB::table('email_queue')->insert([
            'from' => $from,
            'to' => json_encode($to),
            'reply_to' => $replyTo,
            'message' => $message,
            'text' => $text,
            'subject' => $subject,
            'attachments' => json_encode($attachments, JSON_UNESCAPED_UNICODE),
            'user' => Session::id() ?? null,
        ]);
    }
}
