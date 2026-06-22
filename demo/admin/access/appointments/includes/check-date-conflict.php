<?php

require_once __DIR__ . '/../../../../app.php';

use App\Models\Appointments\AppointmentEvent;
use Classes\Session;

try {
    Session::is_logged();

    $date = $_GET['date'] ?? null;
    $excludeEventId = $_GET['exclude_event'] ?? null;

    if (!$date) {
        http_response_code(422);
        echo json_encode(['error' => __('Fecha requerida')]);
        exit;
    }

    $query = AppointmentEvent::where('date', $date);

    // Excluir el evento actual si se proporciona
    if ($excludeEventId) {
        $query->where('id', '!=', $excludeEventId);
    }

    $events = $query->get();

    // Consolidar todos los grados de todos los eventos en esta fecha
    $occupiedGrades = [];
    foreach ($events as $event) {
        $grades = $event->grades ?? [];
        $occupiedGrades = array_merge($occupiedGrades, $grades);
    }

    // Remover duplicados y re-indexar
    $occupiedGrades = array_unique($occupiedGrades);
    $occupiedGrades = array_values($occupiedGrades);

    http_response_code(200);
    echo json_encode([
        'occupied_grades' => $occupiedGrades,
        'has_conflict' => !empty($occupiedGrades)
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
