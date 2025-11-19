<?php
require_once __DIR__ . '/../../../app.php';

use Classes\DataBase\DB;
use Classes\Lang;
use Classes\Route;
use Classes\Session;

Session::is_logged();
$schools = DB::table('colegio')->get();
$lang = new Lang([
    ['Seleccionar profesores para enviar mensaje de texto', 'Select teachers to send sms'],

]);

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Seleccionar profesores para enviar mensaje de texto");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation("Seleccionar profesores para enviar mensaje de texto") ?></h1>

        <div class="container bg-white shadow-lg py-3 my-3 rounded">
            <form id="form" action="<?= Route::url('/admin/messages/sms/') ?>">
                <?php
                $__tableData = $schools;
                $__tableDataCheckbox = true;
                $__dataPk = 'usuario';
                $__tableDataInfo = [
                    [
                        "title" => ["es" => "Usuario", "en" => "Username"],
                        "values" => ['usuario']
                    ]
                ];
                Route::includeFile('/includes/layouts/table.php', true);
                ?>

            </form>
        </div>

    </div>




    </div>
    <?php
    $DataTable = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>