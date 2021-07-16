<?php
require_once '../../app.php';

use Classes\Lang;
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
    $title = "Informe de Asistencia";
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 px-0">
        <h1 class="text-center mt-2">Informe de Asistencia</h1>
        <form action="<?= Route::url('/regiweb/grades/pdf/pdfAttendance.php') ?>" method="post" target="pdfAttendance">
            <div class="mx-auto mt-5" style="width: 20rem;">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="class"><?= Lang::$trans['grade'] ?></label>
                    </div>
                    <select name="class" class="custom-select" id="class" required>                        
                        <option value="grado" selected>Salón Hogar</option>
                        <?php foreach ($teacher->classes() as $class) : ?>
                            <option value="<?= $class->curso ?>"><?= "$class->curso - $class->desc1" ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="semester">Cuatrimestre</label>
                    </div>
                    <select name="semester" class="custom-select" id="semester" required>
                        <option value="" selected><?= Lang::$trans['select'] . "..." ?></option>
                        <option value="1,2,10">Trimestre 1</option>
                        <option value="3,4,12">Trimestre 2</option>
                        <option value="5,6,03">Trimestre 3</option>
                        <option value="7,8,05">Trimestre 4</option>
                    </select>
                </div>
            </div>
            <input class="btn btn-primary mx-auto d-block" type="submit" value="<?= Lang::$trans['continue'] ?>">
        </form>

    </div>

</body>
<?php
Route::includeFile('/includes/layouts/scripts.php', true);
?>

</html>