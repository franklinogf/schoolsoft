<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Parents;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();
$parents = new Parents(Session::id());
$year = $parents->info('year');
$amountOfStudents = sizeof($parents->kids());
$lang = new Lang([
    ["Citas", "Appointment"],
    ["Solicitud de cita con maestro(a)", "Appointment request with teacher"],
    ["Estudiante", "Student"],
    ["Buscar maestros de este estudiante", "Find student's teachers"],
    ["Maestros", "Teachers"],
    ["Pare el estudiante", "For the student"],
    ["Crear cita con este maestro", "Create an appointment with this teacher"],
    ["Información de la cita", "Appointment information"],
    ["Nombre del padre o madre", "Parent name"],
    ["Teléfono", "Phone"],
    ["Teléfonos", "Phones"],
    ["Celulares", "Cell phones"],
    ["Correo electrónico", "Email"],
    ["Fecha:", "Date:"],
    ["Hora:", "Time:"],
    ["Maestro(a):", "Teacher:"],
    ["Curso:", "Class:"],
    ["Propósito de la cita", "Appointment purpose"],
    ["Solicitar cita", "Request an appointment"]


]);
?>

<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Citas");
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>
    <section class="container">
        <h1 class="text-center my-3"><?= $lang->translation("Solicitud de cita con maestro(a)") ?></h1>
        <?php if ($amountOfStudents > 1): ?>
            <form method="POST">
                <div class="form-row my-4">
                    <label class="font-weight-bold col-12" for="studentSS"><?= $lang->translation("Estudiante") ?></label>
                    <select name="studentSS" id="studentSS" class="form-control col-12 col-lg-6">
                        <?php foreach ($parents->kids() as $kid): ?>
                            <option <?= isset($_POST['studentSS']) && $_POST['studentSS'] === $kid->ss ? 'selected=""' : '' ?> value="<?= $kid->ss ?>"><?= "$kid->nombre $kid->apellidos -> $kid->grado" ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <button class="btn btn-primary" type="submit"><?= $lang->translation("Buscar maestros de este estudiante") ?></button>
            </form>
        <?php endif ?>

        <?php if (isset($_POST['studentSS'])):
            $teachers = DB::table('padres')->where([
                ['year', $year],
                ['ss', $_POST['studentSS']]
            ])->get();
            $student = new Student($_POST['studentSS']);
            ?>
            <form method="POST">
                <input type="hidden" name="studentSS" value="<?= $_POST['studentSS'] ?>">
                <div class="form-row my-4">
                    <label class="font-weight-bold col-12" for="teacher"><?= $lang->translation("Maestros") ?></label>
                    <select name="teacher" id="teacher" class="form-control col-12 col-lg-6">
                        <?php foreach ($teachers as $teacher):
                            if (isset($_POST['teacher']))
                                [$teacherId, $teacherClass] = explode(',', $_POST['teacher']);
                            ?>
                            <option <?= isset($_POST['teacher']) && $teacherId === $teacher->id && $teacherClass === $teacher->curso ? 'selected=""' : '' ?> value="<?= "$teacher->id,$teacher->curso" ?>"><?= "$teacher->curso - $teacher->profesor" ?></option>
                        <?php endforeach ?>
                    </select>
                    <small class="text-muted col-12"><?= $lang->translation("Pare el estudiante") ?>     <?= $student->fullName() ?></small>
                </div>
                <button class="btn btn-primary" type="submit"><?= $lang->translation("Crear cita con este maestro") ?></button>
            </form>
        <?php endif ?>

        <?php if (isset($_POST['teacher']) && isset($_POST['studentSS'])):
            [$teacherId, $teacherClass] = explode(',', $_POST['teacher']);
            $teacher = DB::table('padres')->where([
                ['year', $year],
                ['ss', $_POST['studentSS']],
                ['id', $teacherId],
                ['curso', $teacherClass]
            ])->first();
            ?>
            <div class="container shadow-lg p-3 my-4">
                <form action="<?= Route::url('/parents/options/appointment/mailAppointment.php') ?>" method="POST">
                    <input type="hidden" name="teacherId" value="<?= $teacherId ?>">
                    <input type="hidden" name="teacherClass" value="<?= $teacherClass ?>">
                    <input type="hidden" name="student" value="<?= "$teacher->nombre $teacher->apellidos" ?>">
                    <input type="hidden" name="date" value="<?= Util::date() ?>">
                    <input type="hidden" name="time" value="<?= Util::time() ?>">
                    <h2 class="text-center"><?= $lang->translation("Información de la cita") ?></h2>
                    <div class="form-row">
                        <label for="parent" class="col-12"><?= $lang->translation("Nombre del padre o madre") ?></label>
                        <select name="parent" id="parent" class="form-control col-12 col-lg-6">
                            <?php if ($parents->madre !== ''): ?>
                                <option value="madre"><?= $parents->madre ?></option><?php endif ?>
                            <?php if ($parents->padre !== ''): ?>
                                <option value="padre"><?= $parents->padre ?></option><?php endif ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <label for="phone" class="col-12"><?= $lang->translation("Teléfono") ?></label>
                        <select name="phone" id="phone" class="form-control col-12 col-lg-6">
                            <optgroup label="<?= $lang->translation("Teléfonos") ?>">
                                <?php if ($parents->tel_m !== ''): ?>
                                    <option value="<?= $parents->tel_m ?>"><?= $parents->tel_m ?></option><?php endif ?>
                                <?php if ($parents->tel_p !== ''): ?>
                                    <option value="<?= $parents->tel_p ?>"><?= $parents->tel_p ?></option><?php endif ?>
                            </optgroup>
                            <optgroup label="<?= $lang->translation("Celulares") ?>">
                                <?php if ($parents->cel_m !== ''): ?>
                                    <option value="<?= $parents->cel_m ?>"><?= $parents->cel_m ?></option><?php endif ?>
                                <?php if ($parents->cel_p !== ''): ?>
                                    <option value="<?= $parents->cel_p ?>"><?= $parents->cel_p ?></option><?php endif ?>
                            </optgroup>
                        </select>
                    </div>
                    <div class="form-row">
                        <label for="email" class="col-12"><?= $lang->translation("Correo electrónico") ?></label>
                        <select name="email" id="email" class="form-control col-12 col-lg-6">
                            <?php if ($parents->email_m !== ''): ?>
                                <option value="<?= $parents->email_m ?>"><?= $parents->email_m ?></option><?php endif ?>
                            <?php if ($parents->email_p !== ''): ?>
                                <option value="<?= $parents->email_p ?>"><?= $parents->email_p ?></option><?php endif ?>
                        </select>
                    </div>

                    <div class="row my-3">
                        <div class="col-12">
                            <div class="card border-info">
                                <div class="card-body row">
                                    <div class="text-monospace col-3"><?= $lang->translation("Fecha:") ?> <span class="badge badge-info"><?= Util::date() ?> </span></div>
                                    <div class="text-monospace col-3"><?= $lang->translation("Hora:") ?> <span class="badge badge-info"><?= Util::time() ?></span></div>
                                    <div class="text-monospace col-4"><?= $lang->translation("Maestro(a):") ?> <span class="badge badge-info"><?= $teacher->profesor ?></span></div>
                                    <div class="text-monospace col-2"><?= $lang->translation("Curso:") ?> <span class="badge badge-info"><?= $teacher->curso ?></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="message" class="col-12"><?= $lang->translation("Propósito de la cita") ?></label>
                        <textarea class="form-control w-100" name="message" id="message" required></textarea>
                    </div>

                    <div class="text-center">
                        <button class="btn btn-primary my-2" type="submit"><?= $lang->translation("Solicitar cita") ?></button>
                    </div>
                </form>
            </div>

        <?php endif ?>
    </section>
    <?php Route::includeFile('/includes/layouts/scripts.php', true); ?>
</body>

</html>