<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Admin;
use App\Models\EnglishLessonPlan;
use App\Models\Teacher;
use Classes\Route;
use Classes\Session;
use Illuminate\Database\Capsule\Manager as DB;

Session::is_logged();

$teacher = Teacher::find(Session::id());
$school = Admin::primaryAdmin();
$year = $school->year;

// Get teacher's courses
$cursos = $teacher->subjects;

// Get teacher's lesson plans
$planes = $teacher->englishLessonPlans()
    ->orderBy('id', 'desc')
    ->get();

$selectedPlanId = $_GET['plan'] ?? null;
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Plan de Lección de Inglés");
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
    <style>
        .form-check-label {
            cursor: pointer;
            user-select: none;
        }

        .card-header h5 {
            margin-bottom: 0;
        }

        .modification-item {
            margin-bottom: 0.5rem;
        }
    </style>
</head>

<body>
    <?php Route::includeFile('/regiweb/includes/layouts/menu.php'); ?>

    <div class="container-fluid mt-4">
        <h2 class="text-center mb-4"><?= __("Plan de Lección de Inglés") ?></h2>

        <!-- Controles superiores -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary btn-block" id="btnNew">
                            <i class="fas fa-plus"></i> <?= __("Nuevo Plan") ?>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <label><?= __("Seleccionar Plan") ?>:</label>
                        <select class="form-control" id="selectPlan">
                            <option value=""><?= __("Seleccione un plan") ?></option>
                            <?php foreach ($planes as $plan): ?>
                                <option value="<?= $plan->id ?>" <?= $selectedPlanId == $plan->id ? 'selected' : '' ?>>
                                    <?= $plan->getDisplayTitle() ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-info btn-block" id="btnSearch">
                            <i class="fas fa-search"></i> <?= __("Buscar") ?>
                        </button>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-block" id="btnDelete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="col-md-1">
                        <a href="#" class="btn btn-secondary btn-block d-none" id="btnPrintTop" target="_blank">
                            <i class="fas fa-print"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <form id="lessonPlanForm" class="d-none">
            <input type="hidden" id="planId" name="planId">

            <!-- Información Básica -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5><?= __("ENGLISH LESSON PLAN") ?></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong><?= __("TEACHER") ?>:</strong></label>
                                <input type="text" class="form-control" name="maestro" readonly
                                    value="<?= $teacher->fullName ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong><?= __("CLASS") ?>:</strong></label>
                                <select class="form-control" name="materia" required>
                                    <option value=""><?= __("Seleccione") ?></option>
                                    <?php foreach ($cursos as $row): ?>
                                        <option value="<?= $row->curso ?>">
                                            <?= "$row->curso - $row->desc1" ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label><?= __("TITLE") ?>:</label>
                                <input type="text" class="form-control" name="titulo" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?= __("DATE") ?>:</label>
                                <input type="date" class="form-control" name="fecha" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><?= __("DURATION OF LESSON") ?> (weeks):</label>
                                <input type="number" class="form-control" name="duracion" min="1">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transversal Themes & Integration -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <h5><?= __("TRANSVERSAL THEMES & INTEGRATION") ?></h5>
                </div>
                <div class="card-body">
                    <h6><strong><?= __("TRANSVERSAL THEMES") ?>:</strong></h6>
                    <div class="row">
                        <?php
                        $themes = [
                            'transversal1' => 'Educate to love each other',
                            'transversal2' => 'Educate for citizenship',
                            'transversal3' => 'Educate for healthy communion',
                            'transversal4' => 'Educate for conservation of Environment',
                            'transversal5' => 'Educate for Promotion of Life',
                            'transversal6' => 'Educate for Transcendence',
                            'transversal7' => 'Educate for Ethical Leadership'
                        ];
                        foreach ($themes as $key => $label):
                        ?>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="<?= $key ?>" value="si" id="<?= $key ?>">
                                    <label class="form-check-label" for="<?= $key ?>"><?= $label ?></label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <hr>
                    <h6 class="mt-3"><strong><?= __("INTEGRATION") ?>:</strong></h6>
                    <div class="row">
                        <?php
                        $integrations = [
                            'integracion1' => 'Spanish',
                            'integracion2' => 'History',
                            'integracion3' => 'Science',
                            'integracion4' => 'Math',
                            'integracion5' => 'Art',
                            'integracion6' => 'Physical Education',
                            'integracion7' => 'Health',
                            'integracion8' => 'Technology',
                            'integracion9' => 'Others'
                        ];
                        foreach ($integrations as $key => $label):
                        ?>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="<?= $key ?>" value="si" id="<?= $key ?>">
                                    <label class="form-check-label" for="<?= $key ?>"><?= $label ?></label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Stage 1 - Expected Results -->
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h5><?= __("STAGE 1 - EXPECTED RESULTS") ?></h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label><strong><?= __("Summary/Overview") ?>:</strong></label>
                        <textarea class="form-control" name="resumen" rows="4"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6><strong><?= __("ESSENTIAL QUESTIONS") ?>:</strong></h6>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <div class="form-group">
                                    <label>PE<?= $i ?>:</label>
                                    <input type="text" class="form-control" name="pe<?= $i ?>">
                                </div>
                            <?php endfor; ?>
                        </div>
                        <div class="col-md-6">
                            <h6><strong><?= __("ESSENTIAL UNDERSTANDING") ?>:</strong></h6>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <div class="form-group">
                                    <label>ED<?= $i ?>:</label>
                                    <input type="text" class="form-control" name="ed<?= $i ?>">
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><strong><?= __("GENERAL OBJECTIVES") ?>:</strong></label>
                        <textarea class="form-control" name="objetivo_general" rows="5"></textarea>
                    </div>
                </div>
            </div>

            <!-- Stage 2 - Evidence to Evaluate Learning -->
            <div class="card mb-3">
                <div class="card-header bg-warning">
                    <h5><?= __("STAGE 2 - EVIDENCE TO EVALUATE LEARNING") ?></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong><?= __("PERFORMANCE TASKS") ?>:</strong></label>
                                <textarea class="form-control" name="tareas" rows="8"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong><?= __("OTHER TASKS") ?>:</strong></label>
                                <textarea class="form-control" name="otra" rows="8"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stage 3 - Learning Plan -->
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <h5><?= __("STAGE 3 - LEARNING PLAN") ?></h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label><strong>Standard:</strong></label>
                        <input type="text" class="form-control" name="expectativa">
                    </div>
                    <div class="form-group">
                        <label><strong>Depth of Knowledge:</strong></label>
                        <input type="text" class="form-control" name="estrategia">
                    </div>
                    <div class="form-group">
                        <label><strong>Objectives:</strong></label>
                        <input type="text" class="form-control" name="objetivos">
                    </div>
                </div>
            </div>

            <!-- Weekly Activities -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5><?= __("WEEKLY ACTIVITIES") ?></h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="12%">Day</th>
                                    <th width="15%">Activities</th>
                                    <th width="73%">Modifications for Students</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $days = [
                                    1 => 'MONDAY',
                                    2 => 'TUESDAY',
                                    3 => 'WEDNESDAY',
                                    4 => 'THURSDAY',
                                    5 => 'FRIDAY'
                                ];
                                $modifications = [
                                    1 => 'Seating',
                                    2 => 'Additional Time',
                                    3 => 'Individualized help',
                                    4 => 'Additional time for tests',
                                    5 => 'Positive reinforcement',
                                    6 => 'Others'
                                ];
                                foreach ($days as $dayNum => $dayName):
                                ?>
                                    <tr>
                                        <td>
                                            <strong><?= $dayName ?></strong><br>
                                            <input type="date" class="form-control form-control-sm mt-1"
                                                name="fecha<?= $dayNum ?>" id="fecha<?= $dayNum ?>">
                                        </td>
                                        <td>
                                            <textarea class="form-control" name="actividades<?= $dayNum ?>" rows="6"></textarea>
                                        </td>
                                        <td>
                                            <?php foreach ($modifications as $modNum => $modLabel): ?>
                                                <div class="form-check modification-item">
                                                    <input type="checkbox" class="form-check-input"
                                                        name="acomodo<?= $dayNum ?>_<?= $modNum ?>" value="si"
                                                        id="acomodo<?= $dayNum ?>_<?= $modNum ?>">
                                                    <label class="form-check-label" for="acomodo<?= $dayNum ?>_<?= $modNum ?>">
                                                        <?= $modLabel ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                            <div class="form-group mt-2">
                                                <input type="text" class="form-control form-control-sm"
                                                    name="otro<?= $dayNum ?>" placeholder="Other (specify)">
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="card mb-3">
                <div class="card-body text-center">
                    <button type="submit" class="btn btn-success btn-lg mr-2">
                        <i class="fas fa-save"></i> <span id="btnSaveText"><?= __("Guardar") ?></span>
                    </button>
                    <a href="#" class="btn btn-secondary btn-lg d-none mr-2" id="btnPrint" target="_blank">
                        <i class="fas fa-print"></i> <?= __("Imprimir") ?>
                    </a>
                    <a href="../opciones.php" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-arrow-left"></i> <?= __("Regresar") ?>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::sweetAlert();
    ?>

    <script>
        const API_URL = 'includes/index.php';
        let isEditMode = false;

        // New plan
        $('#btnNew').click(function() {
            console.log('Creating new plan');
            $('#lessonPlanForm')[0].reset();
            $('#planId').val('');
            $('#lessonPlanForm').removeClass('d-none');
            $('#btnSaveText').text('<?= __("Crear") ?>');
            $('#btnPrint, #btnPrintTop').addClass('d-none');
            isEditMode = false;
        });

        // Search plan
        $('#btnSearch').click(function() {
            const planId = $('#selectPlan').val();
            if (!planId) {
                Swal.fire('<?= __("Error") ?>', '<?= __("Seleccione un plan") ?>', 'error');
                return;
            }
            loadPlan(planId);
        });

        // Load plan function
        function loadPlan(planId) {
            $.ajax({
                url: API_URL,
                method: 'POST',
                data: {
                    getLessonPlan: planId
                },
                dataType: 'json',
                success: function(data) {
                    $('#planId').val(data.id);

                    // Basic info
                    $('input[name="maestro"]').val(data.profesor);
                    $('select[name="materia"]').val(data.materia);
                    $('input[name="titulo"]').val(data.titulo);
                    $('input[name="fecha"]').val(data.fecha);
                    $('input[name="duracion"]').val(data.duracion);

                    // Transversal themes
                    for (let i = 1; i <= 7; i++) {
                        $(`input[name="transversal${i}"]`).prop('checked', data[`transversal${i}`] == 'si');
                    }

                    // Integration
                    for (let i = 1; i <= 9; i++) {
                        $(`input[name="integracion${i}"]`).prop('checked', data[`integracion${i}`] == 'si');
                    }

                    // Stage 1
                    $('textarea[name="resumen"]').val(data.resumen);
                    for (let i = 1; i <= 5; i++) {
                        $(`input[name="pe${i}"]`).val(data[`pe${i}`]);
                        $(`input[name="ed${i}"]`).val(data[`ed${i}`]);
                    }
                    $('textarea[name="objetivo_general"]').val(data.objetivo_general);

                    // Stage 2
                    $('textarea[name="tareas"]').val(data.tareas);
                    $('textarea[name="otra"]').val(data.otra);

                    // Stage 3
                    $('input[name="expectativa"]').val(data.expectativa);
                    $('input[name="estrategia"]').val(data.estrategia);
                    $('input[name="objetivos"]').val(data.objetivos);

                    // Weekly activities
                    for (let day = 1; day <= 5; day++) {
                        $(`input[name="fecha${day}"]`).val(data[`fecha${day}`]);
                        $(`textarea[name="actividades${day}"]`).val(data[`actividades${day}`]);

                        for (let mod = 1; mod <= 6; mod++) {
                            $(`input[name="acomodo${day}_${mod}"]`).prop('checked', data[`acomodo${day}_${mod}`] == 'si');
                        }
                        $(`input[name="otro${day}"]`).val(data[`otro${day}`]);
                    }

                    $('#lessonPlanForm').removeClass('d-none');
                    $('#btnSaveText').text('<?= __("Actualizar") ?>');
                    $('#btnPrint').attr('href', 'pdf.php?id=' + data.id).removeClass('d-none');
                    $('#btnPrintTop').attr('href', 'pdf.php?id=' + data.id).removeClass('d-none');
                    isEditMode = true;
                },
                error: function() {
                    Swal.fire('<?= __("Error") ?>', '<?= __("Error al cargar el plan") ?>', 'error');
                }
            });
        }

        // Save plan
        $('#lessonPlanForm').submit(function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = {};

            // Convert FormData to object
            formData.forEach((value, key) => {
                if (key !== 'planId') {
                    data[key] = value;
                }
            });

            // Handle checkboxes - set to 'si' if checked, empty if not
            $('input[type="checkbox"]').each(function() {
                const name = $(this).attr('name');
                data[name] = $(this).is(':checked') ? 'si' : '';
            });

            if (isEditMode) {
                data.updateLessonPlan = true;
                data.planId = $('#planId').val();
            } else {
                data.createLessonPlan = true;
            }

            $.ajax({
                url: API_URL,
                method: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('<?= __("Éxito") ?>', '<?= __("Plan guardado exitosamente") ?>', 'success')
                            .then(() => {
                                window.location.href = window.location.pathname + '?plan=' + response.id;
                            });
                    } else {
                        Swal.fire('<?= __("Error") ?>', response.message || '<?= __("Error al guardar") ?>', 'error');
                    }
                },
                error: function() {
                    Swal.fire('<?= __("Error") ?>', '<?= __("Error al guardar el plan") ?>', 'error');
                }
            });
        });

        // Delete plan
        $('#btnDelete').click(function() {
            const planId = $('#selectPlan').val();
            if (!planId) {
                Swal.fire('<?= __("Error") ?>', '<?= __("Seleccione un plan") ?>', 'error');
                return;
            }

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
                        url: API_URL,
                        method: 'POST',
                        data: {
                            deleteLessonPlan: planId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('<?= __("Eliminado") ?>', '<?= __("Plan eliminado exitosamente") ?>', 'success')
                                    .then(() => window.location.href = window.location.pathname);
                            } else {
                                Swal.fire('<?= __("Error") ?>', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('<?= __("Error") ?>', '<?= __("Error al eliminar") ?>', 'error');
                        }
                    });
                }
            });
        });

        // Auto-load if plan ID in URL
        <?php if ($selectedPlanId): ?>
            setTimeout(() => loadPlan(<?= $selectedPlanId ?>), 100);
        <?php endif; ?>
    </script>
</body>

</html>