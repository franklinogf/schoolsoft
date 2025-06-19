<?php

require_once '../../app.php';

use Classes\File;
use Classes\Route;
use Classes\Controllers\Teacher;
use Classes\Server;
use Classes\Session;

Session::is_logged();
Server::is_post();

$id_teacher = $_POST['id_teacher'];

$teacher = new Teacher($id_teacher);
// Personal Information
$teacher->nombre = $_POST['name'];
$teacher->apellidos = $_POST['lastName'];
$teacher->genero = $_POST['gender'];
$teacher->fecha_nac = $_POST['dob'];
$teacher->email1 = $_POST['email1'];
$teacher->email2 = $_POST['email2'];
$teacher->tel_res = $_POST['residentialPhone'];
$teacher->tel_emer = $_POST['emergencyPhone'];
$teacher->cel = $_POST['cellPhone'];
$teacher->comp = $_POST['cellCompany'];
if ($_POST['password'] !== '') {
   $teacher->clave = $_POST['password'];
}

// Adresses
$teacher->dir1 = $_POST['dir1'];
$teacher->dir2 = $_POST['dir2'];
$teacher->pueblo1 = $_POST['city1'];
$teacher->esta1 = $_POST['state1'];
$teacher->zip1 = $_POST['zip1'];

$teacher->dir3 = $_POST['dir3'];
$teacher->dir4 = $_POST['dir4'];
$teacher->pueblo2 = $_POST['city2'];
$teacher->esta2 = $_POST['state2'];
$teacher->zip2 = $_POST['zip2'];

// Other Information
$teacher->alias = $_POST['alias'];
$teacher->posicion = $_POST['position'];
$teacher->nivel = $_POST['level'];
$teacher->preparacion1 = $_POST['preparation1'];
$teacher->preparacion2 = $_POST['preparation2'];
$teacher->fecha_ini = $_POST['initialDate'];
$teacher->fecha_daja = $_POST['dischargeDate'];
$teacher->re_e = $_POST['getEmails'];
// Clubs
for ($i = 1; $i <= 5; $i++){
   $teacher->{"club$i"} = $_POST["club$i"];
   $teacher->{"pre$i"} = $_POST["pre$i"];
   $teacher->{"vi$i"} = $_POST["vi$i"];
   $teacher->{"se$i"} = $_POST["se$i"];
}


$file = new File('picture');

if ($file->amount > 0) {
   $newName = $teacher->id . '.jpg';
   $teacher->foto_name = $newName;
   $file::upload($file->files, __TEACHER_PROFILE_PICTURE_PATH,$newName);
}

$teacher->save();

Route::redirect('/profile.php');
