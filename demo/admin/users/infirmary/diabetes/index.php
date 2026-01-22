<?php

require_once __DIR__ . '/../../../../app.php';

use App\Models\Admin;
use App\Models\DiabetesExercise;
use App\Models\DiabetesInfo;
use App\Models\DiabetesInsulin;
use App\Models\DiabetesInsulinPump;
use App\Models\Student;
use Classes\Route;
use Classes\Session;

Session::is_logged();

// Get current school year
$school = Admin::primaryAdmin();
$currentYear = $school->year ?? '';

// Check if searching
$selectedSS = $_GET['ss'] ?? '';
$student = null;
$diabetesInfo = null;
$diabetesExercise = null;
$diabetesInsulin = null;
$diabetesInsulinPump = null;

// Get all active students
$students = Student::query()->orderBy('apellidos')->orderBy('nombre')->get();

if (!empty($selectedSS)) {
    // Get selected student
    $student = Student::where('ss', $selectedSS)->first();

    if ($student) {
        // Get existing records
        $diabetesInfo = DiabetesInfo::findByStudent($student->id, $student->ss);
        $diabetesExercise = DiabetesExercise::findByStudent($student->id, $student->ss);
        $diabetesInsulin = DiabetesInsulin::findByStudent($student->id, $student->ss);
        $diabetesInsulinPump = DiabetesInsulinPump::findByStudent($student->id, $student->ss);
    }
}

// Get active tab from URL
$activeTab = $_GET['tab'] ?? 'info';

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Plan Médico para el Manejo de la Diabetes");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
    <style>
        .tab-pane {
            padding: 20px 0;
        }
    </style>
</head>

