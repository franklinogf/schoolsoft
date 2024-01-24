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
                'name' => ["es" => 'Conducta', "en" => "Conducta"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Cambios en registro', "en" => "Cambios en registro"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Boleta de verano', "en" => "Boleta de verano"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Promedios decimales', "en" => "Promedios decimales"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Cuadro de honor', "en" => "Cuadro de honor"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Notas finales', "en" => "Notas finales"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Programa de clases', "en" => "Programa de clases"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Lista de promedios', "en" => "Lista de promedios"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Asistencia perfecta', "en" => "Asistencia perfecta"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Horas comunitarias', "en" => "Horas comunitarias"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Comparación de notas', "en" => "Comparación de notas"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Clasificación de notas', "en" => "Clasificación de notas"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Sabana de notas', "en" => "Sabana de notas"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Distri/Notas maestros', "en" => "Distri/Notas maestros"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Listado de 100', "en" => "Listado de 100"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
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
                'name' => ["es" => 'Deficiencia', "en" => "Deficiencia"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Asistencia', "en" => "Asistencia"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Notas', "en" => "Notas"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Asistencia semanal', "en" => "Asistencia semanal"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
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

            <?php foreach ($options as $option) : ?>
                <div class="col mb-4">
                    <fieldset class="border border-secondary rounded-bottom h-100 px-2">
                        <legend class="w-auto">
                            <?= $option['title'][__LANG] ?>
                        </legend>
                        <div class="pb-3">
                            <div class="row row-cols-2">
                                <?php foreach ($option['buttons'] as $button) : ?>
                                    <div class="col mt-1">
                                        <a style="font-size: .8em;" title="<?= $button['desc'][__LANG] ?>" <?= isset($button['target']) ? "target='{$button['target']}'" : '' ?> class="btn btn-primary btn-block" href="<?= $button['link'] ?>">
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