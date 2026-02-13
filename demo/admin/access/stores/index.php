<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Store;
use Classes\Route;
use Classes\Session;

Session::is_logged();
$stores = Store::all();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __('Tiendas');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= __('Tiendas') ?></h1>
        <div class="mx-auto w-100">
            <a class="btn btn-outline-primary" href="./create.php">Crear tienda</a>

            <table class="table table-striped mt-3">
                <caption>Lista de tiendas</caption>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Activo</th>
                        <th>Código</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    foreach ($stores as $store):
                    ?>
                        <tr>
                            <td><?= $store->name ?></td>
                            <td><?= $store->description ?></td>
                            <td><?= $store->active ? 'Sí' : 'No' ?></td>
                            <td><?= $store->prefix_code ?></td>
                            <td>
                                <a class="btn btn-outline-info btn-sm" href="./orders/index.php?store_id=<?= $store->id ?>" title="<?= __('Ver Órdenes') ?>">
                                    <i class="fas fa-shopping-cart"></i> <?= __('Órdenes') ?>
                                </a>
                                <a class="btn btn-outline-primary btn-sm" href="./edit.php?id=<?= $store->id ?>"><?= __('Editar') ?></a>
                                <button data-store-id="<?= $store->id ?>" class="btn btn-outline-danger btn-sm deleteStoreButton"><?= __('Eliminar') ?></button>
                            </td>
                        </tr>
                    <?php
                    endforeach;
                    ?>
                </tbody>
            </table>

        </div>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::sweetAlert();
    ?>
    <script>
        $(function() {
            $(".deleteStoreButton").on("click", function() {
                const storeId = $(this).data("store-id");
                ConfirmationAlert.fire({
                    title: '¿Estás seguro?',
                    text: '¿Quieres eliminar esta tienda?',
                }).then((result) => {
                    $.post('./includes/delete.php', {
                        id: storeId
                    }, function() {
                        window.location.href = './index.php';
                    });
                });
            });
        })
    </script>
</body>

</html>