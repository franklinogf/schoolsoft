<?php
require_once '../app.php';

use Classes\DataBase\DB;

$resend = Resend::client(__RESEND_KEY__);


$emails = DB::table('email_queue')->where(['status', 0])->whereRaw('LIMIT 100')->get();

if (count($emails) > 0) {
    $count = 0;
    foreach ($emails as $email) {
        try {

            $sentEmail = $resend->emails->send([
                'from' => $email->from,
                'to' => json_decode($email->to),
                'reply_to' => $email->reply_to,
                'subject' => $email->subject,
                'html' => $email->message,
                'text' => $email->text,
                // 'bcc'=>'amc@schoolsoftpr.com',
                'attachments' => array_map(function ($file) {
                    return [
                        'path' => $file
                    ];
                }, json_decode($email->attachments))
            ]);
            $timestamp = date('Y-m-d H:i:s');
            $mysqli->query("UPDATE `email_queue` SET `status` = '1', `sent_at`='$timestamp' WHERE id = $email->id");
        } catch (\Exception $e) {
            $mysqli->query("UPDATE `email_queue` SET `status` = '2', failed_reason = '{$e->getMessage()}' WHERE id = $email->id");
            exit('Error: ' . $e->getMessage());
        }

        // echo $sentEmail->toJson();
        $count++;
        if ($count >= LIMIT) {
            $count = 0;
            sleep(1);
        }
    }
    $result->free_result();
}

// /domains/schoolsoftpr.org/public_html/emails/cdls/{file_name}.php