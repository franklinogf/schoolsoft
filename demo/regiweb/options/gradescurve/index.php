<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Controllers\Teacher;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\Route;
use Classes\Session;

Session::is_logged();

$teacher = new Teacher(Session::id());
$homeworks = $teacher->homeworks();
$cursos = $teacher->classes();
$lang = new Lang([
    ["Curva de notas", "Grades curve"],
    ["Guardar", "Save"],
    ["Continuar", "Continue"],
    ["Trimestre", "Trimester"],
    ["Atrás", "Back"],
    ["Curso", "Class"],
    ["Trimestre 1", "Trimester 1"],
    ["Trimestre 2", "Trimester 2"],
    ["Trimestre 3", "Trimester 3"],
    ["Trimestre 4", "Trimester 4"],

]);

$course = $_GET['course'] ?? null;
$trimester = $_GET['trimester'] ?? null;
$value = $_GET['value'] ?? null;

?>
<!DOCTYPE html>
<html lang="<?=__LANG?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
$title = $lang->translation("Curva de notas");
Route::includeFile('/regiweb/includes/layouts/header.php');
?>
</head>

<body>
    <?php
Route::includeFile('/regiweb/includes/layouts/menu.php');
?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-3"><?=$lang->translation("Curva de notas")?></h1>


        <div id="container">
            <div class="mx-auto bg-white shadow-lg py-5 px-3 rounded" style="max-width: 700px;">
                <form method="GET">
                    <div class="row">
                        <div class="form-group col-6">
                            <label for="course"><?=$lang->translation('Curso')?></label>
                            <select class="form-control" name="course" id="course">
                                <?php foreach ($cursos as $row): ?>
                                    <option value="<?=$row->curso?>"><?="$row->curso - $row->desc1";?></option>
                                <?php endforeach?>
                            </select>
                        </div>
                        <div class="form-group col-6">
                            <label for="course"><?=$lang->translation('Trimestre')?></label>
                            <select class="form-control" name="trimester">
                                <option <?=$trimester === "Trimestre-1" ? "selected" : ''?> value="Trimestre-1"><?=$lang->translation('Trimestre 1')?></option>
                                <option <?=$trimester === "Trimestre-2" ? "selected" : ''?> value="Trimestre-2"><?=$lang->translation('Trimestre 2')?></option>
                                <option <?=$trimester === "Trimestre-3" ? "selected" : ''?> value="Trimestre-3"><?=$lang->translation('Trimestre 3')?></option>
                                <option <?=$trimester === "Trimestre-4" ? "selected" : ''?> value="Trimestre-4"><?=$lang->translation('Trimestre 4')?></option>
                            </select>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary"><?=$lang->translation('Continuar')?></button>
                            <a href="../index.php" class="btn btn-primary"><?=$lang->translation('Atrás')?></a>
                        </div>
                    </div>
                </form>

                <?php if ($course && $trimester):
    $values = DB::table('valores')
        ->where([['trimestre', $trimester], ['year', $teacher->year()], ['nivel', 'Notas'], ['curso', $course]])
        ->first();
    ?>
							                <form class="mt-4" method="GET">
							                    <input type="hidden" id="course" name="course" value="<?=$course?>">
							                    <input type="hidden" id="trimester" name="trimester" value="<?=$trimester?>">
							                    <div class="form-group">
							                        <label for="value"><?=$lang->translation('Examen')?></label>
							                        <select class="form-control" name="value" id="value">
							                            <?php for ($i = 1; $i <= 10; $i++): ?>
							                                <?php if (trim($values->{"val$i"}) !== ''): ?>
							                                    <option <?=$value === "$i" ? "selected" : ''?> value="<?=$i?>"><?="{$values->{"tema$i"}} - {$values->{"fec$i"}}";?></option>
							                                <?php endif?>
                                <?php endfor?>
                            </select>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary"><?=$lang->translation('Continuar')?></button>
                        </div>
                    </form>
                <?php endif?>
            </div>

            <?php if ($course && $trimester && $value):

    $notes = DB::table('padres')
        ->where([['baja', ''], ['year', $teacher->year()], ['curso', $course], ['id', Session::id()]])
        ->get();

    $gradeLettersArray = [
        'A' => 0,
        'B' => 0,
        'C' => 0,
        'D' => 0,
        'F' => 0,
        'O' => 0,
    ];
    foreach ($notes as $note) {

        $grade = intval($note->{"not$value"});

        if ($grade >= $teacher->info('vala')) {
            $gradeLettersArray['A']++;
        } elseif ($grade >= $teacher->info('valb')) {
        $gradeLettersArray['B']++;
    } elseif ($grade >= $teacher->info('valc')) {
        $gradeLettersArray['C']++;
    } elseif ($grade >= $teacher->info('vald')) {
        $gradeLettersArray['D']++;
    } elseif ($grade >= $teacher->info('valf')) {
        $gradeLettersArray['F']++;
    } else {
        $gradeLettersArray['O']++;
    }

}

?>
<input type="hidden" id="value" value="<?=$value?>">
        <table class="table mt-4">
            <thead>
                <tr class="text-center">
                    <th>A</th>
                    <th>B</th>
                    <th>C</th>
                    <th>D</th>
                    <th>F</th>
                    <th>O</th>
                    <th>Total</th>
                    <th class="w-25"><?=$lang->translation('Bono')?></th>
                </tr>
            </thead>
            <tbody>
                <tr class="text-center">
                    <td><?=$gradeLettersArray['A']?></td>
                    <td><?=$gradeLettersArray['B']?></td>
                    <td><?=$gradeLettersArray['C']?></td>
                    <td><?=$gradeLettersArray['D']?></td>
                    <td><?=$gradeLettersArray['F']?></td>
                    <td><?=$gradeLettersArray['O']?></td>
                    <td><?=count($notes)?></td>
                    <td><input id="points" type="number" class="form-control" value="<?=$values->{"p{$value}"}?>" /></td>
                </tr>
            </tbody>
        </table>
                <div class="text-center">
                    <a target="gradescurvepdf" class="btn btn-secondary" href="<?="./pdf/gradescurve.php?course={$course}&trimester={$trimester}&value={$value}"?>"><?=$lang->translation('Imprimir')?></a>
                    <button class="btn btn-primary" id="savePoints"><?=$lang->translation('Aplicar puntos')?></button>
                </div>
            <?php endif?>
        </div>
    </div>
    <?php
Route::includeFile('/includes/layouts/scripts.php', true);
Route::sweetAlert();
?>

</body>

</html>