<body class="pb-5">
    <?php Route::includeFile('/admin/includes/layouts/menu.php'); ?>

    <div class="container-fluid mt-5">
        <h1 class="text-center mb-4"><?= __("Departamento de Enfermería") ?></h1>
        <h4 class="text-center mb-4"><?= __("Plan Médico para el Manejo de la Diabetes") ?></h4>

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

        <!-- Student Selection -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><?= __("Seleccionar Estudiante") ?></h5>
            </div>
            <div class="card-body">
                <form action="" method="GET">
                    <input type="hidden" name="tab" value="<?= $activeTab ?>">
                    <div class="row">
                        <div class="col-md-10">
                            <select name="ss" class="form-control" required>
                                <option value=""><?= __("-- Seleccione --") ?></option>
                                <?php foreach ($students as $s): ?>
                                    <option value="<?= $s->ss ?>" <?= $selectedSS == $s->ss ? 'selected' : '' ?>>
                                        <?= $s->apellidos . ', ' . $s->nombre . ' - ID: ' . $s->id ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> <?= __("Buscar") ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($student): ?>
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link <?= $activeTab === 'info' ? 'active' : '' ?>"
                        href="?ss=<?= $selectedSS ?>&tab=info">
                        <i class="fas fa-info-circle"></i> <?= __("Información de Contacto") ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $activeTab === 'insulin' ? 'active' : '' ?>"
                        href="?ss=<?= $selectedSS ?>&tab=insulin">
                        <i class="fas fa-syringe"></i> <?= __("Insulina") ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $activeTab === 'pump' ? 'active' : '' ?>"
                        href="?ss=<?= $selectedSS ?>&tab=pump">
                        <i class="fas fa-heartbeat"></i> <?= __("Bomba de Insulina") ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $activeTab === 'exercise' ? 'active' : '' ?>"
                        href="?ss=<?= $selectedSS ?>&tab=exercise">
                        <i class="fas fa-running"></i> <?= __("Ejercicio y Deportes") ?>
                    </a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content bg-white border border-top-0 p-4">

                <!-- Información de Contacto Tab -->
                <?php if ($activeTab === 'info'): ?>
                    <div class="tab-pane active">
                        <h5 class="mb-4"><?= __("INFORMACIÓN DE CONTACTO") ?></h5>
                        <form action="<?= Route::url('/admin/users/infirmary/diabetes/includes/save_info.php') ?>" method="POST">
                            <input type="hidden" name="id" value="<?= $student->id ?>">
                            <input type="hidden" name="ss" value="<?= $student->ss ?>">

                            <!-- Student and Family Info (Read-only) -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p><strong>Fecha de Nacimiento:</strong> <?= $student->fecha ?></p>
                                            <p><strong>Grado:</strong> <?= $student->grado ?></p>
                                        </div>
                                        <div class="col-md-8">
                                            <?php $family = $student->family; ?>
                                            <?php if ($family): ?>
                                                <p><strong>Madre:</strong> <?= $family->madre ?? '' ?></p>
                                                <p><strong>Dirección:</strong> <?= $family->dir_madre ?? '' ?> <?= $family->urb1 ?? '' ?><br>
                                                   <?= $family->pueblo_madre ?? '' ?> P.R. <?= $family->zip1 ?? '' ?></p>
                                                <p><strong>Tel. Casa:</strong> <?= $family->tel_casa1 ?? '' ?> 
                                                   <strong>Tel. Trabajo:</strong> <?= $family->tel_trabajo1 ?? '' ?> 
                                                   <strong>Cel.:</strong> <?= $family->cel_madre ?? '' ?></p>
                                                
                                                <p><strong>Padre:</strong> <?= $family->padre ?? '' ?></p>
                                                <p><strong>Dirección:</strong> <?= $family->dir_padre ?? '' ?> <?= $family->urb2 ?? '' ?><br>
                                                   <?= $family->pueblo2 ?? '' ?> P.R. <?= $family->zip3 ?? '' ?></p>
                                                <p><strong>Tel. Casa:</strong> <?= $family->tel_casa2 ?? '' ?> 
                                                   <strong>Tel. Trabajo:</strong> <?= $family->tel_trabajo2 ?? '' ?> 
                                                   <strong>Cel.:</strong> <?= $family->cel_padre ?? '' ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h6 class="mb-3"><strong>Fechas en que este plan está en efecto son:</strong></h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha1">Desde:</label>
                                        <input type="date" class="form-control" id="fecha1" name="fecha1"
                                            value="<?= $diabetesInfo && $diabetesInfo->fecha1 ? $diabetesInfo->fecha1->format('Y-m-d') : '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha2">Hasta:</label>
                                        <input type="date" class="form-control" id="fecha2" name="fecha2"
                                            value="<?= $diabetesInfo && $diabetesInfo->fecha2 ? $diabetesInfo->fecha2->format('Y-m-d') : '' ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha3">Fecha en que la Diabetes fue diagnosticada:</label>
                                        <input type="date" class="form-control" id="fecha3" name="fecha3"
                                            value="<?= $diabetesInfo && $diabetesInfo->fecha3 ? $diabetesInfo->fecha3->format('Y-m-d') : '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Condición física:</strong></label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input diabetes-type" id="diabetes_tipo1" name="diabetes" value="Tipo 1"
                                                    <?= ($diabetesInfo && $diabetesInfo->diabetes == 'Tipo 1') ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="diabetes_tipo1">Diabetes Tipo 1</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input diabetes-type" id="diabetes_tipo2" name="diabetes" value="Tipo 2"
                                                    <?= ($diabetesInfo && $diabetesInfo->diabetes == 'Tipo 2') ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="diabetes_tipo2">Diabetes Tipo 2</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h6 class="mb-3"><strong>Doctor del Estudiante/Proveedor de Cuidado Médico</strong></h6>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="doctor">Nombre del Doctor:</label>
                                        <input type="text" class="form-control" id="doctor" name="doctor"
                                            value="<?= $diabetesInfo->doctor ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tel">Teléfono:</label>
                                        <input type="text" class="form-control" id="tel" name="tel_doc"
                                            value="<?= $diabetesInfo->tel_doc ?? '' ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="direccion_doc">Dirección:</label>
                                        <input type="text" class="form-control" id="direccion_doc" name="direccion"
                                            value="<?= $diabetesInfo->direccion ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tel_emer">Tel. de Emergencia:</label>
                                        <input type="text" class="form-control" id="tel_emer" name="tel_emer"
                                            value="<?= $diabetesInfo->tel_emer ?? '' ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="calle_doc">Calle:</label>
                                        <input type="text" class="form-control" id="calle_doc" name="calle"
                                            value="<?= $diabetesInfo->calle ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="pueblo_doc">Pueblo:</label>
                                        <input type="text" class="form-control" id="pueblo_doc" name="pueblo"
                                            value="<?= $diabetesInfo->pueblo ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cpostal_doc">Código Postal:</label>
                                        <input type="text" class="form-control" id="cpostal_doc" name="postal"
                                            value="<?= $diabetesInfo->postal ?? '' ?>">
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h6 class="mb-3"><strong>Otros Contactos en Caso de Emergencia</strong></h6>
                            <?php if ($family): ?>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <p><strong>Nombre:</strong> <?= $family->emergencia ?? '' ?></p>
                                        <p><strong>Relación:</strong> <?= $family->emer_contacto ?? '' ?></p>
                                        <p><strong>Tel. Casa:</strong> <?= $family->emer_tel_casa ?? '' ?> 
                                           <strong>Tel.Trabajo:</strong> <?= $family->emer_trabajo ?? '' ?> 
                                           <strong>Cel.:</strong> <?= $family->emer_cel ?? '' ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="notifique"><strong>Notifique al padre/encargado o contacto de emergencia en las siguientes situaciones:</strong></label>
                                <textarea class="form-control" id="notifique" name="notificacion" rows="5"><?= $diabetesInfo->notificacion ?? '' ?></textarea>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" name="save" class="btn btn-success btn-lg">
                                    <i class="fas fa-save"></i> <?= __("Guardar") ?>
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

                <!-- Exercise Tab -->
                <?php if ($activeTab === 'exercise'): ?>
                    <div class="tab-pane active">
                        <h5 class="mb-4">EJERCICIOS Y DEPORTES</h5>
                        <form action="<?= Route::url('/admin/users/infirmary/diabetes/includes/save_exercise.php') ?>" method="POST">
                            <input type="hidden" name="id" value="<?= $student->id ?>">
                            <input type="hidden" name="ss" value="<?= $student->ss ?>">

                            <div class="form-group">
                                <label>Un carbohidrato de acción rápida como <input type="text" name="carb" style="width: 150px; display: inline;" 
                                    class="form-control d-inline" value="<?= $diabetesExercise->carb ?? '' ?>"> debe estar disponible en el lugar que se haga ejercicio o deportes.</label>
                            </div>

                            <div class="form-group">
                                <label for="actividad">Restricciones en actividad, si las hay:</label>
                                <textarea class="form-control" id="actividad" name="actividad" rows="3"><?= $diabetesExercise->actividad ?? '' ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>
                                    El estudiante no debe ejercitarse si el nivel de glucosa está debajo de 
                                    <input type="text" name="glucosa_min" style="width: 100px;" class="form-control d-inline" 
                                        value="<?= $diabetesExercise->glucosa_min ?? '' ?>"> mg/dl 
                                    o arriba de 
                                    <input type="text" name="glucosa_max" style="width: 100px;" class="form-control d-inline" 
                                        value="<?= $diabetesExercise->glucosa_max ?? '' ?>"> mg/dl
                                </label>
                            </div>

                            <hr class="my-4">
                            <h6 class="bg-secondary text-white p-2 text-center"><strong>HIPOGLUCEMIA (BAJO NIVEL DE AZUCAR EN LA SANGRE)</strong></h6>

                            <div class="form-group">
                                <label for="sintomas_hipo">Sintomas usuales de hipoglucemia:</label>
                                <textarea class="form-control" id="sintomas_hipo" name="sintomas_hipo" rows="3"><?= $diabetesExercise->sintomas_hipo ?? '' ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="tratamiento_hipo">Tratamiento de hipoglucemia:</label>
                                <textarea class="form-control" id="tratamiento_hipo" name="tratamiento_hipo" rows="3"><?= $diabetesExercise->tratamiento_hipo ?? '' ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>
                                    <input type="text" name="dosis" style="width: 120px;" class="form-control d-inline" 
                                        value="<?= $diabetesExercise->dosis ?? '' ?>"> (Dosis) 
                                    Se debe aplicar Glucagón intramuscularmente (IM) si el estudiante está inconsciente, 
                                    tiene una convulsión o no puede tragar. El lugar para una inyección de Glucagón puede ser el brazo, 
                                    el muslo o el glúteo. <strong>Si se requiere glucagón, adminístrelo simultáneamente mientras llama al 911 
                                    y a los padres/guardianes.</strong>
                                </label>
                            </div>

                            <hr class="my-4">
                            <h6 class="bg-secondary text-white p-2 text-center"><strong>HIPERGLUCEMIA (ALTO NIVEL DE AZUCAR EN LA SANGRE)</strong></h6>

                            <div class="form-group">
                                <label for="sintomas_hiper">Sintomas usuales de hiperglucemia:</label>
                                <textarea class="form-control" id="sintomas_hiper" name="sintomas_hiper" rows="3"><?= $diabetesExercise->sintomas_hiper ?? '' ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="tratamiento_hiper">Tratamiento de hiperglucemia:</label>
                                <textarea class="form-control" id="tratamiento_hiper" name="tratamiento_hiper" rows="3"><?= $diabetesExercise->tratamiento_hiper ?? '' ?></textarea>
                            </div>

                            <div class="form-group">
                                <p class="text-center"><strong>*** Llame a los padres si el nivel de azucar es más alto que 
                                <input type="text" name="azucar" style="width: 100px;" class="form-control d-inline" 
                                    value="<?= $diabetesExercise->azucar ?? '' ?>"> mg/dl</strong></p>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" name="save" class="btn btn-success btn-lg">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

                <!-- Insulin Tab -->
                <?php if ($activeTab === 'insulin'): ?>
                    <div class="tab-pane active">
                        <h5 class="mb-4">VIGILANDO EL NIVEL DE GLUCOSA EN LA SANGRE</h5>
                        <form action="<?= Route::url('/admin/users/infirmary/diabetes/includes/save_insulin.php') ?>" method="POST">
                            <input type="hidden" name="id" value="<?= $student->id ?>">
                            <input type="hidden" name="ss" value="<?= $student->ss ?>">

                            <div class="form-group">
                                <label>El rango meta para la glucosa en la sangre es: 
                                <input type="text" name="rango" class="form-control d-inline" style="width: 200px;"
                                    value="<?= $diabetesInsulin->rango ?? '' ?>"></label>
                            </div>

                            <div class="form-group">
                                <label>Las horas de rutina para revisar la glucosa en la sangre en la escuela son: 
                                <select name="horas" class="form-control d-inline" style="width: 80px;">
                                    <option value=""><?= $diabetesInsulin->horas ?? '' ?></option>
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option>5</option>
                                    <option>6</option>
                                    <option>7</option>
                                    <option>8</option>
                                    <option>9</option>
                                </select></label>
                            </div>

                            <hr class="my-4">
                            <p><strong>Las horas extra para revisar la glucosa en la sangre son: (marque todas las que aplican)</strong></p>
                            
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="ejer1" name="ejer1" value="1"
                                    <?= ($diabetesInsulin && $diabetesInsulin->ejer1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="ejer1">antes de hacer ejercicio</label>
                            </div>
                            
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="ejer2" name="ejer2" value="1"
                                    <?= ($diabetesInsulin && $diabetesInsulin->ejer2) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="ejer2">después de hacer ejercicio</label>
                            </div>
                            
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="hiper" name="hiper" value="1"
                                    <?= ($diabetesInsulin && $diabetesInsulin->hiper) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="hiper">cuando el estudiante muestra síntomas de hiperglucemia</label>
                            </div>
                            
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="hipo" name="hipo" value="1"
                                    <?= ($diabetesInsulin && $diabetesInsulin->hipo) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="hipo">cuando el estudiante muestra sintomas de hipoglucemia</label>
                            </div>
                            
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="otro" name="otro" value="1"
                                    <?= ($diabetesInsulin && $diabetesInsulin->otro) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="otro">otro (explique)</label>
                            </div>
                            <div class="form-group ml-4">
                                <input type="text" class="form-control" id="otro2" name="otro2" placeholder="Especifique otro"
                                    value="<?= $diabetesInsulin->otro2 ?? '' ?>">
                            </div>

                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="gluc1" name="gluc1" value="1"
                                    <?= ($diabetesInsulin && $diabetesInsulin->gluc1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="gluc1">El estudinate puede llevar a cabo su revisión de glucosa en la sangre con supervisión</label>
                            </div>
                            
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="gluc2" name="gluc2" value="1"
                                    <?= ($diabetesInsulin && $diabetesInsulin->gluc2) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="gluc2">El estudinate puede llevar a cabo su revisión de glucosa en la sangre sin supervisión</label>
                            </div>
                            
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="gluc3" name="gluc3" value="1"
                                    <?= ($diabetesInsulin && $diabetesInsulin->gluc3) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="gluc3">El personal de la escuela debe llevar a cabo las revisiones de sangre</label>
                            </div>
                            
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="exc1" name="exc1" value="1"
                                    <?= ($diabetesInsulin && $diabetesInsulin->exc1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="exc1">Excepciones:</label>
                            </div>
                            <div class="form-group ml-4">
                                <input type="text" class="form-control" id="exc2" name="exc2" 
                                    value="<?= $diabetesInsulin->exc2 ?? '' ?>">
                            </div>

                            <div class="form-group">
                                <label for="gluc_med">Tipo de mediciones de glucosa en la sangre utilizadas:</label>
                                <input type="text" class="form-control" id="gluc_med" name="gluc_med"
                                    value="<?= $diabetesInsulin->gluc_med ?? '' ?>">
                            </div>

                            <hr class="my-4">
                            <h6 class="bg-secondary text-white p-2 text-center"><strong>PARA LOS QUE UTILIZAN INJECCIONES DE INSULINA</strong></h6>
                            
                            <p><strong>Dosis para la hora del Almuerzo</strong></p>
                            
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="ins1" name="ins1" value="1"
                                    <?= ($diabetesInsulin && $diabetesInsulin->ins1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="ins1">
                                    La dosis básica de insulina que tiene que darse es:
                                </label>
                            </div>
                            <div class="form-row ml-4 mb-3">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="ins1_n" placeholder="(nombre)"
                                        value="<?= $diabetesInsulin->ins1_n ?? '' ?>">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="ins1_u" placeholder="unidades"
                                        value="<?= $diabetesInsulin->ins1_u ?? '' ?>">
                                </div>
                            </div>

                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="ins2" name="ins2" value="1"
                                    <?= ($diabetesInsulin && $diabetesInsulin->ins2) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="ins2">
                                    La dosis flexible de insulina que tiene que darse es:
                                </label>
                            </div>
                            <div class="form-row ml-4 mb-3">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="ins2_n" placeholder="(nombre)"
                                        value="<?= $diabetesInsulin->ins2_n ?? '' ?>">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="ins2_u" placeholder="unidades"
                                        value="<?= $diabetesInsulin->ins2_u ?? '' ?>">
                                </div>
                            </div>
                            <p class="ml-4">gramos de carbohidratos.</p>

                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="ins3" name="ins3" value="1"
                                    <?= ($diabetesInsulin && $diabetesInsulin->ins3) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="ins3">
                                    Otra insulina que tiene que darse:
                                </label>
                            </div>
                            <div class="form-row ml-4 mb-3">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="ins3_n" placeholder="(nombre)"
                                        value="<?= $diabetesInsulin->ins3_n ?? '' ?>">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="ins3_u" placeholder="unidades"
                                        value="<?= $diabetesInsulin->ins3_u ?? '' ?>">
                                </div>
                            </div>

                            <hr class="my-4">
                            <p><strong>Corrección de Insulina</strong> (Escala Variable)</p>
                            
                            <div class="form-group">
                                <label for="insulina">El nombre de la insulina a ser dada es:</label>
                                <input type="text" class="form-control" id="insulina" name="insulina"
                                    value="<?= $diabetesInsulin->insulina ?? '' ?>">
                            </div>

                            <div class="form-group">
                                <input type="text" name="insuni1" class="form-control d-inline" style="width: 100px;" value="<?= $diabetesInsulin->insuni1 ?? '' ?>"> unidades si la glucosa en la sangre es 
                                <input type="text" name="insuni2" class="form-control d-inline" style="width: 100px;" value="<?= $diabetesInsulin->insuni2 ?? '' ?>"> hasta 
                                <input type="text" name="insuni3" class="form-control d-inline" style="width: 100px;" value="<?= $diabetesInsulin->insuni3 ?? '' ?>"> mg./dl
                            </div>

                            <div class="form-group">
                                <input type="text" name="insuni4" class="form-control d-inline" style="width: 100px;" value="<?= $diabetesInsulin->insuni4 ?? '' ?>"> unidades si la glucosa en la sangre es 
                                <input type="text" name="insuni5" class="form-control d-inline" style="width: 100px;" value="<?= $diabetesInsulin->insuni5 ?? '' ?>"> hasta 
                                <input type="text" name="insuni6" class="form-control d-inline" style="width: 100px;" value="<?= $diabetesInsulin->insuni6 ?? '' ?>"> mg./dl
                            </div>

                            <div class="form-group">
                                <input type="text" name="insuni7" class="form-control d-inline" style="width: 100px;" value="<?= $diabetesInsulin->insuni7 ?? '' ?>"> unidades si la glucosa en la sangre es 
                                <input type="text" name="insuni8" class="form-control d-inline" style="width: 100px;" value="<?= $diabetesInsulin->insuni8 ?? '' ?>"> hasta 
                                <input type="text" name="insuni9" class="form-control d-inline" style="width: 100px;" value="<?= $diabetesInsulin->insuni9 ?? '' ?>"> mg./dl
                            </div>

                            <div class="form-group">
                                <input type="text" name="insuni10" class="form-control d-inline" style="width: 100px;" value="<?= $diabetesInsulin->insuni10 ?? '' ?>"> unidades si la glucosa en la sangre es 
                                <input type="text" name="insuni11" class="form-control d-inline" style="width: 100px;" value="<?= $diabetesInsulin->insuni11 ?? '' ?>"> hasta 
                                <input type="text" name="insuni12" class="form-control d-inline" style="width: 100px;" value="<?= $diabetesInsulin->insuni12 ?? '' ?>"> mg./dl
                            </div>

                            <div class="form-group">
                                <input type="text" name="insuni13" class="form-control d-inline" style="width: 100px;" value="<?= $diabetesInsulin->insuni13 ?? '' ?>"> unidades si la glucosa en la sangre es 
                                <input type="text" name="insuni14" class="form-control d-inline" style="width: 100px;" value="<?= $diabetesInsulin->insuni14 ?? '' ?>"> hasta 
                                <input type="text" name="insuni15" class="form-control d-inline" style="width: 100px;" value="<?= $diabetesInsulin->insuni15 ?? '' ?>"> mg./dl
                            </div>

                            <hr class="my-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="insu1" name="insu1" value="1"
                                    <?= ($diabetesInsulin && $diabetesInsulin->insu1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="insu1">El estudiante puede ponerse sus propias injecciones.</label>
                            </div>
                            
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="insu2" name="insu2" value="1"
                                    <?= ($diabetesInsulin && $diabetesInsulin->insu2) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="insu2">El estudiante puede ponerse su propia inyección con supervisión.</label>
                            </div>
                            
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="insu3" name="insu3" value="1"
                                    <?= ($diabetesInsulin && $diabetesInsulin->insu3) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="insu3">El personal de la escuela debe administrar las inyecciones.</label>
                            </div>

                            <br>

                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="insu4" name="insu4" value="1"
                                    <?= ($diabetesInsulin && $diabetesInsulin->insu4) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="insu4">El estudiante puede determinar la cantidad correcta de insulina.</label>
                            </div>
                            
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="insu5" name="insu5" value="1"
                                    <?= ($diabetesInsulin && $diabetesInsulin->insu5) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="insu5">El estudiante puede determinar la cantidad correcta de insulina con supervisión.</label>
                            </div>
                            
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="insu6" name="insu6" value="1"
                                    <?= ($diabetesInsulin && $diabetesInsulin->insu6) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="insu6">El personal escolar debe determinarse la cantidad correcta de insulina.</label>
                            </div>

                            <br>

                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="insu7" name="insu7" value="1"
                                    <?= ($diabetesInsulin && $diabetesInsulin->insu7) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="insu7">El estudiante puede extraer la dosis correcta de insulina.</label>
                            </div>
                            
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="insu8" name="insu8" value="1"
                                    <?= ($diabetesInsulin && $diabetesInsulin->insu8) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="insu8">El estudiante puede extraer la dosis correcta de insulina con supervisión.</label>
                            </div>
                            
                            <div class="form-check mb-4">
                                <input type="checkbox" class="form-check-input" id="insu9" name="insu9" value="1"
                                    <?= ($diabetesInsulin && $diabetesInsulin->insu9) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="insu9">El personal escolar debe extraer la dosis correcta de insulina.</label>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" name="save" class="btn btn-success btn-lg">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

                <!-- Insulin Pump Tab -->
                <?php if ($activeTab === 'pump'): ?>
                    <div class="tab-pane active">
                        <h5 class="mb-4 bg-secondary text-white p-2 text-center">ESTUDIANTES CON BOMBAS DE INSULINA</h5>
                        <form action="<?= Route::url('/admin/users/infirmary/diabetes/includes/save_pump.php') ?>" method="POST">
                            <input type="hidden" name="id" value="<?= $student->id ?>">
                            <input type="hidden" name="ss" value="<?= $student->ss ?>">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tbomba">Tipo de bomba:</label>
                                        <input type="text" class="form-control" id="tbomba" name="tbomba"
                                            value="<?= $diabetesInsulinPump->tbomba ?? '' ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="tinsulina">Tipo de insulina en la bomba:</label>
                                        <input type="text" class="form-control" id="tinsulina" name="tinsulina"
                                            value="<?= $diabetesInsulinPump->tinsulina ?? '' ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="infusion">Tipo de equipo de infusión:</label>
                                        <input type="text" class="form-control" id="infusion" name="infusion"
                                            value="<?= $diabetesInsulinPump->infusion ?? '' ?>">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label><strong>Tasa basal:</strong></label>
                                    <div class="form-group">
                                        <input type="text" name="basal1" class="form-control d-inline" style="width: 80px;" 
                                            value="<?= $diabetesInsulinPump->basal1 ?? '' ?>"> unidades/hora 12 am a 
                                        <input type="text" name="basal2" class="form-control d-inline" style="width: 80px;" 
                                            value="<?= $diabetesInsulinPump->basal2 ?? '' ?>">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="basal3" class="form-control d-inline" style="width: 80px;" 
                                            value="<?= $diabetesInsulinPump->basal3 ?? '' ?>"> unidades/hora 
                                        <input type="text" name="basal4" class="form-control d-inline" style="width: 80px;" 
                                            value="<?= $diabetesInsulinPump->basal4 ?? '' ?>"> a 
                                        <input type="text" name="basal5" class="form-control d-inline" style="width: 80px;" 
                                            value="<?= $diabetesInsulinPump->basal5 ?? '' ?>">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="basal6" class="form-control d-inline" style="width: 80px;" 
                                            value="<?= $diabetesInsulinPump->basal6 ?? '' ?>"> unidades/hora 
                                        <input type="text" name="basal7" class="form-control d-inline" style="width: 80px;" 
                                            value="<?= $diabetesInsulinPump->basal7 ?? '' ?>"> a 
                                        <input type="text" name="basal8" class="form-control d-inline" style="width: 80px;" 
                                            value="<?= $diabetesInsulinPump->basal8 ?? '' ?>">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="basal9" class="form-control d-inline" style="width: 80px;" 
                                            value="<?= $diabetesInsulinPump->basal9 ?? '' ?>"> unidades/hora 
                                        <input type="text" name="basal10" class="form-control d-inline" style="width: 80px;" 
                                            value="<?= $diabetesInsulinPump->basal10 ?? '' ?>"> a 
                                        <input type="text" name="basal11" class="form-control d-inline" style="width: 80px;" 
                                            value="<?= $diabetesInsulinPump->basal11 ?? '' ?>">
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="racion">Ración de carbohidrato/insulina:</label>
                                        <input type="text" class="form-control" id="racion" name="racion"
                                            value="<?= $diabetesInsulinPump->racion ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="equipo">Introduciendo el equipo usado:</label>
                                        <input type="text" class="form-control" id="equipo" name="equipo"
                                            value="<?= $diabetesInsulinPump->equipo ?? '' ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="factor">Factor de corrección:</label>
                                        <input type="text" class="form-control" id="factor" name="factor"
                                            value="<?= $diabetesInsulinPump->factor ?? '' ?>">
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h6 class="bg-secondary text-white p-2">Habilidades/Destrezas del Estudiante para usar la bomba</h6>
                            
                            <table class="table table-bordered">
                                <thead class="bg-secondary text-white">
                                    <tr>
                                        <th style="width: 60%;">Habilidad</th>
                                        <th style="width: 40%;">Necesita Asistencia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Contar carbohidratos</td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input checkbox-si-no" name="carb" value="Si"
                                                    <?= ($diabetesInsulinPump && $diabetesInsulinPump->carb == 'Si') ? 'checked' : '' ?>>
                                                <label class="form-check-label">Si</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input checkbox-si-no" name="carb" value="No"
                                                    <?= ($diabetesInsulinPump && $diabetesInsulinPump->carb == 'No') ? 'checked' : '' ?>>
                                                <label class="form-check-label">No</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Cantidad correcta de bolo para carbohidratos</td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input checkbox-si-no" name="bcarb" value="Si"
                                                    <?= ($diabetesInsulinPump && $diabetesInsulinPump->bcarb == 'Si') ? 'checked' : '' ?>>
                                                <label class="form-check-label">Si</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input checkbox-si-no" name="bcarb" value="No"
                                                    <?= ($diabetesInsulinPump && $diabetesInsulinPump->bcarb == 'No') ? 'checked' : '' ?>>
                                                <label class="form-check-label">No</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Calcular y administrar el bolo correcto</td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input checkbox-si-no" name="bcorrec" value="Si"
                                                    <?= ($diabetesInsulinPump && $diabetesInsulinPump->bcorrec == 'Si') ? 'checked' : '' ?>>
                                                <label class="form-check-label">Si</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input checkbox-si-no" name="bcorrec" value="No"
                                                    <?= ($diabetesInsulinPump && $diabetesInsulinPump->bcorrec == 'No') ? 'checked' : '' ?>>
                                                <label class="form-check-label">No</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Calcular y establecer los perfiles basales</td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input checkbox-si-no" name="pbasales" value="Si"
                                                    <?= ($diabetesInsulinPump && $diabetesInsulinPump->pbasales == 'Si') ? 'checked' : '' ?>>
                                                <label class="form-check-label">Si</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input checkbox-si-no" name="pbasales" value="No"
                                                    <?= ($diabetesInsulinPump && $diabetesInsulinPump->pbasales == 'No') ? 'checked' : '' ?>>
                                                <label class="form-check-label">No</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Calcular y establecer al tasa basal temporal</td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input checkbox-si-no" name="btemp" value="Si"
                                                    <?= ($diabetesInsulinPump && $diabetesInsulinPump->btemp == 'Si') ? 'checked' : '' ?>>
                                                <label class="form-check-label">Si</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input checkbox-si-no" name="btemp" value="No"
                                                    <?= ($diabetesInsulinPump && $diabetesInsulinPump->btemp == 'No') ? 'checked' : '' ?>>
                                                <label class="form-check-label">No</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Desconectar la bomba</td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input checkbox-si-no" name="dbomba" value="Si"
                                                    <?= ($diabetesInsulinPump && $diabetesInsulinPump->dbomba == 'Si') ? 'checked' : '' ?>>
                                                <label class="form-check-label">Si</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input checkbox-si-no" name="dbomba" value="No"
                                                    <?= ($diabetesInsulinPump && $diabetesInsulinPump->dbomba == 'No') ? 'checked' : '' ?>>
                                                <label class="form-check-label">No</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Volver a conectar el equipo de infusión</td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input checkbox-si-no" name="einfu" value="Si"
                                                    <?= ($diabetesInsulinPump && $diabetesInsulinPump->einfu == 'Si') ? 'checked' : '' ?>>
                                                <label class="form-check-label">Si</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input checkbox-si-no" name="einfu" value="No"
                                                    <?= ($diabetesInsulinPump && $diabetesInsulinPump->einfu == 'No') ? 'checked' : '' ?>>
                                                <label class="form-check-label">No</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Preparar el tanque y los tubos</td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input checkbox-si-no" name="tubos" value="Si"
                                                    <?= ($diabetesInsulinPump && $diabetesInsulinPump->tubos == 'Si') ? 'checked' : '' ?>>
                                                <label class="form-check-label">Si</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input checkbox-si-no" name="tubos" value="No"
                                                    <?= ($diabetesInsulinPump && $diabetesInsulinPump->tubos == 'No') ? 'checked' : '' ?>>
                                                <label class="form-check-label">No</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Introducir el equipo de infusión</td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input checkbox-si-no" name="intinf" value="Si"
                                                    <?= ($diabetesInsulinPump && $diabetesInsulinPump->intinf == 'Si') ? 'checked' : '' ?>>
                                                <label class="form-check-label">Si</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input checkbox-si-no" name="intinf" value="No"
                                                    <?= ($diabetesInsulinPump && $diabetesInsulinPump->intinf == 'No') ? 'checked' : '' ?>>
                                                <label class="form-check-label">No</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Revisar alarmas o errores de funcionamiento</td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input checkbox-si-no" name="alarmas" value="Si"
                                                    <?= ($diabetesInsulinPump && $diabetesInsulinPump->alarmas == 'Si') ? 'checked' : '' ?>>
                                                <label class="form-check-label">Si</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input checkbox-si-no" name="alarmas" value="No"
                                                    <?= ($diabetesInsulinPump && $diabetesInsulinPump->alarmas == 'No') ? 'checked' : '' ?>>
                                                <label class="form-check-label">No</label>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <hr class="my-4">
                            <h6 class="bg-secondary text-white p-2"><strong>PARA ESTUDIANTES TOMANDO MEDICINA ORAL PARA LA DIABETES</strong></h6>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="med">Tipo de medicina:</label>
                                        <input type="text" class="form-control" id="med" name="med"
                                            value="<?= $diabetesInsulinPump->med ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hmed">Hora tomada:</label>
                                        <input type="time" class="form-control" id="hmed" name="hmed"
                                            value="<?= $diabetesInsulinPump->hmed ?? '' ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="omed">Otras medicinas:</label>
                                        <input type="text" class="form-control" id="omed" name="omed"
                                            value="<?= $diabetesInsulinPump->omed ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ohmed">Hora tomada:</label>
                                        <input type="time" class="form-control" id="ohmed" name="ohmed"
                                            value="<?= $diabetesInsulinPump->ohmed ?? '' ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" name="save" class="btn btn-success btn-lg">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

            </div>
        <?php endif; ?>

        <div class="mt-4 text-center">
            <a href="<?= Route::url('/admin/users/infirmary/index.php') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> <?= __("Regresar al Menú de Enfermería") ?>
            </a>
        </div>
    </div>

    <?php Route::includeFile('/includes/layouts/scripts.php', true); ?>
    
    <script>
        $(document).ready(function() {
            // Handle diabetes type checkboxes (only one can be selected)
            $('.diabetes-type').on('click', function() {
                if ($(this).is(':checked')) {
                    $('.diabetes-type').prop('checked', false);
                    $(this).prop('checked', true);
                } else {
                    $(this).prop('checked', false);
                }
            });

            // Handle Si/No checkboxes in pump tab (only one can be selected per row)
            $('.checkbox-si-no').on('click', function() {
                if ($(this).is(':checked')) {
                    var name = $(this).attr('name');
                    $('input[name="' + name + '"]').prop('checked', false);
                    $(this).prop('checked', true);
                } else {
                    $(this).prop('checked', false);
                }
            });
        });
    </script>
</body>

</html>