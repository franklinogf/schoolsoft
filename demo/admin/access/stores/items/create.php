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
            <form method="POST" action="./includes/store.php" enctype="multipart/form-data">
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
                    <div id="optionsContainer" class="mt-3"></div>
                </div>

                <div class="form-group">
                    <label class="d-block">Imagen</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="picture_source" id="picture_source_none" value="none">
                        <label class="form-check-label" for="picture_source_none">Sin imagen</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="picture_source" id="picture_source_url" value="url" checked>
                        <label class="form-check-label" for="picture_source_url">Enlace</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="picture_source" id="picture_source_upload" value="upload">
                        <label class="form-check-label" for="picture_source_upload">Subir archivo</label>
                    </div>

                    <div id="pictureUploadGroup" class="mt-3 d-none">
                        <input type="file" class="form-control-file" id="picture_upload" name="picture_upload" accept="image/*">
                        <small class="form-text text-muted">JPG, PNG, GIF o WEBP. Tamaño recomendado: 500x500px.</small>
                    </div>

                    <div id="pictureUrlGroup" class="mt-3">
                        <input type="text" class="form-control" id="picture_url" name="picture_url" placeholder="https://ejemplo.com/imagen.jpg">
                        <small class="form-text text-muted">Ingresa la URL completa de la imagen.</small>
                    </div>

                    <div id="picturePreview" class="mt-3 text-center d-none">
                        <img id="picturePreviewImage" class="img-thumbnail" src="" alt="Vista previa" width="140" height="140" data-initial="">
                    </div>
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
            const $pictureSourceInputs = $("input[name='picture_source']");
            const $pictureUploadGroup = $("#pictureUploadGroup");
            const $pictureUrlGroup = $("#pictureUrlGroup");
            const $pictureFileInput = $("#picture_upload");
            const $pictureUrlInput = $("#picture_url");
            const $picturePreview = $("#picturePreview");
            const $picturePreviewImage = $("#picturePreviewImage");
            let objectUrl = null;

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

            function revokeObjectUrl() {
                if (objectUrl) {
                    URL.revokeObjectURL(objectUrl);
                    objectUrl = null;
                }
            }

            function setPreview(url) {
                if (url) {
                    $picturePreview.removeClass("d-none");
                    $picturePreviewImage.attr("src", url);
                } else {
                    $picturePreview.addClass("d-none");
                    $picturePreviewImage.attr("src", "");
                }
            }

            function updateUploadPreview() {
                const input = $pictureFileInput.get(0);
                const file = input && input.files && input.files[0];

                if (file) {
                    revokeObjectUrl();
                    objectUrl = URL.createObjectURL(file);
                    setPreview(objectUrl);
                    return;
                }

                setPreview(null);
            }

            function updateUrlPreview() {
                const url = ($pictureUrlInput.val() || '').trim();
                setPreview(url || null);
            }

            function handleSourceChange() {
                const source = $pictureSourceInputs.filter(":checked").val();

                $pictureUploadGroup.toggleClass("d-none", source !== 'upload');
                $pictureUrlGroup.toggleClass("d-none", source !== 'url');

                if (source === 'upload') {
                    updateUploadPreview();
                } else if (source === 'url') {
                    $pictureFileInput.val('');
                    revokeObjectUrl();
                    updateUrlPreview();
                } else {
                    $pictureFileInput.val('');
                    revokeObjectUrl();
                    setPreview(null);
                }
            }

            $pictureSourceInputs.on('change', handleSourceChange);
            $pictureFileInput.on('change', updateUploadPreview);
            $pictureUrlInput.on('input', updateUrlPreview);

            handleSourceChange();
        })
    </script>
</body>

</html>