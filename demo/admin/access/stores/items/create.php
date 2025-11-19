<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ['Crear articulo para la tienda', 'Create item for store'],
]);

$storeId = $_GET['store_id'] ?? null;

if (!$storeId) Route::redirect('/access/stores/');


?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation('Crear articulo para la tienda');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Crear articulo para la tienda') ?></h1>
        <div class="mx-auto" style="max-width: 40rem;">
            <a class="btn btn-outline-primary my-2" href="../edit.php?id=<?= $storeId ?>">Volver</a>
            <form method="POST" action="./includes/store.php">
                <input type="hidden" name="store_id" value="<?= $storeId ?>">
                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="price">Precio Principal</label>
                    <input type="number" min="0" step="0.01" class="form-control" id="price" name="price" required>
                </div>
                <div class="form-group">
                    <div class="form-check form-check-inline">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="buy_multiple" checked> Se puede comprar multiples
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <div>
                        <label for="price">Opciones</label>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addOptionButton">Agregar opcion</button>
                    </div>
                    <div id="optionsContainer" class="mt-3">

                    </div>
                    <div class="form-group">
                        <label for="picture_url">Imagen</label>
                        <input type="text" class="form-control" id="picture_url" name="picture_url">
                        <small class="form-text text-muted">URL de la imagen</small>
                    </div>

                    <button type="submit" class="btn btn-primary">Crear</button>
            </form>

        </div>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::sweetAlert();
    ?>
    <script>
        $(function() {
            const $optionsContainer = $("#optionsContainer");

            $("#addOptionButton").on("click", function() {
                const count = $optionsContainer.children().length;
                $optionsContainer.append(Option({
                    index: count
                }));

            });

            $optionsContainer.on("click", ".removeOptionButton", function() {

                $(this).parent().remove();
            });

            function Option({
                index
            }) {
                return `
                <div class="input-group mb-3 d-flex gap-3 align-items-center">
                    <input type="text" class="form-control" name="options[${index}][name]" placeholder="Nombre de la opcion" required>
                    <input type="number" min="0" step="0.01" class="form-control" name="options[${index}][price]" placeholder="Precio de la opcion">
                    <button type="button" class="btn btn-danger removeOptionButton">Eliminar</button>
                </div>`;
            }

        })
    </script>
</body>

</html>