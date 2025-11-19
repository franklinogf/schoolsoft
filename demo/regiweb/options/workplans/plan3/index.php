<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\Teacher;
use Classes\Session;
use Classes\Route;


Session::is_logged();

$teacher = Teacher::find(Session::id());
$workPlans = $teacher->workPlans()->orderBy('fecha', 'desc')->get();
$selectedPlanId = $_GET['plan'] ?? null;
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Plan de Trabajo 3");
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>


    <div class="container-fluid mt-4">
        <h2 class="text-center mb-4"><?= __("Plan de Trabajo 3") ?></h2>

        <!-- Controles superiores -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary btn-block" id="btnNuevo">
                            <?= __("Nuevo") ?>
                        </button>
                    </div>
                    <div class="col-md-5">
                        <label><?= __("Seleccionar Plan") ?>:</label>
                        <select class="form-control" id="selectPlan">
                            <option value=""><?= __("Seleccione un plan") ?></option>
                            <?php foreach ($workPlans as $plan): ?>
                                <option value="<?= $plan->id2 ?>" <?= $selectedPlanId == $plan->id2 ? 'selected' : '' ?>>
                                    <?= $plan->mes ?> <?= $plan->dia1 ?> a <?= $plan->dia2 ?> - <?= $plan->grado ?> - <?= $plan->plan ?> - <?= $plan->asignatura ?> - <?= $plan->id2 ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-info btn-block" id="btnBuscar">
                            <?= __("Buscar") ?>
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-block" id="btnBorrar">
                            <?= __("Borrar") ?>
                        </button>
                    </div>
                    <div class="col-md-1">
                        <a href="#" class="btn btn-secondary btn-block d-none" id="btnPrintTop" target="_blank" title="<?= __("Imprimir") ?>">
                            <i class="fas fa-print"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <form id="workPlanForm" class="d-none">
            <input type="hidden" id="planId" name="planId">

            <!-- Sección 1: Información básica -->
            <div class="card mb-3">
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong><?= __("PLAN DE") ?>:</strong></label>
                                <input type="text" class="form-control" name="plan" maxlength="40" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong><?= __("Estándares de Contenido") ?>:</strong></label>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="estandares" value="Si" id="estandares">
                                    <label class="form-check-label" for="estandares">Si</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?= __("Grado") ?>:</label>
                                <input type="text" class="form-control" name="grado" maxlength="5" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?= __("Asignatura Específica") ?>:</label>
                                <input type="text" class="form-control" name="asignatura" maxlength="50" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?= __("Fecha/Semana") ?>:</label>
                                <div class="form-inline">
                                    <label class="mr-2"><?= __("mes") ?>:</label>
                                    <input type="text" class="form-control mr-2" name="mes" maxlength="12" size="10" required>
                                    <label class="mr-2"><?= __("día") ?></label>
                                    <input type="text" class="form-control mr-2" name="dia1" maxlength="2" size="2" required>
                                    <label class="mr-2"><?= __("al día") ?></label>
                                    <input type="text" class="form-control" name="dia2" maxlength="2" size="2" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 2: Enfoque y Estándares -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <strong>1. <?= __("Enfocar") ?> &nbsp;&nbsp;&nbsp; 2. <?= __("Explorar") ?> &nbsp;&nbsp;&nbsp; 3. <?= __("Reflexionar") ?> &nbsp;&nbsp;&nbsp; 4. <?= __("Aplicación") ?></strong>
                </div>
                <div class="card-body bg-light">
                    <div class="form-group">
                        <label><?= __("Estándares y Espectativas") ?>:</label>
                        <textarea class="form-control" name="espectativas" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label><?= __("Nivel de Profundidad de Conocimiento") ?>:</label>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" name="np1" value="Si" id="np1">
                            <label class="form-check-label" for="np1"><?= __("Memorístico") ?></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" name="np2" value="Si" id="np2">
                            <label class="form-check-label" for="np2"><?= __("Procesamiento") ?></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" name="np3" value="Si" id="np3">
                            <label class="form-check-label" for="np3"><?= __("Estratégico") ?></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" name="np4" value="Si" id="np4">
                            <label class="form-check-label" for="np4"><?= __("Extendido") ?></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" name="np5" value="Si" id="np5">
                            <label class="form-check-label" for="np5">Nivel 5</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 3: Tema y Pre-requisito -->
            <div class="card mb-3">
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong><?= __("Tema") ?>:</strong></label>
                                <input type="text" class="form-control" name="tema" maxlength="80">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong><?= __("Pre-requisito") ?>:</strong></label>
                                <input type="text" class="form-control" name="pre1" maxlength="80">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 4: Objetivos -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <strong><?= __("Objetivo") ?></strong>
                </div>
                <div class="card-body bg-light">
                    <div class="form-group">
                        <label><strong><?= __("Conceptual") ?>:</strong></label>
                        <input type="text" class="form-control mb-2" name="obj1" maxlength="109">
                        <input type="text" class="form-control mb-2" name="ent1" maxlength="109">
                        <input type="text" class="form-control mb-2" name="ent2" maxlength="109">
                        <input type="text" class="form-control mb-2" name="ent3" maxlength="109">
                        <input type="text" class="form-control mb-3" name="ent4" maxlength="109">
                    </div>

                    <div class="form-group">
                        <label><strong><?= __("Procedimental") ?>:</strong></label>
                        <input type="text" class="form-control mb-2" name="obj2" maxlength="109">
                        <input type="text" class="form-control mb-2" name="ent5" maxlength="109">
                        <input type="text" class="form-control mb-2" name="ent6" maxlength="109">
                        <input type="text" class="form-control mb-2" name="ent7" maxlength="109">
                        <input type="text" class="form-control mb-3" name="ent8" maxlength="109">
                    </div>

                    <div class="form-group">
                        <label><strong><?= __("Actitudinal") ?>:</strong></label>
                        <input type="text" class="form-control mb-2" name="obj3" maxlength="109">
                        <input type="text" class="form-control mb-2" name="ent9" maxlength="109">
                        <input type="text" class="form-control mb-2" name="ent10" maxlength="109">
                        <input type="text" class="form-control mb-2" name="ent11" maxlength="109">
                        <input type="text" class="form-control mb-3" name="ent12" maxlength="109">
                    </div>

                    <div class="form-group">
                        <label><strong><?= __("Integración") ?>: <?= __("Explique / Exponer") ?></strong></label>
                        <input type="text" class="form-control" name="integracion" maxlength="120">
                    </div>
                </div>
            </div>

            <!-- Sección 5: Secuencia de actividades y Evaluación -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <!-- Columna izquierda: Secuencia de actividades -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <strong><?= __("SECUENCIA DE ACTIVIDADES") ?></strong>
                                </div>
                                <div class="card-body bg-light">
                                    <!-- Actividad -->
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="act1" value="Si" id="act1">
                                            <label class="form-check-label" for="act1"><?= __("Actividad") ?></label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" class="form-check-input" name="act2" value="Si" id="act2">
                                            <label class="form-check-label" for="act2"><?= __("Exploración") ?></label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" class="form-check-input" name="act3" value="Si" id="act3">
                                            <label class="form-check-label" for="act3"><?= __("Conceptualización") ?></label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" class="form-check-input" name="act4" value="Si" id="act4">
                                            <label class="form-check-label" for="act4"><?= __("Aplicación") ?></label>
                                        </div>
                                    </div>

                                    <!-- 1. Inicio -->
                                    <div class="form-group">
                                        <label><strong>1. <?= __("Inicio") ?>:</strong></label>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="ini1" value="Si" id="ini1">
                                            <label class="form-check-label" for="ini1"><?= __("Inicio") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="ini2" value="Si" id="ini2">
                                            <label class="form-check-label" for="ini2"><?= __("Repaso clase anterior") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="ini3" value="Si" id="ini3">
                                            <label class="form-check-label" for="ini3"><?= __("Corrección de asignación") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="ini4" value="Si" id="ini4">
                                            <label class="form-check-label" for="ini4"><?= __("Refuerzo") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="ini5" value="Si" id="ini5">
                                            <label class="form-check-label" for="ini5"><?= __("Introducción al tema") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="ini6" value="Si" id="ini6">
                                            <label class="form-check-label" for="ini6"><?= __("Torbellino de ideas") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="ini7" value="Si" id="ini7">
                                            <label class="form-check-label" for="ini7"><?= __("Uso de manipulativo") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="ot1" value="Si" id="ot1">
                                            <label class="form-check-label" for="ot1"><?= __("Otros") ?></label>
                                        </div>
                                        <input type="text" class="form-control ml-4 mt-1" name="otr1" maxlength="100">
                                        <input type="text" class="form-control ml-4 mt-1" name="otr2" maxlength="100">
                                    </div>

                                    <!-- 2. Desarrollo -->
                                    <div class="form-group">
                                        <label><strong>2. <?= __("Desarrollo") ?>:</strong></label>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="des1" value="Si" id="des1">
                                            <label class="form-check-label" for="des1"><?= __("Desarrollo") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="des2" value="Si" id="des2">
                                            <label class="form-check-label" for="des2"><?= __("Presentación de la temática") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="des3" value="Si" id="des3">
                                            <label class="form-check-label" for="des3"><?= __("Definición y presentación de los conceptos") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="des4" value="Si" id="des4">
                                            <label class="form-check-label" for="des4"><?= __("Listados o requisitos de un vocabulario") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="des5" value="Si" id="des5">
                                            <label class="form-check-label" for="des5"><?= __("Listados de propiedades, características, detalles, etc.") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="des6" value="Si" id="des6">
                                            <label class="form-check-label" for="des6"><?= __("Ejemplicación sobre los procesos") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="des7" value="Si" id="des7">
                                            <label class="form-check-label" for="des7"><?= __("Uso de la tecnología") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="ot2" value="Si" id="ot2">
                                            <label class="form-check-label" for="ot2"><?= __("Otros") ?></label>
                                        </div>
                                        <input type="text" class="form-control ml-4 mt-1" name="otr3" maxlength="100">
                                        <input type="text" class="form-control ml-4 mt-1" name="otr4" maxlength="100">
                                    </div>

                                    <!-- 3. Cierre -->
                                    <div class="form-group">
                                        <label><strong>3. <?= __("Cierre") ?>:</strong></label>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="cie1" value="Si" id="cie1">
                                            <label class="form-check-label" for="cie1"><?= __("Cierre") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="cie2" value="Si" id="cie2">
                                            <label class="form-check-label" for="cie2"><?= __("Resumir materias discutido") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="cie3" value="Si" id="cie3">
                                            <label class="form-check-label" for="cie3"><?= __("Aclarar dudas") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="cie4" value="Si" id="cie4">
                                            <label class="form-check-label" for="cie4"><?= __("Llegar a conclusiones") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="cie5" value="Si" id="cie5">
                                            <label class="form-check-label" for="cie5"><?= __("Discusión del trabajo asignados") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="ot3" value="Si" id="ot3">
                                            <label class="form-check-label" for="ot3"><?= __("Otros") ?></label>
                                        </div>
                                        <input type="text" class="form-control ml-4 mt-1" name="otr5" maxlength="100">
                                        <input type="text" class="form-control ml-4 mt-1" name="otr6" maxlength="100">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna derecha: Evaluación Informativa -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <strong><?= __("EVALUACION INFORMATIVA") ?></strong>
                                </div>
                                <div class="card-body bg-light">
                                    <!-- 4. Aplicaciones -->
                                    <div class="form-group">
                                        <label><strong>4. <?= __("Aplicaciones") ?>:</strong></label>
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" class="form-check-input" name="eva1" value="Si" id="eva1">
                                            <label class="form-check-label" for="eva1"><?= __("Aplicaciones") ?></label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" class="form-check-input" name="eva2" value="Si" id="eva2">
                                            <label class="form-check-label" for="eva2"><?= __("Texto") ?></label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" class="form-check-input" name="eva3" value="Si" id="eva3">
                                            <label class="form-check-label" for="eva3"><?= __("Cuaderno") ?></label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" class="form-check-input" name="eva4" value="Si" id="eva4">
                                            <label class="form-check-label" for="eva4"><?= __("Fichas") ?></label>
                                        </div>
                                    </div>

                                    <!-- Tabla de prácticas -->
                                    <div class="form-group">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th><?= __("Pág.") ?></th>
                                                    <th><?= __("Ejercicios") ?></th>
                                                    <th><?= __("Impares") ?></th>
                                                    <th><?= __("Pares") ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><?= __("Prácticas") ?></td>
                                                    <td><input type="text" class="form-control form-control-sm" name="tab1" maxlength="10"></td>
                                                    <td><input type="text" class="form-control form-control-sm" name="tab3"></td>
                                                    <td><input type="text" class="form-control form-control-sm" name="tab5" maxlength="10"></td>
                                                    <td><input type="text" class="form-control form-control-sm" name="tab7" maxlength="10"></td>
                                                </tr>
                                                <tr>
                                                    <td><?= __("Asignación") ?></td>
                                                    <td><input type="text" class="form-control form-control-sm" name="tab2" maxlength="10"></td>
                                                    <td><input type="text" class="form-control form-control-sm" name="tab4"></td>
                                                    <td><input type="text" class="form-control form-control-sm" name="tab6" maxlength="10"></td>
                                                    <td><input type="text" class="form-control form-control-sm" name="tab8" maxlength="10"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <small class="form-text text-muted text-right">* <?= __("seleccionados") ?></small>
                                    </div>

                                    <!-- Selección -->
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="sel1" value="Si" id="sel1">
                                            <label class="form-check-label" for="sel1"><?= __("Quiz") ?></label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="sel2" value="Si" id="sel2">
                                            <label class="form-check-label" for="sel2"><?= __("Examen") ?></label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="sel3" value="Si" id="sel3">
                                            <label class="form-check-label" for="sel3"><?= __("Informes") ?></label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="sel4" value="Si" id="sel4">
                                            <label class="form-check-label" for="sel4">
                                                <?= __("Proyecto del día") ?>
                                                <input type="text" class="form-control form-control-sm d-inline-block" name="pro1" maxlength="3" size="3" style="width: 50px;">
                                                <?= __("al") ?>
                                                <input type="text" class="form-control form-control-sm d-inline-block" name="pro2" maxlength="3" size="3" style="width: 50px;">
                                                <?= __("del mes") ?>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="sel5" value="Si" id="sel5">
                                            <label class="form-check-label" for="sel5"><?= __("Otros") ?></label>
                                        </div>
                                        <input type="text" class="form-control mt-1" name="otro" maxlength="60">
                                    </div>

                                    <!-- 5. Assessment -->
                                    <div class="form-group">
                                        <label><strong>5. <?= __("Assessment") ?>:</strong></label>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="as1" value="Si" id="as1">
                                            <label class="form-check-label" for="as1"><?= __("Assessment") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="as2" value="Si" id="as2">
                                            <label class="form-check-label" for="as2"><?= __("Lista de cotejo") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="as3" value="Si" id="as3">
                                            <label class="form-check-label" for="as3"><?= __("Tirilla cómica") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="as4" value="Si" id="as4">
                                            <label class="form-check-label" for="as4"><?= __("Diario reflexivo") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="as5" value="Si" id="as5">
                                            <label class="form-check-label" for="as5"><?= __("Mapa de concepto") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="as6" value="Si" id="as6">
                                            <label class="form-check-label" for="as6"><?= __("Organizador gráfico") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="as7" value="Si" id="as7">
                                            <label class="form-check-label" for="as7"><?= __("Aprendizaje cooperativo") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="as8" value="Si" id="as8">
                                            <label class="form-check-label" for="as8"><?= __("Porfolio") ?></label>
                                        </div>
                                        <div class="form-check ml-4">
                                            <input type="checkbox" class="form-check-input" name="ot4" value="Si" id="ot4">
                                            <label class="form-check-label" for="ot4"><?= __("Otros") ?></label>
                                        </div>
                                        <input type="text" class="form-control ml-4 mt-1" name="otr7" maxlength="100">
                                        <input type="text" class="form-control ml-4 mt-1" name="otr8" maxlength="100">
                                    </div>

                                    <!-- Autoevaluación -->
                                    <div class="form-group">
                                        <label><?= __("Autoevaluación o observaciones") ?>:</label>
                                        <textarea class="form-control" name="autoeva" rows="5"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="card mb-3">
                <div class="card-body text-center">
                    <button type="submit" class="btn btn-success btn-lg mr-2" id="btnGuardar">
                        <span id="btnSaveText"><?= __("Guardar") ?></span>
                    </button>
                    <a href="#" class="btn btn-secondary btn-lg d-none" id="btnPrint" target="_blank">
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
        const API_URL = 'includes/index.php';
        let isEditMode = false;

        // Nuevo plan
        $('#btnNuevo').click(function() {
            $('#workPlanForm')[0].reset();
            $('#planId').val('');
            $('#workPlanForm').removeClass('d-none');
            $('#btnSaveText').text('<?= __("Crear") ?>');
            $('#btnPrint').addClass('d-none');
            $('#btnPrintTop').addClass('d-none');
            isEditMode = false;
        });

        // Buscar plan
        $('#btnBuscar').click(function() {
            const planId = $('#selectPlan').val();
            if (!planId) {
                Swal.fire('<?= __("Error") ?>', '<?= __("Seleccione un plan") ?>', 'error');
                return;
            }
            loadWorkPlan(planId);
        });

        // Función para cargar plan
        function loadWorkPlan(planId) {
            $.ajax({
                url: API_URL,
                method: 'POST',
                data: {
                    getWorkPlan: planId
                },
                dataType: 'json',
                success: function(response) {
                    const data = response;
                    $('#planId').val(data.id2);

                    // Llenar campos básicos
                    $('input[name="plan"]').val(data.plan);
                    $('input[name="grado"]').val(data.grado);
                    $('input[name="asignatura"]').val(data.asignatura);
                    $('input[name="mes"]').val(data.mes);
                    $('input[name="dia1"]').val(data.dia1);
                    $('input[name="dia2"]').val(data.dia2);
                    $('input[name="estandares"]').prop('checked', data.estandares == 'Si');

                    $('textarea[name="espectativas"]').val(data.espectativas);

                    // Nivel de profundidad
                    $('input[name="np1"]').prop('checked', data.np1 == 'Si');
                    $('input[name="np2"]').prop('checked', data.np2 == 'Si');
                    $('input[name="np3"]').prop('checked', data.np3 == 'Si');
                    $('input[name="np4"]').prop('checked', data.np4 == 'Si');
                    $('input[name="np5"]').prop('checked', data.np5 == 'Si');

                    // Tema y pre-requisito
                    $('input[name="tema"]').val(data.tema);
                    $('input[name="pre1"]').val(data.pre1);

                    // Objetivos
                    $('input[name="obj1"]').val(data.obj1);
                    $('input[name="obj2"]').val(data.obj2);
                    $('input[name="obj3"]').val(data.obj3);

                    // Campos adicionales ent1-12
                    for (let i = 1; i <= 12; i++) {
                        $('input[name="ent' + i + '"]').val(data['ent' + i] || '');
                    }

                    $('input[name="integracion"]').val(data.integracion);

                    // Actividades
                    $('input[name="act1"]').prop('checked', data.act1 == 'Si');
                    $('input[name="act2"]').prop('checked', data.act2 == 'Si');
                    $('input[name="act3"]').prop('checked', data.act3 == 'Si');
                    $('input[name="act4"]').prop('checked', data.act4 == 'Si');

                    // Inicio
                    for (let i = 1; i <= 7; i++) {
                        $('input[name="ini' + i + '"]').prop('checked', data['ini' + i] == 'Si');
                    }

                    // Desarrollo
                    for (let i = 1; i <= 7; i++) {
                        $('input[name="des' + i + '"]').prop('checked', data['des' + i] == 'Si');
                    }

                    // Cierre
                    for (let i = 1; i <= 5; i++) {
                        $('input[name="cie' + i + '"]').prop('checked', data['cie' + i] == 'Si');
                    }

                    // Evaluación
                    for (let i = 1; i <= 4; i++) {
                        $('input[name="eva' + i + '"]').prop('checked', data['eva' + i] == 'Si');
                    }

                    // Tablas
                    for (let i = 1; i <= 8; i++) {
                        $('input[name="tab' + i + '"]').val(data['tab' + i] || '');
                    }

                    // Selección
                    for (let i = 1; i <= 5; i++) {
                        $('input[name="sel' + i + '"]').prop('checked', data['sel' + i] == 'Si');
                    }

                    // Proyectos y otros
                    $('input[name="pro1"]').val(data.pro1);
                    $('input[name="pro2"]').val(data.pro2);
                    $('input[name="otro"]').val(data.otro);
                    $('textarea[name="autoeva"]').val(data.autoeva);

                    // Assessment
                    for (let i = 1; i <= 8; i++) {
                        $('input[name="as' + i + '"]').prop('checked', data['as' + i] == 'Si');
                    }

                    // Campos específicos de plan3: ot1-4
                    for (let i = 1; i <= 4; i++) {
                        $('input[name="ot' + i + '"]').prop('checked', data['ot' + i] == 'Si');
                    }

                    // Campos específicos de plan3: otr1-8
                    for (let i = 1; i <= 8; i++) {
                        $('input[name="otr' + i + '"]').val(data['otr' + i] || '');
                    }

                    $('#workPlanForm').removeClass('d-none');
                    $('#btnSaveText').text('<?= __("Guardar") ?>');
                    $('#btnPrint').attr('href', 'planes_inf3.php?plan=' + data.id2).removeClass('d-none');
                    $('#btnPrintTop').removeClass('d-none').attr('href', 'planes_inf3.php?plan=' + data.id2);
                    isEditMode = true;
                },
                error: function() {
                    Swal.fire('<?= __("Error") ?>', '<?= __("Error al cargar el plan") ?>', 'error');
                }
            });
        }

        // Guardar plan
        $('#workPlanForm').submit(function(e) {
            e.preventDefault();

            const formData = $(this).serializeArray();
            const data = {};

            if (isEditMode) {
                data.updateWorkPlan = true;
                data.workPlanId = $('#planId').val();
            } else {
                data.createWorkPlan = true;
            }

            // Convertir checkboxes
            $('input[type="checkbox"]').each(function() {
                const name = $(this).attr('name');
                data[name] = $(this).is(':checked') ? 'Si' : '';
            });

            // Agregar otros campos
            formData.forEach(item => {
                if (item.name !== 'planId') {
                    data[item.name] = item.value;
                }
            });

            $.ajax({
                url: API_URL,
                method: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('<?= __("Éxito") ?>', '<?= __("Plan guardado exitosamente") ?>', 'success')
                            .then(() => {
                                const pathname = window.location.pathname;
                                window.location.href = pathname + '?plan=' + response.id;
                            });
                    } else {
                        Swal.fire('<?= __("Error") ?>', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('<?= __("Error") ?>', '<?= __("Error al guardar el plan") ?>', 'error');
                }
            });
        });

        // Borrar plan
        $('#btnBorrar').click(function() {
            const planId = $('#selectPlan').val();
            if (!planId) {
                Swal.fire('<?= __("Error") ?>', '<?= __("Seleccione un plan") ?>', 'error');
                return;
            }

            Swal.fire({
                title: '<?= __("¿Está seguro?") ?>',
                text: '<?= __("¿Está seguro que desea eliminar el plan de trabajo?") ?>',
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
                            deleteWorkPlan: planId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('<?= __("Eliminado") ?>', '<?= __("Plan eliminado exitosamente") ?>', 'success').then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire('<?= __("Error") ?>', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('<?= __("Error") ?>', '<?= __("Error al eliminar el plan") ?>', 'error');
                        }
                    });
                }
            });
        });

        // Auto-cargar plan si hay ID en URL
        <?php if ($selectedPlanId): ?>
            setTimeout(function() {
                loadWorkPlan(<?= $selectedPlanId ?>);
            }, 100);
        <?php endif; ?>
    </script>
</body>

</html>