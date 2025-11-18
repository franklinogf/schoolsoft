<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Session;

Session::is_logged();

$options = [
    [
        'title' => __('Mensajes'),
        'buttons' => [
            ['name' => __('Correo electrónico'), 'link' => 'email/index.php'],
            ['name' => __('Mensajes de texto'), 'link' => 'sms/index.php'],
            ['name' => __('Mensajes'), 'link' => 'inbox/index.php'],
            // ['name' => __('Mensajes de padres'), 'link' => '#'],
        ]
    ],
    [
        'title' => __('Planes'),
        'buttons' => [
            ['name' => __('Planes de trabajo 1'), 'link' => 'workplans/plan1/index.php'],
            ['name' => __('Planes de trabajo 2'), 'link' => 'workplans/plan2/index.php'],
            ['name' => __('Planes de trabajo 3'), 'link' => 'workplans/plan3/index.php'],
            ['name' => __('Planes de trabajo 4'), 'link' => 'workplans/plan4/index.php'],
            ['name' => __('Plan semanal 1'), 'link' => 'weeklyplans/plan1/index.php'],
            ['name' => __('Plan semanal 2'), 'link' => 'weeklyplans/plan2/index.php'],
            ['name' => __('Plan semanal 3'), 'link' => 'weeklyplans/plan3/index.php'],
            // ['name' => __('Plan maestro'), 'link' => '#'],
            ['name' => __('Plan de clase'), 'link' => 'classplan/index.php'],
            ['name' => __('Plan en inglés'), 'link' => 'englishplan/index.php'],
            ['name' => __('Plan de unidad'), 'link' => 'unitplan/index.php'],
            ['name' => __('Plan de lección en inglés'), 'link' => '#'],
        ]
    ],
    [
        'title' => __('Informes'),
        'buttons' => [
            // ['name' => __('Informe de labor'), 'link' => './reports/labor.php'],
            // ['name' => __('Informe de Notas'), 'link' => './reports/grades.php'],
            ['name' => __('Informe de cambios de notas'), 'link' => 'reports/pdf/gradesChanges.php', 'target' => '_blank'],
            ['name' => __('Listado de 100'), 'link' => 'reports/100.php'],
            ['name' => __('Lista de promedios'), 'link' => 'reports/pdf/averages.php', 'target' => '_blank'],
        ]
    ],
    [
        'title' => __('Otros'),
        'buttons' => [
            ['name' => __('Generador de Examen'), 'link' => 'exam/index.php'],
            ['name' => __('Crear tarea'), 'link' => 'homeworks/index.php'],
            // ['name' => __('Mi Registro'), 'link' => '#'],
            // ['name' => __('Crear clase diaria'), 'link' => '#'],
            ['name' => __('Documentos'), 'link' => 'documents/index.php'],
            ['name' => __('Notas por examen'), 'link' => 'examennota/examennota.php'],
            // ['name' => __('Planilla de disciplina'), 'link' => '#'],
            // ['name' => __('Planilla de uniformes'), 'link' => '#'],
            ['name' => __('Curva de notas'), 'link' => 'gradescurve/index.php'],
            ['name' => __('Clasificación de notas'), 'link' => 'pdf/pdfNoteClasification.php', 'target' => '_blank'],
        ]
    ]

];

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Mensajes y Opciones");
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-3"><?= __("Mensajes y Opciones") ?></h1>

        <div class="row row-cols-1 row-cols-md-2 mx-2 mx-md-0 justify-content-around">

            <?php foreach ($options as $option): ?>
                <div class="col mb-4">
                    <fieldset class="border border-secondary rounded-bottom h-100 px-2">
                        <legend class="w-auto"><?= $option['title'] ?></legend>
                        <div class="pb-3">
                            <div class="row row-cols-2">
                                <?php foreach ($option['buttons'] as $button): ?>
                                    <div class="col mt-1">
                                        <a style="font-size: .8em;" <?= isset($button['target']) ? "target='{$button['target']}'" : '' ?> class="btn btn-primary btn-block" href="<?= Route::url('/regiweb/options/' . $button['link']) ?>"><?= mb_strtoupper($button['name'], 'UTF-8') ?></a>
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