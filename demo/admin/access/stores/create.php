<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ['Crear tienda', 'Create store'],
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation('Crear tienda');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Crear tienda') ?></h1>
        <div class="mx-auto" style="max-width: 40rem;">
            <a class="btn btn-outline-primary my-2" href="./index.php">Volver</a>
            <form method="POST" action="./includes/store.php">
                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="description">Descripción</label>
                    <textarea class="form-control" id="description" name="description"></textarea>
                </div>
                <div class="form-group">
                    <div class="form-check form-check-inline">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="active" value="true" checked> Activo
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="prefix_code">Código indentificador</label>
                    <input type="number" min="1" class="form-control" id="prefix_code" name="prefix_code" required>
                    <?php if (isset($_GET['error']) && $_GET['error'] == 'prefix_code_exists'): ?>
                        <div class="alert alert-danger mt-2" role="alert">
                            El código ya existe
                        </div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">Crear</button>
            </form>

        </div>
    </div>
</body>

</html>