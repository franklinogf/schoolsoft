<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Admin;
use App\Models\Family;
use Classes\Server;
use Classes\Util;

Server::is_post();

if (isset($_POST['save'])) {
    $updates = [
        'men_inac' => htmlspecialchars($_POST['message']),
        'inactivo' => $_POST['lock'],
        'dia_vence' => $_POST['expireDay'],
        'bloqueoauto' => $_POST['automaticLock'],
        'des1' => $_POST['code1'],
        'des2' => $_POST['code2'],
        'des3' => $_POST['code3'],
    ];

    Admin::primaryAdmin()->update($updates);
} else if (isset($_POST['search'])) {

    $school = Admin::primaryAdmin();
    echo Util::toJson([
        'message' => $school->men_inac,
        'lock' => $school->inactivo,
        'expireDay' => $school->dia_vence,
        'automaticLock' => $school->bloqueoauto,
        'code1' => $school->des1,
        'code2' => $school->des2,
        'code3' => $school->des3,
    ]);
} else if (isset($_POST['check'])) {

    Family::query()->find($_POST['check'])->update(['activo' => $_POST['value']]);
}
