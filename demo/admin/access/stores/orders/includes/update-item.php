<?php
require_once __DIR__ . '/../../../../../app.php';

use App\Models\StoreOrderItem;
use App\Models\StoreOrder;
use App\Models\StoreItem;
use App\Models\Scopes\YearScope;
use Classes\Session;

Session::is_logged();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$itemId = $_POST['item_id'] ?? null;
$field = $_POST['field'] ?? null;
$value = $_POST['value'] ?? null;

if (!$itemId || !$field) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Only allow specific fields to be updated
$allowedFields = ['size', 'amount'];
if (!in_array($field, $allowedFields)) {
    echo json_encode(['success' => false, 'message' => 'Invalid field']);
    exit;
}

$item = StoreOrderItem::find($itemId);

if (!$item) {
    echo json_encode(['success' => false, 'message' => 'Item not found']);
    exit;
}

// Validate amount
if ($field === 'amount') {
    $value = intval($value);
    if ($value < 1) {
        echo json_encode(['success' => false, 'message' => 'Amount must be at least 1']);
        exit;
    }
}

// Get new price if size/option is being changed
$newPrice = null;
if ($field === 'size') {
    $itemName = $_POST['item_name'] ?? $item->item_name;
    $storeItem = StoreItem::where('name', $itemName)->first();
    if ($storeItem) {
        $newPrice = $storeItem->getPriceForOption($value);
    }
}

try {
    $item->$field = $value;
    if ($newPrice !== null) {
        $item->price = $newPrice;
    }
    $item->save();

    // Recalculate order totals if amount or size (price) was changed
    $orderData = null;
    if ($field === 'amount' || $newPrice !== null) {
        $order = StoreOrder::withoutGlobalScope(YearScope::class)->find($item->id_compra);
        if ($order) {
            // Calculate new subtotal from all items using a fresh query
            $subtotal = StoreOrderItem::query()->where('id_compra', $order->id)
                ->selectRaw('SUM(price * amount) as total')
                ->value('total') ?? 0;
            $subtotal = (float) $subtotal;

            // Recalculate IVU if it was originally applied
            $originalSubtotal = (float) $order->subtotal;
            $originalIvu = (float) $order->ivu;
            $ivuRate = $originalSubtotal > 0 ? ($originalIvu / $originalSubtotal) : 0;
            $ivu = $subtotal * $ivuRate;
            $total = $subtotal + $ivu;

            $order->update([
                'subtotal' => round($subtotal, 2),
                'ivu' => round($ivu, 2),
                'total' => round($total, 2),
            ]);

            $orderData = [
                'subtotal' => number_format($subtotal, 2),
                'ivu' => number_format($ivu, 2),
                'total' => number_format($total, 2),
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Updated successfully',
        'data' => [
            'id' => $item->id,
            'field' => $field,
            'value' => $value,
            'order' => $orderData,
            'itemPrice' => number_format($item->price, 2),
            'itemSubtotal' => number_format($item->price * $item->amount, 2),
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error updating item: ' . $e->getMessage()]);
}
