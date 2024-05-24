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
                'name' => ["es" => 'Registro',   "en" => "Record"],
                'desc' => ['es' => 'Registro de notas', 'en' => 'Record of notes'],
                'link' => 'registration/'
            ],
            [
                'name' => ["es" => 'Informes',   "en" => "Reports"],
                'desc' => ['es' => 'Pantalla con todos los informes', 'en' => 'Screen with all the reports'],
                'link' => 'reports/'
            ],

            [
                'name' => ["es" => 'Fechas',   "en" => "Dates"],
                'desc' => ['es' => 'Entrada de fechas para los inicios y cierres de los cuatrimestres.', 'en' => 'Dates entry for the beginnings and closures of the quarter.'],
                'link' => 'changeDates.php'
            ],
            [
                'name' => ["es" => 'Activaciones',   "en" => "Activations"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'activations.php'
            ],
            [
                'name' => ["es" => 'Re-matrícula',   "en" => "Re-matrícula"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],


        ]
    ],
    [
        'title' => ["es" => 'Notas', "en" => 'Notes'],
        'buttons' => [
            [
                'name' => ["es" => 'Opciones de notas', "en" => "Note options"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'noteOptions.php'
            ],
            [
                'name' => ["es" => 'Informes de notas', "en" => "Note reports"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'gradesReports/'
            ],
            [
                'name' => ["es" => 'Acumulativa',   "en" => "Credit transcription"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'acumulativa.php'
            ],
            [
                'name' => ["es" => 'Catálogo',   "en" => "Catalog"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'catalog.php'
            ],
            [
                'name' => ["es" => 'Crear grados',   "en" => "Create grades"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'createGrades.php'
            ],
            [
                'name' => ["es" => 'Cursos por grados',   "en" => "Courses by grade"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'gradesCourses.php'
            ],
            [
                'name' => ["es" => 'Programas especiales', "en" => "Specials Programs"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'specialsPrograms.php'
            ],
            [
                'name' => ["es" => 'Cambiar curso',   "en" => "Course change"],
                'desc' => ['es' => 'Para cambiar de un curso a otro curso.', 'en' => 'To change from one course to another course.'],
                'link' => 'CourseChange.php'
            ],
            [
                'name' => ["es" => 'Ordenar cursos',   "en" => "Order courses"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'OrderCourses.php'
            ],
            [
                'name' => ["es" => 'Eliminar',   "en" => "Eliminate"],
                'desc' => ['es' => 'Para eliminar el curso a todos los estudiantes.', 'en' => 'To remove the course from all students.'],
                'link' => 'Eliminate.php'
            ],
            [
                'name' => ["es" => 'Asistencia',   "en" => "Attendance"],
                'desc' => ['es' => 'Entrada de fecha para asistencia trimestral.', 'en' => 'Date entry for quarterly attendance.'],
                'link' => 'Attendance.php'
            ],
            [
                'name' => ["es" => 'Entrada de asistencia',   "en" => "Attendance entry"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'attendance/'
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
                'name' => ["es" => 'Mensajes de notas',   "en" => "Note messages"],
                'desc' => ['es' => 'Mensajes por clase para la tarjeta de notas', 'en' => 'Class Note Card Messages'],
                'link' => 'MensajesNotas.php'
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
                'name' => ["es" => 'Año escolar',   "en" => "School year"],
                'desc' => ['es' => 'Seleccionar el año para trabajar.', 'en' => 'Select the year to work with.'],
                'link' => 'schoolYear.php'
            ],
            [
                'name' => ["es" => 'Mensaje inicial',  "en" => "Initial message"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'InitialMessage.php'
            ],
            [
                'name' => ["es" => 'Mensaje grupal',   "en" => "Group message"],
                'desc' => ['es' => 'Envío de mensajes a padres y profesores en su zona de la plataforma.', 'en' => 'Sending messages to parents and teachers in their area of the platform.'],
                'link' => 'GroupMessage.php'
            ],
            [
                'name' => ["es" => 'Mensaje inactivar',   "en" => "Inactivate message"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'MensajeInactivar.php'
            ],
            [
                'name' => ["es" => 'Encuestas',   "en" => "Surveys"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'Encuestas.php'
            ],
            [
                'name' => ["es" => 'Requisitos',   "en" => "Requirements"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'Requisitos.php'
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
                'name' => ["es" => 'Clave padres',   "en" => "Parent password"],
                'desc' => ['es' => 'Para esforzar cambiar la contraseña de los padres.', 'en' => 'To force change the parent password.'],
                'link' => 'ClavePadres.php'
            ],
            [
                'name' => ["es" => 'Backup',   "en" => "Backup"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Pasar data',   "en" => "Transfer data"],
                'desc' => ['es' => 'Transferir información de un año al siguiente.', 'en' => 'Transfer information from one year to the next.'],
                'link' => 'PasarData.php'
            ],
            [
                'name' => ["es" => 'Exportar data',   "en" => "Export data"],
                'desc' => ['es' => 'Exportación de data a Excel.', 'en' => 'Data export to Excel.'],
                'link' => 'ExportarData.php'
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