<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Session;

Session::is_logged();

$options = [
    [
        'title' => __("Opciones"),
        'buttons' => [
            [
                'name' => __("Registro"),
                'desc' => __('Registro de notas'),
                'link' => 'registration',
            ],
            [
                'name' => __("Informes"),
                'desc' => __('Pantalla con todos los informes'),
                'link' => 'reports',
            ],
            [
                'name' => __("Fechas"),
                'desc' => __('Entrada de fechas para los inicios y cierres de los cuatrimestres.'),
                'link' => 'changeDates.php',
            ],
            [
                'name' => __("Activaciones"),
                'desc' => __(''),
                'link' => 'activations.php',
            ],
            [
                'name' => __("Re-matrícula"),
                'desc' => __(''),
                'link' => '#',
            ],
            [
                'name' => __("Tiendas"),
                'desc' => __(''),
                'link' => 'stores/',
            ],
        ],
    ],
    [
        'title' => __("Notas"),
        'buttons' => [
            [
                'name' => __("Opciones de notas"),
                'desc' => __(''),
                'link' => 'notesOptions.php',
            ],
            [
                'name' => __("Informes de notas"),
                'desc' => __(''),
                'link' => 'gradesReports',
            ],
            [
                'name' => __("Acumulativa"),
                'desc' => __(''),
                'link' => 'acumulativa.php',
            ],
            [
                'name' => __("Catálogo"),
                'desc' => __(''),
                'link' => 'catalog.php',
            ],
            [
                'name' => __("Crear grados"),
                'desc' => __(''),
                'link' => 'createGrades.php',
            ],
            [
                'name' => __("Cursos por grados"),
                'desc' => __(''),
                'link' => 'gradesCourses.php',
            ],
            [
                'name' => __("Programas especiales"),
                'desc' => __(''),
                'link' => 'specialsPrograms.php',
            ],
            [
                'name' => __("Cambiar curso"),
                'desc' => __('Para cambiar de un curso a otro curso.'),
                'link' => 'CourseChange.php',
            ],
            [
                'name' => __("Ordenar cursos"),
                'desc' => __(''),
                'link' => 'OrderCourses.php',
            ],
            [
                'name' => __("Eliminar"),
                'desc' => __('Para eliminar el curso a todos los estudiantes.'),
                'link' => 'Eliminate.php',
            ],
            [
                'name' => __("Asistencia"),
                'desc' => __('Entrada de fecha para asistencia trimestral.'),
                'link' => 'Attendance.php',
            ],
            [
                'name' => __("Entrada de asistencia"),
                'desc' => __(''),
                'link' => 'attendance',
            ],
            [
                'name' => __("Clases diaras"),
                'desc' => __(''),
                'link' => '#',
            ],
            [
                'name' => __("Plan de trabajo"),
                'desc' => __(''),
                'link' => '#',
            ],
            [
                'name' => __("Mensajes de notas"),
                'desc' => __('Mensajes por clase para la tarjeta de notas'),
                'link' => 'MensajesNotas.php',
            ],
            [
                'name' => __("Deporte"),
                'desc' => __(''),
                'link' => '#',
            ],
            [
                'name' => __("Clases de verano"),
                'desc' => __(''),
                'link' => '#',
            ],
        ],
    ],
    [
        'title' => __("Información"),
        'buttons' => [
            [
                'name' => __("Año escolar"),
                'desc' => __('Seleccionar el año para trabajar.'),
                'link' => 'schoolYear.php',
            ],
            [
                'name' => __("Mensaje inicial"),
                'desc' => __(''),
                'link' => 'InitialMessage.php',
            ],
            [
                'name' => __("Mensaje grupal"),
                'desc' => __('Envío de mensajes a padres y profesores en su zona de la plataforma.'),
                'link' => 'GroupMessage.php',
            ],
            [
                'name' => __("Mensaje inactivar"),
                'desc' => __(''),
                'link' => 'MensajeInactivar.php',
            ],
            [
                'name' => __("Encuestas"),
                'desc' => __(''),
                'link' => 'Encuestas.php',
            ],
            [
                'name' => __("Requisitos"),
                'desc' => __(''),
                'link' => 'Requisitos.php',
            ],
        ],
    ],
    [
        'title' => __("Datos"),
        'buttons' => [
            [
                'name' => __("Exportar"),
                'desc' => __(''),
                'link' => '#',
            ],
            [
                'name' => __("Importar"),
                'desc' => __(''),
                'link' => '#',
            ],
            [
                'name' => __("Documentos"),
                'desc' => __(''),
                'link' => '#',
            ],
            [
                'name' => __("Clave padres"),
                'desc' => __('Para esforzar cambiar la contraseña de los padres.'),
                'link' => 'ClavePadres.php',
            ],
            [
                'name' => __("Backup"),
                'desc' => __(''),
                'link' => 'backup/index.php',
            ],
            [
                'name' => __("Pasar data"),
                'desc' => __('Transferir información de un año al siguiente.'),
                'link' => 'PasarData.php',
            ],
            [
                'name' => __("Exportar data"),
                'desc' => __('Exportación de data a Excel.'),
                'link' => 'ExportarData.php',
            ],
        ],
    ],
];


?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = __("Opciones");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-3"><?= __("Opciones") ?></h1>

        <div class="row row-cols-1 row-cols-md-2 mx-2 mx-md-0 justify-content-around">

            <?php foreach ($options as $option): ?>
                <div class="col mb-4">
                    <fieldset class="border border-secondary rounded-bottom h-100 px-2">
                        <legend class="w-auto"><?= $option['title'] ?></legend>
                        <div class="pb-3">
                            <div class="row row-cols-2">
                                <?php foreach ($option['buttons'] as $button): ?>
                                    <div class="col mt-1">
                                        <a style="font-size: .8em;" title="<?= $button['desc'] ?>" <?= isset($button['target']) ? "target='{$button['target']}'" : '' ?> class="btn btn-primary btn-block" href="<?= Route::url('/admin/access/' . $button['link']) ?>"><?= mb_strtoupper($button['name'], 'UTF-8') ?></a>
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