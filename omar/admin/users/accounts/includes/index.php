<?php

require_once '../../../../app.php';

use App\Models\Family;
use App\Models\School;
use Classes\Util;
use Classes\Route;
use Classes\Server;
use Classes\Session;

Session::is_logged();
Server::is_post();


if (isset($_POST['searchUsername'])) {

    $exists = Family::where('usuario', $_POST['searchUsername'])->exists();

    echo Util::toJson(['exist' => $exist]);
} else if (isset($_POST['edit'])) {

    $family = Family::find($_POST['accountNumber']);

    $family->update([
        'usuario' => $_POST['username'],
        'clave' => $_POST['password'],
        'nfam' => $_POST['familyAmount'],
        'madre' => $_POST['nameM'],
        'codigom' => $_POST['referenceM'],
        'ex_m' => $_POST['exM'],
        'email_m' => $_POST['emailM'],
        'tel_m' => $_POST['residentialPhoneM'],
        'cel_m' => $_POST['cellPhoneM'],
        'cel_com_m' => $_POST['cellCompanyM'],
        'dir1' => $_POST['dir1'],
        'dir3' => $_POST['dir3'],
        'pueblo1' => $_POST['city1'],
        'est1' => $_POST['state1'],
        'zip1' => $_POST['zip1'],
        'trabajo_m' => $_POST['jobM'],
        'posicion_m' => $_POST['jobPositionM'],
        'tel_t_m' => $_POST['jobPhoneM'],
        'ex_t_d_m' => $_POST['jobExtM'],
        'sueldom' => $_POST['salaryM'],
        're_e_m' => $_POST['receiveEmailM'],
        're_mc_m' => $_POST['receiveSmsM'],
        'padre' => $_POST['nameP'],
        'codigop' => $_POST['referenceP'],
        'ex_p' => $_POST['exP'],
        'email_p' => $_POST['emailP'],
        'tel_p' => $_POST['residentialPhoneP'],
        'cel_p' => $_POST['cellPhoneP'],
        'cel_com_p' => $_POST['cellCompanyP'],
        'dir2' => $_POST['dir2'],
        'dir4' => $_POST['dir4'],
        'pueblo2' => $_POST['city2'],
        'est2' => $_POST['state2'],
        'zip2' => $_POST['zip2'],
        'trabajo_p' => $_POST['jobP'],
        'posicion_p' => $_POST['jobPositionP'],
        'tel_t_p' => $_POST['jobPhoneP'],
        'ex_t_d_p' => $_POST['jobExtP'],
        'sueldop' => $_POST['salaryP'],
        're_e_p' => $_POST['receiveEmailP'],
        're_mc_p' => $_POST['receiveSmsP'],
        'qpaga' => $_POST["personToPay"],
        'encargado' => $_POST["inChargeName"],
        'parentesco' => $_POST["inChargeRelationship"],
        'email_e' => $_POST["inChargeEmail"],
        'tel_en' => $_POST["inChargePhone"],
        'tel_t_e' => $_POST["inChargeWorkPhone"],
        'cel_e' => $_POST["inChargeCellPhone"],
        'com_e' => $_POST["inChargeCellCompany"],
        'dir_e1' => $_POST["inChargeDir1"],
        'dir_e2' => $_POST["inChargeDir2"],
        'pue_e' => $_POST["inChageCity"],
        'esta_e' => $_POST["inChageState"],
        'zip_e' => $_POST["inChageZip"],
        'per1' => $_POST["person1"],
        'rel1' => $_POST["relationship1"],
        'cel1' => $_POST["celullar1"],
        'tec1' => $_POST["phone1"],
        'tet1' => $_POST["workPhone1"],
        'per2' => $_POST["person2"],
        'rel2' => $_POST["relationship2"],
        'cel2' => $_POST["celullar2"],
        'tec2' => $_POST["phone2"],
        'tet2' => $_POST["workPhone2"],
        'per3' => $_POST["person3"],
        'rel3' => $_POST["relationship3"],
        'cel3' => $_POST["celullar3"],
        'tec3' => $_POST["phone3"],
        'tet3' => $_POST["workPhone3"],
        'per4' => $_POST["person4"],
        'rel4' => $_POST["relationship4"],
        'cel4' => $_POST["celullar4"],
        'tec4' => $_POST["phone4"],
        'tet4' => $_POST["workPhone4"],
        'per5' => $_POST["person5"],
        'rel5' => $_POST["relationship5"],
        'cel5' => $_POST["celullar5"],
        'tec5' => $_POST["phone5"],
        'tet5' => $_POST["workPhone5"],
        'per6' => $_POST["person6"],
        'rel6' => $_POST["relationship6"],
        'cel6' => $_POST["celullar6"],
        'tec6' => $_POST["phone6"],
        'tet6' => $_POST["workPhone6"],
    ]);

    Session::set('edited', true);
    Session::set('accountNumber', $_POST['accountNumber']);
    Route::redirect('/users/accounts/');
} else if (isset($_POST['save'])) {
    $family = Family::create([
        'year' => School::admin()->first()->year(),
        'grupo' => 'Padres',
        'activo' => 'Activo',
        'nfam' => 0,
        'madre' => $_POST['nameM'],
        'codigom' => $_POST['referenceM'],
        'ex_m' => $_POST['exM'],
        'email_m' => $_POST['emailM'],
        'tel_m' => $_POST['residentialPhoneM'],
        'cel_m' => $_POST['cellPhoneM'],
        'cel_com_m' => $_POST['cellCompanyM'],
        'dir1' => $_POST['dir1'],
        'dir3' => $_POST['dir3'],
        'pueblo1' => $_POST['city1'],
        'est1' => $_POST['state1'],
        'zip1' => $_POST['zip1'],
        'trabajo_m' => $_POST['jobM'],
        'posicion_m' => $_POST['jobPositionM'],
        'tel_t_m' => $_POST['jobPhoneM'],
        'ex_t_d_m' => $_POST['jobExtM'],
        'sueldom' => $_POST['salaryM'],
        're_e_m' => $_POST['receiveEmailM'],
        're_mc_m' => $_POST['receiveSmsM'],
        'padre' => $_POST['nameP'],
        'codigop' => $_POST['referenceP'],
        'ex_p' => $_POST['exP'],
        'email_p' => $_POST['emailP'],
        'tel_p' => $_POST['residentialPhoneP'],
        'cel_p' => $_POST['cellPhoneP'],
        'cel_com_p' => $_POST['cellCompanyP'],
        'dir2' => $_POST['dir2'],
        'dir4' => $_POST['dir4'],
        'pueblo2' => $_POST['city2'],
        'est2' => $_POST['state2'],
        'zip2' => $_POST['zip2'],
        'trabajo_p' => $_POST['jobP'],
        'posicion_p' => $_POST['jobPositionP'],
        'tel_t_p' => $_POST['jobPhoneP'],
        'ex_t_d_p' => $_POST['jobExtP'],
        'sueldop' => $_POST['salaryP'],
        're_e_p' => $_POST['receiveEmailP'],
        're_mc_p' => $_POST['receiveSmsP'],
        'qpaga' => $_POST["personToPay"],
        'encargado' => $_POST["inChargeName"],
        'parentesco' => $_POST["inChargeRelationship"],
        'email_e' => $_POST["inChargeEmail"],
        'tel_en' => $_POST["inChargePhone"],
        'tel_t_e' => $_POST["inChargeWorkPhone"],
        'cel_e' => $_POST["inChargeCellPhone"],
        'com_e' => $_POST["inChargeCellCompany"],
        'dir_e1' => $_POST["inChargeDir1"],
        'dir_e2' => $_POST["inChargeDir2"],
        'pue_e' => $_POST["inChageCity"],
        'esta_e' => $_POST["inChageState"],
        'zip_e' => $_POST["inChageZip"],
        'per1' => $_POST["person1"],
        'rel1' => $_POST["relationship1"],
        'cel1' => $_POST["celullar1"],
        'tec1' => $_POST["phone1"],
        'tet1' => $_POST["workPhone1"],
        'per2' => $_POST["person2"],
        'rel2' => $_POST["relationship2"],
        'cel2' => $_POST["celullar2"],
        'tec2' => $_POST["phone2"],
        'tet2' => $_POST["workPhone2"],
        'per3' => $_POST["person3"],
        'rel3' => $_POST["relationship3"],
        'cel3' => $_POST["celullar3"],
        'tec3' => $_POST["phone3"],
        'tet3' => $_POST["workPhone3"],
        'per4' => $_POST["person4"],
        'rel4' => $_POST["relationship4"],
        'cel4' => $_POST["celullar4"],
        'tec4' => $_POST["phone4"],
        'tet4' => $_POST["workPhone4"],
        'per5' => $_POST["person5"],
        'rel5' => $_POST["relationship5"],
        'cel5' => $_POST["celullar5"],
        'tec5' => $_POST["phone5"],
        'tet5' => $_POST["workPhone5"],
        'per6' => $_POST["person6"],
        'rel6' => $_POST["relationship6"],
        'cel6' => $_POST["celullar6"],
        'tec6' => $_POST["phone6"],
        'tet6' => $_POST["workPhone6"],

    ]);

    $family->update([
        'usuario' => $family->id,
        'clave' => $family->id,
    ]);

    Session::set('added', true);
    Session::set('accountNumber', $family->id);
    Route::redirect('/users/accounts/');
}
