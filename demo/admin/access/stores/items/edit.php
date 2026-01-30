<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\StoreItem;
use Classes\Lang;
use Classes\Route;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ['Editar articulo', 'Edit item'],
]);

$storeId = $_GET['store_id'] ?? null;
$id = $_GET['id'] ?? null;

if (!$storeId) Route::redirect('/access/stores/index.php');

if (!$id) Route::redirect("/access/stores/index.php?store_id={$storeId}");

$item = StoreItem::find($id);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation('Editar articulo');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>

    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Editar articulo') ?></h1>
        <div class="mx-auto" style="max-width: 40rem;">
            <a class="btn btn-outline-primary my-2" href="../edit.php?id=<?= $storeId ?>">Volver</a>
            <form method="POST" action="./includes/update.php">
                <input type="hidden" name="id" value="<?= $item->id ?>">
                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= $item->name ?>" required>
                </div>
                <div class="form-group">
                    <label for="price">Precio principal</label>
                    <input type="number" min="0" step="0.01" class="form-control" id="price" name="price" value="<?= $item->price ?>" required>
                </div>
                <div class="form-group">
                    <div class="form-check form-check-inline">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="buy_multiple" <?= $item->buy_multiple ? 'checked' : '' ?>> Se puede comprar multiples
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <div>
                        <label for="price">Opciones</label>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addOptionButton">Agregar opcion</button>
                    </div>
                    <div id="optionsContainer" class="mt-3">
                        <?php
                        $options = $item->options;
                        // Sort by order
                        usort($options, fn($a, $b) => $a->order - $b->order);
                        // Loop through the options and display them
                        foreach ($options as $index => $option):
                        ?>
                            <div class="d-flex align-items-center mb-3">
                                <span class="handle mr-2" style="cursor:move;"><i class="fa fa-arrows" aria-hidden="true"></i></span>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="options[<?= $option->order ?>][name]" value="<?= $option->name ?>" placeholder="Nombre de la opcion" required>
                                    <input type="number" min="0" step="0.01" class="form-control" name="options[<?= $option->order ?>][price]" value="<?= $option->price ?>" placeholder="Precio de la opcion">
                                    <button type="button" class="btn btn-danger removeOptionButton">Eliminar</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-group">
                        <label for="picture_url">Imagen</label>
                        <input type="text" class="form-control" id="picture_url" name="picture_url" value="<?= $item->picture_url ?>">
                        <small class="form-text text-muted">URL de la imagen</small>
                    </div>
                    <?php if ($item->picture_url): ?>
                        <div class="text-center mt-2">
                            <img class="img-thumbnail" src="<?= $item->picture_url ?>" alt="<?= $item->name ?>" width="100" height="100">
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between mt-2">
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                        <button type="button" data-item-id="<?= $id ?>" data-store-id="<?= $storeId ?>" class="btn btn-danger" id="deleteItemButton">
                            Eliminar articulo
                        </button>
                    </div>
            </form>

        </div>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::fontawasome();
    Route::sweetAlert();
    ?>
    <script src="http://SortableJS.github.io/Sortable/Sortable.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-sortablejs@latest/jquery-sortable.js"></script>
    <script>
        $(function() {
            const $optionsContainer = $("#optionsContainer");

            $optionsContainer.sortable({
                handle: '.handle',
                animation: 150,
                onUpdate: () => {
                    const items = $optionsContainer.children().toArray();
                    console.log(items);
                    items.forEach((item, index) => {
                        console.log({
                            item
                        })
                        $(item).find('input[type="text"]').attr('name', `options[${index}][name]`);
                        $(item).find('input[type="number"]').attr('name', `options[${index}][price]`);
                    });
                },

            });


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

            $("#deleteItemButton").on("click", function() {
                const itemId = $(this).data("item-id");
                const storeId = $(this).data("store-id");
                ConfirmationAlert.fire({
                    title: '¿Estás seguro?',
                    text: '¿Quieres eliminar este articulo?',
                }).then((result) => {
                    $.post('./includes/delete.php', {
                        id: itemId
                    }, function() {
                        window.location.href = '../edit.php?id=' + storeId;
                    });
                });
            });

        })
    </script>
</body>

</html>