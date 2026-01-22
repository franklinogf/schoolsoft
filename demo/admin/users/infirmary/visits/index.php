<?php

require_once __DIR__ . '/../../../../app.php';

use App\Models\Family;
use App\Models\InfirmaryVisit;
use App\Models\Student;
use Classes\Route;
use Classes\Session;

Session::is_logged();

// Get all active students for the current year
$students = Student::orderBy('apellidos')
    ->orderBy('nombre')
    ->get();

// Check if a student is selected
$selectedSS = $_GET['ss'] ?? '';
$student = null;
$visits = collect();
$editRecord = null;
$family = null;

if (!empty($selectedSS)) {
    $student = Student::where('ss', $selectedSS)->first();

    if ($student) {
        $visits = InfirmaryVisit::where('ss', $selectedSS)
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->get();

        // Get family information for parent contact dropdown
        $family = Family::where('id', $student->id)->first();

        // Check if we're in edit mode
        $editFecha = $_GET['edit_fecha'] ?? '';
        $editHora = $_GET['edit_hora'] ?? '';

        if (!empty($editFecha) && !empty($editHora)) {
            $editRecord = InfirmaryVisit::findByCompositeKey($selectedSS, $editFecha, $editHora);
        }
    }
}

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Visitas Enfermería");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
    <script>
        function toggleParentContact() {
            const notified = document.getElementById('notif_padres').value;
            const padreSelect = document.getElementById('padre_contacto');
            const telefonoSelect = document.getElementById('telefono');

            if (notified === 'Si') {
                padreSelect.disabled = false;
                telefonoSelect.disabled = false;
            } else {
                padreSelect.disabled = true;
                telefonoSelect.disabled = true;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleParentContact();
        });
    </script>
</head>

