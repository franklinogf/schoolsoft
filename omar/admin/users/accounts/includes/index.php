<?php

require_once '../../../../app.php';

use Classes\Util;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Parents;

Session::is_logged();
Server::is_post();


if (isset($_POST['searchUsername'])) {
    $ss = DB::table('madre')->select('usuario')->where('usuario', $_POST['searchUsername'])->first();
    $exist = 0;
    if ($ss) {
        $exist = true;
    } else {
        $exist = false;
    }
    echo Util::toJson(['exist' => $exist]);
} else if (isset($_POST['edit'])) {
    $parents = new Parents($_POST['accountNumber']);
    Session::set('edited', true);
} else if (isset($_POST['save'])) {
    $parents = new Parents();
    $parents->year = $parents->info('year');
    $parents->grupo = 'Padres';
    $parents->activo = 'Activo';
    Session::set('added', true);
}

if (isset($_POST['save']) || isset($_POST['edit'])) {
    /* --------------------------- Parents information -------------------------- */
    $parents->id = $_POST['accountNumber'];
    $parents->usuario = $_POST['username'];
    $parents->clave = $_POST['password'];
    $parents->nfam = $_POST['familyAmount'];

    // Mother Information
    $parents->madre = $_POST['nameM'];
    $parents->codigom = $_POST['referenceM'];
    $parents->ex_m = $_POST['exM'];
    $parents->email_m = $_POST['emailM'];
    $parents->tel_m = $_POST['residentialPhoneM'];
    $parents->cel_m = $_POST['cellPhoneM'];
    $parents->cel_com_m = $_POST['cellCompanyM'];

    $parents->dir1 = $_POST['dir1'];
    $parents->dir3 = $_POST['dir3'];
    $parents->pueblo1 = $_POST['city1'];
    $parents->est1 = $_POST['state1'];
    $parents->zip1 = $_POST['zip1'];

    $parents->trabajo_m = $_POST['jobM'];
    $parents->posicion_m = $_POST['jobPositionM'];
    $parents->tel_t_m = $_POST['jobPhoneM'];
    $parents->ex_t_d_m = $_POST['jobExtM'];
    $parents->sueldom = $_POST['salaryM'];


    $parents->re_e_m = $_POST['receiveEmailM'];
    $parents->re_mc_m = $_POST['receiveSmsM'];

    // Father Information
    $parents->padre = $_POST['nameP'];
    $parents->codigop = $_POST['referenceP'];
    $parents->ex_p = $_POST['exP'];
    $parents->email_p = $_POST['emailP'];
    $parents->tel_p = $_POST['residentialPhoneP'];
    $parents->cel_p = $_POST['cellPhoneP'];
    $parents->cel_com_p = $_POST['cellCompanyP'];

    $parents->dir2 = $_POST['dir2'];
    $parents->dir4 = $_POST['dir4'];
    $parents->pueblo2 = $_POST['city2'];
    $parents->est2 = $_POST['state2'];
    $parents->zip2 = $_POST['zip2'];

    $parents->trabajo_p = $_POST['jobP'];
    $parents->posicion_p = $_POST['jobPositionP'];
    $parents->tel_t_p = $_POST['jobPhoneP'];
    $parents->ex_t_d_p = $_POST['jobExtP'];
    $parents->sueldop = $_POST['salaryP'];

    $parents->re_e_p = $_POST['receiveEmailP'];
    $parents->re_mc_p = $_POST['receiveSmsP'];


    /* ---------------------- Authorized people to pick up and emergency information ---------------------- */
    for ($i = 1; $i <= 6; $i++) {
        $parents->{"per$i"} = $_POST["person$i"];
        $parents->{"rel$i"} = $_POST["relationship$i"];
        $parents->{"cel$i"} = $_POST["celullar$i"];
        $parents->{"tec$i"} = $_POST["phone$i"];
        $parents->{"tet$i"} = $_POST["workPhone$i"];
    }

    /* ------------------------ Person responsible to pay ----------------------- */

    $parents->qpaga = $_POST["personToPay"];
    $parents->encargado = $_POST["inChargeName"];
    $parents->parentesco = $_POST["inChargeRelationship"];
    $parents->email_e = $_POST["inChargeEmail"];
    $parents->tel_en = $_POST["inChargePhone"];
    $parents->tel_t_e = $_POST["inChargeWorkPhone"];
    $parents->cel_e = $_POST["inChargeCellPhone"];
    $parents->cel_com_m = $_POST["inChargeCellCompany"];
    $parents->dir_e1 = $_POST["inChargeDir1"];
    $parents->dir_e2 = $_POST["inChargeDir2"];
    $parents->pue_e = $_POST["inChageCity"];
    $parents->esta_e = $_POST["inChageState"];
    $parents->zip_e = $_POST["inChageZip"];

    // echo "save or edit";
    // echo '<pre>';print_r($parents);echo '</pre>';
    if (isset($_POST['edit'])) {
        $parents->save();
    } else {
        $parents->save('new');
    }
    Session::set('accountNumber', $_POST['accountNumber']);
    Route::redirect('/users/accounts/');
}
