<?php
use Classes\Route;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Session;

require_once __DIR__ . '/../../../../app.php';

if ($_SERVER["REQUEST_METHOD"] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = $_DELETE['id'];
    DB::table('T_sms_guardados')->where('id', $id)->delete();

} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'];
    $data = DB::table('T_sms_guardados')->where('id', $id)->first();

    $result = [
        "id" => $data->id,
        "titulo" => $data->titulo,
        "mensaje" => $data->mensaje,
    ];
    echo json_encode($result);

} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['titulo'];
    $message = $_POST['mensaje'];
    $school = new School();
    $year = $school->year();
    $id = DB::table('T_sms_guardados')->insertGetId([
        "titulo" => $title,
        "mensaje" => $message,
        "enviado_por" => Session::id(),
        "fecha" => date('Y-m-d'),
        "hora" => date('H:m:i'),
        "year" => $year
    ]);

    $result = [
        "id" => $id,
        "titulo" => $title,
        "mensaje" => $message,
    ];
    echo json_encode($result);
} else {
    Route::error();
}
