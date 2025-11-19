<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informacion personal.xls");

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

$students = DB::table('year')->where([
    ['year', $year]
])->orderBy('apellidos')->get();

?>

<!DOCTYPE html>
<html lang="es">
<meta charset="UTF-8">

<head>
    <title>Informacion</title>
    <style type="text/css">
        .text {
            mso-number-format: "\@";
            /*force text*/
        }
    </style>
</head>

<body>
    <table border="1" cellpadding="2">
        <thead align="center">
            <tr>
                <th colspan="18">Información Personal</th>
            </tr>
            <tr>
                <th colspan="12">Datos Generales</th>
                <th colspan="2">Escuela procedencia</th>
                <th colspan="2">Padre</th>
                <th colspan="2">Madre</th>
            </tr>
            <tr>
                <th>#</th>
                <th>Apellidos</th>
                <th>Nombre</th>
                <th>Genero</th>
                <th>Grado</th>
                <th>ED. Especial</th>
                <th>D.O.B.</th>
                <th><?= utf8_encode('Teléfono #1') ?></th>
                <th><?= utf8_encode('Teléfono #2') ?></th>
                <th>Email</th>
                <th><?= utf8_encode('Dirección postal') ?></th>
                <th><?= utf8_encode('Dirección Residencial') ?></th>
                <th>Nombre Escuela</th>
                <th>Municipio</th>
                <th><?= utf8_encode('Ocupación') ?></th>
                <th><?= utf8_encode('Preparación') ?></th>
                <th><?= utf8_encode('Ocupación') ?></th>
                <th><?= utf8_encode('Preparación') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            foreach ($students as $estu) {
                $count = $count + 1;
                $madre = DB::table('madre')->where([
                    ['id', $estu->id]
                ])->first();
            ?>
                <tr>
                    <td align="center"><?= $count ?></td>
                    <td><?= $estu->apellidos ?></td>
                    <td><?= $estu->nombre ?></td>
                    <td align="center"><?= ($estu->genero == '2') ? 'M' : 'F' ?></td>
                    <td align="center" class="text"><?= $estu->grado ?></td>
                    <td></td>
                    <td align="center"><?= $estu->fecha ?></td>
                    <td><?= $madre->tel_m ?></td>
                    <td><?= $madre->tel_p ?></td>
                    <td><?= $madre->email_m ?></td>
                    <?php
                    $d1 = $madre->post1 ?? '';
                    $d2 = $madre->post2 ?? '';
                    $d3 = $madre->pueblo2 ?? '';
                    $d4 = $madre->estado2 ?? '';
                    $d5 = $madre->zip2 ?? '';
                    ?>
                    <td><?= "$d1 $d2 $d3 $d4 $d5" ?></td>
                    <?php
                    $d1 = $madre->dir1 ?? '';
                    $d2 = $madre->dir2 ?? '';
                    $d3 = $madre->pueblo ?? '';
                    $d4 = $madre->estado ?? '';
                    $d5 = $madre->zip ?? '';
                    ?>
                    <td><?= $d1 . ' ' . $d2 . ' ' . $d3 . ' ' . $d4 . ' ' . $d5 ?></td>
                    <td><?= $estu->colpro ?></td>
                    <td><?= $estu->municipio ?></td>
                    <td><?= $madre->trabajo_p ?></td>
                    <td><?= $madre->posicion_p ?></td>
                    <td><?= $madre->trabajo_m ?></td>
                    <td><?= $madre->posicion_m ?></td>

                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</body>

</html>