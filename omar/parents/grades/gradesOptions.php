<?php
require_once '../../app.php';

use App\Models\Family;
use Classes\Route;
use Classes\Session;

Session::is_logged();
$parents = Family::find(Session::id());
$studentSS = $_GET['studentSS'];
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Selección de notas");
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>
    <div class="container mt-3">
        <h1 class="text-center my-2"><?= __("Selección de notas") ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mt-3">
            <form action="<?= Route::url('/parents/grades/grades.php') ?>" method="POST">
                <input type="hidden" name="studentSS" value="<?= $studentSS ?>">
                <div class="row">
                    <div class="form-group col-6">
                        <label class="font-weight-bold" for="trimester"><?= __("Trimestre") ?></label>
                        <select name="trimester" id="trimester" class="form-control">
                            <option value="Trimestre-1"><?= __("Trimestre") ?> 1</option>
                            <option value="Trimestre-2"><?= __("Trimestre") ?> 2</option>
                            <option value="Trimestre-3"><?= __("Trimestre") ?> 3</option>
                            <option value="Trimestre-4"><?= __("Trimestre") ?> 4</option>
                        </select>
                    </div>
                    <div class="form-group col-6">
                        <label class="font-weight-bold" for="area"><?= __("Area") ?></label>
                        <select name="area" id="area" class="form-control">
                            <option value="Notas"><?= __("Notas") ?></option>
                            <option value="Trimestral"><?= __("Trimestral") ?></option>
                            <option value="Cond-Asis"><?= __("Conducta y Asistencia") ?></option>
                            <option value="Totales"><?= __("Totales") ?></option>
                        </select>
                    </div>
                </div>
                <input class="btn btn-primary mx-auto d-block" type="submit" value="<?= __("Continuar") ?>">
            </form>
        </div>

    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>