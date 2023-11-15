<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Lang;

Session::is_logged();

$options = [
    [
        'title' => ["es" => 'Opciones', "en" => 'Options'],
        'buttons' => [
            [
                'name' => ["es" => 'Registro',   "en" => "Registro"],
                'desc' => ['es' => 'Registro de notas', 'en' => 'Registro de notas'],
                'link' => 'registration/'
            ],
            [
                'name' => ["es" => 'Informes',   "en" => "Reports"],
                'desc' => ['es' => 'Pantalla con todos los informes', 'en' => 'Screen with all the reports'],
                'link' => 'reports/'
            ],

            [
                'name' => ["es" => 'Fechas',   "en" => "Fechas"],
                'desc' => ['es' => 'Entrada de fechas para los inicios y cierres de los trimestres.', 'en' => 'Entrada de fechas para los inicios y cierres de los trimestres.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Activaciones',   "en" => "Activaciones"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Re-matrícula',   "en" => "Re-matrícula"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],


        ]
    ],
    [
        'title' => ["es" => 'Notas', "en" => 'Grades'],
        'buttons' => [
            [
                'name' => ["es" => 'Opciones de notas',   "en" => "Grade options"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Informes de notas',   "en" => "Grade reports"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Acumulativa',   "en" => "Acumulativa"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Catálogo',   "en" => "Catálogo"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Crear los grados',   "en" => "Crear los grados"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Cursos por grados',   "en" => "Cursos por grados"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Programas especiales',   "en" => "Programas especiales"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Cambiar curso',   "en" => "Cambiar curso"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Ordenar cursos',   "en" => "Ordenar cursos"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Eliminar',   "en" => "Eliminar"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Asistencia',   "en" => "Asistencia"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Entrar asistencia',   "en" => "Entrar asistencia"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Clases diaras',   "en" => "Clases diaras"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Plan de trabajo',   "en" => "Plan de trabajo"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Mensajes de notas',   "en" => "Mensajes de notas"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Deporte',   "en" => "Deporte"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Clases de verano',   "en" => "Clases de verano"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],

        ]
    ],
    [
        'title' => ["es" => 'Información', "en" => 'Information'],
        'buttons' => [
            [
                'name' => ["es" => 'Año escolar',   "en" => "Año escolar"],
                'desc' => ['es' => 'Aqui puede seleccionar el año donde van a trabajar.', 'en' => 'Aqui puede seleccionar el año donde van a trabajar.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Mensaje inicial',   "en" => "Mensaje inicial"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Mensaje grupal',   "en" => "Mensaje grupal"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Mensaje inactivar',   "en" => "Mensaje inactivar"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Encuestas',   "en" => "Encuestas"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Requisitos',   "en" => "Requisitos"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
        ]
    ],
    [
        'title' => ["es" => 'Datos', "en" => 'Data'],
        'buttons' => [
            [
                'name' => ["es" => 'Exportar',   "en" => "Exportar"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Importar',   "en" => "Importar"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Documentos',   "en" => "Documentos"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Clave padres',   "en" => "Clave padres"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Backup',   "en" => "Backup"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Pasar data',   "en" => "Pasar data"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Exportar data',   "en" => "Exportar data"],
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
        <h1 class="text-center my-3"><?= $lang->translation("Opciones") ?></h1>

        <div class="row row-cols-1 row-cols-md-2 mx-2 mx-md-0 justify-content-around">

            <?php foreach ($options as $option) : ?>
                <div class="col mb-4">
                    <fieldset class="border border-secondary rounded-bottom h-100 px-2">
                        <legend class="w-auto"><?= $option['title'][__LANG] ?></legend>
                        <div class="pb-3">
                            <div class="row row-cols-2">
                                <?php foreach ($option['buttons'] as $button) : ?>
                                    <div class="col mt-1">
                                        <a style="font-size: .8em;" title="<?= $button['desc'][__LANG] ?>" <?= isset($button['target']) ? "target='{$button['target']}'" : '' ?> class="btn btn-primary btn-block" href="<?= $button['link'] ?>"><?= mb_strtoupper($button['name'][__LANG], 'UTF-8') ?></a>
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