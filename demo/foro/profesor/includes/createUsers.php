<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;


$teacher = new Teacher(Session::id());
$students = $teacher->homeStudents();
foreach ($students as $student) {
    if ($student->usuario === "") {
        $stu = new Student($student->mt);
        $stu->usuario = __SCHOOL_ACRONYM . $stu->mt;
        $stu->clave = "123456";
        $stu->save();
    }
}
Route::redirect('/profesor/home.php');