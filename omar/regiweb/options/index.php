<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;

Session::is_logged();

$teacher = new Teacher(Session::id());
$options = [
    [
        'title' => 'Mensajes',
        'buttons' => [
            ['name' => 'E-mail', 'link' => 'email/'],
            ['name' => 'SMS', 'link' => 'sms/'],
            ['name' => 'Mensaje Nuevo', 'link' => '#'],
            ['name' => 'Mensajes Enviados', 'link' => '#'],
            ['name' => 'Mensajes de padres', 'link' => '#'],
        ]
    ],
    [
        'title' => 'Planes',
        'buttons' => [
            ['name' => 'Planes de trabajo 1', 'link' => '#'],
            ['name' => 'Planes de trabajo 2', 'link' => '#'],
            ['name' => 'Planes de trabajo 3', 'link' => '#'],
            ['name' => 'Planes de trabajo 4', 'link' => '#'],
            ['name' => 'Plan semanal 1', 'link' => '#'],
            ['name' => 'Plan semanal 2', 'link' => '#'],
            ['name' => 'Plan semanal 3', 'link' => '#'],
            ['name' => 'Plan maestro', 'link' => '#'],
            ['name' => 'Plan de clase', 'link' => '#'],
            ['name' => 'Plan en inglés', 'link' => '#'],
            ['name' => 'Plan de unidad', 'link' => '#'],
            ['name' => 'Plan de lesión en inglés', 'link' => '#'],
        ]
    ],
    [
        'title' => 'Informes',
        'buttons' => [
            ['name' => 'Informe de labor', 'link' => './reports/labor.php'],
            ['name' => 'Informe de Notas', 'link' => './reports/grades.php'],
            ['name' => 'Informe cambios de notas', 'link' => './reports/pdf/gradesChanges.php', 'target' => '_blank'],
            ['name' => 'Listado de 100', 'link' => './reports/100.php'],
            ['name' => 'Lista de promedios', 'link' => './reports/pdf/averages.php', 'target' => '_blank'],
        ]
    ],
    [
        'title' => 'Otros',
        'buttons' => [
            ['name' => 'Generador de Examen', 'link' => './exam/'],
            ['name' => 'Crear Asignación', 'link' => './homeworks'],
            // ['name' => 'Mi Registro', 'link' => '#'],
            // ['name' => 'Crear clase diaria', 'link' => '#'],
            ['name' => 'Documentos', 'link' => './documents/'],
            ['name' => 'Notas por examen', 'link' => '#'],
            // ['name' => 'Planilla de disciplina', 'link' => '#'],
            // ['name' => 'Planilla de uniformes', 'link' => '#'],
            ['name' => 'Curva de notas', 'link' => '#'],
            ['name' => 'Clasificación de notas', 'link' => './pdf/pdfNoteClasification.php' , 'target' => '_blank'],
        ]
    ]

];





?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = "Mensajes y Opciones";
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-3">Mensajes y Opciones</h1>

        <div class="row row-cols-1 row-cols-md-2 mx-2 mx-md-0 justify-content-around">

            <?php foreach ($options as $option) : ?>
                <div class="col mb-4">
                    <fieldset class="border border-secondary rounded-bottom h-100 px-2">
                        <legend class="w-auto"><?= $option['title'] ?></legend>
                        <div class="pb-3">
                            <div class="row row-cols-2">
                                <?php foreach ($option['buttons'] as $button) : ?>
                                    <div class="col mt-1">
                                        <a style="font-size: .8em;" <?= $button['target'] ? "target='{$button['target']}'" : '' ?> class="btn btn-primary btn-block" href="<?= $button['link'] ?>"><?= mb_strtoupper($button['name'], 'UTF-8') ?></a>
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