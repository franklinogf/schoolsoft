<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Teacher;

Session::is_logged();
$teacher = new Teacher(Session::id());

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = "Informe de Asistencia Diaria";
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 px-0">
        <h1 class="text-center mt-2">Informe de Asistencia Diaria</h1>
        <form action="<?= Route::url('/regiweb/grades/pdf/pdfDailyAttendance.php') ?>" method="post" target="pdfDailyAttendance">
            <h3 class="text-center">Seleccione las fechas</h3>
            <div class="mx-1 row">
                <div class="col-12 col-md-6">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="class">Desde</label>
                        </div>
                        <input class="form-control" type="date" name="date1" required>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="class">Hasta</label>
                        </div>
                        <input class="form-control" type="date" name="date2" value="<?= Util::date() ?>" required>
                    </div>
                </div>
                <div class="col-12 offset-md-3 col-md-6 mb-3">
                    <select name="option" id="option" class="form-control">
                        <option value="home">Salon Hogar</option>
                        <option value="students">Por estudiante</option>
                    </select>
                </div>
                <div class="col-12 offset-md-3 col-md-6" id="infoType">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="class">Tipo</label>
                        </div>
                        <select name="type"  class="form-control">
                            <option value="list">Lista</option>
                            <option value="resum">Resumen</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 offset-md-3 col-md-6 hidden" id="infoStudents">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="class">Estudiantes</label>
                        </div>
                        <select name="ss" class="form-control">
                            <?php foreach ($teacher->homeStudents() as $student) : ?>
                                <option value="<?= $student->ss ?>"><?= "$student->apellidos, $student->nombre" ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
            </div>
            <input class="btn btn-primary mx-auto d-block my-3" type="submit" value="<?= Lang::$continue ?>">
        </form>

    </div>

</body>
<?php
Route::includeFile('/includes/layouts/scripts.php', true);
?>

</html>