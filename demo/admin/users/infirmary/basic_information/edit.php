<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\Student;
use Classes\Route;
use Classes\Session;
use Carbon\Carbon;

Session::is_logged();

// Get student SS from query parameter
$ss = $_GET['ss'] ?? '';

if (empty($ss)) {
    Session::set('error', __("No se ha especificado un estudiante"));
    Route::redirect('/users/infirmary/basic_information/index.php');
}

// Load student with family and infirmary data
$student = Student::where('ss', $ss)
    ->with(['family', 'infirmary'])
    ->firstOrFail();

// Get infirmary record (may be null for new records)
$infirmary = $student->infirmary;

// Parse family history if exists
$familyHistory = $infirmary ? $infirmary->getFamilyHistoryArray() : [];

// Calculate age
$age = null;
if ($student->fecha) {
    $birthDate = Carbon::parse($student->fecha);
    $age = $birthDate->age;
}

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Información de Enfermería");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
    <style>
        .checkbox-group label {
            margin-right: 15px;
            font-weight: normal;
        }
        .health-section {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .table-family-history {
            font-size: 0.9em;
        }
        .table-family-history td, .table-family-history th {
            text-align: center;
            vertical-align: middle;
        }
    </style>
</head>

<body class="pb-5">
    <?php Route::includeFile('/admin/includes/layouts/menu.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4"><?= __("Departamento de Enfermería") ?></h1>
        <h4 class="text-center mb-4"><?= __("Información Básica de Salud") ?></h4>

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

        <!-- Student Information Card -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><?= __("Información del Estudiante") ?></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong><?= __("Nombre del Estudiante") ?>:</strong> <?= $student->fullName ?></p>
                        <p><strong><?= __("Fecha de Nacimiento") ?>:</strong> <?= $student->fecha ? $student->fecha->format('Y-m-d') : '' ?></p>
                        <p><strong><?= __("Edad") ?>:</strong> <?= $age ?? '' ?></p>
                        <p><strong><?= __("Género") ?>:</strong> <?= $student->genero ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong><?= __("Lugar de Nacimiento") ?>:</strong> <?= $student->lugar_nac ?></p>
                        <p><strong><?= __("Grado") ?>:</strong> <?= $student->grado ?></p>
                        <p><strong><?= __("Madre") ?>:</strong> <?= $student->family->madre ?? '' ?></p>
                        <p><strong><?= __("Padre") ?>:</strong> <?= $student->family->padre ?? '' ?></p>
                    </div>
                </div>
                <div class="text-right">
                    <a href="<?= Route::url('/admin/users/infirmary/basic_information/pdf/report.php?ss=' . urlencode($ss)) ?>" 
                       class="btn btn-danger" target="_blank">
                        <i class="fas fa-file-pdf"></i> <?= __("Generar PDF") ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Health Form -->
        <form method="POST" action="<?= Route::url('/admin/users/infirmary/basic_information/includes/save.php') ?>">
            <input type="hidden" name="ss" value="<?= $ss ?>">
            <input type="hidden" name="id" value="<?= $student->id ?>">

            <!-- Vaccination Section -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><?= __("Datos de Salud") ?></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><strong><?= __("Vacunas al día") ?>:</strong></h6>
                            <div class="checkbox-group mb-3">
                                <label><input type="checkbox" name="vacdia" value="Si" <?= ($infirmary->va_dia ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Sí") ?></label>
                                <label><input type="checkbox" name="vacdia" value="No" <?= ($infirmary->va_dia ?? '') == 'No' ? 'checked' : '' ?>> <?= __("No") ?></label>
                            </div>

                            <h6><strong><?= __("Vacunas Recibidas") ?>:</strong></h6>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="dtp" value="Si" <?= ($infirmary->vac1 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label">DTP/aP/DT</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="polio" value="Si" <?= ($infirmary->vac2 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label">Polio</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="mmr" value="Si" <?= ($infirmary->vac3 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label">MMR</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="hib" value="Si" <?= ($infirmary->vac4 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label">HIB</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="ppd" value="Si" <?= ($infirmary->vac5 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label">PPD</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="varicela" value="Si" <?= ($infirmary->vac6 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label">Varicella</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="vac7" value="Si" <?= ($infirmary->vac7 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label">HPV</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="vac8" value="Si" <?= ($infirmary->vac8 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label">HepA</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="vac9" value="Si" <?= ($infirmary->vac9 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label">HepB</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="vac10" value="Si" <?= ($infirmary->vac10 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label">Influenza</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="vac11" value="Si" <?= ($infirmary->vac11 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label">Measles</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="vac12" value="Si" <?= ($infirmary->vac12 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label">Meningo</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="vac13" value="Si" <?= ($infirmary->vac13 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label">Meningo B</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="vac14" value="Si" <?= ($infirmary->vac14 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label">Mumps</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="vac15" value="Si" <?= ($infirmary->vac15 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label">Pneumococcal</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="vac16" value="Si" <?= ($infirmary->vac16 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label">Rota</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="vac17" value="Si" <?= ($infirmary->vac17 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label">Rubella</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="vac18" value="Si" <?= ($infirmary->vac18 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label">Td/Tdap</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6><strong><?= __("Refuerzos") ?>:</strong></h6>
                            <div class="checkbox-group mb-3">
                                <label><input type="checkbox" name="refuerzos" value="Si" <?= ($infirmary->refuerzos ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Sí") ?></label>
                                <label><input type="checkbox" name="refuerzos" value="No" <?= ($infirmary->refuerzos ?? '') == 'No' ? 'checked' : '' ?>> <?= __("No") ?></label>
                            </div>

                            <div class="form-group">
                                <label><strong><?= __("Peso") ?>:</strong></label>
                                <input type="number" name="peso" class="form-control" 
                                       value="<?= $infirmary->peso ?? '' ?>" 
                                       min="0" max="999" step="0.1">
                                       <small><?= __("en libras") ?></small>
                            </div>

                            <div class="form-group">
                                <label><strong><?= __("Estatura") ?>:</strong></label>
                                <input type="text" name="estatura" class="form-control" 
                                       value="<?= $infirmary->estatura ?? '' ?>" 
                                       maxlength="4">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Health Conditions Section -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><?= __("Condiciones de Salud") ?></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong><?= __("Condiciones de Salud del Estudiante") ?>:</strong></label>
                                <textarea name="condicion" class="form-control" rows="3" maxlength="230"><?= $infirmary->cond_salud ?? '' ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong><?= __("Medicamento de uso diario") ?>:</strong></label>
                                <textarea name="medicamento" class="form-control" rows="3" maxlength="230"><?= $infirmary->med_usodi ?? '' ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong><?= __("Dosis") ?>:</strong></label>
                                <input type="text" name="dosis" class="form-control" 
                                       value="<?= $infirmary->dosis ?? '' ?>" maxlength="50">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong><?= __("Frecuencia") ?>:</strong></label>
                                <input type="text" name="frecuencia" class="form-control" 
                                       value="<?= $infirmary->frec ?? '' ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Behavior Section -->
            <div class="card mb-4">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><?= __("Comportamiento") ?></h5>
                </div>
                <div class="card-body">
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="com1" value="Si" <?= ($infirmary->com1 ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Cooperador") ?></label>
                        <label><input type="checkbox" name="com2" value="Si" <?= ($infirmary->com2 ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("No Cooperador") ?></label>
                        <label><input type="checkbox" name="com3" value="Si" <?= ($infirmary->com3 ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Agresivo") ?></label>
                        <label><input type="checkbox" name="com4" value="Si" <?= ($infirmary->com4 ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Otros") ?></label>
                    </div>
                </div>
            </div>

            <!-- Physical Exam Section -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><?= __("Apariencia General") ?></h5>
                </div>
                <div class="card-body">
                    <div class="health-section">
                        <h6><strong><?= __("Piel") ?>:</strong></h6>
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="piel1" value="Si" <?= ($infirmary->piel1 ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Seca") ?></label>
                            <label><input type="checkbox" name="piel2" value="Si" <?= ($infirmary->piel2 ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Rosada") ?></label>
                            <label><input type="checkbox" name="piel3" value="Si" <?= ($infirmary->piel3 ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Pálida") ?></label>
                            <label><input type="checkbox" name="piel4" value="Si" <?= ($infirmary->piel4 ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Sudorosa") ?></label>
                            <label><input type="checkbox" name="piel5" value="Si" <?= ($infirmary->piel5 ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Eritemas") ?></label>
                        </div>
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="cicatrices" value="Si" <?= ($infirmary->cicatrices ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Cicatrices") ?></label>
                            <label><input type="checkbox" name="quemaduras" value="Si" <?= ($infirmary->quemaduras ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Quemaduras") ?></label>
                        </div>
                    </div>

                    <div class="health-section">
                        <h6><strong><?= __("Cabeza") ?>:</strong></h6>
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="vision" value="Si" <?= ($infirmary->vision ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Pérdida de visión") ?></label>
                            <label><input type="checkbox" name="audicion" value="Si" <?= ($infirmary->audicion ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Dificultad auditiva") ?></label>
                            <label><input type="checkbox" name="congestion" value="Si" <?= ($infirmary->nasal ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Congestión nasal") ?></label>
                        </div>
                        <div class="form-group mt-2">
                            <strong><?= __("Espejuelos") ?>:</strong>
                            <label class="ml-3"><input type="checkbox" name="espejuelos" value="Si" <?= ($infirmary->espejuelos ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Sí") ?></label>
                            <label><input type="checkbox" name="espejuelos" value="No" <?= ($infirmary->espejuelos ?? '') == 'No' ? 'checked' : '' ?>> <?= __("No") ?></label>
                        </div>
                        <div class="form-group">
                            <strong><?= __("Dentadura Postiza") ?>:</strong>
                            <label class="ml-3"><input type="checkbox" name="dentadura" value="Si" <?= ($infirmary->dentadura ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Sí") ?></label>
                            <label><input type="checkbox" name="dentadura" value="No" <?= ($infirmary->dentadura ?? '') == 'No' ? 'checked' : '' ?>> <?= __("No") ?></label>
                        </div>
                    </div>

                    <div class="health-section">
                        <h6><strong><?= __("Pecho") ?>:</strong></h6>
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="respiracion" value="Si" <?= ($infirmary->respiracion ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Dificultad respiratoria") ?></label>
                            <label><input type="checkbox" name="asma" value="Si" <?= ($infirmary->asma ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Asma") ?></label>
                            <label><input type="checkbox" name="condritis" value="Si" <?= ($infirmary->condritis ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Costo condritis") ?></label>
                            <label><input type="checkbox" name="espasmo" value="Si" <?= ($infirmary->espasmos ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Espasmos") ?></label>
                        </div>
                    </div>

                    <div class="health-section">
                        <h6><strong><?= __("Abdomen") ?>:</strong></h6>
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="edema" value="Si" <?= ($infirmary->ab_edema ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Edema") ?></label>
                            <label><input type="checkbox" name="herida" value="Si" <?= ($infirmary->ab_herida ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Herida") ?></label>
                            <label><input type="checkbox" name="deformidad" value="Si" <?= ($infirmary->ab_deformidad ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Deformidad") ?></label>
                        </div>
                        <div class="form-group mt-2">
                            <label><?= __("Describa") ?>:</label>
                            <textarea name="descrip1" class="form-control" rows="2" maxlength="60"><?= $infirmary->desc1 ?? '' ?></textarea>
                        </div>
                    </div>

                    <div class="health-section">
                        <h6><strong><?= __("Extremidades") ?>:</strong></h6>
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="exedema" value="Si" <?= ($infirmary->ex_edema ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Edema") ?></label>
                            <label><input type="checkbox" name="exherida" value="Si" <?= ($infirmary->ex_herida ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Heridas") ?></label>
                            <label><input type="checkbox" name="exdeform" value="Si" <?= ($infirmary->ex_deformidad ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Deformación") ?></label>
                            <label><input type="checkbox" name="exprotesis" value="Si" <?= ($infirmary->ex_protesis ?? '') == 'Si' ? 'checked' : '' ?>> <?= __("Prótesis") ?></label>
                        </div>
                        <div class="form-group mt-2">
                            <label><?= __("Describa") ?>:</label>
                            <textarea name="descrip2" class="form-control" rows="2" maxlength="60"><?= $infirmary->desc2 ?? '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Family History Section -->
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><?= __("Historial de Enfermedad Familiar") ?></h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-family-history">
                            <thead class="thead-dark">
                                <tr>
                                    <th><?= __("Hist Familiar") ?></th>
                                    <th><?= __("Estudiante") ?></th>
                                    <th><?= __("Padre") ?></th>
                                    <th><?= __("Madre") ?></th>
                                    <th><?= __("Hermano") ?></th>
                                    <th><?= __("Otro Fam.") ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $diseases = [
                                    'heart_disease' => __('Enf del corazón'),
                                    'cancer' => __('Cáncer'),
                                    'diabetes' => __('Diabetes'),
                                    'hypertension' => __('Hipertensión'),
                                    'hypotension' => __('Hipotensión'),
                                    'hyperthyroidism' => __('Hipertiroidismo'),
                                    'hypothyroidism' => __('Hipotiroidismo'),
                                    'anemia' => __('Anemia'),
                                    'other' => __('Otros')
                                ];
                                $checkboxNum = 1;
                                foreach ($diseases as $diseaseKey => $diseaseName):
                                    $history = $familyHistory[$diseaseKey] ?? ['student' => '', 'father' => '', 'mother' => '', 'sibling' => '', 'other' => ''];
                                ?>
                                <tr>
                                    <td><?= $diseaseName ?></td>
                                    <td><input type="checkbox" name="Checkbox<?= $checkboxNum ?>" value="Si" <?= $history['student'] == 'Si' ? 'checked' : '' ?>></td>
                                    <td><input type="checkbox" name="Checkbox<?= $checkboxNum + 1 ?>" value="Si" <?= $history['father'] == 'Si' ? 'checked' : '' ?>></td>
                                    <td><input type="checkbox" name="Checkbox<?= $checkboxNum + 2 ?>" value="Si" <?= $history['mother'] == 'Si' ? 'checked' : '' ?>></td>
                                    <td><input type="checkbox" name="Checkbox<?= $checkboxNum + 3 ?>" value="Si" <?= $history['sibling'] == 'Si' ? 'checked' : '' ?>></td>
                                    <td><input type="checkbox" name="Checkbox<?= $checkboxNum + 4 ?>" value="Si" <?= $history['other'] == 'Si' ? 'checked' : '' ?>></td>
                                </tr>
                                <?php 
                                    $checkboxNum += 5;
                                endforeach; 
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="text-center mb-5">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-save"></i> <?= __("Guardar") ?>
                </button>
                <a href="<?= Route::url('/admin/users/infirmary/basic_information/index.php') ?>" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times"></i> <?= __("Cancelar") ?>
                </a>
            </div>
        </form>
    </div>

    <?php Route::includeFile('/includes/layouts/scripts.php', true); ?>

    <script>
        // Checkbox toggle behavior (single selection per group)
        document.addEventListener('DOMContentLoaded', function() {
            // For specific checkbox groups (vacdia, refuerzos, espejuelos, dentadura)
            const checkboxGroups = ['vacdia', 'refuerzos', 'espejuelos', 'dentadura'];
            
            checkboxGroups.forEach(function(groupName) {
                const checkboxes = document.querySelectorAll('input[name="' + groupName + '"]');
                checkboxes.forEach(function(checkbox) {
                    checkbox.addEventListener('click', function() {
                        if (this.checked) {
                            checkboxes.forEach(function(cb) {
                                if (cb !== checkbox) {
                                    cb.checked = false;
                                }
                            });
                        }
                    });
                });
            });
        });
    </script>
</body>

</html>
