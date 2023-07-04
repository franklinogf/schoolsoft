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
    ['Documentos', 'Documents'],
    ['Estudiante', 'Student'],
    ['Buscar', 'Search'],
    ['Editar', 'Edit'],
    ['Eliminar', 'Delete'],
    ['Lista de documentos', 'Documents list'],
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
    $title = $lang->translation("Documentos");
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>

</head>

<body class='pb-5'>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container mt-5">
        <h1 class="text-center"><?= $lang->translation("Documentos") ?></h1>
        <div class="row">
            <div class="col-12">
                <form method="POST">
                    <select class="form-control selectpicker w-100" name="student" data-live-search="true" required>
                        <option value=""><?= $lang->translation("Seleccionar") . ' ' . $lang->translation('estudiante') ?></option>
                        <?php foreach ($students->All() as $student) : ?>
                            <option <?= isset($_REQUEST['student']) && $_REQUEST['student'] == $student->ss ? 'selected=""' : '' ?> value="<?= $student->ss ?>"><?= "$student->apellidos $student->nombre ($student->id)" ?></option>
                        <?php endforeach ?>
                    </select>
                    <button class="btn btn-primary btn-sm btn-block mt-2" type="submit"><?= $lang->translation("Buscar") ?></button>
                </form>

            </div>
        </div>
        <?php if (isset($_REQUEST['student'])) :
            $documents = DB::table('estudiantes_docs')->where(['ss_estudiante', $_REQUEST['student']])->get();
        ?>
            <h2 class="text-center mt-3 <?php sizeof($documents) > 0 ? '' : 'invisible' ?>"><?= $lang->translation("Lista de documentos") ?></h2>

            <button id="addDocument" class="btn btn-info my-2"><?= $lang->translation("Agregar documento") ?></button>

            <div id="documentsList" class="row row-cols-1 row-cols-md-4">
                <?php if (sizeof($documents) > 0) : ?>
                    <?php foreach ($documents as $document) : ?>
                        <div class="col mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title title"><?= $document->titulo ?></h5>
                                    <p class="card-text"><?= $lang->translation("Fecha de entrega:") ?> <br /> <span class="date"><?= Util::formatDate($document->fecha) ?></span></p>
                                    <a href="<?= Route::url("/admin/users/documents/files/$document->nombre_archivo") ?>" class="btn btn-sm btn-info btn-block" download><?= $lang->translation("Descargar") .' '. File::faIcon(File::extension($document->nombre_archivo))?></a>
                                </div>
                                <div class="card-footer text-center">
                                    <button class="btn btn-primary edit" data-id=<?= $document->id ?>><?= $lang->translation("Editar") ?></button>
                                    <button class="btn btn-danger del" data-id=<?= $document->id ?>><?= $lang->translation("Eliminar") ?></button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                <?php endif ?>
            </div>
        <?php endif ?>
        <div class="modal fade" id="addDocumentModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="addDocumentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="addDocumentForm" action="<?= Route::url('/admin/users/documents/includes/index.php') ?>" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="addDocumentOption" name="option" value="save">
                        <input type="hidden" id="addDocumentId" name="addDocumentId">
                        <input type="hidden" id="addDocumentStudentSs" name="addDocumentStudentSs" value="<?= $_REQUEST['student'] ?>">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addDocumentModalLabel"><?= $lang->translation("Agregar documento") ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="title">Titulo</label>
                                        <input id="title" name="title" class="form-control" type="text">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="date">Fecha</label>
                                        <input id="date" name="date" class="form-control" type="date">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label for="file">Archivo</label>                                    
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="file" name="file">
                                        <label class="custom-file-label" for="file" data-browse="<?= $lang->translation("Buscar") ?>">Seleccionar archivo</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <small id="alertMsg" class="text-danger invisible"><?= $lang->translation("Debe de llenar todos los campos") ?></small>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" id="submitBtn" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>


    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::selectPicker('js');

    ?>

</body>

</html>