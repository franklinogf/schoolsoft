<?php
require_once '../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;
use Classes\DataBase0\DB;

Session::is_logged();

$tarjeta =  $_COOKIE["variable2"];
$tnot =  $_COOKIE["variable3"];
$grado =  $_COOKIE["variable4"];
$idioma =  $_COOKIE["variable5"];
$opcion =  $_COOKIE["variable6"];
$grados =  $_COOKIE["variable7"];
$Year =  $_COOKIE["variable8"];
$estu =  $_COOKIE["variable9"];
$cep =  $_COOKIE["variable10"];
$fg =  $_COOKIE["variable11"];
$fdg =  $_COOKIE["variable12"];
$memsa1 =  $_COOKIE["variable13"];
$memsa2 =  $_COOKIE["variable14"];
$nhc     =  $_COOKIE["variable15"];

//echo $grado.' / '.$grados;

if ($opcion=='2' and $fg=='true')
   {
//   echo $fg.' 7777';
        $thisCourse2 = DB::table("year")->where([
            ['grado', $grados],
            ['year', $Year]
        ])->update([
            'fechagra' => $fdg,
        ]);

   }


$tar= 'Tarjeta'.$tarjeta.'.php';

if ($grado == 'C' and $tarjeta == '31') {
    $tar = 'Tarjeta' . $tarjeta . 'c.php';
}
if ($grado == 'B' and $tarjeta == '31') {
    $tar = 'Tarjeta' . $tarjeta . 'b.php';
}
if ($grado == 'A' and $tarjeta == '31') {
    $tar = 'Tarjeta' . $tarjeta . 'a.php';
}

require_once $tar;

exit;
