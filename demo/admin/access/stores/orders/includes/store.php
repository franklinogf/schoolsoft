<?php
require_once __DIR__ . '/../../../../../app.php';

use App\Models\StoreOrder;
use App\Models\StoreOrderItem;
use App\Models\StoreItem;
use App\Models\Student;
use App\Services\SchoolService;
use Carbon\Carbon;
use Classes\Route;
use Classes\Session;

Session::is_logged();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Route::redirect('/access/stores/index.php');
}

// Get admin ID
$adminId = Session::id();

// Validate required fields
$storeId = $_POST['store_id'] ?? null;
$storePrefix = $_POST['store_prefix'] ?? null;
$accountID = $_POST['accountID'] ?? null;
$customerName = $_POST['customerName'] ?? '';
$customerEmail = $_POST['customerEmail'] ?? '';
$deliveryTo = $_POST['deliveryTo'] ?? '';
$paymentType = $_POST['payment_type'] ?? '';
$ivu = floatval($_POST['ivu'] ?? 0);
$items = $_POST['items'] ?? [];
$orderDatetimeInput = $_POST['order_datetime'] ?? '';
$timezone = config('app.timezone', 'America/Puerto_Rico');

// Validation
if (!$storeId || !$storePrefix || !$accountID || !$customerEmail || !$paymentType || !$orderDatetimeInput) {
    $_SESSION['error'] = __('Faltan campos requeridos');
    Route::redirect("/access/stores/orders/create.php?store_id={$storeId}");
}

if (empty($items)) {
    $_SESSION['error'] = __('Debe agregar al menos un artículo');
    Route::redirect("/access/stores/orders/create.php?store_id={$storeId}");
}

// Get student info
$student = Student::where('id', $accountID)->first();

if (!$student) {
    $_SESSION['error'] = __('Estudiante no encontrado');
    Route::redirect("/access/stores/orders/create.php?store_id={$storeId}");
}

try {
    $orderDate = Carbon::createFromFormat('Y-m-d\TH:i', $orderDatetimeInput, $timezone);
} catch (Exception $e) {
    $_SESSION['error'] = __('La fecha y hora indicada no es válida');
    Route::redirect("/access/stores/orders/create.php?store_id={$storeId}");
}

try {
    // Calculate subtotal
    $subtotal = 0;
    $orderItems = [];

    foreach ($items as $itemData) {
        $itemId = $itemData['item_id'] ?? null;
        $optionName = $itemData['option'] ?? '';
        $quantity = intval($itemData['quantity'] ?? 0);

        if (!$itemId || $quantity <= 0) {
            continue;
        }

        $storeItem = StoreItem::find($itemId);

        if (!$storeItem) {
            continue;
        }

        if (!$optionName) {
            $_SESSION['error'] = __('Cada artículo debe incluir una opción válida');
            Route::redirect("/access/stores/orders/create.php?store_id={$storeId}");
        }

        // Determine price based on option
        $price = $storeItem->getPriceForOption($optionName);

        if ($price === null) {
            $_SESSION['error'] = __('La opción seleccionada no es válida para uno de los artículos');
            Route::redirect("/access/stores/orders/create.php?store_id={$storeId}");
        }

        $subtotal += $price * $quantity;

        $orderItems[] = [
            'store_item_id' => $storeItem->id,
            'item_name' => $storeItem->name,
            'amount' => $quantity,
            'size' => $optionName,
            'price' => $price,
            'year' => SchoolService::getCurrentYear(),
        ];
    }

    if (empty($orderItems)) {
        $_SESSION['error'] = __('No se pudieron procesar los artículos');
        Route::redirect("/access/stores/orders/create.php?store_id={$storeId}");
    }

    // Calculate total
    $total = $subtotal + $ivu;

    // Generate reference number
    $refNumber = "MANUAL-{$adminId}-" . time();

    // Create order
    $order = StoreOrder::create([
        'accountID' => $student->id,
        'trxID' => $refNumber,
        'customerName' => $customerName,
        'customerEmail' => $customerEmail,
        'refNumber' => $refNumber,
        'date' => $orderDate,
        'subtotal' => round($subtotal, 2),
        'ivu' => round($ivu, 2),
        'total' => round($total, 2),
        'deliveryTo' => $deliveryTo,
        'shopping' => $storePrefix,
        'year' => SchoolService::getCurrentYear(),
        'paid' => 1,
        'payment_type' => $paymentType,
    ]);

    // Create order items
    foreach ($orderItems as $orderItem) {
        $order->items()->create($orderItem);
    }

    $_SESSION['success'] = __('Orden creada exitosamente');
    Route::redirect("/access/stores/orders/view.php?store_id={$storeId}&order_id={$order->id}");
} catch (Exception $e) {
    $_SESSION['error'] = __('Error al crear la orden') . ': ' . $e->getMessage();
    Route::redirect("/access/stores/orders/create.php?store_id={$storeId}");
}
