<?php


namespace Classes;

use App\Models\Admin;
use App\Models\EmailQueue;
use App\Models\School;
use App\Models\Teacher;

use Classes\Mail;
use Exception;
use Illuminate\Database\Capsule\Manager;

class Email
{

    protected ?string $from = null;
    protected ?array $to = null;
    protected string $subject = 'No Subject';
    protected string $body = '';
    protected array $attachments = [];
    protected string $replyTo = '';
    protected ?string $text = null;

    public static function from(string $email): self
    {
        return (new self())->setFrom($email);
    }

    public static function to(string|array $email): self
    {
        if (!is_array($email)) {
            $email = [$email];
        }
        return (new self())->setTo($email);
    }

    public function setFrom(string $email): self
    {
        $this->from = $email;
        return $this;
    }

    public function setTo(array $email): self
    {
        $this->to = $email;

        return $this;
    }

    public function subject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function body(string $body): self
    {
        $this->body = $body;
        return $this;
    }
    public function replyTo(string $email): self
    {
        $this->replyTo = $email;
        return $this;
    }

    public function text(?string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function attach(string|array $fileOrContent, ?string $filename = null): self
    {
        if (is_array($fileOrContent)) {
            foreach ($fileOrContent as $file) {
                if (isset($file['content']) && isset($file['filename'])) {
                    $this->attachments[] = $file;
                    continue;
                }
                $this->attach($file, $filename);
            }
            return $this;
        }

        if (filter_var($fileOrContent, FILTER_VALIDATE_URL)) {
            $this->attachments[] = [
                'path' => $fileOrContent,
                'filename' => $filename ?? basename($fileOrContent),
            ];
        } else {
            if (!$filename) {
                throw new Exception("Filename is required when attaching raw content.");
            }
            $this->attachments[] = [
                'content' => base64_encode(file_get_contents($fileOrContent)),
                'filename' => $filename,
            ];
        }
        return $this;
    }


    public function queue(): void
    {
        if (!$this->to  || count($this->to) === 0) {
            throw new Exception('No recipients specified');
        }

        $from = $this->from;

        if ($from === null) {
            $school = School::current();
            $from = $school->data['default_mail_from'];
        }

        $admin = Admin::primaryAdmin()->first();

        EmailQueue::create([
            'from' => $from,
            'to' => $this->to,
            'reply_to' => $this->replyTo,
            'message' => $this->body,
            'text' => $this->text,
            'subject' => $this->subject,
            'attachments' => $this->attachments,
            'user' => Session::id() ?? null,
            'year' => $admin->year(),
        ]);
    }

    public function send(): void
    {
        if (!$this->to  || count($this->to) === 0) {
            throw new Exception('No recipients specified');
        }

        $school = School::current();


        if ($school->data['default_mailer'] === 'resend') {
            try {

                if ($this->replyTo === null) {
                    $admin = Admin::primaryAdmin()->first();
                    $this->replyTo = $admin->correo;
                }

                $resend = \Resend::client($school->data['resend_key']);
                $resend->emails->send([
                    'from' => $school->data['default_mail_from'],
                    'to' => $this->to,
                    'reply_to' => $this->replyTo,
                    'subject' => $this->subject,
                    'html' => $this->body,
                    'text' => $this->text,
                    'attachments' => $this->attachments,
                ]);
            } catch (Exception $e) {
                throw new Exception('Error sending email via Resend: ' . $e->getMessage());
            }
        } else {
            $mail = new Mail();

            foreach ($this->to as $t) {
                $mail->addAddress($t);
            }
            if ($this->replyTo !== null) {
                $mail->addReplyTo($this->replyTo);
            }
            $mail->Subject = $this->subject;
            $mail->isHTML(true);
            $mail->Body = $this->body;
            if ($this->text !== null) {
                $mail->AltBody = $this->text;
            }
            foreach ($this->attachments as $attachment) {
                if (isset($attachment['content'])) {
                    $mail->addStringAttachment($attachment['content'], $attachment['filename']);
                    continue;
                }
                $mail->addAttachment($attachment);
            }

            if (!$mail->send()) {
                throw new Exception('Error sending email: ' . $mail->ErrorInfo);
            }
        }
    }

    public static function sendQueued(EmailQueue $queuedEmail): void
    {
        try {
            self::from($queuedEmail->from)
                ->to($queuedEmail->to)
                ->setFrom($queuedEmail->from)
                ->subject($queuedEmail->subject)
                ->body($queuedEmail->message)
                ->replyTo($queuedEmail->reply_to)
                ->text($queuedEmail->text)
                ->attach($queuedEmail->attachments)
                ->send();
            $queuedEmail->markAsSent();
        } catch (Exception $e) {
            $queuedEmail->markAsFailed($e->getMessage());
        }
    }
}
