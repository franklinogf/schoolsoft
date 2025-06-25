<?php
require_once '../../app.php';

use App\Models\Admin;
use Classes\Controllers\School;
use Classes\Email;
use Classes\Session;

Session::is_logged();

$reg = Admin::primaryAdmin()->first();
$colegio = $reg->colegio;

$school = new School(Session::id());

$title = __('Primer aviso de cobro');
$subject = __('Primer aviso de cobro');

$schoolName = $colegio;

ob_start();
include 'pagos_aut_emailTemplate.php';
$body = ob_get_contents();
ob_end_clean();


Email::to('franklinomarflores@gmail.com')
    ->subject('Recibo')
    ->body($body)
    ->send();
