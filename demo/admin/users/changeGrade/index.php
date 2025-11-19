<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Controllers\Parents;
use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;

Session::is_logged();
$students = new Student();
$lang = new Lang([
    ['Cambiar grado', 'Change grade'],
    ['grado', 'grade'],
    ['Buscar', 'Search'],
]);
$year = $students->info('year');
$allGrades = DB::table('year')->select('DISTINCT grado')->where([
    ['activo', ''],
    ['year', $year]
])->orderBy('grado')->get();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Cambiar grado");
    $DataTable = true;
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>

</head>

<body class='pb-5'>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container mt-5">
        <h1 class="text-center"><?= $lang->translation("Cambiar grado") ?></h1>
        <div class="row">
            <div class="col-12">
                <form method="POST">
                    <select class="form-control w-100" name="grade" data-live-search="true" required>
                        <option value=""><?= $lang->translation("Seleccionar") . ' ' . $lang->translation('grado') ?></option>
                        <?php foreach ($allGrades as $grade) : ?>
                            <option <?= isset($_REQUEST['grade']) && $_REQUEST['grade'] == $grade->grado ? 'selected=""' : '' ?> value="<?= $grade->grado ?>"><?= $grade->grado ?></option>
                        <?php endforeach ?>
                    </select>
                    <button class="btn btn-primary btn-sm btn-block mt-2" type="submit"><?= $lang->translation("Buscar") ?></button>
                </form>

            </div>
        </div>
        <?php if (isset($_REQUEST['grade'])) :
            $grade = $_REQUEST['grade'];
            $gradeStudents = $students->findByGrade($grade)
        ?>
            <form class="mt-3" action="<?= Route::url('/admin/users/changeGrade/includes/index.php') ?>" method="POST">
                <input type="hidden" name="grade" value="<?= $grade ?>">
                <table class="dataTable table display compact  no-footer">
                    <thead class="thead-dark">
                        <tr>
                            <th>Estudiantes del grado <?= $grade ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gradeStudents as $student) : ?>
                            <tr>
                                <td><?= "$student->nombre $student->apellidos" ?></td>
                                <td><input type="text" class="form-control form-control-sm grade" style="width: 5rem; text-align: center;" name="students[<?= $student->mt ?>]" value="<?= $student->grado ?>" required></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                <div class="text-center">
                    <input class="btn btn-primary btn-lg" type="submit" value="Guardar">
                </div>

            </form>
    </div>
<?php endif ?>

</div>


<?php

Route::includeFile('/includes/layouts/scripts.php', true);

?>

</body>

</html>