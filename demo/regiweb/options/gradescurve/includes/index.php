<?php
require_once '../../../../app.php';
use Classes\Controllers\School;
use Classes\DataBase\DB;

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents('php://input'), $_PUT);
    $points = $_PUT['points'] ?? null;
    $course = $_PUT['course'] ?? null;
    $trimester = $_PUT['trimester'] ?? null;
    $value = $_PUT['value'] ?? null;

    // Validation
    if (is_null($points) || is_null($course) || is_null($trimester) || is_null($value)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
        exit;
    }
    if (!is_numeric($points) || !is_numeric($value)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid parameter values']);
        exit;
    }

    $school = new School();
    $error = DB::table('valores')
        ->where([
            ['trimestre', $trimester],
            ['year', $school->year()],
            ['nivel', 'Notas'],
            ['curso', $course],
        ])
        ->update([
            "p{$value}" => $points,
            "f{$value}" => date('Y-m-d'),
        ]);

    if ($error) {
        echo json_encode(['status' => 'error']);
        exit;
    }

    echo json_encode(['status' => 'success']);
}
