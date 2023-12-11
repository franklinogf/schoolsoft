<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;

Session::is_logged();
$lang = new Lang([
    ['Lista de estudiantes', 'Students list'],
    ['Atrás', 'Go back'],
]);
$students = new Student();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('Lista de estudiantes');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center my-3"><?= $lang->translation('Lista de estudiantes') ?></h1>
        <form id="form" action="<?= Route::url('/admin/access/reports/pdf/formSocio.php') ?>" method="POST" target="_blank">
            <?php
            $__tableData = $students->all();
            $__tableDataCheckbox = true;
            $__dataPk = 'ss';
            // $__tableDataCheckboxName = 'students';
            $__tableDataInfo = [
                [
                    'title' => ["es" => "Nombre compleo", 'en' => "Full Name"],
                    'values' => ['nombre', 'apellidos']
                ],
            ];
            Route::includeFile('/includes/layouts/table.php', true);
            ?>
        </form>

        <a href="<?= Route::url('/admin/access/reports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>

    </div>
    <?php
    $DataTable = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
    <script>
        $(document).ready(function() {
            $("#form").submit(function(e) {
                tableDataToSubmit("#form", dataTable[0], 'students[]')
            });
        });
    </script>
</body>

</html>