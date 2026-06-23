<?php

require_once __DIR__ . '/../../../../app.php';

use Classes\Session;
use App\Models\Student;
use App\Models\Appointments\AppointmentEvent;

try {
    Session::is_logged();

    $eventId = $_GET['event_id'] ?? null;

    if (!$eventId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => __('Event ID required')]);
        exit;
    }

    $event = AppointmentEvent::find($eventId);

    if (!$event || !$event->is_active) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => __('Event not found')]);
        exit;
    }

    // Get family ID from session
    $familyId = Session::id();

    // Query students from this family (Student.id FK to Family.id)
    // YearScope is automatically applied by the Student model
    $students = Student::where('id', $familyId)
        ->whereIn('grado', $event->grades ?? [])
        ->get();

    $result = [
        'success' => true,
        'students' => []
    ];

    if ($students && count($students) > 0) {
        foreach ($students as $student) {
            $result['students'][] = [
                'id' => $student->mt,
                'name' => $student->nombre . ' ' . $student->apellidos,
                'grade' => $student->grado
            ];
        }
    }

    http_response_code(200);
    echo json_encode($result);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
