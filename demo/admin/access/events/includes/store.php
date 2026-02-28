<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\Admin;
use App\Models\CalendarEvent;
use Classes\Session;

try {
    $data = json_decode(file_get_contents("php://input"), true);

    $user = Admin::query()
        ->user(Session::id())->firstOrFail();


    CalendarEvent::create([
        'title' => $data['title'],
        'start_at' => $data['start'],
        'end_at' => $data['end'],
        'color' => $data['color'],
        'description' => $data['description'] ?: null,
        'location' => $data['location'] ?: null,
        'created_by' => $user->id,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al crear el evento. Por favor, inténtalo de nuevo.']);
    exit;
}
