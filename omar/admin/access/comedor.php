<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase0\DB;
use Classes\Controllers\School;

Session::is_logged();
$lang = new Lang([
    ['Exportación de data a Excel', 'Data export to Excel'],
    ['Selección de Base de datos', 'Database Selection'],
    ['Seleccióna el año', 'Select the year'],
    ['Selección', 'Selection'],
    ['Padres', 'Parents'],
    ['Estudiantes', 'Students'],
    ['Año para transferir datos', 'Year to transfer data'],
    ['Notas', 'Grades'],
    ['Pagos', 'Payments'],
    ['Transferir', 'Transfer'],
    ['Documentos de estudiantes', 'Student documents'],
    ['Padres/Estudiantes', 'Parents/Students'],
    ['Comedor escolar', 'School cafeteria'],
    ['Verano', 'Summer'],
    ['Si', 'Yes'],
    ['Lista', 'List'],
    ['Guardar', 'Save'],
    ['Crear', 'Create'],
    ['Buscar', 'Search'],
    ['Limpiar', 'Clear'],
    ['Eliminar', 'Delete'],
    ['Estás seguro que quieres borrar el curso?', 'Are you sure you want to delete the course?'],
]);
$school = new School(Session::id());

$tabla =  $_COOKIE["variable9"];
$year  =  $_COOKIE["variable10"];
session_start();

$students = DB::table('year')->where([
     ['year', $year],
     ['codigobaja', 0]
   ])->orderBy('apellidos')->get();

function haveDate($date)
{
    return $date === '0000-00-00' ? '' : $date;
}
header("Content-type: application/vnd.ms-excel") ; 
header("Content-Disposition: attachment; filename=Comedor escolar $cole->year.xls"); 
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comedor escolar</title>
</head>

<body>
    <table border="1">
        <thead>
            <tr>
                <th>NUMERO ESTUDIANTE</th>
                <th>SEG SOCIAL</th>
                <th>NOMBRE</th>
                <th>INICIAL</th>
                <th>APELLIDO PATERNO</th>
                <th>APELLIDO MATERNO</th>
                <th>SEXO</th>
                <th>FECHA NACIMIENTO</th>
                <th>CIUDADANIA</th>
                <th>ESTADO CIVIL</th>
                <th>NOMBRE PADRE O ENCARGADO</th>
                <th>INCAPACIDAD</th>
                <th>CODIGO DE ESCOLARIDAD</th>
                <th>ESCUELA</th>
                <th>MUNICIPIO ESCUELA</th>
                <th>ASISTE REGULARIDAD</th>
                <th>TELEFONO</th>
                <th>EMAIL</th>
                <th>DSC DIR1 POSTAL</th>
                <th>DSC DIR2 POSTAL</th>
                <th>ZIP CODE POSTAL</th>
                <th>CIUDAD POSTAL</th>
                <th>DSC DIR1 RESIDENCIAL</th>
                <th>DSC DIR2 RESIDENCIAL</th>
                <th>ZIP CODE RESIDENCIAL</th>
                <th>CIUDAD RESIDENCIAL</th>
                <th>FECHA MATRICULA</th>
                <th>FECHA BAJA</th>
                <th>SCHOOL CODE</th>
                <th>DIAS VIRTUALES</th>
            </tr>
        </thead>
        <tbody>
            <?php 
        foreach ($students as $student)
                {
                list($name, $initial) = explode(' ', $student->nombre);
                list($lastName1, $lastName2) = explode(' ', $student->apellidos);
                
            $madre = DB::table('madre')->where([
              ['id', $student->id]
            ])->first();

                if ($madre->madre !== '') {
                    $father = $madre->madre;
                } else if ($madre->padre !== '') {
                    $father = $madre->padre;
                } else {
                    $father = $madre->encargado;
                }

                $tel = '';
                if ($madre->tel_m !== '(___)___-____' && $madre->tel_m !== '') {
                    $tel = $madre->tel_m;
                } else if ($madre->tel_p !== '(___)___-____' && $madre->tel_p !== '') {
                    $tel = $madre->tel_p;
                }
                $email = '';
                if ($madre->email_m !== '') {
                    $email = $madre->email_m;
                } else if ($madre->email_p !== '') {
                    $email = $madre->email_p;
                }
            ?>
                <tr>
                    <td><?= $student->id ?></td>
                    <td><?= $student->ss ?></td>
                    <td><?= utf8_encode($name) ?></td>
                    <td><?= $initial[0] ?></td>
                    <td><?= utf8_encode($lastName1) ?></td>
                    <td><?= utf8_encode($lastName2) ?></td>
                    <td><?= $student->genero ?></td>
                    <td><?= haveDate($student->fecha) ?></td>
                    <td></td>
                    <td></td>
                    <td><?= utf8_encode($father) ?></td>
                    <td><?= $student->imp1 ?></td>
                    <td></td>
                    <td><?= $cole->colegio ?></td>
                    <td><?= $cole->pueblo1 ?></td>
                    <td></td>
                    <td><?=$tel?></td>
                    <td><?= $student->email ?></td>
                    <td><?= $madre->dir1 ?></td>
                    <td><?= $madre->dir3 ?></td>
                    <td><?= $madre->zip1 ?></td>
                    <td><?= $madre->pueblo1 ?></td>
                    <td><?= $madre->dir2 ?></td>
                    <td><?= $madre->dir4 ?></td>
                    <td><?= $madre->zip2 ?></td>
                    <td><?= $madre->pueblo2 ?></td>
                    <td><?= haveDate($student->fecha_matri) ?></td>
                    <td><?= haveDate($student->fecha_baja) ?></td>
                    <td></td>
                    <td></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>

</html>