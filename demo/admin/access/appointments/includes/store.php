<?php

require_once __DIR__ . '/../../../../app.php';

use App\Models\Appointments\AppointmentEvent;
use App\Models\Appointments\AppointmentSlot;
use App\Services\AppointmentSlotGeneratorService;
use Classes\Session;
use Carbon\Carbon;

try {
    Session::is_logged();

    $data = json_decode(file_get_contents("php://input"), true);

    // Validaciones básicas
    if (empty($data['name']) || empty($data['date']) || empty($data['start_time']) || empty($data['end_time'])) {
        http_response_code(422);
        echo json_encode(['error' => __('Campo requerido faltante')]);
        exit;
    }

    if (empty($data['grades']) || !is_array($data['grades'])) {
        http_response_code(422);
        echo json_encode(['error' => __('Debe seleccionar al menos un grado')]);
        exit;
    }

    // Validar que no haya conflictos de grados en la misma fecha
    $existingEvent = AppointmentEvent::where('date', $data['date'])->first();
    if ($existingEvent) {
        $existingGrades = $existingEvent->grades ?? [];
        $conflictingGrades = array_intersect($data['grades'], $existingGrades);

        if (!empty($conflictingGrades)) {
            http_response_code(409);
            echo json_encode([
                'error' => __('Los siguientes grados ya tienen un evento asignado para esta fecha: ') . implode(', ', $conflictingGrades),
                'conflicting_grades' => $conflictingGrades
            ]);
            exit;
        }
    }

    $slotDuration = (int)($data['slot_duration'] ?? 30);
    if ($slotDuration < 5 || $slotDuration > 120) {
        $slotDuration = 30;
    }

    // Crear evento
    $event = AppointmentEvent::create([
        'name' => $data['name'],
        'date' => $data['date'],
        'start_time' => $data['start_time'],
        'end_time' => $data['end_time'],
        'break_start_time' => $data['break_start_time'] ?? null,
        'break_end_time' => $data['break_end_time'] ?? null,
        'slot_duration' => $slotDuration,
        'grades' => $data['grades'],
        'is_active' => true,
    ]);

    // Obtener profesores por grados seleccionados
    $teacherIds = AppointmentSlotGeneratorService::getTeacherIdsByGrades($data['grades']);

    if ($teacherIds->isEmpty()) {
        // Evento sin profesores: guardar sin slots
        http_response_code(201);
        echo json_encode([
            'id' => $event->id,
            'message' => __('Evento creado. No se encontraron profesores para los grados seleccionados.'),
            'slot_count' => 0,
        ]);
        exit;
    }

    // Generar slots respetando break
    $timeSlots = AppointmentSlotGeneratorService::generateTimeSlots(
        $data['date'],
        $data['start_time'],
        $data['end_time'],
        $slotDuration,
        $data['break_start_time'] ?? null,
        $data['break_end_time'] ?? null
    );

    if ($timeSlots->isEmpty()) {
        http_response_code(422);
        echo json_encode(['error' => __('No se pueden generar slots con los horarios especificados')]);
        $event->delete();
        exit;
    }

    // Construir array de slots para inserción en lote
    $slotsToInsert = [];
    foreach ($teacherIds as $teacherId) {
        foreach ($timeSlots as $slot) {
            $slotsToInsert[] = [
                'appointment_event_id' => $event->id,
                'teacher_id' => $teacherId,
                'starts_at' => $slot['start'],
                'ends_at' => $slot['end'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
    }

    // Insertar todos los slots en una sola query
    AppointmentSlot::insert($slotsToInsert);

    http_response_code(201);
    echo json_encode([
        'id' => $event->id,
        'message' => __('Evento creado exitosamente'),
        'slot_count' => count($slotsToInsert),
    ]);
} catch (\Throwable $th) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al crear el evento. Por favor, inténtalo de nuevo.']);
}
