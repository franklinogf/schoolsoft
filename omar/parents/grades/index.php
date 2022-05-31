<?php
require_once '../../app.php';

use Classes\Controllers\Parents;
use Classes\Route;
use Classes\Session;

Session::is_logged();
$parents = new Parents(Session::id());
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = "Notas diaras";
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>
    <div class="container mt-3">
        <h1 class="text-center my-2">Notas Diaras</h1>
        <div class="jumbotron mt-3">
            <p>Yo, padre, madre o encargado, certifico que veré las notas diarias de mi hijo(a) a través del Internet. El sistema guardará un recibo en evidencia de que ví las notas diarias en curso.</p>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="accept">
                <label class="custom-control-label" for="accept">He leido y estoy de acuerdo.</label>
            </div>
        </div>
        <form action="<?= Route::url('/parents/grades/gradesOptions.php') ?>" method="POST">
            <div class="form-row">
                <label class="font-weight-bold col-12" for="studentSS">Estudiantes inscritos</label>
                <select name="studentSS" id="studentSS" class="form-control col-12 col-lg-6">
                    <?php foreach ($parents->kids() as $kid) : ?>
                        <option value="<?= $kid->ss ?>"><?= "$kid->nombre $kid->apellidos -> $kid->grado" ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <button id="gradesBtn" type="submit" class="btn btn-primary mt-3" disabled>Notas diaras</button>
        </form>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>