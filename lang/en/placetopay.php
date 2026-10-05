<?php

return [
    'status' => [
        'approved' => 'Approved',
        'failed' => 'Failed',
        'approved_partial' => 'Partially approved',
        'rejected' => 'Rejected',
        'pending' => 'Pending',
        'pending_validation' => 'Pending validation',
        'refunded' => 'Refunded',
        'unknown' => 'Unknown',
    ],
    'form' => [
        'last_name' => 'Last name',
        'accept_terms' => 'I accept the',
        'terms_link' => 'terms and conditions',
        'pay_with_placetopay' => 'Pay with PlacetoPay',
        'pay' => 'Pay',
        'processing' => 'Processing...',
        'redirect_notice' => 'You will be redirected to PlacetoPay to complete the payment securely.',
    ],
    'validation' => [
        'terms' => 'You must accept the terms and conditions to continue.',
        'first_name' => 'First name is required.',
        'last_name' => 'Last name is required.',
        'email' => 'Please enter a valid email address.',
        'mobile' => 'Please enter a valid mobile number (10 digits).',
    ],
    'errors' => [
        'session_not_found' => 'The payment session was not found.',
        'start_failed' => 'The payment could not be started.',
        'invalid_deposit' => 'Invalid deposit data.',
        'invalid_payment' => 'Invalid payment data.',
        'pending_blocked' => 'You have a pending transaction (reference :reference). Wait for it to be resolved before making a new payment.',
    ],
    'pending' => [
        'warning' => 'You have a pending transaction (reference :reference). Wait for it to be resolved before making a new payment to avoid double charges.',
        'view_detail' => 'View details',
    ],
    'deposit' => [
        'title' => 'Cafeteria Deposit',
        'select_student' => 'Select the student who will receive the deposit',
        'select_student_error' => 'You must select a student to make the deposit.',
        'details' => 'Deposit details',
        'history_link' => 'View deposit history',
        'amount' => 'Amount to deposit',
        'amount_to' => 'to :name',
        'min_amount' => 'The minimum amount is :amount',
        'min_amount_invalid' => 'Please enter an amount equal to or greater than :amount',
        'success' => 'Deposit successful!',
        'new_balance' => 'The new balance is :amount',
        'failed' => 'The deposit could not be processed.',
    ],
    'result' => [
        'title' => 'Payment summary',
        'transaction' => 'Transaction',
        'concept' => 'Description',
        'amount' => 'Amount',
        'pending_notice' => 'Your payment is pending confirmation by the financial institution. We will let you know when its status changes; do not make the payment again.',
    ],
    'history' => [
        'title' => 'Payment history',
        'link' => 'View payment history',
        'subtitle' => 'Online payments',
        'empty' => 'You have no payments on record.',
    ],
    'terms' => [
        'title' => 'Terms and conditions',
        'intro' => 'These terms govern the online payments (cafeteria deposits and store purchases) made to :school through this portal.',
        'processing' => [
            'title' => '1. Payment processing',
            'body' => 'Payments are processed by PlacetoPay (Evertec). When you continue you will be redirected to their secure platform; the school does not receive or store your card or bank account details.',
        ],
        'buyer' => [
            'title' => '2. Buyer information',
            'body' => 'To process the payment, your first name, last name, email address and mobile number are sent to PlacetoPay. You are responsible for this information being correct.',
        ],
        'expiration' => [
            'title' => '3. Time to complete the payment',
            'body' => 'The payment session expires after :minutes minutes. If you do not complete the payment within that time you will need to start it again.',
        ],
        'pending' => [
            'title' => '4. Pending payments',
            'body' => 'Some payments (for example ACH) may remain pending confirmation by the financial institution. While you have a pending payment you will not be able to start a new one, to avoid double charges. The status is updated automatically.',
        ],
        'crediting' => [
            'title' => '5. Crediting',
            'body' => 'Cafeteria deposits are credited to the student\'s balance and purchases are marked as paid only when the payment is approved. You can check the status of all your payments in the payment history.',
        ],
        'refunds' => [
            'title' => '6. Refunds and reversals',
            'body' => 'Refund requests must be directed to the school administration. When a payment is refunded or reversed, the amount is deducted from the student\'s cafeteria balance or the purchase is marked as unpaid.',
        ],
        'privacy' => [
            'title' => '7. Privacy',
            'body' => 'The information provided is used only to process and record your payments, and is not shared with third parties other than the payment processor.',
        ],
        'contact' => [
            'title' => '8. Contact',
            'body' => 'For questions about your payments write to :email.',
        ],
    ],
];
