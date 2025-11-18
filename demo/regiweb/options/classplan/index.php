<?php
require_once '../../../app.php';

use App\Models\Teacher;
use Classes\Route;
use Classes\Session;

Session::is_logged();

$teacher = Teacher::find(Session::id());
if (!$teacher) {
    die('Error: Maestro no encontrado');
}

// Get teacher's courses
$courses = $teacher->subjects;

// Get all class plans for this teacher
$plans = $teacher->classPlans()->orderBy('id', 'desc')->get();

// Get selected plan ID from URL if exists
$selectedPlanId = $_GET['plan'] ?? null;
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = "Plan de Clase";
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
    <style>
        .checkbox-body td {
            text-align: center;
            vertical-align: middle;
        }

        .checkbox-body td:first-child {
            text-align: left;
        }

        .checkbox-body input[type="checkbox"] {
            margin: 0;
            margin-left: -6px;
        }

        .checkbox-with-input {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }

        .checkbox-with-input input[type="checkbox"] {
            margin: 0;
        }

        .checkbox-with-input input[type="text"] {
            width: 100%;
            max-width: 150px;
        }
    </style>

</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>

    <div class="container-fluid mt-3 mb-5 px-3">
        <h1 class="text-center mb-4">Plan de Clase</h1>

        <!-- Controles superiores -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary btn-block" id="btnNew">
                            Nuevo
                        </button>
                    </div>
                    <div class="col-md-5">
                        <label>Seleccionar Plan:</label>
                        <select class="form-control" id="planSelect">
                            <option value="">-- Seleccione un plan --</option>
                            <?php foreach ($plans as $plan): ?>
                                <option value="<?= $plan->id ?>" <?= $selectedPlanId == $plan->id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($plan->getTitle()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-info btn-block" id="btnSearch">
                            Buscar
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-block d-none" id="btnDelete">
                            Borrar
                        </button>
                    </div>
                    <div class="col-md-1">
                        <a href="#" id="btnPrintTop" class="btn btn-secondary btn-block d-none" target="_blank" title="Imprimir">
                            <i class="fas fa-print"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plan Form -->
        <form id="classPlanForm" class="card d-none">
            <div class="card-body">
                <input type="hidden" id="planId" name="planId">
                <input type="hidden" id="isNew" name="isNew" value="0">

                <!-- Teacher and Subject -->
                <div class="bg-light p-3 mb-3 rounded">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="font-weight-bold">Maestro(a):</label>
                            <input type="text" class="form-control" name="maestro" readonly
                                value="<?= htmlspecialchars($teacher->nombre . ' ' . $teacher->apellidos) ?>">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="font-weight-bold">Materia:</label>
                            <select class="form-control" name="materia" required>
                                <option value="">-- Seleccione --</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?= $course->curso ?>">
                                        <?= htmlspecialchars($course->curso . ' - ' . $course->desc1) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Class Theme and Duration -->
                <div class="bg-light p-3 mb-3 rounded">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label>Tema de la clase:</label>
                            <input type="text" class="form-control" name="tema" required>
                        </div>
                        <div class="col-md-3">
                            <label>Fecha:</label>
                            <input type="date" class="form-control" name="fecha" required>
                        </div>
                        <div class="col-md-3">
                            <label>Duración (semanas):</label>
                            <input type="number" class="form-control" name="duracion" min="1" value="1">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <label>Estrategia:</label>
                            <input type="text" class="form-control" name="estrategia">
                        </div>
                    </div>
                </div>

                <!-- Etapas y Tareas de Desempeño -->
                <div class="bg-info text-white text-center p-2 mb-3 rounded">
                    <strong>Etapas y Tareas de Desempeño</strong>
                </div>
                <div class="bg-light p-3 mb-3 rounded">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr class="section-header">
                                    <th width="10%">Etapa</th>
                                    <th width="50%">Actividades para el logro de las tareas de desempeño</th>
                                    <th width="40%">Tareas de Desempeño u otra evidencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th class="section-header">Antes</th>
                                    <td><textarea class="form-control" name="antes"></textarea></td>
                                    <td rowspan="3" class="align-top">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="tarea1" value="tarea1" id="tarea1">
                                                    <label class="form-check-label" for="tarea1">Prueba</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="tarea2" value="tarea2" id="tarea2">
                                                    <label class="form-check-label" for="tarea2">Quizz</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="tarea3" value="tarea3" id="tarea3">
                                                    <label class="form-check-label" for="tarea3">Proyecto</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="tarea4" value="tarea4" id="tarea4">
                                                    <label class="form-check-label" for="tarea4">Mapa de conceptos</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="tarea5" value="tarea5" id="tarea5">
                                                    <label class="form-check-label" for="tarea5">Organizador gráfico</label>
                                                    <input type="text" class="form-control form-control-sm mt-1" name="t5" placeholder="Especifique">
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="tarea6" value="tarea6" id="tarea6">
                                                    <label class="form-check-label" for="tarea6">Ejercicios de práctica</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="tarea7" value="tarea7" id="tarea7">
                                                    <label class="form-check-label" for="tarea7">Tirilla cómica</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="tarea8" value="tarea8" id="tarea8">
                                                    <label class="form-check-label" for="tarea8">Pregunta abierta</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="tarea9" value="tarea9" id="tarea9">
                                                    <label class="form-check-label" for="tarea9">Laboratorio</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="tarea10" value="tarea10" id="tarea10">
                                                    <label class="form-check-label" for="tarea10">Construcción de modelos</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="tarea11" value="tarea11" id="tarea11">
                                                    <label class="form-check-label" for="tarea11">Debate</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="tarea12" value="tarea12" id="tarea12">
                                                    <label class="form-check-label" for="tarea12">Dibujo</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="tarea13" value="tarea13" id="tarea13">
                                                    <label class="form-check-label" for="tarea13">Trabajo Creativo</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="tarea14" value="tarea14" id="tarea14">
                                                    <label class="form-check-label" for="tarea14">Otros</label>
                                                    <input type="text" class="form-control form-control-sm mt-1" name="t14" placeholder="Especifique">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="section-header">Durante</th>
                                    <td><textarea class="form-control" name="durante"></textarea></td>
                                </tr>
                                <tr>
                                    <th class="section-header">Después</th>
                                    <td><textarea class="form-control" name="despues"></textarea></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Weekly Guide -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <strong>Guía Semanal del Maestro</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Fecha</label>
                                <input type="date" class="form-control" name="fechaG">
                            </div>
                            <div class="col-md-4">
                                <label>Duración (semanas)</label>
                                <input type="number" class="form-control" name="duracionG" min="1" value="1">
                            </div>
                            <div class="col-md-4">
                                <label>Valor de la semana</label>
                                <input type="number" class="form-control" name="valorG" min="0" value="100">
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr class="bg-secondary text-white">
                                        <th>Días de la semana</th>
                                        <th>Lunes</th>
                                        <th>Martes</th>
                                        <th>Miércoles</th>
                                        <th>Jueves</th>
                                        <th>Viernes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Estándares -->
                                    <tr>
                                        <th class="bg-light">Estándares (PRCS)</th>
                                        <td><textarea class="form-control form-control-sm" name="estandares1" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="estandares2" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="estandares3" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="estandares4" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="estandares5" rows="3"></textarea></td>
                                    </tr>
                                    <!-- Expectativas -->
                                    <tr>
                                        <th class="bg-light">Expectativa</th>
                                        <td><textarea class="form-control form-control-sm" name="expectativa1" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="expectativa2" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="expectativa3" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="expectativa4" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="expectativa5" rows="3"></textarea></td>
                                    </tr>
                                    <!-- Objetivos -->
                                    <tr>
                                        <th class="bg-light">Objetivos</th>
                                        <td><textarea class="form-control form-control-sm" name="objetivos1" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="objetivos2" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="objetivos3" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="objetivos4" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="objetivos5" rows="3"></textarea></td>
                                    </tr>
                                    <!-- Actividades de Aprendizaje Header -->
                                    <tr>
                                        <th class="bg-light" rowspan="2">Semana</th>
                                        <th class="text-center">Actividades de Aprendizaje</th>
                                        <th class="text-center">Actividades de Aprendizaje</th>
                                        <th class="text-center">Actividades de Aprendizaje</th>
                                        <th class="text-center">Actividades de Aprendizaje</th>
                                        <th class="text-center">Actividades de Aprendizaje</th>
                                    </tr>
                                    <!-- Nivel de pensamiento -->
                                    <tr>
                                        <td>
                                            <div class="mb-2"><strong>Nivel de pensamiento</strong></div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="pensamiento1_1" value="pensamiento1_1" id="pensamiento1_1">
                                                <label class="form-check-label" for="pensamiento1_1">1 Memorístico</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="pensamiento1_2" value="pensamiento1_2" id="pensamiento1_2">
                                                <label class="form-check-label" for="pensamiento1_2">2 Procesamiento</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="pensamiento1_3" value="pensamiento1_3" id="pensamiento1_3">
                                                <label class="form-check-label" for="pensamiento1_3">3 Estratégico</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="pensamiento1_4" value="pensamiento1_4" id="pensamiento1_4">
                                                <label class="form-check-label" for="pensamiento1_4">4 Extendido</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="mb-2"><strong>Nivel de pensamiento</strong></div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="pensamiento2_1" value="pensamiento2_1" id="pensamiento2_1">
                                                <label class="form-check-label" for="pensamiento2_1">1 Memorístico</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="pensamiento2_2" value="pensamiento2_2" id="pensamiento2_2">
                                                <label class="form-check-label" for="pensamiento2_2">2 Procesamiento</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="pensamiento2_3" value="pensamiento2_3" id="pensamiento2_3">
                                                <label class="form-check-label" for="pensamiento2_3">3 Estratégico</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="pensamiento2_4" value="pensamiento2_4" id="pensamiento2_4">
                                                <label class="form-check-label" for="pensamiento2_4">4 Extendido</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="mb-2"><strong>Nivel de pensamiento</strong></div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="pensamiento3_1" value="pensamiento3_1" id="pensamiento3_1">
                                                <label class="form-check-label" for="pensamiento3_1">1 Memorístico</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="pensamiento3_2" value="pensamiento3_2" id="pensamiento3_2">
                                                <label class="form-check-label" for="pensamiento3_2">2 Procesamiento</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="pensamiento3_3" value="pensamiento3_3" id="pensamiento3_3">
                                                <label class="form-check-label" for="pensamiento3_3">3 Estratégico</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="pensamiento3_4" value="pensamiento3_4" id="pensamiento3_4">
                                                <label class="form-check-label" for="pensamiento3_4">4 Extendido</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="mb-2"><strong>Nivel de pensamiento</strong></div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="pensamiento4_1" value="pensamiento4_1" id="pensamiento4_1">
                                                <label class="form-check-label" for="pensamiento4_1">1 Memorístico</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="pensamiento4_2" value="pensamiento4_2" id="pensamiento4_2">
                                                <label class="form-check-label" for="pensamiento4_2">2 Procesamiento</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="pensamiento4_3" value="pensamiento4_3" id="pensamiento4_3">
                                                <label class="form-check-label" for="pensamiento4_3">3 Estratégico</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="pensamiento4_4" value="pensamiento4_4" id="pensamiento4_4">
                                                <label class="form-check-label" for="pensamiento4_4">4 Extendido</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="mb-2"><strong>Nivel de pensamiento</strong></div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="pensamiento5_1" value="pensamiento5_1" id="pensamiento5_1">
                                                <label class="form-check-label" for="pensamiento5_1">1 Memorístico</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="pensamiento5_2" value="pensamiento5_2" id="pensamiento5_2">
                                                <label class="form-check-label" for="pensamiento5_2">2 Procesamiento</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="pensamiento5_3" value="pensamiento5_3" id="pensamiento5_3">
                                                <label class="form-check-label" for="pensamiento5_3">3 Estratégico</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="pensamiento5_4" value="pensamiento5_4" id="pensamiento5_4">
                                                <label class="form-check-label" for="pensamiento5_4">4 Extendido</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Antes -->
                                    <tr>
                                        <th class="bg-light">Antes</th>
                                        <td><textarea class="form-control form-control-sm" name="antes1" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="antes2" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="antes3" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="antes4" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="antes5" rows="3"></textarea></td>
                                    </tr>
                                    <!-- Durante -->
                                    <tr>
                                        <th class="bg-light">Durante</th>
                                        <td><textarea class="form-control form-control-sm" name="durante1" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="durante2" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="durante3" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="durante4" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="durante5" rows="3"></textarea></td>
                                    </tr>
                                    <!-- Después -->
                                    <tr>
                                        <th class="bg-light">Después</th>
                                        <td><textarea class="form-control form-control-sm" name="despues1" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="despues2" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="despues3" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="despues4" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="despues5" rows="3"></textarea></td>
                                    </tr>
                                    <!-- Estrategia académica -->
                                    <tr>
                                        <th class="bg-light">Estrategia académica</th>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="estrategia_a1_1" value="estrategia_a1_1" id="estrategia_a1_1">
                                                <label class="form-check-label small" for="estrategia_a1_1">Aprendizaje basado en problemas</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="estrategia_a1_2" value="estrategia_a1_2" id="estrategia_a1_2">
                                                <label class="form-check-label small" for="estrategia_a1_2">Trabajo cooperativo</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="estrategia_a1_3" value="estrategia_a1_3" id="estrategia_a1_3">
                                                <label class="form-check-label small" for="estrategia_a1_3">Ciclos de aprendizaje</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="estrategia_a1_4" value="estrategia_a1_4" id="estrategia_a1_4">
                                                <label class="form-check-label small" for="estrategia_a1_4">ECA:</label>
                                                <input type="text" class="form-control form-control-sm mt-1" name="estrategia_a1_41" placeholder="Especifique">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="estrategia_a2_1" value="estrategia_a2_1" id="estrategia_a2_1">
                                                <label class="form-check-label small" for="estrategia_a2_1">Aprendizaje basado en problemas</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="estrategia_a2_2" value="estrategia_a2_2" id="estrategia_a2_2">
                                                <label class="form-check-label small" for="estrategia_a2_2">Trabajo cooperativo</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="estrategia_a2_3" value="estrategia_a2_3" id="estrategia_a2_3">
                                                <label class="form-check-label small" for="estrategia_a2_3">Ciclos de aprendizaje</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="estrategia_a2_4" value="estrategia_a2_4" id="estrategia_a2_4">
                                                <label class="form-check-label small" for="estrategia_a2_4">ECA:</label>
                                                <input type="text" class="form-control form-control-sm mt-1" name="estrategia_a2_41" placeholder="Especifique">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="estrategia_a3_1" value="estrategia_a3_1" id="estrategia_a3_1">
                                                <label class="form-check-label small" for="estrategia_a3_1">Aprendizaje basado en problemas</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="estrategia_a3_2" value="estrategia_a3_2" id="estrategia_a3_2">
                                                <label class="form-check-label small" for="estrategia_a3_2">Trabajo cooperativo</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="estrategia_a3_3" value="estrategia_a3_3" id="estrategia_a3_3">
                                                <label class="form-check-label small" for="estrategia_a3_3">Ciclos de aprendizaje</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="estrategia_a3_4" value="estrategia_a3_4" id="estrategia_a3_4">
                                                <label class="form-check-label small" for="estrategia_a3_4">ECA:</label>
                                                <input type="text" class="form-control form-control-sm mt-1" name="estrategia_a3_41" placeholder="Especifique">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="estrategia_a4_1" value="estrategia_a4_1" id="estrategia_a4_1">
                                                <label class="form-check-label small" for="estrategia_a4_1">Aprendizaje basado en problemas</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="estrategia_a4_2" value="estrategia_a4_2" id="estrategia_a4_2">
                                                <label class="form-check-label small" for="estrategia_a4_2">Trabajo cooperativo</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="estrategia_a4_3" value="estrategia_a4_3" id="estrategia_a4_3">
                                                <label class="form-check-label small" for="estrategia_a4_3">Ciclos de aprendizaje</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="estrategia_a4_4" value="estrategia_a4_4" id="estrategia_a4_4">
                                                <label class="form-check-label small" for="estrategia_a4_4">ECA:</label>
                                                <input type="text" class="form-control form-control-sm mt-1" name="estrategia_a4_41" placeholder="Especifique">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="estrategia_a5_1" value="estrategia_a5_1" id="estrategia_a5_1">
                                                <label class="form-check-label small" for="estrategia_a5_1">Aprendizaje basado en problemas</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="estrategia_a5_2" value="estrategia_a5_2" id="estrategia_a5_2">
                                                <label class="form-check-label small" for="estrategia_a5_2">Trabajo cooperativo</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="estrategia_a5_3" value="estrategia_a5_3" id="estrategia_a5_3">
                                                <label class="form-check-label small" for="estrategia_a5_3">Ciclos de aprendizaje</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="estrategia_a5_4" value="estrategia_a5_4" id="estrategia_a5_4">
                                                <label class="form-check-label small" for="estrategia_a5_4">ECA:</label>
                                                <input type="text" class="form-control form-control-sm mt-1" name="estrategia_a5_41" placeholder="Especifique">
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Valores -->
                                    <tr>
                                        <th class="bg-light">Valores</th>
                                        <td><textarea class="form-control form-control-sm" name="valores1" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="valores2" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="valores3" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="valores4" rows="3"></textarea></td>
                                        <td><textarea class="form-control form-control-sm" name="valores5" rows="3"></textarea></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Estrategia de educación diferenciada -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Estrategia de educación diferenciada</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 20%;">Estrategia</th>
                                    <th style="width: 16%;">Lunes</th>
                                    <th style="width: 16%;">Martes</th>
                                    <th style="width: 16%;">Miércoles</th>
                                    <th style="width: 16%;">Jueves</th>
                                    <th style="width: 16%;">Viernes</th>
                                </tr>
                            </thead>
                            <tbody class="checkbox-body">
                                <tr>
                                    <td><strong>Tiempo adicional</strong></td>
                                    <td><input type="checkbox" name="estrategia_e1_1"></td>
                                    <td><input type="checkbox" name="estrategia_e2_1"></td>
                                    <td><input type="checkbox" name="estrategia_e3_1"></td>
                                    <td><input type="checkbox" name="estrategia_e4_1"></td>
                                    <td><input type="checkbox" name="estrategia_e5_1"></td>
                                </tr>
                                <tr>
                                    <td><strong>Ubicación de pupitre</strong></td>
                                    <td><input type="checkbox" name="estrategia_e1_2"></td>
                                    <td><input type="checkbox" name="estrategia_e2_2"></td>
                                    <td><input type="checkbox" name="estrategia_e3_2"></td>
                                    <td><input type="checkbox" name="estrategia_e4_2"></td>
                                    <td><input type="checkbox" name="estrategia_e5_2"></td>
                                </tr>
                                <tr>
                                    <td><strong>Fragmentar trabajos</strong></td>
                                    <td><input type="checkbox" name="estrategia_e1_3"></td>
                                    <td><input type="checkbox" name="estrategia_e2_3"></td>
                                    <td><input type="checkbox" name="estrategia_e3_3"></td>
                                    <td><input type="checkbox" name="estrategia_e4_3"></td>
                                    <td><input type="checkbox" name="estrategia_e5_3"></td>
                                </tr>
                                <tr>
                                    <td><strong>Trabajo individual / grupal</strong></td>
                                    <td><input type="checkbox" name="estrategia_e1_4"></td>
                                    <td><input type="checkbox" name="estrategia_e2_4"></td>
                                    <td><input type="checkbox" name="estrategia_e3_4"></td>
                                    <td><input type="checkbox" name="estrategia_e4_4"></td>
                                    <td><input type="checkbox" name="estrategia_e5_4"></td>
                                </tr>
                                <tr>
                                    <td><strong>Material complementario</strong></td>
                                    <td><input type="checkbox" name="estrategia_e1_5"></td>
                                    <td><input type="checkbox" name="estrategia_e2_5"></td>
                                    <td><input type="checkbox" name="estrategia_e3_5"></td>
                                    <td><input type="checkbox" name="estrategia_e4_5"></td>
                                    <td><input type="checkbox" name="estrategia_e5_5"></td>
                                </tr>
                                <tr>
                                    <td><strong>Traducción de material</strong></td>
                                    <td><input type="checkbox" name="estrategia_e1_6"></td>
                                    <td><input type="checkbox" name="estrategia_e2_6"></td>
                                    <td><input type="checkbox" name="estrategia_e3_6"></td>
                                    <td><input type="checkbox" name="estrategia_e4_6"></td>
                                    <td><input type="checkbox" name="estrategia_e5_6"></td>
                                </tr>
                                <tr>
                                    <td><strong>Investigación grupal</strong></td>
                                    <td><input type="checkbox" name="estrategia_e1_7"></td>
                                    <td><input type="checkbox" name="estrategia_e2_7"></td>
                                    <td><input type="checkbox" name="estrategia_e3_7"></td>
                                    <td><input type="checkbox" name="estrategia_e4_7"></td>
                                    <td><input type="checkbox" name="estrategia_e5_7"></td>
                                </tr>
                                <tr>
                                    <td><strong>Tareas de inteligencias múltiples</strong></td>
                                    <td><input type="checkbox" name="estrategia_e1_8"></td>
                                    <td><input type="checkbox" name="estrategia_e2_8"></td>
                                    <td><input type="checkbox" name="estrategia_e3_8"></td>
                                    <td><input type="checkbox" name="estrategia_e4_8"></td>
                                    <td><input type="checkbox" name="estrategia_e5_8"></td>
                                </tr>
                                <tr>
                                    <td><strong>Diccionario</strong></td>
                                    <td><input type="checkbox" name="estrategia_e1_9"></td>
                                    <td><input type="checkbox" name="estrategia_e2_9"></td>
                                    <td><input type="checkbox" name="estrategia_e3_9"></td>
                                    <td><input type="checkbox" name="estrategia_e4_9"></td>
                                    <td><input type="checkbox" name="estrategia_e5_9"></td>
                                </tr>
                                <tr>
                                    <td><strong>Diversos modos de expresión</strong></td>
                                    <td><input type="checkbox" name="estrategia_e1_10"></td>
                                    <td><input type="checkbox" name="estrategia_e2_10"></td>
                                    <td><input type="checkbox" name="estrategia_e3_10"></td>
                                    <td><input type="checkbox" name="estrategia_e4_10"></td>
                                    <td><input type="checkbox" name="estrategia_e5_10"></td>
                                </tr>
                                <tr>
                                    <td><strong>Instrucciones claras y precisas</strong></td>
                                    <td><input type="checkbox" name="estrategia_e1_11"></td>
                                    <td><input type="checkbox" name="estrategia_e2_11"></td>
                                    <td><input type="checkbox" name="estrategia_e3_11"></td>
                                    <td><input type="checkbox" name="estrategia_e4_11"></td>
                                    <td><input type="checkbox" name="estrategia_e5_11"></td>
                                </tr>
                                <tr>
                                    <td><strong>Proveer ejemplos</strong></td>
                                    <td><input type="checkbox" name="estrategia_e1_12"></td>
                                    <td><input type="checkbox" name="estrategia_e2_12"></td>
                                    <td><input type="checkbox" name="estrategia_e3_12"></td>
                                    <td><input type="checkbox" name="estrategia_e4_12"></td>
                                    <td><input type="checkbox" name="estrategia_e5_12"></td>
                                </tr>
                                <tr>
                                    <td><strong>Otros</strong></td>
                                    <td>
                                        <div class="checkbox-with-input">

                                            <input type="checkbox" name="estrategia_e1_13">
                                            <input type="text" class="form-control form-control-sm mt-1" name="estrategia_e1_131">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">

                                            <input type="checkbox" name="estrategia_e2_13">
                                            <input type="text" class="form-control form-control-sm mt-1" name="estrategia_e2_131">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">

                                            <input type="checkbox" name="estrategia_e3_13">
                                            <input type="text" class="form-control form-control-sm mt-1" name="estrategia_e3_131">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">

                                            <input type="checkbox" name="estrategia_e4_13">
                                            <input type="text" class="form-control form-control-sm mt-1" name="estrategia_e4_131">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">

                                            <input type="checkbox" name="estrategia_e5_13">
                                            <input type="text" class="form-control form-control-sm mt-1" name="estrategia_e5_131">
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Conceptos y destrezas -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Conceptos y destrezas</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 20%;">Concepto/Destreza</th>
                                    <th style="width: 16%;">Lunes</th>
                                    <th style="width: 16%;">Martes</th>
                                    <th style="width: 16%;">Miércoles</th>
                                    <th style="width: 16%;">Jueves</th>
                                    <th style="width: 16%;">Viernes</th>
                                </tr>
                            </thead>
                            <tbody class="checkbox-body">
                                <tr>
                                    <td><strong>Formula preguntas</strong></td>
                                    <td><input type="checkbox" name="conceptos1_1"></td>
                                    <td><input type="checkbox" name="conceptos2_1"></td>
                                    <td><input type="checkbox" name="conceptos3_1"></td>
                                    <td><input type="checkbox" name="conceptos4_1"></td>
                                    <td><input type="checkbox" name="conceptos5_1"></td>
                                </tr>
                                <tr>
                                    <td><strong>Desarrolla modelos</strong></td>
                                    <td><input type="checkbox" name="conceptos1_2"></td>
                                    <td><input type="checkbox" name="conceptos2_2"></td>
                                    <td><input type="checkbox" name="conceptos3_2"></td>
                                    <td><input type="checkbox" name="conceptos4_2"></td>
                                    <td><input type="checkbox" name="conceptos5_2"></td>
                                </tr>
                                <tr>
                                    <td><strong>Planifica y lleva a cabo experimentos</strong></td>
                                    <td><input type="checkbox" name="conceptos1_3"></td>
                                    <td><input type="checkbox" name="conceptos2_3"></td>
                                    <td><input type="checkbox" name="conceptos3_3"></td>
                                    <td><input type="checkbox" name="conceptos4_3"></td>
                                    <td><input type="checkbox" name="conceptos5_3"></td>
                                </tr>
                                <tr>
                                    <td><strong>Analiza e interpreta datos</strong></td>
                                    <td><input type="checkbox" name="conceptos1_4"></td>
                                    <td><input type="checkbox" name="conceptos2_4"></td>
                                    <td><input type="checkbox" name="conceptos3_4"></td>
                                    <td><input type="checkbox" name="conceptos4_4"></td>
                                    <td><input type="checkbox" name="conceptos5_4"></td>
                                </tr>
                                <tr>
                                    <td><strong>Usa pensamiento matemático y computacional</strong></td>
                                    <td><input type="checkbox" name="conceptos1_5"></td>
                                    <td><input type="checkbox" name="conceptos2_5"></td>
                                    <td><input type="checkbox" name="conceptos3_5"></td>
                                    <td><input type="checkbox" name="conceptos4_5"></td>
                                    <td><input type="checkbox" name="conceptos5_5"></td>
                                </tr>
                                <tr>
                                    <td><strong>Propone explicaciones</strong></td>
                                    <td><input type="checkbox" name="conceptos1_6"></td>
                                    <td><input type="checkbox" name="conceptos2_6"></td>
                                    <td><input type="checkbox" name="conceptos3_6"></td>
                                    <td><input type="checkbox" name="conceptos4_6"></td>
                                    <td><input type="checkbox" name="conceptos5_6"></td>
                                </tr>
                                <tr>
                                    <td><strong>Expone argumentos basándose en la evidencia</strong></td>
                                    <td><input type="checkbox" name="conceptos1_7"></td>
                                    <td><input type="checkbox" name="conceptos2_7"></td>
                                    <td><input type="checkbox" name="conceptos3_7"></td>
                                    <td><input type="checkbox" name="conceptos4_7"></td>
                                    <td><input type="checkbox" name="conceptos5_7"></td>
                                </tr>
                                <tr>
                                    <td><strong>Obtiene, evalúa y comunica información</strong></td>
                                    <td><input type="checkbox" name="conceptos1_8"></td>
                                    <td><input type="checkbox" name="conceptos2_8"></td>
                                    <td><input type="checkbox" name="conceptos3_8"></td>
                                    <td><input type="checkbox" name="conceptos4_8"></td>
                                    <td><input type="checkbox" name="conceptos5_8"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Temas transversales -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Temas transversales</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 20%;">Tema</th>
                                    <th style="width: 16%;">Lunes</th>
                                    <th style="width: 16%;">Martes</th>
                                    <th style="width: 16%;">Miércoles</th>
                                    <th style="width: 16%;">Jueves</th>
                                    <th style="width: 16%;">Viernes</th>
                                </tr>
                            </thead>
                            <tbody class="checkbox-body">
                                <tr>
                                    <td><strong>Identidad cultural</strong></td>
                                    <td><input type="checkbox" name="temas1_1"></td>
                                    <td><input type="checkbox" name="temas2_1"></td>
                                    <td><input type="checkbox" name="temas3_1"></td>
                                    <td><input type="checkbox" name="temas4_1"></td>
                                    <td><input type="checkbox" name="temas5_1"></td>
                                </tr>
                                <tr>
                                    <td><strong>Educación cívica y ética</strong></td>
                                    <td><input type="checkbox" name="temas1_2"></td>
                                    <td><input type="checkbox" name="temas2_2"></td>
                                    <td><input type="checkbox" name="temas3_2"></td>
                                    <td><input type="checkbox" name="temas4_2"></td>
                                    <td><input type="checkbox" name="temas5_2"></td>
                                </tr>
                                <tr>
                                    <td><strong>Educación para la paz</strong></td>
                                    <td><input type="checkbox" name="temas1_3"></td>
                                    <td><input type="checkbox" name="temas2_3"></td>
                                    <td><input type="checkbox" name="temas3_3"></td>
                                    <td><input type="checkbox" name="temas4_3"></td>
                                    <td><input type="checkbox" name="temas5_3"></td>
                                </tr>
                                <tr>
                                    <td><strong>Educación ambiental</strong></td>
                                    <td><input type="checkbox" name="temas1_4"></td>
                                    <td><input type="checkbox" name="temas2_4"></td>
                                    <td><input type="checkbox" name="temas3_4"></td>
                                    <td><input type="checkbox" name="temas4_4"></td>
                                    <td><input type="checkbox" name="temas5_4"></td>
                                </tr>
                                <tr>
                                    <td><strong>Tecnología y educación</strong></td>
                                    <td><input type="checkbox" name="temas1_5"></td>
                                    <td><input type="checkbox" name="temas2_5"></td>
                                    <td><input type="checkbox" name="temas3_5"></td>
                                    <td><input type="checkbox" name="temas4_5"></td>
                                    <td><input type="checkbox" name="temas5_5"></td>
                                </tr>
                                <tr>
                                    <td><strong>Educación para el trabajo</strong></td>
                                    <td><input type="checkbox" name="temas1_6"></td>
                                    <td><input type="checkbox" name="temas2_6"></td>
                                    <td><input type="checkbox" name="temas3_6"></td>
                                    <td><input type="checkbox" name="temas4_6"></td>
                                    <td><input type="checkbox" name="temas5_6"></td>
                                </tr>
                                <tr>
                                    <td><strong>Prevención de riesgos</strong></td>
                                    <td><input type="checkbox" name="temas1_7"></td>
                                    <td><input type="checkbox" name="temas2_7"></td>
                                    <td><input type="checkbox" name="temas3_7"></td>
                                    <td><input type="checkbox" name="temas4_7"></td>
                                    <td><input type="checkbox" name="temas5_7"></td>
                                </tr>
                                <tr>
                                    <td><strong>Educación para la salud</strong></td>
                                    <td><input type="checkbox" name="temas1_8"></td>
                                    <td><input type="checkbox" name="temas2_8"></td>
                                    <td><input type="checkbox" name="temas3_8"></td>
                                    <td><input type="checkbox" name="temas4_8"></td>
                                    <td><input type="checkbox" name="temas5_8"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Materiales o recursos -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Materiales o recursos</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 20%;">Material/Recurso</th>
                                    <th style="width: 16%;">Lunes</th>
                                    <th style="width: 16%;">Martes</th>
                                    <th style="width: 16%;">Miércoles</th>
                                    <th style="width: 16%;">Jueves</th>
                                    <th style="width: 16%;">Viernes</th>
                                </tr>
                            </thead>
                            <tbody class="checkbox-body">
                                <tr>
                                    <td><strong>Computadora o Proyector</strong></td>
                                    <td><input type="checkbox" class="form-check-input" name="materiales1_1"></td>
                                    <td><input type="checkbox" class="form-check-input" name="materiales2_1"></td>
                                    <td><input type="checkbox" class="form-check-input" name="materiales3_1"></td>
                                    <td><input type="checkbox" class="form-check-input" name="materiales4_1"></td>
                                    <td><input type="checkbox" class="form-check-input" name="materiales5_1"></td>
                                </tr>
                                <tr>
                                    <td><strong>Material fotocopiado</strong></td>
                                    <td><input type="checkbox" class="form-check-input" name="materiales1_2"></td>
                                    <td><input type="checkbox" class="form-check-input" name="materiales2_2"></td>
                                    <td><input type="checkbox" class="form-check-input" name="materiales3_2"></td>
                                    <td><input type="checkbox" class="form-check-input" name="materiales4_2"></td>
                                    <td><input type="checkbox" class="form-check-input" name="materiales5_2"></td>
                                </tr>
                                <tr>
                                    <td><strong>Libro</strong></td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="materiales1_3">
                                            <input type="text" class="form-control form-control-sm" name="materiales1_31">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="materiales2_3">
                                            <input type="text" class="form-control form-control-sm" name="materiales2_31">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="materiales3_3">
                                            <input type="text" class="form-control form-control-sm" name="materiales3_31">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="materiales4_3">
                                            <input type="text" class="form-control form-control-sm" name="materiales4_31">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="materiales5_3">
                                            <input type="text" class="form-control form-control-sm" name="materiales5_31">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Equipo de</strong></td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="materiales1_4">
                                            <input type="text" class="form-control form-control-sm" name="materiales1_41">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="materiales2_4">
                                            <input type="text" class="form-control form-control-sm" name="materiales2_41">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="materiales3_4">
                                            <input type="text" class="form-control form-control-sm" name="materiales3_41">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="materiales4_4">
                                            <input type="text" class="form-control form-control-sm" name="materiales4_41">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="materiales5_4">
                                            <input type="text" class="form-control form-control-sm" name="materiales5_41">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Video/ Película</strong></td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="materiales1_5">
                                            <input type="text" class="form-control form-control-sm" name="materiales1_51">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="materiales2_5">
                                            <input type="text" class="form-control form-control-sm" name="materiales2_51">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="materiales3_5">
                                            <input type="text" class="form-control form-control-sm" name="materiales3_51">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="materiales4_5">
                                            <input type="text" class="form-control form-control-sm" name="materiales4_51">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="materiales5_5">
                                            <input type="text" class="form-control form-control-sm" name="materiales5_51">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Manipulativos</strong></td>
                                    <td><input type="checkbox" class="form-check-input" name="materiales1_6"></td>
                                    <td><input type="checkbox" class="form-check-input" name="materiales2_6"></td>
                                    <td><input type="checkbox" class="form-check-input" name="materiales3_6"></td>
                                    <td><input type="checkbox" class="form-check-input" name="materiales4_6"></td>
                                    <td><input type="checkbox" class="form-check-input" name="materiales5_6"></td>
                                </tr>
                                <tr>
                                    <td><strong>Otros</strong></td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="materiales1_7">
                                            <input type="text" class="form-control form-control-sm" name="materiales1_71">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="materiales2_7">
                                            <input type="text" class="form-control form-control-sm" name="materiales2_71">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="materiales3_7">
                                            <input type="text" class="form-control form-control-sm" name="materiales3_71">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="materiales4_7">
                                            <input type="text" class="form-control form-control-sm" name="materiales4_71">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="materiales5_7">
                                            <input type="text" class="form-control form-control-sm" name="materiales5_71">
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Asignaciones / Tareas especiales -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Asignaciones / Tareas especiales</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 20%;">Tipo</th>
                                    <th style="width: 16%;">Lunes</th>
                                    <th style="width: 16%;">Martes</th>
                                    <th style="width: 16%;">Miércoles</th>
                                    <th style="width: 16%;">Jueves</th>
                                    <th style="width: 16%;">Viernes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Tarea</strong></td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="tareas1_1">
                                            <input type="text" class="form-control form-control-sm" name="tareas1_11">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="tareas2_1">
                                            <input type="text" class="form-control form-control-sm" name="tareas2_11">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="tareas3_1">
                                            <input type="text" class="form-control form-control-sm" name="tareas3_11">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="tareas4_1">
                                            <input type="text" class="form-control form-control-sm" name="tareas4_11">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="tareas5_1">
                                            <input type="text" class="form-control form-control-sm" name="tareas5_11">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Proyecto</strong></td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="tareas1_2">
                                            <input type="text" class="form-control form-control-sm" name="tareas1_21">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="tareas2_2">
                                            <input type="text" class="form-control form-control-sm" name="tareas2_21">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="tareas3_2">
                                            <input type="text" class="form-control form-control-sm" name="tareas3_21">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="tareas4_2">
                                            <input type="text" class="form-control form-control-sm" name="tareas4_21">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="tareas5_2">
                                            <input type="text" class="form-control form-control-sm" name="tareas5_21">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Investigación</strong></td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="tareas1_3">
                                            <input type="text" class="form-control form-control-sm" name="tareas1_31">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="tareas2_3">
                                            <input type="text" class="form-control form-control-sm" name="tareas2_31">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="tareas3_3">
                                            <input type="text" class="form-control form-control-sm" name="tareas3_31">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="tareas4_3">
                                            <input type="text" class="form-control form-control-sm" name="tareas4_31">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox-with-input">
                                            <input type="checkbox" name="tareas5_3">
                                            <input type="text" class="form-control form-control-sm" name="tareas5_31">
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Actividades -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Actividades</h5>
                    </div>
                    <div class="card-body">
                        <!-- Antes -->
                        <h6 class="text-primary mb-3">Antes</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="actividad_antes1" id="actividad_antes1">
                                    <label class="form-check-label" for="actividad_antes1">Torbellino de ideas</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="actividad_antes2" id="actividad_antes2">
                                    <label class="form-check-label" for="actividad_antes2">Discusión reflexiva y socializada</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="actividad_antes3" id="actividad_antes3">
                                    <label class="form-check-label" for="actividad_antes3">Redacción de lista de conceptos conocidos</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="actividad_antes4" id="actividad_antes4">
                                    <label class="form-check-label" for="actividad_antes4">Contestar preguntas</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="actividad_antes5" id="actividad_antes5">
                                    <label class="form-check-label" for="actividad_antes5">Organizadores gráficos</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="actividad_antes6" id="actividad_antes6">
                                    <label class="form-check-label" for="actividad_antes6">Presentación visual</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="actividad_antes7" id="actividad_antes7">
                                    <label class="form-check-label" for="actividad_antes7">Inicio de proyectos</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="actividad_antes8" id="actividad_antes8">
                                    <label class="form-check-label" for="actividad_antes8">Otros</label>
                                    <input type="text" class="form-control form-control-sm mt-1" name="actividad_antes81">
                                </div>
                            </div>
                        </div>

                        <!-- Durante -->
                        <h6 class="text-primary mb-3">Durante</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="actividad_durante1" id="actividad_durante1">
                                    <label class="form-check-label" for="actividad_durante1">Torbellino de ideas</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="actividad_durante2" id="actividad_durante2">
                                    <label class="form-check-label" for="actividad_durante2">Conferencia</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="actividad_durante3" id="actividad_durante3">
                                    <label class="form-check-label" for="actividad_durante3">Desarrollo de proyecto</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="actividad_durante4" id="actividad_durante4">
                                    <label class="form-check-label" for="actividad_durante4">Laboratorios</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="actividad_durante5" id="actividad_durante5">
                                    <label class="form-check-label" for="actividad_durante5">Trabajo en equipo</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="actividad_durante6" id="actividad_durante6">
                                    <label class="form-check-label" for="actividad_durante6">Trabajo individual</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="actividad_durante7" id="actividad_durante7">
                                    <label class="form-check-label" for="actividad_durante7">Lecturas</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="actividad_durante8" id="actividad_durante8">
                                    <label class="form-check-label" for="actividad_durante8">Investigación</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="actividad_durante9" id="actividad_durante9">
                                    <label class="form-check-label" for="actividad_durante9">Otros</label>
                                    <input type="text" class="form-control form-control-sm mt-1" name="actividad_durante91">
                                </div>
                            </div>
                        </div>

                        <!-- Después -->
                        <h6 class="text-primary mb-3">Después</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="actividad_despues1" id="actividad_despues1">
                                    <label class="form-check-label" for="actividad_despues1">Organizadores gráficos</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="actividad_despues2" id="actividad_despues2">
                                    <label class="form-check-label" for="actividad_despues2">Presentaciones individuales</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="actividad_despues3" id="actividad_despues3">
                                    <label class="form-check-label" for="actividad_despues3">Presentaciones grupales</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="actividad_despues4" id="actividad_despues4">
                                    <label class="form-check-label" for="actividad_despues4">Informes orales</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="actividad_despues5" id="actividad_despues5">
                                    <label class="form-check-label" for="actividad_despues5">Informes escritos</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="actividad_despues6" id="actividad_despues6">
                                    <label class="form-check-label" for="actividad_despues6">Dramatización</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="actividad_despues7" id="actividad_despues7">
                                    <label class="form-check-label" for="actividad_despues7">Otros</label>
                                    <input type="text" class="form-control form-control-sm mt-1" name="actividad_despues71">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reflexión sobre la praxis -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Reflexión sobre la praxis</h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" name="reflexion" rows="4" placeholder="Escriba su reflexión sobre la práctica docente..."></textarea>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center mt-4">
                    <button type="submit" id="btnSave" class="btn btn-success btn-lg">
                        <i class="fas fa-save"></i> <span id="btnSaveText">Guardar</span>
                    </button>
                    <a href="#" id="btnPrint" class="btn btn-info btn-lg ml-2 d-none" target="_blank">
                        <i class="fas fa-print"></i> Imprimir
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
            const classPlanForm = $('#classPlanForm');
            const planSelect = $('#planSelect');
            const btnNew = $('#btnNew');
            const btnSearch = $('#btnSearch');
            const btnDelete = $('#btnDelete');
            const btnPrint = $('#btnPrint');
            const btnSaveText = $('#btnSaveText');
            const isNewInput = $('#isNew');
            const planIdInput = $('#planId');

            // Si hay un plan seleccionado en la URL, cargarlo automáticamente
            <?php if ($selectedPlanId): ?>
                setTimeout(function() {
                    loadClassPlan(<?= $selectedPlanId ?>);
                }, 100);
            <?php endif; ?>

            // Nuevo plan
            btnNew.click(function() {
                clearForm();
                isNewInput.val('1');
                classPlanForm.removeClass('d-none');
                btnSaveText.text('Crear');
                btnPrint.addClass('d-none');
                $('#btnPrintTop').addClass('d-none');
                btnDelete.addClass('d-none');
                planSelect.val('');
            });

            // Buscar plan
            btnSearch.click(function() {
                const planId = planSelect.val();
                if (!planId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Seleccione un plan de clase',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    return;
                }

                loadClassPlan(planId);
            });

            // Función para cargar un plan de clase
            function loadClassPlan(planId) {
                $.ajax({
                    url: '<?= Route::url('/regiweb/options/classplan/includes/index.php') ?>',
                    type: 'POST',
                    data: {
                        action: 'getPlan',
                        planId: planId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            fillForm(response.data);
                            isNewInput.val('0');
                            classPlanForm.removeClass('d-none');
                            btnSaveText.text('Guardar');
                            btnPrint.removeClass('d-none');
                            btnPrint.attr('href', 'pdf.php?id=' + planId);
                            $('#btnPrintTop').removeClass('d-none').attr('href', 'pdf.php?id=' + planId);
                            btnDelete.removeClass('d-none');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Error al cargar el plan de clase'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al cargar el plan de clase'
                        });
                    }
                });
            }

            // Borrar plan
            btnDelete.click(function() {
                const planId = planSelect.val();
                if (!planId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Seleccione un plan de clase',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    return;
                }

                Swal.fire({
                    title: '¿Está seguro que desea eliminar el plan de clase?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Borrar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '<?= Route::url('/regiweb/options/classplan/includes/index.php') ?>',
                            type: 'POST',
                            data: {
                                action: 'deletePlan',
                                planId: planId
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Eliminado',
                                        text: response.message
                                    }).then(() => {
                                        window.location.href = window.location.pathname;
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message || 'Error al eliminar el plan'
                                    });
                                }
                            },
                            error: function(error) {
                                console.error('Error details:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Error al procesar la solicitud'
                                });
                            }
                        });
                    }
                });
            });

            // Guardar/Crear plan
            classPlanForm.submit(function(e) {
                e.preventDefault();
                const formData = $(this).serializeArray();
                const isNew = isNewInput.val() === '1';

                // Convertir checkboxes a valores
                const checkboxes = ['tarea1', 'tarea2', 'tarea3', 'tarea4', 'tarea5', 'tarea6',
                    'tarea7', 'tarea8', 'tarea9', 'tarea10', 'tarea11', 'tarea12', 'tarea13', 'tarea14'
                ];

                // Agregar checkboxes de pensamiento para cada día
                for (let i = 1; i <= 5; i++) {
                    for (let j = 1; j <= 4; j++) {
                        checkboxes.push('pensamiento' + i + '_' + j);
                    }
                }

                let data = {};
                formData.forEach(item => {
                    data[item.name] = item.value;
                });

                checkboxes.forEach(name => {
                    if (!data[name]) {
                        data[name] = $('#' + name).is(':checked') ? 'si' : 'no';
                    }
                });

                if (isNew) {
                    data.action = 'createPlan';
                } else {
                    data.action = 'updatePlan';
                    data.planId = planIdInput.val();
                }
                console.log('Data to be sent:', data);
                $.ajax({
                    url: '<?= Route::url('/regiweb/options/classplan/includes/index.php') ?>',
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        console.log('Response:', response);
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: 'Plan de clase ' + (isNew ? 'creado' : 'guardado') + ' correctamente'
                            }).then(() => {
                                const planId = response.planId || planIdInput.val();
                                window.location.href = window.location.pathname + '?plan=' + planId;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Error al guardar'
                            });
                        }
                    },
                    error: function(data, textStatus, errorThrown) {
                        console.error('Error details form:', {
                            data,
                            textStatus,
                            errorThrown
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al procesar la solicitud'
                        });
                    }
                });
            });

            function fillForm(data) {
                planIdInput.val(data.id);

                // Llenar campos de texto usando name en lugar de id
                Object.keys(data).forEach(key => {
                    if (key === 'actividad_antes1') {
                        console.log('Filling field:', key, 'with value:', data[key]);
                    }
                    const input = $('[name="' + key + '"]');
                    if (input.length && input.attr('type') !== 'checkbox') {
                        input.val(data[key]);
                    } else if (input.length && input.attr('type') === 'checkbox') {
                        $('[name="' + key + '"]').prop('checked', data[key] === 'si');
                    }
                });
            }

            function clearForm() {
                classPlanForm[0].reset();
                planIdInput.val('');
                $('input[type="checkbox"]').prop('checked', false);
            }
        });
    </script>
</body>

</html>