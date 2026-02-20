<?php
require_once __DIR__ . '/../app.php';

use App\Models\EmailQueue;
use Classes\Email;

$emails = EmailQueue::query()->pending()->limit(100)->get();


$count = 0;
foreach ($emails as $email) {
    $result = Email::sendQueued($email);

    if ($result) {
        echo "Email sent to: " . implode(', ', $email->to) . "\n";
    } else {
        echo "Failed to send email to: " . implode(', ', $email->to) . "\n";
    }

    $count++;
    if ($count >= 2) {
        $count = 0;
        sleep(2);
    }
}


// /domains/schoolsoftpr.org/public_html/cron/demo/send-email.php