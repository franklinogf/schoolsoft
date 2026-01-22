<?php

require_once __DIR__ . '/../../../../app.php';

use App\Models\Student;
use App\Models\Vital;
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
$vitals = collect();
$editRecord = null;

if (!empty($selectedSS)) {
    $student = Student::where('ss', $selectedSS)->first();

    if ($student) {
        $vitals = Vital::where('ss', $selectedSS)
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->get();

        // Check if we're in edit mode
        $editFecha = $_GET['edit_fecha'] ?? '';
        $editHora = $_GET['edit_hora'] ?? '';

        if (!empty($editFecha) && !empty($editHora)) {
            $editRecord = Vital::findByCompositeKey($selectedSS, $editFecha, $editHora);
        }
    }
}

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Monitoreo de Vitales");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body class="pb-5">
    <?php Route::includeFile('/admin/includes/layouts/menu.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4"><?= __("Departamento de Enfermería") ?></h1>
        <h4 class="text-center mb-4"><?= __("Monitoreo de Vitales") ?></h4>

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

            <!-- Vitals Table -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><?= __("Registros de Vitales") ?></h5>
                </div>
                <div class="card-body">
                    <?php if ($vitals->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th><?= __("Fecha") ?></th>
                                        <th><?= __("Hora") ?></th>
                                        <th><?= __("Presión Arterial") ?> (B/P)</th>
                                        <th><?= __("Pulso") ?> (P)</th>
                                        <th><?= __("Respiración") ?> (R)</th>
                                        <th><?= __("Temperatura") ?> (T)</th>
                                        <th><?= __("Glucosa") ?> (Dxt)</th>
                                        <th class="text-center"><?= __("Acciones") ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($vitals as $vital): ?>
                                        <tr>
                                            <td><?= $vital->fecha ? $vital->fecha->format('Y-m-d') : '' ?></td>
                                            <td><?= $vital->hora ?></td>
                                            <td><?= $vital->bp ?></td>
                                            <td><?= $vital->p ?></td>
                                            <td><?= $vital->r ?></td>
                                            <td><?= $vital->t ?></td>
                                            <td><?= $vital->dxt ?></td>
                                            <td class="text-center">
                                                <a href="?ss=<?= $student->ss ?>&edit_fecha=<?= $vital->fecha ? $vital->fecha->format('Y-m-d') : '' ?>&edit_hora=<?= urlencode($vital->hora) ?>"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> <?= __("Editar") ?>
                                                </a>
                                                <form action="<?= Route::url('/admin/users/infirmary/vitals/includes/delete.php') ?>"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('<?= __("¿Está seguro que desea eliminar este registro?") ?>')">
                                                    <input type="hidden" name="ss" value="<?= $student->ss ?>">
                                                    <input type="hidden" name="id" value="<?= $student->id ?>">
                                                    <input type="hidden" name="fecha" value="<?= $vital->fecha ? $vital->fecha->format('Y-m-d') : '' ?>">
                                                    <input type="hidden" name="hora" value="<?= $vital->hora ?>">
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
                            <?= __("No hay registros de vitales para este estudiante.") ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Add/Edit Form -->
            <div class="card mb-4">
                <div class="card-header <?= $editRecord ? 'bg-warning' : 'bg-success' ?> text-white">
                    <h5 class="mb-0">
                        <?= $editRecord ? __("Editar Registro de Vitales") : __("Agregar Nuevo Registro") ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= Route::url('/admin/users/infirmary/vitals/includes/save.php') ?>" method="POST">
                        <input type="hidden" name="ss" value="<?= $student->ss ?>">
                        <input type="hidden" name="id" value="<?= $student->id ?>">

                        <?php if ($editRecord): ?>
                            <input type="hidden" name="old_fecha" value="<?= $editRecord->fecha ? $editRecord->fecha->format('Y-m-d') : '' ?>">
                            <input type="hidden" name="old_hora" value="<?= $editRecord->hora ?>">
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fecha"><strong><?= __("Fecha") ?>:</strong></label>
                                    <input type="date" class="form-control" id="fecha" name="fecha"
                                        value="<?= $editRecord && $editRecord->fecha ? $editRecord->fecha->format('Y-m-d') : date('Y-m-d') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="hora"><strong><?= __("Hora") ?>:</strong></label>
                                    <input type="time" class="form-control" id="hora" name="hora"
                                        value="<?= $editRecord ? $editRecord->hora : date('H:i') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="bp"><strong><?= __("Presión Arterial") ?> (B/P):</strong></label>
                                    <input type="text" class="form-control" id="bp" name="bp"
                                        value="<?= $editRecord ? $editRecord->bp : '' ?>" placeholder="120/80">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="p"><strong><?= __("Pulso") ?> (P):</strong></label>
                                    <input type="text" class="form-control" id="p" name="p"
                                        value="<?= $editRecord ? $editRecord->p : '' ?>" placeholder="72">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="r"><strong><?= __("Respiración") ?> (R):</strong></label>
                                    <input type="text" class="form-control" id="r" name="r"
                                        value="<?= $editRecord ? $editRecord->r : '' ?>" placeholder="16">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="t"><strong><?= __("Temperatura") ?> (T):</strong></label>
                                    <input type="text" class="form-control" id="t" name="t"
                                        value="<?= $editRecord ? $editRecord->t : '' ?>" placeholder="98.6°F">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="dxt"><strong><?= __("Glucosa") ?> (Dxt):</strong></label>
                                    <input type="text" class="form-control" id="dxt" name="dxt"
                                        value="<?= $editRecord ? $editRecord->dxt : '' ?>" placeholder="100 mg/dL">
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
