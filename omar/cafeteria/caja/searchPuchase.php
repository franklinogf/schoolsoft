<?php

use Classes\Controllers\School;
use Classes\DataBase\DB;

require_once '../../app.php';

$school = new School();
$year = $school->year();
$date = $_POST['date'] ?? null;
$array = [];
if (isset($_POST['ss'])) {
    $ss = $_POST['ss'];

    $result = DB::table('compra_cafeteria')
        ->where([['ss', $ss], ['fecha', $date]])
        ->get();

    if(count($result) === 0){
        $data = ['exist' => false];            
    } else {
        $data = ['exist' => true, 'data' => $result];
    }    
   
} else if (isset($_POST['code'])) {
    $code = $_POST['code'];

    $student = DB::table('year')
        ->select('ss')
        ->where([['year', $year], ['cbarra', $code]])
        ->first();

    if ($student) {       

        $ss = $student->ss;

        $result = DB::table('compra_cafeteria')
            ->where([['ss', $ss], ['fecha', $date]])
            ->get();
            $data = ['exist' => true, 'data' => $result];    
    } else {
        $data = ['exist' => false];
    }
} else {
    $id = $_POST['id'];
    
    $result = DB::table('compra_cafeteria')
        ->where('id', $id)
        ->get();

    if (!$result) {
        $data = ['exist' => false];
    }else{
        $data = ['exist' => true, 'data' => $result];
    }
}

header('Content-Type: application/json');
echo json_encode($data);
