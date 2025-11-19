<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Store;
use Classes\Route;
use Classes\Session;

Session::is_logged();
// $lang = new Lang([
//     ["Tiendas", "Stores"],
//     ["No hay tiendas disponibles por el momento.", "No stores available at the moment."]
// ]);
$stores = Store::active()->get();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Tiendas");
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-4"><?= __("Tiendas") ?></h1>
        <div class="row justify-content-center">
            <?php
            // Get all stores from the database


            if (count($stores) > 0) {
                foreach ($stores as $store) {
            ?>
                    <div class="col-md-4 col-sm-6 mb-3">
                        <a href="store.php?id=<?= $store->id ?>" class="btn btn-primary btn-lg btn-block">
                            <?= $store->name ?>
                        </a>
                    </div>
                <?php
                }
            } else {
                ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <?= __("No hay tiendas disponibles por el momento.") ?>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>