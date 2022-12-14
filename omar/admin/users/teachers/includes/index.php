<?php

require_once '../../../../app.php';

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
    $teacher->id = $_POST['accountNumber'];
    $teacher->usuario = $_POST['username'];
    $teacher->clave = $_POST['password'];
    $teacher->docente = $_POST['type'];
    $teacher->nombre = $_POST['name'];
    $teacher->apellidos = $_POST['surnames'];
    $teacher->alias = $_POST['alias'];
    $teacher->genero = $_POST['gender'];
    $teacher->fecha_nac = $_POST['dateOfBirth'];
    $teacher->fecha_ini = $_POST['initialDate'];
    $teacher->fecha_daja = $_POST['finalDate'];
    $teacher->preparacion1 = $_POST['preparation1'];
    $teacher->preparacion2 = $_POST['preparation2'];
    $teacher->nivel = $_POST['level'];
    $teacher->grado = $_POST['home'];
    $teacher->dep = $_POST['department'];
    $teacher->posicion = $_POST['position'];
    $teacher->tel_res = $_POST['residentialPhone'];
    $teacher->tel_emer = $_POST['residentialPhone'];
    $teacher->cel = $_POST['emergencyPhone'];
    $teacher->comp = $_POST['cellPhone'];
    $teacher->comp = $_POST['cellCompanyM'];
    $teacher->email1 = $_POST['email1'];
    $teacher->email2 = $_POST['email2'];
    $teacher->re_e = $_POST['receiveEmail'];
    $teacher->baja = $_POST['active'];
    $teacher->activo = $_POST['active'] === '' ? 'Activo' : 'Inactivo';

    /* -------------------------------- Addresses ------------------------------- */
    $teacher->dir1 = $_POST['dir1'];
    $teacher->dir2 = $_POST['dir2'];
    $teacher->pueblo1 = $_POST['city1'];
    $teacher->esta1 = $_POST['state1'];
    $teacher->zip1 = $_POST['zip1'];
    
    $teacher->dir3 = $_POST['dir3'];
    $teacher->dir4 = $_POST['dir4'];
    $teacher->pueblo2 = $_POST['city2'];
    $teacher->esta2 = $_POST['state2'];
    $teacher->zip2 = $_POST['zip2'];


    /* ---------------------------- Teacher licenses ---------------------------- */
    for ($i = 1; $i <= 4; $i++) {
        $teacher->{"lic$i"} = $_POSt["licence$id"];
        $teacher->{"fex$i"} = $_POSt["expire$id"];
        $teacher->{"lp$i"} = $_POSt["expireDate$id"];
    }

    /* ---------------------------------- Club ---------------------------------- */
    for ($i = 1; $i <= 5; $i++) {
        $teacher->{"club$i"} = $_POST["clubName$i"];
        $teacher->{"pre$i"} = $_POST["clubPresident$i"];
        $teacher->{"vi$i"} = $_POST["clubVicePresident$i"];
        $teacher->{"se$i"} = $_POST["clubSecretary$i"];
    }

    /* ----------------------------- Profile picture ---------------------------- */
    $file = new File('picture');
    if ($file->amount > 0) {
        $newName = $teacher->id . '.jpg';
        $teacher->foto_name = $newName;
        $file::upload($file->files, __TEACHER_PROFILE_PICTURE_PATH, $newName);
    }


    if (isset($_POST['edit'])) {
        $teacher->save();
    } else {
        $teacher->save('new');
    }
    Session::set('accountNumber', $_POST['accountNumber']);
    Route::redirect('/users/teachers/');
}
