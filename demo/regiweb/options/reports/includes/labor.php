<?php
require_once '../../../../app.php';

use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Teacher;
use Classes\Util;

Session::is_logged();
Server::is_post();
$teacher = new Teacher(Session::id());
if (isset($_POST['getDates'])) {
    $class = $_POST['getDates'];
    $dates = DB::table("labor")->Where([
        ['id_profesor', $teacher->id],
        ['year', $teacher->info('year')],
        ['curso', $class]
    ])->get();
    if ($dates) {
        $array = [
            'response' => true,
            'data' => $dates
        ];
    } else {
        $array = [
            'response' => false
        ];
    }
    echo Util::toJson($array);
}if (isset($_POST['checkDate'])) {
    $class = $_POST['class'];
    $date = $_POST['date'];
   if(DB::table("labor")->Where([
        ['id_profesor', $teacher->id],
        ['year', $teacher->info('year')],
        ['curso', $class],
        ['fecha', $date]
    ])->first()){   
        $array = [
            'response' => true            
        ];
    } else {
        $array = [
            'response' => false
        ];
    }
    echo Util::toJson($array);
}else if(isset($_POST['getReport'])){
    $class = $_POST['class'];
    $date = $_POST['date'];
    $labor = DB::table("labor")->Where([
        ['id_profesor', $teacher->id],
        ['year', $teacher->info('year')],
        ['curso', $class],
        ['fecha', $date]
    ])->first();

    if ($labor) {
        $array = [
            'response' => true,
            'data' => $labor
        ];
    } else {
        $array = [
            'response' => false
        ];
    }
    echo Util::toJson($array);
}else if(isset($_POST['submitNewReport'])){
    DB::table('labor')->insert([
        'curso'=> $_POST['newClass'],
        'fecha'=> $_POST['newDate'],
        'asuntos'=> $_POST['newMatters'],
        'disciplina'=> $_POST['newDiscipline'],
        'asistencias'=> $_POST['newAssists'],
        'entrevistap'=> $_POST['newFatherInterviews'],
        'entrevistae'=> $_POST['newStudentInterviews'],
        'reuniones'=> $_POST['newMeetings'],
        'otros'=> $_POST['newOthers'],
        'cantasuntos'=> $_POST['newAmountMatters'],
        'cantdisciplina'=> $_POST['newAmountDiscipline'],
        'cantasistencias'=> $_POST['newAmountAssists'],
        'cantentrevistap'=> $_POST['newAmountFatherInterviews'],
        'cantentrevistae'=> $_POST['newAmountStudentInterviews'],
        'cantreuniones'=> $_POST['newAmountMeetings'],
        'cantotros'=> $_POST['newAmountOthers'],
        'id_profesor'=> $teacher->id,
        'year'=> $teacher->info('year')        
    ]);
}else if(isset($_POST['editReport'])){
    DB::table('labor')->update([
        'curso'=> $_POST['newClass'],
        'fecha'=> $_POST['newDate'],
        'asuntos'=> $_POST['newMatters'],
        'disciplina'=> $_POST['newDiscipline'],
        'asistencias'=> $_POST['newAssists'],
        'entrevistap'=> $_POST['newFatherInterviews'],
        'entrevistae'=> $_POST['newStudentInterviews'],
        'reuniones'=> $_POST['newMeetings'],
        'otros'=> $_POST['newOthers'],
        'cantasuntos'=> $_POST['newAmountMatters'],
        'cantdisciplina'=> $_POST['newAmountDiscipline'],
        'cantasistencias'=> $_POST['newAmountAssists'],
        'cantentrevistap'=> $_POST['newAmountFatherInterviews'],
        'cantentrevistae'=> $_POST['newAmountStudentInterviews'],
        'cantreuniones'=> $_POST['newAmountMeetings'],
        'cantotros'=> $_POST['newAmountOthers']         
    ]);
    var_dump($_POST);
}else if(isset($_POST['daleteReport'])){
    $class = $_POST['class'];
    $date = $_POST['date'];
    DB::table("labor")->Where([
        ['id_profesor', $teacher->id],
        ['year', $teacher->info('year')],
        ['curso', $class],
        ['fecha', $date]
    ])->delete();
}



