<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Información Basica.xls");
session_start();
$id = $_SESSION['id1'];
$usua = $_SESSION['usua1'];

require_once __DIR__ . '/../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;
use Classes\DataBase\DB;

Session::is_logged();

$school = new School(Session::id());
$year = $school->info('year2');


function CalcularEdad($fecha)
{
    list($Y, $m, $d) = explode("-", $fecha);
    return (date("md") < $m . $d ? date("Y") - $Y - 1 : date("Y") - $Y);
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Información Basica</title>
    <style type="text/css">
        .style1 {
            text-align: center;
            font-size: large;
        }

        .style2 {
            text-align: center;
        }

        .style3 {
            background-color: #CCCCCC;
        }

        .style4 {
            background-color: #FFFFCC;
            text-align: center;
        }

        .text {
            mso-number-format: "\@";
            /*force text*/
        }
    </style>
    <link href="../../jv/botones.css" rel="stylesheet" type="text/css" />
</head>

<body>

    <?php
    $count = 1;
    $students = DB::table('year')->where([
        ['year', $year]
    ])->orderBy('apellidos')->get();
    ?>
    <table id="estudiantes" align="center" cellpadding="2" border="1" cellspacing="0">
        <thead>
            <tr class="">

                <th colspan="31">Información Basica</th>
            </tr>
            <tr class="">
                <th>#</th>
                <th>NOMBRE</th>
                <th>GRADO</th>
                <th>SLE</th>
                <th>EDAD</th>
                <th>CANTAR</th>
                <th>DIBUJAR</th>
                <th>BAILAR</th>
                <th>LEER</th>
                <th>ORADOR</th>
                <th>PINTAR</th>
                <th>DECLAMAR</th>
                <th>ESCRIBIR</th>
                <th>INVESTSIGAR</th>
                <th>VOLIBOL</th>
                <th>SOCCER</th>
                <th>PELOTA</th>
                <th>BALONCESTO</th>
                <th>DOMINO</th>
                <th>AJEDREZ</th>
                <th>INSTRUMENTO(CUAL)</th>
                <th>LIDER</th>
                <th>TECNOLOGICO</th>
                <th>A/B</th>
                <th>C</th>
                <th>MENOS DE C</th>
                <th>CON QUIEN VIVE</th>
                <th>#ADULTOS</th>
                <th>#NIÑOS</th>
                <th>LLEGARIA</th>
                <th>REGRESARIA</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($students as $estu) {

            ?>
                <?php
                $info = DB::table('T_informacion_basica')->where([
                    ['ss', $estu->ss],
                    ['year', $year]
                ])->first();
                ?>
                <tr align="center">
                    <th><?= $count ?></th>
                    <td align="left"><?= $estu->nombre . ' ' . $estu->apellidos ?></td>
                    <td class="text"><?= $estu->grado ?></td>
                    <td><?= ($info["info"] ?? '') ? 'X' : '' ?></td>
                    <td><?= ($estu->fecha != '0000-00-00') ? CalcularEdad($estu->fecha) : '' ?></td>
                    <?php for ($i = 1; $i <= 15; $i++): ?>

                        <td><?= ($info["info$i"] ?? '') ? 'X' : '' ?></td>
                    <?php endfor ?>

                    <td><?= $info['info16'] ?? '' ?></td>
                    <td>
                        <?= ($info['info17'] ?? '' == 1) ? 'Si' : 'No' ?>
                    </td>
                    <td>
                        <?= ($info['info18'] ?? '' == 1) ? 'Si' : 'No' ?>
                    </td>

                    <?php for ($i = 19; $i <= 21; $i++): ?>


                        <td><?= ($info["info$i"] ?? '') ? 'X' : '' ?></td>
                    <?php endfor ?>

                    <td><?= $info['info22'] ?? '' ?></td>
                    <td><?= $info['info23'] ?? '' ?></td>
                    <td><?= $info['info24'] ?? '' ?></td>
                    <td><?= $info['info25'] ?? '' ?></td>
                    <td><?= $info['info26'] ?? '' ?></td>

                </tr>
                <?php $count++; ?>
            <?php } ?>
        </tbody>
    </table>




</body>

</html>