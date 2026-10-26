<?php

return [
    'email' => [
        'reminder_subject' => 'Automatic payment notice - :school',
        'reminder_title' => 'Your automatic payment is coming up',
        'reminder_intro' => 'On :date your card :card will be charged automatically.',
        'reminder_note' => 'The final amount may change if your balances change before that date.',
        'failed_subject' => 'We could not process your automatic payment - :school',
        'failed_title' => 'We could not process your automatic payment',
        'failed_intro' => 'The charge to your card :card could not be completed.',
        'failed_retry' => 'We will try again on :date.',
        'failed_final' => 'The automatic payment has been stopped. Register a new card to turn it back on.',
        'reason' => 'Reason',
        'student' => 'Student',
        'concept' => 'Concept',
        'amount' => 'Amount',
        'estimated_total' => 'Estimated total',
        'manage' => 'Manage automatic payment',
        'footer' => 'This is an automated message from :school.',
    ],
];
