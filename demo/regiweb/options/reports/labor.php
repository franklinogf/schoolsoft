<?php
require_once '../../../app.php';

use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Teacher;

Session::is_logged();
$teacher = new Teacher(Session::id());
?>

<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = "Informe de labor";
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-3">Informe de labor</h1>
        <button class="btn btn-primary ml-3 ml-md-0 mb-3" data-toggle="modal" data-target="#newModal">Nuevo informe</button>
        <div class="bg-white shadow-lg p-3 rounded">
            <div class="form-row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label-form" for="class">Cursos</label>
                        <select name="class" id="class" class="form-control">
                            <option value="">Seleccionar curso</option>
                            <?php foreach ($teacher->classes() as $class) : ?>
                                <option value="<?= $class->curso ?>"><?= "$class->curso - $class->desc1" ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label-form" for="date">Fechas</label>
                        <select name="date" id="date" class="form-control" disabled>
                            <option value="">Seleccionar fecha</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="offset-md-6 col-md-6 text-center">
                    <button id="edit" class="btn btn-info invisible">Editar</button>
                    <button id="delete" class="btn btn-danger invisible">Eliminar</button>
                </div>
                <div class="col-12 text-center mt-3">
                    <button class="btn btn-primary">Imprimir</button>
                    <a href="<?= Route::url('/regiweb/options/') ?>" class="btn btn-secondary">Atrás</a>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="newModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="newModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newModalLabel">Informe de labor</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="newModalForm" action="POST">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="label-form" for="newClass">Cursos</label>
                                        <select name="newClass" id="newClass" class="form-control" required>
                                            <option value="">Seleccionar curso</option>
                                            <?php foreach ($teacher->classes() as $class) : ?>
                                                <option value="<?= $class->curso ?>"><?= "$class->curso - $class->desc1" ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="label-form" for="newDate">Fecha</label>
                                        <input class="form-control" type="date" name="newDate" id="newDate" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="label-form" for="newMatters">Asuntos atendidos</label>
                                        <textarea class="form-control" name="newMatters" id="newMatters"></textarea>
                                    </div>
                                    <div class="form-group row">
                                        <label class="label-form offset-1 col-4 col-form-label" for="newAmountMatters">Cantidad</label>
                                        <div class="col-5">
                                            <input type='number' class="form-control form-control-sm" name="newAmountMatters" id="newAmountMatters"></input>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="label-form" for="newDiscipline">Disciplina</label>
                                        <textarea class="form-control" name="newDiscipline" id="newDiscipline"></textarea>
                                    </div>
                                    <div class="form-group row">
                                        <label class="label-form offset-1 col-4 col-form-label" for="newAmountDiscipline">Cantidad</label>
                                        <div class="col-5">
                                            <input type='number' class="form-control form-control-sm" name="newAmountDiscipline" id="newAmountDiscipline"></input>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="label-form" for="newAssists">Asistencias y tardanzas</label>
                                        <textarea class="form-control" name="newAssists" id="newAssists"></textarea>
                                    </div>
                                    <div class="form-group row">
                                        <label class="label-form offset-1 col-4 col-form-label" for="newAmountAssists">Cantidad</label>
                                        <div class="col-5">
                                            <input type='number' class="form-control form-control-sm" name="newAmountAssists" id="newAmountAssists"></input>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="label-form" for="newFatherInterviews">Entrevistas con padres</label>
                                        <textarea class="form-control" name="newFatherInterviews" id="newFatherInterviews"></textarea>
                                    </div>
                                    <div class="form-group row">
                                        <label class="label-form offset-1 col-4 col-form-label" for="newAmountFatherInterviews">Cantidad</label>
                                        <div class="col-5">
                                            <input type='number' class="form-control form-control-sm" name="newAmountFatherInterviews" id="newAmountFatherInterviews"></input>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="label-form" for="newStudentInterviews">Entrevistas con estudiantes</label>
                                        <textarea class="form-control" name="newStudentInterviews" id="newStudentInterviews"></textarea>
                                    </div>
                                    <div class="form-group row">
                                        <label class="label-form offset-1 col-4 col-form-label" for="newAmountStudentInterviews">Cantidad</label>
                                        <div class="col-5">
                                            <input type='number' class="form-control form-control-sm" name="newAmountStudentInterviews" id="newAmountStudentInterviews"></input>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="label-form" for="newMeetings">Reuniones</label>
                                        <textarea class="form-control" name="newMeetings" id="newMeetings"></textarea>
                                    </div>
                                    <div class="form-group row">
                                        <label class="label-form offset-1 col-4 col-form-label" for="newAmountMeetings">Cantidad</label>
                                        <div class="col-5">
                                            <input type='number' class="form-control form-control-sm" name="newAmountMeetings" id="newAmountMeetings"></input>
                                        </div>
                                    </div>
                                </div>
                                <div class="offset-4 col-md-4">
                                    <div class="form-group mx-auto">
                                        <label class="label-form" for="newOthers">Otros</label>
                                        <textarea class="form-control" name="newOthers" id="newOthers"></textarea>
                                    </div>
                                    <div class="form-group row">
                                        <label class="label-form offset-1 col-4 col-form-label" for="newAmountOthers">Cantidad</label>
                                        <div class="col-5">
                                            <input type='number' class="form-control form-control-sm" name="newAmountOthers" id="newAmountOthers"></input>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button id="newModalSubmitBtn" type="submit" class="btn btn-primary" data-action='save'>Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>