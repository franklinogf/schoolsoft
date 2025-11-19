<?php
require_once __DIR__ . '/../../app.php';

use Classes\Route;
use Classes\Session;

Session::is_logged();

$options = [
    [
        'buttons' => [
            [
                'name' => __('Correo electronico'),
                'desc' => '',
                'links' => [
                    [
                        'name' => __('Profesores'),
                        'href' => "email/teachers.php"
                    ],
                    [
                        'name' => __('Estudiantes'),
                        'href' => "email/students.php"
                    ],
                    [
                        'name' => __('Administradores'),
                        'href' => "email/admins.php"
                    ]
                ]
            ],
            [
                'name' => __('Mensajes de texto'),
                'desc' => '',
                'links' => [
                    [
                        'name' => __('Profesores'),
                        'href' => "sms/teachers.php"
                    ],
                    [
                        'name' => __('Estudiantes'),
                        'href' => "sms/students.php"
                    ],
                    [
                        'name' => __('Administradores'),
                        'href' => "sms/admins.php"
                    ]
                ]
            ],
            [
                'name' => __('Mensajes internos'),
                'desc' => '',
                'link' => '#'
            ],
            [
                'name' => __('Whatsapp'),
                'desc' => '',
                'links' => [
                    [
                        'name' => __('Profesores'),
                        'href' => "#"
                    ],
                    [
                        'name' => __('Estudiantes'),
                        'href' => "#"
                    ]
                ]
            ],
            [
                'name' => __('Mensajes enviados'),
                'desc' => '',
                'link' => 'email/sent.php'
            ],
            [
                'name' => __('Mensaje Grupal'),
                'desc' => '',
                'link' => 'mensajes/mensa_grupos.php'
            ],

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
                        <?php if (isset($option['title'])): ?>
                            <legend class="w-auto"><?= $option['title'] ?></legend>
                        <?php endif ?>
                        <div class="pb-3">
                            <div class="row row-cols-2">
                                <?php foreach ($option['buttons'] as $button): ?>
                                    <?php if (isset($button['link'])): ?>
                                        <div class="col mt-1">
                                            <a style="font-size: .8em;" title="<?= $button['desc'] ?>" <?= isset($button['target']) ? "target='{$button['target']}'" : '' ?> class="btn btn-primary btn-block" href="<?= $button['link'] ?>"><?= mb_strtoupper($button['name'], 'UTF-8') ?></a>
                                        </div>
                                    <?php else: ?>
                                        <div class="col mt-1 ">
                                            <div class="dropdown w-100">
                                                <button style="font-size: .8em;" class="btn btn-primary dropdown-toggle btn-block" type="button" data-toggle="dropdown" aria-expanded="false">
                                                    <?= mb_strtoupper($button['name'], 'UTF-8') ?>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <?php foreach ($button['links'] as $link): ?>
                                                        <a class="dropdown-item" href="<?= $link['href'] ?>"><?= $link['name'] ?></a>
                                                    <?php endforeach ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif ?>
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