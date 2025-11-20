<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\Admin;
use App\Models\Teacher;
use App\Models\WeeklyPlan3;
use Classes\Route;
use Classes\Session;

Session::is_logged();

$teacher = Teacher::find(Session::id());
$school = Admin::primaryAdmin();
$year = $school->year;
// Get all courses for the teacher
$courses = $teacher->subjects;

// Get selected course from URL or POST
$selectedCourse = $_GET['curso'] ?? null;
$selectedWeek = $_GET['week'] ?? null;
$weeklyPlan = null;
$weeklyPlans = [];


// If course is selected, get all weekly plans for that course
if ($selectedCourse) {
    $weeklyPlans = WeeklyPlan3::byTeacher($teacher->id)
        ->byCourse($selectedCourse)
        ->orderBy('week', 'desc')
        ->get();

    // If specific week is selected, load that plan
    if ($selectedWeek) {
        $weeklyPlan = WeeklyPlan3::byTeacher($teacher->id)
            ->byCourse($selectedCourse)
            ->byWeek($selectedWeek)
            ->first();

        // If new plan is requested and doesn't exist, create empty object
        if (!$weeklyPlan && isset($_GET['new'])) {
            $weeklyPlan = new WeeklyPlan3();
            $weeklyPlan->week = $selectedWeek;
            $weeklyPlan->curso = $selectedCourse;
        }
    }
}

$months = [
    "01" => "Enero",
    "02" => "Febrero",
    "03" => "Marzo",
    "04" => "Abril",
    "05" => "Mayo",
    "06" => "Junio",
    "07" => "Julio",
    "08" => "Agosto",
    "09" => "Septiembre",
    "10" => "Octubre",
    "11" => "Noviembre",
    "12" => "Diciembre"
];


?>

