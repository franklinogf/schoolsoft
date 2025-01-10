<?php
require_once '../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Email;
use Classes\Lang;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ['es', 'in'],
    ['', ''],
]);

$reg = DB::table('colegio')->whereRaw("usuario = 'administrador'")->first();
$colegio = $reg->colegio;

$school = new School(Session::id());
$year = $school->info('year2');

$email = new Email();

$title = $lang->translation('Primer aviso de cobro');
$subject = $lang->translation('Primer aviso de cobro');

$schoolName = $colegio;

ob_start();
include 'pagos_aut_emailTemplate.php';
$body = ob_get_contents();
ob_end_clean();

$email->send(
    to: ['franklinomarflores@gmail.com'],
    subject: 'Recibo',
    message: $body
);
