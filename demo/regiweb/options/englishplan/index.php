<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Admin;
use App\Models\EnglishPlan;
use App\Models\Teacher;
use Classes\Route;
use Classes\Session;

Session::is_logged();

$teacher = Teacher::find(Session::id());
$school = Admin::primaryAdmin();
$plans = $teacher->englishPlans()->orderBy('id', 'desc')->get();
$selectedPlanId = $_GET['plan'] ?? null;
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Plan de Inglés");
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
    <style>
        .form-check-label {
            cursor: pointer;
            user-select: none;
        }

        .table-condensed {
            font-size: 0.9rem;
        }

        .table-condensed td {
            padding: 0.25rem;
        }

        .activity-section {
            border-left: 3px solid #007bff;
            padding-left: 1rem;
        }
    </style>
</head>

<body>
    <?php Route::includeFile('/regiweb/includes/layouts/menu.php'); ?>

    <div class="container-fluid mt-4">
        <h2 class="text-center mb-4"><?= __("Plan de Inglés") ?></h2>

        <!-- Controles superiores -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary btn-block" id="btnNew">
                            <i class="fas fa-plus"></i> <?= __("Nuevo Plan") ?>
                        </button>
                    </div>
                    <div class="col-md-5">
                        <label><?= __("Seleccionar Plan") ?>:</label>
                        <select class="form-control" id="selectPlan">
                            <option value=""><?= __("Seleccione un plan") ?></option>
                            <?php foreach ($plans as $plan): ?>
                                <option value="<?= $plan->id ?>" <?= $selectedPlanId == $plan->id ? 'selected' : '' ?>>
                                    <?= $plan->getDisplayTitle() ?> - Grade: <?= $plan->grade ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-info btn-block" id="btnSearch">
                            <i class="fas fa-search"></i> <?= __("Buscar") ?>
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-block" id="btnDelete">
                            <i class="fas fa-trash"></i> <?= __("Eliminar") ?>
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
        <form id="englishPlanForm" class="d-none">
            <input type="hidden" id="planId" name="planId">

            <!-- Información Básica -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><?= __("Información Básica") ?></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong><?= __("Maestro") ?>:</strong></label>
                                <input type="text" class="form-control" name="teacher" readonly
                                    value="<?= $teacher->nombre . ' ' . $teacher->apellidos ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong><?= __("Institución") ?>:</strong></label>
                                <input type="text" class="form-control" name="institution" readonly
                                    value="<?= $school->colegio ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><?= __("Grado") ?>:</label>
                                <input type="text" class="form-control" name="grade" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><?= __("Fechas") ?>:</label>
                                <input type="date" class="form-control" name="dates" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><?= __("Materia") ?>:</label>
                                <input type="text" class="form-control" name="subject" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><?= __("Tema") ?>:</label>
                                <input type="text" class="form-control" name="topic" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Standards y Strategy -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><?= __("Standards & Strategy") ?></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><?= __("Standards") ?>:</h6>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="standard1" value="Si" id="standard1">
                                <label class="form-check-label" for="standard1">Oral Communication</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="standard2" value="Si" id="standard2">
                                <label class="form-check-label" for="standard2">Written Communication</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="standard3" value="Si" id="standard3">
                                <label class="form-check-label" for="standard3">Communication Reading</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><?= __("Strategy") ?>:</h6>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="strategy1" value="Si" id="strategy1">
                                <label class="form-check-label" for="strategy1">ECA</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="strategy2" value="Si" id="strategy2">
                                <label class="form-check-label" for="strategy2">Trilogy Literacy</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="strategy3" value="Si" id="strategy3">
                                <label class="form-check-label" for="strategy3">Cycles of Learning</label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h6><?= __("Depth Level of Knowledge") ?>:</h6>
                            <div class="form-check form-check-inline">
                                <input type="checkbox" class="form-check-input" name="depth1" value="Si" id="depth1">
                                <label class="form-check-label" for="depth1">Rote</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="checkbox" class="form-check-input" name="depth2" value="Si" id="depth2">
                                <label class="form-check-label" for="depth2">Processing</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="checkbox" class="form-check-input" name="depth3" value="Si" id="depth3">
                                <label class="form-check-label" for="depth3">Strategic</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="checkbox" class="form-check-input" name="depth4" value="Si" id="depth4">
                                <label class="form-check-label" for="depth4">Extended</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><?= __("Appraisal") ?>:</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    $appraisals = [
                                        'Diagnostic Test',
                                        'Whirlwind of Ideas',
                                        'Targeted List',
                                        'Concept Map',
                                        'Concrete Poem',
                                        'Comics',
                                        'Open Question'
                                    ];
                                    for ($i = 1; $i <= 7; $i++):
                                    ?>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="appraisal<?= $i ?>" value="Si" id="appraisal<?= $i ?>">
                                            <label class="form-check-label" for="appraisal<?= $i ?>"><?= $appraisals[$i - 1] ?></label>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                                <div class="col-md-6">
                                    <?php
                                    $appraisals2 = ['Reflective Journal', 'Test', 'Interviews', 'Quiz', 'Review', 'Review 2', 'Other'];
                                    for ($i = 8; $i <= 14; $i++):
                                    ?>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="appraisal<?= $i ?>" value="Si" id="appraisal<?= $i ?>">
                                            <label class="form-check-label" for="appraisal<?= $i ?>"><?= $appraisals2[$i - 8] ?></label>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- General Objectives -->
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><?= __("General Objectives") ?></h5>
                </div>
                <div class="card-body">
                    <textarea class="form-control" name="general" rows="4"></textarea>
                </div>
            </div>

            <!-- Specific Objectives -->
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><?= __("Specific Objectives") ?> <small>(Use Norman Webb Verbs List)</small></h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th width="10%">Level</th>
                                <th width="45%">Objective 1</th>
                                <th width="45%">Objective 2</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <tr>
                                    <td class="align-middle text-center"><strong>Level <?= $i ?></strong></td>
                                    <td><input type="text" class="form-control form-control-sm" name="level<?= $i ?>_1"></td>
                                    <td><input type="text" class="form-control form-control-sm" name="level<?= $i ?>_2"></td>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Activities Section -->
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><?= __("Activities, Materials & Assessment") ?></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Activities -->
                        <div class="col-md-2">
                            <h6><strong>Activities - Fases:</strong></h6>
                            <?php
                            $activities = [
                                'Exploration',
                                'Conceptualization',
                                'Implementation',
                                'Before Reading',
                                'During Read',
                                'After Reading',
                                'Focus',
                                'Scan',
                                'Reflect',
                                'Apply'
                            ];
                            foreach ($activities as $idx => $activity):
                                $i = $idx + 1;
                            ?>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="activities<?= $i ?>" value="Si" id="activities<?= $i ?>">
                                    <label class="form-check-label" for="activities<?= $i ?>"><?= $activity ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Materials -->
                        <div class="col-md-2">
                            <h6><strong>Materials:</strong></h6>
                            <?php
                            $materials = [
                                'Copy',
                                'Book',
                                'Slate',
                                'Newspaper',
                                'Calculator',
                                'Computer',
                                'Crayons',
                                'Graph Paper',
                                'Construction Paper',
                                'Conveyor/Rule',
                                'Transparency',
                                'Manipulatives',
                                'Mimeographed Sheet',
                                'Other'
                            ];
                            foreach ($materials as $idx => $material):
                                $i = $idx + 1;
                            ?>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="materials<?= $i ?>" value="Si" id="materials<?= $i ?>">
                                    <label class="form-check-label" for="materials<?= $i ?>"><?= $material ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Home -->
                        <div class="col-md-2">
                            <h6><strong>Home:</strong></h6>
                            <?php
                            $homes = [
                                'Reflection',
                                'Poem',
                                'Song',
                                'Game',
                                'Discussion of Allocation',
                                'Questions to Study',
                                'Review Concepts',
                                'Observation and Study'
                            ];
                            foreach ($homes as $idx => $home):
                                $i = $idx + 1;
                            ?>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="home<?= $i ?>" value="Si" id="home<?= $i ?>">
                                    <label class="form-check-label" for="home<?= $i ?>"><?= $home ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Development -->
                        <div class="col-md-3">
                            <h6><strong>Development:</strong></h6>
                            <?php
                            $developments = [
                                'Oral Reading',
                                'Reading and Analysis',
                                'Definition of Concepts',
                                'Demonstration & Examples',
                                'Work Practice',
                                'Oral Report',
                                'Film Analysis',
                                'Competition',
                                'Test',
                                'Test Cut'
                            ];
                            foreach ($developments as $idx => $development):
                                $i = $idx + 1;
                            ?>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="development<?= $i ?>" value="Si" id="development<?= $i ?>">
                                    <label class="form-check-label" for="development<?= $i ?>"><?= $development ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Closing & Assessment -->
                        <div class="col-md-3">
                            <h6><strong>Closing:</strong></h6>
                            <?php
                            $closings = ['Clarifying Concepts', 'Discussion of Work', 'Compare Work'];
                            foreach ($closings as $idx => $closing):
                                $i = $idx + 1;
                            ?>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="closing<?= $i ?>" value="Si" id="closing<?= $i ?>">
                                    <label class="form-check-label" for="closing<?= $i ?>"><?= $closing ?></label>
                                </div>
                            <?php endforeach; ?>

                            <h6 class="mt-3"><strong>Assessment:</strong></h6>
                            <?php
                            $assessments = [
                                'Diagnostic Test',
                                'Whirlwind of Ideas',
                                'Targeted List',
                                'Concept Map',
                                'Concrete Poem',
                                'Comics',
                                'Draft',
                                'Open Question',
                                'Reflective Journal',
                                'Test',
                                'Interviews',
                                'Quiz',
                                'Review',
                                'Other'
                            ];
                            foreach ($assessments as $idx => $assessment):
                                $i = $idx + 1;
                            ?>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="assessment<?= $i ?>" value="Si" id="assessment<?= $i ?>">
                                    <label class="form-check-label" for="assessment<?= $i ?>"><?= $assessment ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Activities -->
            <div class="card mb-3">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><?= __("Daily Activities") ?></h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th width="15%">Day / Phase</th>
                                <th width="17%">Materials</th>
                                <th width="17%">Home</th>
                                <th width="17%">Development</th>
                                <th width="17%">Closing</th>
                                <th width="17%">Assessment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $days = ['tuesday' => 'Tuesday', 'wednesday' => 'Wednesday', 'thursday' => 'Thursday', 'friday' => 'Friday'];
                            foreach ($days as $day => $dayLabel):
                            ?>
                                <tr>
                                    <td>
                                        <strong><?= $dayLabel ?></strong><br>
                                        <small>Phase:</small>
                                        <input type="text" class="form-control form-control-sm mt-1" name="<?= $day ?>" placeholder="Phase">
                                    </td>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <td>
                                            <textarea class="form-control" name="<?= $day . $i ?>" rows="3"></textarea>
                                        </td>
                                    <?php endfor; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="card mb-3">
                <div class="card-body text-center">
                    <button type="submit" class="btn btn-success btn-lg mr-2">
                        <i class="fas fa-save"></i> <span id="btnSaveText"><?= __("Guardar") ?></span>
                    </button>
                    <a href="#" class="btn btn-secondary btn-lg d-none" id="btnPrint" target="_blank">
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
            $('#englishPlanForm')[0].reset();
            $('#planId').val('');
            $('#englishPlanForm').removeClass('d-none');
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
                    getEnglishPlan: planId
                },
                dataType: 'json',
                success: function(data) {
                    $('#planId').val(data.id);

                    // Basic info
                    $('input[name="teacher"]').val(data.teacher);
                    $('input[name="institution"]').val(data.institution);
                    $('input[name="grade"]').val(data.grade);
                    $('input[name="dates"]').val(data.dates);
                    $('input[name="subject"]').val(data.subject);
                    $('input[name="topic"]').val(data.topic);

                    // Standards, strategy, depth
                    for (let i = 1; i <= 3; i++) {
                        $(`input[name="standard${i}"]`).prop('checked', data[`standard${i}`] == 'Si');
                        $(`input[name="strategy${i}"]`).prop('checked', data[`strategy${i}`] == 'Si');
                    }
                    for (let i = 1; i <= 4; i++) {
                        $(`input[name="depth${i}"]`).prop('checked', data[`depth${i}`] == 'Si');
                    }

                    // Appraisal
                    for (let i = 1; i <= 14; i++) {
                        $(`input[name="appraisal${i}"]`).prop('checked', data[`appraisal${i}`] == 'Si');
                    }

                    // Objectives
                    $('textarea[name="general"]').val(data.general);
                    for (let i = 1; i <= 4; i++) {
                        $(`input[name="level${i}_1"]`).val(data[`level${i}_1`]);
                        $(`input[name="level${i}_2"]`).val(data[`level${i}_2`]);
                    }

                    // Activities
                    for (let i = 1; i <= 10; i++) {
                        $(`input[name="activities${i}"]`).prop('checked', data[`activities${i}`] == 'Si');
                    }

                    // Materials
                    for (let i = 1; i <= 14; i++) {
                        $(`input[name="materials${i}"]`).prop('checked', data[`materials${i}`] == 'Si');
                    }

                    // Home
                    for (let i = 1; i <= 8; i++) {
                        $(`input[name="home${i}"]`).prop('checked', data[`home${i}`] == 'Si');
                    }

                    // Development
                    for (let i = 1; i <= 10; i++) {
                        $(`input[name="development${i}"]`).prop('checked', data[`development${i}`] == 'Si');
                    }

                    // Closing
                    for (let i = 1; i <= 3; i++) {
                        $(`input[name="closing${i}"]`).prop('checked', data[`closing${i}`] == 'Si');
                    }

                    // Assessment
                    for (let i = 1; i <= 14; i++) {
                        $(`input[name="assessment${i}"]`).prop('checked', data[`assessment${i}`] == 'Si');
                    }

                    // Daily activities
                    const days = ['tuesday', 'wednesday', 'thursday', 'friday'];
                    days.forEach(day => {
                        $(`input[name="${day}"]`).val(data[day]);
                        for (let i = 1; i <= 5; i++) {
                            $(`textarea[name="${day}${i}"]`).val(data[`${day}${i}`]);
                        }
                    });

                    $('#englishPlanForm').removeClass('d-none');
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
        $('#englishPlanForm').submit(function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = {};

            // Convert FormData to object
            formData.forEach((value, key) => {
                if (key !== 'planId') {
                    data[key] = value;
                }
            });

            // Handle checkboxes
            $('input[type="checkbox"]').each(function() {
                const name = $(this).attr('name');
                data[name] = $(this).is(':checked') ? 'Si' : '';
            });

            if (isEditMode) {
                data.updateEnglishPlan = true;
                data.planId = $('#planId').val();
            } else {
                data.createEnglishPlan = true;
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
                            deleteEnglishPlan: planId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('<?= __("Eliminado") ?>', '<?= __("Plan eliminado exitosamente") ?>', 'success')
                                    .then(() => window.location.reload());
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