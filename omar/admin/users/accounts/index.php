<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;

Session::is_logged();
$students = new Student();
$lang = new Lang([
    ["Cambiar foto de perfil", "Change profile picture"],
    ["Información importante", "Important information"],
    ["Usuario", "Username"],
    ["Grado", "Grade"],
    ["Nombre", "Name"],
    ["Apellidos", "Surnames"],
    ["Correo electrónico principal", "Main email"],
    ["Correo electrónico secundario", "Secondary email"],
    ["Celular", "Cellphone"],
    ["Compañia telefonica", "Cellphone company"],
    ["Nueva contraseña", "New Password"],
    ["Confirmar contraseña", "Confirm Password"],
    ["Las contraseñas no coinciden", "Passwords do not match"],
    ["Guardar", "Save"],

    ['Cuentas', 'Accounts'],
    ['Total de estudiantes', 'Total students'],
    ['Femeninas', 'Female'],
    ['Masculinos', 'Male'],
    ['Total de familias', 'Total families'],
    ['Año escolar', 'School year'],
    ['Información de los padres', 'Parents information']

]);
$year = $students->info('year');
$female = DB::table('year')->whereRaw("year = '$year' AND activo = '' AND (genero = 'F' OR genero = 1)")->get();
$male = DB::table('year')->whereRaw("year = '$year' AND activo = '' AND (genero = 'M' OR genero = 2)")->get();
$families = DB::table('year')->select("DISTINCT id")->where([
    ['year', $year],
])->get();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Cuentas");
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>

</head>

<body class='pb-5'>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-4 col-sm-12">
                <hr class="d-lg-none d-sm-block" />
                <div class="card border-info">
                    <div class="card-body">
                        <p class="text-monospace"><?= $lang->translation("Total de estudiantes") ?>: <span class="badge badge-info"><?= sizeof($students->All()) ?></span></p>
                        <p class="text-monospace"><?= $lang->translation("Femeninas") ?>: <span class="badge badge-info"><?= sizeof($female) ?></span></p>
                        <p class="text-monospace"><?= $lang->translation("Masculinos") ?>: <span class="badge badge-info"><?= sizeof($male) ?></span></p>
                        <p class="text-monospace"><?= $lang->translation("Total de familias") ?>: <span class="badge badge-info"><?= sizeof($families) ?></span></p>
                        <p class="text-monospace"><?= $lang->translation("Año escolar") ?>: <span class="badge badge-info"><?= $year ?></span></p>
                    </div>
                </div>

            </div>
            <div class="col-lg-8 col-sm-12">
                <select class="selectpicker w-75" name="estu" id="estu" title="-Seleccionar al estudiante-" aria-label="Buscar estudiante" data-live-search="true">
                    <?php foreach ($students->All() as $student) : ?>
                        <option value="<?= $student->id ?>"><?= "$student->apellidos $student->nombre ($student->id)" ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <h1 class="text-center mt-3"><?= $lang->translation("Información de los padres") ?> <i class="far fa-id-card"></i></h1>

    </div>


    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::selectPicker('js');

    ?>

</body>

</html>