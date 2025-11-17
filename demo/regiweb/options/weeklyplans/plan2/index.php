<?php
require_once '../../../../app.php';

use App\Models\Admin;
use App\Models\Teacher;
use App\Models\WeeklyPlan2;
use Classes\Route;
use Classes\Session;

Session::is_logged();

$teacher = Teacher::find(Session::id());
$school = Admin::primaryAdmin();
$year = $school->year;

// Obtener todos los planes semanales del maestro
$weeklyPlans = $teacher->hasMany(WeeklyPlan2::class, 'id', 'id')->orderBy('fecha', 'desc')->get();

// Obtener el ID del plan seleccionado de la URL si existe
$selectedPlanId = $_GET['plan'] ?? null;
$weeklyPlan = null;

if ($selectedPlanId) {
    $weeklyPlan = WeeklyPlan2::find($selectedPlanId);
    if (!$weeklyPlan || $weeklyPlan->id != $teacher->id) {
        $weeklyPlan = null;
    }
}
?>

<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Plan Semanal 2");
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

        .checkbox-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .trabajo-semanal-table {
            width: 100%;
        }

        .trabajo-semanal-table th {
            background-color: #6c757d;
            color: white;
            padding: 8px;
        }

        .trabajo-semanal-table td {
            padding: 8px;
            border: 1px solid #dee2e6;
        }
    </style>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>

    <div class="container-fluid mt-3 mb-5 px-3">
        <h1 class="text-center mb-4"><?= __("Plan Semanal 2") ?></h1>

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
                            <?php foreach ($weeklyPlans as $plan): ?>
                                <option value="<?= $plan->id2 ?>" <?= $selectedPlanId == $plan->id2 ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($plan->asignatura) ?> - <?= htmlspecialchars($plan->tema) ?> - <?= $plan->fecha ?> (ID: <?= $plan->id2 ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-info" id="searchPlanBtn">
                            <i class="fa fa-search"></i> <?= __("Buscar") ?>
                        </button>
                        <button type="button" class="btn btn-danger" id="deletePlanBtn" <?= !$weeklyPlan ? 'style="display:none;"' : '' ?>>
                            <i class="fa fa-trash"></i> <?= __("Borrar") ?>
                        </button>
                        <button type="button" class="btn btn-secondary" id="printPlanBtn" <?= !$weeklyPlan ? 'style="display:none;"' : '' ?>>
                            <i class="fa fa-print"></i> <?= __("Imprimir") ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario del Plan -->
        <form id="weeklyPlanForm" style="<?= !$weeklyPlan && !isset($_GET['new']) ? 'display:none;' : '' ?>">
            <input type="hidden" name="id2" id="planId" value="<?= $weeklyPlan->id2 ?? '' ?>">
            <input type="hidden" name="year" value="<?= $year ?>">
            <input type="hidden" name="isNew" id="isNew" value="<?= !$weeklyPlan ? '1' : '0' ?>">

            <!-- Sección 1: Información General -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <strong><?= __("Información General") ?></strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label><?= __("Nombre") ?>:</label>
                            <input type="text" class="form-control" name="nombre" maxlength="40" required
                                value="<?= htmlspecialchars($teacher->nombre . ' ' . $teacher->apellidos) ?>" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label><?= __("Asignatura") ?>:</label>
                            <input type="text" class="form-control" name="asignatura" maxlength="40" required
                                value="<?= htmlspecialchars($weeklyPlan->asignatura ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label><?= __("Grado") ?>:</label>
                            <select class="form-control" name="grado" required>
                                <option value=""><?= __("Seleccione un grado") ?></option>
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?= $i ?>" <?= ($weeklyPlan && $weeklyPlan->grado == $i) ? 'selected' : '' ?>>
                                        <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label><?= __("Fechas") ?>:</label>
                            <input type="text" class="form-control" name="fecha" maxlength="40" required
                                value="<?= htmlspecialchars($weeklyPlan->fecha ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label><?= __("Desde") ?>:</label>
                            <input type="text" class="form-control" name="desde" maxlength="40" required
                                value="<?= htmlspecialchars($weeklyPlan->desde ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label><?= __("Hasta") ?>:</label>
                            <input type="text" class="form-control" name="hasta" maxlength="40" required
                                value="<?= htmlspecialchars($weeklyPlan->hasta ?? '') ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label><?= __("Tema") ?>:</label>
                            <input type="text" class="form-control" name="tema" required
                                value="<?= htmlspecialchars($weeklyPlan->tema ?? '') ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label><?= __("Estándares") ?>:</label>
                            <input type="text" class="form-control mb-2" name="estandares1"
                                value="<?= htmlspecialchars($weeklyPlan->estandares1 ?? '') ?>">
                            <input type="text" class="form-control mb-2" name="estandares2"
                                value="<?= htmlspecialchars($weeklyPlan->estandares2 ?? '') ?>">
                            <input type="text" class="form-control" name="estandares3"
                                value="<?= htmlspecialchars($weeklyPlan->estandares3 ?? '') ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label><?= __("Objetivos") ?>:</label>
                            <input type="text" class="form-control mb-2" name="objetivos1"
                                value="<?= htmlspecialchars($weeklyPlan->objetivos1 ?? '') ?>">
                            <input type="text" class="form-control mb-2" name="objetivos2"
                                value="<?= htmlspecialchars($weeklyPlan->objetivos2 ?? '') ?>">
                            <input type="text" class="form-control mb-2" name="objetivos3"
                                value="<?= htmlspecialchars($weeklyPlan->objetivos3 ?? '') ?>">
                            <input type="text" class="form-control" name="objetivos4"
                                value="<?= htmlspecialchars($weeklyPlan->objetivos4 ?? '') ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label><?= __("Destrezas") ?>:</label>
                            <input type="text" class="form-control mb-2" name="destrezas1"
                                value="<?= htmlspecialchars($weeklyPlan->destrezas1 ?? '') ?>">
                            <input type="text" class="form-control mb-2" name="destrezas2"
                                value="<?= htmlspecialchars($weeklyPlan->destrezas2 ?? '') ?>">
                            <input type="text" class="form-control mb-2" name="destrezas3"
                                value="<?= htmlspecialchars($weeklyPlan->destrezas3 ?? '') ?>">
                            <input type="text" class="form-control" name="destrezas4"
                                value="<?= htmlspecialchars($weeklyPlan->destrezas4 ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 2: Estándares Comunes y Apoyo -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <strong><?= __("Estándares Comunes y Recursos") ?></strong>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label><strong><?= __("Estándares Comunes / Common Core Standards") ?>:</strong></label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="estand_comun1" value="Si"
                                        <?= ($weeklyPlan && $weeklyPlan->estand_comun1 == 'Si') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Vida laboral y universitaria</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="estand_comun2" value="Si"
                                        <?= ($weeklyPlan && $weeklyPlan->estand_comun2 == 'Si') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Rigurosidad académica</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="estand_comun3" value="Si"
                                        <?= ($weeklyPlan && $weeklyPlan->estand_comun3 == 'Si') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Integración internacional</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="estand_comun4" value="Si"
                                        <?= ($weeklyPlan && $weeklyPlan->estand_comun4 == 'Si') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Investigación basada en evidencia</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label><strong><?= __("Apoyo Didáctico") ?>:</strong></label>
                            <div class="checkbox-grid">
                                <?php
                                $apoyos = [
                                    'apoyo1' => 'Calculadora',
                                    'apoyo2' => 'DVD/VCR',
                                    'apoyo3' => 'Dibujo',
                                    'apoyo4' => 'Radio CD/Ipod',
                                    'apoyo5' => 'Diccionario',
                                    'apoyo6' => 'Tecnología',
                                    'apoyo7' => 'Filminas',
                                    'apoyo8' => 'Computadora',
                                    'apoyo9' => 'Grabadoras',
                                    'apoyo10' => 'Teatro',
                                    'apoyo11' => 'Láminas',
                                    'apoyo12' => 'Biblioteca',
                                    'apoyo13' => 'Películas',
                                    'apoyo14' => 'Música',
                                    'apoyo15' => 'Biblia',
                                    'apoyo16' => 'Hoja Fotocopia',
                                    'apoyo17' => 'Pizarra - Electrónica',
                                    'apoyo18' => 'Mapas',
                                    'apoyo19' => 'Franjas',
                                    'apoyo20' => 'Power Point',
                                    'apoyo21' => 'Juegos',
                                    'apoyo22' => 'Excel',
                                    'apoyo23' => 'Texto',
                                    'apoyo24' => 'Word',
                                    'apoyo25' => 'Carteles',
                                    'apoyo26' => 'Publisher',
                                    'apoyo27' => 'Proyector'
                                ];
                                foreach ($apoyos as $key => $label): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="<?= $key ?>" value="Si"
                                            <?= ($weeklyPlan && $weeklyPlan->$key == 'Si') ? 'checked' : '' ?>>
                                        <label class="form-check-label"><?= $label ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label><strong><?= __("Integración") ?>:</strong></label>
                            <?php
                            $integraciones = [
                                'integracion1' => 'Artes',
                                'integracion2' => 'Música',
                                'integracion3' => 'Religión',
                                'integracion4' => 'Español',
                                'integracion5' => 'Ciencias',
                                'integracion6' => 'Estudio Sociales',
                                'integracion7' => 'Inglés',
                                'integracion8' => 'Matemáticas',
                                'integracion9' => 'Educación Física',
                                'integracion10' => 'Teatro',
                                'integracion11' => 'Salud',
                                'integracion12' => 'Computadoras'
                            ];
                            foreach ($integraciones as $key => $label): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="<?= $key ?>" value="Si"
                                        <?= ($weeklyPlan && $weeklyPlan->$key == 'Si') ? 'checked' : '' ?>>
                                    <label class="form-check-label"><?= $label ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label><strong><?= __("Estrategias de Enseñanza-Aprendizaje") ?>:</strong></label>
                            <?php
                            $estrategias = [
                                'estrategias1' => 'Grupo Cooperativo',
                                'estrategias2' => 'Informe Oral',
                                'estrategias3' => 'Informe Escrito',
                                'estrategias4' => 'Demostración',
                                'estrategias5' => 'Conferencia',
                                'estrategias6' => 'Proyecto de investigación',
                                'estrategias7' => 'Mapa de Conceptos',
                                'estrategias8' => 'Experiencia de Campo',
                                'estrategias9' => 'Entrevista',
                                'estrategias10' => 'Debate',
                                'estrategias11' => 'Repaso',
                                'estrategias12' => 'Canción',
                                'estrategias13' => 'Laboratorio',
                                'estrategias14' => 'Tirillas Cómicas',
                                'estrategias15' => 'Observaciones'
                            ];
                            foreach ($estrategias as $key => $label): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="<?= $key ?>" value="Si"
                                        <?= ($weeklyPlan && $weeklyPlan->$key == 'Si') ? 'checked' : '' ?>>
                                    <label class="form-check-label"><?= $label ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 3: Avalúo y Valores -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <strong><?= __("Avalúo, Evaluación y Valores") ?></strong>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label><strong><?= __("Avalúo y Evaluación Realizado por los Estudiantes") ?>:</strong></label>
                        <div class="row">
                            <div class="col-md-3">
                                <label class="text-muted">Portafolio:</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="portafolio1" value="Si"
                                        <?= ($weeklyPlan && $weeklyPlan->portafolio1 == 'Si') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Examen Escrito</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="portafolio2" value="Si"
                                        <?= ($weeklyPlan && $weeklyPlan->portafolio2 == 'Si') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Examen Oral</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted">Prueba Corta:</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="prueba1" value="Si"
                                        <?= ($weeklyPlan && $weeklyPlan->prueba1 == 'Si') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Informe Escrito</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="prueba2" value="Si"
                                        <?= ($weeklyPlan && $weeklyPlan->prueba2 == 'Si') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Informe Oral</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted">Proyecto Especial:</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="proyecto1" value="Si"
                                        <?= ($weeklyPlan && $weeklyPlan->proyecto1 == 'Si') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Mapa Conceptual</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="proyecto2" value="Si"
                                        <?= ($weeklyPlan && $weeklyPlan->proyecto2 == 'Si') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Diario Reflexivo</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted">Contestar Preguntas:</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="contestar1" value="Si"
                                        <?= ($weeklyPlan && $weeklyPlan->contestar1 == 'Si') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Trabajo Cooperativo</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="contestar2" value="Si"
                                        <?= ($weeklyPlan && $weeklyPlan->contestar2 == 'Si') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Discusión Socializada</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label><strong><?= __("Valores") ?>:</strong></label>
                            <div class="checkbox-grid">
                                <?php
                                $valores = [
                                    'valores1' => 'Amor',
                                    'valores2' => 'Paz',
                                    'valores3' => 'Perdón',
                                    'valores4' => 'Respeto',
                                    'valores5' => 'Trabajo',
                                    'valores6' => 'Fe',
                                    'valores7' => 'Armonía',
                                    'valores8' => 'Honestidad',
                                    'valores9' => 'Alegría',
                                    'valores10' => 'Dignidad',
                                    'valores11' => 'Libertad',
                                    'valores12' => 'Solidaridad',
                                    'valores13' => 'Entrega',
                                    'valores14' => 'Tolerancia',
                                    'valores15' => 'Justicia',
                                    'valores16' => 'Generosidad',
                                    'valores17' => 'Servicio',
                                    'valores18' => 'Esperanza',
                                    'valores19' => 'Comunicación',
                                    'valores20' => 'Responsabilidad',
                                    'valores21' => 'Caridad',
                                    'valores22' => 'Esfuerzo'
                                ];
                                foreach ($valores as $key => $label): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="<?= $key ?>" value="Si"
                                            <?= ($weeklyPlan && $weeklyPlan->$key == 'Si') ? 'checked' : '' ?>>
                                        <label class="form-check-label"><?= $label ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><strong><?= __("Acomodos Razonables") ?>:</strong></label>
                            <p class="text-muted small">Favor de referirse a tabla de acomodos razonables e indicar solo el número.</p>
                            <?php
                            $acomodos = [
                                'acomodo1' => 'Atención',
                                'acomodo2' => 'Conducta',
                                'acomodo3' => 'Presentación',
                                'acomodo4' => 'Evaluación',
                                'acomodo5' => 'Ambiente y Lugar',
                                'acomodo6' => 'Tiempo e Itinerario'
                            ];
                            foreach ($acomodos as $key => $label): ?>
                                <div class="mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="<?= $key ?>" value="Si"
                                            <?= ($weeklyPlan && $weeklyPlan->$key == 'Si') ? 'checked' : '' ?>>
                                        <label class="form-check-label"><?= $label ?></label>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control form-control-sm" name="<?= $key ?>_1"
                                                value="<?= htmlspecialchars($weeklyPlan->{$key . '_1'} ?? '') ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control form-control-sm" name="<?= $key ?>_2"
                                                value="<?= htmlspecialchars($weeklyPlan->{$key . '_2'} ?? '') ?>">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 4: Trabajo Semanal -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <strong><?= __("Trabajo Semanal (2 semanas)") ?></strong>
                </div>
                <div class="card-body">
                    <table class="trabajo-semanal-table table table-bordered">
                        <?php
                        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
                        for ($semana = 1; $semana <= 2; $semana++):
                            if ($semana == 2): ?>
                                <tr>
                                    <td colspan="3" class="bg-light text-center"><strong>Semana 2</strong></td>
                                </tr>
                            <?php endif;

                            foreach ($dias as $index => $dia):
                                $num = ($semana - 1) * 5 + ($index + 1);
                            ?>
                                <tr>
                                    <th style="width: 20%;"><?= $dia ?></th>
                                    <th style="width: 40%;">Fase:</th>
                                    <th style="width: 40%;">Acomodo:</th>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm"
                                            name="semanal<?= $num ?>_1"
                                            value="<?= htmlspecialchars($weeklyPlan->{"semanal{$num}_1"} ?? '') ?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm"
                                            name="semanal<?= $num ?>_2"
                                            value="<?= htmlspecialchars($weeklyPlan->{"semanal{$num}_2"} ?? '') ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tarea:</th>
                                    <td colspan="2">
                                        <input type="text" class="form-control form-control-sm"
                                            name="semanal<?= $num ?>_3"
                                            value="<?= htmlspecialchars($weeklyPlan->{"semanal{$num}_3"} ?? '') ?>">
                                    </td>
                                </tr>
                        <?php
                            endforeach;
                        endfor;
                        ?>
                    </table>
                </div>
            </div>

            <!-- Sección 5: Revisión -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <strong><?= __("Revisión") ?></strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label><?= __("Revisado por") ?>:</label>
                            <input type="text" class="form-control" name="revisado1"
                                value="<?= htmlspecialchars($weeklyPlan->revisado1 ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label><?= __("Fecha") ?>:</label>
                            <input type="text" class="form-control" name="revisado2"
                                value="<?= htmlspecialchars($weeklyPlan->revisado2 ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label><?= __("Aprobado") ?>:</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="revisado3" value="Si"
                                        <?= ($weeklyPlan && $weeklyPlan->revisado3 == 'Si') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Sí</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="revisado4" value="Si"
                                        <?= ($weeklyPlan && $weeklyPlan->revisado4 == 'Si') ? 'checked' : '' ?>>
                                    <label class="form-check-label">No</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label><?= __("Comentario") ?>:</label>
                            <input type="text" class="form-control" name="revisado5"
                                value="<?= htmlspecialchars($weeklyPlan->revisado5 ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="text-center mb-4">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fa fa-save"></i>
                    <?= !$weeklyPlan || isset($_GET['new']) ? __("Crear") : __("Guardar") ?>
                </button>
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
            const deletePlanBtn = $('#deletePlanBtn');
            const printPlanBtn = $('#printPlanBtn');
            const newPlanBtn = $('#newPlanBtn');
            const weeklyPlanForm = $('#weeklyPlanForm');
            const planIdInput = $('#planId');
            const isNewInput = $('#isNew');

            // Nuevo plan
            newPlanBtn.click(function() {
                window.location.href = '<?= Route::url('/regiweb/options/weeklyplans/plan2/index.php?new=1') ?>';
            });

            // Buscar plan
            searchPlanBtn.click(function() {
                const planId = planSelector.val();
                if (planId) {
                    window.location.href = '<?= Route::url('/regiweb/options/weeklyplans/plan2/index.php') ?>?plan=' + planId;
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: '<?= __("Atención") ?>',
                        text: '<?= __("Por favor seleccione un plan") ?>'
                    });
                }
            });

            // Eliminar plan
            deletePlanBtn.click(function() {
                const planId = planIdInput.val();
                if (!planId) return;

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
                            url: '<?= Route::url('/regiweb/options/weeklyplans/plan2/includes/index.php') ?>',
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
                                        window.location.href = '<?= Route::url('/regiweb/options/weeklyplans/plan2/index.php') ?>';
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
                    window.open('<?= Route::url('/regiweb/options/weeklyplans/plan2/planes_inf.php') ?>?plan=' + planId, '_blank');
                }
            });

            // Guardar/Crear plan
            weeklyPlanForm.submit(function(e) {
                e.preventDefault();
                const formData = $(this).serializeArray();
                const isNew = isNewInput.val() === '1';
                const data = {};

                formData.forEach(item => {
                    data[item.name] = item.value;
                });

                if (isNew) {
                    data.createWeeklyPlan = true;
                } else {
                    data.updateWeeklyPlan = true;
                    data.weeklyPlanId = planIdInput.val();
                }

                $.ajax({
                    url: '<?= Route::url('/regiweb/options/weeklyplans/plan2/includes/index.php') ?>',
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: isNew ? '<?= __("Crear") ?>' : '<?= __("Guardar") ?>',
                                text: '<?= __("Plan semanal") ?> ' + (isNew ? '<?= __("creado") ?>' : '<?= __("guardado") ?>') + ' <?= __("correctamente") ?>'
                            }).then(() => {
                                window.location.href = '<?= Route::url('/regiweb/options/weeklyplans/plan2/index.php') ?>?plan=' + response.id;
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