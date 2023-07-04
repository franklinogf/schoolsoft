<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();
$teacher = new Teacher();
$lang = new Lang([
    ['Label', 'Label'],
    ['Grado', 'Grade'],
    ['Con cuenta', 'With account'],
    ['Con grado', 'With grade'],
    ['Grado arriba', 'Grade top'],
    ['Grado al lado', 'Grade on the side'],
    ['Nombres', 'Names'],
    ['Separados', 'Separated'],
    ['Juntos', 'Together'],
    ['Nombre de padres', 'Parents name'],
    ['Tamaño de letra', 'Font size'],
    ['Con dirección', 'With address'],
    ['Repetición de estudiantes', 'Student repetition'],
    ['Atrás', 'Go back'],
    ['Todos los grados', 'All grades'],
]);
$students = new Student();
$allStudents = $students->all();
$school = new School();
$grades = $school->allGrades();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation('Label');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center my-3"><?= $lang->translation('Label') ?></h1>
        <a href="<?= Route::url('/admin/access/reports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form action="<?= Route::url('/admin/access/reports/pdf/label.php') ?>" target="label" method="POST">
                <div class="mx-auto" style="width: 25rem;">

                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <label class='input-group-text'><?= $lang->translation("Grado") ?></label>
                        </div>
                        <select name="grade" class="form-control">
                            <option value=""><?= $lang->translation("Todos los grados") ?></option>
                            <?php foreach ($grades as $grade) : ?>
                                <option value="<?= $grade ?>"><?= $grade ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <label class='input-group-text'><?= $lang->translation('Label') ?></label>
                        </div>
                        <select name="label" class="form-control">
                            <option value="3422" selected>3422</option>
                            <option value="5160">5160</option>
                            <option value="5161">5161</option>
                            <option value="5162">5162</option>
                            <option value="5163">5163</option>
                            <option value="5164">5164</option>
                            <option value="6240">6240</option>
                            <option value="8160">8160</option>
                            <option value="8366">8366</option>
                            <option value="8600">8600</option>
                            <option value="L5160">L5160</option>
                            <option value="L7163">L7163</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-info">
                                <input type="checkbox" checked name="withAccount" value="si"><?= $lang->translation("Con cuenta") ?>
                            </label>
                        </div>
                        <div class="btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-info">
                                <input type="checkbox" checked name="withGrade" value="si"><?= $lang->translation("Con grado") ?>
                            </label>
                        </div>

                        <div class="btn-group-toggle mb-2" data-toggle="buttons">
                            <label class="btn btn-outline-info">
                                <input type="checkbox" checked name="withAddress" value="si"><?= $lang->translation("Con dirección") ?>
                            </label>
                        </div>
                    </div>
                    <div class="d-flex mt-2">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-info active">
                                <input type="radio" name="gradePlacement" value="top" checked> <?= $lang->translation("Grado arriba") ?>
                            </label>
                            <label class="btn btn-outline-info">
                                <input type="radio" name="gradePlacement" value="side"> <?= $lang->translation("Grado al lado") ?>
                            </label>
                        </div>
                    </div>
                    <p class="m-0 mt-2"><?= $lang->translation("Nombres") ?>:</p>
                    <div class="d-flex justify-content-between">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-info active">
                                <input type="radio" name="nameOption" value="separated" checked> <?= $lang->translation("Separados") ?>
                            </label>
                            <label class="btn btn-outline-info">
                                <input type="radio" name="nameOption" value="together"> <?= $lang->translation("Juntos") ?>
                            </label>
                        </div>
                        <div class="btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-info">
                                <input type="checkbox" checked name="parentsName" value="si"><?= $lang->translation("Nombre de padres") ?>
                            </label>
                        </div>
                    </div>

                    <div class="input-group my-2">
                        <div class="input-group-prepend">
                            <label class='input-group-text'><?= $lang->translation("Tamaño de letra") ?></label>
                        </div>
                        <select name="fontSize" class="form-control">
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10" selected>10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                            <option value="16">16</option>
                            <option value="18">18</option>
                        </select>
                    </div>


                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <label class='input-group-text'><?= $lang->translation("Repetición de estudiantes") ?></label>
                        </div>
                        <input class="form-control" type="number" name="repeatStudents" value="1" required>
                    </div>


                    <div class="text-center">
                        <button class="btn btn-primary mt-4" type="submit"><?= $lang->translation("Continuar") ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);

    ?>
</body>

</html>