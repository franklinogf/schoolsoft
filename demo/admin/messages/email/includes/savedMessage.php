<?php
use Classes\Route;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Session;

require_once '../../../../app.php';

if ($_SERVER["REQUEST_METHOD"] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = $_DELETE['id'];
    DB::table('T_correos_guardados')->where('id', $id)->delete();

} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'];
    $data = DB::table('T_correos_guardados')->where('id', $id)->first();

    $result = [
        "id" => $data->id,
        "titulo" => $data->titulo,
        "asunto" => $data->asunto,
        "mensaje" => $data->mensaje,
    ];
    echo json_encode($result);

} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['titulo'];
    $subject = $_POST['asunto'];
    $message = $_POST['mensaje'];
    $school = new School();
    $year = $school->year();
    $id = DB::table('T_correos_guardados')->insertGetId([
        "titulo" => $title,
        "asunto" => $subject,
        "mensaje" => $message,
        "colegio" => Session::id(),
        "year" => $year
    ]);

    $result = [
        "id" => $id,
        "titulo" => $title,
        "asunto" => $subject,
        "mensaje" => $message,
    ];
    echo json_encode($result);
} else {
    Route::error();
}
