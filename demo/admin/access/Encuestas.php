<?php
require_once __DIR__ . '/../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Teacher;

Session::is_logged();
$lang = new Lang([
    ['Encuestas', 'Surveys'],
    ['Selección', 'Selection'],
    ['Título', 'Title'],
    ['Todos', 'All'],
    ['Padres', 'Parents'],
    ['Estás seguro que quieres borrar el mensage?', 'Are you sure you want to delete the message?'],
    ['Peso', 'Peso'],
    ['Maestros', 'Teachers'],
    ['Fecha de comienzo', 'Start date'],
    ['Fecha final', 'Final date'],
    ['Días', 'Days'],
    ['Avanzada', 'Advance'],
    ['Que vean los detalles:', 'Let them see the details:'],
    ['Que puedan comentar:', 'Who can comment:'],
    ['Verano', 'Summer'],
    ['Si', 'Yes'],
    ['Lista', 'List'],
    ['Guardar', 'Save'],
    ['Crear', 'Create'],
    ['Buscar', 'Search'],
    ['Limpiar', 'Clear'],
    ['Eliminar', 'Delete'],
    ['Descripción', 'Description'],
]);
$school = new School(Session::id());
$grades = $school->allGrades();

if (isset($_REQUEST['search'])) {
    $thisCourse = DB::table('estadisticas')->where('codigo', $_REQUEST['course'])->first();
}
if (isset($_REQUEST['save'])) {
    $thisCourse = DB::table('estadisticas')->where('codigo', $_REQUEST['courseId'])->update([
        'text' => $_POST['text'],
        'grupo' => $_POST['grupo'],
        'fecha_in' => $_POST['fecha_in'],
        'fecha_out' => $_POST['fecha_out'],
        'titulo' => $_POST['titulo'],
        'vicible' => $_POST['vicible'],
        'comentario' => $_POST['comentario'],
    ]);
}
if (isset($_REQUEST['create'])) {
    DB::table('estadisticas')->insert([
        'text' => $_POST['text'],
        'grupo' => $_POST['grupo'],
        'fecha_in' => $_POST['fecha_in'],
        'fecha_out' => $_POST['fecha_out'],
        'titulo' => $_POST['titulo'],
        'vicible' => $_POST['vicible'],
        'comentario' => $_POST['comentario'],
    ]);
}
if (isset($_REQUEST['delete'])) {
    DB::table('estadisticas')->where('codigo', $_REQUEST['courseId'])->delete();
}
$courses = DB::table('estadisticas')->orderBy('titulo')->get();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation('Encuestas');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
</head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Encuestas') ?></h1>
        <div class="container">
            <div class="mx-auto bg-white shadow-lg py-5 px-3 rounded" style="max-width: 500px;">
                <form method="POST">
                    <select class="form-control selectpicker w-100" name="course" data-live-search="true" required>
                        <?php foreach ($courses as $course) : ?>
                            <option <?= isset($_REQUEST['course']) && $_REQUEST['course'] == $course->codigo ? 'selected=""' : '' ?> value="<?= $course->codigo ?>"><?= "$course->titulo ($course->codigo)" ?></option>
                        <?php endforeach ?>
                    </select>
                    <input name="search" class="btn btn-primary mx-auto d-block mt-1" type="submit" value="<?= $lang->translation("Buscar") ?>">
                </form>

                <form class="mt-3" method="POST">
                    <?php if (isset($_POST['search'])) : ?>
                        <input type="hidden" name="courseId" value="<?= $_REQUEST['course'] ?>">
                    <?php endif ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="desc1"><?= $lang->translation("Título") ?></label>
                                <input type="text" value='<?= $thisCourse->titulo ?? '' ?>' class="form-control" maxlength="40" name='titulo' id="titulo" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="desc2"><?= $lang->translation("Descripción") ?></label>
                                <textarea cols="60" name="text" rows="10"><?= $thisCourse->text ?? '' ?></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="ava"><?= $lang->translation("Selección") ?></label>
                                <select class="form-control" name="grupo" id="grupo" required>
                                    <option <?= $thisCourse->grupo ?? '' == '' ? 'selected=""' : '' ?> value=""><?= $lang->translation("Selección") ?></option>
                                    <option <?= $thisCourse->grupo ?? '' == 'Todos' ? 'selected=""' : '' ?> value="Todos"><?= $lang->translation("Todos") ?></option>
                                    <option <?= $thisCourse->grupo ?? '' == 'Maestros' ? 'selected=""' : '' ?> value="Maestros"><?= $lang->translation("Maestros") ?></option>
                                    <option <?= $thisCourse->grupo ?? '' == 'Padres' ? 'selected=""' : '' ?> value="Padres"><?= $lang->translation("Padres") ?></option>
                                    <?php foreach ($grades as $grade): ?>
                                        <option <?= $thisCourse->grupo ?? '' == $grade ? 'selected=""' : '' ?> value='<?= $grade ?>'>
                                            <?= $grade ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="entrada"><?= $lang->translation("Fecha de comienzo") ?></label>
                                <input type="date" value='<?= $thisCourse->fecha_in ?? '' ?>' class="form-control" maxlength="7" name='fecha_in' id="fecha_in">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="salida"><?= $lang->translation("Fecha final") ?></label>
                                <input type="date" value='<?= $thisCourse->fecha_out ?? '' ?>' class="form-control" maxlength="7" name='fecha_out' id="fecha_out">
                            </div>
                        </div>


                        <div class="col-6">
                            <div class="form-group">
                                <label for="entrada"><?= $lang->translation("Que vean los detalles:") ?></label>
                                <select class="form-control" name="vicible" id="vicible" required>
                                    <option <?= $thisCourse->vicible ?? '' == 'NO' ? 'selected=""' : '' ?> value="NO">No</option>
                                    <option <?= $thisCourse->vicible ?? '' == 'SI' ? 'selected=""' : '' ?> value="SI"><?= $lang->translation("Si") ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="salida"><?= $lang->translation("Que puedan comentar:") ?></label>
                                <select class="form-control" name="comentario" id="comentario" required>
                                    <option <?= $thisCourse->comentario ?? '' == 'NO' ? 'selected=""' : '' ?> value="NO">No</option>
                                    <option <?= $thisCourse->comentario ?? '' == 'SI' ? 'selected=""' : '' ?> value="SI"><?= $lang->translation("Si") ?></option>
                                </select>
                            </div>
                        </div>



                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary" name="<?= isset($_POST['search']) ? 'save' : 'create' ?>" type="submit"><?= $lang->translation(isset($_POST['search']) ? 'Guardar' : 'Crear') ?></button>
                            <?php if (isset($_POST['search'])) : ?>
                                <a href="Encuestas.php" class="btn btn-secondary"><?= $lang->translation('Limpiar') ?></a>
                                <button type="submit" class="btn btn-danger" name="delete" type="submit" onclick="return confirmar('<?= $lang->translation('Estás seguro que quieres borrar el mensage?') ?>')"><?= $lang->translation('Eliminar') ?></button>
                            <?php endif ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
    $jqMask = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::selectPicker('js');
    ?>
    <script>
        function confirmar(mensaje) {
            return confirm(mensaje);
        }
        $(document).ready(function() {

            $('.--float').mask("0.00").change(function() {
                if ($(this).val()) {
                    $(this).val(parseFloat($(this).val()).toFixed(2))
                } else {
                    $(this).val('0.00')
                }
            });
            $('#ava').change(function(e) {
                e.preventDefault();
                if ($(this).val() == 'No') {
                    $('#valor,#verano').attr('disabled', true)
                } else {
                    $('#valor,#verano').attr('disabled', false)

                }
            });
            $('.--float,#ava').change();
        });
    </script>

</body>

</html>