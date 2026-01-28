`<?php

    require_once __DIR__ . '/../../../../app.php';

    use Classes\Route;
    use Classes\Session;

    Session::is_logged();

    $options = [
        [
            'title' => __('Informes de Enfermería'),
            'buttons' => [
                ['name' => __('Informe de Enfermería'), 'link' => 'basic_health.php'],
                ['name' => __('Visitas a Enfermería'), 'link' => 'visits.php'],
                ['name' => __('Estado de Vacunación'), 'link' => 'vaccinations.php'],
                ['name' => __('Vacunas Incompletas'), 'link' => 'incomplete_vaccines.php'],
                ['name' => __('Excenciones de Vacunas'), 'link' => 'vaccine_exemptions.php'],
                ['name' => __('Informe de Diabetes'), 'link' => 'diabetes.php'],
                ['name' => __('Signos Vitales'), 'link' => 'vitals.php'],
                ['name' => __('Contactos de Enfermería'), 'link' => 'contacts.php'],
            ]
        ],
    ];

    ?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Informes de Enfermería");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-3"><?= $title ?></h1>

        <div class="row row-cols-1 row-cols-md-2 mx-2 mx-md-0 justify-content-around">

            <?php foreach ($options as $option) : ?>
                <div class="col mb-4">
                    <fieldset class="border border-secondary rounded-bottom h-100 px-2">
                        <legend class="w-auto"><?= $option['title'] ?></legend>
                        <div class="pb-3">
                            <div class="row row-cols-2">
                                <?php foreach ($option['buttons'] as $button) : ?>
                                    <div class="col mt-1">
                                        <a style="font-size: .8em;" class="btn btn-primary btn-block" href="<?= Route::url('/admin/users/infirmary/reports/' . $button['link']) ?>"><?= mb_strtoupper($button['name'], 'UTF-8') ?></a>
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