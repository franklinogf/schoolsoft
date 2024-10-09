<?php
require_once '../../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Lang;

Session::is_logged();

$options = [
    [
        'title' => ["es" => 'Opciones', "en" => 'Options'],
        'buttons' => [

            [
                'name' => ["es" => 'Tarjeta de notas', "en" => "Grade report"],
                'desc' => ['es' => 'Pantalla para imprimir reporte de notas', 'en' => 'Screen to print grade report'],
                'link' => 'TarjetaOpciones.php'
            ],
            [
                'name' => ["es" => 'Hoja de progreso', "en" => "Progress sheet"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'HojaPrograso.php'
            ],
            [
                'name' => ["es" => 'Registro de notas', "en" => "Record of notes"],
                'desc' => ['es' => 'Informes del registro de los maestros', 'en' => 'Teacher Record Reports'],
                'link' => 'RegistroNotas.php'
            ],
            [
                'name' => ["es" => 'Distribucción de notas', "en" => "Note distribution"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'DistribuccionNotas.php '
            ],
            [
                'name' => ["es" => 'Lista de fracasados', "en" => "List of failures"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'ListaFracasados.php'
            ],
            [
                'name' => ["es" => 'Lista de promedios', "en" => "List of averages"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'ListaPromedios.php'
            ],
            [
                'name' => ["es" => 'Notas en letras', "en" => "Notas en letras"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Lista de rango', "en" => "Rank List"],
                'desc' => ['es' => 'Listado de rango por materias', 'en' => 'Rank list by materials'],
                'link' => 'ListaRango.php'
            ],
            [
                'name' => ["es" => 'Rango por grado', "en" => "Rank by grade"],
                'desc' => ['es' => 'Listado de rango por grado', 'en' => 'List of rank by grade'],
                'link' => 'RangoGrado.php'
            ],
            [
                'name' => ["es" => 'Conducta', "en" => "Conduct"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'Conduct.php'
            ],
            [
                'name' => ["es" => 'Cambios en registro', "en" => "Registry changes"],
                'desc' => ['es' => 'Cambio que los maestros hacen en el registro de notas', 'en' => 'Change that teachers make in the grade record'],
                'link' => 'gradesChanges.php'
            ],
            [
                'name' => ["es" => 'Boleta de verano', "en" => "Summer card"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'SummerCard.php'
            ],
            [
                'name' => ["es" => 'Promedios decimales', "en" => "Promedios decimales"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Cuadro de honor', "en" => "Honor roll"],
                'desc' => ['es' => 'Listado por curso o materias para el cuadro de honor.', 'en' => 'List by course or subjects for the honor roll.'],
                'link' => 'HonorRoll.php'
            ],
            [
                'name' => ["es" => 'Inf. de Deficiencia', "en" => "Deficiency Report"],
                'desc' => ['es' => 'Beleta de deficiencia por estudiante para los padres.', 'en' => 'Deficiency letter per student for parents.'],
                'link' => 'Deficiencia.php'
            ],
            [
                'name' => ["es" => 'Notas finales', "en" => "Notas finales"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Programa de clases', "en" => "Class Program"],
                'desc' => ['es' => 'Hoja de itinerario de las clases', 'en' => 'Class itinerary sheet'],
                'link' => 'ProgramaClases.php'
            ],
            [
                'name' => ["es" => 'Lista de promedios', "en" => "List of averages"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'ListadePromedios.php'
            ],
            [
                'name' => ["es" => 'Asistencia perfecta', "en" => "Perfect assistance"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'AsistenciaPerfecta.php'
            ],
            [
                'name' => ["es" => 'Horas comunitarias', "en" => "Communitary hours"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'pdf/HorasComunitarias.php',
                'target' => 'HorasComunitarias'
            ],
            [
                'name' => ["es" => 'Comparación de notas', "en" => "Comparison of notes"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'ComparacionNotas.php'
            ],
            [
                'name' => ["es" => 'Inf. Aprove. Académico', "en" => "Inf. about academic achiev."],
                'desc' => ['es' => 'Información sobre el rendimiento académico.', 'en' => 'Information on academic performance.'],
                'link' => 'InfAproveAcademico.php'
            ],
            [
                'name' => ["es" => 'Clasificación de notas', "en" => "Note classification"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'NoteClassification.php'
            ],
            [
                'name' => ["es" => 'Sabana de notas', "en" => "Sheet of Notes"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'pdf/sabana_notas.php',
                'target' => 'HorasComunitarias'
            ],
            [
                'name' => ["es" => 'Distri/Notas maestros', "en" => "Teacher Notes Distribution"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'DistriNotasMaestros.php'
            ],
            [
                'name' => ["es" => 'Listado de 100', "en" => "Average Listing 100"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'Listade100.php'
            ],
            [
                'name' => ["es" => 'Informe acumulativo de notas', "en" => "Cumulative grade report"],
                'link' => 'pdf/CumulativeGradeReport.php',
                'target' => 'CumulativeGradeReport'
            ],

        ]
    ],
    [
        'title' => ["es" => 'Informes', "en" => 'Reports'],
        'buttons' => [
            [
                'name' => ["es" => 'Planes', "en" => "Planes"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Deficiencia', "en" => "Deficiencia"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Asistencia', "en" => "Attendance"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'Attendance.php'
            ],
            [
                'name' => ["es" => 'Notas', "en" => "Notas"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Asistencia semanal', "en" => "Weekly attendance"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'pdf/AsistenciaSemanal.php',
                'target' => 'AsistenciaSemanal'
            ],
            [
                'name' => ["es" => 'Notas por examen', "en" => "Notas por examen"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Aprove. Academico', "en" => "Aprove. Academico"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Curriculum', "en" => "Curriculum"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Labor', "en" => "Labor"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],


        ]
    ],




];


$lang = new Lang([
    ["Opciones", "Options"]
]);


?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation("Opciones");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-3">
            <?= $lang->translation("Opciones") ?>
        </h1>

        <div class="row row-cols-1 row-cols-md-2 mx-2 mx-md-0 justify-content-around">

            <?php foreach ($options as $option): ?>
                <div class="col mb-4">
                    <fieldset class="border border-secondary rounded-bottom h-100 px-2">
                        <legend class="w-auto">
                            <?= $option['title'][__LANG] ?>
                        </legend>
                        <div class="pb-3">
                            <div class="row row-cols-2">
                                <?php foreach ($option['buttons'] as $button): ?>
                                    <div class="col mt-1">
                                        <a style="font-size: .8em;" title="<?= isset($button['desc']) ? $button['desc'][__LANG] : '' ?>" <?= isset($button['target']) ? "target='{$button['target']}'" : '' ?> class="btn btn-primary btn-block" href="<?= $button['link'] ?>">
                                            <?= mb_strtoupper($button['name'][__LANG], 'UTF-8') ?>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </fieldset>
                </div>
            <?php endforeach ?>



        </div>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>