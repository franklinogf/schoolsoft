<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Teacher;

Session::is_logged();
$lang = new Lang([
    ['Mensajes por clase para la tarjeta de notas', 'Class Note Card Messages'],
    ['Código', 'Code'],
    ['Descripción', 'Description'],
    ['Comentario', 'Comment'],
    ['Estás seguro que quieres borrar el comentario?', 'Are you sure you want to delete the comment?'],
    ['Credito', 'Credit'],
    ['Peso', 'Peso'],
    ['Maestro', 'Teacher'],
    ['Horario entrada', 'Enter time'],
    ['Horario salida', 'Exit time'],
    ['Días', 'Days'],
    ['Avanzada', 'Advance'],
    ['Valor', 'Value'],
    ['Regular', 'Regular'],
    ['Verano', 'Summer'],
    ['Si', 'Yes'],
    ['Lista', 'List'],
    ['Guardar', 'Save'],
    ['Crear', 'Create'],
    ['Buscar', 'Search'],
    ['Limpiar', 'Clear'],
    ['Eliminar', 'Delete'],
]);
//$years = DB::table('year')->select("DISTINCT year")->get();
$school = new School(Session::id());


$teachers = new Teacher;
//$teachers = $teachers->all();
if (isset($_REQUEST['search'])) {
    $thisCourse = DB::table('comentarios')->where('codigo', $_REQUEST['course'])->first();
}
if (isset($_REQUEST['save'])) {
    $thisCourse = DB::table('comentarios')->where('codigo', $_REQUEST['courseId'])->update([
        'code' => $_POST['code'],
        'comenta' => $_POST['comenta'],
    ]);
}
if (isset($_REQUEST['create'])) {
    DB::table('comentarios')->insert([
        'code' => $_POST['code'],
        'comenta' => $_POST['comenta'],
    ]);
}
if (isset($_REQUEST['delete'])) {
    DB::table('comentarios')->where('codigo', $_REQUEST['courseId'])->delete();
}
$courses = DB::table('comentarios')->orderBy('code')->get();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('Mensajes por clase para la tarjeta de notas');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Mensajes por clase para la tarjeta de notas') ?></h1>
        <div class="d-flex justify-content-center mb-3">
            <a href="<?= Route::url('/admin/access/pdf/MensajesNotas.php') ?>" target="listaA" class="btn btn-outline-primary mr-2"><?= $lang->translation('Lista') ?></a>
        </div>
        <div class="container">
            <div class="mx-auto bg-white shadow-lg py-5 px-3 rounded" style="max-width: 500px;">
                <form method="POST">
                    <select class="form-control selectpicker w-100" name="course" data-live-search="true" required>
                        <?php foreach ($courses as $course) : ?>
                            <option <?= isset($_REQUEST['course']) && $_REQUEST['course'] == $course->codigo ? 'selected=""' : '' ?> value="<?= $course->codigo ?>"><?= "$course->code - $course->comenta ($course->codigo)" ?></option>
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
                            <div class="form-group col-6 px-0">
                                <label for="curso"><?= $lang->translation("Código") ?></label>
                                <input type="text" value='<?= $thisCourse->code ?? '' ?>' class="form-control" name='code' id="curso" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="desc1"><?= $lang->translation("Comentario") ?></label>
                                <input type="text" value='<?= $thisCourse->comenta ?? '' ?>' class="form-control" maxlength="40" name='comenta' id="desc1" required>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary" name="<?= isset($_POST['search']) ? 'save' : 'create' ?>" type="submit"><?= $lang->translation(isset($_POST['search']) ? 'Guardar' : 'Crear') ?></button>
                            <?php if (isset($_POST['search'])) : ?>
                                <a href="MensajesNotas.php" class="btn btn-secondary"><?= $lang->translation('Limpiar') ?></a>
                                <button type="submit" class="btn btn-danger" name="delete" type="submit" onclick="return confirmar('<?= $lang->translation('Estás seguro que quieres borrar el comentario?') ?>')"><?= $lang->translation('Eliminar') ?></button>
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