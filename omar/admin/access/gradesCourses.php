<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();
$lang = new Lang([
    ['Cursos por grado', 'Courses by grade'],
    ['Buscar', 'Search'],
    ['Nuevo', 'New'],
    ['Borrar', 'Delete'],
    ['Cursos', 'Courses'],
    ['Descripción', 'Description'],
    ['Grado', 'Grade'],
    ['Nuevo grado creado', 'New grade created'],
]);
$school = new School(Session::id());
$grades = DB::table('materias')->where('year', $school->info('year'))->orderBy('grado')->get();
$allCourses = DB::table('cursos')->where('year', $school->info('year'))->orderBy('curso')->get();

$courses = [];
foreach ($allCourses as $course) {
    $courses[] = ['course' => $course->curso, 'desc' => $course->desc1];
}
$courses = json_encode($courses);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation('Cursos por grado');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Cursos por grado') ?></h1>
        <div class="mx-auto" style="max-width: 500px;">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="searchGrade"><?= $lang->translation('Grado') ?></label>
                </div>
                <select id="searchGrade" name="searchGrade" class="custom-select" required>
                    <?php foreach ($grades as $grade) : ?>
                        <option value='<?= $grade->grado ?>'><?= $grade->grado ?></option>
                    <?php endforeach ?>
                </select>
                <div class="input-group-append">
                    <button id="search" class="btn btn-primary"><?= $lang->translation('Buscar') ?></button>
                </div>
                <div class="input-group-append">
                    <button id='new' class="btn btn-secondary"><?= $lang->translation('Nuevo') ?></button>
                </div>
                <div class="input-group-append">
                    <button id='delete' class="btn btn-danger hidden"><?= $lang->translation('Borrar') ?></button>
                </div>
            </div>
        </div>
        <div class="container bg-white shadow-lg py-3 rounded">
            <div class="row">
                <div class="col-12 mb-1 d-flex align-items-center">
                    <div class="mr-2">
                        <label for="grade"><?= $lang->translation('Grado') ?></label>
                        <input type="text" name="grade" id="grade" class="form-control form-control-sm" style="max-width: 100px;" disabled>
                    </div>
                    <div id="gradeAlert" class="alert alert-success mb-0 hidden" role="alert">

                    </div>
                </div>
                <div class="col-6">
                    <?php for ($i = 1; $i <= 20; $i++) : ?>
                        <div class="row mb-1">
                            <label for="des<?= $i ?>" class="col-1"><?= $i ?>.</label>
                            <select data-index='<?= $i ?>' name="des<?= $i ?>" id="des<?= $i ?>" class="form-control form-control-sm col-5 course" disabled>
                                <option value=""></option>
                            </select>
                            <label for="des<?= $i ?>" class="col-6"></label>
                        </div>
                    <?php endfor ?>
                </div>
                <div class="col-6">
                    <?php for ($i = 21; $i <= 40; $i++) : ?>
                        <div class="row mb-1">
                            <label for="des<?= $i ?>" class="col-1"><?= $i ?>.</label>
                            <select data-index='<?= $i ?>' name="des<?= $i ?>" id="des<?= $i ?>" class="form-control form-control-sm col-5 course" disabled>
                                <option value=""></option>
                            </select>
                            <label for="des<?= $i ?>" class="col-6"></label>
                        </div>
                    <?php endfor ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
    <script>
        $(function() {
            // {course:'',desc:''}
            const courses = JSON.parse('<?= $courses ?>')
            let selectedGrade = false;
            courses.map((course) => {
                $('select.course').append(`<option>${course.course}</option>`)
            })
            $("#grade").change(function(e) {
                console.log(selectedGrade);
                if (selectedGrade) {
                    $.post(includeThisFile(), {
                            'changeGrade': $('#grade').val(),
                        },
                        function(data, textStatus, jqXHR) {
                            if (data.exist) {
                                $('#gradeAlert').removeClass('alert-success').addClass('alert-danger').text('Ya existe este grado').show();
                                $("#grade").val(selectedGrade)
                            } else {
                                $('#gradeAlert').removeClass('alert-danger').addClass('alert-success').text('Grado copiado').show();
                                $("select.course").change();
                                selectedGrade = $("#grade").val()
                                $('#searchGrade').prepend(`<option value="${selectedGrade}">${selectedGrade}</option>`)
                                $('#searchGrade').val(selectedGrade)
                            }
                        },
                        "json"
                    );

                }
            })
            $("select.course").change(function(e) {
                if ($('#grade').val() !== '') {
                    value = $(this).val()
                    let desc = ''
                    if (value !== '') {
                        const course = courses.find(c => c.course === value)
                        desc = course.desc;
                    }
                    $(this).next('label').text(desc)
                    $.post(includeThisFile(), {
                        'changeCourse': $(this).data('index'),
                        value,
                        desc,
                        'grade': $('#grade').val()
                    }, function(data) {
                        if (data.new) {
                            $('#searchGrade').prepend(`<option value="${selectedGrade}">${selectedGrade}</option>`)
                            $('#searchGrade').val($('#grade').val())
                            $('#gradeAlert').text('Nuevo grado creado').show();
                        }
                    }, 'json');
                } else {
                    alert('Debe de escribir un grado primero')
                    $(this).val('');
                    $(this).next('label').text('');
                }
            })

            $("#search").click(function(e) {
                $('#gradeAlert').text('').hide();
                $('.course').prop('disabled', false);
                $('#grade').prop('disabled', false).val($('#searchGrade').val());
                $('#delete').show();
                $.post(includeThisFile(), {
                        'searchGrade': $('#searchGrade').val()
                    },
                    function(data, textStatus, jqXHR) {
                        for (let index = 1; index <= 40; index++) {
                            $(`select.course#des${index}`).val(data[`curso${index}`])
                            $(`select.course#des${index}`).next('label').text(data[`des${index}`])
                        }
                        selectedGrade = $('#searchGrade').val()
                    },
                    "json"
                );
            })
            $("#new").click(function(e) {
                $('.course').prop('disabled', false).val('');
                $('.course').next('label').text('');
                $('#grade').prop('disabled', false).val('');
                $('#delete').hide();
                $('#gradeAlert').text('').hide();

            })
            $("#delete").click(function(e) {
                if (confirm("Esta seguro que desea borrarlo?")) {
                    const grade = $("#grade").val()
                    $.post(includeThisFile(), {
                            deleteGrade: grade
                        },
                        function(data, textStatus, jqXHR) {
                            $('.course').prop('disabled', true).val('');
                            $('.course').next('label').text('');
                            $('#grade').prop('disabled', true).val('');
                            $('#delete').hide();
                            $(`#searchGrade > option[value=${grade}]`).remove()
                        });
                }
            })
        });
    </script>

</body>

</html>