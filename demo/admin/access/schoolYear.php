<?php
require_once __DIR__ . '/../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();
$lang = new Lang([
    ['Año escolar', 'School year'],
    ['Selección de año', 'Year selection'],
    ['Para administración', 'For admin'],
    ['Para maestro(a)s', 'For teachers'],
    ['Guardar', 'Save'],
]);
$years = DB::table('year')->select("DISTINCT year")->get();
$school = new School();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation('Año escolar');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::fontawasome();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Año escolar') ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form method="POST" action="<?= Route::url('/admin/access/includes/schoolYear.php') ?>">
                <div class="mx-auto" style="width: 20rem;">
                    <h2 class="text-center"><?= $lang->translation("Selección de año") ?></h2>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="class"><?= $lang->translation('Para administración') ?></label>
                        </div>
                        <select name="adminYear" class="custom-select" required>
                            <?php foreach ($years as $year) : ?>
                                <option <?= $school->info('year2') == $year->year ? 'selected' : '' ?> value="<?= $year->year ?>"><?= $year->year ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <?php if (Session::id() === 'administrador') : ?>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="class"><?= $lang->translation('Para maestro(a)s') ?></label>
                            </div>
                            <select name="teacherYear" class="custom-select" required>
                                <?php foreach ($years as $year) : ?>
                                    <option <?= $school->info('year') == $year->year ? 'selected' : '' ?> value="<?= $year->year ?>"><?= $year->year ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    <?php endif ?>
                    <input name="save" class="btn btn-primary mx-auto d-block" type="submit" value="<?= $lang->translation("Continuar") ?>">

                </div>
            </form>
        </div>
        <?php if (Session::get('schoolYear')) : ?>
            <div class="alert alert-primary col-2 mx-auto mt-1" role="alert">
                <i class="fa-solid fa-square-check"></i> <?= Session::get('schoolYear', true) ?>
            </div>
        <?php endif ?>

    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>