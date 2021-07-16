<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Teacher;

Session::is_logged();
$teacher = new Teacher(Session::id());
$admins = DB::table('colegio')->select('usuario')->get();

/* ------------------------------- Transaltion ------------------------------ */
$TRANS = [
    "es" => [
        "PAGE_TITLE" => 'Enviar correo electronico',
        "OPTION1" => 'Estudiantes',
        "OPTION2" => 'Cursos',
        "OPTION3" => 'Administradores',
    ],
    "en" => [
        "PAGE_TITLE" => 'Send E-mail',
        "OPTION1" => 'Students',
        "OPTION2" => 'Classes',
        "OPTION3" => 'Admins',
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
        <div class="jumbotron bg-secondary shadow-sm py-3">
            <div class="row row-cols-1 row-cols-md-3">
                <div class="col mb-3 mb-md-0">
                    <button data-id="students" class="btn btn-outline-light btn-block btn-lg options"><?= Lang::translation('OPTION1') ?></button>
                </div>
                <div class="col mb-3 mb-md-0">
                    <button data-id="grades" class="btn btn-outline-light btn-block btn-lg options"><?= Lang::translation('OPTION2') ?></button>
                </div>
                <div class="col">
                    <button data-id="admins" class="btn btn-outline-light btn-block btn-lg options"><?= Lang::translation('OPTION3') ?></button>
                </div>
            </div>
        </div>
        <?php if (Session::get('emailSent')) : ?>
            <div class="alert alert-info" role="alert">
                <?= Session::get('emailSent', true) ?>
            </div>
        <?php endif ?>
        <div id="value" class="container bg-white shadow-lg py-3 rounded hidden">
            <form action="<?= Route::url('/regiweb/options/email/form.php') ?>" method="post">
                <div class="mx-auto" style="width: 20rem;">

                    <div id='students' class="input-group mb-3 option hidden">
                        <div class="input-group-prepend">
                            <label class="input-group-text"><?= Lang::translation('OPTION1') ?></label>
                        </div>
                        <select name="student" class="custom-select" required>
                            <option value="" selected><?= Lang::$trans['select'] . "..." ?></option>
                            <?php foreach ($teacher->homeStudents() as $student) : ?>
                                <option value="<?= $student->ss ?>"><?= "$student->apellidos, $student->nombre" ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div id='grades' class="input-group mb-3 option hidden">
                        <div class="input-group-prepend">
                            <label class="input-group-text"><?= Lang::translation('OPTION2') ?></label>
                        </div>
                        <select name="grade" class="custom-select" required>
                            <option value="" selected><?= Lang::$trans['select'] . "..." ?></option>
                            <?php foreach ($teacher->classes() as $class) : ?>
                                <option value="<?= $class->curso ?>"><?= "$class->curso - $class->desc1" ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div id='admins' class="input-group mb-3 option hidden">
                        <div class="input-group-prepend">
                            <label class="input-group-text"><?= Lang::translation('OPTION3') ?></label>
                        </div>
                        <select name="admin" class="custom-select" required>
                            <option value="" selected><?= Lang::$trans['select'] . "..." ?></option>
                            <?php foreach ($admins as $admin) : ?>
                                <option value="<?= $admin->usuario ?>"><?= "$admin->usuario" ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                </div>

                <input class="btn btn-primary mx-auto d-block" type="submit" value="<?= Lang::$trans['continue'] ?>">
        </div>
        </form>

    </div>




    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>