<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Lang;

Session::is_logged();

$options = [
    [
        'buttons' => [
            [
                'name' => ["es" => 'Correo electrónico', "en" => "Email"],
                'desc' => ['es' => '', 'en' => ''],
                'links' => [
                    [
                        'name' => ["es" => 'Profesores', "en" => "Teachers"],
                        'href' => "email/teachers.php"
                    ],
                    [
                        'name' => ["es" => 'Estudiantes', "en" => "Students"],
                        'href' => "email/students.php"
                    ],
                    [
                        'name' => ["es" => 'Administración', "en" => "Admin"],
                        'href' => "email/admins.php"
                    ]
                ]

            ],
            [
                'name' => ["es" => 'Mensajes de texto', "en" => "SMS"],
                'desc' => ['es' => '', 'en' => ''],
                'links' => [
                    [
                        'name' => ["es" => 'Profesores', "en" => "Teachers"],
                        'href' => "sms/teachers.php"
                    ],
                    [
                        'name' => ["es" => 'Estudiantes', "en" => "Students"],
                        'href' => "sms/students.php"
                    ],
                    [
                        'name' => ["es" => 'Administración', "en" => "Admin"],
                        'href' => "sms/admins.php"
                    ]
                ]
            ],
            [
                'name' => ["es" => 'Mensajes internos', "en" => "Internal messages"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Whatsapp', "en" => "Whatsapp"],
                'desc' => ['es' => '', 'en' => ''],
                'links' => [
                    [
                        'name' => ["es" => 'Profesores', "en" => "Teachers"],
                        'href' => "#"
                    ],
                    [
                        'name' => ["es" => 'Estudiantes', "en" => "Students"],
                        'href' => "#"
                    ],

                ]
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

            <?php foreach ($options as $option): ?>

                <div class="col mb-4">
                    <fieldset class="border border-secondary rounded-bottom h-100 px-2">
                        <?php if (isset($option['title'])): ?>
                            <legend class="w-auto"><?= $option['title'][__LANG] ?></legend>
                        <?php endif ?>
                        <div class="pb-3">
                            <div class="row row-cols-2">
                                <?php foreach ($option['buttons'] as $button): ?>
                                    <?php if (isset($button['link'])): ?>
                                        <div class="col mt-1">
                                            <a style="font-size: .8em;" title="<?= $button['desc'][__LANG] ?>" <?= isset($button['target']) ? "target='{$button['target']}'" : '' ?> class="btn btn-primary btn-block" href="<?= $button['link'] ?>"><?= mb_strtoupper($button['name'][__LANG], 'UTF-8') ?></a>
                                        </div>
                                    <?php else: ?>
                                        <div class="col mt-1 ">
                                            <div class="dropdown w-100">
                                                <button style="font-size: .8em;" class="btn btn-primary dropdown-toggle btn-block" type="button" data-toggle="dropdown" aria-expanded="false">
                                                    <?= mb_strtoupper($button['name'][__LANG], 'UTF-8') ?>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <?php foreach ($button['links'] as $link): ?>
                                                        <a class="dropdown-item" href="<?= $link['href'] ?>"><?= $link['name'][__LANG] ?></a>
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