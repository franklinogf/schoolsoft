<?php
require_once '../../../app.php';

use Classes\Controllers\Student;
use Classes\Lang;
use Classes\Route;
use Classes\Session;

Session::is_logged();
$teachers = new Student();
$lang = new Lang([
    ['Seleccionar estudiantes para enviar correo electrónico', 'Select students to send email'],

]);

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Seleccionar estudiantes para enviar correo electrónico");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation("Seleccionar estudiantes para enviar correo electrónico") ?></h1>

        <div class="container bg-white shadow-lg py-3 my-3 rounded">
            <form id="form" action="<?= Route::url('/admin/messages/email/') ?>">

                <?php
                $__tableData = $teachers->all();
                $__tableDataCheckbox = true;
                $__dataPk = 'ss';
                Route::includeFile('/includes/layouts/table.php', true)
                    ?>

            </form>
        </div>

    </div>




    </div>
    <?php
    $DataTable = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>