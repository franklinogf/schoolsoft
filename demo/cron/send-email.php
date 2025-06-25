<?php
require_once __DIR__ . '/../app.php';

use App\Models\EmailQueue;
use Classes\Email;

$emails = EmailQueue::pending()->limit(100)->get();


$count = 0;
foreach ($emails as $email) {
    Email::sendQueued($email);

    $count++;
    if ($count >= 2) {
        $count = 0;
        sleep(2);
    }
}


// /domains/schoolsoftpr.org/public_html/cron/demo/send-email.php