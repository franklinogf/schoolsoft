<?php
require_once '../../app.php';

use Classes\Controllers\Parents;
use Classes\Lang;
use Classes\Route;
use Classes\Session;

Session::is_logged();
$parents = new Parents(Session::id());
$studentSS = $_GET['studentSS'];
$lang = new Lang([
    ['Selección de notas', 'Selection of grades'],
    ['Area','Area'],
    ['Notas','Grades'],
    ['Trimestral','Quarterly'],
    ['Conducta y Asistencia','Behavior and attendance'],
    ['Totales','Total']    
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Selección de notas");
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>
    <div class="container mt-3">
        <h1 class="text-center my-2"><?= $lang->translation("Selección de notas") ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mt-3">
            <form action="<?= Route::url('/parents/grades/grades.php') ?>" method="POST">
            <input type="hidden" name="studentSS" value="<?= $studentSS ?>">
                <div class="row">
                    <div class="form-group col-6">
                        <label class="font-weight-bold" for="trimester"><?= $lang->translation("Trimestre") ?></label>
                        <select name="trimester" id="trimester" class="form-control">
                            <option value="Trimestre-1"><?= $lang->translation("Trimestre") ?> 1</option>
                            <option value="Trimestre-2"><?= $lang->translation("Trimestre") ?> 2</option>
                            <option value="Trimestre-3"><?= $lang->translation("Trimestre") ?> 3</option>
                            <option value="Trimestre-4"><?= $lang->translation("Trimestre") ?> 4</option>
                        </select>
                    </div>
                    <div class="form-group col-6">
                        <label class="font-weight-bold" for="area"><?= $lang->translation("Area") ?></label>
                        <select name="area" id="area" class="form-control">
                            <option value="Notas"><?= $lang->translation("Notas") ?></option>
                            <option value="Trimestral"><?= $lang->translation("Trimestral") ?></option>
                            <option value="Cond-Asis"><?= $lang->translation("Conducta y Asistencia") ?></option>
                            <option value="Totales"><?= $lang->translation("Totales") ?></option>
                        </select>
                    </div>
                </div>
                <input class="btn btn-primary mx-auto d-block" type="submit" value="<?= $lang->translation("Continuar") ?>">
            </form>
        </div>

    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>