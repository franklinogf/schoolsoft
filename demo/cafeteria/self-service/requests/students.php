<?php

use App\Models\Admin;
use App\Models\Student;

require_once __DIR__ . '/../../../app.php';
$school = Admin::primaryAdmin();
$year = $school->year;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $barCode = $_GET['barcode'];
    if (!$barCode) {
        http_response_code(400);
        return;
    }
    $student = Student::where('cbarra', $barCode)->first();
    $data = null;
    if ($student) {
        $data = [
            "id" => intval($student->mt),
            "name" => $student->nombre,
            "lastName" => $student->apellidos,
            "depositAmount" => floatval($student->cantidad),
            "profilePictureUrl" => $student->tipo ? "../../picture/{$student->tipo}.jpg" : null,
            "pinCode" => $student->codigopin,
            "hasDiscount" => $student->hde ? strtolower($student->hde) === 'si' : false,
            "grade" => $student->grado,
            "gradeNumber" => intval(substr($student->grado, 0, 2))
        ];
    }

    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputs = json_decode(file_get_contents('php://input'));
    $pinCode = $inputs->pinCode;
    $mt = $inputs->id;
    if (!$pinCode === '' || !$mt === '') {
        http_response_code(400);
        echo json_encode(null, JSON_UNESCAPED_UNICODE);
        exit;
    }

    Student::where('mt', $mt)->update(['codigopin' => $pinCode]);

    $student = Student::where('mt', $mt)->first();
    if ($student) {
        $student = [
            "id" => intval($student->mt),
            "name" => $student->nombre,
            "lastName" => $student->apellidos,
            "depositAmount" => floatval($student->cantidad),
            "profilePictureUrl" => $student->tipo ? "../../picture/{$student->tipo}.jpg" : null,
            "pinCode" => $student->codigopin,
            "hasDiscount" => strtolower($student->hde) === 'si',
            "grade" => $student->grado,
            "gradeNumber" => intval(substr($student->grado, 0, 2))
        ];
    }

    echo json_encode($student, JSON_UNESCAPED_UNICODE);
}
