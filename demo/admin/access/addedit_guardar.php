<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

//echo "<pre>";
//var_dump($_POST);
//echo "</pre>";

/*echo "<pre>";
var_dump($_POST);
echo "</pre>";*/
if (isset($_POST['borrar'])) {
	// echo "DELETE FROM acumulativa WHERE id = '{$_POST['borrar']}'";
    DB::table('acumulativa')->where('id', $_POST['borrar'])->delete();

}else{
	$cantidad = sizeof($_POST['nota1']);
	for ($i=0; $i < $cantidad; $i++) { 

        $cur = $_POST['curso'][$i];
        $ye = $_POST['year'][$i];
        $se1 = $_POST['nota1'][$i];
        $se2 = $_POST['nota2'][$i];
        $gr = $_POST['grado'][$i];
        $id = $_POST['id'][$i];
        $curso = DB::table('cursos')->select("DISTINCT desc1, credito")->where('curso', $cur)->orderBy('curso')->first();
	
		if (!isset($_POST['id'][$i]))
 		   {
		
    DB::table('acumulativa')->insert([
        'ss' => $_POST['ss'],
        'nombre' => $_POST['nombre'],
        'apellidos' => $_POST['apellidos'],
        'year' => $ye,
        'curso' => $cur,
        'credito' => $curso->credito,
        'desc1' => $curso->desc1,
        'grado' => $gr,
        'sem1' => $se1,
        'sem2' => $se2,
    ]);

		}else{

    DB::table('acumulativa')->where('id', $id)->update([
        'ss' => $_POST['ss'],
        'nombre' => $_POST['nombre'],
        'apellidos' => $_POST['apellidos'],
        'year' => $ye,
        'curso' => $cur,
        'credito' => $curso->credito,
        'desc1' => $curso->desc1,
        'grado' => $gr,
        'sem1' => $se1,
        'sem2' => $se2,
    ]);
			
		}
		
	}
	header('Location: add_edit.php?estu='.$_POST['ss']);
}