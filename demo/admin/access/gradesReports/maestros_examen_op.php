<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

$lang = new Lang([
    ['Calificaciones de los exámenes', 'Grades of exams given by Teachers'],
    ['Reporte de Notas', 'Grade Report'],
    ['Idioma', 'Language'],
    ['Grado', 'Grade'],
    ['Opción', 'Option'],
    ['Continuar', 'Continue'],
    ['Semestre 1', 'Semester 1'],
    ['Semestre 2', 'Semester 2'],
    ['Trimestre 1', 'Quarter 1'],
    ['Trimestre 2', 'Quarter 2'],
    ['Trimestre 3', 'Quarter 3'],
    ['Trimestre 4', 'Quarter 4'],
    ['Con promedio final', 'With final average'],
    ['Con Créditos', 'With Credits'],
    ['Con firma', 'With signature'],
    ['Atrás', 'Go back'],
    ['Trimestre', 'Quarters'],
    ['Pagina', 'Page'],
    ['Selección', 'Selection'],
    ['Notas', 'Grades'],
    ['Trabajo libreta', 'Daily Homework'],
    ['Trabajos diarios', 'Homework'],
    ['Prueba cortas', 'Quiz'],
]);
$school = new School(Session::id());
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Examen</title>
    <?php
    $title = $lang->translation('Calificaciones de los exámenes');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5">
            <?= $lang->translation('Calificaciones de los exámenes') ?>
        </h1>
        <a href="<?= Route::url('/admin/access/gradesReports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded" style="max-width: 500px;">
            <div id="container">
                <form action="mestros_examen_inf.php" method="POST" target="_blank" target="examenes">
                    <table cellpadding="2" cellspacing="0" border="0" style="max-width: 400px; width: 615px;">
                        <thead>
                            <tr class="gris">
                                <th>
                                    <?= $lang->translation('Trimestres') ?>
                                </th>
                                <th>
                                    <?= $lang->translation('Paginas') ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="color">
                                <td>
                                    <select name="cuatrimestre">
                                        <option value="Trimestre-1"><?= $lang->translation('Trimestre 1') ?></option>
                                        <option value="Trimestre-2"><?= $lang->translation('Trimestre 2') ?></option>
                                        <option value="Trimestre-3"><?= $lang->translation('Trimestre 3') ?></option>
                                        <option value="Trimestre-4"><?= $lang->translation('Trimestre 4') ?></option>
                                    </select>
                                </td>
                                <td>
                                    <select name="pagina">
                                        <option value="Notas"><?= $lang->translation('Notas') ?></option>
                                        <option value="Pruebas-Cortas"><?= $lang->translation('Pruebas Cortas') ?></option>
                                        <option value="Trab-Diarios"><?= $lang->translation('Trabajos Diarios') ?></option>
                                        <option value="Trab-Libreta"><?= $lang->translation('Trabajos Libreta') ?></option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                        <tr class="gris">
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                    <br />
                    <div class="input-group mb-3">
                        <button name='create' type="submit" class="btn btn-primary d-block mx-auto">
                            <?= $lang->translation('Continuar') ?>
                        </button>
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