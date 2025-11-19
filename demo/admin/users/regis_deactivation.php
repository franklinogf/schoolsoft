<?php
require_once __DIR__ . '/../../app.php';

use Classes\Controllers\Parents;
use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;

Session::is_logged();
$lang = new Lang([
    ['inactivacion de matrícula', 'registration deactivation'],
    ['Activar o desactivar la reinscripción de padres para el año', 'Turn parent re-enrollment on or off for the year'],
    ['Apellidos', 'Last name'],
    ['Nombre', 'Name'],
    ['Grado', 'Grade'],
    ['Guardar', 'Save'],
    ['Cerrar', 'Close'],
    ['Opciones', 'Options'],
    ['Marcar para inactivar', 'Check to inactivate'],
    ['Mensaje para los inactivos', 'Message for inactive'],
    ['Mensaje', 'Message'],
    ['Control del bloqueo', 'Block control'],
    ['Todo el sistema', 'The whole system'],
    ['Area de notas', 'Grades area'],
    ['Día de vencimiento', 'Expiration day'],
    ['Bloqueo automático si debe la cuenta', 'Automatic blocking if the account owe'],
    ['Codigos para controlar el bloqueo', 'Codes to control the block'],
    ['Si', 'Yes'],
    ['Guardado!', 'Saved!'],


]);
$school = new School();
$students = new Student();
$students = $students->all();
$budgets = DB::table('presupuesto')->where('year', $school->year())->get();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation('inactivacion de matrícula');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::fontawasome();
    ?>
</head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Activar o desactivar la reinscripción de padres para el año') . ' ' . $school->year() ?> </h1>
        <div class="container mt-5">
            <button class="btn btn-primary mb-1" data-toggle="modal" data-target="#optionsModal"><?= $lang->translation("Opciones") ?></button>
            <div class="table_wrap">
                <small class="text-muted d-block"><?= $lang->translation("Marcar para inactivar") ?></small>
                <table class="dataTable table table-sm table-pointer table-striped table-hover cell-border shadow">
                    <thead class="bg-gradient-primary bg-primary border-0">
                        <tr class="checkbox">
                            <th style=" width: 1px;">
                                <!-- <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input bg-success checkAll" type="checkbox" id="check1">
                                    <label class="custom-control-label" for="check1"></label>
                                </div> -->
                            </th>
                            <th><?= $lang->translation("Apellidos") ?></th>
                            <th><?= $lang->translation("Nombre") ?></th>
                            <th><?= $lang->translation("Grado") ?></th>
                            <th>ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student) :
                            $parent = new Parents($student->id);
                        ?>
                            <tr>
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input id="student_<?= $student->mt ?>" class="custom-control-input check" type="checkbox" data-id="<?= $student->mt ?>" <?= $student->re_ma === 'SI' ? 'checked=""' : '' ?>>
                                        <label class="custom-control-label" for="student_<?= $student->mt ?>"></label>
                                    </div>
                                </td>
                                <td><?= $student->nombre ?></td>
                                <td><?= $student->apellidos ?></td>
                                <td><?= $student->grado ?></td>
                                <td><?= $student->id ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>


    <div class="modal fade" id="optionsModal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId"><?= $lang->translation("Mensaje para los inactivos") ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-3">
                    <div class="form-group">
                        <label for="message"><?= $lang->translation("Mensaje") ?></label>
                        <textarea class="form-control" name="message" id="message" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <div id="alert" class="alert alert-success mr-auto p-2 hidden" role="alert">
                        <i class="fa-solid fa-square-check"></i> <?= $lang->translation("Guardado!") ?>
                    </div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->translation("Cerrar") ?></button>
                    <button type="button" class="btn btn-primary" id="save" data-dismiss="modal"><?= $lang->translation("Guardar") ?></button>
                </div>
            </div>
        </div>
    </div>


    <?php
    $DataTable = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#save").on('click', function(e) {
                $.post('./includes/regis_deactivation.php', {
                    save: true,
                    message: $("#message").val(),
                    lock: $("#lock").val(),
                    automaticLock: $("#automaticLock").val(),
                }, function(data, textStatus, xhr) {
                    $('#alert').show()
                });
            })
            $('#optionsModal').on('show.bs.modal', function(e) {
                $('#alert').hide()

                $.post('./includes/regis_deactivation.php', {
                    search: true,
                }, function(data, textStatus, xhr) {
                    console.log(data);
                    $("#message").val(data.message)
                    $("#lock").val(data.lock)
                    $("#expireDay").val(data.expireDay)
                    $("#automaticLock").val(data.automaticLock)
                    $("#code1").val(data.code1)
                    $("#code2").val(data.code2)
                    $("#code3").val(data.code3)
                    $("#automaticLock").change()
                }, 'json');
            })
            $("#expireDay").change(function() {
                if ($(this).val() > 30) {
                    $(this).val(30);
                } else if ($(this).val() < 1) {
                    $(this).val(1);
                }
            });

            $("#automaticLock").on('change', function(e) {
                $("#code1,#code2,#code3").prop('disabled', $(this).val() === 'SI' ? false : true);
            });

            $(".custom-control-input.check").on('change', function(e) {
                const id = $(this).data('id');
                const value = !$(this).prop('checked') ? '' : 'SI';
                //                alert("Hello! I am an alert box!!");
                $.post('./includes/regis_deactivation.php', {
                    check: id,
                    value,
                }, function(data, textStatus, xhr) {
                    console.log('data:', data);
                });
            });


        });
    </script>

</body>

</html>