<?php
require_once '../../../app.php';

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
    $title = "Informe de notas";
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-3">Informe de notas</h1>
        <div class="bg-white shadow-lg p-3 rounded">
            <div class="form-row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="label-form" for="class">Cursos</label>
                        <select name="class" id="class" class="form-control">
                            <option value="">Todos</option>
                            <option value="home">Salon Hogar</option>
                            <?php foreach ($teacher->classes() as $class) : ?>
                                <option value="<?= $class->curso ?>"><?= "$class->curso - $class->desc1" ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="label-form" for="type">Tipo</label>
                        <select name="type" id="type" class="form-control">
                            <option value="nota">Nota</option>
                            <option value="credito">Credito</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="label-form" for="not">Notas</label>
                        <select name="not" id="not" class="form-control">
                            <option value="A">Trimestre 1 y 3</option>
                            <option value="B">Semestre 1 y 2</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">               
                <div class="col-12 text-center mt-3">
                    <button class="btn btn-primary">Imprimir</button>
                    <a href="<?= Route::url('/regiweb/options/') ?>" class="btn btn-secondary">Atrás</a>
                </div>
            </div>
        </div>   
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>