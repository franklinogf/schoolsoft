<?php

use App\Services\EvertecPayment;

require_once '../../../../app.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$postData = json_decode(file_get_contents('php://input'), true);
if (!$postData) {
    echo json_encode(['success' => false, 'error' => 'No payment data provided']);
    exit;
}



$evertec = new EvertecPayment();

$response =  $evertec->processCreditCard($postData);

// var_dump($response);
echo json_encode($response);
