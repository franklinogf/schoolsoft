<?php
require_once '../../../app.php';

use Classes\File;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Parents;
use Classes\Controllers\Student;
use Classes\Lang;

Session::is_logged();
$parents = new Parents(Session::id());
$lang = new Lang([
    ["Tarjeta de notas", "Grades card"],
    ["La tarjeta <b>NO</b> está disponible en estos momentos.", "The card is <b>NOT</b> currently available."],
    ["Yo, padre, madre o encargado, certifico que veré la tarjeta de notas de mi hijo(a) a través del Internet. El sistema guardará un recibo en evidencia de que ví las notas diarias en curso.", "I, the parent, mother or guardian, certify that I will see the grades card of my child(ren) through the Internet. The system will save a receipt in evidence that I saw the daily notes in progress."],
    ["He leido y estoy de acuerdo.", "I have read and I agree."],
    ["Estudiantes inscritos", "Registered students"]

]);

$colegio = DB::table('colegio')->where([
    ['usuario', 'administrador']
])->orderBy('id')->first();

$tar = $colegio->tar;

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation("Tarjeta de notas");
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-4"><?= $lang->translation("Tarjeta de notas") ?></h1>
        <?php if ($parents->info('logo') !== 'NO' || $parents->info('fec_t') <= Util::date()) : ?>
            <div class="jumbotron mt-3">
                <h5 class="text-center text-danger"><?= $lang->translation("La tarjeta <b>NO</b> está disponible en estos momentos.") ?></h5>
            </div>
        <?php else : ?>
            <div class="jumbotron mt-3">
                <p><?= $lang->translation("Yo, padre, madre o encargado, certifico que veré la tarjeta de notas de mi hijo(a) a través del Internet. El sistema guardará un recibo en evidencia de que ví las notas diarias en curso.") ?></p>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="accept">
                    <label class="custom-control-label" for="accept"><?= $lang->translation("He leido y estoy de acuerdo.") ?></label>
                </div>
            </div>
            <form action="<?= Route::url('/parents/options/grades/pdfGrades' . $tar . '.php') ?>" method="POST" target="_blank">
                <div class="form-row">
                    <label class="font-weight-bold col-12" for="studentSS"><?= $lang->translation("Estudiantes inscritos") ?></label>
                    <select name="studentSS" id="studentSS" class="form-control col-12 col-lg-6">
                        <?php foreach ($parents->kids() as $kid) : ?>
                            <option value="<?= $kid->ss ?>"><?= "$kid->nombre $kid->apellidos -> $kid->grado" ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <button id="gradesBtn" type="submit" class="btn btn-primary mt-3" disabled><?= $lang->translation("Tarjeta de notas") ?></button>
            </form>
        <?php endif ?>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>