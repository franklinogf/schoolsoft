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
    ['Catálogo', 'Catalog'],
    ['Curso', 'Course'],
    ['Descripción', 'Description'],
    ['español', 'spanish'],
    ['inglés', 'english'],
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
    ['Estás seguro que quieres borrar el curso?', 'Are you sure you want to delete the course?'],
]);
$years = DB::table('year')->select("DISTINCT year")->get();
$school = new School(Session::id());


$teachers = new Teacher;
$teachers = $teachers->all();
if (isset($_REQUEST['search'])) {
    $thisCourse = DB::table('cursos')->where('mt', $_REQUEST['course'])->first();
}
if (isset($_REQUEST['save'])) {
    $teacher = new Teacher($_POST['teacherId']);
    $thisCourse = DB::table('cursos')->where('mt', $_REQUEST['courseId'])->update([
        'curso' => $_POST['curso'],
        'desc1' => $_POST['desc1'],
        'desc2' => $_POST['desc2'],
        'credito' => $_POST['credito'],
        'peso' => $_POST['peso'],
        'id' => $_POST['teacherId'],
        'entrada' => $_POST['entrada'],
        'salida' => $_POST['salida'],
        'dias' => $_POST['dias'],
        'ava' => $_POST['ava'],
        'valor' => $_POST['valor'],
        'verano' => $_POST['verano'],
    ]);

    $thisCourse2 = DB::table('padres')->where([
        ['curso', $_POST['curso']],
        ['year', $school->info('year2')]
    ])->update([
        'descripcion' => $_POST['desc1'],
        'profesor' => "$teacher->nombre $teacher->apellidos",
        'desc2' => $_POST['desc2'],
        'credito' => $_POST['credito'],
        'peso' => $_POST['peso'],
        'id' => $_POST['teacherId'],
        'dias' => $_POST['dias'],
        'ava' => $_POST['ava'],
        'valor' => $_POST['valor'],
        'verano' => $_POST['verano'],
    ]);
    for ($a = 2; $a <= 6; $a++) {
        $thisCourse2 = DB::table("padres$a")->where([
            ['curso', $_POST['curso']],
            ['year', $school->info('year2')]
        ])->update([
            'descripcion' => $_POST['desc1'],
            'profesor' => "$teacher->nombre $teacher->apellidos",
        ]);
    }
}
if (isset($_REQUEST['create'])) {
    DB::table('cursos')->insert([
        'year' => $school->info('year2'),
        'curso' => $_POST['curso'],
        'desc1' => $_POST['desc1'],
        'desc2' => $_POST['desc2'],
        'credito' => $_POST['credito'],
        'peso' => $_POST['peso'],
        'id' => $_POST['teacherId'],
        'entrada' => $_POST['entrada'],
        'salida' => $_POST['salida'],
        'dias' => $_POST['dias'],
        'ava' => $_POST['ava'],
        'valor' => $_POST['valor'],
        'verano' => $_POST['verano'],
    ]);
}
if (isset($_REQUEST['delete'])) {
    DB::table('cursos')->where('mt', $_REQUEST['courseId'])->delete();
}
$courses = DB::table('cursos')->where([
    ['year', $school->info('year2')]
])->get();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation('Catálogo');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Catálogo') ?></h1>
        <div class="d-flex justify-content-center mb-3">
            <a href="<?= Route::url('/admin/access/pdf/listA.php') ?>" target="listaA" class="btn btn-outline-primary mr-2"><?= $lang->translation('Lista') ?> A</a>
            <a href="<?= Route::url('/admin/access/pdf/listB.php') ?>" target="listaB" class="btn btn-outline-primary"><?= $lang->translation('Lista') ?> B</a>
        </div>
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

                <form class="mt-3" method="POST">
                    <?php if (isset($_POST['search'])) : ?>
                        <input type="hidden" name="courseId" value="<?= $_REQUEST['course'] ?>">
                    <?php endif ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group col-6 px-0">
                                <label for="curso"><?= $lang->translation("Curso") ?></label>
                                <input type="text" value='<?= $thisCourse->curso ?>' class="form-control" name='curso' id="curso" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="desc1"><?= $lang->translation("Descripción") . ' ' . $lang->translation("español") ?></label>
                                <input type="text" value='<?= $thisCourse->desc1 ?>' class="form-control" maxlength="40" name='desc1' id="desc1" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="desc2"><?= $lang->translation("Descripción") . ' ' . $lang->translation("inglés") ?></label>
                                <input type="text" value='<?= $thisCourse->desc2 ?>' class="form-control" maxlength="40" name='desc2' id="desc2" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="credito"><?= $lang->translation("Credito") ?></label>
                                <input type="text" value='<?= $thisCourse->credito ?>' class="form-control --float" name='credito' id="credito" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="peso"><?= $lang->translation("Peso") ?></label>
                                <input type="text" value='<?= $thisCourse->peso ?>' class="form-control --float" name='peso' id="peso" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="teacherId"><?= $lang->translation("Maestro") ?></label>
                                <select class="form-control selectpicker w-100" name="teacherId" id="teacherId" data-live-search="true" required>
                                    <option value=""><?= $lang->translation("Seleccionar") ?></option>
                                    <?php foreach ($teachers as $teacher) : ?>
                                        <option <?= $thisCourse->id == $teacher->id ? 'selected=""' : '' ?> value="<?= $teacher->id ?>"><?= "$teacher->id - $teacher->nombre $teacher->apellidos" ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="entrada"><?= $lang->translation("Horario entrada") ?></label>
                                <input type="text" value='<?= $thisCourse->entrada ?>' class="form-control" maxlength="7" name='entrada' id="entrada">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="salida"><?= $lang->translation("Horario salida") ?></label>
                                <input type="text" value='<?= $thisCourse->salida ?>' class="form-control" maxlength="7" name='salida' id="salida">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group col-6 px-0">
                                <label for="dias"><?= $lang->translation("Días") ?></label>
                                <input type="text" value='<?= $thisCourse->dias ?>' class="form-control" maxlength="6" name='dias' id="dias">
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <label for="ava"><?= $lang->translation("Avanzada") ?></label>
                                <select class="form-control" name="ava" id="ava" required>
                                    <option <?= $thisCourse->ava == 'No' ? 'selected=""' : '' ?> value="No">No</option>
                                    <option <?= $thisCourse->ava == 'Si' ? 'selected=""' : '' ?> value="Si"><?= $lang->translation("Si") ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-7">
                            <label for="valor"><?= $lang->translation("Valor") ?></label>
                            <div class="input-group">
                                <input type="text" value='<?= $thisCourse->valor ?>' class="form-control --float" name='valor' id="valor" disabled>
                                <select class="form-control" name="verano" id="verano" disabled>
                                    <option <?= $thisCourse->verano == '' ? 'selected=""' : '' ?> value=""><?= $lang->translation("Regular") ?></option>
                                    <option <?= $thisCourse->verano == '2' ? 'selected=""' : '' ?> value="2"><?= $lang->translation("Verano") ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary" name="<?= isset($_POST['search']) ? 'save' : 'create' ?>" type="submit"><?= $lang->translation(isset($_POST['search']) ? 'Guardar' : 'Crear') ?></button>
                            <?php if (isset($_POST['search'])) : ?>
                                <a href="catalog.php" class="btn btn-secondary"><?= $lang->translation('Limpiar') ?></a>
                                <button type="submit" class="btn btn-danger" name="delete" type="submit" onclick="return confirmar('<?= $lang->translation('Estás seguro que quieres borrar el curso?') ?>')"><?= $lang->translation('Eliminar') ?></button>
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