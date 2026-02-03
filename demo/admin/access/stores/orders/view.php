<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\Store;
use App\Models\StoreOrder;
use App\Models\StoreItem;
use App\Models\Student;
use Classes\Route;
use Classes\Session;

Session::is_logged();

$storeId = $_GET['store_id'] ?? null;
$orderId = $_GET['order_id'] ?? null;

if (!$storeId || !$orderId) Route::redirect('/access/stores/index.php');

$store = Store::find($storeId);
if (!$store) Route::redirect('/access/stores/index.php');

$order = StoreOrder::with('items')->find($orderId);
if (!$order || $order->shopping != $store->prefix_code) {
    Route::redirect("/access/stores/orders/index.php?store_id={$storeId}");
}

// Get store items for size options dropdown
$storeItems = StoreItem::where('store_id', $storeId)->get()->keyBy('name');

// Get students from the same family (same id field which is the family ID)
$students = Student::where('id', $order->accountID)
    ->orderBy('apellidos')
    ->orderBy('nombre')
    ->get();

// Get delivery student if deliveryTo contains a SS
$deliveryStudent = null;
if ($order->deliveryTo) {
    $deliveryStudent = Student::where('ss', $order->deliveryTo)->first();
}
$deliveryDisplay = $deliveryStudent
    ? "{$deliveryStudent->apellidos} {$deliveryStudent->nombre} ({$deliveryStudent->grado})"
    : ($order->deliveryTo ?: '-');
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __('Orden') . ' #' . $order->refNumber;
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
    <style>
        .order-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .order-info dt {
            font-weight: 600;
            color: #495057;
        }

        .order-info dd {
            margin-bottom: 0.75rem;
        }

        .edit-size-btn {
            cursor: pointer;
        }

        .size-display {
            display: inline-block;
            min-width: 60px;
        }

        .size-edit-form {
            display: none;
        }

        .size-edit-form.active {
            display: inline-flex;
            gap: 0.5rem;
            align-items: center;
        }

        .size-display.editing {
            display: none;
        }
    </style>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= __('Orden') ?> #<?= $order->refNumber ?></h1>

        <div class="mx-auto w-100">
            <div class="mb-3">
                <a class="btn btn-outline-primary" href="./index.php?store_id=<?= $store->id ?>">
                    <i class="fas fa-arrow-left"></i> <?= __('Volver a Órdenes') ?>
                </a>
            </div>

            <!-- Order Info Card -->
            <div class="order-info">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-sm-4"><?= __('Cliente') ?>:</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($order->customerName) ?></dd>

                            <dt class="col-sm-4"><?= __('Email') ?>:</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($order->customerEmail) ?></dd>

                            <dt class="col-sm-4"><?= __('Entregar a') ?>:</dt>
                            <dd class="col-sm-8">
                                <span id="deliveryDisplay"><?= htmlspecialchars($deliveryDisplay) ?></span>
                                <button type="button" class="btn btn-sm btn-outline-primary ml-2" id="editDeliveryBtn">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <div id="deliveryEditForm" style="display:none;" class="mt-2">
                                    <select class="form-control selectpicker" id="deliveryToSelect" data-live-search="true">
                                        <option value=""><?= __('Sin entregar (opcional)') ?></option>
                                        <?php foreach ($students as $student): ?>
                                            <option value="<?= $student->ss ?>" <?= $order->deliveryTo === $student->ss ? 'selected' : '' ?>>
                                                <?= "{$student->apellidos} {$student->nombre} ({$student->grado})" ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-success" id="saveDeliveryBtn">
                                            <i class="fas fa-check"></i> <?= __('Guardar') ?>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-secondary" id="cancelDeliveryBtn">
                                            <i class="fas fa-times"></i> <?= __('Cancelar') ?>
                                        </button>
                                    </div>
                                </div>
                            </dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-sm-4"><?= __('Fecha') ?>:</dt>
                            <dd class="col-sm-8"><?= $order->date->format('d/m/Y H:i') ?></dd>

                            <dt class="col-sm-4"><?= __('Tipo de Pago') ?>:</dt>
                            <dd class="col-sm-8"><?= $order->payment_type->label() ?></dd>

                            <dt class="col-sm-4"><?= __('Estado') ?>:</dt>
                            <dd class="col-sm-8">
                                <?php if ($order->paid): ?>
                                    <span class="badge badge-success"><?= __('Pagado') ?></span>
                                <?php else: ?>
                                    <span class="badge badge-warning"><?= __('Pendiente') ?></span>
                                <?php endif; ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Order Items Table -->
            <h4 class="mb-3"><?= __('Artículos de la Orden') ?></h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?= __('Artículo') ?></th>
                        <th class="text-center"><?= __('Opción') ?></th>
                        <th class="text-center"><?= __('Cantidad') ?></th>
                        <th class="text-right"><?= __('Precio Unit.') ?></th>
                        <th class="text-right"><?= __('Subtotal') ?></th>
                        <th class="text-center"><?= __('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order->items as $item):
                        $storeItem = $storeItems->get($item->item_name);
                        $options = $storeItem?->options ?? [];
                    ?>
                        <tr data-item-id="<?= $item->id ?>">
                            <td><?= htmlspecialchars($item->item_name) ?></td>
                            <td class="text-center">
                                <span class="size-display" data-item-id="<?= $item->id ?>">
                                    <?= htmlspecialchars($item->size ?: '-') ?>
                                </span>
                                <div class="size-edit-form" data-item-id="<?= $item->id ?>" data-item-name="<?= htmlspecialchars($item->item_name) ?>">
                                    <?php if (!empty($options)): ?>
                                        <select class="form-control form-control-sm size-select" style="width: auto;">
                                            <?php foreach ($options as $option):
                                                $optionPrice = $option->price ?? $storeItem->price ?? $item->price;
                                            ?>
                                                <option value="<?= htmlspecialchars($option->name) ?>"
                                                    data-price="<?= $optionPrice ?>"
                                                    <?= $item->size === $option->name ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($option->name) ?> ($<?= number_format($optionPrice, 2) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else: ?>
                                        <input type="text" class="form-control form-control-sm size-input" value="<?= htmlspecialchars($item->size) ?>" style="width: 100px;">
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-sm btn-success save-size-btn" title="<?= __('Guardar') ?>">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-secondary cancel-size-btn" title="<?= __('Cancelar') ?>">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="amount-display" data-item-id="<?= $item->id ?>">
                                    <?= $item->amount ?>
                                </span>
                                <div class="amount-edit-form" data-item-id="<?= $item->id ?>" style="display: none;">
                                    <input type="number" class="form-control form-control-sm amount-input" value="<?= $item->amount ?>" min="1" style="width: 70px;">
                                    <button type="button" class="btn btn-sm btn-success save-amount-btn" title="<?= __('Guardar') ?>">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-secondary cancel-amount-btn" title="<?= __('Cancelar') ?>">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="text-right">$<?= number_format($item->price, 2) ?></td>
                            <td class="text-right">$<?= number_format($item->price * $item->amount, 2) ?></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary edit-size-btn" data-item-id="<?= $item->id ?>" title="<?= __('Editar Opción') ?>">
                                    <i class="fas fa-tag"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary edit-amount-btn" data-item-id="<?= $item->id ?>" title="<?= __('Editar Cantidad') ?>">
                                    <i class="fas fa-sort-numeric-up"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-right"><?= __('Subtotal') ?>:</th>
                        <th class="text-right" id="order-subtotal">$<?= number_format($order->subtotal, 2) ?></th>
                        <th></th>
                    </tr>
                    <tr id="ivu-row" <?= $order->ivu <= 0 ? 'style="display:none"' : '' ?>>
                        <th colspan="4" class="text-right"><?= __('IVU') ?>:</th>
                        <th class="text-right" id="order-ivu">$<?= number_format($order->ivu, 2) ?></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-right"><?= __('Total') ?>:</th>
                        <th class="text-right" id="order-total">$<?= number_format($order->total, 2) ?></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::selectPicker('js');
    Route::sweetAlert();
    ?>
    <script>
        $(function() {
            // Initialize selectpicker
            $('.selectpicker').selectpicker();

            // Edit Delivery To functionality
            $('#editDeliveryBtn').on('click', function() {
                $('#deliveryDisplay').hide();
                $(this).hide();
                $('#deliveryEditForm').show();
                $('.selectpicker').selectpicker('refresh');
            });

            $('#cancelDeliveryBtn').on('click', function() {
                $('#deliveryEditForm').hide();
                $('#deliveryDisplay').show();
                $('#editDeliveryBtn').show();
            });

            $('#saveDeliveryBtn').on('click', function() {
                const deliveryTo = $('#deliveryToSelect').val();

                $.ajax({
                    url: './includes/update-delivery.php',
                    method: 'POST',
                    data: {
                        order_id: <?= $order->id ?>,
                        deliveryTo: deliveryTo
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert(response.error || '<?= __("Error al actualizar") ?>');
                        }
                    },
                    error: function() {
                        alert('<?= __("Error al actualizar") ?>');
                    }
                });
            });

            // Edit Size functionality
            $('.edit-size-btn').on('click', function() {
                const itemId = $(this).data('item-id');
                $(`.size-display[data-item-id="${itemId}"]`).addClass('editing');
                $(`.size-edit-form[data-item-id="${itemId}"]`).addClass('active');
            });

            $('.cancel-size-btn').on('click', function() {
                const form = $(this).closest('.size-edit-form');
                const itemId = form.data('item-id');
                form.removeClass('active');
                $(`.size-display[data-item-id="${itemId}"]`).removeClass('editing');
            });

            $('.save-size-btn').on('click', function() {
                const form = $(this).closest('.size-edit-form');
                const itemId = form.data('item-id');
                const itemName = form.data('item-name');
                const newSize = form.find('.size-select, .size-input').val();

                $.post('./includes/update-item.php', {
                    item_id: itemId,
                    field: 'size',
                    value: newSize,
                    item_name: itemName
                }, function(response) {
                    if (response.success) {
                        $(`.size-display[data-item-id="${itemId}"]`).text(newSize || '-').removeClass('editing');
                        form.removeClass('active');

                        // Update price and subtotal in row
                        if (response.data.itemPrice) {
                            $(`tr[data-item-id="${itemId}"] td:nth-child(4)`).text('$' + response.data.itemPrice);
                        }
                        if (response.data.itemSubtotal) {
                            $(`tr[data-item-id="${itemId}"] td:nth-child(5)`).text('$' + response.data.itemSubtotal);
                        }

                        // Update order totals if changed
                        if (response.data.order) {
                            $('#order-subtotal').text('$' + response.data.order.subtotal);
                            $('#order-ivu').text('$' + response.data.order.ivu);
                            $('#order-total').text('$' + response.data.order.total);

                            if (parseFloat(response.data.order.ivu) > 0) {
                                $('#ivu-row').show();
                            } else {
                                $('#ivu-row').hide();
                            }
                        }

                        Toast.fire({
                            icon: 'success',
                            title: '<?= __('Opción actualizada') ?>'
                        });
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response.message || '<?= __('Error al actualizar') ?>'
                        });
                    }
                }, 'json').fail(function() {
                    Toast.fire({
                        icon: 'error',
                        title: '<?= __('Error al actualizar') ?>'
                    });
                });
            });

            // Edit Amount functionality
            $('.edit-amount-btn').on('click', function() {
                const itemId = $(this).data('item-id');
                $(`.amount-display[data-item-id="${itemId}"]`).hide();
                $(`.amount-edit-form[data-item-id="${itemId}"]`).css('display', 'inline-flex');
            });

            $('.cancel-amount-btn').on('click', function() {
                const form = $(this).closest('.amount-edit-form');
                const itemId = form.data('item-id');
                form.hide();
                $(`.amount-display[data-item-id="${itemId}"]`).show();
            });

            $('.save-amount-btn').on('click', function() {
                const form = $(this).closest('.amount-edit-form');
                const itemId = form.data('item-id');
                const newAmount = form.find('.amount-input').val();

                if (newAmount < 1) {
                    Toast.fire({
                        icon: 'error',
                        title: '<?= __('La cantidad debe ser al menos 1') ?>'
                    });
                    return;
                }

                $.post('./includes/update-item.php', {
                    item_id: itemId,
                    field: 'amount',
                    value: newAmount
                }, function(response) {
                    console.log('Response:', response);
                    if (response.success) {
                        $(`.amount-display[data-item-id="${itemId}"]`).text(newAmount).show();
                        form.hide();

                        // Update item subtotal in row
                        if (response.data.itemSubtotal) {
                            $(`tr[data-item-id="${itemId}"] td:nth-child(5)`).text('$' + response.data.itemSubtotal);
                        }

                        // Update order totals
                        console.log('Order data:', response.data.order);
                        if (response.data.order) {
                            $('#order-subtotal').text('$' + response.data.order.subtotal);
                            $('#order-ivu').text('$' + response.data.order.ivu);
                            $('#order-total').text('$' + response.data.order.total);

                            // Show/hide IVU row
                            if (parseFloat(response.data.order.ivu) > 0) {
                                $('#ivu-row').show();
                            } else {
                                $('#ivu-row').hide();
                            }
                        }

                        Toast.fire({
                            icon: 'success',
                            title: '<?= __('Cantidad actualizada') ?>'
                        });
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response.message || '<?= __('Error al actualizar') ?>'
                        });
                    }
                }, 'json').fail(function() {
                    Toast.fire({
                        icon: 'error',
                        title: '<?= __('Error al actualizar') ?>'
                    });
                });
            });
        });
    </script>
</body>

</html>