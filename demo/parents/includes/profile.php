<?php

require_once '../../app.php';

use Classes\Controllers\Parents;
use Classes\Route;
use Classes\Server;
use Classes\Session;

Session::is_logged();
Server::is_post();



$parents = new Parents(Session::id());
// Mother Information
$parents->madre = $_POST['nameM'];
$parents->ex_m = $_POST['exM'];
$parents->email_m = $_POST['emailM'];
$parents->tel_m = $_POST['residentialPhoneM'];
$parents->cel_m = $_POST['cellPhoneM'];
$parents->cel_com_m = $_POST['cellCompanyM'];

$parents->trabajo_m = $_POST['jobM'];
$parents->posicion_m = $_POST['jobPositionM'];
$parents->tel_t_m = $_POST['jobPhoneM'];
$parents->re_e_m = $_POST['receiveEmailM'];
$parents->re_mc_m = $_POST['receiveSmsM'];

$parents->dir1 = $_POST['dir1'];
$parents->dir3 = $_POST['dir3'];
$parents->pueblo1 = $_POST['city1'];
$parents->est1 = $_POST['state1'];
$parents->zip1 = $_POST['zip1'];

// Father Information
$parents->padre = $_POST['nameP'];
$parents->ex_p = $_POST['exP'];
$parents->email_p = $_POST['emailP'];
$parents->tel_p = $_POST['residentialPhoneP'];
$parents->cel_p = $_POST['cellPhoneP'];
$parents->cel_com_p = $_POST['cellCompanyP'];

$parents->trabajo_p = $_POST['jobP'];
$parents->posicion_p = $_POST['jobPositionP'];
$parents->tel_t_p = $_POST['jobPhoneP'];
$parents->re_e_p = $_POST['receiveEmailP'];
$parents->re_mc_p = $_POST['receiveSmsP'];

$parents->dir2 = $_POST['dir2'];
$parents->dir4 = $_POST['dir4'];
$parents->pueblo2 = $_POST['city2'];
$parents->est2 = $_POST['state2'];
$parents->zip2 = $_POST['zip2'];


// Other Information
$parents->tel_e = $_POST['emergencyPhone'];
if ($_POST['password'] !== '') {
   $parents->clave = $_POST['password'];
}



$parents->save();

Route::redirect('/profile.php');
