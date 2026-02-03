<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\Store;
use App\Models\StoreOrder;
use Classes\Route;
use Classes\Session;

Session::is_logged();

$storeId = $_GET['store_id'] ?? null;

if (!$storeId) Route::redirect('/access/stores/index.php');

$store = Store::find($storeId);

if (!$store) Route::redirect('/access/stores/index.php');

$orders = StoreOrder::where('shopping', $store->prefix_code)
    ->with('items')
    ->orderBy('date', 'desc')
    ->get();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __('Órdenes') . ' - ' . $store->name;
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::includeFile('/includes/datatable-css.php', true);
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= __('Órdenes') ?> - <?= $store->name ?></h1>
        <div class="mx-auto w-100">
            <a class="btn btn-outline-primary mb-3" href="../index.php"><?= __('Volver a Tiendas') ?></a>
            <a class="btn btn-outline-secondary mb-3" href="../edit.php?id=<?= $store->id ?>"><?= __('Editar Tienda') ?></a>
            <a class="btn btn-success mb-3" href="./create.php?store_id=<?= $store->id ?>">
                <i class="fa fa-plus"></i> <?= __('Nueva Orden') ?>
            </a>

            <table class="table table-striped mt-3" id="ordersTable">
                <caption><?= __('Lista de órdenes') ?></caption>
                <thead>
                    <tr>
                        <th><?= __('Ref #') ?></th>
                        <th><?= __('Cliente') ?></th>
                        <th><?= __('Email') ?></th>
                        <th><?= __('Fecha') ?></th>
                        <th class="text-right"><?= __('Total') ?></th>
                        <th class="text-center"><?= __('Artículos') ?></th>
                        <th class="text-center"><?= __('Estado') ?></th>
                        <th><?= __('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= $order->refNumber ?></td>
                            <td><?= htmlspecialchars($order->customerName) ?></td>
                            <td><?= htmlspecialchars($order->customerEmail) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($order->date)) ?></td>
                            <td class="text-right">$<?= number_format($order->total, 2) ?></td>
                            <td class="text-center"><?= $order->items->count() ?></td>
                            <td class="text-center">
                                <?php if ($order->paid): ?>
                                    <span class="badge badge-success"><?= __('Pagado') ?></span>
                                <?php else: ?>
                                    <span class="badge badge-warning"><?= __('Pendiente') ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary" href="./view.php?store_id=<?= $store->id ?>&order_id=<?= $order->id ?>">
                                    <i class="fas fa-eye"></i> <?= __('Ver') ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::includeFile('/includes/datatable-js.php', true);
    ?>
    <script>
        $(function() {
            $('#ordersTable').DataTable({
                order: [
                    [3, 'desc']
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/<?= __LANG === 'es' ? 'es-ES' : 'en-GB' ?>.json'
                }
            });
        });
    </script>
</body>

</html>