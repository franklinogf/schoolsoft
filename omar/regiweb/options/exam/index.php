<?php
require_once '../../../app.php';

use Classes\Controllers\Exam;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;
use Classes\DataBase\DB;

Session::is_logged();

$teacher = new Teacher(Session::id());
$exams = new Exam();
$exams = $exams->findByTeacher($teacher->id);

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = "Mensajes y Opciones";
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-1 px-md-0">
        <h1 class="text-center my-3">Generador de examen</h1>
        <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#newExamModal" disabled>Nuevo examen</button>
        <button class="btn btn-info text-dark" type="button" data-toggle="modal" data-target="#searchExamModal" disabled>Buscar Examen</button>

        <div class="row">
            <div class="col-12">
                <div class="form-group mt-3">
                    <label class="label-form" for="title">
                        <h3>Titulo del examen</h3>
                    </label>
                    <input type="text" name="title" id="title" class="form-control shadow-sm" disabled>
                    <div class="invalid-feedback">
                        No puede dejarlo vacío.
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-8 mx-md-auto">
                <h3>Temas del examen</h3>
                <div id="menuButtons" class="list-group">
                    <button type="button" class="list-group-item list-group-item-action option1" data-toggle="modal" data-target="#option1Modal" disabled>Falso y verdadero <span class="float-right"><span class="badge badge-secondary amount">cantidad: 0</span> <span class="badge badge-secondary value">valor: 0</span></span></button>
                    <button type="button" class="list-group-item list-group-item-action option2" data-toggle="modal" data-target="#option2Modal" disabled>Selecciona la respuesta correcta <span class="float-right"><span class="badge badge-secondary amount">cantidad: 0</span> <span class="badge badge-secondary value">valor: 0</span></span></button>
                    <button type="button" class="list-group-item list-group-item-action option3" data-toggle="modal" data-target="#option3Modal" disabled>Parea <span class="float-right"><span class="badge badge-secondary amount">cantidad: 0</span> <span class="badge badge-secondary value">valor: 0</span></span></button>
                    <button type="button" class="list-group-item list-group-item-action option4" data-toggle="modal" data-target="#option4Modal" disabled>Linea en blanco <span class="float-right"><span class="badge badge-secondary amount">cantidad: 0</span> <span class="badge badge-secondary value">valor: 0</span></span></button>
                    <button type="button" class="list-group-item list-group-item-action option5" data-toggle="modal" data-target="#option5Modal" disabled>Preguntas <span class="float-right"><span class="badge badge-secondary amount">cantidad: 0</span> <span class="badge badge-secondary value">valor: 0</span></span></button>
                </div>
                <div class="float-right text-muted mr-3">
                    Total: <span id="examTotalAmount" class="badge badge-pill badge-info amount">cantidad: 0</span> <span id="examTotalValue" class="badge badge-pill badge-info value">valor: 0</span>
                </div>
            </div>
            <div class="col-12 col-md-8 mx-auto">
                <h3 class="my-2">Opciones del examen</h3>
                <row id="settingsButtons" class="row">
                    <div class="col-6 my-1">
                        <button type="button" class="btn btn-outline-primary btn-block text-dark" data-toggle="modal" data-target="#infoExamModal" disabled>Información del examen</button>
                    </div>
                    <div class="col-6 my-1">
                        <button type="button" class="btn btn-outline-primary btn-block text-dark" data-toggle="modal" data-target="#gradeOptionsModal" disabled>Opciones de Notas</button>
                    </div>
                    <div class="col-6 my-1">
                        <button type="button" class="btn btn-outline-primary btn-block text-dark" disabled>Imprimir informe de examen</button>
                    </div>
                    <div class="col-6 my-1">
                        <button type="button" class="btn btn-outline-primary btn-block text-dark" disabled>Corregir preguntas</button>
                    </div>
                    <div class="col-12 my-1">
                        <button type="button" class="btn btn-outline-primary btn-block text-dark" data-toggle="modal" data-target="#correctExamsModal" disabled>Corregir examen</button>
                    </div>
                </row>
            </div>
        </div>

    </div>
    <!-- option1Modal -->
    <div class="modal fade optionModal" data-option-number="1" id="option1Modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="option1ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="option1ModalLabel">Falso y verdadero</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="label-form" for="option1Question">Pregunta</label>
                                <input type="text" name="option1Question" id="option1Question" class="form-control" required>
                                <div class="input-group flex-nowrap">
                                    <select class="form-control" name="option1Answer" id="option1Answer" required>
                                        <option value="v">Verdadero</option>
                                        <option value="f">Falso</option>
                                    </select>
                                    <input id="option1Value" name="option1Value" type="number" class="form-control" placeholder="Valor" aria-label="Valor" aria-describedby="addon-wrapping" min="1" max="100" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button id="option1Add" class="btn btn-primary col-6 d-block mx-auto optionAddButton" data-action='save' disabled>Agregar</button>
                        </div>
                        <div class="col-12">
                            <p class="my-1">Preguntas creadas</p>
                            <ul id="option1CreatedQuestions" class="list-group"></ul>
                        </div>
                        <div class="col-12">
                            <div class="input-group my-3">
                                <div class="input-group-prepend">
                                    <div class="input-group-text py-0">
                                        <label for="option1Check" style="margin-bottom: 0px;">Usar este titulo para el tema</label>
                                        <input class="mx-2" type="checkbox" aria-label="Checkbox for following text input" id="option1Check" name="option1Check" value="si" data-target='option1Description'>
                                    </div>
                                </div>
                                <input id="option1Description" name="option1Desciption" type="text" class="form-control" aria-label="Text input with checkbox" disabled>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- option2Modal -->
    <div class="modal fade optionModal" data-option-number="2" id="option2Modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="option2ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="option2ModalLabel">Selecciona la respuesta correcta</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="label-form" for="option2Question">Pregunta</label>
                                <div class="input-group row">
                                    <input type="text" name="option2Question" id="option2Question" class="form-control col-10" required>
                                    <input id="option2Value" name="option2Value" type="number" class="form-control col-2" placeholder="Valor" aria-label="Valor" aria-describedby="addon-wrapping" min="1" max="100" required>
                                </div>
                            </div>

                        </div>
                        <div class="col-8">
                            <div class="row row-cols-2 answers">
                                <p class="col-12">Respuestas</p>
                                <div class="form-group col">
                                    <label class="label-form" for="option2Answer1">Respuesta #1</label>
                                    <input type="text" name="option2Answer1" id="option2Answer1" class="form-control form-control-sm">
                                </div>
                                <div class="form-group col">
                                    <label class="label-form" for="option2Answer2">Respuesta #2</label>
                                    <input type="text" name="option2Answer2" id="option2Answer2" class="form-control form-control-sm">
                                </div>
                                <div class="form-group col">
                                    <label class="label-form" for="option2Answer3">Respuesta #3</label>
                                    <input type="text" name="option2Answer3" id="option2Answer3" class="form-control form-control-sm">
                                </div>
                                <div class="form-group col">
                                    <label class="label-form" for="option2Answer4">Respuesta #4</label>
                                    <input type="text" name="option2Answer4" id="option2Answer4" class="form-control form-control-sm">
                                </div>
                                <div class="form-group col">
                                    <label class="label-form" for="option2Answer5">Respuesta #5</label>
                                    <input type="text" name="option2Answer5" id="option2Answer5" class="form-control form-control-sm">
                                </div>
                                <div class="form-group col">
                                    <label class="label-form" for="option2Answer6">Respuesta #6</label>
                                    <input type="text" name="option2Answer6" id="option2Answer6" class="form-control form-control-sm">
                                </div>
                                <div class="form-group col">
                                    <label class="label-form" for="option2Answer7">Respuesta #7</label>
                                    <input type="text" name="option2Answer7" id="option2Answer7" class="form-control form-control-sm">
                                </div>
                                <div class="form-group col">
                                    <label class="label-form" for="option2Answer8">Respuesta #8</label>
                                    <input type="text" name="option2Answer8" id="option2Answer8" class="form-control form-control-sm">
                                </div>

                            </div>
                        </div>
                        <div class="col-4">
                            <p>Respuesta correcta</p>
                            <select class="form-control" name="option2Answer" id="option2Answer" required>
                                <option value="1">Respuesta #1</option>
                                <option value="2">Respuesta #2</option>
                                <option value="3">Respuesta #3</option>
                                <option value="4">Respuesta #4</option>
                                <option value="5">Respuesta #5</option>
                                <option value="6">Respuesta #6</option>
                                <option value="7">Respuesta #7</option>
                                <option value="8">Respuesta #8</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button id="option2Add" class="btn btn-primary col-6 d-block mx-auto optionAddButton" data-action='save' disabled>Agregar</button>
                        </div>
                        <div class="col-12">
                            <p class="my-1">Preguntas creadas</p>
                            <ul id="option2CreatedQuestions" class="list-group"></ul>
                        </div>
                        <div class="col-12">
                            <div class="input-group my-3">
                                <div class="input-group-prepend">
                                    <div class="input-group-text py-0">
                                        <label for="option2Check" style="margin-bottom: 0px;">Usar este titulo para el tema</label>
                                        <input class="mx-2" type="checkbox" aria-label="Checkbox for following text input" id="option2Check" name="option2Check" value="si" data-target='option2Description'>
                                    </div>
                                </div>
                                <input id="option2Description" name="option2Desciption" type="text" class="form-control" aria-label="Text input with checkbox" disabled>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- option3Modal -->
    <div class="modal fade optionModal" data-option-number="3" id="option3Modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="option3ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="option3ModalLabel">Parea</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="option3Code">Respuestas para el parea</label>
                                <input type="text" name="option3Code" id="option3Code" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <button id="option3AddCode" class="btn btn-primary col-6 d-block mx-auto">Agregar</button>
                        </div>
                        <div class="col-12">
                            <p class="my-1">Repuestas creadas</p>
                            <ul id="option3CreatedAnswers" class="list-group">

                            </ul>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="label-form" for="option3Question">Pregunta</label>
                                <input type="text" name="option3Question" id="option3Question" class="form-control" required>
                                <div class="input-group flex-nowrap">
                                    <select class="form-control col-10" name="option3Answer" id="option3Answer" required>

                                    </select>
                                    <input id="option3Value" name="option3Value" type="number" class="form-control col-4" placeholder="Valor" aria-label="Valor" aria-describedby="addon-wrapping" min="1" max="100" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button id="option3Add" class="btn btn-primary col-6 d-block mx-auto optionAddButton" data-action='save' disabled>Agregar</button>
                        </div>
                        <div class="col-12">
                            <p class="my-1">Preguntas creadas</p>
                            <ul id="option3CreatedQuestions" class="list-group"></ul>
                        </div>
                        <div class="col-12">
                            <div class="input-group my-3">
                                <div class="input-group-prepend">
                                    <div class="input-group-text py-0">
                                        <label for="option3Check" style="margin-bottom: 0px;">Usar este titulo para el tema</label>
                                        <input class="mx-2" type="checkbox" aria-label="Checkbox for following text input" id="option3Check" name="option3Check" value="si" data-target='option3Description'>
                                    </div>
                                </div>
                                <input id="option3Description" name="option3Desciption" type="text" class="form-control" aria-label="Text input with checkbox" disabled>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- option4Modal -->
    <div class="modal fade optionModal" data-option-number="4" id="option4Modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="option4ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="option4ModalLabel">Linea en blanco</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="label-form" for="option4Question">Pregunta</label>
                                <div class="input-group row">
                                    <input type="text" name="option4Question" id="option4Question" class="form-control col-10" required>
                                    <input id="option4Value" name="option4Value" type="number" class="form-control col-2" placeholder="Valor" aria-label="Valor" aria-describedby="addon-wrapping" min="1" max="100" required>
                                </div>
                                <small class="text-warning">Utilice 3 _ (guion abajo) por cada respuesta, en orden.</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="option4AnswersAmount">Cantidad de respuestas</label>
                                <input style="width: 5rem;" id="option4AnswersAmount" name="option4AnswersAmount" type="number" class="form-control" aria-label="Cantidad" min="1" max="5" required>
                            </div>
                            <div class="form-group">
                                <p class="mb-0">Respuestas</p>
                                <div id="option4Answers">

                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <button id="option4Add" class="btn btn-primary col-6 d-block mx-auto optionAddButton" data-action='save' disabled>Agregar</button>
                        </div>
                        <div class="col-12">
                            <p class="my-1">Preguntas creadas</p>
                            <ul id="option4CreatedQuestions" class="list-group"></ul>
                        </div>
                        <div class="col-12">
                            <div class="input-group my-3">
                                <div class="input-group-prepend">
                                    <div class="input-group-text py-0">
                                        <label for="option4Check" style="margin-bottom: 0px;">Usar este titulo para el tema</label>
                                        <input class="mx-2" type="checkbox" aria-label="Checkbox for following text input" id="option4Check" name="option4Check" value="si" data-target='option4Description'>
                                    </div>
                                </div>
                                <input id="option4Description" name="option4Desciption" type="text" class="form-control" aria-label="Text input with checkbox" disabled>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- option5Modal -->
    <div class="modal fade optionModal" data-option-number="5" id="option5Modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="option5ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="option5ModalLabel">Preguntas</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="label-form" for="option5Question">Pregunta</label>
                                <textarea name="option5Question" id="option5Question" rows="3" class="form-control" required></textarea>
                                <div class="input-group flex-nowrap">
                                    <input id="option5AmountOfLines" name="option5AmountOfLines" type="number" class="form-control" placeholder="Cantidad de lineas en blanco para la respuesta" aria-label="Cantidad de lineas en blanco para la respuesta" aria-describedby="addon-wrapping" min="1" max="10" value="1" required>
                                    <input id="option5Value" name="option5Value" type="number" class="form-control" placeholder="Valor" aria-label="Valor" aria-describedby="addon-wrapping" min="1" max="100" required>
                                </div>
                                <div class="d-flex justify-content-around">
                                    <small class="text-muted">Cantidad de lineas</small>
                                    <small class="text-muted">Valor de la pregunta</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button id="option5Add" class="btn btn-primary col-6 d-block mx-auto optionAddButton" data-action='save' disabled>Agregar</button>
                        </div>
                        <div class="col-12">
                            <p class="my-1">Preguntas creadas</p>
                            <ul id="option5CreatedQuestions" class="list-group"></ul>
                        </div>
                        <div class="col-12">
                            <div class="input-group my-3">
                                <div class="input-group-prepend">
                                    <div class="input-group-text py-0">
                                        <label for="option5Check" style="margin-bottom: 0px;">Usar este titulo para el tema</label>
                                        <input class="mx-2" type="checkbox" aria-label="Checkbox for following text input" id="option5Check" name="option5Check" value="si" data-target='option5Description'>
                                    </div>
                                </div>
                                <input id="option5Description" name="option5Desciption" type="text" class="form-control" aria-label="Text input with checkbox" disabled>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- newExamModal -->
    <div class="modal fade" id="newExamModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="newExamModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newExamModalLabel">Nuevo examen</h5>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="label-form" for="newExamTitle">Titulo</label>
                                <input type="text" name="newExamTitle" id="newExamTitle" class="form-control" required>
                                <div class="invalid-feedback">
                                    No puede dejarlo vacío.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="label-form" for="newExamGrade">Curso</label>
                                <select name="newExamGrade" id="newExamGrade" class="form-control" required>
                                    <?php foreach ($teacher->classes() as $class) : ?>
                                        <option value="<?= $class->curso ?>"><?= "$class->curso - $class->desc1" ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button id="newExamButton" type="button" class="btn btn-primary">Crear</button>
                </div>
            </div>
        </div>
    </div>
    <!-- searchExamModal -->
    <div class="modal fade" id="searchExamModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="searchExamModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchExamModalLabel">Buscar examen</h5>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="label-form" for="searchExamId">Titulo</label>
                                <select name="searchExamId" id="searchExamId" class="form-control" required>
                                    <?php foreach ($exams as $exam) : ?>
                                        <option value="<?= $exam->id ?>"><?= $exam->titulo ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button id="searchExamBtn" type="button" class="btn btn-primary">Buscar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- deleteModal -->
    <div class="modal fade" id="deleteModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body d-flex justify-content-between bg-danger">
                    <p class="mb-0">¿Esta seguro que desea eliminarlo?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
                    <button id="deleteButton" type="button" class="btn btn-danger btn-sm">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- infoExamModal -->
    <div class="modal fade" id="infoExamModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="infoExamModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="infoExamForm" class="needs-validation" novalidate>
                    <div class="modal-header">
                        <h5 class="modal-title" id="infoExamModalLabel">Información del examen</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="label-form" for="infoExamGrade">Curso</label>
                                    <select name="infoExamGrade" id="infoExamGrade" class="form-control" required>
                                        <?php foreach ($teacher->classes() as $class) : ?>
                                            <option value="<?= $class->curso ?>"><?= "$class->curso - $class->desc1" ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="label-form" for="infoExamStartTime">Hora de inicio</label>
                                    <input type="time" name="infoExamStartTime" id="infoExamStartTime" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="label-form" for="infoExamEndTime">Hora de termino</label>
                                    <input type="time" name="infoExamEndTime" id="infoExamEndTime" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="label-form" for="infoExamDate">Fecha</label>
                                    <input type="date" name="infoExamDate" id="infoExamDate" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="label-form" for="infoExamTime">Tiempo para coger el examen en minutos</label>
                                    <input type="number" name="infoExamTime" id="infoExamTime" class="form-control mx-auto w-50" min="1" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <p>Permitir ver la nota</p>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="infoExamPreviewGrade1" name="infoExamPreviewGrade" class="custom-control-input" value="si" required>
                                        <label class="custom-control-label" for="infoExamPreviewGrade1">Si</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="infoExamPreviewGrade2" name="infoExamPreviewGrade" class="custom-control-input" value="no" required>
                                        <label class="custom-control-label" for="infoExamPreviewGrade2">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <p>Examen disponible</p>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="infoExamAvailability1" name="infoExamAvailability" class="custom-control-input" value="si" required>
                                        <label class="custom-control-label" for="infoExamAvailability1">Si</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="infoExamAvailability2" name="infoExamAvailability" class="custom-control-input" value="no" required>
                                        <label class="custom-control-label" for="infoExamAvailability2">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#duplicateExamModal">Duplicar Examen</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button id="infoExamButton" type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- duplicateExam -->
    <div class="modal fade" id="duplicateExamModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="duplicateExamModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="duplicateExamModalLabel">Nuevo examen</h5>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="label-form" for="duplicateExamTitle">Titulo</label>
                                <input type="text" name="duplicateExamTitle" id="duplicateExamTitle" class="form-control" required>
                                <div class="invalid-feedback">
                                    No puede dejarlo vacío.
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="label-form" for="duplicateExamGrade">Curso</label>
                                <select name="duplicateExamGrade" id="duplicateExamGrade" class="form-control" required>
                                    <?php foreach ($teacher->classes() as $class) : ?>
                                        <option value="<?= $class->curso ?>"><?= "$class->curso - $class->desc1" ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button id="duplicateExamButton" type="button" class="btn btn-primary">Duplicar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- gradeOptionsModal -->
    <div class="modal fade" id="gradeOptionsModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="gradeOptionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="gradeOptionsModalLabel">Opciones de notas</h5>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-4">
                            <div class="form-group">
                                <label class="label-form" for="gradeOptionsModalGrade">Curso</label>
                                <select name="gradeOptionsModalGrade" id="gradeOptionsModalGrade" class="form-control" required>
                                    <?php foreach ($teacher->classes() as $class) : ?>
                                        <option value="<?= $class->curso ?>"><?= "$class->curso - $class->desc1" ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="label-form" for="gradeOptionsTrimester">Trimestre</label>
                                <select name="gradeOptionsTrimester" id="gradeOptionsTrimester" class="form-control" required>
                                    <?php for ($i = 1; $i <= 4; $i++) : ?>
                                        <option value="Trimestre-<?= $i ?>">Trimestre <?= $i ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="label-form" for="gradeOptionsType">Tipo de nota</label>
                                <select name="gradeOptionsType" id="gradeOptionsType" class="form-control" required>
                                    <option value="Notas">Notas</option>
                                    <option value="Pruebas-Cortas">Pruebas Cortas</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <button id="gradeOptionsSearchButton" class="btn btn-primary btn-block">Buscar</button>
                        </div>
                    </div>
                    <div class="mt-3 row">
                        <div class="col-6">
                            <p class="font-weight-bold text-center">Descripción</p>
                        </div>
                        <div class="col-2">
                            <p class="font-weight-bold text-center">Valor</p>
                        </div>
                        <div class="col-4">
                            <p class="font-weight-bold text-center">Fecha</p>
                        </div>
                    </div>
                    <div id="gradeOptionsList">
                        <?php for ($i = 1; $i <= 10; $i++) : ?>
                            <div class="row mt-2">
                                <div class="col-1 text-center">
                                    <input type="radio" style="width:25px; height:25px;" class="align-middle" id="gradeOptionsSelected<?= $i ?>" name="gradeOptionsSelected" value="<?= $i ?>" disabled>
                                </div>
                                <div class="col-5">
                                    <input class="form-control form-control-sm" type="text" name="gradeOptionsDescription<?= $i ?>" id="gradeOptionsDescription<?= $i ?>" disabled>
                                </div>
                                <div class="col-2">
                                    <input class="form-control form-control-sm" type="number" name="gradeOptionsValue<?= $i ?>" id="gradeOptionsValue<?= $i ?>" min="1" max="100" disabled>
                                </div>
                                <div class="col-4">
                                    <input class="form-control form-control-sm" type="date" name="gradeOptionsDate<?= $i ?>" id="gradeOptionsDate<?= $i ?>" disabled>
                                </div>
                            </div>
                        <?php endfor ?>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button id="gradeOptionsButton" type="button" class="btn btn-primary" disabled>Guardar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- correctExamsModal -->
    <div class="modal fade" id="correctExamsModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="correctExamsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="correctExamsModalLabel">Estudiantes que han tomado el examen</h5>
                </div>
                <div class="modal-body">
                    <div class="table_wrap">
                        <table id="correctExamsTable" class="table table-striped table-hover cell-border w-100 shadow">
                            <thead class="bg-gradient-primary bg-primary border-0">
                                <tr>
                                    <th>Estudiante</th>
                                    <th>Fecha</th>
                                    <th>Puntos</th>
                                    <th>Porcentaje</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                            <tfoot>
                                <tr class="bg-gradient-secondary bg-secondary">
                                    <th>Estudiante</th>
                                    <th>Fecha</th>
                                    <th>Puntos</th>
                                    <th>Porcentaje</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button id="correctExamsButton" type="button" class="btn btn-primary" disabled>Corregir examenes</button>
                    <button id="correctExamsButton2" type="button" class="btn btn-info" disabled>Pasar puntos</button>
                    <button id="correctExamsButton3" type="button" class="btn btn-info" disabled>Pasar porcentajes</button>
                    <button id="correctExamsButton4" type="button" class="btn btn-warning" disabled>Dar oportunidad</button>
                </div>
            </div>
        </div>
    </div>
    <?php
    $DataTable = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>