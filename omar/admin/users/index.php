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
                'name' => ["es" => 'Cuentas / Matricula', "en" => "Accounts / Enrollment"],
                'desc' => ['es' => 'Crear, añadir, modificar las cuentas de los padres y estudiantes.', 'en' => 'Create, add, modify parents and students accounts.'],
                'link' => 'accounts/'
            ],
            [
                'name' => ["es" => 'Entrega de documentos', "en" => "Documents delivery"],
                'desc' => ['es' => 'Documentación que se pide y entregan a la oficina.', 'en' => 'Documents to be handle to the office.'],
                'link' => 'documentsDelivery/'
            ],
            [
                'name' => ["es" => 'Re-matrícula', "en" => "Re-enrollment"],
                'desc' => ['es' => 'Pantalla para pasar los estudiantes de un grado al próximo grado. Ejem. 01-A al 02-A', 'en' => 'Screen to pass the students of a grade to the next grade. e.i. 01-A to 02-A'],
                'link' => 'reenrollment/'
            ],
            [
                'name' => ["es" => 'Maestros', "en" => "Teachers"],
                'desc' => ['es' => 'Añadir, borrar o modificar información de los maestros.', 'en' => 'Add, update or delete teachers information.'],
                'link' => 'teachers/'
            ],
            [
                'name' => ["es" => 'Administración', "en" => "Administración"],
                'desc' => ['es' => 'Pantalla para crear los usuarios administrativos con areas de acceso.', 'en' => 'Pantalla para crear los usuarios administrativos con areas de acceso.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Usuarios de padres', "en" => "Usuarios de padres"],
                'desc' => ['es' => 'Es para añadir un usuario adicional al que tiene, para que entren diferentes usuario a la misma cuenta.', 'en' => 'Es para añadir un usuario adicional al que tiene, para que entren diferentes usuario a la misma cuenta.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Modificar nombres', "en" => "Modificar nombres"],
                'desc' => ['es' => 'Pantalla para cambiar los nombre y apellidos de los estudiantes.', 'en' => 'Pantalla para cambiar los nombre y apellidos de los estudiantes.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Memos y deméritos', "en" => "Memos and demerits"],
                'desc' => ['es' => 'Pantalla para entrar los memos y deméritos a los estudiantes.', 'en' => 'Screen to enter the memos and demerit to the students.'],
                'link' => 'memos/'
            ],
            [
                'name' => ["es" => 'Pantalla de bajas', "en" => "Unenrollment"],
                'desc' => ['es' => 'Pantalla para dar de bajas a los estudiantes.', 'en' => 'Screen to unenroll students.'],
                'link' => 'unenrollment/'
            ],
            [
                'name' => ["es" => 'Enfermeria', "en" => "Enfermeria"],
                'desc' => ['es' => 'Ingrese o vea información médica de los estudiantes.', 'en' => 'Ingrese o vea información médica de los estudiantes.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Informe de enfermeria', "en" => "Informe de enfermeria"],
                'desc' => ['es' => 'Informes de Enfermeria.', 'en' => 'Informes de Enfermeria.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Documentos', "en" => "Documents"],
                'desc' => ['es' => 'Pantalla para guardar los archivos de los estudiantes.', 'en' => 'Screen to save the files for the students.'],
                'link' => 'documents/'
            ],
            [
                'name' => ["es" => 'Impresiones', "en" => "Impresiones"],
                'desc' => ['es' => 'Pantalla para llevar control de copias.', 'en' => 'Pantalla para llevar control de copias.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Cambiar grado', "en" => "Cambiar grado"],
                'desc' => ['es' => 'Pantalla para cambiar el grado', 'en' => 'Screen to change grade'],
                'link' => 'changeGrade/'
            ],
            [
                'name' => ["es" => 'Cambiar cuenta', "en" => "Change account"],
                'desc' => ['es' => 'Pantalla para cambiar una cuenta a otra.', 'en' => 'Screen to change one account to another.'],
                'link' => 'change_account/'
            ],
            [
                'name' => ["es" => 'Admisión', "en" => "Admisión"],
                'desc' => ['es' => 'Pantalla para matrículas nuevas.', 'en' => 'Pantalla para matrículas nuevas.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Enviar usuarios', "en" => "Send users"],
                'desc' => ['es' => 'Pantalla para enviar los usuarios a los padres.', 'en' => 'Screen to send users to parents.'],
                'link' => 'email/sendUsers.php'
            ],
            [
                'name' => ["es" => 'Buscar correo electrónico', "en" => "Search email"],
                'desc' => ['es' => 'Pantalla para buscar de quien es el email.', 'en' => 'Screen to search for who the email is from.'],
                'link' => 'search_email/'
            ],
            [
                'name' => ["es" => 'Bloqueo de re-matrícula', "en" => "Re-Enrollment lock"],
                'desc' => ['es' => 'Acceso o bloqueo de las re-matricula a los padres.', 'en' => 'Access or blockage of re-enrollment to parents.'],
                'link' => 'regis_deactivation.php'
            ],
        ]
    ],
    [
        'title' => ["es" => 'Entrada de codigos', "en" => 'Code entry'],
        'buttons' => [
            [
                'name' => ["es" => 'Códigos de bajas', "en" => "Unenrollment codes"],
                'desc' => ['es' => 'Pantalla para entrar los código de bajas.', 'en' => 'Screen to enter the deregistration codes.'],
                'link' => 'unenrollment/codes.php'
            ],
            [
                'name' => ["es" => 'Socio económico', "en" => "Socioeconomic"],
                'desc' => ['es' => 'Entrada de valores para el Socio Económico.', 'en' => 'Input of values for the Socioeconomic.'],
                'link' => 'socioeconomic/codes.php'
            ],
            [
                'name' => ["es" => 'Códigos especiales', "en" => "Special codes"],
                'desc' => ['es' => 'Entrada de valores Especiales.', 'en' => 'Entry of Special values.'],
                'link' => 'Special_codes.php'
            ],
            [
                'name' => ["es" => 'Códigos documentos', "en" => "Documents codes"],
                'desc' => ['es' => 'Definir la entrada de los documentos para entregar.', 'en' => 'Define the entry of documents to deliver.'],
                'link' => 'documentsDelivery/codes.php'
            ],
            [
                'name' => ["es" => 'Códigos Departamentos', "en" => "Department codes"],
                'desc' => ['es' => 'Entra los códigos de los departamentos del Colegio.', 'en' => 'Enter the codes of the College departments.'],
                'link' => 'deparment/codes.php'
            ],
            [
                'name' => ["es" => 'Códigos de memos', "en" => "Memos codes"],
                'desc' => ['es' => 'Pantalla para entrar los código de bajas.', 'en' => 'Screen to enter the deregistration codes.'],
                'link' => 'memos/codes.php'
            ],
            [
                'name' => ["es" => 'Códigos de mensajes', "en" => "Message codes"],
                'desc' => ['es' => 'Definir los mensajes para la Tarjeta de notas y Hoja de progreso.', 'en' => 'Define messages for the Note Card and Progress Sheet.'],
                'link' => 'messagecode/codes.php'
            ]
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

            <?php foreach ($options as $option): ?>
                <div class="col mb-4">
                    <fieldset class="border border-secondary rounded-bottom h-100 px-2">
                        <legend class="w-auto"><?= $option['title'][__LANG] ?></legend>
                        <div class="pb-3">
                            <div class="row row-cols-2">
                                <?php foreach ($option['buttons'] as $button): ?>
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