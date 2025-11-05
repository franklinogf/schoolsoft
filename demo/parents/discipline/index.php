<?php
require_once '../../app.php';

use App\Models\Admin;
use App\Models\Family;
use Classes\Route;
use Classes\Session;

Session::is_logged();
$parents = Family::find(Session::id());
$school = Admin::primaryAdmin();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __('Disciplina');
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>
    <div class="container mt-3">
        <h1 class="text-center my-2"><?= __("Disciplina") ?></h1>
        <?php if ($parents->activo == 'Activo') : ?>
            <div class="jumbotron mt-3">
                <p><?= __("Yo, padre, madre o encargado, certifico que veré las Disciplina de mi hijo(a) a través del Internet. El sistema guardará un recibo en evidencia de que ví las Disciplina en curso.") ?></p>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="accept">
                    <label class="custom-control-label" for="accept"><?= __("He leido y estoy de acuerdo") ?></label>
                </div>
            </div>
            <form action="<?= Route::url('/parents/discipline/pdfDiscipline.php') ?>" method="POST" target="_blank">
                <div class="form-row">
                    <label class="font-weight-bold col-12" for="studentSS"><?= __("Estudiantes inscritos") ?></label>
                    <select name="studentSS" id="studentSS" class="form-control col-12 col-lg-6">
                        <?php foreach ($parents->kids as $kid) : ?>
                            <option value="<?= $kid->ss ?>"><?= "$kid->nombre $kid->apellidos -> $kid->grado" ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <button id="gradesBtn" type="submit" class="btn btn-primary mt-3" disabled><?= __("Disciplina") ?></button>
            </form>
        <?php else : ?>
            <div class="jumbotron mt-3 border-danger">
                <div class="alert alert-danger" role="alert">
                    <?= $school->men_inac ?>
                </div>
            </div>
        <?php endif ?>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>