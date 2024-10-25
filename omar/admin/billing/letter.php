<?php
require_once '../../app.php';

use Classes\Controllers\Parents;
use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;

Session::is_logged();
$lang = new Lang([
    ['Lista de deudores', 'List of debtors'],
    ['Atrás', 'Go back'],
    ['Nombre', 'Name'],
    ['Grado', 'Grade'],
    ['Deudas', 'Debtors'],
    ['Apellidos', 'Last Name'],
    ['Procesar', 'Process'],
    ['Enviar por E-mail', 'Send E-mail'],
    ['Se enviaron por correo electrónico todas las deudas que están marcadas en la lista.', 'All debts that are marked on the list were sent by email.'],
]);
$school = new School(Session::id());
$year = $school->info('year2');
$students = new Student();
$students = $students->all();

$debe = 0;
foreach ($students as $student) {
    $debe = 0;
    $result10 = DB::table('pagos')
        ->whereRaw(" id='$student->id' and ss='$student->ss' and year='$year' and baja='' and fecha_d <= '" . date('Y-m-d') . "'")->orderBy('codigo')->get();
    foreach ($result10 as $row10) {
        $debe = $debe + ($row10->deuda - $row10->pago);
    }
    $thisCourse2 = DB::table("year")->where([
        ['id', $student->id],
        ['ss', $student->ss],
        ['year', $year]
    ])->update([
        'tr1' => $debe,
    ]);
}

$students = DB::table('year')
    ->whereRaw("year='$year' and activo='' and tr1 > 0 ")->orderBy('tr1 DESC')->get();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation('Lista de deudores');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::fontawasome();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Lista de deudores') . ' ' . $year ?> </h1>
        <div class="container mt-1">
            <form action="pdf/letter_inf.php" method="post" target="_blank">
                <div class="table_wrap">
                    <table class="dataTable table table-sm table-pointer table-striped table-hover cell-border shadow">
                        <thead class="bg-gradient-primary bg-primary border-0">
                            <tr class="checkbox">
                                <th style=" width: 1px;">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input bg-success checkAll" type="checkbox" id="check1">
                                        <label class="custom-control-label" for="check1"></label>
                                    </div>
                                </th>
                                <th><?= $lang->translation("Apellidos") ?></th>
                                <th><?= $lang->translation("Nombre") ?></th>
                                <th><?= $lang->translation("Grado") ?></th>
                                <th>ID</th>
                                <th><?= $lang->translation("Deudas") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student) :
                                $parent = new Parents($student->id);
                            ?>
                                <tr>
                                    <td>
                                        <div class="custom-control custom-checkbox">
                                            <input id="student_<?= $student->mt ?>" class="custom-control-input check" type="checkbox" data-id="<?= $student->id ?>" <?= ($parent->activo !== 'Activo') ? 'checked=""' : '' ?>>
                                            <label class="custom-control-label" for="student_<?= $student->mt ?>"></label>
                                        </div>
                                    </td>
                                    <td><?= $student->nombre ?></td>
                                    <td><?= $student->apellidos ?></td>
                                    <td><?= $student->grado ?></td>
                                    <td><?= $student->id ?></td>
                                    <td><?= $student->tr1 ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <div><b>
                        <center>
                            <span lang="en-us"><?= $lang->translation("Enviar por E-mail") ?></span>
                            <input name="m6" type="checkbox" value="Si" style="height: 25px; width: 25px"><br />
                            <span lang="en-us"><?= $lang->translation("Se enviaron por correo electrónico todas las deudas que están marcadas en la lista.") ?></span>
                        </center>
                    </b></div>
                <input name="buscar" style="width: 140px;" class="btn btn-primary mx-auto d-block mt-2" type="submit" value="<?= $lang->translation("Procesar") ?>" />
            </form>
        </div>
    </div>

    <?php
    $DataTable = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#save").on('click', function(e) {
                $.post(includeThisFile(), {
                    save: true,
                    message: $("#message").val(),
                    lock: $("#lock").val(),
                    expireDay: $("#expireDay").val(),
                    automaticLock: $("#automaticLock").val(),
                    code1: $("#code1").val(),
                    code2: $("#code2").val(),
                    code3: $("#code3").val(),
                }, function(data, textStatus, xhr) {
                    $('#alert').show()
                });

            })
            $('#optionsModal').on('show.bs.modal', function(e) {
                $('#alert').hide()

                $.post(includeThisFile(), {
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
                $("#code1,#code2,#code3").prop('disabled', $(this).val() === 'Si' ? false : true);
            });

            $(".custom-control-input.check").on('change', function(e) {
                const id = $(this).data('id')
                const value = !$(this).prop('checked') ? 'Activo' : 'Inactivo'

                $.post(includeThisFile(), {
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