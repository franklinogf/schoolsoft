<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();
$lang = new Lang([
    ['Tiendas', 'Stores'],
]);
$stores = DB::table('stores')->get();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation('Tiendas');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Tiendas') ?></h1>
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
                                <a class="btn btn-outline-primary" href="./edit.php?id=<?= $store->id ?>">Editar</a>
                                <button data-store-id="<?= $store->id ?>" class="btn btn-outline-danger deleteStoreButton">Eliminar</b>
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