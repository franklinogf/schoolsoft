<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\Exam;
use Classes\Controllers\Teacher;

Session::is_logged();

$teacher = new Teacher(Session::id());
$exams = new Exam();
$exams = $exams->findByTeacher($teacher->id);

$lang = new Lang([
    ["Generador de examen", "Exam generator"],
    ["Nuevo examen", "New exam"],
    ["Buscar examen", "Search exam"],
    ["Carta", "Letter"],
    ["Hoja legal", "Legal sheet"],
    ["Imprimir examen", "Print exam"],
    ["Titulo del examen", "Exam title"],
    ["No puede dejarlo vacío.", "Can't leave it empty."],
    ["Temas del examen", "Exam topics"],
    ["Falso y verdadero", "False and true"],
    ["Selecciona la respuesta correcta", "Select the correct answer"],
    ["Parea", "Match"],
    ["Línea en blanco", "Blank line"],
    ["Preguntas", "Questions"],
    ["cantidad:", "amount:"],
    ["valor:", "value:"],
    ["Opciones del examen", "Exam options"],
    ["Información del examen", "Exam information"],
    ["Opciones de notas", "Grades options"],
    ["Imprimir informe de examen", "Print exam report"],
    ["Corregir examen", "Correct exam"],
    ["Pregunta", "Question"],
    ["Falso", "False"],
    ["Verdadero", "True"],
    ["Preguntas creadas", "Created questions"],
    ["Usar este titulo para este tema", "Use this title for this topic"],
    ["Agregar", "Add"],
    ["Cerrar", "Close"],
    ["Respuestas", "Answers"],
    ["Respuesta", "Answer"],
    ["Respuesta correcta", "Correct answer"],
    ["Respuestas creadas", "Created answers"],
    ["Valor", "Value"],
    ["Utilice 3 _ (guion bajo) por cada respuesta, en orden.", "Use 3 _ (underscore) for each answer, in order."],
    ["Cantidad de respuestas", "Amount of answers"],
    ["Cantidad de líneas", "Number of lines"],
    ["Valor de la pregunta", "Question value"],
    ["Cantidad de líneas en blanco para la respuesta", "Number of blank lines for the answer"],
    ["Titulo", "Title"],
    ["Curso", "Class"],
    ["Crear", "Create"],
    ["Buscar", "Search"],
    ["¿Está seguro que desea eliminarlo?", "Are you sure you want to delete it?"],
    ["Eliminar", "Delete"],
    ["Hora de inicio", "Start time"],
    ["Hora de término", "End time"],
    ["Fecha", "Date"],
    ["Tiempo para coger el examen en minutos", "Time to take the exam in minutes"],
    ["Si", "Yes"],
    ["Permitir ver la nota del examen", "Allow to see the grade of the exam"],
    ["¿Examen disponible?", "Exam available?"],
    ["Duplicar examen", "Duplicate exam"],
    ["Eliminar examen", "Delete exam"],
    ["Guardar", "Save"],
    ["Duplicar", "Duplicate"],
    ["Tipo de nota", "Grade type"],
    ["Notas", "Grades"],
    ["Descripción", "Description"],
    ["Estudiantes que han tomado el examen", "Students who have taken the exam"],
    ["Estudiante", "Student"],
    ["Puntos", "Points"],
    ["Porcentaje", "Percent"],
    ["Corregir examenes", "Correct exams"],
    ["Pasar puntos", "Pass points"],
    ["Pasar porcentajes", "Pass percentages"],
    ["Dar oportunidad", "Give opportunity"],
    ["¿Desea eliminar este examen?", "Do you want to delete this exam?"],
    ["Cancelar", "Cancel"]
]);

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Generador de examen");
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-1 px-md-0">
        <h1 class="text-center my-3"><?= $lang->translation("Generador de examen") ?></h1>
        <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#newExamModal" disabled><?= $lang->translation("Nuevo examen") ?></button>
        <button class="btn btn-info text-dark" type="button" data-toggle="modal" data-target="#searchExamModal" disabled><?= $lang->translation("Buscar Examen") ?></button>
        <form action="<?= Route::url('/regiweb/options/exam/pdf/printExam.php') ?>" method="POST" class="float-right" target="printExam">
            <input type="hidden" name="printExamId" class="printExamId">
            <select class="printExam" name="paperSize" required disabled>
                <option value="A4" selected><?= $lang->translation("Carta") ?></option>
                <option value="Legal"><?= $lang->translation("Hoja legal") ?></option>
            </select>
            <button class="btn btn-secondary printExam" type="submit" disabled><?= $lang->translation("Imprimir examen") ?></button>
        </form>

        <div class="row">
            <div class="col-12">
                <div class="form-group mt-3">
                    <label class="label-form" for="title">
                        <h3><?= $lang->translation("Titulo del examen") ?></h3>
                    </label>
                    <input type="text" name="title" id="title" class="form-control shadow-sm" disabled>
                    <div class="invalid-feedback">
                        <?= $lang->translation("No puede dejarlo vacío.") ?>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-8 mx-md-auto">
                <h3><?= $lang->translation("Temas del examen") ?></h3>
                <div id="menuButtons" class="list-group">
                    <button type="button" class="list-group-item list-group-item-action option1" data-toggle="modal" data-target="#option1Modal" disabled><?= $lang->translation("Falso y verdadero") ?> <span class="float-right"><span
                                class="badge badge-secondary amount"><?= $lang->translation("cantidad:") ?> 0</span> <span class="badge badge-secondary value"><?= $lang->translation("valor:") ?> 0</span></span></button>
                    <button type="button" class="list-group-item list-group-item-action option2" data-toggle="modal" data-target="#option2Modal" disabled><?= $lang->translation("Selecciona la respuesta correcta") ?> <span class="float-right"><span
                                class="badge badge-secondary amount"><?= $lang->translation("cantidad:") ?> 0</span> <span class="badge badge-secondary value"><?= $lang->translation("valor:") ?> 0</span></span></button>
                    <button type="button" class="list-group-item list-group-item-action option3" data-toggle="modal" data-target="#option3Modal" disabled><?= $lang->translation("Parea") ?> <span class="float-right"><span class="badge badge-secondary amount"><?= $lang->translation("cantidad:") ?>
                                0</span> <span class="badge badge-secondary value"><?= $lang->translation("valor:") ?> 0</span></span></button>
                    <button type="button" class="list-group-item list-group-item-action option4" data-toggle="modal" data-target="#option4Modal" disabled><?= $lang->translation("Línea en blanco") ?> <span class="float-right"><span
                                class="badge badge-secondary amount"><?= $lang->translation("cantidad:") ?> 0</span> <span class="badge badge-secondary value"><?= $lang->translation("valor:") ?> 0</span></span></button>
                    <button type="button" class="list-group-item list-group-item-action option5" data-toggle="modal" data-target="#option5Modal" disabled><?= $lang->translation("Preguntas") ?> <span class="float-right"><span class="badge badge-secondary amount"><?= $lang->translation("cantidad:") ?>
                                0</span> <span class="badge badge-secondary value"><?= $lang->translation("valor:") ?> 0</span></span></button>
                </div>
                <div class="float-right text-muted mr-3">
                    Total: <span id="examTotalAmount" class="badge badge-pill badge-info amount"><?= $lang->translation("cantidad:") ?> 0</span> <span id="examTotalValue" class="badge badge-pill badge-info value"><?= $lang->translation("valor:") ?> 0</span>
                </div>
            </div>
            <div class="col-12 col-md-8 mx-auto">
                <h3 class="my-2"><?= $lang->translation("Opciones del examen") ?></h3>
                <row id="settingsButtons" class="row">
                    <div class="col-6 my-1">
                        <button type="button" class="btn btn-outline-primary btn-block text-dark" data-toggle="modal" data-target="#infoExamModal" disabled><?= $lang->translation("Información del examen") ?></button>
                    </div>
                    <div class="col-6 my-1">
                        <button type="button" class="btn btn-outline-primary btn-block text-dark" data-toggle="modal" data-target="#gradeOptionsModal" disabled><?= $lang->translation("Opciones de notas") ?></button>
                    </div>
                    <div class="col-6 my-1">
                        <form action="<?= Route::url('/regiweb/options/exam/pdf/doneExams.php') ?>" method="POST" target="doneExamPdf">
                            <input type="hidden" name="printExamId" class="printExamId">
                            <button type="submit" class="btn btn-outline-primary btn-block text-dark" disabled><?= $lang->translation("Imprimir informe de examen") ?></button>
                        </form>
                    </div>
                    <div class="col-6 my-1">
                        <button type="button" class="btn btn-outline-primary btn-block text-dark" data-toggle="modal" data-target="#correctExamsModal" disabled><?= $lang->translation("Corregir examen") ?></button>
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
                    <h5 class="modal-title" id="option1ModalLabel"><?= $lang->translation("Falso y verdadero") ?></h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="label-form" for="option1Question"><?= $lang->translation("Pregunta") ?></label>
                                <input type="text" name="option1Question" id="option1Question" class="form-control" required>
                                <div class="input-group flex-nowrap">
                                    <select class="form-control" name="option1Answer" id="option1Answer" required>
                                        <option value="v"><?= $lang->translation("Verdadero") ?></option>
                                        <option value="f"><?= $lang->translation("Falso") ?></option>
                                    </select>
                                    <input id="option1Value" name="option1Value" type="number" class="form-control" placeholder="<?= $lang->translation("Valor") ?>" aria-label="<?= $lang->translation("Valor") ?>" aria-describedby="addon-wrapping" min="1" max="100" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button id="option1Add" class="btn btn-primary col-6 d-block mx-auto optionAddButton" data-action='save' disabled><?= $lang->translation("Agregar") ?></button>
                        </div>
                        <div class="col-12">
                            <p class="my-1"><?= $lang->translation("Preguntas creadas") ?></p>
                            <ul id="option1CreatedQuestions" class="list-group"></ul>
                        </div>
                        <div class="col-12">
                            <div class="input-group my-3">
                                <div class="input-group-prepend">
                                    <div class="input-group-text py-0">
                                        <label for="option1Check" class="mb-0"><?= $lang->translation("Usar este titulo para este tema") ?></label>
                                        <input class="mx-2 optionCheck mt-1" data-check='desc1' type="checkbox" aria-label="Checkbox for following text input" id="option1Check" name="option1Check" value="si" data-target='option1Description'>
                                    </div>
                                </div>
                                <input id="option1Description" name="option1Description" type="text" class="form-control optionDescription" data-desc='desc1_1' aria-label="Text input with checkbox" disabled>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->translation("Cerrar") ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- option2Modal -->
    <div class="modal fade optionModal" data-option-number="2" id="option2Modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="option2ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="option2ModalLabel"><?= $lang->translation("Selecciona la respuesta correcta") ?></h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="label-form" for="option2Question"><?= $lang->translation("Pregunta") ?></label>
                                <div class="input-group row">
                                    <input type="text" name="option2Question" id="option2Question" class="form-control col-10" required>
                                    <input id="option2Value" name="option2Value" type="number" class="form-control col-2" placeholder="<?= $lang->translation("Valor") ?>" aria-label="<?= $lang->translation("Valor") ?>" aria-describedby="addon-wrapping" min="1" max="100" required>
                                </div>
                            </div>

                        </div>
                        <div class="col-8">
                            <div class="row row-cols-2 answers">
                                <p class="col-12"><?= $lang->translation("Respuestas") ?></p>
                                <div class="form-group col">
                                    <label class="label-form" for="option2Answer1"><?= $lang->translation("Respuesta") ?> #1</label>
                                    <input type="text" name="option2Answer1" id="option2Answer1" class="form-control form-control-sm">
                                </div>
                                <div class="form-group col">
                                    <label class="label-form" for="option2Answer2"><?= $lang->translation("Respuesta") ?> #2</label>
                                    <input type="text" name="option2Answer2" id="option2Answer2" class="form-control form-control-sm">
                                </div>
                                <div class="form-group col">
                                    <label class="label-form" for="option2Answer3"><?= $lang->translation("Respuesta") ?> #3</label>
                                    <input type="text" name="option2Answer3" id="option2Answer3" class="form-control form-control-sm">
                                </div>
                                <div class="form-group col">
                                    <label class="label-form" for="option2Answer4"><?= $lang->translation("Respuesta") ?> #4</label>
                                    <input type="text" name="option2Answer4" id="option2Answer4" class="form-control form-control-sm">
                                </div>
                                <div class="form-group col">
                                    <label class="label-form" for="option2Answer5"><?= $lang->translation("Respuesta") ?> #5</label>
                                    <input type="text" name="option2Answer5" id="option2Answer5" class="form-control form-control-sm">
                                </div>
                                <div class="form-group col">
                                    <label class="label-form" for="option2Answer6"><?= $lang->translation("Respuesta") ?> #6</label>
                                    <input type="text" name="option2Answer6" id="option2Answer6" class="form-control form-control-sm">
                                </div>
                                <div class="form-group col">
                                    <label class="label-form" for="option2Answer7"><?= $lang->translation("Respuesta") ?> #7</label>
                                    <input type="text" name="option2Answer7" id="option2Answer7" class="form-control form-control-sm">
                                </div>
                                <div class="form-group col">
                                    <label class="label-form" for="option2Answer8"><?= $lang->translation("Respuesta") ?> #8</label>
                                    <input type="text" name="option2Answer8" id="option2Answer8" class="form-control form-control-sm">
                                </div>

                            </div>
                        </div>
                        <div class="col-4">
                            <p><?= $lang->translation("Respuesta correcta") ?></p>
                            <select class="form-control" name="option2Answer" id="option2Answer" required>
                                <option value="1"><?= $lang->translation("Respuesta") ?> #1</option>
                                <option value="2"><?= $lang->translation("Respuesta") ?> #2</option>
                                <option value="3"><?= $lang->translation("Respuesta") ?> #3</option>
                                <option value="4"><?= $lang->translation("Respuesta") ?> #4</option>
                                <option value="5"><?= $lang->translation("Respuesta") ?> #5</option>
                                <option value="6"><?= $lang->translation("Respuesta") ?> #6</option>
                                <option value="7"><?= $lang->translation("Respuesta") ?> #7</option>
                                <option value="8"><?= $lang->translation("Respuesta") ?> #8</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button id="option2Add" class="btn btn-primary col-6 d-block mx-auto optionAddButton" data-action='save' disabled><?= $lang->translation("Agregar") ?></button>
                        </div>
                        <div class="col-12">
                            <p class="my-1"><?= $lang->translation("Preguntas creadas") ?></p>
                            <ul id="option2CreatedQuestions" class="list-group"></ul>
                        </div>
                        <div class="col-12">
                            <div class="input-group my-3">
                                <div class="input-group-prepend">
                                    <div class="input-group-text py-0">
                                        <label for="option2Check" class="mb-0"><?= $lang->translation("Usar este titulo para este tema") ?></label>
                                        <input class="mx-2 optionCheck mt-1" data-check='desc2' type="checkbox" aria-label="Checkbox for following text input" id="option2Check" name="option2Check" value="si" data-target='option2Description'>
                                    </div>
                                </div>
                                <input id="option2Description" name="option2Description" type="text" class="form-control optionDescription" data-desc='desc2_1' aria-label="Text input with checkbox" disabled>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->translation("Cerrar") ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- option3Modal -->
    <div class="modal fade optionModal" data-option-number="3" id="option3Modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="option3ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="option3ModalLabel"><?= $lang->translation("Parea") ?></h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="option3Code"><?= $lang->translation("Respuestas") ?></label>
                                <input type="text" name="option3Code" id="option3Code" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <button id="option3AddCode" class="btn btn-primary col-6 d-block mx-auto"><?= $lang->translation("Agregar") ?></button>
                        </div>
                        <div class="col-12">
                            <p class="my-1"><?= $lang->translation("Repuestas creadas") ?></p>
                            <ul id="option3CreatedAnswers" class="list-group">

                            </ul>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="label-form" for="option3Question"><?= $lang->translation("Pregunta") ?></label>
                                <input type="text" name="option3Question" id="option3Question" class="form-control" required>
                                <div class="input-group flex-nowrap">
                                    <select class="form-control col-10" name="option3Answer" id="option3Answer" required>

                                    </select>
                                    <input id="option3Value" name="option3Value" type="number" class="form-control col-4" placeholder="<?= $lang->translation("Valor") ?>" aria-label="<?= $lang->translation("Valor") ?>" aria-describedby="addon-wrapping" min="1" max="100" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button id="option3Add" class="btn btn-primary col-6 d-block mx-auto optionAddButton" data-action='save' disabled><?= $lang->translation("Agregar") ?></button>
                        </div>
                        <div class="col-12">
                            <p class="my-1"><?= $lang->translation("Preguntas creadas") ?></p>
                            <ul id="option3CreatedQuestions" class="list-group"></ul>
                        </div>
                        <div class="col-12">
                            <div class="input-group my-3">
                                <div class="input-group-prepend">
                                    <div class="input-group-text py-0">
                                        <label for="option3Check" class="mb-0"><?= $lang->translation("Usar este titulo para este tema") ?></label>
                                        <input class="mx-2 optionCheck mt-1" data-check='desc3' type="checkbox" aria-label="Checkbox for following text input" id="option3Check" name="option3Check" value="si" data-target='option3Description'>
                                    </div>
                                </div>
                                <input id="option3Description" name="option3Description" type="text" class="form-control optionDescription" data-desc='desc3_1' aria-label="Text input with checkbox" disabled>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->translation("Cerrar") ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- option4Modal -->
    <div class="modal fade optionModal" data-option-number="4" id="option4Modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="option4ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="option4ModalLabel"><?= $lang->translation("Línea en blanco") ?></h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="label-form" for="option4Question"><?= $lang->translation("Pregunta") ?></label>
                                <div class="input-group row">
                                    <input type="text" name="option4Question" id="option4Question" class="form-control col-10" required>
                                    <input id="option4Value" name="option4Value" type="number" class="form-control col-2" placeholder="<?= $lang->translation("Valor") ?>" aria-label="<?= $lang->translation("Valor") ?>" aria-describedby="addon-wrapping" min="1" max="100" required>
                                </div>
                                <small class="text-warning"><?= $lang->translation("Utilice 3 _ (guion bajo) por cada respuesta, en orden.") ?></small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="option4AnswersAmount"><?= $lang->translation("Cantidad de respuestas") ?></label>
                                <input style="width: 5rem;" id="option4AnswersAmount" name="option4AnswersAmount" type="number" class="form-control" aria-label="Cantidad" min="1" max="5" required>
                            </div>
                            <div class="form-group">
                                <p class="mb-0"><?= $lang->translation("Respuestas") ?></p>
                                <div id="option4Answers">

                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <button id="option4Add" class="btn btn-primary col-6 d-block mx-auto optionAddButton" data-action='save' disabled><?= $lang->translation("Agregar") ?></button>
                        </div>
                        <div class="col-12">
                            <p class="my-1"><?= $lang->translation("Preguntas creadas") ?></p>
                            <ul id="option4CreatedQuestions" class="list-group"></ul>
                        </div>
                        <div class="col-12">
                            <div class="input-group my-3">
                                <div class="input-group-prepend">
                                    <div class="input-group-text py-0">
                                        <label for="option4Check" class="mb-0"><?= $lang->translation("Usar este titulo para este tema") ?></label>
                                        <input class="mx-2 optionCheck mt-1" data-check='desc4' type="checkbox" aria-label="Checkbox for following text input" id="option4Check" name="option4Check" value="si" data-target='option4Description'>
                                    </div>
                                </div>
                                <input id="option4Description" name="option4Description" type="text" class="form-control optionDescription" data-desc='desc4_1' aria-label="Text input with checkbox" disabled>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->translation("Cerrar") ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- option5Modal -->
    <div class="modal fade optionModal" data-option-number="5" id="option5Modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="option5ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="option5ModalLabel"><?= $lang->translation("Preguntas") ?></h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="label-form" for="option5Question"><?= $lang->translation("Pregunta") ?></label>
                                <textarea name="option5Question" id="option5Question" rows="3" class="form-control" required></textarea>
                                <div class="input-group flex-nowrap">
                                    <input id="option5AmountOfLines" name="option5AmountOfLines" type="number" class="form-control" placeholder="<?= $lang->translation("Cantidad de líneas en blanco para la respuesta") ?>"
                                        aria-label="<?= $lang->translation("Cantidad de líneas en blanco para la respuesta") ?>" aria-describedby="addon-wrapping" min="1" max="10" value="1" required>
                                    <input id="option5Value" name="option5Value" type="number" class="form-control" placeholder="<?= $lang->translation("Valor") ?>" aria-label="<?= $lang->translation("Valor") ?>" aria-describedby="addon-wrapping" min="1" max="100" required>
                                </div>
                                <div class="d-flex justify-content-around">
                                    <small class="text-muted"><?= $lang->translation("Cantidad de líneas") ?></small>
                                    <small class="text-muted"><?= $lang->translation("Valor de la pregunta") ?></small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button id="option5Add" class="btn btn-primary col-6 d-block mx-auto optionAddButton" data-action='save' disabled><?= $lang->translation("Agregar") ?></button>
                        </div>
                        <div class="col-12">
                            <p class="my-1"><?= $lang->translation("Preguntas creadas") ?></p>
                            <ul id="option5CreatedQuestions" class="list-group"></ul>
                        </div>
                        <div class="col-12">
                            <div class="input-group my-3">
                                <div class="input-group-prepend">
                                    <div class="input-group-text py-0">
                                        <label for="option5Check" class="mb-0"><?= $lang->translation("Usar este titulo para este tema") ?></label>
                                        <input class="mx-2 optionCheck mt-1" data-check='desc5' type="checkbox" aria-label="Checkbox for following text input" id="option5Check" name="option5Check" value="si" data-target='option5Description'>
                                    </div>
                                </div>
                                <input id="option5Description" name="option5Description" type="text" class="form-control optionDescription" data-desc='desc5_1' aria-label="Text input with checkbox" disabled>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->translation("Cerrar") ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- newExamModal -->
    <div class="modal fade" id="newExamModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="newExamModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newExamModalLabel"><?= $lang->translation("Nuevo examen") ?></h5>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="label-form" for="newExamTitle"><?= $lang->translation("Titulo") ?></label>
                                <input type="text" name="newExamTitle" id="newExamTitle" class="form-control" required>
                                <div class="invalid-feedback">
                                    <?= $lang->translation("No puede dejarlo vacío.") ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="label-form" for="newExamGrade"><?= $lang->translation("Curso") ?></label>
                                <select name="newExamGrade" id="newExamGrade" class="form-control" required>
                                    <?php foreach ($teacher->classes() as $class): ?>
                                        <option value="<?= $class->curso ?>"><?= "$class->curso - $class->desc1" ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->translation("Cerrar") ?></button>
                    <button id="newExamButton" type="button" class="btn btn-primary"><?= $lang->translation("Crear") ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- searchExamModal -->
    <div class="modal fade" id="searchExamModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="searchExamModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchExamModalLabel"><?= $lang->translation("Buscar examen") ?></h5>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="label-form" for="searchExamId"><?= $lang->translation("Titulo") ?></label>
                                <select name="searchExamId" id="searchExamId" class="form-control" required>
                                    <?php foreach ($exams as $exam): ?>
                                        <option value="<?= $exam->id ?>"><?= $exam->titulo ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->translation("Cerrar") ?></button>
                    <button id="searchExamBtn" type="button" class="btn btn-primary"><?= $lang->translation("Buscar") ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- deleteModal -->
    <div class="modal fade" id="deleteModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body d-flex justify-content-between bg-danger">
                    <p class="mb-0"><?= $lang->translation("¿Está seguro que desea eliminarlo?") ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><?= $lang->translation("Cerrar") ?></button>
                    <button id="deleteButton" type="button" class="btn btn-danger btn-sm"><?= $lang->translation("Eliminar") ?></button>
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
                        <h5 class="modal-title" id="infoExamModalLabel"><?= $lang->translation("Información del examen") ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="label-form" for="infoExamGrade"><?= $lang->translation("Curso") ?></label>
                                    <select name="infoExamGrade" id="infoExamGrade" class="form-control" required>
                                        <?php foreach ($teacher->classes() as $class): ?>
                                            <option value="<?= $class->curso ?>"><?= "$class->curso - $class->desc1" ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="label-form" for="infoExamStartTime"><?= $lang->translation("Hora de inicio") ?></label>
                                    <input type="time" name="infoExamStartTime" id="infoExamStartTime" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="label-form" for="infoExamEndTime"><?= $lang->translation("Hora de término") ?></label>
                                    <input type="time" name="infoExamEndTime" id="infoExamEndTime" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="label-form" for="infoExamDate"><?= $lang->translation("Fecha") ?></label>
                                    <input type="date" name="infoExamDate" id="infoExamDate" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="label-form" for="infoExamTime"><?= $lang->translation("Tiempo para coger el examen en minutos") ?></label>
                                    <input type="number" name="infoExamTime" id="infoExamTime" class="form-control mx-auto w-50" min="1" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <p><?= $lang->translation("Permitir ver la nota del examen") ?></p>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="infoExamPreviewGrade1" name="infoExamPreviewGrade" class="custom-control-input" value="si" required>
                                        <label class="custom-control-label" for="infoExamPreviewGrade1"><?= $lang->translation("Si") ?></label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="infoExamPreviewGrade2" name="infoExamPreviewGrade" class="custom-control-input" value="no" required>
                                        <label class="custom-control-label" for="infoExamPreviewGrade2">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <p><?= $lang->translation("¿Examen disponible?") ?></p>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="infoExamAvailability1" name="infoExamAvailability" class="custom-control-input" value="si" required>
                                        <label class="custom-control-label" for="infoExamAvailability1"><?= $lang->translation("Si") ?></label>
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
                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#duplicateExamModal"><?= $lang->translation("Duplicar Examen") ?></button>
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteExamModal"><?= $lang->translation("Eliminar Examen") ?></button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->translation("Cerrar") ?></button>
                        <button id="infoExamButton" type="submit" class="btn btn-primary"><?= $lang->translation("Guardar") ?></button>
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
                    <h5 class="modal-title" id="duplicateExamModalLabel"><?= $lang->translation("Nuevo examen") ?></h5>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="label-form" for="duplicateExamTitle"><?= $lang->translation("Titulo") ?></label>
                                <input type="text" name="duplicateExamTitle" id="duplicateExamTitle" class="form-control" required>
                                <div class="invalid-feedback">
                                    <?= $lang->translation("No puede dejarlo vacío.") ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="label-form" for="duplicateExamGrade"><?= $lang->translation("Curso") ?></label>
                                <select name="duplicateExamGrade" id="duplicateExamGrade" class="form-control" required>
                                    <?php foreach ($teacher->classes() as $class): ?>
                                        <option value="<?= $class->curso ?>"><?= "$class->curso - $class->desc1" ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->translation("Cerrar") ?></button>
                    <button id="duplicateExamButton" type="button" class="btn btn-primary"><?= $lang->translation("Duplicar") ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- gradeOptionsModal -->
    <div class="modal fade" id="gradeOptionsModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="gradeOptionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="gradeOptionsModalLabel"><?= $lang->translation("Opciones de notas") ?></h5>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-4">
                            <div class="form-group">
                                <label class="label-form" for="gradeOptionsModalGrade"><?= $lang->translation("Curso") ?></label>
                                <select name="gradeOptionsModalGrade" id="gradeOptionsModalGrade" class="form-control" required>
                                    <?php foreach ($teacher->classes() as $class): ?>
                                        <option value="<?= $class->curso ?>"><?= "$class->curso - $class->desc1" ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="label-form" for="gradeOptionsTrimester"><?= $lang->translation("Trimestre") ?></label>
                                <select name="gradeOptionsTrimester" id="gradeOptionsTrimester" class="form-control" required>
                                    <?php for ($i = 1; $i <= 4; $i++): ?>
                                        <option value="Trimestre-<?= $i ?>"><?= $lang->translation("Trimestre") ?>     <?= $i ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="label-form" for="gradeOptionsType"><?= $lang->translation("Tipo de nota") ?></label>
                                <select name="gradeOptionsType" id="gradeOptionsType" class="form-control" required>
                                    <option value="Notas"><?= $lang->translation("Notas") ?></option>
                                    <option value="Pruebas-Cortas"><?= $lang->translation("Pruebas cortas") ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <button id="gradeOptionsSearchButton" class="btn btn-primary btn-block"><?= $lang->translation("Buscar") ?></button>
                        </div>
                    </div>
                    <div class="mt-3 row">
                        <div class="col-6">
                            <p class="font-weight-bold text-center"><?= $lang->translation("Descripción") ?></p>
                        </div>
                        <div class="col-2">
                            <p class="font-weight-bold text-center"><?= $lang->translation("Valor") ?></p>
                        </div>
                        <div class="col-4">
                            <p class="font-weight-bold text-center"><?= $lang->translation("Fecha") ?></p>
                        </div>
                    </div>
                    <div id="gradeOptionsList">
                        <?php for ($i = 1; $i <= 10; $i++): ?>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->translation("Cerrar") ?></button>
                    <button id="gradeOptionsButton" type="button" class="btn btn-primary" disabled><?= $lang->translation("Guardar") ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- correctExamsModal -->
    <div class="modal fade" id="correctExamsModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="correctExamsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="correctExamsModalLabel"><?= $lang->translation("Estudiantes que han tomado el examen") ?></h5>
                </div>
                <div class="modal-body">
                    <div class="table_wrap">
                        <table id="correctExamsTable" class="table table-striped table-hover cell-border w-100 shadow">
                            <thead class="bg-gradient-primary bg-primary border-0">
                                <tr>
                                    <th><?= $lang->translation("Estudiante") ?></th>
                                    <th><?= $lang->translation("Fecha") ?></th>
                                    <th><?= $lang->translation("Puntos") ?></th>
                                    <th><?= $lang->translation("Porcentaje") ?></th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr class="bg-gradient-secondary bg-secondary">
                                    <th><?= $lang->translation("Estudiante") ?></th>
                                    <th><?= $lang->translation("Fecha") ?></th>
                                    <th><?= $lang->translation("Puntos") ?></th>
                                    <th><?= $lang->translation("Porcentaje") ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->translation("Cerrar") ?></button>
                    <button id="correctExamsButton" type="button" class="btn btn-primary" disabled><?= $lang->translation("Corregir examenes") ?></button>
                    <button id="correctExamsButton2" type="button" class="btn btn-info" disabled><?= $lang->translation("Pasar puntos") ?></button>
                    <button id="correctExamsButton3" type="button" class="btn btn-info" disabled><?= $lang->translation("Pasar porcentajes") ?></button>
                    <button id="correctExamsButton4" type="button" class="btn btn-warning" disabled><?= $lang->translation("Dar oportunidad") ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewExamModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="viewExamModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-body bg-light">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->translation("Cerrar") ?></button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteExamModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="deleteExamModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-danger">
                <div class="modal-body">
                    <p><?= $lang->translation("¿Desea eliminar este examen?") ?></p>
                </div>
                <div class="modal-footer border-danger">
                    <button class="btn btn-danger" id="deleteExamButton"><?= $lang->translation("Eliminar") ?></button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->translation("Cancelar") ?></button>
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