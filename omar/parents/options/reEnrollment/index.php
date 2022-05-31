<?php
require_once '../../../app.php';

use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\Parents;

Session::is_logged();
$parents = new Parents(Session::id());

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = "Re-Matricula";
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-4">Formulario de Re-Matricula</h1>
        <small class="text-muted">Seleccionar los estudiantes marcando la casilla</small>
        <form method="post" id="reEnrollmentForm">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3">
                <?php foreach ($parents->kids() as $kid) : ?>
                    <div class="col mb-3">
                        <div class="card <?= $kid->rema === 'Si' ? "border-info" : '' ?>" >
                            <div class="card-body">
                                <div class="form-check">
                                    <input <?= $kid->rema === 'Si' ? "checked" : '' ?> class="form-check-input position-static float-right studentCheckbox" name="student[<?=$kid->mt?>]" type="checkbox" value="Si">
                                </div>
                                <img src="<?= Util::studentProfilePicture($kid) ?>" class="rounded-circle img-thumbnail d-block mx-auto mb-3" alt="Profile Picture" width="150" height="150">
                                <h6 class="card-title"><?= "$kid->nombre $kid->apellidos" ?></h6>
                                <p class="card-text">Grado: <?= $kid->grado ?></p>
                                <p class="card-text">Fecha de nacimiento: <?= $kid->fecha ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
                
            </div>
            <button class="btn btn-primary d-block mx-auto mt-2" type="submit" id="reEnrollmentBtn">Re-Matricular estudiantes seleccionados</button>
        </form>

    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>