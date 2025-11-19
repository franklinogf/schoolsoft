<?php

use App\Models\CafeteriaButton;
use Illuminate\Database\Capsule\Manager;

require_once __DIR__ . '/../../app.php';
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'];

    $button = CafeteriaButton::find($id);

    echo json_encode($button, JSON_UNESCAPED_SLASHES);
}
