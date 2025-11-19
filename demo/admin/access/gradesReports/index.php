<?php
require_once __DIR__ . '/../../../app.php';

use App\Enums\AdminPermission;
use Classes\Route;
use Classes\Session;
use App\Models\Admin;

Session::is_logged();

$user  = Admin::user(Session::id())->first();

$options = [
    [
        'title' => __('Opciones'),
        'buttons' => [

            [
                'name' => __("Tarjeta de notas"),
                'desc' => __("Pantalla para imprimir la tarjeta de notas"),
                'link' => 'TarjetaOpciones.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::GRADES_REPORTS_NOTE_CARD)
            ],
            [
                'name' => __("Hoja de progreso"),
                'desc' => __("Pantalla para imprimir la hoja de progreso"),
                'link' => 'HojaPrograso.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::GRADES_REPORTS_PROGRESS_SHEET)
            ],
            [
                'name' => __("Registro de notas"),
                'desc' => __("Informes del registro de los maestros"),
                'link' => 'RegistroNotas.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::GRADES_REPORTS_NOTE_REGISTRY)
            ],
            [
                'name' => __("Distribucción de notas"),
                'desc' => '',
                'link' => 'DistribuccionNotas.php ',
                'hidden' => !$user->hasPermissionTo(AdminPermission::GRADES_REPORTS_NOTE_DISTRIBUTION)
            ],
            [
                'name' => __("Lista de fracasados"),
                'desc' => '',
                'link' => 'ListaFracasados.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::GRADES_REPORTS_FAILED_LIST)
            ],
            [
                'name' => __("Lista de promedios"),
                'desc' => '',
                'link' => 'ListaPromedios.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::GRADES_REPORTS_AVERAGE_LIST)
            ],
            [
                'name' => __("Notas en letras"),
                'desc' => '',
                'link' => 'ListadePromediosLetras.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::GRADES_REPORTS_NOTES_IN_LETTERS)
            ],
            [
                'name' => __('Lista de rango'),
                'desc' => __("Listado de rango por materias"),
                'link' => 'ListaRango.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::GRADES_REPORTS_RANK_LIST)
            ],
            [
                'name' => __("Rango por grado"),
                'desc' => __("Listado de rango por grado"),
                'link' => 'RangoGrado.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::GRADES_REPORTS_GRADE_RANK)
            ],
            [
                'name' => __("Conducta"),
                'desc' => '',
                'link' => 'Conduct.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::GRADES_REPORTS_AVERAGE_AND_BEHAVIOR)
            ],
            [
                'name' => __("Cambios en registro"),
                'desc' => __("Cambio que los maestros hacen en el registro de notas"),
                'link' => 'gradesChanges.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::GRADES_REPORTS_REGIWEB_CHANGES)
            ],
            [
                'name' => __("Tarjeta de verano"),
                'desc' => '',
                'link' => 'SummerCard.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::GRADES_REPORTS_SUMMER_REPORT)
            ],
            [
                'name' => __("Promedios decimales"),
                'desc' => '',
                'link' => '#',
                'hidden' => !$user->hasPermissionTo(AdminPermission::GRADES_REPORTS_PERCENT_TO_DECIMAL)
            ],
            [
                'name' => __("Cuadro de honor"),
                'desc' => __("Listado por curso o materias para el cuadro de honor"),
                'link' => 'HonorRoll.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::GRADES_REPORTS_HONOR_ROLL)
            ],
            [
                'name' => __("Inf. de Deficiencia"),
                'desc' => __("Tarjeta de deficiencia por estudiante para los padres"),
                'link' => 'Deficiencia.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::GRADES_REPORTS_DEFICIENCY_REPORTS)
            ],
            [
                'name' => __("Notas finales"),
                'desc' => '',
                'link' => '#',
                'hidden' => false,
            ],
            [
                'name' => __("Programa de clases"),
                'desc' => __("Hoja de itinerario de las clases"),
                'link' => 'ProgramaClases.php',
                'hidden' => false,
            ],
            [
                'name' => __("Lista de promedios"),
                'desc' => '',
                'link' => 'ListadePromedios.php',
                'hidden' => false,
            ],
            [
                'name' => __("Asistencia perfecta"),
                'desc' => '',
                'link' => 'AsistenciaPerfecta.php',
                'hidden' => false,
            ],
            [
                'name' => __("Horas comunitarias"),
                'desc' => '',
                'link' => 'pdf/HorasComunitarias.php',
                'hidden' => false,
                'target' => 'HorasComunitarias',
            ],
            [
                'name' => __("Comparación de notas"),
                'desc' => '',
                'link' => 'ComparacionNotas.php',
                'hidden' => false,
            ],
            [
                'name' => __("Inf. Aprove. Académico"),
                'desc' => __("Información sobre el rendimiento académico"),
                'link' => 'InfAproveAcademico.php',
                'hidden' => false,
            ],
            [
                'name' => __("Clasificación de notas"),
                'desc' => '',
                'link' => 'NoteClassification.php',
                'hidden' => false,
            ],
            [
                'name' => __("Sabana de notas"),
                'desc' => '',
                'link' => 'pdf/sabana_notas.php',
                'hidden' => false,
                'target' => 'HorasComunitarias',
            ],
            [
                'name' => __("Distri/Notas maestros"),
                'desc' => '',
                'link' => 'DistriNotasMaestros.php',
                'hidden' => false,
            ],
            [
                'name' => __("Listado de 100"),
                'desc' => '',
                'link' => 'Listade100.php',
                'hidden' => false,
            ],
            [
                'name' => __("Informe acumulativo de notas"),
                'link' => 'pdf/CumulativeGradeReport.php',
                'hidden' => false,
                'desc' => '',
                'target' => 'CumulativeGradeReport',
            ],

        ],
    ],
    [
        'title' => __("Informes"),
        'buttons' => [
            [
                'name' => __("Planes"),
                'desc' => '',
                'link' => '#',
                'hidden' => false,
            ],
            [
                'name' => __("Asistencia"),
                'desc' => '',
                'link' => 'Attendance.php',
                'hidden' => false,
            ],
            [
                'name' => __("Notas"),
                'desc' => '',
                'link' => '#',
                'hidden' => false,
            ],
            [
                'name' => __("Asistencia semanal"),
                'desc' => '',
                'link' => 'pdf/AsistenciaSemanal.php',
                'hidden' => false,
                'target' => 'AsistenciaSemanal',
            ],
            [
                'name' => __("Notas por examen"),
                'desc' => '',
                'link' => 'maestros_examen_op.php',
                'hidden' => false,
            ],
            [
                'name' => __("Aprove. Académico"),
                'desc' => '',
                'link' => '#',
                'hidden' => false,
            ],
            [
                'name' => __("Labor"),
                'desc' => '',
                'link' => '#',
                'hidden' => false,
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
        <h1 class="text-center my-3">
            <?= __("Opciones") ?>
        </h1>

        <div class="row row-cols-1 row-cols-md-2 mx-2 mx-md-0 justify-content-around">

            <?php foreach ($options as $option): ?>
                <div class="col mb-4">
                    <fieldset class="border border-secondary rounded-bottom h-100 px-2">
                        <legend class="w-auto">
                            <?= $option['title'] ?>
                        </legend>
                        <div class="pb-3">
                            <div class="row row-cols-2">
                                <?php foreach ($option['buttons'] as $button): ?>
                                    <?php if ($button['hidden']) continue  ?>
                                    <div class="col mt-1">
                                        <a style="font-size: .8em;" title="<?= isset($button['desc']) ? $button['desc'] : '' ?>" <?= isset($button['target']) ? "target='{$button['target']}'" : '' ?> class="btn btn-primary btn-block" href="<?= Route::url('/admin/access/gradesReports/' . $button['link']) ?>">
                                            <?= mb_strtoupper($button['name'], 'UTF-8') ?>
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