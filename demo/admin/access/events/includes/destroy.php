<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\CalendarEvent;

try {

    $data = json_decode(file_get_contents("php://input"), true);

    $event = CalendarEvent::query()
        ->findOrFail($data['id']);

    $event->delete();
} catch (\Throwable $th) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al eliminar el evento. Por favor, inténtalo de nuevo.']);
    exit;
}
