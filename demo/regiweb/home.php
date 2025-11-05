<?php
require_once '../app.php';

use App\Models\Admin;
use App\Models\Teacher;
use Classes\Route;
use Classes\Session;
use Illuminate\Database\Capsule\Manager;

Session::is_logged();
$teacher = Teacher::find(Session::id());
$school = Admin::primaryAdmin();
$year = $school->year;

$date = date("Y-m-d");
$messages = Manager::table('mensa_tarjeta')
    ->where(fn($query) => $query
        ->where('grupo', 'Maestros')
        ->orWhere('grupo', 'Todos'))
    ->whereDate('fecha_in', '<=', $date)
    ->whereDate('fecha_out', '>=', $date)->get();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = __("Inicio");
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 px-0">

        <div class="mx-auto text-center">
            <h1 class="display-4 mt-2"><?= __("Conectate desde cualquier parte del Mundo") ?></h1>
            <img class="img-fluid mx-auto d-block mt-5 mt-lg-4 w-20" src="/images/globe.gif" height="150" width="150" />

        </div>
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

    <?php Route::includeFile('/includes/layouts/scripts.php', true);    ?>


</body>

</html>