<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;

Session::is_logged();
$lang = new Lang([
    ['Programas especiales', 'Special programs'],
    ['Buscar', 'Search'],
    ['estudiante', 'student'],
    ['Cursos disponibles', 'Available courses'],
    ['Agregar', 'Add'],
    ['Cursos del estudiante', 'Student courses'],
    ['Borrar todo', 'Delete everything'],
    ['Tiene notas', 'Has notes'],
    ['Borrar', 'Delete'],
]);
$students = new Student();
$school = new School();
$students = $students->All();
$availableCourses = DB::table('cursos')->where(['year', $school->year()])->orderBy('curso')->get();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation('Programas especiales');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5">
            <?= $lang->translation('Programas especiales') ?>
        </h1>
        <div class="container bg-white shadow-lg p-3 rounded">
            <form method="POST">
                <select class="form-control selectpicker w-100" name="student" data-live-search="true" title="<?= $lang->translation("Seleccionar") . ' ' . $lang->translation('estudiante') ?>" required>
                    <?php foreach ($students as $student): ?>
                        <option <?= isset($_REQUEST['student']) && $_REQUEST['student'] == $student->ss ? 'selected=""' : '' ?> value="<?= $student->ss ?>">
                            <?= "$student->apellidos $student->nombre ($student->id)" ?>
                        </option>
                    <?php endforeach ?>
                </select>
                <button class="btn btn-primary btn-sm btn-block mt-2" type="submit">
                    <?= $lang->translation("Buscar") ?>
                </button>
            </form>

            <?php if ($_REQUEST['student']):
                $studentCourses = DB::table('padres')->where([
                    ['ss', $_REQUEST['student']],
                    ['year', $school->year()]
                ])->orderBy('curso')->get();
                ?>
                <input type="hidden" id="studentSS" value="<?= $_REQUEST['student'] ?>">
                <div class="row mt-4">
                    <div class="col-12 col-md-6 gap-2">
                        <header class="mb-1">
                            <h3 class="flex-grow-1 text-center">
                                <?= $lang->translation("Cursos disponibles"); ?>
                            </h3>
                        </header>
                        <div class="shadow-sm" style="height: 500px; overflow-y:scroll;">
                            <ul id="availableCourses" class="list-group">
                                <?php foreach ($availableCourses as $course):
                                    $desc = __LANG === 'es' ? $course->desc1 : $course->desc2;
                                    ?>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>
                                            <?= "$course->curso - $desc ($course->id)" ?>
                                        </span>
                                        <buttton data-id="<?= $course->id ?>" class="addCourse btn btn-sm btn-primary">
                                            <?= $lang->translation("Agregar"); ?> >
                                        </buttton>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <header class="d-flex align-items-center mb-1 mt-2 mt-md-0">
                            <h3 class="text-center">
                                <?= $lang->translation("Cursos del estudiante"); ?>
                            </h3>
                            <buttton id="removeAllCourses" class="btn btn-sm btn-danger ml-auto">
                                <?= $lang->translation("Borrar todos"); ?>
                            </buttton>
                        </header>

                        <div class="shadow-sm" style="height: 500px; overflow-y:scroll;">
                            <ul id="studentCourses" class="list-group">
                                <?php foreach ($studentCourses as $course):
                                    $desc = __LANG === 'es' ? $course->descripcion : $course->desc2;
                                    ?>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>
                                            <?= "$course->curso - $desc ($course->id)" ?>
                                            <?php if ($course->nota1 !== '' || $course->nota2 !== '' || $course->nota3 !== '' || $course->nota4 !== ''): ?>
                                                <span class="badge text-bg-warning ml-1">
                                                    <?= $lang->translation("Tiene notas"); ?>
                                                </span>
                                            <?php endif ?>
                                        </span>
                                        <buttton data-id="<?= $course->id ?>" class="removeCourse btn btn-sm btn-danger">
                                            <?= $lang->translation("Borrar"); ?>
                                        </buttton>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif ?>

        </div>




    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::selectPicker('js');
    ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#availableCourses").on('click', '.addCourse', function (e) {
                const text = $(this).prev('span').text().trim()
                const id = $(this).data('id')
                let canBeAdded = true
                $("#studentCourses li").each((index, $li) => {
                    if ($($li).children('.removeCourse').data('id') === id) {
                        console.log('button ---> ', $($li).children('.removeCourse').data('id'));
                        canBeAdded = false
                        return false
                    }
                });
                if (canBeAdded) {
                    $.post(includeThisFile(), {
                        addCourse: true,
                        courseID: id,
                        studentSS: $("#studentSS").val(),
                    }, function (data) {
                        console.log('Add course --> ', data);
                        $("#studentCourses").prepend(`
                        <li class="list-group-item d-flex justify-content-between bg-info">
                            <span>
                                ${text}
                            </span>
                            <buttton data-id="${id}" class="removeCourse btn btn-sm btn-danger">Borrar</buttton>
                        </li >
                        `)
                        setTimeout(() => {
                            $(`.removeCourse[data-id='${id}']`).parent('li').removeClass('bg-info')
                        }, 1000)
                    });

                } else {
                    alert('Este curso ya ha sido agregado')
                }

            })

            $("#studentCourses").on('click', ".removeCourse", function (e) {
                if (confirm('Esta seguro que desea eliminar este curso?')) {
                    const id = $(this).data('id')
                    const $li = $(this).parent('li')
                    $.post(includeThisFile(), {
                        removeCourse: true,
                        courseID: id,
                        studentSS: $("#studentSS").val(),
                    }, function (data) {
                        console.log('Remove course --> ', data);
                        $li.remove()
                    });
                }

            })
            $("#removeAllCourses").on('click', function (e) {
                if (confirm('Esta seguro que desea eliminar todos los cursos?')) {
                    $.post(includeThisFile(), {
                        removeAllCourses: true,
                        studentSS: $("#studentSS").val(),
                    }, function (data) {
                        console.log('Remove all courses --> ', data);
                        $("#studentCourses li").remove();
                    });
                }

            })


        })

    </script>

</body>

</html>