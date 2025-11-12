<?php
require_once '../../../../app.php';

use App\Models\Admin;
use App\Models\Teacher;
use Classes\Route;
use Classes\Session;

Session::is_logged();

$teacher = Teacher::find(Session::id());
$school = Admin::primaryAdmin();
$year = $school->year;

// Obtener todos los planes de trabajo del maestro
$workPlans = $teacher->workPlans()->orderBy('fecha', 'desc')->get();

// Obtener el ID del plan seleccionado de la URL si existe
$selectedPlanId = $_GET['plan'] ?? null;
?>

<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Plan de trabajo 1");
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>

    <div class="container-fluid mt-3 mb-5 px-3">
        <h1 class="text-center mb-4"><?= __("Plan de trabajo 1") ?></h1>

        <!-- Controles superiores -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary btn-block" id="btnNew">
                            <?= __("Nuevo") ?>
                        </button>
                    </div>
                    <div class="col-md-5">
                        <label><?= __("Seleccionar Plan") ?>:</label>
                        <select class="form-control" id="workPlanSelect">
                            <option value=""><?= __("Seleccione un plan") ?></option>
                            <?php foreach ($workPlans as $plan): ?>
                                <option value="<?= $plan->id2 ?>" <?= $selectedPlanId == $plan->id2 ? 'selected' : '' ?>>
                                    <?= $plan->mes ?> <?= $plan->dia1 ?> a <?= $plan->dia2 ?> - <?= $plan->grado ?> - <?= $plan->plan ?> - <?= $plan->asignatura ?> - <?= $plan->id2 ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-info btn-block" id="btnSearch">
                            <?= __("Buscar") ?>
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-block" id="btnDelete">
                            <?= __("Borrar") ?>
                        </button>
                    </div>
                    <div class="col-md-1">
                        <a href="#" id="btnPrintTop" class="btn btn-secondary btn-block d-none" target="_blank" title="<?= __("Imprimir") ?>">
                            <i class="fas fa-print"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario del plan de trabajo -->
        <form id="workPlanForm" class="card d-none">
            <div class="card-body">
                <input type="hidden" id="workPlanId" name="workPlanId">
                <input type="hidden" id="isNew" name="isNew" value="0">

                <!-- Plan de -->
                <div class="bg-light p-3 mb-3 rounded">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="font-weight-bold"><?= __("Plan de") ?>:</label>
                            <input type="text" class="form-control" name="plan" id="plan" maxlength="40" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="font-weight-bold"><?= __("Estándares de Contenido") ?>:</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="estandares" id="estandares" value="Si">
                                <label class="form-check-label" for="estandares">Si</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información básica -->
                <div class="bg-light p-3 mb-3 rounded">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label><?= __("Grado") ?>:</label>
                            <input type="text" class="form-control" name="grado" id="grado" maxlength="5" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="font-weight-bold"><?= __("Asignatura Específica Temas") ?></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label><?= __("Asignatura Específica") ?>:</label>
                            <input type="text" class="form-control" name="asignatura" id="asignatura" maxlength="50" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <input type="text" class="form-control" name="tema1" id="tema1" maxlength="75" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="form-inline">
                                <label class="mr-2"><?= __("Fecha/Semana") ?>: <?= __("mes") ?>:</label>
                                <input type="text" class="form-control mr-2" style="width: 120px;" name="mes" id="mes" maxlength="12" required>
                                <label class="mr-2"><?= __("día") ?></label>
                                <input type="text" class="form-control mr-2" style="width: 50px;" name="dia1" id="dia1" maxlength="2" required>
                                <label class="mr-2"><?= __("al día") ?></label>
                                <input type="text" class="form-control" style="width: 50px;" name="dia2" id="dia2" maxlength="2" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <input type="text" class="form-control" name="tema2" id="tema2" maxlength="75">
                        </div>
                    </div>
                </div>

                <!-- Enfoque -->
                <div class="bg-secondary text-white text-center p-2 mb-3 rounded">
                    <strong>1. <?= __("Enfocar") ?> &nbsp; 2. <?= __("Explorar") ?> &nbsp; 3. <?= __("Reflexionar") ?> &nbsp; 4. <?= __("Aplicación") ?></strong>
                </div>

                <!-- Estándares y Espectativas -->
                <div class="bg-light p-3 mb-3 rounded">
                    <label><?= __("Estándares y Espectativas") ?>:</label>
                    <input type="text" class="form-control" name="espectativas" id="espectativas" maxlength="120">
                </div>

                <!-- Nivel de Profundidad -->
                <div class="bg-light p-3 mb-3 rounded">
                    <label class="d-block mb-2"><?= __("Nivel de Profundidad de Conocimiento") ?>:</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="np1" id="np1" value="Si">
                        <label class="form-check-label" for="np1"><?= __("Memorístico") ?></label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="np2" id="np2" value="Si">
                        <label class="form-check-label" for="np2"><?= __("Procesamiento") ?></label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="np3" id="np3" value="Si">
                        <label class="form-check-label" for="np3"><?= __("Estratégico") ?></label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="np4" id="np4" value="Si">
                        <label class="form-check-label" for="np4"><?= __("Extendido") ?></label>
                    </div>
                </div>

                <!-- EduSystem y Tema -->
                <div class="bg-secondary text-white text-center p-2 mb-3 rounded">
                    <div class="row">
                        <div class="col-md-6"><strong>EduSystem</strong></div>
                        <div class="col-md-6"><strong><?= __("Tema") ?></strong></div>
                    </div>
                </div>

                <div class="bg-light p-3 mb-3 rounded">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label><?= __("Unidad") ?>:</label>
                            <input type="text" class="form-control" name="unidad" id="unidad" maxlength="30" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="tema" id="tema" maxlength="80">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label><?= __("Lección") ?>:</label>
                            <input type="text" class="form-control" name="leccion" id="leccion" maxlength="30" required>
                        </div>
                        <div class="col-md-6">
                            <label><?= __("Pre-requisito") ?>:</label>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label><?= __("Código") ?>:</label>
                            <input type="text" class="form-control" name="codigo" id="codigo" maxlength="30" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="pre1" id="pre1" maxlength="80">
                        </div>
                    </div>
                </div>

                <!-- Objetivos -->
                <div class="bg-secondary text-white text-center p-2 mb-3 rounded">
                    <strong><?= __("Objetivo") ?></strong>
                </div>

                <div class="bg-light p-3 mb-3 rounded">
                    <div class="mb-2">
                        <label><?= __("Conceptual") ?>:</label>
                        <input type="text" class="form-control" name="obj1" id="obj1" maxlength="109">
                    </div>
                    <div class="mb-2">
                        <label><?= __("Procedimental") ?>:</label>
                        <input type="text" class="form-control" name="obj2" id="obj2" maxlength="109">
                    </div>
                    <div class="mb-2">
                        <label><?= __("Actitudinal") ?>:</label>
                        <input type="text" class="form-control" name="obj3" id="obj3" maxlength="109">
                    </div>
                    <div class="mb-2">
                        <label><?= __("Integración: Explique / Exponer") ?></label>
                        <input type="text" class="form-control" name="integracion" id="integracion" maxlength="120">
                    </div>
                </div>

                <!-- Secuencia de actividades y Evaluación -->
                <div class="bg-secondary text-white text-center p-2 mb-3 rounded">
                    <div class="row">
                        <div class="col-md-6"><strong><?= __("SECUENCIA DE ACTIVIDADES") ?></strong></div>
                        <div class="col-md-6"><strong><?= __("EVALUACION INFORMATIVA") ?></strong></div>
                    </div>
                </div>

                <div class="bg-light p-3 mb-3 rounded">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="act1" id="act1" value="Si">
                                    <label class="form-check-label font-weight-bold" for="act1"><?= __("Actividad") ?>:</label>
                                </div>
                                <div class="form-check form-check-inline ml-3">
                                    <input class="form-check-input" type="checkbox" name="act2" id="act2" value="Si">
                                    <label class="form-check-label" for="act2"><?= __("Exploración") ?></label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="act3" id="act3" value="Si">
                                    <label class="form-check-label" for="act3"><?= __("Conceptualización") ?></label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="act4" id="act4" value="Si">
                                    <label class="form-check-label" for="act4"><?= __("Aplicación") ?></label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <strong>1. <?= __("Inicio") ?>:</strong>
                                <input class="form-check-input ml-2" type="checkbox" name="ini1" id="ini1" value="Si">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="ini2" id="ini2" value="Si">
                                    <label class="form-check-label" for="ini2"><?= __("Repaso clase anterior") ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="ini3" id="ini3" value="Si">
                                    <label class="form-check-label" for="ini3"><?= __("Exploración conocimientos previos") ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="ini4" id="ini4" value="Si">
                                    <label class="form-check-label" for="ini4"><?= __("Presentación de objetivos") ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="ini5" id="ini5" value="Si">
                                    <label class="form-check-label" for="ini5"><?= __("Motivación o inicio") ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="ini6" id="ini6" value="Si">
                                    <label class="form-check-label" for="ini6"><?= __("Presentación vocabulario") ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="ini7" id="ini7" value="Si">
                                    <label class="form-check-label" for="ini7"><?= __("Presentación del tema") ?></label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <strong>2. <?= __("Desarrollo") ?>:</strong>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="des1" id="des1" value="Si">
                                    <label class="form-check-label" for="des1"><?= __("Estrategia ECA") ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="des2" id="des2" value="Si">
                                    <label class="form-check-label" for="des2"><?= __("Trabajo cooperativo") ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="des3" id="des3" value="Si">
                                    <label class="form-check-label" for="des3"><?= __("Laboratorio") ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="des4" id="des4" value="Si">
                                    <label class="form-check-label" for="des4"><?= __("Discusión socializada") ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="des5" id="des5" value="Si">
                                    <label class="form-check-label" for="des5"><?= __("Centros de aprendizaje") ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="des6" id="des6" value="Si">
                                    <label class="form-check-label" for="des6"><?= __("Conferencia") ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="des7" id="des7" value="Si">
                                    <label class="form-check-label" for="des7"><?= __("Otra") ?></label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <strong>3. <?= __("Cierre") ?>:</strong>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="cie1" id="cie1" value="Si">
                                    <label class="form-check-label" for="cie1"><?= __("Repaso clase anterior") ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="cie2" id="cie2" value="Si">
                                    <label class="form-check-label" for="cie2"><?= __("Trabajo cooperativo") ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="cie3" id="cie3" value="Si">
                                    <label class="form-check-label" for="cie3"><?= __("Discusión socializada") ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="cie4" id="cie4" value="Si">
                                    <label class="form-check-label" for="cie4"><?= __("Centros de aprendizaje") ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="cie5" id="cie5" value="Si">
                                    <label class="form-check-label" for="cie5"><?= __("Otra") ?></label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>4. <?= __("Aplicaciones") ?>:</strong>
                                <div class="form-check form-check-inline ml-2">
                                    <input class="form-check-input" type="checkbox" name="eva2" id="eva2" value="Si">
                                    <label class="form-check-label" for="eva2"><?= __("Texto") ?></label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="eva3" id="eva3" value="Si">
                                    <label class="form-check-label" for="eva3"><?= __("Cuaderno") ?></label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="eva4" id="eva4" value="Si">
                                    <label class="form-check-label" for="eva4"><?= __("Fichas") ?></label>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="tab1" id="tab1" value="Si">
                                    <label class="form-check-label" for="tab1"><?= __("Pruebas cortas o Pruebas") ?></label>
                                </div>
                                <input type="text" class="form-control form-control-sm" name="tab2" id="tab2" maxlength="80">
                            </div>

                            <div class="mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="tab3" id="tab3" value="Si">
                                    <label class="form-check-label" for="tab3"><?= __("Proyectos o tareas de desempeño") ?></label>
                                </div>
                                <input type="text" class="form-control form-control-sm" name="tab4" id="tab4" maxlength="80">
                            </div>

                            <div class="mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="tab5" id="tab5" value="Si">
                                    <label class="form-check-label" for="tab5"><?= __("Tareas") ?></label>
                                </div>
                                <input type="text" class="form-control form-control-sm" name="tab6" id="tab6" maxlength="80">
                            </div>

                            <div class="mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="tab7" id="tab7" value="Si">
                                    <label class="form-check-label" for="tab7"><?= __("Portafolio") ?></label>
                                </div>
                                <input type="text" class="form-control form-control-sm" name="tab8" id="tab8" maxlength="80">
                            </div>

                            <div class="mb-3">
                                <strong><?= __("Selección") ?>:</strong>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sel1" id="sel1" value="Si">
                                    <label class="form-check-label" for="sel1"><?= __("Llenar blancos") ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sel2" id="sel2" value="Si">
                                    <label class="form-check-label" for="sel2"><?= __("Pareo") ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sel3" id="sel3" value="Si">
                                    <label class="form-check-label" for="sel3"><?= __("Cierto falso") ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sel4" id="sel4" value="Si">
                                    <label class="form-check-label" for="sel4"><?= __("Informes orales o escritos") ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sel5" id="sel5" value="Si">
                                    <label class="form-check-label" for="sel5"><?= __("Otro") ?></label>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="pro1" id="pro1" value="Si">
                                    <label class="form-check-label" for="pro1"><?= __("Prontuarios") ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="pro2" id="pro2" value="Si">
                                    <label class="form-check-label" for="pro2"><?= __("Diarios reflexivos") ?></label>
                                </div>
                                <input type="text" class="form-control form-control-sm mt-2" name="otro" id="otro" maxlength="80" placeholder="<?= __("Otro") ?>">
                            </div>

                            <div class="mb-2">
                                <label><?= __("Autoevaluación") ?>:</label>
                                <input type="text" class="form-control form-control-sm" name="autoeva" id="autoeva" maxlength="80">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acomodos razonables -->
                <div class="bg-light p-3 mb-3 rounded">
                    <label class="font-weight-bold"><?= __("Acomodos razonables") ?>:</label>
                    <div class="row">
                        <?php for ($i = 1; $i <= 8; $i++): ?>
                            <div class="col-md-6 mb-2">
                                <input type="text" class="form-control form-control-sm" name="as<?= $i ?>" id="as<?= $i ?>" maxlength="80">
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="text-center mt-4">
                    <button type="submit" id="btnSave" class="btn btn-success btn-lg">
                        <i class="fas fa-save"></i> <span id="btnSaveText"><?= __("Guardar") ?></span>
                    </button>
                    <a href="#" id="btnPrint" class="btn btn-info btn-lg ml-2 d-none" target="_blank">
                        <i class="fas fa-print"></i> <?= __("Imprimir") ?>
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
        $(document).ready(function() {
            const workPlanForm = $('#workPlanForm');
            const workPlanSelect = $('#workPlanSelect');
            const btnNew = $('#btnNew');
            const btnSearch = $('#btnSearch');
            const btnDelete = $('#btnDelete');
            const btnPrint = $('#btnPrint');
            const btnSaveText = $('#btnSaveText');
            const isNewInput = $('#isNew');
            const workPlanIdInput = $('#workPlanId');

            // Si hay un plan seleccionado en la URL, cargarlo automáticamente
            <?php if ($selectedPlanId): ?>
                // Cargar automáticamente el plan seleccionado
                setTimeout(function() {
                    loadWorkPlan(<?= $selectedPlanId ?>);
                }, 100);
            <?php endif; ?>

            // Nuevo plan
            btnNew.click(function() {
                clearForm();
                isNewInput.val('1');
                workPlanForm.removeClass('d-none');
                btnSaveText.text('<?= __("Crear") ?>');
                btnPrint.addClass('d-none');
                $('#btnPrintTop').addClass('d-none');
            });

            // Buscar plan
            btnSearch.click(function() {
                const workPlanId = workPlanSelect.val();
                if (!workPlanId) {
                    Swal.fire({
                        icon: 'warning',
                        title: '<?= __("Seleccione un plan de trabajo") ?>',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    return;
                }

                loadWorkPlan(workPlanId);
            });

            // Función para cargar un plan de trabajo
            function loadWorkPlan(workPlanId) {
                $.ajax({
                    url: '<?= Route::url('/regiweb/options/workplans/plan1/includes/index.php') ?>',
                    type: 'POST',
                    data: {
                        getWorkPlan: workPlanId
                    },
                    dataType: 'json',
                    success: function(data) {
                        fillForm(data);
                        isNewInput.val('0');
                        workPlanForm.removeClass('d-none');
                        btnSaveText.text('<?= __("Guardar") ?>');
                        btnPrint.removeClass('d-none');
                        btnPrint.attr('href', 'planes_inf.php?plan=' + workPlanId);
                        $('#btnPrintTop').removeClass('d-none').attr('href', 'planes_inf.php?plan=' + workPlanId);
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: '<?= __("Error al cargar el plan de trabajo") ?>'
                        });
                    }
                });
            }

            // Borrar plan
            btnDelete.click(function() {
                const workPlanId = workPlanSelect.val();
                if (!workPlanId) {
                    Swal.fire({
                        icon: 'warning',
                        title: '<?= __("Seleccione un plan de trabajo") ?>',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    return;
                }

                Swal.fire({
                    title: '<?= __("¿Está seguro que desea eliminar el plan de trabajo?") ?>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '<?= __("Borrar") ?>',
                    cancelButtonText: '<?= __("Cancelar") ?>'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '<?= Route::url('/regiweb/options/workplans/plan1/includes/index.php') ?>',
                            type: 'POST',
                            data: {
                                deleteWorkPlan: workPlanId
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '<?= __("Eliminado") ?>',
                                        text: '<?= __("Plan de trabajo eliminado correctamente") ?>'
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message || '<?= __("Error al eliminar") ?>'
                                    });
                                }
                            }
                        });
                    }
                });
            });

            // Guardar/Crear plan
            workPlanForm.submit(function(e) {
                e.preventDefault();
                const formData = $(this).serializeArray();
                const isNew = isNewInput.val() === '1';

                // Convertir checkboxes a valores
                const checkboxes = ['estandares', 'np1', 'np2', 'np3', 'np4', 'act1', 'act2', 'act3', 'act4',
                    'ini1', 'ini2', 'ini3', 'ini4', 'ini5', 'ini6', 'ini7',
                    'des1', 'des2', 'des3', 'des4', 'des5', 'des6', 'des7',
                    'cie1', 'cie2', 'cie3', 'cie4', 'cie5',
                    'eva1', 'eva2', 'eva3', 'eva4',
                    'tab1', 'tab3', 'tab5', 'tab7',
                    'sel1', 'sel2', 'sel3', 'sel4', 'sel5',
                    'pro1', 'pro2'
                ];

                let data = {};
                formData.forEach(item => {
                    data[item.name] = item.value;
                });

                checkboxes.forEach(name => {
                    if (!data[name]) {
                        data[name] = $('#' + name).is(':checked') ? 'Si' : 'No';
                    }
                });

                if (isNew) {
                    data.createWorkPlan = true;
                } else {
                    data.updateWorkPlan = true;
                    data.workPlanId = workPlanIdInput.val();
                }

                $.ajax({
                    url: '<?= Route::url('/regiweb/options/workplans/plan1/includes/index.php') ?>',
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: isNew ? '<?= __("Crear") ?>' : '<?= __("Guardar") ?>',
                                text: '<?= __("Plan de trabajo") ?> ' + (isNew ? '<?= __("creado") ?>' : '<?= __("guardado") ?>') + ' <?= __("correctamente") ?>'
                            }).then(() => {
                                // Redirigir con el ID del plan en la URL
                                const planId = response.id || workPlanIdInput.val();
                                window.location.href = window.location.pathname + '?plan=' + planId;
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
                            text: '<?= __("Error al procesar la solicitud") ?>'
                        });
                    }
                });
            });

            function fillForm(data) {
                workPlanIdInput.val(data.id2);

                // Llenar campos de texto
                Object.keys(data).forEach(key => {
                    const input = $('#' + key);
                    if (input.length && input.attr('type') !== 'checkbox') {
                        input.val(data[key]);
                    }
                });

                // Llenar checkboxes
                const checkboxFields = ['estandares', 'np1', 'np2', 'np3', 'np4', 'act1', 'act2', 'act3', 'act4',
                    'ini1', 'ini2', 'ini3', 'ini4', 'ini5', 'ini6', 'ini7',
                    'des1', 'des2', 'des3', 'des4', 'des5', 'des6', 'des7',
                    'cie1', 'cie2', 'cie3', 'cie4', 'cie5',
                    'eva1', 'eva2', 'eva3', 'eva4',
                    'tab1', 'tab3', 'tab5', 'tab7',
                    'sel1', 'sel2', 'sel3', 'sel4', 'sel5',
                    'pro1', 'pro2'
                ];

                checkboxFields.forEach(field => {
                    $('#' + field).prop('checked', data[field] === 'Si' || data[field] === '1Si');
                });
            }

            function clearForm() {
                workPlanForm[0].reset();
                workPlanIdInput.val('');
                $('input[type="checkbox"]').prop('checked', false);
            }
        });
    </script>
</body>

</html>