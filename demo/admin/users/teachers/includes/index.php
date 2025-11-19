<?php

require_once __DIR__ . '/../../../../app.php';

use Classes\File;
use Classes\Util;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Parents;
use Classes\Controllers\Teacher;

Session::is_logged();
Server::is_post();


if (isset($_POST['searchUsername'])) {
    $ss = DB::table('profesor')->select('usuario')->where('usuario', $_POST['searchUsername'])->first();
    $exist = 0;
    if ($ss) {
        $exist = true;
    } else {
        $exist = false;
    }
    echo Util::toJson(['exist' => $exist]);
} else if (isset($_POST['edit'])) {
    $teacher = new Teacher($_POST['accountNumber']);
    Session::set('edited', true);
} else if (isset($_POST['save'])) {
    $teacher = new Teacher();
    $teacher->year = $teacher->info('year');
    Session::set('added', true);
}

if (isset($_POST['save']) || isset($_POST['edit'])) {
    /* --------------------------- Information -------------------------- */

    $data = [
        "id" => $_POST['accountNumber'],
        "usuario" => $_POST['username'],
        "clave" => $_POST['password'],
        "docente" => $_POST['type'],
        "nombre" => $_POST['name'],
        "apellidos" => $_POST['surnames'],
        "alias" => $_POST['alias'],
        "genero" => $_POST['gender'],
        "fecha_nac" => $_POST['dateOfBirth'],
        "fecha_ini" => $_POST['initialDate'],
        "fecha_daja" => $_POST['finalDate'],
        "preparacion1" => $_POST['preparation1'],
        "preparacion2" => $_POST['preparation2'],
        "nivel" => $_POST['level'] ?? '',
        "grado" => $_POST['home'] ?? '',
        "dep" => $_POST['department'],
        "posicion" => $_POST['position'],
        "tel_res" => $_POST['residentialPhone'],
        "tel_emer" => $_POST['emergencyPhone'],
        "cel" => $_POST['cellPhone'],
        "comp" => $_POST['cellCompanyM'],
        "email1" => $_POST['email1'],
        "email2" => $_POST['email2'],
        "re_e" => $_POST['receiveEmail'],
        "baja" => $_POST['active'],
        "activo" => $_POST['active'] === '' ? 'Activo' : 'Inactivo',
        "dir1" => $_POST['dir1'],
        "dir2" => $_POST['dir2'],
        "pueblo1" => $_POST['city1'],
        "esta1" => $_POST['state1'],
        "zip1" => $_POST['zip1'],
        "dir3" => $_POST['dir3'],
        "dir4" => $_POST['dir4'],
        "pueblo2" => $_POST['city2'],
        "esta2" => $_POST['state2'],
        "zip2" => $_POST['zip2'],
    ];


    /* ---------------------------- Teacher licenses ---------------------------- */
    for ($i = 1; $i <= 4; $i++) {
        $data["lic$i"] = $_POST["licence$i"] ?? '';
        $data["lp$i"] = $_POST["expire$i"] ?? '';
        $data["fex$i"] = $_POST["expireDate$i"] ?? '';
    }

    /* ---------------------------------- Club ---------------------------------- */
    for ($i = 1; $i <= 5; $i++) {
        $data["club$i"] = $_POST["clubName$i"] ?? '';
        $data["pre$i"] = $_POST["clubPresident$i"] ?? '';
        $data["vi$i"] = $_POST["clubVicePresident$i"] ?? '';
        $data["se$i"] = $_POST["clubSecretary$i"] ?? '';
    }

    /* ----------------------------- Profile picture ---------------------------- */
    $file = new File('picture');
    if ($file->amount > 0) {
        $newName = $data['id'] . '.jpg';
        $data['foto_name'] = $newName;
        $file::upload($file->files, __TEACHER_PROFILE_PICTURE_PATH, $newName);
    }
    if (isset($_POST['save'])) {
        DB::table('profesor')->insert($data);
    } else {
        DB::table('profesor')->where('id', $_POST['accountNumber'])->update($data);
    }

    Session::set('accountNumber', $_POST['accountNumber']);
    Route::redirect('/users/teachers/');
}
