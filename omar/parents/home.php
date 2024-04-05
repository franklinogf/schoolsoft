<?php
require_once '../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Parents;


$school = new School();
$parents = new Parents(Session::id());
$year = $school->info('year');


Session::is_logged();
$lang = new Lang([
    ['Conectate desde cualquier parte del Mundo.', "Connect from anywhere in the world."],
    ['Mensaje(s) para los padres', "Parent's messages"],
    ['Titulo del Mensaje: ', 'Message title: '],
    ['Encuesta para los padres', "Parent's survey"],
    ['Titulo de la encuesta: ', 'Survey title: '],
    ['Comentario:', 'Comment:'],
])
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = "Inicio";
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3  px-0">
        <center>
            <h1 class="display-4 mt-2"><?= $lang->translation("Conectate desde cualquier parte del Mundo.") ?></h1>
            <img class="img-fluid mx-auto d-block mt-5 mt-lg-4 w-20" src="/images/globe.gif" height="150" width="150" />
        </center>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    // Route::js('/react-components/Clock.js', true);

    if (isset($_POST['Grabar'])) {
        for ($a = 1; $a <= $_POST[num_rec]; $a++) {
            $codi = 'est(' . $a . ',1)';
            $dijo = 'est(' . $a . ',2)';
            $gra = 'est(' . $a . ',3)';
            $nom = 'est(' . $a . ',5)';
            $ape = 'est(' . $a . ',4)';
            $com = 'est(' . $a . ',6)';
            $date = date("Y-m-d");
            if ($_POST[$dijo] == '') {
            } else {
                echo $q = "INSERT INTO respuestas set comentario='" . $_POST[$com] . "',id2='$parents->id', codigo='$_POST[$codi]', dijo='$_POST[$dijo]', fecha='$date', grado='$_POST[$gra]', year='$year', nombre='$_POST[$nom]', apellidos='$_POST[$ape]'";

                DB::table('respuestas')->insert([
                    'year' => $year,
                    'id2' => $parents->id,
                    'codigo' => $_POST[$codi],
                    'dijo' => $_POST[$dijo],
                    'fecha' => $date,
                    'apellidos' => $_POST[$ape],
                    'nombre' => $_POST[$nom],
                    'comentario' => $_POST[$com],
                ]);
            }
        }
    }


    $date = date("Y-m-d");
    $mensages = DB::table('mensa_tarjeta')->where([
        ['fecha_in', '<=', $date],
        ['fecha_out', '>=', $date],
        ['grupo', '!=', 'Maestros'],
    ])->get();

    $can = count($mensages);

    if ($can > 0) {
    ?>
        <div class="container-lg mt-lg-3  px-0">
            <center>
                <h1 class="display-12 mt-2">
                    <font size="6"><b><?= $lang->translation("Mensaje(s) para los padres") ?></b></font>
                </h1>

                <table border="0" width="64%" cellspacing="0" cellpadding="3">
                    <?
                    foreach ($mensages as $mensage) {
                    ?>
                        <tr>
                            <td bgcolor="#C0C0C0">
                                <p align="center"><b>
                                        <font size="4"><?= $lang->translation("Titulo del Mensaje: ") . '</b>' . $mensage->titulo ?></font>
                            </td>
                        </tr>
                    <?
                        echo '<tr>';
                        echo '<td>';
                        echo $mensage->text;
                        echo '</td>';
                        echo '</tr>';
                        echo '<tr>';
                        echo '<td bgcolor="#C0C0C0">';
                        echo '</td>';
                        echo '</tr>';
                    }
                    ?>
                </table>
            </center>
        </div>
    <?
    }
    //********************************************


    $date = date("Y-m-d");

    $mensages = DB::table('estadisticas')->where([
        ['fecha_in', '<=', $date],
        ['fecha_out', '>=', $date],
        ['grupo', '!=', 'Maestros'],
    ])->get();

    $can = count($mensages);

    if ($can > 0) {
    ?>
        <div class="container-lg mt-lg-3  px-0">
            <center>
                <h1 class="display-12 mt-2">
                    <font size="6"><b><?= $lang->translation("Encuesta para los padres") ?></b></font>
                </h1>
            </center>
        </div>

        <form action="home.php" method="post">

            <div align="center">
                <table border="0" width="64%" cellspacing="0" cellpadding="3">
                    <?
                    $a = 0;
                    foreach ($mensages as $mensage) {
                    ?>
                        <tr>
                            <td bgcolor="#C0C0C0">
                                <p align="center"><b>
                                        <font size="4"><?= $lang->translation("Titulo de la Encuesta: ") . '</b>' . $mensage->titulo ?></font>
                            </td>
                        </tr>
                    <?
                        echo '<tr>';
                        echo '<td>';
                        echo $mensage->text;
                        echo '</td>';
                        echo '</tr>';
                        echo '<tr>';
                        echo '<td bgcolor="">';
                        $a = $a + 1;

                        $respuesta = DB::table('respuestas')->where([
                            ['id2', $parents->id],
                            ['codigo', $mensage->codigo],
                            ['year',  $year],
                        ])->first();
                        $estu = DB::table('year')->where([
                            ['id', $parents->id],
                            ['year', $year],
                        ])->first();

                        echo "<input type=hidden name='est($a,1)' value='$mensage->codigo'>";
                        echo "<input type=hidden name='est($a,3)' value='$estu->grado'>";
                        echo "<input type=hidden name='est($a,4)' value='$estu->apellidos'>";
                        echo "<input type=hidden name='est($a,5)' value='$estu->nombre'>";
                        $di = '';
                        if ($respuesta->dijo != '') {
                            echo 'Su Contestación fue: ' . $respuesta->dijo;
                            $di = 'disabled="disabled"';
                        } else {
                            echo 'Su Contestación es: ';
                            echo "<select size='1' name='est($a,2)'>";
                            echo '<option></option>';
                            echo '<option>SI</option>';
                            echo '<option>NO</option>';
                            echo '<option>INDECISO</option>';
                            echo '</select>';
                        }
                        echo '</td>';
                        echo '</tr>';
                        if ($mensage->vicible == 'SI') {
                            $respuesta1 = DB::table('respuestas')->where([
                                ['dijo', 'SI'],
                                ['codigo', $mensage->codigo],
                                ['year',  $year],
                            ])->get();
                            $can1 = count($respuesta1);

                            $respuesta1 = DB::table('respuestas')->where([
                                ['dijo', 'NO'],
                                ['codigo', $mensage->codigo],
                                ['year',  $year],
                            ])->get();
                            $can2 = count($respuesta1);

                            $respuesta1 = DB::table('respuestas')->where([
                                ['dijo', 'INDECISO'],
                                ['codigo', $mensage->codigo],
                                ['year',  $year],
                            ])->get();
                            $can3 = count($respuesta1);

                            echo '<tr>';
                            echo '<td><CENTER>';
                            echo '>>>>&nbsp; TOTAL SI: ' . $can1;
                            echo '&nbsp;&nbsp; / &nbsp;&nbsp;TOTAL NO: ' . $can2;
                            echo '&nbsp;&nbsp; / &nbsp;&nbsp;TOTAL INDECISO: ' . $can3 . '&nbsp; <<<<';
                            echo '</CENTER></td>';
                            echo '</tr>';
                        }

                        if ($mensage->comentario == 'SI') {
                            echo '<tr>';
                            echo '<td>';
                            echo '<p align="center"><font size="4"><b>' . $lang->translation("Comentario:") . '</b></font>';
                            echo '</td>';
                            echo '</tr>';

                            echo '<tr>';
                            echo '<td>';
                            echo "<textarea name='est($a,6)' $di rows='4' style='width: 803px'>" . $respuesta->comentario . "</textarea>";
                            echo '</td>';
                            echo '</tr>';
                        }

                        echo '<td bgcolor="#C0C0C0">';
                        echo '</td>';
                        echo '</tr>';
                    }
                    echo "<input type=hidden name=num_rec value=$a>";

                    ?>
                </table>
                <br>
                <?
                if ($respuesta->dijo != '') {
                    exit;
                }

                ?>
                <input type=submit name='Grabar' class="myButton" value="Contestar" style="font-size: 12pt; font-weight: bold; width: 199px; height: 30px;"></p>
            </div>
        </form>


    <?   } ?>
    <br>
    <br>
    <br>



</body>

</html>