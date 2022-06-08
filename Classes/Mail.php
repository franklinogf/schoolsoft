<?php

namespace Classes;

use Classes\Mail\SMTP;
use Classes\Mail\PHPMailer;
use Classes\Controllers\School;
use Classes\Controllers\Teacher;
use Classes\Mail\Exception;

class Mail extends PHPMailer
{
   public $replyTo = true;

   public function __construct($debug = false, $type = 'School')
   {

      if ($type === 'School' || $type === 'Teacher') {
         $school = new School();
         $isSMTP = $school->info('host') === 'E' ? true : false;
         $email = $school->info('correo');
         $name = $school->info('colegio');
         $replayToEmail = $email;
         $replayToName = $name;
         $host = $school->info('host_smtp');
         $username = $school->info('email_smtp');
         $password = $school->info('clave_email');
         $port = $school->info('port');
         if ($type === 'Teacher') {
            $teacher = new Teacher(Session::id());
            $replayToEmail = $teacher->email1;
            $replayToName = $teacher->fullName();
         }

         if ($isSMTP) {
            parent::__construct(true);
            $this->SMTPDebug = ($debug) ? SMTP::DEBUG_SERVER : SMTP::DEBUG_OFF;
            $this->isSMTP();
            $this->Host       = $host;
            $this->SMTPAuth   = true;
            $this->Username   = $username;
            $this->Password   = $password;
            $this->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $this->Port       =  $port;
         }

         $this->setFrom($email, utf8_decode($name));
         if ($this->replyTo)  $this->addReplyTo($replayToEmail, utf8_decode($replayToName));
      }
   }
}
