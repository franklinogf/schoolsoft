<?php
require_once __DIR__ . '/../../../app.php';

use Classes\DataBase\DB;
use Classes\Lang;
use Classes\Route;
use Classes\Server;
use Classes\Session;

Server::is_post();

$lang = new Lang([
    ['Guardado', 'Saved'],
]);

$updates = [
    'hdt' => $_POST['tri2'],
    'hdp' => $_POST['hoja'],
    'vnf' => $_POST['vnf'],
    'fra' => $_POST['tri1'],
    'nel' => $_POST['nel'],
    'suantri' => $_POST['suantri'],
    'cppd' => $_POST['cppd'],
    'npn' => $_POST['npn'],
    'etd' => $_POST['etd'],
    'nmf' => $_POST['nmf'],
    'enf' => $_POST['enf'],
    'vala' => $_POST['vala'],
    'valb' => $_POST['valb'],
    'valc' => $_POST['valc'],
    'vald' => $_POST['vald'],
    'por3' => $_POST['por3'],
    'cv' => $_POST['cv'],
    'teg' => $_POST['teg'],
    'nin' => $_POST['nin'],
    'tar' => $_POST['tarj'],
    'vs1' => $_POST['vs1'],
    'vs2' => $_POST['vs2'],
    'vf' => $_POST['vf'],
    'tr1' => $_POST['tr1'],
    'tr2' => $_POST['tr2'],
    'tr3' => $_POST['tr3'],
    'tr4' => $_POST['tr4'],
    'vt1' => $_POST['vt1'],
    'vt2' => $_POST['vt2'],
    'vt3' => $_POST['vt3'],
    'vt4' => $_POST['vt4'],
    'tpa' => $_POST['tpa'],
    'cm' => $_POST['cm'],
    'ns1' => $_POST['ns1'],
    'ns2' => $_POST['ns2'],
    'nf' => $_POST['nf'],
    'se1' => $_POST['se1'],
    'se2' => $_POST['se2'],
    'fin' => $_POST['fin'],
    'valf' => $_POST['valf'],
    'sutri' => $_POST['sutri'],
    'sie' => $_POST['sie'],
    'sieab' => $_POST['sieab'],
    'por1' => $_POST['por1'],
    'por2' => $_POST['por2'],
    'np' => $_POST['np'],
    'tarjeta' => $_POST['tarjeta'],
    'logo' => $_POST['logo'],
    'tri' => $_POST['tri'],
    'fec_t' => $_POST['fec_t'],
];

DB::table('colegio')->where('usuario', 'administrador')->update($updates);
Session::set('saved', $lang->translation("Guardado"));

Route::redirect('/access/notesOptions.php');
