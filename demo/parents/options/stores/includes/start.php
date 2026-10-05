<?php

use App\Models\Admin;
use App\Models\Store;
use App\Models\StoreItem;
use App\Models\StoreOrder;
use App\Services\PlacetoPayCheckout;
use App\Services\PlacetoPayPaymentProcessor;
use Classes\Route;
use Classes\Session;
use Dnetix\Redirection\Exceptions\PlacetoPayException;
use Illuminate\Database\Capsule\Manager;

require_once __DIR__ . '/../../../../app.php';

Session::is_logged();

$storeId = (int) ($_POST['store_id'] ?? 0);
// Route::redirect() is relative to the portal folder (/parents)
$storeUrl = '/options/stores/store.php?id=' . $storeId;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Route::redirect($storeUrl);
}

$email = trim($_POST['email'] ?? '');
$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');

$store = Store::find($storeId);
$cart = $_SESSION['cart'] ?? [];

if (! $store || empty($cart)) {
    Route::redirect($storeUrl . '&status=error&message=' . urlencode(__('placetopay.errors.invalid_payment')));
}

if ($error = PlacetoPayPaymentProcessor::validateBuyer($_POST)) {
    Route::redirect($storeUrl . '&status=error&message=' . urlencode($error));
}

try {
    $processor = new PlacetoPayPaymentProcessor();

    if ($pending = $processor->pendingFor(Session::id())) {
        Route::redirect($storeUrl . '&status=error&message=' . urlencode(__('placetopay.errors.pending_blocked', ['reference' => $pending->reference])));
    }
} catch (PlacetoPayException $e) {
    Route::redirect($storeUrl . '&status=error&message=' . urlencode($e->getMessage()));
}

// Recalculate the total server side from the cart, never trust the client amount
$lines = [];
$checkoutItems = [];
$total = 0;

foreach ($cart as $cartItem) {
    $product = StoreItem::find($cartItem['product_id']);

    if (! $product) {
        continue;
    }

    $price = $product->price;
    $size = '';
    $optionIndex = (int) $cartItem['option_index'];

    if ($optionIndex >= 0 && isset($product->options[$optionIndex])) {
        $option = $product->options[$optionIndex];
        $size = $option->name;
        if (! is_null($option->price)) {
            $price = $option->price;
        }
    }

    $quantity = (int) $cartItem['quantity'];
    $total += $price * $quantity;

    $lines[] = [
        'item_name' => $product->name,
        'amount' => $quantity,
        'size' => $size,
        'price' => $price,
    ];

    $checkoutItems[] = [
        'sku' => $product->id,
        'name' => $size !== '' ? "{$product->name} ({$size})" : $product->name,
        'qty' => $quantity,
        'price' => $price,
    ];
}

$total = round($total, 2);

if (empty($lines) || $total <= 0) {
    Route::redirect($storeUrl . '&status=error&message=' . urlencode(__('Su carrito está vacío')));
}

$reference = PlacetoPayCheckout::generateReference('STO', $store->prefix_code, Session::id());
$customerName = "{$firstName} {$lastName}";
$year = Admin::primaryAdmin()->year;

// Persist the order as unpaid before leaving the site, the buyer may come
// back to return.php without the session (and the cart) that created it.
$order = Manager::connection()->transaction(function () use ($reference, $customerName, $email, $total, $store, $year, $lines) {
    $order = StoreOrder::create([
        'accountID' => Session::id(),
        'trxID' => $reference,
        'customerName' => $customerName,
        'customerEmail' => $email,
        'date' => date('Y-m-d H:i:s'),
        'subtotal' => $total,
        'ivu' => 0.00,
        'total' => $total,
        'deliveryTo' => '',
        'shopping' => $store->prefix_code,
        'year' => $year,
        'paid' => 0,
        'refNumber' => $reference,
    ]);

    foreach ($lines as $line) {
        $order->items()->create($line + ['year' => $year]);
    }

    return $order;
});

try {
    $session = (new PlacetoPayCheckout())->createSession([
        'reference' => $reference,
        'description' => "Compra {$store->name}",
        'amount' => $total,
        'accountId' => Session::id(),
        'buyerEmail' => $email,
        'buyerName' => $firstName,
        'buyerSurname' => $lastName,
        'buyerMobile' => PlacetoPayPaymentProcessor::normalizeMobile($_POST['mobile']),
        'payableType' => 'store_order',
        'payableId' => (string) $order->id,
        'returnUrl' => school_url('parents/options/stores/includes/return.php?reference=' . $reference),
        'skipResult' => true,
    ], [], $checkoutItems);

    $message = $session->statusMessage();
} catch (PlacetoPayException $e) {
    $session = null;
    $message = $e->getMessage();
}

if ($session?->process_url) {
    header('Location: ' . $session->process_url);
    exit;
}

// The checkout never started, so the unpaid order can't be paid
Manager::connection()->transaction(function () use ($order) {
    $order->items()->delete();
    $order->delete();
});

$message ??= __('placetopay.errors.start_failed');

Route::redirect($storeUrl . '&status=error&message=' . urlencode($message));
