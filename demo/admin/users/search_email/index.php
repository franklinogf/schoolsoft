<?php
require_once '../../../app.php';

use Classes\Controllers\Parents;
use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;
use Classes\File;

Session::is_logged();
$students = new Student();
$lang = new Lang([
    ['Buscar correo electrónico', 'Search email'],
    ['Estudiante', 'Student'],
    ['Buscar', 'Search'],
    ['Padre', 'Father'],
    ['Madre', 'Mother'],
    ['Correo', 'Email'],
    ['Fecha de entrega:', 'Date of delivery:'],
    ['Descargar', 'Download'],
    ['Agregar documento', 'Add document'],
    ['Buscar', 'Search'],
    ['Debe de llenar todos los campos', 'You must fill all fields'],
]);
$year = $students->info('year');

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Buscar correo electrónico");
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>

</head>

<body class='pb-5'>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container mt-5">
        <h1 class="text-center"><?= $lang->translation("Buscar correo electrónico") ?></h1>
        <div class="container">
            <div class="mx-auto bg-white shadow-lg py-5 px-3 rounded" style="max-width: 300px;">
                <form method="POST">
                    <input type="text" class="form-control" name='email' id="email">
                    <button class="btn btn-primary btn-sm btn-block mt-2" type="submit"><?= $lang->translation("Buscar") ?></button>
                </form>

            </div>
        </div>
        <?php if (isset($_REQUEST['email'])) :
            $email=$_REQUEST['email'];
            $documents = DB::table('madre')
        ->whereRaw("email_m = '$email' or email_p = '$email'")->orderBy('id')->get();

        ?>
            <h2 class="text-center mt-3 <?php sizeof($documents) > 0 ? '' : 'invisible' ?>"><?= $lang->translation("Lista de documentos") ?></h2>


            <div id="documentsList" class="row row-cols-1 row-cols-md-4">
                <?php if (sizeof($documents) > 0) : 
                         foreach ($documents as $estu) 
                                 {
                                 if ($estu->email_p==$email)
                                    {
                ?>
                        <div class="col mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title title">ID: <?= $estu->id ?></h5>
                                    <h5 class="card-title title"><?= $lang->translation("Padre") ?></h5>
                                    <p class="card-text"><span><?= $estu->padre ?></span></p>
                                    <h5 class="card-title title"><?= $lang->translation("Correo") ?></h5>
                                    <p class="card-text"><span><?= $estu->email_p ?></span></p>
                                </div>
                            </div>
                        </div>
                <?php
                                  }
                                 if ($estu->email_m==$email)
                                    {
                ?>
                        <div class="col mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title title">ID: <?= $estu->id ?></h5>
                                    <h5 class="card-title title"><?= $lang->translation("Madre") ?></h5>
                                    <p class="card-text"><span><?= $estu->madre ?></span></p>
                                    <h5 class="card-title title"><?= $lang->translation("Correo") ?></h5>
                                    <p class="card-text"><span><?= $estu->email_m ?></span></p>
                                </div>
                            </div>
                        </div>
                <?php
                                  }
                         }
                     endif
                  ?>
            </div>
        <?php endif ?>
    </div>


    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::selectPicker('js');

    ?>

</body>

</html>