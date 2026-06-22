<?php

require_once __DIR__ . '/../../../../app.php';

use App\Models\Appointments\AppointmentEvent;
use App\Models\Appointments\AppointmentSlot;
use App\Models\Appointments\Appointment;
use App\Services\AppointmentSlotGeneratorService;
use Classes\Session;
use Carbon\Carbon;

try {
    Session::is_logged();

    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['id'])) {
        http_response_code(422);
        echo json_encode(['error' => __('ID de evento requerido')]);
        exit;
    }

    $event = AppointmentEvent::find($data['id']);
    if (!$event) {
        http_response_code(404);
        echo json_encode(['error' => __('Evento no encontrado')]);
        exit;
    }

    // Determinar si necesita regeneración
    $oldGrades = $event->grades ?? [];
    $newGrades = $data['grades'] ?? [];
    $gradesChanged = json_encode(sort($oldGrades)) !== json_encode(sort($newGrades));

    $hoursChanged = $event->start_time !== ($data['start_time'] ?? '')
        || $event->end_time !== ($data['end_time'] ?? '')
        || $event->break_start_time !== ($data['break_start_time'] ?? null)
        || $event->break_end_time !== ($data['break_end_time'] ?? null);

    $needsRegeneration = $gradesChanged || $hoursChanged;

    // Si necesita regeneración, verificar que no haya slots reservados
    if ($needsRegeneration) {
        $reservedSlotsCount = AppointmentSlot::where('appointment_event_id', $event->id)
            ->whereHas('appointment')
            ->count();

        if ($reservedSlotsCount > 0) {
            http_response_code(409);
            echo json_encode([
                'error' => __('No se puede regenerar slots. Existen') . " $reservedSlotsCount " . __('citas reservadas en este evento.'),
                'conflict' => true,
            ]);
            exit;
        }
    }

    // Actualizar evento
    $event->update([
        'name' => $data['name'] ?? $event->name,
        'date' => $data['date'] ?? $event->date,
        'start_time' => $data['start_time'] ?? $event->start_time,
        'end_time' => $data['end_time'] ?? $event->end_time,
        'break_start_time' => $data['break_start_time'] ?? $event->break_start_time,
        'break_end_time' => $data['break_end_time'] ?? $event->break_end_time,
        'slot_duration' => $data['slot_duration'] ?? $event->slot_duration,
        'grades' => $data['grades'] ?? $event->grades,
        'is_active' => $data['is_active'] ?? $event->is_active,
    ]);

    $slotCountAfter = $event->slots()->count();

    if ($needsRegeneration) {
        // Eliminar slots libres (sin citas)
        AppointmentSlot::where('appointment_event_id', $event->id)
            ->doesntHave('appointment')
            ->delete();

        // Obtener nuevamente profesores y generar nuevos slots
        $teacherIds = AppointmentSlotGeneratorService::getTeacherIdsByGrades($data['grades'] ?? $event->grades);

        if (!$teacherIds->isEmpty()) {
            $timeSlots = AppointmentSlotGeneratorService::generateTimeSlots(
                $data['date'] ?? $event->date,
                $data['start_time'] ?? $event->start_time,
                $data['end_time'] ?? $event->end_time,
                $data['slot_duration'] ?? $event->slot_duration,
                $data['break_start_time'] ?? $event->break_start_time,
                $data['break_end_time'] ?? $event->break_end_time
            );

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

            if (!empty($slotsToInsert)) {
                AppointmentSlot::insert($slotsToInsert);
            }
        }

        $slotCountAfter = $event->slots()->count();
    }

    http_response_code(200);
    echo json_encode([
        'message' => __('Evento actualizado exitosamente'),
        'regenerated' => $needsRegeneration,
        'slot_count' => $slotCountAfter,
    ]);
} catch (\Throwable $th) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al actualizar el evento. Por favor, inténtalo de nuevo.']);
}
