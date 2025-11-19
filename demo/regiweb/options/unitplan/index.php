<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Admin;
use App\Models\Teacher;
use App\Models\UnitPlan;
use Classes\Route;
use Classes\Session;

Session::is_logged();

$teacher = Teacher::find(Session::id());
$school = Admin::primaryAdmin();
$year = $school->year;

if (!$teacher) {
    die('Error: Maestro no encontrado');
}

// Get teacher's courses
$courses = $teacher->subjects;

// Get all unit plans for this teacher
$unitPlans = $teacher->unitPlans()->orderBy('fecha', 'desc')->get();

// Get selected plan ID from URL if exists
$selectedPlanId = $_GET['plan'] ?? null;
$unitPlan = null;

if ($selectedPlanId) {
    $unitPlan = UnitPlan::find($selectedPlanId);
    if (!$unitPlan || $unitPlan->id_profesor != $teacher->id) {
        $unitPlan = null;
    }
}
?>

<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Plan de Unidad");
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
    <style>
        .form-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .section-title {
            background-color: #6c757d;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .checkbox-group label {
            margin-right: 15px;
            font-weight: normal;
        }

        .day-header {
            background-color: #e9ecef;
            font-weight: bold;
            text-align: center;
        }

        .nivel-checkbox,
        .acomodo-checkbox {
            margin-bottom: 5px;
        }

        textarea {
            min-height: 80px;
        }
    </style>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>

    <div class="container-fluid mt-3 mb-5 px-3">
        <h1 class="text-center mb-4"><?= __("Plan de Unidad") ?></h1>

        <!-- Controles superiores -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary btn-block" id="newPlanBtn">
                            <i class="fa fa-plus"></i> <?= __("Nuevo") ?>
                        </button>
                    </div>
                    <div class="col-md-7">
                        <label><?= __("Seleccionar Plan") ?>:</label>
                        <select class="form-control" id="planSelector">
                            <option value=""><?= __("Seleccione un plan") ?></option>
                            <?php foreach ($unitPlans as $plan): ?>
                                <option value="<?= $plan->id ?>" <?= $selectedPlanId == $plan->id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($plan->titulo) ?> - <?= htmlspecialchars($plan->materia) ?> - <?= $plan->fecha ?> (ID: <?= $plan->id ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-info" id="searchPlanBtn">
                            <i class="fa fa-search"></i> <?= __("Buscar") ?>
                        </button>
                        <button type="button" class="btn btn-danger" id="deletePlanBtn" <?= !$unitPlan ? 'style="display:none;"' : '' ?>>
                            <i class="fa fa-trash"></i> <?= __("Borrar") ?>
                        </button>
                        <button type="button" class="btn btn-secondary" id="printPlanBtn" <?= !$unitPlan ? 'style="display:none;"' : '' ?>>
                            <i class="fa fa-print"></i> <?= __("Imprimir") ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario del Plan -->
        <form id="unitPlanForm" style="<?= !$unitPlan && !isset($_GET['new']) ? 'display:none;' : '' ?>">
            <input type="hidden" name="id" id="planId" value="<?= $unitPlan->id ?? '' ?>">
            <input type="hidden" name="isNew" id="isNew" value="<?= !$unitPlan ? '1' : '0' ?>">

            <!-- Sección 1: Información del Maestro y Materia -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <strong><?= __("Información General") ?></strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label><?= __("Maestro(a)") ?>:</label>
                            <input type="text" class="form-control" readonly
                                value="<?= htmlspecialchars($teacher->nombre . ' ' . $teacher->apellidos) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label><?= __("Materia") ?>:</label>
                            <select class="form-control" name="materia" required>
                                <option value="">-- <?= __("Seleccione") ?> --</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?= $course->curso ?>"
                                        <?= ($unitPlan && $unitPlan->materia == $course->curso) ? 'selected' : '' ?>>
                                        <?= "$course->curso - $course->desc1" ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label><?= __("Título") ?>:</label>
                            <input type="text" class="form-control" name="titulo" required
                                value="<?= htmlspecialchars($unitPlan->titulo ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label><?= __("Fecha") ?>:</label>
                            <input type="date" class="form-control" name="fecha"
                                value="<?= $unitPlan->fecha ?? '' ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label><?= __("Duración (semanas)") ?>:</label>
                            <input type="number" class="form-control" name="duracion" min="1"
                                value="<?= $unitPlan->duracion ?? '' ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 2: Tema Transversal -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <strong><?= __("Tema Transversal") ?></strong>
                </div>
                <div class="card-body">
                    <div class="checkbox-group">
                        <?php
                        $transversales = [
                            'Educar para el amor al prójimo',
                            'Educar para la transcendencia',
                            'Educación para la promoción de la vida',
                            'Educar para el liderazgo moral',
                            'Educación para la ciudadanía consciente y activa',
                            'Educar para la comunión',
                            'Educar para la conservación del medio ambiente'
                        ];
                        foreach ($transversales as $i => $label):
                            $index = $i + 1;
                            $checked = ($unitPlan && $unitPlan->{"transversal{$index}"} === 'si') ? 'checked' : '';
                        ?>
                            <label class="d-block">
                                <input type="checkbox" name="transversal<?= $index ?>" <?= $checked ?>>
                                <?= $label ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Sección 3: Integración -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <strong><?= __("Integración") ?></strong>
                </div>
                <div class="card-body">
                    <div class="checkbox-group">
                        <?php
                        $integraciones = [
                            'Español',
                            'Inglés',
                            'Estudios Sociales',
                            'Ciencia',
                            'Matemáticas',
                            'Bellas Artes',
                            'Educación Física',
                            'Salud Escolar',
                            'Tecnología'
                        ];
                        foreach ($integraciones as $i => $label):
                            $index = $i + 1;
                            $checked = ($unitPlan && $unitPlan->{"integracion{$index}"} === 'si') ? 'checked' : '';
                        ?>
                            <label class="d-inline-block mr-3">
                                <input type="checkbox" name="integracion<?= $index ?>" <?= $checked ?>>
                                <?= $label ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Sección 4: Estándares y Meta -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <strong><?= __("Estándares y Meta") ?></strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label><?= __("Estándar a)") ?>:</label>
                            <input type="text" class="form-control" name="estandar1"
                                value="<?= htmlspecialchars($unitPlan->estandar1 ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label><?= __("Estándar b)") ?>:</label>
                            <input type="text" class="form-control" name="estandar2"
                                value="<?= htmlspecialchars($unitPlan->estandar2 ?? '') ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label><?= __("Meta") ?>:</label>
                        <input type="text" class="form-control" name="meta"
                            value="<?= htmlspecialchars($unitPlan->meta ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- Etapa 1: Resultados Esperados -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <strong><?= __("ETAPA 1 - Resultados Esperados") ?></strong>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="font-weight-bold"><?= __("Resumen de la unidad") ?>:</label>
                        <textarea class="form-control" name="resumen" rows="4"><?= htmlspecialchars($unitPlan->resumen ?? '') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold"><?= __("Preguntas Esenciales") ?></h6>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <div class="mb-2">
                                    <label>PE<?= $i ?>:</label>
                                    <input type="text" class="form-control" name="pe<?= $i ?>"
                                        value="<?= htmlspecialchars($unitPlan->{"pe{$i}"} ?? '') ?>">
                                </div>
                            <?php endfor; ?>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold"><?= __("Entendimiento Duradero") ?></h6>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <div class="mb-2">
                                    <label>ED<?= $i ?>:</label>
                                    <input type="text" class="form-control" name="ed<?= $i ?>"
                                        value="<?= htmlspecialchars($unitPlan->{"ed{$i}"} ?? '') ?>">
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="font-weight-bold"><?= __("Objetivos de Transferencia (General)") ?>:</label>
                        <input type="text" class="form-control" name="objetivo_general"
                            value="<?= htmlspecialchars($unitPlan->objetivo_general ?? '') ?>">
                    </div>

                    <div class="mt-3">
                        <label class="font-weight-bold"><?= __("Objetivos de Adquisición") ?>:</label>
                        <small class="text-muted d-block mb-2"><?= __("Al finalizar esta unidad, el estudiante:") ?></small>
                        <textarea class="form-control" name="objetivo_adquisicion" rows="5"><?= htmlspecialchars($unitPlan->objetivo_adquisicion ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Etapa 2: Evidencia para evaluar aprendizaje -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <strong><?= __("ETAPA 2 - Evidencia para Evaluar Aprendizaje") ?></strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold"><?= __("Tareas de Desempeño Auténtico") ?>:</label>
                            <textarea class="form-control" name="tareas" rows="8"><?= htmlspecialchars($unitPlan->tareas ?? '') ?></textarea>
                            <label class="mt-2"><?= __("Observaciones") ?>:</label>
                            <textarea class="form-control" name="tareas_observaciones" rows="4"><?= htmlspecialchars($unitPlan->tareas_observaciones ?? '') ?></textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold"><?= __("Otra Evidencia") ?>:</label>
                            <textarea class="form-control" name="otra" rows="8"><?= htmlspecialchars($unitPlan->otra ?? '') ?></textarea>
                            <label class="mt-2"><?= __("Observaciones") ?>:</label>
                            <textarea class="form-control" name="otra_observaciones" rows="4"><?= htmlspecialchars($unitPlan->otra_observaciones ?? '') ?></textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold"><?= __("Actividades") ?>:</label>
                            <textarea class="form-control" name="actividades" rows="8"><?= htmlspecialchars($unitPlan->actividades ?? '') ?></textarea>
                            <label class="mt-2"><?= __("Observaciones") ?>:</label>
                            <textarea class="form-control" name="actividades_observaciones" rows="4"><?= htmlspecialchars($unitPlan->actividades_observaciones ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Etapa 3: Plan de Aprendizaje -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <strong><?= __("ETAPA 3 - Plan de Aprendizaje") ?></strong>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label><?= __("Expectativa o indicador") ?>:</label>
                            <input type="text" class="form-control" name="expectativa"
                                value="<?= htmlspecialchars($unitPlan->expectativa ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label><?= __("Estrategia general") ?>:</label>
                            <input type="text" class="form-control" name="estrategia"
                                value="<?= htmlspecialchars($unitPlan->estrategia ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label><?= __("Objetivos") ?>:</label>
                            <input type="text" class="form-control" name="objetivos"
                                value="<?= htmlspecialchars($unitPlan->objetivos ?? '') ?>">
                        </div>
                    </div>

                    <!-- Días de la semana -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th width="120"><?= __("Día / Fecha") ?></th>
                                    <?php
                                    $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
                                    for ($i = 1; $i <= 5; $i++):
                                    ?>
                                        <th class="text-center">
                                            <?= $dias[$i - 1] ?><br>
                                            <input type="date" class="form-control form-control-sm mt-1"
                                                name="fecha<?= $i ?>" value="<?= $unitPlan->{"fecha{$i}"} ?? '' ?>">
                                        </th>
                                    <?php endfor; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Nivel de Profundidad -->
                                <tr>
                                    <td class="font-weight-bold"><?= __("Nivel de Profundidad") ?></td>
                                    <?php
                                    $niveles = ['Memorístico', 'Procesamiento', 'Estratégico', 'Extendido'];
                                    for ($day = 1; $day <= 5; $day++):
                                    ?>
                                        <td>
                                            <?php foreach ($niveles as $j => $nivel):
                                                $levelIndex = $j + 1;
                                                $checked = ($unitPlan && $unitPlan->{"nivel{$day}_{$levelIndex}"} === 'si') ? 'checked' : '';
                                            ?>
                                                <label class="d-block nivel-checkbox">
                                                    <input type="checkbox" name="nivel<?= $day ?>_<?= $levelIndex ?>" <?= $checked ?>>
                                                    <?= $nivel ?>
                                                </label>
                                            <?php endforeach; ?>
                                        </td>
                                    <?php endfor; ?>
                                </tr>
                                <!-- Inicio -->
                                <tr>
                                    <td class="font-weight-bold"><?= __("Inicio") ?></td>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <td>
                                            <textarea class="form-control" name="inicio<?= $i ?>" rows="3"><?= htmlspecialchars($unitPlan->{"inicio{$i}"} ?? '') ?></textarea>
                                        </td>
                                    <?php endfor; ?>
                                </tr>
                                <!-- Desarrollo -->
                                <tr>
                                    <td class="font-weight-bold"><?= __("Desarrollo") ?></td>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <td>
                                            <textarea class="form-control" name="desarrollo<?= $i ?>" rows="3"><?= htmlspecialchars($unitPlan->{"desarrollo{$i}"} ?? '') ?></textarea>
                                        </td>
                                    <?php endfor; ?>
                                </tr>
                                <!-- Cierre -->
                                <tr>
                                    <td class="font-weight-bold"><?= __("Cierre") ?></td>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <td>
                                            <textarea class="form-control" name="cierre<?= $i ?>" rows="3"><?= htmlspecialchars($unitPlan->{"cierre{$i}"} ?? '') ?></textarea>
                                        </td>
                                    <?php endfor; ?>
                                </tr>
                                <!-- Acomodo Razonable -->
                                <tr>
                                    <td class="font-weight-bold"><?= __("Acomodo Razonable") ?></td>
                                    <?php
                                    $acomodos = [
                                        'Ubicación adecuada del pupitre',
                                        'Tiempo adicional',
                                        'Ayuda individualizada',
                                        'Tareas y exámenes mas cortos',
                                        'Refuerzo positivo',
                                        'Otro'
                                    ];
                                    for ($day = 1; $day <= 5; $day++):
                                    ?>
                                        <td>
                                            <?php foreach ($acomodos as $j => $acomodo):
                                                $acomodoIndex = $j + 1;
                                                $checked = ($unitPlan && $unitPlan->{"acomodo{$day}_{$acomodoIndex}"} === 'si') ? 'checked' : '';
                                            ?>
                                                <label class="d-block acomodo-checkbox">
                                                    <input type="checkbox" name="acomodo<?= $day ?>_<?= $acomodoIndex ?>" <?= $checked ?>>
                                                    <?= $acomodo ?>
                                                </label>
                                                <?php if ($acomodoIndex === 6): ?>
                                                    <input type="text" class="form-control form-control-sm"
                                                        name="otro<?= $day ?>"
                                                        placeholder="<?= __("Especificar") ?>"
                                                        value="<?= htmlspecialchars($unitPlan->{"otro{$day}"} ?? '') ?>">
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </td>
                                    <?php endfor; ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="text-center">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fa fa-save"></i> <?= __("Guardar") ?>
                        </button>
                        <a href="<?= Route::url('/regiweb/options/index.php') ?>" class="btn btn-secondary btn-lg">
                            <i class="fa fa-arrow-left"></i> <?= __("Atrás") ?>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::sweetAlert();
    ?>

    <script>
        $(document).ready(function() {
            const planSelector = $('#planSelector');
            const searchPlanBtn = $('#searchPlanBtn');
            const newPlanBtn = $('#newPlanBtn');
            const deletePlanBtn = $('#deletePlanBtn');
            const printPlanBtn = $('#printPlanBtn');
            const unitPlanForm = $('#unitPlanForm');
            const planIdInput = $('#planId');
            const isNewInput = $('#isNew');

            // Nuevo plan
            newPlanBtn.click(function() {
                window.location.href = '<?= Route::url('/regiweb/options/unitplan/index.php?new=1') ?>';
            });

            // Buscar plan
            searchPlanBtn.click(function() {
                const planId = planSelector.val();
                if (planId) {
                    window.location.href = '<?= Route::url('/regiweb/options/unitplan/index.php') ?>?plan=' + planId;
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: '<?= __("Advertencia") ?>',
                        text: '<?= __("Debe seleccionar un plan") ?>'
                    });
                }
            });

            // Eliminar plan
            deletePlanBtn.click(function() {
                const planId = planIdInput.val();
                if (!planId) return;

                Swal.fire({
                    title: '<?= __("¿Está seguro?") ?>',
                    text: '<?= __("Esta acción no se puede deshacer") ?>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '<?= __("Sí, eliminar") ?>',
                    cancelButtonText: '<?= __("Cancelar") ?>'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '<?= Route::url('/regiweb/options/unitplan/includes/index.php') ?>',
                            type: 'POST',
                            data: {
                                deleteUnitPlan: planId
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '<?= __("Éxito") ?>',
                                        text: response.message
                                    }).then(() => {
                                        window.location.href = '<?= Route::url('/regiweb/options/unitplan/index.php') ?>';
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message || '<?= __("Error al eliminar") ?>'
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: '<?= __("Error de conexión") ?>'
                                });
                            }
                        });
                    }
                });
            });

            // Imprimir plan
            printPlanBtn.click(function() {
                const planId = planIdInput.val();
                if (planId) {
                    window.open('<?= Route::url('/regiweb/options/unitplan/pdf.php') ?>?id=' + planId, '_blank');
                }
            });

            // Guardar/Crear plan
            unitPlanForm.submit(function(e) {
                e.preventDefault();
                const formData = $(this).serializeArray();
                const isNew = isNewInput.val() === '1';
                const data = {};

                formData.forEach(item => {
                    data[item.name] = item.value;
                });

                if (isNew) {
                    data.createUnitPlan = true;
                } else {
                    data.updateUnitPlan = true;
                    data.unitPlanId = planIdInput.val();
                }

                $.ajax({
                    url: '<?= Route::url('/regiweb/options/unitplan/includes/index.php') ?>',
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '<?= __("Éxito") ?>',
                                text: response.message
                            }).then(() => {
                                if (isNew && response.unitPlanId) {
                                    window.location.href = '<?= Route::url('/regiweb/options/unitplan/index.php') ?>?plan=' + response.unitPlanId;
                                } else {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.error || '<?= __("Error al guardar") ?>'
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = '<?= __("Error de conexión") ?>';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>