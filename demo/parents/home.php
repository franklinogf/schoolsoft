<?php
require_once '../app.php';

use App\Models\Admin;
use App\Models\Family;
use Classes\Route;
use Classes\Session;
use Illuminate\Database\Capsule\Manager;

Session::is_logged();

$family = Family::find(Session::id());
$school = Admin::primaryAdmin();
$year = $school->year;

$date = date("Y-m-d");
$messages = Manager::table('mensa_tarjeta')
    ->where(fn($query) => $query
        ->where('grupo', 'Padres')
        ->orWhere('grupo', 'Todos'))
    ->whereDate('fecha_in', '<=', $date)
    ->whereDate('fecha_out', '>=', $date)
    ->get();

$studentGrades = $family->kids()->pluck('grado');

$surveys = Manager::table('estadisticas')
    ->where(
        fn($query) => $query
            ->where('grupo', 'Padres')
            ->orWhere('grupo', 'Todos')
            ->orWhereIn('grupo', $studentGrades)
    )
    ->whereDate('fecha_in', '<=', $date)
    ->whereDate('fecha_out', '>=', $date)->get();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = __("Inicio");
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mx-auto  px-0">

        <h1 class="display-4 mt-2 text-center"><?= __("Conectate desde cualquier parte del Mundo.") ?></h1>
        <img class="img-fluid mx-auto d-block mt-5 mt-lg-4 w-20" src="/images/globe.gif" height="150" width="150" />

    </div>
    <?php if (count($messages) > 0): ?>
        <div class="container mt-3">
            <h1 class="display-12 mt-2"><?= __("Mensaje(s)") ?></h1>

            <?php foreach ($messages as $message): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h3 class="card-title text-center">
                            <?= $message->titulo ?></h3>
                        <p class="card-text"><?= $message->text ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if (count($surveys) > 0): ?>
        <div class="container mt-3 ">
            <h1 class="display-12 mt-2"><?= __("Encuesta(s)") ?></h1>

            <?php foreach ($surveys as $survey):

                $answer = Manager::table('respuestas')
                    ->where('id2', $family->id)
                    ->where('codigo', $survey->codigo)
                    ->where('year', $year)
                    ->first();
            ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h3 class="card-title text-center">
                            <?= $survey->titulo ?></h3>
                        <p class="card-text"><?= $survey->text ?></p>
                        <?php if (!$answer): ?>
                            <form action="./includes/answerSurvey.php" method="post">
                                <div class="form-group col-4">
                                    <label for="answer">Respuesta:</label>
                                    <select class="form-control" name="answer" id="answer">
                                        <option value="" selected disabled><?= __("Seleccione una respuesta") ?></option>
                                        <option value="SI"><?= __("SI") ?></option>
                                        <option value="NO"><?= __("NO") ?></option>
                                        <option value="INDECISO"><?= __("INDECISO") ?></option>
                                    </select>
                                </div>

                                <?php if ($survey->comentario == 'SI'): ?>
                                    <div class="form-group col-4">
                                        <label for="comment"><?= __("Comentario (opcional)") ?></label>
                                        <textarea class="form-control" name="comment" id="comment" rows="3"></textarea>
                                    </div>
                                <?php endif; ?>

                                <input type="hidden" name="survey_id" value="<?= $survey->codigo ?>">
                                <button type="submit" class="btn btn-primary"><?= __("Responder") ?></button>
                            </form>
                        <?php else: ?>
                            <p class="text-muted"> Su respuesta fue: <?= $answer->dijo ?></p>
                            <?php if ($answer->comentario): ?>
                                <p class="text-muted"><?= __("Comentario:") ?></p>
                                <small class="form-text text-muted"><?= $answer->comentario ?></small>
                            <?php endif; ?>
                        <?php endif; ?>

                        <hr>

                        <?php if ($survey->vicible == 'SI'): ?>
                            <div>
                                <h5>Resultado de la encuesta</h5>
                                <table class="table table-striped table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th><?= __("Respuesta") ?></th>
                                            <th><?= __("Cantidad") ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $siCount = Manager::table('respuestas')
                                            ->where('codigo', $survey->codigo)
                                            ->where('year', $year)
                                            ->where('dijo', 'SI')
                                            ->count();


                                        $noCount = Manager::table('respuestas')
                                            ->where('codigo', $survey->codigo)
                                            ->where('year', $year)
                                            ->where('dijo', 'NO')
                                            ->count();

                                        $indecisoCount = Manager::table('respuestas')
                                            ->where('codigo', $survey->codigo)
                                            ->where('year', $year)
                                            ->where('dijo', 'INDECISO')
                                            ->count();
                                        ?>
                                        <tr>
                                            <td><?= __("SI") ?></td>
                                            <td><?= $siCount ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= __("NO") ?></td>
                                            <td><?= $noCount ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= __("INDECISO") ?></td>
                                            <td><?= $indecisoCount ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>


</body>

</html>