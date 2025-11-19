<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;
use Classes\DataBase\DB;

Session::is_logged();

$teacher = new Teacher(Session::id());
$homeworks = $teacher->homeworks();
$cursos = $teacher->classes();
$lang = new Lang([
    ["Pantalla de notas por examen", "Notes screen per exam"],
    ["Cursos", "Courses"],
    ["Pagina", "Page"],
    ["Cuatrimestres", "Quarters"],
    ["Titulo", "Title"],
    ["Descripción", "Description"],
    ["Curso", "Class"],
    ["Fecha Inicial", "Initial date"],
    ["Trimestre 1", "Quarter 1"],
    ["Trimestre 2", "Quarter 2"],
    ["Trimestre 3", "Quarter 3"],
    ["Trimestre 4", "Quarter 4"],
    ["La tarea estará disponible en esta fecha", "The homework will be available on this date"],
    ["La tarea dejará de estar disponible después de esta fecha", "The homework will no longer be available after this date"],
    ["¿Recibir archivos de los estudiantes?", "Receive files from students?"],
    ["Si", "Yes"],
    ["Si: se puede recibir archivos de los estudiantes", "Yes: files can be received from students"],
    ["No: no se va a recibir archivos de los estudiantes", "No: files are not going to be received from students"],
    ["Links", "Links"],
    ["Link", "Link"],
    ["Agregar archivos", "Add files"],
    ["Guardar", "Save"],
    ["Al guardar la tarea se le enviara un correo a los padres.", "When saving the homework, an email will be sent to the parents."],
    ["Notas", "Grades"],
    ["Pruebas cortas", "Short tests"],
    ["Trabajos Diarios", "Daily Work"],
    ["Trabajos Libreta", "Notebook Work"],
    ["Continuar", "Continue"],
    ["Atrás", "Back"],
    ["Sin fecha de finalización", "No final date"]
]);

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <?php
    $title = $lang->translation("Pantalla de notas por examen");
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
    <title>Examen</title>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-3"><?= $lang->translation("Pantalla de notas por examen") ?></h1>


        <div id="container">
            <div class="mx-auto bg-white shadow-lg py-5 px-3 rounded" style="max-width: 700px;">
                <form action="examenespdf.php" method="POST" target="examenes">
                    <div class="style11">
                        <table cellpadding="2" cellspacing="0" border="0" style="width: 650px">
                            <thead>
                                <tr class="gris">
                                    <th><?= $lang->translation('Cursos') ?></th>
                                    <th><?= $lang->translation('Cuatrimestres') ?></th>
                                    <th><?= $lang->translation('Paginas') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select class="big" name="curso">
                                            <?php foreach ($cursos as $row): ?>
                                                <option value="<?= $row->curso ?>"><?= "$row->curso - $row->desc1"; ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="cuatrimestre">
                                            <option value="Trimestre-1"><?= $lang->translation('Trimestre 1') ?></option>
                                            <option value="Trimestre-2"><?= $lang->translation('Trimestre 2') ?></option>
                                            <option value="Trimestre-3"><?= $lang->translation('Trimestre 3') ?></option>
                                            <option value="Trimestre-4"><?= $lang->translation('Trimestre 4') ?></option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="pagina">
                                            <option value="Notas"><?= $lang->translation('Notas') ?></option>
                                            <option value="Pruebas-Cortas"><?= $lang->translation('Pruebas Cortas') ?></option>
                                            <option value="Trab-Diarios"><?= $lang->translation('Trabajos Diarios') ?></option>
                                            <option value="Trab-Libreta"><?= $lang->translation('Trabajos Libreta') ?></option>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                            <tr>
                            </tr>
                        </table>
                        <br>
                        <br>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary"><?= $lang->translation('Continuar') ?></button>
                            <a href="../index.php" class="btn btn-primary"><?= $lang->translation('Atrás') ?></a>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>