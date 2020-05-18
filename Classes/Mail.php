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
      
      if ($type === 'School') {
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
      } else {
         $teacher = new Teacher(Session::id());
         $isSMTP = $teacher->host === 'E' ? true : false;
         $email = !$isSMTP ? $teacher->email1 : $teacher->info('email_smtp');
         $name = !$isSMTP ? $teacher->fullName() : $teacher->info('colegio');
         $replayToEmail = $teacher->email1;
         $replayToName = $teacher->fullName();
         $host = $teacher->info('host_smtp');
         $username = $teacher->info('email_smtp');
         $password = $teacher->info('clave_email');
         $port = $teacher->info('port');
      }

      if ($isSMTP) {
         parent::__construct(true);
         if ($debug)  $this->SMTPDebug = SMTP::DEBUG_SERVER;
         $this->isSMTP();
         $this->Host       = $host;
         $this->SMTPAuth   = true;
         $this->Username   = $username;
         $this->Password   = $password;
         $this->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
         $this->Port       =  $port;
      }

      $this->setFrom($email, $name);
      if ($this->replyTo)  $this->addReplyTo($replayToEmail, $replayToName);
   }
}
