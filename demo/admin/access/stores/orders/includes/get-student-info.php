<?php
require_once __DIR__ . '/../../../../../app.php';

use App\Models\Student;
use Classes\Session;

Session::is_logged();

header('Content-Type: application/json');

$studentId = $_GET['student_id'] ?? null;

if (!$studentId) {
    echo json_encode(['error' => 'Student ID required']);
    exit;
}

$student = Student::where('id', $studentId)->with('family')->first();

if (!$student) {
    echo json_encode(['error' => 'Student not found']);
    exit;
}

// Get siblings (students with same family id)
$siblings = Student::where('id', $student->id)
    ->orderBy('apellidos')
    ->orderBy('nombre')
    ->get()
    ->map(function ($s) {
        return [
            'id' => $s->id,
            'ss' => $s->ss,
            'nombre' => $s->nombre,
            'apellidos' => $s->apellidos,
            'grado' => $s->grado,
        ];
    });

$response = [
    'id' => $student->id,
    'ss' => $student->ss,
    'fullName' => trim("{$student->apellidos} {$student->nombre}"),
    'studentName' => trim("{$student->nombre} {$student->apellidos}"),
    'family' => [
        'email_m' => $student->family->email_m ?? '',
        'email_p' => $student->family->email_p ?? '',
        'madre' => $student->family->madre ?? '',
        'padre' => $student->family->padre ?? '',
    ],
    'siblings' => $siblings
];

echo json_encode($response);
