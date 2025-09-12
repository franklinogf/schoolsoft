<?php
require_once '../../app.php';


use App\Models\Family;
use Classes\Route;
use Classes\Session;

Session::is_logged();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = __("Buscar padres");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-3"><?= __("Buscar padres") ?></h1>
        <?php
        $__tableData = Family::query()
            ->active()
            ->get(['id', 'madre', 'padre']);

        $__dataPk = 'id';
        $__tableDataInfo = [
            ['title' => 'ID', 'values' => ['id']],
            ['title' => __('Madre'), 'values' => ['madre']],
            ['title' => __('Padre'), 'values' => ['padre']],

        ];

        Route::includeFile('/includes/layouts/table.php', true);
        ?>
    </div>

    <?php
    $DataTable = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>