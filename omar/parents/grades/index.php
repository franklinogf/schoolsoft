<?php
require_once '../../app.php';

use Classes\Controllers\Parents;
use Classes\Lang;
use Classes\Route;
use Classes\Session;

Session::is_logged();
$parents = new Parents(Session::id());
$lang = new Lang([
    ["Notas diarias", "Daily notes"],
    ["Yo, padre, madre o encargado, certifico que veré las notas diarias de mi hijo(a) a través del Internet. El sistema guardará un recibo en evidencia de que ví las notas diarias en curso.", "I, father, mother or in charge, I certify that I will see the daily notes of my child through the Internet.The system will keep an evidence that I saw the daily notes."],
    ["He leido y estoy de acuerdo.", "I have read and I agree."],
    ["Estudiantes inscritos", "Registered students"]
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Notas diarias");
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>
    <div class="container mt-3">
        <h1 class="text-center my-2"><?= $lang->translation("Notas diarias") ?></h1>
        <?php if ($parents->activo == 'Activo') : ?>
            <div class="jumbotron mt-3">
                <p><?= $lang->translation("Yo, padre, madre o encargado, certifico que veré las notas diarias de mi hijo(a) a través del Internet. El sistema guardará un recibo en evidencia de que ví las notas diarias en curso.") ?></p>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="accept">
                    <label class="custom-control-label" for="accept"><?= $lang->translation("He leido y estoy de acuerdo.") ?></label>
                </div>
            </div>
            <form action="<?= Route::url('/parents/grades/gradesOptions.php') ?>" method="POST">
                <div class="form-row">
                    <label class="font-weight-bold col-12" for="studentSS"><?= $lang->translation("Estudiantes inscritos") ?></label>
                    <select name="studentSS" id="studentSS" class="form-control col-12 col-lg-6">
                        <?php foreach ($parents->kids() as $kid) : ?>
                            <option value="<?= $kid->ss ?>"><?= "$kid->nombre $kid->apellidos -> $kid->grado" ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <button id="gradesBtn" type="submit" class="btn btn-primary mt-3" disabled><?= $lang->translation("Notas diarias") ?></button>
            </form>
        <?php else : ?>
            <div class="jumbotron mt-3 border-danger">
                <div class="alert alert-danger" role="alert">
                    <?= $parents->info('men_inac') ?>
                </div>
            </div>
        <?php endif ?>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>