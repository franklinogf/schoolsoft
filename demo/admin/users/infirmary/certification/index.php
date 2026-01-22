<?php

require_once __DIR__ . '/../../../../app.php';

use App\Models\Admin;
use App\Models\Classes;
use App\Models\InfirmaryCertification;
use App\Models\School;
use App\Models\Student;
use Classes\Route;
use Classes\Session;

Session::is_logged();

// Get current school year
$school = Admin::primaryAdmin();
$currentYear = $school->year ?? '';

// Get all courses/grades
$courses = Classes::query()
    ->orderBy('curso')
    ->get()
    ->unique('curso');

// Check if searching
$selectedCourse = $_GET['curso'] ?? '';
$selectedSS = $_GET['ss'] ?? '';
$students = [];
$student = null;
$certification = null;

if (!empty($selectedCourse)) {
    // Get students for selected course
    $students = Student::byClass( $selectedCourse)        
        ->get();
}

if (!empty($selectedSS) && !empty($selectedCourse)) {
    // Get selected student
    $student = Student::query()->byClass($selectedCourse)
        ->where('ss', $selectedSS)
        ->first();

    if ($student) {
        // Get existing certification
        $certification = InfirmaryCertification::findByCourseAndYear($selectedSS, $selectedCourse, $currentYear);
    }
}

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Exención a Vacunar por Razones Médicas o Religiosas");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body class="pb-5">
    <?php Route::includeFile('/admin/includes/layouts/menu.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4"><?= __("Departamento de Enfermería") ?></h1>
        <h4 class="text-center mb-4"><?= __("Exención a Vacunar por Razones Médicas o Religiosas") ?></h4>

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
                    <?php if ($certification): ?>
                        <a href="<?= Route::url('/admin/users/infirmary/certification/pdf/report.php?id=' . $certification->id) ?>" 
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

            <!-- Certification Form -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><?= __("Certificación de Exención") ?></h5>
                </div>
                <div class="card-body">
                    <form action="<?= Route::url('/admin/users/infirmary/certification/includes/save.php') ?>" method="POST">
                        <input type="hidden" name="ss" value="<?= $student->ss ?>">
                        <input type="hidden" name="curso" value="<?= $selectedCourse ?>">
                        <input type="hidden" name="year" value="<?= $currentYear ?>">
                        <?php if ($certification): ?>
                            <input type="hidden" name="id" value="<?= $certification->id ?>">
                        <?php endif; ?>

                        <!-- Medical Exemption Certification -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><strong><?= __("Certificación de Excención Médica") ?></strong></h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="cert1"><strong><?= __("Razón y posible duración de la condición") ?>:</strong></label>
                                    <input type="text" class="form-control" id="cert1" name="cert1" 
                                           value="<?= $certification->cert1 ?? '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="cert2"><strong><?= __("Vacuna(s) eximida(s)") ?>:</strong></label>
                                    <input type="text" class="form-control" id="cert2" name="cert2" 
                                           value="<?= $certification->cert2 ?? '' ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Religious Exemption Declaration -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><strong><?= __("Declaración Jurada por Exención creencias religiosas") ?></strong></h6>
                            </div>
                            <div class="card-body">
                                <p style="line-height: 2.5; text-align: justify;">
                                    <?= __("Yo") ?>, 
                                    <input type="text" style="display: inline-block; width: 180px; padding: 2px 5px; font-size: 0.9rem;" name="dec1" placeholder="<?= __("Nombre del ministro") ?>" value="<?= $certification->dec1 ?? '' ?>"> 
                                    <?= __("ministro(a) de la religión (o secta)") ?> 
                                    <input type="text" style="display: inline-block; width: 160px; padding: 2px 5px; font-size: 0.9rem;" name="dec2" placeholder="<?= __("Nombre de la religión") ?>" value="<?= $certification->dec2 ?? '' ?>">, 
                                    <?= __("mayor de edad, de estatus civil") ?> 
                                    <input type="text" style="display: inline-block; width: 120px; padding: 2px 5px; font-size: 0.9rem;" name="dec3" placeholder="<?= __("Estado civil") ?>" value="<?= $certification->dec3 ?? '' ?>"> 
                                    <?= __("y vecino(a) de") ?> 
                                    <input type="text" style="display: inline-block; width: 250px; padding: 2px 5px; font-size: 0.9rem;" name="dec4" placeholder="<?= __("Dirección") ?>" value="<?= $certification->dec4 ?? '' ?>">, 
                                    <?= __("y Yo") ?>, 
                                    <input type="text" style="display: inline-block; width: 200px; padding: 2px 5px; font-size: 0.9rem;" name="dec5" placeholder="<?= __("Nombre del padre/madre/tutor") ?>" value="<?= $certification->dec5 ?? '' ?>">, 
                                    <?= __("padre, madre o tutor legal de") ?> <strong><?= $student->fullName ?></strong>, <?= __("mayor de edad, de estatus civil") ?> 
                                    <input type="text" style="display: inline-block; width: 120px; padding: 2px 5px; font-size: 0.9rem;" name="dec6" placeholder="<?= __("Estado civil") ?>" value="<?= $certification->dec6 ?? '' ?>">, 
                                    <?= __("de ocupación") ?> 
                                    <input type="text" style="display: inline-block; width: 150px; padding: 2px 5px; font-size: 0.9rem;" name="dec7" placeholder="<?= __("Ocupación") ?>" value="<?= $certification->dec7 ?? '' ?>"> 
                                    <?= __("y vecino(a) de") ?> 
                                    <input type="text" style="display: inline-block; width: 250px; padding: 2px 5px; font-size: 0.9rem;" name="dec8" placeholder="<?= __("Dirección") ?>" value="<?= $certification->dec8 ?? '' ?>">, 
                                    <?= __("certificamos y damos fe de lo anteriormente declarado") ?>.
                                </p>
                            </div>
                        </div>

                        <!-- Sworn Statement -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><strong><?= __("Testimonio") ?></strong></h6>
                            </div>
                            <div class="card-body">
                                <p style="line-height: 2.5; text-align: justify;">
                                    <?= __("Jurado y suscrito ante mi por") ?> 
                                    <input type="text" style="display: inline-block; width: 200px; padding: 2px 5px; font-size: 0.9rem;" name="tes1" placeholder="<?= __("Primer firmante") ?>" value="<?= $certification->tes1 ?? '' ?>"> 
                                    <?= __("y") ?> 
                                    <input type="text" style="display: inline-block; width: 200px; padding: 2px 5px; font-size: 0.9rem;" name="tes2" placeholder="<?= __("Segundo firmante") ?>" value="<?= $certification->tes2 ?? '' ?>">, 
                                    <?= __("de las circunstancias anteriormente a quienes doy fe de conocer personalmente o haber identificado mediante") ?> 
                                    <input type="text" style="display: inline-block; width: 180px; padding: 2px 5px; font-size: 0.9rem;" name="tes3" placeholder="<?= __("Tipo de identificación") ?>" value="<?= $certification->tes3 ?? '' ?>">. 
                                    <?= __("En") ?> 
                                    <input type="text" style="display: inline-block; width: 150px; padding: 2px 5px; font-size: 0.9rem;" name="tes4" placeholder="<?= __("Ciudad") ?>" value="<?= $certification->tes4 ?? '' ?>">, 
                                    <?= __("Puerto Rico a") ?> 
                                    <input type="number" min="1" max="31" style="display: inline-block; width: 60px; padding: 2px 5px; font-size: 0.9rem;" name="tes5" placeholder="<?= __("Día") ?>" value="<?= $certification->tes5 ?? '' ?>"> 
                                    <?= __("de") ?> 
                                    <input type="text" style="display: inline-block; width: 120px; padding: 2px 5px; font-size: 0.9rem;" name="tes6" placeholder="<?= __("Mes") ?>" value="<?= $certification->tes6 ?? '' ?>"> 
                                    <?= __("de") ?> 
                                    <input type="text" style="display: inline-block; width: 80px; padding: 2px 5px; font-size: 0.9rem;" name="tes7" placeholder="<?= __("Año") ?>" value="<?= $certification->tes7 ?? '' ?>">.
                                </p>
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
