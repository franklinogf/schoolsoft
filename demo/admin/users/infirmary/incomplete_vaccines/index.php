<?php

require_once __DIR__ . '/../../../../app.php';

use App\Models\Admin;
use App\Models\Classes;
use App\Models\IncompleteVaccine;
use App\Models\Student;
use Classes\Route;
use Classes\Session;

Session::is_logged();

// Get current school year
$school = Admin::primaryAdmin();
$currentYear = $school->year ?? '';

// Get all courses/grades
$courses = Classes::where('year', $currentYear)
    ->orderBy('curso')
    ->get()
    ->unique('curso');

// Check if searching
$selectedCourse = $_GET['curso'] ?? '';
$selectedSS = $_GET['ss'] ?? '';
$students = [];
$student = null;
$record = null;

if (!empty($selectedCourse)) {
    // Get students for selected course
    $students = Student::byClass($selectedCourse)->get();
}

if (!empty($selectedSS) && !empty($selectedCourse)) {
    // Get selected student
    $student = Student::query()->byClass($selectedCourse)
        ->where('ss', $selectedSS)
        ->first();

    if ($student) {
        // Get existing record
        $record = IncompleteVaccine::findByCourseAndYear($selectedSS, $selectedCourse, $currentYear);
    }
}

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Notificación de Vacunas Incompletas");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body class="pb-5">
    <?php Route::includeFile('/admin/includes/layouts/menu.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4"><?= __("Departamento de Enfermería") ?></h1>
        <h4 class="text-center mb-4"><?= __("Notificación de Vacunas Incompletas") ?></h4>

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

        <!-- Course Selection -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><?= __("Seleccionar Curso") ?></h5>
            </div>
            <div class="card-body">
                <form action="" method="GET">
                    <div class="form-group">
                        <label for="curso"><strong><?= __("Curso") ?>:</strong></label>
                        <select name="curso" id="curso" class="form-control" required>
                            <option value=""><?= __("-- Seleccione --") ?></option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?= $course->curso ?>" <?= $selectedCourse == $course->curso ? 'selected' : '' ?>>
                                    <?= $course->curso ?> - <?= $course->descripcion ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> <?= __("Buscar curso") ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if (!empty($selectedCourse) && count($students) > 0): ?>
            <!-- Student Selection -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><?= __("Seleccionar Estudiante") ?></h5>
                </div>
                <div class="card-body">
                    <form action="" method="GET">
                        <input type="hidden" name="curso" value="<?= $selectedCourse ?>">
                        <div class="form-group">
                            <label for="estudiante_ss"><strong><?= __("Estudiante") ?>:</strong></label>
                            <select name="ss" id="estudiante_ss" class="form-control" required>
                                <option value=""><?= __("-- Seleccione --") ?></option>
                                <?php foreach ($students as $s): ?>
                                    <option value="<?= $s->ss ?>" <?= $selectedSS == $s->ss ? 'selected' : '' ?>>
                                        <?= $s->apellidos . ', ' . $s->nombre ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> <?= __("Buscar estudiante") ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($student): ?>
            <!-- Student Info -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><?= __("Información del Estudiante") ?></h5>
                    <?php if ($record): ?>
                        <a href="<?= Route::url('/admin/users/infirmary/incomplete_vaccines/pdf/report.php?id=' . $record->id) ?>"
                            target="_blank" class="btn btn-light btn-sm">
                            <i class="fas fa-print"></i> <?= __("Imprimir") ?>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong><?= __("Nombre") ?>:</strong> <?= $student->fullName ?></p>
                        </div>
                        <div class="col-md-3">
                            <p><strong><?= __("Curso") ?>:</strong> <?= $student->curso ?></p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>SS:</strong> <?= $student->ss ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Incomplete Vaccines Form -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><?= __("División de Vacunación") ?></h5>
                </div>
                <div class="card-body">
                    <form action="<?= Route::url('/admin/users/infirmary/incomplete_vaccines/includes/save.php') ?>" method="POST">
                        <input type="hidden" name="ss" value="<?= $student->ss ?>">
                        <input type="hidden" name="curso" value="<?= $selectedCourse ?>">
                        <input type="hidden" name="year" value="<?= $currentYear ?>">
                        <?php if ($record): ?>
                            <input type="hidden" name="id" value="<?= $record->id ?>">
                        <?php endif; ?>

                        <h6 class="mb-3"><strong><?= __("Le falta(n) la(s) siguiente(s) vacuna(s)") ?>:</strong></h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="vacuna1" name="vacuna1" value="x"
                                        <?= ($record && $record->vacuna1 == 'x') ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="vacuna1">DTaP</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="vacuna2" name="vacuna2" value="x"
                                        <?= ($record && $record->vacuna2 == 'x') ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="vacuna2">Td</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="vacuna3" name="vacuna3" value="x"
                                        <?= ($record && $record->vacuna3 == 'x') ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="vacuna3">Polio</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="vacuna4" name="vacuna4" value="x"
                                        <?= ($record && $record->vacuna4 == 'x') ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="vacuna4">Tdap</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="vacuna5" name="vacuna5" value="x"
                                        <?= ($record && $record->vacuna5 == 'x') ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="vacuna5">HIB</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="vacuna6" name="vacuna6" value="x"
                                        <?= ($record && $record->vacuna6 == 'x') ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="vacuna6">MCV</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="vacuna7" name="vacuna7" value="x"
                                        <?= ($record && $record->vacuna7 == 'x') ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="vacuna7">MMR</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="vacuna8" name="vacuna8" value="x"
                                        <?= ($record && $record->vacuna8 == 'x') ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="vacuna8">HPV*</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="vacuna9" name="vacuna9" value="x"
                                        <?= ($record && $record->vacuna9 == 'x') ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="vacuna9">Hep. B</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="vacuna10" name="vacuna10" value="x"
                                        <?= ($record && $record->vacuna10 == 'x') ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="vacuna10">Hep. A*</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="vacuna11" name="vacuna11" value="x"
                                        <?= ($record && $record->vacuna11 == 'x') ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="vacuna11">PCV</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="vacuna12" name="vacuna12" value="x"
                                        <?= ($record && $record->vacuna12 == 'x') ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="vacuna12">Rota*</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="vacuna13" name="vacuna13" value="x"
                                        <?= ($record && $record->vacuna13 == 'x') ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="vacuna13">VAR</label>
                                </div>
                            </div>
                        </div>

                        <p class="text-muted mt-3 mb-4"><small>*<?= __("Estas son vacunas recomendadas") ?>.</small></p>

                        <hr>

                        <!-- Certification Section -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="mr-3"><strong><?= __("Certificación médica") ?>:</strong></label>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" id="cert1_si" name="cert1" value="si"
                                        <?= ($record && $record->cert1 == 'si') ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="cert1_si"><?= __("Sí") ?></label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" id="cert1_no" name="cert1" value="no"
                                        <?= ($record && $record->cert1 == 'no') ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="cert1_no"><?= __("No") ?></label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="cert2" name="cert2" value="x"
                                        <?= ($record && $record->cert2 == 'x') ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="cert2"><?= __("Exención religiosa") ?></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="cert3" name="cert3" value="x"
                                        <?= ($record && $record->cert3 == 'x') ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="cert3"><?= __("Plan de vacunación") ?></label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="pvac" name="pvac" value="x"
                                        <?= ($record && $record->pvac == 'x') ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="pvac"><?= __("Le falta la evidencia de vacunación (certificado verde de vacunación. PVAC-3)") ?></label>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Comments -->
                        <div class="form-group">
                            <label for="comentario"><strong><?= __("Comentario") ?>:</strong></label>
                            <textarea class="form-control" id="comentario" name="comentario" rows="5"><?= $record->comentario ?? '' ?></textarea>
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