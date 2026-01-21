<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\Student;
use Classes\Route;
use Classes\Session;

Session::is_logged();

// Get all active students for the current year
$students = Student::with(['family', 'infirmary'])
    ->orderBy('apellidos')
    ->orderBy('nombre')
    ->get();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Información Básica de Enfermería");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body class="pb-5">
    <?php Route::includeFile('/admin/includes/layouts/menu.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4"><?= __("Departamento de Enfermería") ?></h1>
        <h4 class="text-center mb-4"><?= __("Información Básica de Salud") ?></h4>

        <?php if ($success = Session::get('success',true)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $success ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if ($error = Session::get('error',true)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $error ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><?= __("Buscar Estudiante") ?></h5>
            </div>
            <div class="card-body">
                <form action="<?=  Route::url('/admin/users/infirmary/basic_information/edit.php') ?>" method="GET">
                    <div class="form-group">
                        <label for="estudiante_ss"><strong><?= __("Seleccione un Estudiante") ?>:</strong></label>
                        <select name="ss" id="estudiante_ss" class="form-control" required>
                            <option value=""><?= __("-- Seleccione --") ?></option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?= $student->ss ?>">
                                    <?= $student->apellidos . ', ' . $student->nombre . ' - ' . __("Grado") . ': ' . $student->grado . ' (SS: ' . $student->ss . ')' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-search"></i> <?= __("Buscar") ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-4 text-center">
            <a href="<?= Route::url('/admin/users/infirmary/index.php') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> <?= __("Regresar al Menú de Enfermería") ?>
            </a>
        </div>
    </div>

    <?php Route::includeFile('/includes/layouts/scripts.php', true); ?>
</body>

</html>
