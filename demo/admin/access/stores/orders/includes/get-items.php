<?php
require_once __DIR__ . '/../../../../../app.php';

use App\Models\StoreItem;
use Classes\Session;

Session::is_logged();

header('Content-Type: application/json');

$storeId = $_GET['store_id'] ?? null;

if (!$storeId) {
    echo json_encode(['error' => 'Store ID required']);
    exit;
}

$items = StoreItem::where('store_id', $storeId)->get();

echo json_encode($items);