<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Plan Semanal 3");
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
    <style>
        .weekly-table {
            width: 100%;
        }

        .weekly-table th {
            background-color: #6c757d;
            color: white;
            padding: 10px;
            text-align: center;
        }

        .weekly-table td {
            border: 1px solid #dee2e6;
            padding: 10px;
            vertical-align: top;
        }

        .month-header {
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .day-header {
            background-color: #e9ecef;
            font-weight: bold;
            text-align: center;
        }

        .comment-section {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>

    <div class="container-fluid mt-3 mb-5 px-3">
        <h1 class="text-center mb-4"><?= __("Plan Semanal 3") ?></h1>

        <!-- Course Selection -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" id="courseForm">
                    <div class="row align-items-end">
                        <div class="col-md-7">
                            <label><?= __("Seleccionar Curso") ?>:</label>
                            <select class="form-control" name="curso" id="courseSelector" required>
                                <option value=""><?= __("Seleccione un curso") ?></option>
                                <?php foreach ($courses as $course): ?>

                                    <option value="<?= $course->curso ?>" <?= $selectedCourse == $course->curso ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($course->descripcion) ?> - <?= $course->curso ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <button type="submit" class="btn btn-info">
                                <i class="fa fa-search"></i> <?= __("Buscar") ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($selectedCourse): ?>
            <!-- Week Selection and Actions -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label><?= __("Crear Nueva Semana") ?>:</label>
                            <input type="week" class="form-control" id="newWeekInput">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary" id="createWeekBtn">
                                <i class="fa fa-plus"></i> <?= __("Crear") ?>
                            </button>
                        </div>
                        <div class="col-md-4">
                            <label><?= __("Seleccionar Semana") ?>:</label>
                            <select class="form-control" id="weekSelector">
                                <option value=""><?= __("Seleccione una semana") ?></option>
                                <?php foreach ($weeklyPlans as $plan): ?>
                                    <?php
                                    $weekDisplay = $plan->getFormattedWeek();
                                    ?>
                                    <option value="<?= $plan->week ?>" <?= $selectedWeek == $plan->week ? 'selected' : '' ?>>
                                        <?= $weekDisplay ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-info" id="searchWeekBtn">
                                <i class="fa fa-search"></i> <?= __("Buscar") ?>
                            </button>
                        </div>
                    </div>

                    <?php if ($weeklyPlan && $weeklyPlan->id): ?>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-secondary" id="printBtn">
                                    <i class="fa fa-print"></i> <?= __("Imprimir") ?>
                                </button>
                                <button type="button" class="btn btn-success" id="needsBtn">
                                    <i class="fa fa-list"></i> <?= __("Necesidades") ?>
                                </button>
                                <button type="button" class="btn btn-danger" id="deleteBtn">
                                    <i class="fa fa-trash"></i> <?= __("Borrar") ?>
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($weeklyPlan): ?>
                <!-- Weekly Plan Form -->
                <form id="weeklyPlanForm">
                    <input type="hidden" name="id" value="<?= $weeklyPlan->id ?? '' ?>">
                    <input type="hidden" name="curso" value="<?= $selectedCourse ?>">
                    <input type="hidden" name="week" value="<?= $weeklyPlan->week ?>">

                    <!-- Month Header -->
                    <?php
                    $week = strstr($weeklyPlan->week, "W");
                    $y = str_replace('-', '', strstr($weeklyPlan->week, "W", true));
                    $monthName = $months[date("m", strtotime($y . $week . "1"))];
                    ?>
                    <div class="month-header">
                        <?= $monthName ?>
                    </div>

                    <!-- Weekly Activities Table -->
                    <div class="card mb-3">
                        <div class="card-header bg-secondary text-white">
                            <strong><?= __("Actividades Semanales") ?></strong>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="weekly-table table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 15%;"><?= __("Día") ?></th>
                                            <th style="width: 42.5%;"><?= __("Tema") ?></th>
                                            <th style="width: 42.5%;"><?= __("Objetivo") ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
                                        for ($i = 1; $i <= 5; $i++):
                                            $dayDate = date('d', strtotime($y . $week . $i));
                                        ?>
                                            <tr>
                                                <td class="day-header">
                                                    <strong><?= $days[$i - 1] ?> <?= $dayDate ?></strong>
                                                </td>
                                                <td>
                                                    <textarea class="form-control" name="dia<?= $i ?>_1" rows="5"><?= htmlspecialchars($weeklyPlan->{"dia{$i}_1"} ?? '') ?></textarea>
                                                </td>
                                                <td>
                                                    <textarea class="form-control" name="dia<?= $i ?>_2" rows="5"><?= htmlspecialchars($weeklyPlan->{"dia{$i}_2"} ?? '') ?></textarea>
                                                </td>
                                            </tr>
                                        <?php endfor; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Comments Section -->
                    <div class="card mb-3">
                        <div class="card-header bg-secondary text-white">
                            <strong><?= __("Comentarios") ?></strong>
                        </div>
                        <div class="card-body">
                            <textarea class="form-control" name="nota" rows="5"><?= htmlspecialchars($weeklyPlan->nota ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- Admin Comments (if any) -->
                    <?php if (!empty($weeklyPlan->comentario)): ?>
                        <div class="comment-section">
                            <h5><i class="fa fa-comment"></i> <?= __("Comentario del Administrador") ?></h5>
                            <p><?= htmlspecialchars($weeklyPlan->comentario) ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Save Button -->
                    <div class="text-center mb-4">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fa fa-save"></i> <?= __("Guardar") ?>
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Modal for Student Needs -->
    <div class="modal fade" id="needsModal" tabindex="-1" role="dialog" aria-labelledby="needsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="max-width: 90%;">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="needsModalLabel">
                        <i class="fa fa-list"></i> <?= __("Necesidades Especiales de Estudiantes") ?>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="needsModalBody">
                    <div class="text-center">
                        <i class="fa fa-spinner fa-spin fa-3x"></i>
                        <p><?= __("Cargando...") ?></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> <?= __("Cerrar") ?>
                    </button>
                    <button type="button" class="btn btn-success" id="saveNeedsBtn">
                        <i class="fa fa-save"></i> <?= __("Guardar") ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::sweetAlert();
    ?>

    <script>
        $(document).ready(function() {
            const courseSelector = $('#courseSelector');
            const weekSelector = $('#weekSelector');
            const newWeekInput = $('#newWeekInput');
            const createWeekBtn = $('#createWeekBtn');
            const searchWeekBtn = $('#searchWeekBtn');
            const deleteBtn = $('#deleteBtn');
            const printBtn = $('#printBtn');
            const needsBtn = $('#needsBtn');
            const weeklyPlanForm = $('#weeklyPlanForm');


            // Create new week
            createWeekBtn.click(function() {
                const week = newWeekInput.val();
                const curso = courseSelector.val();

                if (!week) {
                    Swal.fire({
                        icon: 'warning',
                        title: '<?= __("Atención") ?>',
                        text: '<?= __("Por favor seleccione una semana") ?>'
                    });
                    return;
                }

                if (!curso) {
                    Swal.fire({
                        icon: 'warning',
                        title: '<?= __("Atención") ?>',
                        text: '<?= __("Por favor seleccione un curso") ?>'
                    });
                    return;
                }

                window.location.href = '<?= Route::url('/regiweb/options/weeklyplans/plan3/index.php') ?>?curso=' + curso + '&week=' + week + '&new=1';
            });

            // Search week
            searchWeekBtn.click(function() {
                const week = weekSelector.val();
                const curso = courseSelector.val();

                if (!week) {
                    Swal.fire({
                        icon: 'warning',
                        title: '<?= __("Atención") ?>',
                        text: '<?= __("Por favor seleccione una semana") ?>'
                    });
                    return;
                }

                window.location.href = '<?= Route::url('/regiweb/options/weeklyplans/plan3/index.php') ?>?curso=' + curso + '&week=' + week;
            });

            // Delete plan
            deleteBtn.click(function() {
                const planId = $('input[name="id"]').val();

                Swal.fire({
                    title: '<?= __("¿Está seguro?") ?>',
                    text: '<?= __("¿Desea eliminar este plan semanal?") ?>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '<?= __("Sí, eliminar") ?>',
                    cancelButtonText: '<?= __("Cancelar") ?>'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '<?= Route::url('/regiweb/options/weeklyplans/plan3/includes/index.php') ?>',
                            type: 'POST',
                            data: {
                                deleteWeeklyPlan: planId
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '<?= __("Eliminado") ?>',
                                        text: '<?= __("Plan semanal eliminado correctamente") ?>'
                                    }).then(() => {
                                        window.location.href = '<?= Route::url('/regiweb/options/weeklyplans/plan3/index.php') ?>?curso=' + courseSelector.val();
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

            // Print plan
            printBtn.click(function() {
                const planId = $('input[name="id"]').val();
                window.open('<?= Route::url('/regiweb/options/weeklyplans/plan3/planes_inf.php') ?>?id=' + planId, '_blank');
            });

            // Needs management
            needsBtn.click(function() {
                const planId = $('input[name="id"]').val();
                const curso = courseSelector.val();

                // Show modal
                $('#needsModal').modal('show');

                // Load needs via AJAX
                $.ajax({
                    url: '<?= Route::url('/regiweb/options/weeklyplans/plan3/includes/index.php') ?>',
                    type: 'POST',
                    data: {
                        getStudentNeeds: planId,
                        curso: curso
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            let html = '<form id="needsForm">';
                            html += '<input type="hidden" name="planId" value="' + planId + '">';
                            html += '<input type="hidden" name="curso" value="' + curso + '">';
                            html += '<table class="table table-bordered">';
                            html += '<thead class="thead-light">';
                            html += '<tr>';
                            html += '<th style="width: 5%;">#</th>';
                            html += '<th style="width: 35%;"><?= __("Estudiante") ?></th>';
                            html += '<th style="width: 60%;"><?= __("Necesidades Especiales") ?></th>';
                            html += '</tr>';
                            html += '</thead>';
                            html += '<tbody>';

                            if (response.students.length > 0) {
                                response.students.forEach(function(student, index) {
                                    html += '<tr>';
                                    html += '<td>' + (index + 1) + '</td>';
                                    html += '<td class="font-weight-bold">' + student.name + '</td>';
                                    html += '<td>';
                                    html += '<input type="text" class="form-control" name="need_' + student.id + '" ';
                                    html += 'value="' + (student.necesidad || '') + '" ';
                                    html += 'placeholder="<?= __("Ingrese las necesidades especiales del estudiante") ?>">';
                                    html += '</td>';
                                    html += '</tr>';
                                });
                            } else {
                                html += '<tr><td colspan="3" class="text-center"><?= __("No hay estudiantes registrados en este curso") ?></td></tr>';
                            }

                            html += '</tbody>';
                            html += '</table>';
                            html += '</form>';

                            $('#needsModalBody').html(html);
                        } else {
                            $('#needsModalBody').html('<div class="alert alert-danger">' + (response.message || 'Error al cargar') + '</div>');
                        }
                    },
                    error: function() {
                        $('#needsModalBody').html('<div class="alert alert-danger"><?= __("Error de conexión") ?></div>');
                    }
                });
            });

            // Save student needs
            $(document).on('click', '#saveNeedsBtn', function() {
                const formData = $('#needsForm').serialize();

                $.ajax({
                    url: '<?= Route::url('/regiweb/options/weeklyplans/plan3/includes/index.php') ?>',
                    type: 'POST',
                    data: formData + '&saveStudentNeeds=1',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '<?= __("Guardado") ?>',
                                text: '<?= __("Necesidades guardadas correctamente") ?>',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            $('#needsModal').modal('hide');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || '<?= __("Error al guardar") ?>'
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
            });

            // Save/Update plan
            weeklyPlanForm.submit(function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                const isNew = <?= (isset($_GET['new']) && !$weeklyPlan->id) ? 'true' : 'false' ?>;

                $.ajax({
                    url: '<?= Route::url('/regiweb/options/weeklyplans/plan3/includes/index.php') ?>',
                    type: 'POST',
                    data: formData + '&' + (isNew ? 'createWeeklyPlan=1' : 'updateWeeklyPlan=1'),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: isNew ? '<?= __("Creado") ?>' : '<?= __("Guardado") ?>',
                                text: '<?= __("Plan semanal") ?> ' + (isNew ? '<?= __("creado") ?>' : '<?= __("guardado") ?>') + ' <?= __("correctamente") ?>'
                            }).then(() => {
                                if (isNew) {
                                    window.location.href = '<?= Route::url('/regiweb/options/weeklyplans/plan3/index.php') ?>?curso=' + courseSelector.val() + '&week=' + response.week;
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || '<?= __("Error al guardar") ?>'
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
            });
        });
    </script>
</body>

</html>