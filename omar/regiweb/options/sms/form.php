<?php
require_once '../../../app.php';

use Classes\Controllers\Student;
use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Teacher;

Session::is_logged();
Route::includeFile('/simple-php-captcha/simple-php-captcha.php', true);
$_SESSION['captcha'] = simple_php_captcha();
$teacher = new Teacher(Session::id());
if (isset($_POST['student'])) {
    $_ss = $_POST['student'];
    $student = new Student($_ss);
    $_titleValue = $student->fullName();
} else if (isset($_POST['grade'])) {
    $_grade = $_POST['grade'];
    $_titleValue =  __LANG === 'es' ? "todos los estudiantes del grado $_grade" : "all the students of the grade $_grade";
} else {
    $_phoneNumber = $_POST['phoneNumber'];
    $_phoneCompany = $_POST['phoneCompany'];
    $_titleValue =  __LANG === 'es' ? "un numero especifico" : "a specific number";
}
// $admins = DB::table('colegio')->select('usuario')->get();
$savedMessages = DB::table('T_sms_guardados')
    ->where('enviado_por', $teacher->id)
    ->orderBy('fecha,hora')->get();
/* ------------------------------- Transaltion ------------------------------ */
$TRANS = [
    "es" => [
        "PAGE_TITLE" => "Enviar mensaje de texto a $_titleValue",
        "OPTION1" => 'Titulo',
        "OPTION3" => 'Mensaje',
        "OPTION4" => 'Archivos',
    ],
    "en" => [
        "PAGE_TITLE" => "Send mensaje de texto to $_titleValue",
        "OPTION1" => 'Titulo',
        "OPTION3" => 'Message',
        "OPTION4" => 'Files',
    ]
];

Lang::addTranslation($TRANS);

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = Lang::translation('PAGE_TITLE');
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 px-0">
        <h1 class="text-center mb-3 mt-5"><?= Lang::translation('PAGE_TITLE') ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form action="<?= Route::url('/regiweb/options/sms/includes/form.php') ?>" method="post" enctype="multipart/form-data">
                <?php if (isset($_POST['student'])) : ?>
                    <input type="hidden" name="student" value="<?= $_POST['student'] ?>">
                <?php elseif (isset($_POST['grade'])) : ?>
                    <input type="hidden" name="grade" value="<?= $_POST['grade'] ?>">
                <?php else : ?>
                    <input type="hidden" name="phoneNumber" value="<?= $_POST['phoneNumber'] ?>">
                    <input type="hidden" name="phoneCompany" value="<?= $_POST['phoneCompany'] ?>">
                <?php endif ?>
                <div class="input-group mb-3 option">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="title"><?= Lang::translation('OPTION1') ?></label>
                    </div>
                    <input class="form-control" type="text" name="title" id="title" maxlength="10" required>
                </div>
                <div class="input-group mb-3 option">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="message"><?= Lang::translation('OPTION3') ?></label>
                    </div>
                    <textarea class="form-control" name="message" id="message" required> </textarea>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="saveMessage" name="saveMessage">
                    <label class="custom-control-label" for="saveMessage">Guardar este mensaje</label>
                    <small class="text-info">(Se Guarda sin archivos)</small>
                </div>
                <div class="row my-3">
                    <div class="col-6 text-right">
                        <img src="<?= $_SESSION['captcha']['image_src'] ?>" alt="captcha">
                        <input type="hidden" id="cap" value="<?= $_SESSION['captcha']['code'] ?>" autocomplete="no">
                    </div>
                    <div class="col-6 col-md-3 d-flex">
                        <input class="form-control align-self-center" id="code" required type="text">
                    </div>
                </div>
                <div class="text-center">
                    <a href="index.php" class="btn btn-secondary">Atrás</a>
                    <input class="btn btn-primary" type="submit" value="<?= Lang::$trans['continue'] ?>">
                </div>
        </div>
        </form>

        <?php if (sizeof($savedMessages) > 0) : ?>
            <div class="mt-5">
                <h4 class="text-center mb-4">Mensajes guardados</h4>
                <div id="savedMessages" class="row row-cols-2 row-cols-md-3">
                    <?php foreach ($savedMessages as $message) : ?>
                        <div class="col mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title title text-truncate" title="<?= $message->titulo ?>"><?= $message->titulo ?></h5>
                                    <p class="card-text message text-muted text-truncate"><?= $message->mensaje ?></p>
                                </div>

                                <div class="card-body">
                                    <button class="btn btn-primary">Usar</button>
                                    <button data-id="<?= $message->id ?>" class="btn btn-danger delete">Borrar</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endif ?>

    </div>




    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>