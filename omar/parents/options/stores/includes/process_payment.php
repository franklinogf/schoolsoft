<?php
require_once '../../../../app.php';

use Classes\Session;
use Classes\Services\EvertecPayment;
use Classes\DataBase\DB;

Session::is_logged();

// Ensure this script is accessed via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// Load the Evertec Payment class
require_once 'EvertecPayment.php';

// Check if payment data is provided
$postData = json_decode(file_get_contents('php://input'), true);
if (!$postData) {
    echo json_encode(['success' => false, 'error' => 'No payment data provided']);
    exit;
}

// Initialize payment processor
$isDevelopmentMode = true; // Set to false for production
$evertecPrefix  = $postData['evertecPrefix'] ?? null; // Set your prefix here
$paymentProcessor = new EvertecPayment($isDevelopmentMode, $evertecPrefix);

// Generate transaction ID (use a more robust method in production)
$trxID = EvertecPayment::generateTransactionId();

// Common payment data
$paymentData = [
    'customerName' => $postData['customerName'] ?? '',
    'customerEmail' => $postData['customerEmail'] ?? '',
    'trxID' => $trxID,
    'trxDescription' => 'Payment for order at ' . date('Y-m-d H:i:s'),
    'trxAmount' => $postData['amount'],
    'address1' => $postData['address1'] ?? '',
    'city' => $postData['city'] ?? '',
    'state' => $postData['state'] ?? '',
    'zipcode' => $postData['zipcode'] ?? '00960'
];

$response = [];

// Process the payment based on payment method
if ($postData['paymentMethod'] === 'creditCard') {
    // Add credit card specific data
    $paymentData['cardNumber'] = $postData['cardNumber'];
    $paymentData['expDate'] = $postData['expMonth'] . substr($postData['expYear'], -2); // Format as MMYY
    $paymentData['cvv'] = $postData['cvv'];

    // Process credit card payment
    $response = $paymentProcessor->processCreditCard($paymentData);
} elseif ($postData['paymentMethod'] === 'ach') {
    // Add ACH specific data
    $paymentData['bankAccount'] = $postData['bankAccount'];
    $paymentData['routing'] = $postData['routing'];
    $paymentData['accType'] = $postData['accType'];

    // Process ACH payment
    $response = $paymentProcessor->processACH($paymentData);
} else {
    $response = ['success' => false, 'error' => 'Invalid payment method'];
}

// If payment is successful, save the order
if (isset($response['success']) && $response['success']) {
    // Get cart data
    $cart = $_SESSION['cart'] ?? [];

    if (!empty($cart)) {
        try {
            // Create order in database using the correct 'compras' table
            $orderId = DB::table('compras')->insertGetId([
                'accountID' => Session::id(),
                'trxID' => $trxID,
                'customerName' => $postData['customerName'] ?? '',
                'customerEmail' => $postData['customerEmail'] ?? '',
                'date' => date('Y-m-d H:i:s'),
                'subtotal' => $postData['amount'], // May need to adjust if you have tax calculation
                'ivu' => 0.00, // Set appropriate tax amount if available
                'total' => $postData['amount'],
                'deliveryTo' => $postData['address1'] ?? '',
                'shopping' => 1,
                'year' => date('Y'),
                'paid' => 1, // Payment is complete
                'payment_type' => $postData['paymentMethod'],
                'refNumber' => json_encode($response) // Store payment details in refNumber field
            ]);

            // Save order items using the correct 'compras_detalle' table
            foreach ($cart as $cart_key => $cart_item) {
                $product_id = $cart_item['product_id'];
                $option_index = $cart_item['option_index'];
                $quantity = $cart_item['quantity'];

                $product = DB::table('store_items')->where("id", $product_id)->first();

                if ($product) {
                    $price = $product->price;
                    $size = '';

                    // Get option details if exists
                    if ($option_index >= 0 && !empty($product->options)) {
                        $options = json_decode($product->options, true);
                        if (isset($options[$option_index])) {
                            $option = $options[$option_index];
                            $size = $option['name'];
                            // Replace price with option price if not null
                            if (!is_null($option['price'])) {
                                $price = $option['price'];
                            }
                        }
                    }

                    DB::table('compras_detalle')->insert([
                        'id_compra' => $orderId,
                        'item_name' => $product->name,
                        'amount' => $quantity,
                        'size' => $size,
                        'price' => $price,
                        'year' => date('Y'),
                        'orden' => $cart_key + 1 // Using cart key for order sequence
                    ]);
                }
            }

            // Clear the cart
            $_SESSION['cart'] = [];

            // Add order ID to response
            $response['orderId'] = $orderId;
        } catch (Exception $e) {
            $response['success'] = false;
            $response['error'] = 'Database error: ' . $e->getMessage();
        }
    }
}

// Return response as JSON
header('Content-Type: application/json');
echo json_encode($response);
