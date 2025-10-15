<?php
require_once '../../app.php';

use App\Enums\AdminPermission;
use Classes\Route;
use Classes\Session;
use App\Models\Admin;

Session::is_logged();

$user  = Admin::user(Session::id())->first();

$options = [
    [
        'title' => __("Opciones"),
        'buttons' => [
            [
                'name' => __("Cuentas / Matricula"),
                'desc' => __("Crear, añadir, modificar las cuentas de los padres y estudiantes."),
                'link' => 'accounts/',
                'hidden' => !$user->hasPermissionTo(AdminPermission::USERS_ACCOUNTS_ENROLLMENT)
            ],
            [
                'name' => __("Entrega de documentos"),
                'desc' => __("Documentación que se pide y entregan a la oficina."),
                'link' => 'documentsDelivery/',
                'hidden' => !$user->hasPermissionTo(AdminPermission::USERS_DOCUMENT_DELIVERY)
            ],
            [
                'name' => __("Re-matrícula"),
                'desc' => __("Pantalla para pasar los estudiantes de un grado al próximo grado. Ejem. 01-A al 02-A."),
                'link' => 'reenrollment/',
                'hidden' => !$user->hasPermissionTo(AdminPermission::USERS_RE_ENROLLMENT)
            ],
            [
                'name' => __("Maestros"),
                'desc' => __("Añadir, borrar o modificar información de los maestros."),
                'link' => 'teachers/',
                'hidden' => !$user->hasPermissionTo(AdminPermission::USERS_TEACHERS)
            ],
            [
                'name' => __("Administración"),
                'desc' => __("Pantalla para crear los usuarios administrativos con areas de acceso."),
                'link' => 'administration/',
                'hidden' => !$user->hasPermissionTo(AdminPermission::USERS_ADMINISTRATIVE)
            ],
            [
                'name' => __("Usuarios de padres"),
                'desc' => __("Es para añadir un usuario adicional al que tiene, para que entren diferentes usuario a la misma cuenta."),
                'link' => '#',
                'hidden' => !$user->hasPermissionTo(AdminPermission::USERS_PARENT_USERS)
            ],
            [
                'name' => __("Modificar nombres"),
                'desc' => __("Pantalla para cambiar los nombre y apellidos de los estudiantes."),
                'link' => '#',
                'hidden' => !$user->hasPermissionTo(AdminPermission::USERS_RENAME_STUDENTS)
            ],
            [
                'name' => __("Memos y deméritos"),
                'desc' => __("Pantalla para entrar los memos y deméritos a los estudiantes."),
                'link' => 'memos/',
                'hidden' => !$user->hasPermissionTo(AdminPermission::USERS_MEMOS_DEMERITS)
            ],
            [
                'name' => __("Pantalla de bajas"),
                'desc' => __("Pantalla para dar de bajas a los estudiantes."),
                'link' => 'unenrollment/',
                'hidden' => !$user->hasPermissionTo(AdminPermission::USERS_WITHDRAWAL_SCREEN)
            ],
            [
                'name' => __("Enfermeria"),
                'desc' => __("Ingrese o vea información médica de los estudiantes."),
                'link' => '#',
                'hidden' => !$user->hasPermissionTo(AdminPermission::USERS_NURSE_OFFICE)
            ],
            [
                'name' => __("Informe de enfermeria"),
                'desc' => __("Informes de Enfermeria."),
                'link' => '#',
                'hidden' => !$user->hasPermissionTo(AdminPermission::USERS_NURSE_REPORTS)
            ],
            [
                'name' => __("Documentos"),
                'desc' => __("Pantalla para guardar los archivos de los estudiantes."),
                'link' => 'documents/',
                'hidden' => false
            ],
            [
                'name' => __("Impresiones"),
                'desc' => __("Pantalla para llevar control de copias."),
                'link' => '#',
                'hidden' => false
            ],
            [
                'name' => __("Cambiar grado"),
                'desc' => __("Pantalla para cambiar el grado"),
                'link' => 'changeGrade/',
                'hidden' => false
            ],
            [
                'name' => __("Cambiar cuenta"),
                'desc' => __("Pantalla para cambiar una cuenta a otra."),
                'link' => 'change_account/',
                'hidden' => false
            ],
            [
                'name' => __("Admisión"),
                'desc' => __("Pantalla para matrículas nuevas."),
                'link' => '#',
                'hidden' => false
            ],
            [
                'name' => __("Enviar usuarios"),
                'desc' => __("Pantalla para enviar los usuarios a los padres."),
                'link' => 'email/sendUsers.php',
                'hidden' => false
            ],
            [
                'name' => __("Buscar correo electrónico"),
                'desc' => __("Pantalla para buscar de quien es el email."),
                'link' => 'search_email/',
                'hidden' => false
            ],
            [
                'name' => __("Bloqueo de re-matrícula"),
                'desc' => __("Acceso o bloqueo de las re-matricula a los padres."),
                'link' => 'regis_deactivation.php',
                'hidden' => false
            ],
            [
                'name' => __('Buscar padres'),
                'desc' => __('Pantalla para ver las familias activas.'),
                'link' => 'active_families.php',
                'hidden' => false
            ],
            [
                'name' => __("Cambiar Alias"),
                'desc' => __("Pantalla para cambiar de Alias."),
                'link' => 'changeAlias/grado.php',
                'hidden' => false
            ],
        ]
    ],
    [
        'title' => __("Entrada de codigos"),
        'buttons' => [
            [
                'name' => __("Códigos de bajas"),
                'desc' => __("Pantalla para entrar los código de bajas."),
                'link' => 'unenrollment/codes.php',
                'hidden' => false
            ],
            [
                'name' => __("Socio económico"),
                'desc' => __("Entrada de valores para el Socio Económico."),
                'link' => 'socioeconomic/codes.php',
                'hidden' => false
            ],
            [
                'name' => __("Códigos especiales"),
                'desc' => __("Entrada de valores Especiales."),
                'link' => 'Special_codes.php',
                'hidden' => false
            ],
            [
                'name' => __("Códigos documentos"),
                'desc' => __("Definir la entrada de los documentos para entregar."),
                'link' => 'documentsDelivery/codes.php',
                'hidden' => false
            ],
            [
                'name' => __("Códigos Departamentos"),
                'desc' => __("Entra los códigos de los departamentos del Colegio."),
                'link' => 'deparment/codes.php',
                'hidden' => false
            ],
            [
                'name' => __("Códigos de memos"),
                'desc' => __("Pantalla para entrar los código de bajas."),
                'link' => 'memos/codes.php',
                'hidden' => false
            ],
            [
                'name' => __("Códigos de mensajes"),
                'desc' => __("Definir los mensajes para la Tarjeta de notas y Hoja de progreso."),
                'link' => 'messagecode/codes.php',
                'hidden' => false
            ],
            [
                'name' => __("Alias"),
                'desc' => __("Entrada de valores para las Alias."),
                'link' => 'alias/codes.php',
                'hidden' => false
            ],
        ]
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
                                    <?php if ($button['hidden']) continue  ?>
                                    <div class="col mt-1">
                                        <a style="font-size: .8em;" title="<?= $button['desc'] ?>" <?= isset($button['target']) ? "target='{$button['target']}'" : '' ?> class="btn btn-primary btn-block" href="<?= Route::url('admin/users/' . $button['link']) ?>"><?= mb_strtoupper($button['name'], 'UTF-8') ?></a>
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