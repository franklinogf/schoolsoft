<?php

require_once __DIR__ . '/../../../app.php';

use App\Models\Teacher;
use Classes\File;
use Classes\Route;

use Classes\Server;
use Classes\Session;

Session::is_logged();
Server::is_post();

$teacher = Teacher::query()->withCount('homeStudents')->findOrFail(Session::id());

$teacher->fill([
   'nombre' => $_POST['name'],
   'apellidos' => $_POST['lastName'],
   'email1' => $_POST['email1'],
   'email2' => $_POST['email2'],
   'cel' => $_POST['cellPhone'],
   'comp' => $_POST['cellCompany'],
]);


if ($_POST['password'] !== '') {
   $teacher->fill(['clave' => $_POST['password']]);
}


$file = new File('picture');

if ($file->amount > 0) {
   $newName = $teacher->id . '.jpg';
   $teacher->foto_name = $newName;
   $file::upload($file->files, __TEACHER_PROFILE_PICTURE_PATH, $newName);
}

$teacher->save();

Route::redirect('/profesor/profile.php');
