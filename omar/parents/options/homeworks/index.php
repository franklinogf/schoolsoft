<?php
require_once '../../../app.php';

use Classes\File;
use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\Parents;
use Classes\Controllers\Student;

Session::is_logged();
$parents = new Parents(Session::id());
$lang = new Lang([
    ['Tareas', 'Homeworks'],
    ['Tareas asignadas por estudiante', 'Tasks assigned by student'],
    ['No tiene tareas pendientes!', 'Has no pending tasks!'],
    ["Archivo", "File"],
    ["Link", "Link"]
]);

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Tareas");
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-4"><?= $lang->translation("Tareas asignadas por estudiante") ?></h1>
        <?php foreach ($parents->kids() as $kid):
            $student = new Student($kid->mt);
            $homeworks = $student->homeworks();
            ?>
            <h5 class="card-title"><?= "$kid->nombre $kid->apellidos" ?></h5>
            <?php if ($homeworks): ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3">
                    <?php foreach ($homeworks as $homework):
                        $sent = $student->doneHomework($homework->id_documento) ? 'success' : 'white';
                        $cantSend = Util::date() >= $homework->fec_in ? true : false;
                        $expired = $homework->fec_out >= Util::date() || $homework->fec_out === '0000-00-00' ? '' : 'danger';
                        ?>
                        <div class="col mb-4 homework <?= $homework->id_documento ?>">
                            <div class="card <?= $expired === 'danger' ? "border-{$expired}" : "" ?>">
                                <h6 class="card-header bg-gradient-primary bg-primary d-flex justify-content-between">
                                    <?= "{$homework->curso} - {$homework->desc}" ?>
                                    <?php if ($homework->enviartarea === 'si'): ?>
                                        <i class="fas fa-circle text-<?= $sent ?>"></i>
                                    <?php endif ?>
                                </h6>
                                <div class="card-body ">
                                    <h5 class="card-title"><?= $homework->titulo ?></h5>
                                    <p class="card-text"><?= $homework->descripcion ?></p>
                                </div>
                                <div class="card-footer bg-white">
                                    <small class="card-text text-warning d-block"><?= $homework->fec_out !== '0000-00-00' ? "Fecha final: " . Util::formatDate($homework->fec_out, true) : 'Sin fecha de finalización' ?></small>
                                    <?php if (!empty($homework->lin1) || !empty($homework->lin2) || !empty($homework->lin3)): ?>
                                        <div class="btn-group btn-group-sm w-100 mt-2">
                                            <?php for ($i = 1; $i <= 3; $i++): ?>
                                                <?php if ($homework->{"lin{$i}"} !== ''): ?>
                                                    <a href="<?= $homework->{"lin{$i}"} ?>" target="_blank" data-toggle="tooltip" title='<?= $homework->{"lin{$i}"} ?>' class="btn btn-outline-info px-1"><i class="fas fa-external-link-alt"></i> <?= $lang->translation("Link") ?>                         <?= $i ?> </a>
                                                <?php endif ?>
                                            <?php endfor ?>
                                        </div>
                                    <?php endif ?>

                                    <?php if (property_exists($homework, 'archivos') && $cantSend): ?>
                                        <div class="btn-group-vertical w-100 mt-2">
                                            <?php foreach ($homework->archivos as $i => $file): ?>
                                                <a data-file-id="<?= $file->id ?>" target="_blank" href="<?= __TEACHER_HOMEWORKS_DIRECTORY_URL . $file->nombre ?>" data-toggle="tooltip" title='<?= File::name($file->nombre, true) ?>' class="btn btn-outline-dark btn-sm downloadFIle"
                                                    download><?= File::faIcon(File::extension($file->nombre)) . $lang->translation("Archivo") . ($i + 1) ?> </a>
                                            <?php endforeach ?>
                                        </div>
                                    <?php endif ?>

                                </div>
                                <div class="card-footer bg-gradient-secondary bg-secondary d-flex justify-content-between">
                                    <small class="text-primary blend-screen"><?= Util::formatDate($homework->fec_in, true) ?></small>
                                    <small class="text-primary blend-screen"><?= (strpos($homework->hora, '(') > -1 ? $homework->hora : Util::formatTime($homework->hora)) ?></small>
                                </div>
                            </div>

                        </div>
                    <?php endforeach ?>

                </div> <!-- end row -->
            <?php else: ?>
                <div class="alert alert-info mx-auto" role="alert">
                    <?= $lang->translation("No tiene tareas pendientes!") ?> <i class="far fa-laugh-beam"></i>
                </div>
            <?php endif ?>

        <?php endforeach ?>

    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>