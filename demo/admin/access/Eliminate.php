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
    ['Para eliminar el curso a todos los estudiantes.', 'To remove the course from all students.'],
    ['Curso', 'Course'],
    ['Descripción', 'Description'],
    ['español', 'spanish'],
    ['inglés', 'english'],
    ['Crédito', 'Credit'],
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
    ['Cancelar', 'Cancel'],
    ['Tenga en cuenta que los cursos para los estudiantes se eliminan permanentemente.', 'Please note that student courses are permanently removed.'],
    ['Buscar', 'Search'],
    ['Estás seguro que desea borrar el curso a todos los Estudiante?', 'Are you sure you want to delete the course for all Students?'],
    ['Eliminar', 'Delete'],
]);
$years = DB::table('year')->select("DISTINCT year")->get();
$school = new School(Session::id());


$teachers = new Teacher;
$teachers = $teachers->all();
if (isset($_REQUEST['search'])) {
    $thisCourse = DB::table('cursos')->where('mt', $_REQUEST['course'])->first();
}

if (isset($_REQUEST['delete'])) {
    DB::table('padres')->where([
        ['curso', $_POST['course']],
        ['year', $school->info('year2')]
    ])->delete();

    for ($a = 2; $a <= 6; $a++) {
        DB::table("padres$a")->where([
            ['curso', $_POST['course']],
            ['year', $school->info('year2')]
        ])->delete();
    }
}


$courses = DB::table('cursos')->where([
    ['year', $school->info('year2')]
])->get();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('Para eliminar el curso a todos los estudiantes.');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Para eliminar el curso a todos los estudiantes.') ?></h1>
        <div class="container">
            <div class="mx-auto bg-white shadow-lg py-5 px-3 rounded" style="max-width: 500px;">
                <form method="POST">
                    <select class="form-control selectpicker w-100" name="course" data-live-search="true" required>
                        <?php foreach ($courses as $course) : ?>
                            <option <?= isset($_REQUEST['course']) && $_REQUEST['course'] == $course->mt ? 'selected=""' : '' ?> value="<?= $course->mt ?>"><?= "$course->curso - $course->desc1 ($course->id)" ?></option>
                        <?php endforeach ?>
                    </select>
                    <input name="search" class="btn btn-primary mx-auto d-block mt-1" type="submit" value="<?= $lang->translation("Buscar") ?>">
                </form>
                <?
                if (isset($_REQUEST['search'])) {
                ?>
                    <form class="mt-3" method="POST">
                        <?php if (isset($_POST['search'])) : ?>
                            <input type="hidden" name="courseId" value="<?= $_REQUEST['course'] ?>">
                        <?php endif ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group col-6 px-0">
                                    <label for="curso"><?= $lang->translation("Curso") ?></label>
                                    <input type="text" disabled value='<?= $thisCourse->curso ?? '' ?>' class="form-control" name='curso' id="curso" required>
                                    <input type="hidden" name="course" value="<?= $thisCourse->curso ?? '' ?>">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="desc1"><?= $lang->translation("Descripción") . ' ' . $lang->translation("español") ?></label>
                                    <input type="text" disabled value='<?= $thisCourse->desc1 ?? '' ?>' class="form-control" maxlength="40" name='desc1' id="desc1" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="desc2"><?= $lang->translation("Descripción") . ' ' . $lang->translation("inglés") ?></label>
                                    <input type="text" disabled value='<?= $thisCourse->desc2 ?? '' ?>' class="form-control" maxlength="40" name='desc2' id="desc2" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="credito"><?= $lang->translation("Crédito") ?></label>
                                    <input type="text" disabled value='<?= $thisCourse->credito ?? '' ?>' class="form-control --float" name='credito' id="credito" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="peso"><?= $lang->translation("Peso") ?></label>
                                    <input type="text" disabled value='<?= $thisCourse->peso ?? '' ?>' class="form-control --float" name='peso' id="peso" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="teacherId"><?= $lang->translation("Maestro") ?></label>
                                    <select class="form-control selectpicker w-100" name="teacherId" id="teacherId" data-live-search="true" disabled required>
                                        <option value=""><?= $lang->translation("Seleccionar") ?></option>
                                        <?php foreach ($teachers as $teacher) : ?>
                                            <option <?= $thisCourse->id ?? '' == $teacher->id ? 'selected=""' : '' ?> value="<?= $teacher->id ?>"><?= "$teacher->id - $teacher->nombre $teacher->apellidos" ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="entrada"><?= $lang->translation("Horario entrada") ?></label>
                                    <input type="text" disabled value='<?= $thisCourse->entrada ?? '' ?>' class="form-control" maxlength="7" name='entrada' id="entrada">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="salida"><?= $lang->translation("Horario salida") ?></label>
                                    <input type="text" disabled value='<?= $thisCourse->salida ?? '' ?>' class="form-control" maxlength="7" name='salida' id="salida">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group col-6 px-0">
                                    <label for="dias"><?= $lang->translation("Días") ?></label>
                                    <input type="text" disabled value='<?= $thisCourse->dias ?? '' ?>' class="form-control" maxlength="6" name='dias' id="dias">
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="form-group">
                                    <label for="ava"><?= $lang->translation("Avanzada") ?></label>
                                    <select class="form-control" name="ava" id="ava" disabled required>
                                        <option <?= $thisCourse->ava ?? '' == 'No' ? 'selected=""' : '' ?> value="No">No</option>
                                        <option <?= $thisCourse->ava ?? '' == 'Si' ? 'selected=""' : '' ?> value="Si"><?= $lang->translation("Si") ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-7">
                                <label for="valor"><?= $lang->translation("Valor") ?></label>
                                <div class="input-group">
                                    <input type="text" value='<?= $thisCourse->valor ?? '' ?>' class="form-control --float" name='valor' id="valor" disabled>
                                    <select class="form-control" name="verano" id="verano" disabled>
                                        <option <?= $thisCourse->verano ?? '' == '' ? 'selected=""' : '' ?> value=""><?= $lang->translation("Regular") ?></option>
                                        <option <?= $thisCourse->verano ?? '' == '2' ? 'selected=""' : '' ?> value="2"><?= $lang->translation("Verano") ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <label for="dias"><?= $lang->translation("Tenga en cuenta que los cursos para los estudiantes se eliminan permanentemente.") ?></label>
                            </div>

                            <div class="col-12 text-center">
                                <?php if (isset($_POST['search'])) : ?>
                                    <a href="Eliminate.php" class="btn btn-secondary"><?= $lang->translation('Cancelar') ?></a>
                                    <button type="submit" class="btn btn-danger" name="delete" type="submit" onclick="return confirmar('<?= $lang->translation('Estás seguro que desea borrar el curso a todos los Estudiante?') ?>')"><?= $lang->translation('Eliminar') ?></button>
                                <?php endif ?>
                            </div>
                        </div>
                    </form>
                <?
                }
                ?>
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