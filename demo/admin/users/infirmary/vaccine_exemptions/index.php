<?php

require_once __DIR__ . '/../../../../app.php';

use App\Models\Admin;
use App\Models\School;
use App\Models\Student;
use App\Models\VaccineExemption;
use Classes\Route;
use Classes\Session;

Session::is_logged();

// Get all active students for the current year
$students = Student::orderBy('apellidos')
    ->orderBy('nombre')
    ->get();

// Get current school year
$school = Admin::primaryAdmin();
$currentYear = $school->year ?? '';

// Check if a student is selected
$selectedSS = $_GET['ss'] ?? '';
$student = null;
$exemptions = [];

if (!empty($selectedSS)) {
    $student = Student::where('ss', $selectedSS)->first();

    if ($student) {
        // Get existing exemptions
        $existingExemptions = VaccineExemption::where('ss', $selectedSS)
            ->where('year', $currentYear)
            ->get()
            ->keyBy('vacuna');

        $exemptions = $existingExemptions;
    }
}

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Excenciones de Vacunas");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body class="pb-5">
    <?php Route::includeFile('/admin/includes/layouts/menu.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4"><?= __("Departamento de Enfermería") ?></h1>
        <h4 class="text-center mb-4"><?= __("Excenciones de Vacunas") ?></h4>

        <?php if ($success = Session::get('success', true)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $success ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if ($error = Session::get('error', true)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $error ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Student Search -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><?= __("Buscar Estudiante") ?></h5>
            </div>
            <div class="card-body">
                <form action="" method="GET">
                    <div class="form-group">
                        <label for="estudiante_ss"><strong><?= __("Seleccione un Estudiante") ?>:</strong></label>
                        <select name="ss" id="estudiante_ss" class="form-control" required>
                            <option value=""><?= __("-- Seleccione --") ?></option>
                            <?php foreach ($students as $s): ?>
                                <option value="<?= $s->ss ?>" <?= $selectedSS == $s->ss ? 'selected' : '' ?>>
                                    <?= $s->apellidos . ', ' . $s->nombre . ' - ' . __("Grado") . ': ' . $s->grado . ' (ID: ' . $s->id . ')' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> <?= __("Buscar") ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($student): ?>
            <!-- Student Info -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><?= __("Información del Estudiante") ?></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong><?= __("Nombre") ?>:</strong> <?= $student->fullName ?></p>
                        </div>
                        <div class="col-md-3">
                            <p><strong><?= __("Grado") ?>:</strong> <?= $student->grado ?></p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>SS:</strong> <?= $student->ss ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exemptions Form -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><?= __("Registrar Excenciones de Vacunas") ?></h5>
                </div>
                <div class="card-body">
                    <form action="<?= Route::url('/admin/users/infirmary/vaccine_exemptions/includes/save.php') ?>" method="POST">
                        <input type="hidden" name="ss" value="<?= $student->ss ?>">

                        <div class="row">
                            <!-- Vacunas P-VAC-3 -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><strong><?= __("Vacunas P-VAC-3") ?></strong></h6>
                                    </div>
                                    <div class="card-body">
                                        <?php $pvac3 = $exemptions['Vacunas P-VAC-3'] ?? null; ?>
                                        
                                        <div class="form-group">
                                            <label for="excencion_pvac3"><strong><?= __("Excención") ?>:</strong></label>
                                            <select class="form-control" id="excencion_pvac3" name="excencion_<?= md5('Vacunas P-VAC-3') ?>">
                                                <option value=""><?= __("-- Seleccione --") ?></option>
                                                <option value="Religiosa" <?= ($pvac3 && $pvac3->excencion == 'Religiosa') ? 'selected' : '' ?>>
                                                    <?= __("Religiosa") ?>
                                                </option>
                                                <option value="Médica" <?= ($pvac3 && $pvac3->excencion == 'Médica') ? 'selected' : '' ?>>
                                                    <?= __("Médica") ?>
                                                </option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="fecha_entrega_pvac3"><strong><?= __("Fecha de Entrega") ?>:</strong></label>
                                            <input type="date" class="form-control" id="fecha_entrega_pvac3" 
                                                name="fecha_entrega_<?= md5('Vacunas P-VAC-3') ?>"
                                                value="<?= $pvac3 && $pvac3->fechaEntrega ? $pvac3->fechaEntrega->format('Y-m-d') : '' ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="fecha_expiracion_pvac3"><strong><?= __("Fecha de Expiración") ?>:</strong></label>
                                            <input type="date" class="form-control" id="fecha_expiracion_pvac3" 
                                                name="fecha_expiracion_<?= md5('Vacunas P-VAC-3') ?>"
                                                value="<?= $pvac3 && $pvac3->fechaExpiracion ? $pvac3->fechaExpiracion->format('Y-m-d') : '' ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Vacunas COVID-19 -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><strong><?= __("Vacunas COVID-19") ?></strong></h6>
                                    </div>
                                    <div class="card-body">
                                        <?php $covid = $exemptions['Vacunas COVID-19'] ?? null; ?>
                                        
                                        <div class="form-group">
                                            <label for="excencion_covid"><strong><?= __("Excención") ?>:</strong></label>
                                            <select class="form-control" id="excencion_covid" name="excencion_<?= md5('Vacunas COVID-19') ?>">
                                                <option value=""><?= __("-- Seleccione --") ?></option>
                                                <option value="Religiosa" <?= ($covid && $covid->excencion == 'Religiosa') ? 'selected' : '' ?>>
                                                    <?= __("Religiosa") ?>
                                                </option>
                                                <option value="Médica" <?= ($covid && $covid->excencion == 'Médica') ? 'selected' : '' ?>>
                                                    <?= __("Médica") ?>
                                                </option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="fecha_entrega_covid"><strong><?= __("Fecha de Entrega") ?>:</strong></label>
                                            <input type="date" class="form-control" id="fecha_entrega_covid" 
                                                name="fecha_entrega_<?= md5('Vacunas COVID-19') ?>"
                                                value="<?= $covid && $covid->fechaEntrega ? $covid->fechaEntrega->format('Y-m-d') : '' ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="fecha_expiracion_covid"><strong><?= __("Fecha de Expiración") ?>:</strong></label>
                                            <input type="date" class="form-control" id="fecha_expiracion_covid" 
                                                name="fecha_expiracion_<?= md5('Vacunas COVID-19') ?>"
                                                value="<?= $covid && $covid->fechaExpiracion ? $covid->fechaExpiracion->format('Y-m-d') : '' ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" name="save" class="btn btn-success btn-lg">
                                <i class="fas fa-save"></i> <?= __("Guardar") ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="mt-4 text-center">
            <a href="<?= Route::url('/admin/users/infirmary/index.php') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> <?= __("Regresar al Menú de Enfermería") ?>
            </a>
        </div>
    </div>

    <?php Route::includeFile('/includes/layouts/scripts.php', true); ?>
</body>

</html>
