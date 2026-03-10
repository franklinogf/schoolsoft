<?php
require_once __DIR__ . '/../../app.php';

use App\Models\Admin;
use Classes\Lang;
use Classes\Route;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ['Mensaje inicial', 'Initial message'],
    ['Mensaje inicial derecha', 'Right initial message'],
    ['Mensaje inicial izquierda', 'Left initial message'],
    ['Grabar', 'Save'],

]);

$school = Admin::primaryAdmin();

if (isset($_REQUEST['Grabar'])) {
    $school->update([
        'men_ini' => $_POST['men_ini'],
        'men_nota' => $_POST['men_nota'],
    ]);
    $school->refresh();
}


$men_ini = $school->men_ini;
$men_nota = $school->men_nota;

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('Mensaje inicial');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Mensaje inicial') ?></h1>
        <div class="container">
            <div class="mx-auto bg-white shadow-lg py-5 px-3 rounded" style="max-width: 650px;">
                <form class="mt-3" method="POST">
                    <div class="row">
                        <div class="col-18">
                            <div class="form-group col-6 px-0">
                                <label for="curso"><?= $lang->translation("Mensaje inicial izquierda") ?></label>
                                <textarea cols="84" name="men_ini" rows="15"><?= $men_ini ?></textarea>

                                <label for="curso"><?= $lang->translation("Mensaje inicial derecha") ?></label>
                                <textarea cols="84" name="men_nota" rows="15"><?= $men_nota ?></textarea>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-danger" name="Grabar" type="submit"><?= $lang->translation('Grabar') ?></button>
                        </div>

                        <div class="col-12 text-center">
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
    <?php

    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>