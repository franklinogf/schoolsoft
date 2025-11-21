<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Route;
use Classes\Session;

Session::is_logged();

$options = [
    [
        'title' => __("Planes de Trabajo"),
        'buttons' => [
            [
                'name' => __("Plan de trabajo 1"),
                'desc' => __('Gestión del Plan de Trabajo 1'),
                'link' => 'workplans/index.php?plan=1',
            ],
            [
                'name' => __("Plan de trabajo 2"),
                'desc' => __('Gestión del Plan de Trabajo 2'),
                'link' => 'workplans/index.php?plan=2',
            ],
            [
                'name' => __("Plan de trabajo 3"),
                'desc' => __('Gestión del Plan de Trabajo 3'),
                'link' => 'workplans/index.php?plan=3',
            ],
            [
                'name' => __("Plan de trabajo 4"),
                'desc' => __('Gestión del Plan de Trabajo 4'),
                'link' => 'workplans/index.php?plan=4',
            ]
        ],
    ],
    [
        'title' => __("Planes Semanales"),
        'buttons' => [
            [
                'name' => __("Plan semanal 1"),
                'desc' => __('Gestión del Plan semanal 1'),
                'link' => 'weeklyplans/plan1/index.php',
            ],
            [
                'name' => __("Plan semanal 2"),
                'desc' => __('Gestión del Plan semanal 2'),
                'link' => 'weeklyplans/plan2/index.php',
            ],
            [
                'name' => __("Plan semanal 3"),
                'desc' => __('Gestión del Plan semanal 3'),
                'link' => 'weeklyplans/plan3/index.php',
            ]
        ],
    ],
    [
        'title' => __("Otros Planes"),
        'buttons' => [
            [
                'name' => __("Plan de clase"),
                'desc' => __('Gestión del Plan de clase'),
                'link' => 'classplan/index.php',
            ],
            [
                'name' => __("Plan en inglés"),
                'desc' => __('Gestión del Plan en inglés'),
                'link' => 'englishplan/index.php',
            ],
            [
                'name' => __("Plan de unidad"),
                'desc' => __('Gestión del Plan de unidad'),
                'link' => 'unitplan/index.php',
            ],
            [
                'name' => __("Plan de lección en inglés"),
                'desc' => __('Gestión del Plan de lección en inglés'),
                'link' => 'englishlessonplan/index.php',
            ]
        ]

    ]
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
                                        <a style="font-size: .8em;" title="<?= $button['desc'] ?>" <?= isset($button['target']) ? "target='{$button['target']}'" : '' ?> class="btn btn-primary btn-block" href="<?= Route::url('/admin/access/plans/' . $button['link']) ?>"><?= mb_strtoupper($button['name'], 'UTF-8') ?></a>
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