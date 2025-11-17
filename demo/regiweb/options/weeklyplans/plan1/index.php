<?php
require_once '../../../../app.php';

use App\Models\Admin;
use App\Models\Teacher;
use App\Models\WeeklyPlan;
use Classes\Route;
use Classes\Session;

Session::is_logged();

$teacher = Teacher::find(Session::id());
$school = Admin::primaryAdmin();
$year = $school->year;

// Obtener todos los planes semanales del maestro
$weeklyPlans = $teacher->weeklyPlans()->orderBy('fecha', 'desc')->get();

// Obtener el ID del plan seleccionado de la URL si existe
$selectedPlanId = $_GET['plan'] ?? null;
$weeklyPlan = null;

if ($selectedPlanId) {
    $weeklyPlan = WeeklyPlan::find($selectedPlanId);
    if (!$weeklyPlan || $weeklyPlan->id != $teacher->id) {
        $weeklyPlan = null;
    }
}
?>

<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Plan Semanal 1");
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

        .weekly-table ul {
            list-style-type: none;
            padding-left: 0;
            margin-bottom: 5px;
        }

        .weekly-table li {
            margin-bottom: 5px;
        }

        .otros-input {
            width: 100%;
            margin-top: 5px;
        }

        .day-cell {
            background-color: #e9ecef;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>

    <div class="container-fluid mt-3 mb-5 px-3">
        <h1 class="text-center mb-4"><?= __("Plan Semanal 1") ?></h1>

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
                                    <?= htmlspecialchars($plan->clase) ?> - <?= htmlspecialchars($plan->tema) ?> - <?= $plan->fecha ?> (ID: <?= $plan->id2 ?>)
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
                        <div class="col-md-6 mb-3">
                            <label><?= __("Maestro(a)") ?>:</label>
                            <input type="text" class="form-control" name="maestro" maxlength="40" required
                                value="<?= htmlspecialchars($teacher->apellidos . ', ' . $teacher->nombre) ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label><?= __("Institución") ?>:</label>
                            <input type="text" class="form-control" name="insti" maxlength="40" required
                                value="<?= htmlspecialchars($school->colegio) ?>" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label><?= __("Clase") ?>:</label>
                            <select class="form-control" name="clase" required>
                                <option value=""><?= __("Seleccione una clase") ?></option>
                                <?php
                                $classes = ['Ciencias', 'Español', 'Estudio Sociales', 'Inglés', 'Historia', 'Matemática', 'Electiva'];
                                foreach ($classes as $class):
                                ?>
                                    <option value="<?= $class ?>" <?= ($weeklyPlan && $weeklyPlan->clase == $class) ? 'selected' : '' ?>>
                                        <?= $class ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
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
                        <div class="col-md-6 mb-3">
                            <label><?= __("Tema") ?>:</label>
                            <input type="text" class="form-control" name="tema" maxlength="40" required
                                value="<?= htmlspecialchars($weeklyPlan->tema ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label><?= __("Fechas") ?>:</label>
                            <input type="date" class="form-control" name="fecha" required
                                value="<?= htmlspecialchars($weeklyPlan->fecha ?? '') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label><?= __("Lección") ?>:</label>
                            <input type="text" class="form-control" name="leccion" maxlength="40" required
                                value="<?= htmlspecialchars($weeklyPlan->leccion ?? '') ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label><?= __("Estándares") ?>:</label>
                            <textarea class="form-control" name="estand" rows="10"><?= htmlspecialchars($weeklyPlan->est ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label><?= __("Expectativas") ?>:</label>
                            <textarea class="form-control" name="expec" rows="10"><?= htmlspecialchars($weeklyPlan->exp ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 2: Objetivos -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <strong><?= __("Objetivos") ?></strong>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label><strong><?= __("Objetivos Generales") ?>:</strong></label>
                        <textarea class="form-control" name="objGen" rows="5"><?= htmlspecialchars($weeklyPlan->obj_gen ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label><strong><?= __("Objetivos Específicos - Verbos de Referencia") ?>:</strong></label>
                    </div>
                    <?php
                    $niveles = ['Anotar', 'Archivar', 'Asociar', 'Bosquejar', 'Calcular', 'Cambiar', 'Comparar', 'Comprender', 'Computar', 'Concluir', 'Contrastar', 'Deducir', 'Definir', 'Determinar', 'Dibujar', 'Diferenciar', 'Discutir', 'Distinguir', 'Enumerar', 'Escribir', 'Especificar', 'Explicar', 'Expresar', 'Formular', 'Identificar', 'Ilustrar', 'Indicar', 'Informar', 'Interpretar', 'Leer', 'Lista', 'Llamar', 'Localizar', 'Manifestar', 'Medir', 'Memorizar', 'Mencionar', 'Nombrar', 'Notificar', 'Opinar', 'Organizar', 'Parafrasear', 'Parear', 'Predecir', 'Preparar', 'Recitar', 'Reconocer', 'Recordar', 'Referir', 'Refrasear', 'Registrar', 'Relacionar', 'Relatar', 'Repasar', 'Repetir', 'Resumir', 'Revelar', 'Revisar', 'Seleccionar', 'Señalar', 'Subrayar', 'Sustituir', 'Traducir'];

                    for ($i = 1; $i <= 4; $i++):
                        $nivelField = "nivel{$i}";
                        $listField = "lst_v{$i}";
                    ?>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label><?= __("Nivel") ?> <?= $i ?>:</label>
                                <select class="form-control" name="nivel<?= $i ?>">
                                    <option value=""><?= __("Seleccione") ?></option>
                                    <?php foreach ($niveles as $nivel): ?>
                                        <option value="<?= $nivel ?>" <?= ($weeklyPlan && $weeklyPlan->$nivelField == $nivel) ? 'selected' : '' ?>>
                                            <?= $nivel ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-9">
                                <label><?= __("Descripción") ?>:</label>
                                <input type="text" class="form-control" name="list<?= $i ?>" maxlength="200"
                                    value="<?= htmlspecialchars($weeklyPlan->$listField ?? '') ?>">
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Sección 3: Actividades Semanales -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <strong><?= __("Actividades Semanales") ?></strong>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="weekly-table table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 12%;"><?= __("Actividades") ?></th>
                                    <th style="width: 18%;"><?= __("Materiales") ?></th>
                                    <th style="width: 17%;"><?= __("Inicio") ?></th>
                                    <th style="width: 17%;"><?= __("Desarrollo") ?></th>
                                    <th style="width: 17%;"><?= __("Cierre") ?></th>
                                    <th style="width: 19%;"><?= __("Assessment") ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $diaSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
                                $actividades = ['Exploración', 'Conceptuación', 'Aplicación'];

                                $materiales = ['hoja de trabajo digital', 'canciones', 'libro de texto', 'cuaderno', 'guía', 'diccionarios', 'carteles', 'franjas', 'tarjetas', 'láminas', 'objetos', 'hoja de trabajo', 'pizarra', 'lección digital', 'libreta', 'películas', 'poemas', 'proyector', 'computadoras', 'impresoras', 'calculadoras', 'manipulativos', 'radio', 'diccionario pictórico', 'juegos digitales'];

                                $inicios = ['actividades de rutina', 'repaso de la clase anterior', 'introducción a la destreza o temas', 'discusión de la asignación', 'dictado', 'discusión del vocabulario', 'discusión de una noticia', 'presentación de ejercicio', 'conversación socializada', 'preguntas abiertas', 'caja sorpresa', 'adivinanza', 'lectura de cuento', 'canción', 'rutinas', 'poemas', 'juego didáctico', 'video', 'torbellino de ideas', 'juegos digitales', 'diagrama CDA(KWL)'];

                                $desarrollos = ['lectura silenciosa', 'lectura en grupo', 'lectura oral', 'discusión oral del tema o idea central', 'discusión oral de los ejercicios', 'trabajo en grupo', 'ejercicios en la pizarra', 'presentación oral', 'resumen de noticias', 'discusión de láminas', 'uso de texto', 'preguntas abiertas', 'juegos digitales', 'hoja de trabajo'];

                                $cierres = ['resumen de la clase', 'discusión del ejercicio', 'discusión de la lectura', 'explicación de la asignación', 'trabajo en libreta', 'repaso para examen', 'instrucción para el siguiente día', 'técnica de avalúo', 'contestación de ejercicio', 'dibujo', 'contestación dudas o preguntas', 'asignación', 'corrección de la tarea', 'preguntas abiertas', 'juegos digitales', 'hoja de trabajo'];

                                $assessments = ['listas de cotejo', 'llena blancos', 'pareo', 'lista focalizadas', 'organizadores gráficos', 'preguntas y respuestas', 'bosquejo incompleto', 'preguntas de alto nivel', 'selección múltiples', 'diarios reflexivos', 'tareas de ejecución', 'trabajos escritos', 'portafolio', 'diagrama de Venn', 'informes escritos', 'informes orales', 'preguntas abiertas', 'ensayos', 'exámenes', 'trabajo de creación tirillas cómicas', 'propuestas de investigación', 'poemas', 'pruebas cortas', 'mapas pictóricos', 'mapas concretos', 'flujograma', 'poema cinquain', 'poema syntu', 'poema concreto', 'rúbrica', 'debate', 'cierto o falso', 'diagrama CDA(KWL)'];

                                for ($i = 1; $i <= 5; $i++):
                                    $actField = "act{$i}";
                                    $matField = "mat{$i}";
                                    $iniField = "ini{$i}";
                                    $desField = "des{$i}";
                                    $cieField = "cie{$i}";
                                    $asseField = "asse{$i}";

                                    $matArray = $weeklyPlan ? $weeklyPlan->getMaterialsArray($matField) : [];
                                    $iniArray = $weeklyPlan ? $weeklyPlan->getMaterialsArray($iniField) : [];
                                    $desArray = $weeklyPlan ? $weeklyPlan->getMaterialsArray($desField) : [];
                                    $cieArray = $weeklyPlan ? $weeklyPlan->getMaterialsArray($cieField) : [];
                                    $asseArray = $weeklyPlan ? $weeklyPlan->getMaterialsArray($asseField) : [];
                                ?>
                                    <tr>
                                        <td class="day-cell">
                                            <strong><?= $diaSemana[$i - 1] ?></strong><br><br>
                                            <label><?= __("Fase") ?>:</label>
                                            <select class="form-control form-control-sm" name="activi<?= $i ?>">
                                                <option value=""><?= __("Seleccione") ?></option>
                                                <?php foreach ($actividades as $actividad): ?>
                                                    <option value="<?= $actividad ?>" <?= ($weeklyPlan && $weeklyPlan->$actField == $actividad) ? 'selected' : '' ?>>
                                                        <?= $actividad ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <ul>
                                                <?php for ($j = 0; $j < 3; $j++): ?>
                                                    <li>
                                                        <select class="form-control form-control-sm" name="material<?= $i ?>-<?= $j ?>">
                                                            <option value=""><?= __("Seleccione") ?></option>
                                                            <?php foreach ($materiales as $material): ?>
                                                                <option value="<?= $material ?>" <?= (isset($matArray[$j]) && $matArray[$j] == $material) ? 'selected' : '' ?>>
                                                                    <?= $material ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </li>
                                                <?php endfor; ?>
                                            </ul>
                                            <label style="font-size: 0.85rem;"><?= __("Otros") ?>:</label>
                                            <input type="text" class="form-control form-control-sm otros-input" name="otros_m<?= $i ?>"
                                                value="<?= htmlspecialchars($weeklyPlan->{"otros_m{$i}"} ?? '') ?>">
                                        </td>
                                        <td>
                                            <ul>
                                                <?php for ($k = 0; $k < 2; $k++): ?>
                                                    <li>
                                                        <select class="form-control form-control-sm" name="inicio<?= $i ?>-<?= $k ?>">
                                                            <option value=""><?= __("Seleccione") ?></option>
                                                            <?php foreach ($inicios as $inicio): ?>
                                                                <option value="<?= $inicio ?>" <?= (isset($iniArray[$k]) && $iniArray[$k] == $inicio) ? 'selected' : '' ?>>
                                                                    <?= $inicio ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </li>
                                                <?php endfor; ?>
                                            </ul>
                                            <label style="font-size: 0.85rem;"><?= __("Otros") ?>:</label>
                                            <input type="text" class="form-control form-control-sm otros-input" name="otros_i<?= $i ?>"
                                                value="<?= htmlspecialchars($weeklyPlan->{"otros_i{$i}"} ?? '') ?>">
                                        </td>
                                        <td>
                                            <ul>
                                                <?php for ($l = 0; $l < 2; $l++): ?>
                                                    <li>
                                                        <select class="form-control form-control-sm" name="desarrollo<?= $i ?>-<?= $l ?>">
                                                            <option value=""><?= __("Seleccione") ?></option>
                                                            <?php foreach ($desarrollos as $desarrollo): ?>
                                                                <option value="<?= $desarrollo ?>" <?= (isset($desArray[$l]) && $desArray[$l] == $desarrollo) ? 'selected' : '' ?>>
                                                                    <?= $desarrollo ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </li>
                                                <?php endfor; ?>
                                            </ul>
                                            <label style="font-size: 0.85rem;"><?= __("Otros") ?>:</label>
                                            <input type="text" class="form-control form-control-sm otros-input" name="otros_d<?= $i ?>"
                                                value="<?= htmlspecialchars($weeklyPlan->{"otros_d{$i}"} ?? '') ?>">
                                        </td>
                                        <td>
                                            <ul>
                                                <?php for ($m = 0; $m < 2; $m++): ?>
                                                    <li>
                                                        <select class="form-control form-control-sm" name="cierre<?= $i ?>-<?= $m ?>">
                                                            <option value=""><?= __("Seleccione") ?></option>
                                                            <?php foreach ($cierres as $cierre): ?>
                                                                <option value="<?= $cierre ?>" <?= (isset($cieArray[$m]) && $cieArray[$m] == $cierre) ? 'selected' : '' ?>>
                                                                    <?= $cierre ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </li>
                                                <?php endfor; ?>
                                            </ul>
                                            <label style="font-size: 0.85rem;"><?= __("Otros") ?>:</label>
                                            <input type="text" class="form-control form-control-sm otros-input" name="otros_c<?= $i ?>"
                                                value="<?= htmlspecialchars($weeklyPlan->{"otros_c{$i}"} ?? '') ?>">
                                        </td>
                                        <td>
                                            <ul>
                                                <?php for ($n = 0; $n < 2; $n++): ?>
                                                    <li>
                                                        <select class="form-control form-control-sm" name="assess<?= $i ?>-<?= $n ?>">
                                                            <option value=""><?= __("Seleccione") ?></option>
                                                            <?php foreach ($assessments as $assessment): ?>
                                                                <option value="<?= $assessment ?>" <?= (isset($asseArray[$n]) && $asseArray[$n] == $assessment) ? 'selected' : '' ?>>
                                                                    <?= $assessment ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </li>
                                                <?php endfor; ?>
                                            </ul>
                                            <label style="font-size: 0.85rem;"><?= __("Otros") ?>:</label>
                                            <input type="text" class="form-control form-control-sm otros-input" name="otros_a<?= $i ?>"
                                                value="<?= htmlspecialchars($weeklyPlan->{"otros_a{$i}"} ?? '') ?>">
                                        </td>
                                    </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sección 4: Comentarios -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <strong><?= __("Comentarios") ?></strong>
                </div>
                <div class="card-body">
                    <textarea class="form-control" name="coment" rows="5"><?= htmlspecialchars($weeklyPlan->coment ?? '') ?></textarea>
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
                window.location.href = '<?= Route::url('/regiweb/options/weeklyplans/plan1/index.php?new=1') ?>';
            });

            // Buscar plan
            searchPlanBtn.click(function() {
                const planId = planSelector.val();
                if (planId) {
                    window.location.href = '<?= Route::url('/regiweb/options/weeklyplans/plan1/index.php') ?>?plan=' + planId;
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
                            url: '<?= Route::url('/regiweb/options/weeklyplans/plan1/includes/index.php') ?>',
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
                                        window.location.href = '<?= Route::url('/regiweb/options/weeklyplans/plan1/index.php') ?>';
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
                    window.open('<?= Route::url('/regiweb/options/weeklyplans/plan1/planes_inf.php') ?>?plan=' + planId, '_blank');
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
                    url: '<?= Route::url('/regiweb/options/weeklyplans/plan1/includes/index.php') ?>',
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
                                window.location.href = '<?= Route::url('/regiweb/options/weeklyplans/plan1/index.php') ?>?plan=' + response.id;
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