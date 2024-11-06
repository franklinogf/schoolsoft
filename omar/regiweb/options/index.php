<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;
use Classes\Lang;

Session::is_logged();

$teacher = new Teacher(Session::id());
$options = [
    [
        'title' => ["es" => 'Mensajes', "en" => 'Messages'],
        'buttons' => [
            ['name' => ["es" => 'Correo electrónico', "en" => "Email"], 'link' => 'email/'],
            ['name' => ["es" => 'Mensaje de texto', "en" => "SMS"], 'link' => 'sms/'],
            ['name' => ["es" => 'Mensajes', "en" => "Inbox"], 'link' => 'inbox/'],
            // ['name' => ["es" => 'Mensajes de padres', "en" => "Parents messages"], 'link' => '#'],
        ]
    ],
    // [
    //     'title' => 'Planes',
    //     'buttons' => [
    //         ['name' => ["es" => 'Planes de trabajo 1', "en" => "Work plans 1"], 'link' => '#'],
    //         ['name' => ["es" => 'Planes de trabajo 2', "en" => "Work plans 2"], 'link' => '#'],
    //         ['name' => ["es" => 'Planes de trabajo 3', "en" => "Work plans 3"], 'link' => '#'],
    //         ['name' => ["es" => 'Planes de trabajo 4', "en" => "Work plans 4"], 'link' => '#'],
    //         ['name' => ["es" => 'Plan semanal 1', "en" => "Weekly plan 1"], 'link' => '#'],
    //         ['name' => ["es" => 'Plan semanal 2', "en" => "Weekly plan 2"], 'link' => '#'],
    //         ['name' => ["es" => 'Plan semanal 3', "en" => "Weekly plan 3"], 'link' => '#'],
    //         ['name' => ["es" => 'Plan maestro', "en" => "Master plan"], 'link' => '#'],
    //         ['name' => ["es" => 'Plan de clase', "en" => "Class plan"], 'link' => '#'],
    //         ['name' => ["es" => 'Plan en inglés', "en" => "English plan"], 'link' => '#'],
    //         ['name' => ["es" => 'Plan de unidad', "en" => "Unit plan"], 'link' => '#'],
    //         ['name' => ["es" => 'Plan de lección en inglés', "en" => "English lesson plan"], 'link' => '#'],
    //     ]
    // ],
    [
        'title' => ["es" => 'Informes', "en" => 'Reports'],
        'buttons' => [
            // ['name' => ["es" => 'Informe de labor', "en" => "Labor report"], 'link' => './reports/labor.php'],
            // ['name' => ["es" => 'Informe de Notas', "en" => "Grades report"], 'link' => './reports/grades.php'],
            ['name' => ["es" => 'Informe de cambios de notas', "en" => "Grades Changes Report"], 'link' => './reports/pdf/gradesChanges.php', 'target' => '_blank'],
            ['name' => ["es" => 'Listado de 100', "en" => "List of 100"], 'link' => './reports/100.php'],
            ['name' => ["es" => 'Lista de promedios', "en" => "List of averages"], 'link' => './reports/pdf/averages.php', 'target' => '_blank'],
        ]
    ],
    [
        'title' => ["es" => 'Otros', "en" => 'Others'],
        'buttons' => [
            ['name' => ["es" => 'Generador de Examen', "en" => "Exam generator"], 'link' => './exam/'],
            ['name' => ["es" => 'Crear tarea', "en" => "Create homework"], 'link' => './homeworks'],
            // ['name' => ["es"=> 'Mi Registro',"en"=>""], 'link' => '#'],
            // ['name' => ["es"=> 'Crear clase diaria',"en"=>""], 'link' => '#'],
            ['name' => ["es" => 'Documentos', "en" => "Documents"], 'link' => './documents/'],
            ['name' => ["es" => 'Notas por examen', "en" => "Grades by exam"], 'link' => './examennota/examennota.php'],
            // ['name' => ["es"=> 'Planilla de disciplina',"en"=>""], 'link' => '#'],
            // ['name' => ["es"=> 'Planilla de uniformes',"en"=>""], 'link' => '#'],
            ['name' => ["es" => 'Curva de notas', "en" => "Grades curve"], 'link' => '#'],
            ['name' => ["es" => 'Clasificación de notas', "en" => "grades classification"], 'link' => './pdf/pdfNoteClasification.php', 'target' => '_blank'],
        ]
    ]

];


$lang = new Lang([
    ["Mensajes y opciones", "Messages and options"]
]);


?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Mensajes y Opciones");
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-3"><?= $lang->translation("Mensajes y Opciones") ?></h1>

        <div class="row row-cols-1 row-cols-md-2 mx-2 mx-md-0 justify-content-around">

            <?php foreach ($options as $option): ?>
                <div class="col mb-4">
                    <fieldset class="border border-secondary rounded-bottom h-100 px-2">
                        <legend class="w-auto"><?= $option['title'][__LANG] ?></legend>
                        <div class="pb-3">
                            <div class="row row-cols-2">
                                <?php foreach ($option['buttons'] as $button): ?>
                                    <div class="col mt-1">
                                        <a style="font-size: .8em;" <?= isset($button['target']) ? "target='{$button['target']}'" : '' ?> class="btn btn-primary btn-block" href="<?= $button['link'] ?>"><?= mb_strtoupper($button['name'][__LANG], 'UTF-8') ?></a>
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