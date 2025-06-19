<?php
require_once '../../../app.php';

use Classes\Controllers\Parents;
use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\File;

Session::is_logged();
$school = new School(Session::id());

$lang = new Lang([
    ['Cambiar cuenta', 'Change account'],
    ['Cuenta Vieja', 'Old Account'],
    ['Cuenta Nueva', 'New Account'],
    ['Cambiar', 'Change'],
    ['Estás seguro que quieres cambiar la cuenta?', 'Are you sure you want to change the account?'],
    ['Madre', 'Mother'],
    ['Correo', 'Email'],
    ['Fecha de entrega:', 'Date of delivery:'],
    ['Descargar', 'Download'],
    ['Agregar documento', 'Add document'],
    ['Buscar', 'Search'],
    ['Debe de llenar todos los campos', 'You must fill all fields'],
]);
//$year = $students->info('year');

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation("Cambiar cuenta");
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>

</head>

<body class='pb-5'>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container mt-5">
        <h1 class="text-center"><?= $lang->translation("Cambiar cuenta") ?></h1>
        <div class="container">
            <div class="mx-auto bg-white shadow-lg py-5 px-3 rounded" style="max-width: 300px;">
                <form method="POST">
                    <h5 class="card-title title"><?= $lang->translation("Cuenta Vieja") ?></h5>
                    <input type="text" class="form-control" name='oac' id="oac" style="width: 80px">
                    <h5 class="card-title title"><?= $lang->translation("Cuenta Nueva") ?></h5>
                    <input type="text" class="form-control" name='nac' id="nac" style="width: 80px">
                    <input name="cambiar" class="btn btn-primary btn-sm btn-block mt-2" type="submit" onclick="return confirmar('<?= $lang->translation('Estás seguro que quieres cambiar la cuenta?') ?>')" value="<?= $lang->translation("Cambiar") ?>">
                </form>

            </div>
        </div>
        <?php if (isset($_REQUEST['cambiar']) and !empty($_REQUEST['oac']) and !empty($_REQUEST['nac'])) :
    $acc = DB::table('madre')->where([
        ['id', $ido]
    ])->update([
        'id' => $idn,
    ]);
    $acc = DB::table('padres')->where([
        ['id', $ido],
        ['year', $school->info('year2')]
    ])->update([
        'id' => $idn,
    ]);
    $acc = DB::table('year')->where([
        ['id', $ido],
        ['year', $school->info('year2')]
    ])->update([
        'id' => $idn,
    ]);
    $acc = DB::table('pagos')->where([
        ['id', $ido],
        ['year', $school->info('year2')]
    ])->update([
        'id' => $idn,
    ]);

        ?>
        <?php endif ?>
    </div>


    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::selectPicker('js');

    ?>
    <script>
        function confirmar(mensaje) {
            return confirm(mensaje);
        }
    </script>

</body>

</html>