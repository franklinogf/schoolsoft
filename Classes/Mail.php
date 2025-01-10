<?php

namespace Classes;

use Classes\Controllers\School;
use Classes\Controllers\Teacher;
use Classes\DataBase\DB;
use Classes\Mail\PHPMailer;
use Classes\Mail\SMTP;

class Mail extends PHPMailer
{
    public $replyTo = true;

    public function __construct($debug = false, $type = 'School')
    {
        $this->CharSet = 'UTF-8';

        if ($type === 'School' || $type === 'Teacher') {
            $school = new School();
            $isSMTP = $school->info('host') === 'E' ? true : false;
            $name = $school->info('colegio');
            $fromEmail = $replayToEmail = $school->info('correo');
            $replayToName = $name;
            if ($type === 'Teacher') {
                $teacher = new Teacher(Session::id());
                $replayToEmail = $teacher->email1;
                $replayToName = $teacher->fullName();
            }
            if ($isSMTP) {
                $host = $school->info('host_smtp');
                $fromEmail = $school->info('email_smtp');
                $password = $school->info('clave_email');
                $port = $school->info('port');
                parent::__construct(true);
                $this->isSMTP();
                $this->SMTPDebug = ($debug) ? SMTP::DEBUG_SERVER : SMTP::DEBUG_OFF;
                $this->Host = $host;
                $this->SMTPAuth = true;
                $this->Username = $fromEmail;
                $this->Password = $password;
                $this->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $this->Port = $port;
            }

            $this->setFrom($fromEmail, utf8_decode($name));
            if ($this->replyTo) {
                $this->addReplyTo($replayToEmail, utf8_decode($replayToName));
            }

        }
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
