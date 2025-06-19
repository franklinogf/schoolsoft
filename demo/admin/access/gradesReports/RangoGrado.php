<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();


$lang = new Lang([
    ['Rango por grado', 'Rank by grade'],
    ['Todos', 'All'],
    ['Notas Redondeada', 'Rounded Average'],
    ['Con Lineas', 'With Lines'],
    ['Opción', 'Option'],
    ['Continuar', 'Continue'],
    ['Semestre 1', 'Semester 1'],
    ['Semestre 2', 'Semester 2'],
    ['Trimestre 1', 'Quarter 1'],
    ['Trimestre 2', 'Quarter 2'],
    ['Trimestre 3', 'Quarter 3'],
    ['Trimestre 4', 'Quarter 4'],
    ['Promedio final', 'Final average'],
    ['Porciento', 'Percent'],
    ['Notas', 'Grade'],
    ['Informe de distribución de promedio por grado', 'GPA Distribution Report by Grade'],
    ['Atrás', 'Go back'],
    ['Decimal', 'Decimal'],
    ['Grado', 'Grade'],
    ['Orden', 'Order'],
    ['Promedio final', 'Final average'],
    ['Crédito', 'Credit'],
    ['Curso', 'Course'],
    ['Si', 'Yes'],
    ['No', 'No'],
    ['Selección', 'Selection'],
    ['', ''],
    

    
]);
$school = new School(Session::id());
$grades = $school->allGrades();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<head>
    <?php
    $title = $lang->translation('Rango por grado');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
	<style type="text/css">
	.style1 {
		text-align: center;
	}
	</style>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5">
            <?= $lang->translation('Rango por grado') ?>
        </h1>
        <a href="<?= Route::url('/admin/access/gradesReports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form id="TarjetaNotas2" name="TarjetaNotas2" method="POST" target="_blank" action="<?= Route::url('/admin/access/gradesReports/pdf/RangoGrado.php') ?>">
                <div class="mx-auto" style="max-width: 500px;">
                    <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="class"><?= $lang->translation('Grado') ?></label>
                            </div>
                        <select id="grade" name="grade" class="form-control" required>

                                <option value='all'>
                                    <?= $lang->translation('Todos') ?>
                                </option>

                            <?php foreach ($grades as $grade): ?>
                                <option value='<?= $grade ?>'>
                                    <?= $grade ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Selección') ?>
                            </label>
                        <select id="divicion" name="divicion" class="form-control" required>
                               <option value='N'><?= $lang->translation('Notas') ?></option>
                               <option value='C'><?= $lang->translation('Crédito') ?></option>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Orden') ?>
                            </label>
                        </div>
                        <select id="nota" name="nota" class="form-control" required>
                               <option value='nota1-<?= $lang->translation('Trimestre 1') ?>'><?= $lang->translation('Trimestre 1') ?></option>
                               <option value='nota2-<?= $lang->translation('Trimestre 2') ?>'><?= $lang->translation('Trimestre 2') ?></option>
                               <option value='nota3-<?= $lang->translation('Trimestre 3') ?>'><?= $lang->translation('Trimestre 3') ?></option>
                               <option value='nota4-<?= $lang->translation('Trimestre 4') ?>'><?= $lang->translation('Trimestre 4') ?></option>
                               <option value='sem1-<?= $lang->translation('Semestre 1') ?>'><?= $lang->translation('Semestre 1') ?></option>
                               <option value='sem2-<?= $lang->translation('Semestre 2') ?>'><?= $lang->translation('Semestre 2') ?></option>
                               <option value='final-<?= $lang->translation('Promedio final') ?>'><?= $lang->translation('Promedio final') ?></option>
                        </select>
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Selección') ?>
                            </label>
                        </div>
                        <select id="valor" name="valor" class="form-control" required>
                               <option value='P'><?= $lang->translation('Porciento') ?></option>
                               <option value='D'><?= $lang->translation('Decimal') ?></option>
                        </select>
                    </div>
                    <div class="input-group mb-3">


                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Notas Redondeada') ?>
                            </label>
                        </div>
                        <select id="notar" name="notar" class="form-control" required>
                               <option value='0'><?= $lang->translation('Si') ?></option>
                               <option value='2'><?= $lang->translation('No') ?></option>
                        </select>

                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Con Lineas') ?>
                            </label>
                        </div>
                        <select id="cl" name="cl" class="form-control" required>
                               <option value='1'><?= $lang->translation('Si') ?></option>
                               <option value='0'><?= $lang->translation('No') ?></option>
                        </select>



                    </div>


                    <button name='create' type="submit" class="btn btn-primary d-block mx-auto">
                        <?= $lang->translation('Continuar') ?>
                    </button>
                </div>
            </form>
        </div>

    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>
</html>