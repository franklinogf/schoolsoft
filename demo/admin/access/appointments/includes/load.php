<?php

require_once __DIR__ . '/../../../../app.php';

use App\Models\Appointments\AppointmentEvent;
use Classes\Session;

try {
    Session::is_logged();

    $events = AppointmentEvent::with(['slots' => fn($q) => $q->select('id', 'appointment_event_id', 'teacher_id', 'starts_at', 'ends_at')])
        ->orderBy('date', 'desc')
        ->orderBy('start_time', 'asc')
        ->get()
        ->map(fn(AppointmentEvent $event) => [
            'id' => $event->id,
            'name' => $event->name,
            'date' => $event->date,
            'start_time' => $event->start_time,
            'end_time' => $event->end_time,
            'slot_duration' => $event->slot_duration,
            'grades' => $event->grades,
            'is_active' => $event->is_active,
            'slot_count' => $event->slots->count(),
            'created_at' => $event->created_at?->format('Y-m-d H:i'),
        ])
        ->toArray();

    http_response_code(200);
    echo json_encode($events);
} catch (\Throwable $th) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al cargar los eventos']);
}
