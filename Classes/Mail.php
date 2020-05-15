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
      parent::__construct(true);
      if ($type === 'School') {
         $school = new School();
         $email = $school->info('correo');
         $name = $school->info('colegio');
         $isSMTP = $school->info('host') === 'E' ? true : false;
         $host = $school->info('host_smtp');
         $username = $school->info('email_smtp');
         $password = $school->info('clave_email');
         $port = $school->info('port');
      } else {
         $teacher = new Teacher(Session::id());
         $email = $teacher->email1;
         $name = $teacher->fullName();
         $isSMTP = $teacher->host === 'E' ? true : false;
         $host = $teacher->host_smtp;
         $username = $teacher->email_smtp;
         $password = $teacher->clave_email;
         $port = $teacher->port;
      }

      $this->setFrom($email, $name);
      if ($this->replyTo)  $this->addReplyTo($email, $name);
      if ($isSMTP) {
         if ($debug)  $this->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
         $this->isSMTP();                                            // Send using SMTP
         $this->Host       = $host;          // Set the SMTP server to send through
         $this->SMTPAuth   = true;                                   // Enable SMTP authentication
         $this->Username   = $username;                     // SMTP username
         $this->Password   = $password;                               // SMTP password
         $this->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
         $this->Port       =  $port;
      }
   }
}
