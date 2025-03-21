<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;

Session::is_logged();

$buttons = [
    ['name' => 'Ver mensajes', 'link' => '#'],
    ['name' => 'Hacer cita', 'link' => 'appointment/'],
    ['name' => 'Re-Matrícula', 'link' => 'reEnrollment/'],
    ['name' => 'Tareas', 'link' => 'homeworks/'],
    ['name' => 'Tarjeta de notas', 'link' => 'grades/'],
    ['name' => 'Documentos', 'link' => 'documents/'],
    ['name' => 'Hoja de progreso', 'link' => 'progress/'],
    ['name' => 'Informe de deficiencia', 'link' => 'deficiency/'],
    ['name' => 'Tiendas', 'link' => 'stores/'],
];
$lang = new Lang([
    ["Mensajes y Opciones", "Messages and Options"],
    ["Ver mensajes", "View messages"],
    ["Hacer cita", "Make an appointment"],
    ["Re-Matrícula", "Re-Enrollment"],
    ["Tareas", "Homeworks"],
    ["Tarjeta de notas", "Grades card"],
    ["Documentos", "Documents"],
    ["Hoja de progreso", "Progress sheet"],
    ["Informe de deficiencia", "Deficiency report"],
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Mensajes y Opciones");
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-4"><?= $lang->translation("Mensajes y Opciones") ?></h1>
        <div class="row row-cols-1 row-cols-md-2">
            <?php foreach ($buttons as $button): ?>
                <div class="col mb-1">
                    <a style="font-size: .8em;" <?= isset($button['target']) ? "target='{$button['target']}'" : '' ?> class="btn btn-primary btn-block" href="<?= $button['link'] ?>"><?= mb_strtoupper($lang->translation($button['name']), 'UTF-8') ?></a>
                </div>
            <?php endforeach ?>
        </div>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>