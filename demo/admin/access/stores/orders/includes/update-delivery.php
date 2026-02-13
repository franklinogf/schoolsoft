<?php
require_once __DIR__ . '/../../../../../app.php';

use App\Models\StoreOrder;
use Classes\Session;

Session::is_logged();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$orderId = $_POST['order_id'] ?? null;
$deliveryTo = $_POST['deliveryTo'] ?? '';

if (!$orderId) {
    echo json_encode(['success' => false, 'error' => 'Order ID required']);
    exit;
}

$order = StoreOrder::find($orderId);

if (!$order) {
    echo json_encode(['success' => false, 'error' => 'Order not found']);
    exit;
}

try {
    $order->update([
        'deliveryTo' => $deliveryTo
    ]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
