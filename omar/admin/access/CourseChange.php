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
    ['Para cambiar de un curso a otro curso.', 'To change from one course to another course.'],
    ['Curso', 'Course'],
    ['Descripción', 'Description'],
    ['Selección', 'Selecction'],
    ['Selección del curso', 'Course selection'],
    ['Estás seguro que quieres cambiar el curso del estudiante?', 'Are you sure you want to change the student course?'],
    ['Peso', 'Peso'],
    ['Maestro', 'Teacher'],
    ['Horario entrada', 'Enter time'],
    ['Horario salida', 'Exit time'],
    ['Curso nuevo para cambiar', 'New course to change'],
    ['Avanzada', 'Advance'],
    ['Valor', 'Value'],
    ['Regular', 'Regular'],
    ['Verano', 'Summer'],
    ['Si', 'Yes'],
    ['Lista', 'List'],
    ['Guardar', 'Save'],
    ['Crear', 'Create'],
    ['Buscar', 'Search'],
    ['Cancelar', 'Cancel'],
    ['Cambiar', 'Change'],
]);
//$years = DB::table('year')->select("DISTINCT year")->get();
$school = new School(Session::id());


$teachers = new Teacher;
$teachers = $teachers->all();
if (isset($_REQUEST['search'])) {
//    $thisCourse = DB::table('cursos')->where('mt', $_REQUEST['course'])->first();

$MyCourses = DB::table('padres')->where([
    ['year', $school->info('year2')],
    ['ss', $_POST['ss']]
])->get();

}
if (isset($_REQUEST['save'])) {
    $course1 = $_POST['course1'];
    $course2 = $_POST['course2'];
    $ss = $_POST['ss'];

    $courses = DB::table('cursos')->where([
       ['year', $school->info('year2')],
       ['curso', $course2]
    ])->first();
    $teacher = new Teacher($courses->id);

//echo $courses->curso.' / '.$ss.' / '.$teacher->nombre;
    $thisCourse2 = DB::table('padres')->where([
        ['curso', $course1],
        ['ss', $ss],
        ['year', $school->info('year2')]
    ])->update([
        'descripcion' => $courses->desc1,
        'profesor' => "$teacher->nombre $teacher->apellidos",
        'desc2' => $courses->desc2,
        'credito' => $courses->credito,
        'peso' => $courses->peso,
        'id' => $teacher->id,
        'dias' => $courses->dias,
        'ava' => $courses->ava,
        'valor' => $courses->valor,
        'verano' => $courses->verano,
        'curso' => $course2,
    ]);
    for ($a = 2; $a <= 6; $a++) {
        $thisCourse2 = DB::table("padres$a")->where([
            ['curso', $course1],
            ['ss', $ss],
            ['year', $school->info('year2')]
        ])->update([
            'id' => $teacher->id,
            'curso' => $course2,
            'descripcion' => $courses->desc1,
            'profesor' => "$teacher->nombre $teacher->apellidos",
        ]);
    }

}

if (isset($_REQUEST['delete2'])) {
//    DB::table('cursos')->where('mt', $_REQUEST['courseId'])->delete();
}


$courses = DB::table('cursos')->where([
    ['year', $school->info('year2')]
])->get();

$estudiantes = DB::table('year')->where([
    ['year', $school->info('year2')],
    ['codigobaja', 0]
])->orderBy('apellidos')->get();


?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('Para cambiar de un curso a otro curso.');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Para cambiar de un curso a otro curso.') ?></h1>
        <div class="container">
            <div class="mx-auto bg-white shadow-lg py-5 px-3 rounded" style="max-width: 500px;">

                <form method="POST">
                    <label for="valor"><?= $lang->translation("Selección") ?></label>
                    <select class="form-control selectpicker w-100" name="ss" data-live-search="true" required>
                        <?php foreach ($estudiantes as $estudiante) : ?>
                            <option <?= $_POST['ss'] == $estudiante->ss ? 'selected=""' : '' ?> value="<?= $estudiante->ss ?>"><?= "$estudiante->apellidos - $estudiante->nombre ($estudiante->grado)" ?></option>
                        <?php endforeach ?>
                    </select>
                    <input name="search" class="btn btn-primary mx-auto d-block mt-1" type="submit" value="<?= $lang->translation("Buscar") ?>">
                </form>


                <?php if (isset($_POST['search'])) : ?>

                <form method="POST">
            <div class="mx-auto bg-white shadow-lg py-5 px-3 rounded" style="max-width: 500px;">
                    <label for="valor"><?= $lang->translation("Selección del curso") ?></label>
                    <div class="row">
                    <select class="form-control selectpicker w-100" name="course1" data-live-search="true" required>
                        <?php foreach ($MyCourses as $course) : ?>
                            <option value="<?= $course->curso ?>"><?= "$course->curso - $course->descripcion ($course->id)" ?></option>
                        <?php endforeach ?>
                    </select>
                    </div>
            </div>

            <div class="mx-auto0 bg-white shadow-lg py-5 px-3 rounded" style="max-width: 500px;">
                    <div class="row">
                        <div class="col-7">
                            <label for="valor"><?= $lang->translation("Curso nuevo para cambiar") ?></label>
                            <div class="input-group">
                    <select class="form-control selectpicker w-100" name="course2" data-live-search="true" required>
                        <?php foreach ($courses as $course) : ?>
                            <option value="<?= $course->curso ?>"><?= "$course->curso - $course->desc1 ($course->id)" ?></option>
                        <?php endforeach ?>
                    </select>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="ss" value="<?= $_POST['ss'] ?>">

                <div class="mx-auto0 bg-white shadow-lg py-5 px-3 rounded" style="max-width: 500px;">
                     <div class="col-12 text-center">
                         <a href="CourseChange.php" class="btn btn-secondary"><?= $lang->translation('Cancelar') ?></a>
                         <button type="submit" class="btn btn-danger" name="save" type="submit" onclick="return confirmar('<?= $lang->translation('Estás seguro que quieres cambiar el curso del estudiante?') ?>')"><?= $lang->translation('Cambiar') ?></button>
                     </div>
                </div>
              </form>
             <?php endif ?>
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