<?php

use App\Models\Admin;
use Classes\DataBase\DB;
use Classes\Route;
use Classes\Session;

include '../../../app.php';
Session::is_logged();

$admin = Admin::find(Session::id());

if ($_SERVER["REQUEST_METHOD"] == 'POST') {

    $constants = $environments = [];
    foreach ($_POST['environments'] as $key => $values) {
        if ($values['value'] === '') {
            continue;
        }

        $environments[$key]['value'] = $values['value'];
        $environments[$key]['other'] = $values['other'];
    }
    foreach ($_POST['constants'] as $key => $value) {
        if ($value === '') {
            continue;
        }

        $constants[$key] = $value;
    }

    if (isset($_POST['save'])) {
        $updateData = [
            'idioma' => $_POST['idioma'],
            'colegio' => $_POST['colegio'],
            'director' => $_POST['director'],
            'telefono' => $_POST['telefono'],
            'fax' => $_POST['fax'],
            'principal' => $_POST['principal'],
            'correo' => $_POST['correo'],
            'pagina' => $_POST['pagina'],
            'dir1' => $_POST['dir1'],
            'dir2' => $_POST['dir2'],
            'pueblo1' => $_POST['pueblo1'],
            'esta1' => $_POST['esta1'],
            'zip1' => $_POST['zip1'],
            'dir3' => $_POST['dir3'],
            'dir4' => $_POST['dir4'],
            'pueblo2' => $_POST['pueblo2'],
            'esta2' => $_POST['esta2'],
            'zip2' => $_POST['zip2'],
            'email3' => $_POST['email3'],
            'email5' => $_POST['email5'],
            'email4' => $_POST['email4'],
            'environments' => count($environments) > 0 ? json_encode($environments) : null,
            'constants' => count($constants) > 0 ? json_encode($constants) : null,
            // 'pag_ini2' => $_POST['pag_ini2'],
            // 'colegio2' => $_POST['colegio2'],
            // 'dir5' => $_POST['dir5'],
            // 'dir6' => $_POST['dir6'],
            // 'pueblo3' => $_POST['pueblo3'],
            // 'zip3' => $_POST['zip3'],
            // 'est3' => $_POST['est3'],
            // 'tel3' => $_POST['tel3'],
            // 'tel4' => $_POST['tel4'],
            // 'fax2' => $_POST['fax2'],

        ];

        $admin->update($updateData);

        // var_dump($result);
    } else if (isset($_POST['savePassword'])) {
        if (empty($_POST['clave'])) {
            Session::set('passwordError', true);
            Route::redirect('/information');
        }

        $admin->update([
            'clave' => $_POST['clave'],
        ]);

        Session::set('passwordSaved', true);
    }
    Route::redirect('/information');
} else {
    Route::error();
}
