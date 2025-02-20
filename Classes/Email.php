<?php


namespace Classes;

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Mail;

class Email
{

    public function __construct(public string $type = 'School') {}
    public static function queue(string $from, array $to, string $subject, string $message, string $replyTo = '', ?string $text = null, array $attachments = [])
    {
        if (count($to) === 0) {
            return;
        }
        $school = new School();
        DB::table('email_queue')->insert([
            'from' => $from,
            'to' => json_encode($to),
            'reply_to' => $replyTo,
            'message' => $message,
            'text' => $text,
            'subject' => $subject,
            'attachments' => json_encode($attachments, JSON_UNESCAPED_UNICODE),
            'user' => Session::id() ?? null,
            'year' => $school->year(),
        ]);
    }

    public function send(array $to, string $subject, string $message, ?string $replyTo = null, ?string $text = null, array $attachments = [])
    {
        if (count($to) === 0) {
            return;
        }

        if (__RESEND && defined('__RESEND_KEY__') && defined('__RESEND_KEY_OTHER__')) {
            try {
                $resend = \Resend::client(__RESEND_KEY__);
                $resend->emails->send([
                    'from' => __RESEND_KEY_OTHER__,
                    'to' => $to,
                    'reply_to' => $replyTo,
                    'subject' => $subject,
                    'html' => $message,
                    'text' => $text,
                    'attachments' => array_map(function ($file) {
                        return [
                            'path' => $file,
                        ];
                    }, $attachments),
                ]);
            } catch (\Exception $e) {
                throw new \Exception($e);
            }
        } else {
            try {
                $mail = new Mail(false, $this->type);

                foreach ($to as $t) {
                    $mail->addAddress($t);
                }
                if ($replyTo !== null) {
                    $mail->addReplyTo($t);
                }
                $mail->Subject = $subject;
                $mail->isHTML(true);
                $mail->Body = $message;
                if ($text !== null) {
                    $mail->AltBody = $text;
                }
                foreach ($attachments as $attachment) {
                    $mail->addAttachment($attachment);
                }

                $mail->send();
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }
    }
}
