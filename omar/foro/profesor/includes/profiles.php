<?php

use Classes\Route;
use Classes\Controllers\Teacher;
use Classes\Util;

require_once '../../../app.php';

if(!isset($_SESSION['logged'])){
   Route::redirect('/foro');
}

$id_teacher = $_POST['id_teacher'];

$teacher = new Teacher($id_teacher);
$teacher->nombre = $_POST['name'];
$teacher->apellidos = $_POST['lastName'];
$teacher->email1 = $_POST['email1'];
$teacher->email2 = $_POST['email2'];
if($_POST['password'] !== ''){
   $teacher->clave = $_POST['password'];
}
// Util::dump($teacher);
$teacher->save();
 


Route::redirect('/foro/profesor/profile.php');
