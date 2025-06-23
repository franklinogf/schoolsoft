<?php


namespace Classes;

use App\Models\Admin;
use App\Models\School;
use App\Models\Teacher;
// use Classes\Controllers\School;

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

        $admin = Admin::primaryAdmin()->first();

        DB::table('email_queue')->insert([
            'from' => $from,
            'to' => json_encode($to),
            'reply_to' => $replyTo,
            'message' => $message,
            'text' => $text,
            'subject' => $subject,
            'attachments' => json_encode($attachments, JSON_UNESCAPED_UNICODE),
            'user' => Session::id() ?? null,
            'year' => $admin->year(),
        ]);
    }

    public function send(array $to, string $subject, string $message, ?string $replyTo = null, ?string $text = null, array $attachments = []): array
    {
        if (count($to) === 0) {
            throw new \Exception('No recipients specified');
        }

        $school = School::current();


        if ($school->data['default_mailer'] === 'resend') {
            try {
                if ($replyTo === null) {
                    if ($this->type === 'School') {
                        $admin = Admin::primaryAdmin()->first();
                        $replyTo = $admin->correo;
                    } else {
                        $teacher = Teacher::where('id', Session::id())->first();
                        $replyTo = $teacher->email1;
                    }
                }
                $resend = \Resend::client($school->data['resend_key']);
                $resend->emails->send([
                    'from' => $school->data['default_mail_from'],
                    'to' => $to,
                    'reply_to' => $replyTo,
                    'subject' => $subject,
                    'html' => $message,
                    'text' => $text,
                    'attachments' => array_map(function ($file) {
                        if (isset($file['content'])) {
                            return [
                                'filename' => $file['filename'],
                                'content' => base64_encode($file['content']),
                            ];
                        }
                        return [
                            'path' => $file,
                        ];
                    }, $attachments),
                ]);
                return ['error' => false, 'message' => 'Email sent'];
            } catch (\Exception $e) {
                return ['error' => true, 'message' => $e->getMessage()];
            }
        } else {
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
                if (isset($attachment['content'])) {
                    $mail->addStringAttachment($attachment['content'], $attachment['filename']);
                    continue;
                }
                $mail->addAttachment($attachment);
            }

            if (!$mail->send()) {
                return ['error' => true, 'message' => $mail->ErrorInfo];
            }
            return ['error' => false, 'message' => 'Email sent'];
        }
    }
}
