<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Student;
use App\Models\Teacher;
use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;

Session::is_logged();
Route::includeFile('/simple-php-captcha/simple-php-captcha.php', true);
Session::set("captcha", simple_php_captcha());

$teacher = Teacher::find(Session::id());
if (isset($_POST['students'])) {
    $studentsAmount = sizeof($_POST['students']);
    if ($studentsAmount > 1) {
        $_titleValue = __LANG === 'es' ? "$studentsAmount estudiantes" : "$studentsAmount students";
    } else {
        $_ss = $_POST['students'][0];
        $student = Student::bySs($_ss);
        $_titleValue = $student->fullName();
    }
    Session::set('students', $_POST['students']);
} else if (isset($_POST['classes'])) {
    $classesAmount = sizeof($_POST['classes']);
    if ($classesAmount > 1) {
        $_titleValue =  __LANG === 'es' ? "todos los estudiantes de $classesAmount grados" : "all the students of $classesAmount classes";
    } else {
        $_grade = $_POST['classes'][0];
        $_titleValue =  __LANG === 'es' ? "todos los estudiantes del grado $_grade" : "all the students of the grade $_grade";
    }
    Session::set('classes', $_POST['classes']);
} else {
    $_user = $_POST['admin'];
    $_titleValue =  __LANG === 'es' ? "al administrador $_user" : "to the admin $_user";
}
$savedMessages = DB::table('T_correos_guardados')->where([
    ['colegio', $teacher->usuario],
    ['id_profesor', $teacher->id]
])->orderBy('id DESC')->get();

/* ------------------------------- Transaltion ------------------------------ */
$lang = new Lang([
    ["Enviar correo a $_titleValue", "Send E-mail to $_titleValue"],
    ['Titulo', 'Title'],
    ['Asunto', 'Subject'],
    ['Mensaje', 'Message'],
    ['Archivos', 'Files'],
    ["Agregar archivo", "Add file"],
    ["Guardar este mensaje", "Save this message"],
    ["(Se Guarda sin archivos)", "(Will be save without files)"],
    ["Notificar por SMS", "Notify by SMS"],
    ["Atrás", "Back"],
    ["Mensajes guardados", "Saved messages"],
    ['Usar', 'Use'],
    ['Borrar', 'Delete'],

]);

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Enviar correo a $_titleValue");
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation("Enviar correo a $_titleValue") ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form action="<?= Route::url('/regiweb/options/email/includes/form.php') ?>" method="post" enctype="multipart/form-data">
                <?php if (isset($_POST['students'])) : ?>
                    <input type="hidden" name="studentsAmount" value="<?= $studentsAmount ?>" />
                <?php elseif (isset($_POST['classes'])) : ?>
                    <input type="hidden" name="classesAmount" value="<?= $classesAmount ?>" />
                <?php else : ?>
                    <input type="hidden" name="admin" value="<?= $_POST['admin'] ?>">
                <?php endif ?>
                <div class="input-group mb-3 option">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="title"><?= $lang->translation("Titulo") ?></label>
                    </div>
                    <input class="form-control" type="text" name="title" id="title" required>
                </div>
                <div class="input-group mb-3 option">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="subject"><?= $lang->translation("Asunto") ?></label>
                    </div>
                    <input class="form-control" type="text" name="subject" id="subject" required>
                </div>
                <div class="input-group mb-3 option">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="message"><?= $lang->translation("Mensaje") ?></label>
                    </div>
                    <textarea class="form-control" name="message" id="message" required> </textarea>
                </div>
                <div class="form-group">
                    <label for="addFile"><?= $lang->translation("Archivos") ?></label>
                    <button class="btn btn-secondary d-block mx-auto addFile" id="addFile"><?= $lang->translation("Agregar archivo") ?></button>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="saveMessage" name="saveMessage">
                    <label class="custom-control-label" for="saveMessage"><?= $lang->translation("Guardar este mensaje") ?></label>
                    <small class="text-info"><?= $lang->translation("(Se Guarda sin archivos)") ?></small>
                </div>
                <?php if (!isset($_POST['admin'])) : ?>
                    <div class="custom-control custom-checkbox ">
                        <input type="checkbox" class="custom-control-input" id="sms" name="sms">
                        <label class="custom-control-label" for="sms"><?= $lang->translation("Notificar por SMS") ?></label>
                    </div>
                <?php endif ?>
                <div class="row my-3">
                    <div class="col-6 text-right">
                        <img src="<?= $_SESSION['captcha']['image_src'] ?>" alt="captcha" style="width:120px;">
                        <input type="hidden" id="cap" value="<?= $_SESSION['captcha']['code'] ?>">
                    </div>
                    <div class="col-6 col-md-3 d-flex">
                        <input class="form-control form-control-sm align-self-center" id="code" type="text">
                    </div>
                </div>
                <div class="text-center">
                    <a href="index.php" class="btn btn-secondary"><?= $lang->translation("Atrás") ?></a>
                    <input class="btn btn-primary" type="submit" value="<?= $lang->translation("Continuar"); ?>">
                </div>
        </div>
        </form>

        <?php if (sizeof($savedMessages) > 0) : ?>
            <div class="mt-5">
                <h4 class="text-center mb-4"><?= $lang->translation("Mensajes guardados") ?></h4>
                <div id="savedMessages" class="row row-cols-2 row-cols-md-3">
                    <?php foreach ($savedMessages as $message) : ?>
                        <div class="col mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title title text-truncate" title="<?= $message->titulo ?>"><?= $message->titulo ?></h5>
                                    <p class="card-text subject text-truncate"><?= $message->asunto ?></p>
                                    <p class="card-text message text-muted text-truncate"><?= $message->mensaje ?></p>
                                </div>

                                <div class="card-body">
                                    <button class="btn btn-primary"><?= $lang->translation("Usar") ?></button>
                                    <button data-id="<?= $message->id ?>" class="btn btn-danger delete"><?= $lang->translation("Borrar") ?></button>
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