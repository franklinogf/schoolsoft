<script>
alert("Hello! I am an alert box1111!!");
</script>

<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Teacher;

Session::is_logged();
$school = new School(Session::id());
$year = $school->info('year2');

$costos = explode('~', $_POST['costos']);
foreach ($costos as $costo) {
	list($id,$activo,$grado) = explode(',', $costo);
    $thisCourse = DB::table('costos')->
    whereRaw("codigo = '$id' and grado = '$grado' and year = '$year'")->update([
        'activo' => $activo,
    ]);

}

echo "UPDATE costos set activo ='$activo' where codigo = '$id' and grado = '$grado' and year = '$year'";