<body class="pb-5">
    <?php Route::includeFile('/admin/includes/layouts/menu.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4"><?= __("Departamento de Enfermería") ?></h1>
        <h4 class="text-center mb-4"><?= __("Visitas Enfermería") ?></h4>

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
                                    <?= $s->apellidos . ', ' . $s->nombre . ' - ' . __("Grado") . ': ' . $s->grado . ' (SS: ' . $s->ss . ')' ?>
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

            <!-- Add/Edit Form -->
            <div class="card mb-4">
                <div class="card-header <?= $editRecord ? 'bg-warning' : 'bg-success' ?> text-white">
                    <h5 class="mb-0">
                        <?= $editRecord ? __("Editar Visita") : __("Registrar Nueva Visita") ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= Route::url('/admin/users/infirmary/visits/includes/save.php') ?>" method="POST">
                        <input type="hidden" name="ss" value="<?= $student->ss ?>">
                        <input type="hidden" name="id" value="<?= $student->id ?>">

                        <?php if ($editRecord): ?>
                            <input type="hidden" name="old_fecha" value="<?= $editRecord->fecha ? $editRecord->fecha->format('Y-m-d') : '' ?>">
                            <input type="hidden" name="old_hora" value="<?= $editRecord->hora ?>">
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha"><strong><?= __("Fecha") ?>:</strong></label>
                                    <input type="date" class="form-control" id="fecha" name="fecha"
                                        value="<?= $editRecord && $editRecord->fecha ? $editRecord->fecha->format('Y-m-d') : date('Y-m-d') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="hora"><strong><?= __("Hora") ?>:</strong></label>
                                    <input type="time" class="form-control" id="hora" name="hora"
                                        value="<?= $editRecord ? $editRecord->hora : date('H:i') ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="razon"><strong><?= __("Razón de la visita") ?>:</strong></label>
                                    <textarea class="form-control" id="razon" name="razon" rows="3" required><?= $editRecord ? $editRecord->razon : '' ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tratamiento"><strong><?= __("Tratamiento") ?>:</strong></label>
                                    <textarea class="form-control" id="tratamiento" name="tratamiento" rows="3"><?= $editRecord ? $editRecord->tratamiento : '' ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="recomendacion"><strong><?= __("Recomendación") ?>:</strong></label>
                                    <textarea class="form-control" id="recomendacion" name="recomendacion" rows="3"><?= $editRecord ? $editRecord->recomendacion : '' ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="observaciones"><strong><?= __("Observaciones") ?>:</strong></label>
                                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3"><?= $editRecord ? $editRecord->observaciones : '' ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="notif_padres"><strong><?= __("Se Notificó a Padres") ?>:</strong></label>
                                    <select class="form-control" id="notif_padres" name="notif_padres" onchange="toggleParentContact()">
                                        <option value="No" <?= (!$editRecord || $editRecord->notif_padres == 'No') ? 'selected' : '' ?>>
                                            <?= __("No") ?>
                                        </option>
                                        <option value="Si" <?= ($editRecord && $editRecord->notif_padres == 'Si') ? 'selected' : '' ?>>
                                            <?= __("Sí") ?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="padre_contacto"><strong><?= __("Padre Contactado") ?>:</strong></label>
                                    <select class="form-control" id="padre_contacto" name="padre_contacto">
                                        <option value=""><?= __("-- Seleccione --") ?></option>
                                        <?php if ($family): ?>
                                            <?php if (!empty($family->madre)): ?>
                                                <option value="<?= $family->madre ?>" <?= ($editRecord && $editRecord->padre_contacto == $family->madre) ? 'selected' : '' ?>>
                                                    <?= $family->madre ?>
                                                </option>
                                            <?php endif; ?>
                                            <?php if (!empty($family->padre)): ?>
                                                <option value="<?= $family->padre ?>" <?= ($editRecord && $editRecord->padre_contacto == $family->padre) ? 'selected' : '' ?>>
                                                    <?= $family->padre ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="telefono"><strong><?= __("Teléfono") ?>:</strong></label>
                                    <select class="form-control" id="telefono" name="telefono">
                                        <option value=""><?= __("-- Seleccione --") ?></option>
                                        <?php if ($family): ?>
                                            <?php if (!empty($family->tel_m)): ?>
                                                <option value="<?= $family->tel_m ?>" <?= ($editRecord && $editRecord->telefono == $family->tel_m) ? 'selected' : '' ?>>
                                                    <?= $family->tel_m ?> (<?= __("Madre") ?>)
                                                </option>
                                            <?php endif; ?>
                                            <?php if (!empty($family->tel_p)): ?>
                                                <option value="<?= $family->tel_p ?>" <?= ($editRecord && $editRecord->telefono == $family->tel_p) ? 'selected' : '' ?>>
                                                    <?= $family->tel_p ?> (<?= __("Padre") ?>)
                                                </option>
                                            <?php endif; ?>
                                            <?php if (!empty($family->tel_t_m)): ?>
                                                <option value="<?= $family->tel_t_m ?>" <?= ($editRecord && $editRecord->telefono == $family->tel_t_m) ? 'selected' : '' ?>>
                                                    <?= $family->tel_t_m ?> (<?= __("Trabajo Madre") ?>)
                                                </option>
                                            <?php endif; ?>
                                            <?php if (!empty($family->tel_t_p)): ?>
                                                <option value="<?= $family->tel_t_p ?>" <?= ($editRecord && $editRecord->telefono == $family->tel_t_p) ? 'selected' : '' ?>>
                                                    <?= $family->tel_t_p ?> (<?= __("Trabajo Padre") ?>)
                                                </option>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <button type="submit" class="btn <?= $editRecord ? 'btn-warning' : 'btn-success' ?> btn-lg">
                                <i class="fas fa-save"></i>
                                <?= $editRecord ? __("Actualizar") : __("Guardar") ?>
                            </button>
                            <?php if ($editRecord): ?>
                                <a href="?ss=<?= $student->ss ?>" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times"></i> <?= __("Cancelar") ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Visits Table -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><?= __("Historial de Visitas") ?></h5>
                </div>
                <div class="card-body">
                    <?php if ($visits->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th><?= __("Fecha") ?></th>
                                        <th><?= __("Hora") ?></th>
                                        <th><?= __("Razón") ?></th>
                                        <th><?= __("Tratamiento") ?></th>
                                        <th><?= __("Recomendación") ?></th>
                                        <th><?= __("Notificó Padre") ?></th>
                                        <th><?= __("Padre Contactado") ?></th>
                                        <th><?= __("Teléfono") ?></th>
                                        <th class="text-center"><?= __("Acciones") ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($visits as $visit): ?>
                                        <tr>
                                            <td><?= $visit->fecha ? $visit->fecha->format('Y-m-d') : '' ?></td>
                                            <td><?= $visit->hora ?></td>
                                            <td><?= $visit->razon ?></td>
                                            <td><?= $visit->tratamiento ?></td>
                                            <td><?= $visit->recomendacion ?></td>
                                            <td><?= $visit->notif_padres ?></td>
                                            <td><?= $visit->padre_contacto ?></td>
                                            <td><?= $visit->telefono ?></td>
                                            <td class="text-center">
                                                <a href="?ss=<?= $student->ss ?>&edit_fecha=<?= $visit->fecha ? $visit->fecha->format('Y-m-d') : '' ?>&edit_hora=<?= urlencode($visit->hora) ?>"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> <?= __("Editar") ?>
                                                </a>
                                                <form action="<?= Route::url('/admin/users/infirmary/visits/includes/delete.php') ?>"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('<?= __("¿Está seguro que desea eliminar este registro?") ?>')">
                                                    <input type="hidden" name="ss" value="<?= $student->ss ?>">
                                                    <input type="hidden" name="id" value="<?= $student->id ?>">
                                                    <input type="hidden" name="fecha" value="<?= $visit->fecha ? $visit->fecha->format('Y-m-d') : '' ?>">
                                                    <input type="hidden" name="hora" value="<?= $visit->hora ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> <?= __("Eliminar") ?>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <?= __("No hay visitas registradas para este estudiante.") ?>
                        </div>
                    <?php endif; ?>
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
