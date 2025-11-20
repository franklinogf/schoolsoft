<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\Admin;
use App\Models\Teacher;
use App\Models\WorkPlan4;
use Classes\Route;
use Classes\Session;

Session::is_logged();

$teacher = Teacher::find(Session::id());
$school = Admin::primaryAdmin();
$year = $school->year;

// Get plan ID from URL
$planId = $_GET['plan'] ?? null;
$workPlan = null;

if ($planId) {
    $workPlan = WorkPlan4::query()
        ->where('id_profesor', $teacher->id)
        ->where('year', $school->year2)
        ->where('id', $planId)
        ->first();
}

// Get all plans for this teacher
$workPlans = WorkPlan4::query()
    ->where('id_profesor', $teacher->id)
    ->where('year', $school->year2)
    ->orderBy('id', 'DESC')
    ->get();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Plan de Trabajo - Artes Visuales");
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
    <style>
        .form-section {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .form-section h5 {
            margin-bottom: 15px;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
        }

        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
        }

        .control-buttons {
            position: sticky;
            top: 0;
            background: white;
            padding: 15px 0;
            z-index: 100;
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <div class="container-fluid mt-4">
        <div class="control-buttons row">
            <div class="col-md-3">
                <button type="button" class="btn btn-primary btn-block" id="newPlan">
                    <i class="fa fa-plus"></i> <?php echo __("Nuevo Plan") ?>
                </button>
            </div>
            <div class="col-md-6">
                <select class="form-control" id="planSelect">
                    <option value=""><?php echo __("Seleccionar plan...") ?></option>
                    <?php foreach ($workPlans as $plan): ?>
                        <option value="<?php echo $plan->id ?>" <?php echo ($planId == $plan->id) ? 'selected' : '' ?>>
                            <?php echo $plan->unidad ?> - <?php echo $plan->temas ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-3">
                <div class="btn-group btn-block" role="group">
                    <button type="button" class="btn btn-success" id="savePlan" style="display:none;">
                        <i class="fa fa-save"></i> <?php echo __("Guardar") ?>
                    </button>
                    <button type="button" class="btn btn-info" id="printPlan" style="display:none;">
                        <i class="fa fa-print"></i> <?php echo __("Imprimir") ?>
                    </button>
                    <button type="button" class="btn btn-danger" id="deletePlan" style="display:none;">
                        <i class="fa fa-trash"></i> <?php echo __("Eliminar") ?>
                    </button>
                </div>
            </div>
        </div>

        <form id="planForm" style="display:none;">
            <input type="hidden" id="planId" name="planId" value="<?php echo $workPlan->id ?? '' ?>">

            <h3 class="text-center mb-4"><?php echo __("PLAN DE TRABAJO - ARTES VISUALES") ?></h3>

            <!-- Información Básica -->
            <div class="form-section">
                <h5><?php echo __("Información Básica") ?></h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo __("Unidad") ?>:</label>
                            <input type="text" class="form-control" name="unidad" value="<?php echo $workPlan->unidad ?? '' ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo __("Temas") ?>:</label>
                            <input type="text" class="form-control" name="temas" value="<?php echo $workPlan->temas ?? '' ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label><?php echo __("Fecha") ?> <?php echo $i ?>:</label>
                                <input type="date" class="form-control" name="fecha<?php echo $i ?>" value="<?php echo $workPlan->{"fecha{$i}"} ?? '' ?>">
                            </div>
                        </div>
                    <?php endfor ?>
                </div>
            </div>

            <!-- Fase y Niveles -->
            <div class="form-section">
                <div class="row">
                    <div class="col-md-6">
                        <h5><?php echo __("Fase") ?></h5>
                        <div class="checkbox-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="fase1" <?php echo ($workPlan->fase1 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label"><?php echo __("Exploración") ?></label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="fase2" <?php echo ($workPlan->fase2 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label"><?php echo __("Antes") ?></label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="fase3" <?php echo ($workPlan->fase3 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label"><?php echo __("Enfocar") ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5><?php echo __("Niveles de Profundidad de Conocimiento") ?></h5>
                        <div class="checkbox-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="niveles1" <?php echo ($workPlan->niveles1 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label"><?php echo __("Memorístico") ?></label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="niveles2" <?php echo ($workPlan->niveles2 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label"><?php echo __("Estratégico") ?></label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="niveles3" <?php echo ($workPlan->niveles3 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label"><?php echo __("Procesamiento") ?></label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="niveles4" <?php echo ($workPlan->niveles4 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label"><?php echo __("Extendido") ?></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estándares y Expectativas -->
            <div class="form-section">
                <div class="row">
                    <div class="col-md-6">
                        <h5><?php echo __("Estándares") ?></h5>
                        <div class="checkbox-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="estandares1" <?php echo ($workPlan->estandares1 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label"><?php echo __("Educación Estética") ?></label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="estandares2" <?php echo ($workPlan->estandares2 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label"><?php echo __("Investigación Histórica, Social y Cultura") ?></label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="estandares3" <?php echo ($workPlan->estandares3 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label"><?php echo __("Expresión Creativa") ?></label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="estandares4" <?php echo ($workPlan->estandares4 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label"><?php echo __("Juicio Estético") ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5><?php echo __("Estándares y Expectativas Códigos") ?></h5>
                        <?php
                        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
                        for ($i = 1; $i <= 5; $i++):
                        ?>
                            <div class="form-group">
                                <label><?php echo __($dias[$i - 1]) ?>:</label>
                                <input type="text" class="form-control" name="expectativas<?php echo $i ?>" value="<?php echo $workPlan->{"expectativas{$i}"} ?? '' ?>">
                            </div>
                        <?php endfor ?>
                    </div>
                </div>
            </div>

            <!-- Objetivos y Avalúo -->
            <div class="form-section">
                <h5><?php echo __("Objetivos y Avalúo") ?></h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong><?php echo __("Conceptual (Conceptos, principios, datos, hechos)") ?>:</strong></label>
                            <textarea class="form-control" name="conceptual" rows="4"><?php echo $workPlan->conceptual ?? '' ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label><strong><?php echo __("Avalúo") ?>:</strong></label>
                        <div class="checkbox-group">
                            <?php
                            $avaluos1 = [
                                'avaluo1' => 'Preguntas y Respuestas',
                                'avaluo2' => 'Preguntas abiertas',
                                'avaluo3' => 'Rúbrica',
                                'avaluo4' => 'Portafolio',
                                'avaluo5' => 'Ensayo',
                                'avaluo6' => 'Tareas de ejecución',
                                'avaluo7' => 'Tareas escritas',
                            ];
                            foreach ($avaluos1 as $key => $label):
                            ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="<?php echo $key ?>" <?php echo ($workPlan->{$key} ?? '') == 'Si' ? 'checked' : '' ?>>
                                    <label class="form-check-label"><?php echo __($label) ?></label>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong><?php echo __("Procedimental (Procesos, habilidades, estrategias, destrezas)") ?>:</strong></label>
                            <textarea class="form-control" name="procedimental" rows="4"><?php echo $workPlan->procedimental ?? '' ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkbox-group">
                            <?php
                            $avaluos2 = [
                                'avaluo8' => 'Prueba Diagnóstica',
                                'avaluo9' => 'Prueba Corta',
                                'avaluo10' => 'Examen',
                                'avaluo11' => 'Propuesta de la investigación',
                                'avaluo12' => 'Proyecto',
                                'avaluo13' => 'Informe oral o escrito',
                            ];
                            foreach ($avaluos2 as $key => $label):
                            ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="<?php echo $key ?>" <?php echo ($workPlan->{$key} ?? '') == 'Si' ? 'checked' : '' ?>>
                                    <label class="form-check-label"><?php echo __($label) ?></label>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong><?php echo __("Actitudinal (Actitudes, Valores, Normas)") ?>:</strong></label>
                            <textarea class="form-control" name="actitudinal" rows="4"><?php echo $workPlan->actitudinal ?? '' ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkbox-group">
                            <?php
                            $avaluos3 = [
                                'avaluo14' => 'Dibujo',
                                'avaluo15' => 'Pintura',
                                'avaluo16' => 'Escultura',
                                'avaluo17' => 'Grabado',
                                'avaluo18' => 'Fotografía',
                                'avaluo19' => 'Tirilla cómica',
                            ];
                            foreach ($avaluos3 as $key => $label):
                            ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="<?php echo $key ?>" <?php echo ($workPlan->{$key} ?? '') == 'Si' ? 'checked' : '' ?>>
                                    <label class="form-check-label"><?php echo __($label) ?></label>
                                </div>
                            <?php endforeach ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="avaluo20" <?php echo ($workPlan->avaluo20 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label"><?php echo __("Otros") ?>:</label>
                                <input type="text" class="form-control form-control-sm" name="avaluo201" value="<?php echo $workPlan->avaluo201 ?? '' ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metodologías -->
            <div class="form-section">
                <h5><?php echo __("Metodologías") ?></h5>
                <div class="row">
                    <div class="col-md-3">
                        <h6><?php echo __("Comprensión Lectora") ?></h6>
                        <?php
                        $comprension = [
                            'comprension1' => 'Lectura en voz alta',
                            'comprension2' => 'Lectura dirigida',
                            'comprension3' => 'Lectura compartida',
                            'comprension4' => 'Escritura Interactiva',
                        ];
                        foreach ($comprension as $key => $label):
                        ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="<?php echo $key ?>" <?php echo ($workPlan->{$key} ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label"><?php echo __($label) ?></label>
                            </div>
                        <?php endforeach ?>
                    </div>
                    <div class="col-md-3">
                        <h6><?php echo __("Aprendizaje Cooperativo") ?></h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="aprendizaje" <?php echo ($workPlan->aprendizaje ?? '') == 'Si' ? 'checked' : '' ?>>
                            <label class="form-check-label"><?php echo __("Tutoría entre pares") ?></label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h6><?php echo __("Aprendizaje Basado en Problema") ?></h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="aprendizaje_problema" <?php echo ($workPlan->aprendizaje_problema ?? '') == 'Si' ? 'checked' : '' ?>>
                            <label class="form-check-label"><?php echo __("Enseñanza Contextualizada") ?></label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h6><?php echo __("Integración Curricular") ?></h6>
                        <?php
                        $integracion = [
                            'integracion1' => 'Integración a la tecnología',
                            'integracion2' => 'Integración Curricular',
                            'integracion3' => 'Español',
                            'integracion4' => 'Matemáticas',
                            'integracion5' => 'Inglés',
                            'integracion6' => 'Ciencias',
                            'integracion7' => 'Estudios Sociales',
                        ];
                        foreach ($integracion as $key => $label):
                        ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="<?php echo $key ?>" <?php echo ($workPlan->{$key} ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label"><?php echo __($label) ?></label>
                            </div>
                        <?php endforeach ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="integracion8" <?php echo ($workPlan->integracion8 ?? '') == 'Si' ? 'checked' : '' ?>>
                            <label class="form-check-label"><?php echo __("Otras") ?>:</label>
                            <input type="text" class="form-control form-control-sm" name="integracion81" value="<?php echo $workPlan->integracion81 ?? '' ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actividades -->
            <div class="form-section">
                <h5><?php echo __("Actividades") ?></h5>
                <div class="row">
                    <div class="col-md-4">
                        <h6><?php echo __("Inicio") ?></h6>
                        <?php
                        $inicio = [
                            'inicio1' => 'Actividad de Rutina (Bienvenida, Saludo Canción)',
                            'inicio2' => 'Presentación del tema',
                            'inicio3' => 'Discusión de Asignación',
                            'inicio4' => 'Canción',
                            'inicio5' => 'Juego',
                            'inicio6' => 'Problema del Día',
                            'inicio7' => 'Adivinanza',
                            'inicio8' => 'Discusión de Noticia, Asignación o tema de actualidad',
                            'inicio9' => 'Repaso Conceptos Discutidos',
                            'inicio10' => 'Observación y Estudio',
                            'inicio11' => 'Reflexión',
                        ];
                        foreach ($inicio as $key => $label):
                        ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="<?php echo $key ?>" <?php echo ($workPlan->{$key} ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label"><?php echo __($label) ?></label>
                            </div>
                        <?php endforeach ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="inicio12" <?php echo ($workPlan->inicio12 ?? '') == 'Si' ? 'checked' : '' ?>>
                            <label class="form-check-label"><?php echo __("Otro") ?>:</label>
                            <textarea class="form-control form-control-sm" name="inicio121" rows="2"><?php echo $workPlan->inicio121 ?? '' ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h6><?php echo __("Desarrollo") ?></h6>
                        <?php
                        $desarrollo = [
                            'desarrollo1' => 'Introducción de Vocabulario',
                            'desarrollo2' => 'Estudio Supervisado o dirigido',
                            'desarrollo3' => 'Práctica',
                            'desarrollo4' => 'Lectura y análisis',
                            'desarrollo5' => 'Informe Oral',
                            'desarrollo6' => 'Formular y/o contestar preguntas',
                            'desarrollo7' => 'Resolver ejercicios de Práctica',
                            'desarrollo8' => 'Prueba corta',
                            'desarrollo9' => 'Competencia',
                            'desarrollo10' => 'Debate',
                            'desarrollo11' => 'Examen',
                        ];
                        foreach ($desarrollo as $key => $label):
                        ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="<?php echo $key ?>" <?php echo ($workPlan->{$key} ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label"><?php echo __($label) ?></label>
                            </div>
                        <?php endforeach ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="desarrollo12" <?php echo ($workPlan->desarrollo12 ?? '') == 'Si' ? 'checked' : '' ?>>
                            <label class="form-check-label"><?php echo __("Otro") ?>:</label>
                            <textarea class="form-control form-control-sm" name="desarrollo121" rows="2"><?php echo $workPlan->desarrollo121 ?? '' ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h6><?php echo __("Cierre") ?></h6>
                        <?php
                        $cierre = [
                            'cierre1' => 'Resumir Material Estudio',
                            'cierre2' => 'Discusión del trabajo asignado',
                            'cierre3' => 'Trabajo en grupo',
                            'cierre4' => 'Corrección y evaluación del trabajo',
                            'cierre5' => 'Aclarar dudas de la destreza',
                            'cierre6' => 'Copiar material en la libreta',
                            'cierre7' => 'Ejercicios o actividades para comprobar aprendizaje',
                        ];
                        foreach ($cierre as $key => $label):
                        ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="<?php echo $key ?>" <?php echo ($workPlan->{$key} ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label"><?php echo __($label) ?></label>
                            </div>
                        <?php endforeach ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="cierre8" <?php echo ($workPlan->cierre8 ?? '') == 'Si' ? 'checked' : '' ?>>
                            <label class="form-check-label"><?php echo __("Otro") ?>:</label>
                            <textarea class="form-control form-control-sm" name="cierre81" rows="2"><?php echo $workPlan->cierre81 ?? '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acomodo y Materiales -->
            <div class="form-section">
                <div class="row">
                    <div class="col-md-6">
                        <h5><?php echo __("Acomodo Razonable") ?></h5>
                        <?php
                        $acomodo = [
                            'acomodo1' => 'Tiempo y Medio',
                            'acomodo2' => 'Ubicación Pupitre',
                            'acomodo3' => 'Adaptar a la Institución',
                            'acomodo4' => 'Servicio Suplementario de Apoyo',
                            'acomodo5' => 'Fragmentar trabajos y/o exámenes',
                        ];
                        foreach ($acomodo as $key => $label):
                        ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="<?php echo $key ?>" <?php echo ($workPlan->{$key} ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label"><?php echo __($label) ?></label>
                            </div>
                        <?php endforeach ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="acomodo6" <?php echo ($workPlan->acomodo6 ?? '') == 'Si' ? 'checked' : '' ?>>
                            <label class="form-check-label"><?php echo __("Otro") ?>:</label>
                            <input type="text" class="form-control form-control-sm" name="acomodo61" value="<?php echo $workPlan->acomodo61 ?? '' ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5><?php echo __("Materiales") ?></h5>
                        <div class="checkbox-group">
                            <?php
                            $materiales = [
                                'materiales1' => 'Libreta',
                                'materiales2' => 'Crayola',
                                'materiales3' => 'Tijera',
                                'materiales4' => 'Pega',
                                'materiales5' => 'Delantal',
                                'materiales6' => 'Pinceles',
                                'materiales7' => 'Tempera',
                                'materiales8' => 'Cartulinas de colores',
                                'materiales9' => 'Lápices #2B o 2B-6B',
                                'materiales10' => 'Saca puntas',
                                'materiales11' => 'Borra',
                                'materiales12' => 'Libreta de dibujo multiuso',
                                'materiales13' => 'Libreta de acuarela',
                                'materiales14' => 'Carboncillo comprimido',
                                'materiales15' => 'Lápices de colores',
                                'materiales16' => 'Pasteles a color con aceite o sin aceite',
                            ];
                            foreach ($materiales as $key => $label):
                            ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="<?php echo $key ?>" <?php echo ($workPlan->{$key} ?? '') == 'Si' ? 'checked' : '' ?>>
                                    <label class="form-check-label"><?php echo __($label) ?></label>
                                </div>
                            <?php endforeach ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="materiales17" <?php echo ($workPlan->materiales17 ?? '') == 'Si' ? 'checked' : '' ?>>
                                <label class="form-check-label"><?php echo __("Otro") ?>:</label>
                                <textarea class="form-control form-control-sm" name="materiales171" rows="2"><?php echo $workPlan->materiales171 ?? '' ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::sweetAlert();
    ?>
    <script>
        const planForm = document.getElementById('planForm');
        const planSelect = document.getElementById('planSelect');
        const savePlan = document.getElementById('savePlan');
        const printPlan = document.getElementById('printPlan');
        const deletePlan = document.getElementById('deletePlan');
        const newPlan = document.getElementById('newPlan');
        const planIdInput = document.getElementById('planId');

        // Show form when plan is selected or new plan is clicked
        planSelect.addEventListener('change', function() {
            if (this.value) {
                window.location.href = '?plan=' + this.value;
            }
        });

        newPlan.addEventListener('click', function() {
            planIdInput.value = '';
            planForm.reset();
            planForm.style.display = 'block';
            savePlan.style.display = 'block';
            printPlan.style.display = 'none';
            deletePlan.style.display = 'none';
        });

        <?php if ($workPlan || $planId): ?>
            // Show form and buttons if we have a plan
            planForm.style.display = 'block';
            savePlan.style.display = 'block';
            printPlan.style.display = 'block';
            deletePlan.style.display = 'block';
        <?php endif ?>

        // Save plan
        savePlan.addEventListener('click', function() {
            const formData = new FormData(planForm);
            const isNew = !planIdInput.value;

            if (isNew) {
                formData.append('createWorkPlan', 'true');
            } else {
                formData.append('updateWorkPlan', 'true');
                formData.append('workPlanId', planIdInput.value);
            }

            fetch('includes/index.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        if (isNew && data.id) {
                            window.location.href = '?plan=' + data.id;
                        } else {
                            location.reload();
                        }
                    } else {
                        alert(data.error || '<?php echo __("Error al guardar") ?>');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('<?php echo __("Error al guardar el plan") ?>');
                });
        });

        // Print plan
        printPlan.addEventListener('click', function() {
            if (planIdInput.value) {
                window.open('pdf.php?plan=' + planIdInput.value, '_blank');
            }
        });

        // Delete plan
        deletePlan.addEventListener('click', function() {
            if (!planIdInput.value) return;

            if (confirm('<?php echo __("¿Está seguro que desea eliminar este plan?") ?>')) {
                const formData = new FormData();
                formData.append('deleteWorkPlan', planIdInput.value);

                fetch('includes/index.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            window.location.href = 'index.php';
                        } else {
                            alert(data.error || '<?php echo __("Error al eliminar") ?>');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('<?php echo __("Error al eliminar el plan") ?>');
                    });
            }
        });
    </script>
</body>

</html>