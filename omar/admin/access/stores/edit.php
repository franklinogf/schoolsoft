<?php
require_once '../../../app.php';

use Classes\DataBase\DB;
use Classes\Lang;
use Classes\Route;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ['Editar tienda', 'Edit store'],
]);

$id = $_GET['id'] ?? null;

if (!$id) Route::redirect('/access/stores/index.php');

$store = DB::table('stores')->where('id', $id)->first();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation('Editar tienda');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>

    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Editar tienda') ?></h1>
        <div class="mx-auto" style="max-width: 40rem;">
            <a class="btn btn-outline-primary my-2" href="./index.php">Volver</a>
            <form method="POST" action="./includes/update.php">
                <input type="hidden" name="id" value="<?= $store->id ?>">
                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= $store->name ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Descripción</label>
                    <textarea class="form-control" id="description" name="description"><?= $store->description ?></textarea>
                </div>
                <div class="form-group">
                    <div class="form-check form-check-inline">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="active" <?= $store->active ? 'checked' : '' ?>> Activo
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="prefix_code">Código indentificador</label>
                    <input type="number" min="1" class="form-control" id="prefix_code" name="prefix_code" value="<?= $store->prefix_code ?>" required>
                    <?php if (isset($_GET['error']) && $_GET['error'] == 'prefix_code_exists'): ?>
                        <div class="alert alert-danger mt-2" role="alert">
                            El código ya existe
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <button type="button" data-store-id="<?= $store->id ?>" class="btn btn-danger" id="deleteStoreButton">
                        Eliminar tienda
                    </button>
                </div>
            </form>







        </div>
        <div class="mt-3">

            <a class="btn btn-outline-primary mt-3" href="./items/create.php?store_id=<?= $store->id ?>">
                Crear articulo
            </a>

            <table class="table table-striped mt-3">
                <thead>
                    <tr class="text-center">
                        <th>Imagén</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Opciones</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="itemsTableBody">
                    <?php
                    $items = DB::table('store_items')->where('store_id', $store->id)->get();
                    foreach ($items as $item):
                    ?>
                        <tr>
                            <td>
                                <?php if ($item->picture_url): ?>
                                    <img src="<?= $item->picture_url ?>" alt="<?= $item->name ?>" width="50" height="50">
                                <?php endif ?>
                            </td>
                            <td><?= $item->name ?></td>
                            <td class="text-right"><?= $item->price ?></td>
                            <td class="text-center"><?= count(json_decode($item->options) ?? []) ?></td>
                            <td class="d-flex justify-content-center">
                                <a class="btn btn-outline-primary mr-2" href="./items/edit.php?store_id=<?= $item->store_id ?>&id=<?= $item->id ?>">Editar</a>
                                <button data-store-id="<?= $store->id ?>" data-item-id="<?= $item->id ?>" class="btn btn-outline-danger deleteItemButton">Eliminar</b>
                            </td>
                        </tr>
                    <?php endforeach; ?>
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
            $("#deleteStoreButton").on("click", function() {
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

            $(".deleteItemButton").on("click", function() {
                const itemId = $(this).data("item-id");
                const storeId = $(this).data("store-id");
                ConfirmationAlert.fire({
                    title: '¿Estás seguro?',
                    text: '¿Quieres eliminar este articulo?',
                }).then((result) => {
                    $.post('./items/includes/delete.php', {
                        id: itemId
                    }, function() {
                        window.location.href = './edit.php?id=' + storeId;
                    });
                });
            });

        })
    </script>
</body>

</html>