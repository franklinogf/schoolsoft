<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Route;
use Classes\Session;

Session::is_logged();

$options = [
    [
        'title' => __('Opciones'),
        'buttons' => [
            ['name' => __('Informacion basica'), 'link' => 'basic_information/index.php'],
            ['name' => __('Visitas Enfermeria'), 'link' => '#'],
            ['name' => __('Certificacion medica'), 'link' => '#'],
            ['name' => __('Excenciones de vacunas'), 'link' => '#'],
            ['name' => __('Vitales'), 'link' => 'vitals/index.php'],
            ['name' => __('Diabetes'), 'link' => '#'],
            ['name' => __('Vacunas incompletas'), 'link' => '#'],
        ]
    ],    

];

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Departamento de enfermeria");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-3"><?= __("Departamento de enfermeria") ?></h1>

        <div class="row row-cols-1 row-cols-md-2 mx-2 mx-md-0 justify-content-around">

            <?php foreach ($options as $option): ?>
                <div class="col mb-4">
                    <fieldset class="border border-secondary rounded-bottom h-100 px-2">
                        <legend class="w-auto"><?= $option['title'] ?></legend>
                        <div class="pb-3">
                            <div class="row row-cols-2">
                                <?php foreach ($option['buttons'] as $button): ?>
                                    <div class="col mt-1">
                                        <a style="font-size: .8em;" <?= isset($button['target']) ? "target='{$button['target']}'" : '' ?> class="btn btn-primary btn-block" href="<?= Route::url('/admin/users/infirmary/' . $button['link']) ?>"><?= mb_strtoupper($button['name'], 'UTF-8') ?></a>
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