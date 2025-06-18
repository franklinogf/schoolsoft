<?php

require_once '../../../../app.php';

use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\File;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Util;

Session::is_logged();
Server::is_post();



if (isset($_POST['option'])) {
    if ($_POST['option'] === 'save') {
        $title = $_POST['title'];
        $date = $_POST['date'];
        $teacher = $_POST['teacher'];
        $demerits = $_POST['demerits'];
        $time = $_POST['time'];
        $noRegistritation = $_POST['noRegistritation'] !== 'Si' ? '' : 'Si';
        $absence = $_POST['absence'];
        $comment = $_POST['comment'];
        $ss = $_POST['addMemoStudentSs'];
        $student = new Student($ss);
        $memoCodde = DB::table('memos_codes')->where(['codigo', $title])->first();
        list($teacherName, $teacherId) = explode(', ', $teacher);
        DB::table("memos")->insert([
            'id' => $student->id,
            'ss' => $ss,
            'year' => $student->info('year'),
            'grado' => $student->grado,
            'fecha' => $date,
            'profesor' => $teacher,
            'no_matricula' => $noRegistritation,
            'demeritos' => $demerits,
            'hora' => $time,
            'comentario' => $comment,
            'id_m' => $teacherId,
            'titulo' => $memoCodde->nombre,
            'falta' => $absence,
            'dpd' => $title,
        ]);

        Route::redirect("/users/memos/index.php");
    } else if ($_POST['option'] === 'edit') {
        $memoId = $_POST['addMemoId'];
        $title = $_POST['title'];
        $date = $_POST['date'];
        $teacher = $_POST['teacher'];
        $demerits = $_POST['demerits'];
        $time = $_POST['time'];
        $noRegistritation = $_POST['noRegistritation'] !== 'Si' ? '' : 'Si';
        $absence = $_POST['absence'];
        $comment = $_POST['comment'];
        $ss = $_POST['addMemoStudentSs'];
        $student = new Student($ss);
        $memoCodde = DB::table('memos_codes')->where(['codigo', $title])->first();
        list($teacherName, $teacherId) = explode(', ', $teacher);
        DB::table("memos")->where(['mt', $memoId])->update([
            'fecha' => $date,
            'profesor' => $teacher,
            'no_matricula' => $noRegistritation,
            'demeritos' => $demerits,
            'hora' => $time,
            'comentario' => $comment,
            'id_m' => $teacherId,
            'titulo' => $memoCodde->nombre,
            'falta' => $absence,
            'dpd' => $title,
        ]);

        Route::redirect("/users/memos/index.php");
    } else if ($_POST['option'] === 'delete') {
        $memoId = $_POST['addMemoId'];
        $ss = $_POST['addMemoStudentSs'];
        DB::table('memos')->where(['mt', $memoId])->delete();
        Route::redirect("/users/memos/index.php");
    }

}


if (isset($_POST['search'])) {
    $memoId = $_POST['search'];
    $memos = DB::table('memos')->where(['mt', $memoId])->first();

    echo Util::toJson($memos);
}
