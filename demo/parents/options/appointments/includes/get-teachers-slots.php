<?php

require_once __DIR__ . '/../../../../app.php';

use Classes\Session;
use App\Models\Student;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Appointments\Appointment;
use App\Models\Appointments\AppointmentEvent;
use App\Models\Appointments\AppointmentSlot;



try {
    Session::is_logged();

    $studentId = $_GET['student_id'] ?? null;
    $eventId = $_GET['event_id'] ?? null;

    if (!$studentId || !$eventId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => __('Student ID and Event ID required')]);
        exit;
    }

    $event = AppointmentEvent::find($eventId);
    if (!$event || !$event->is_active) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => __('Event not found')]);
        exit;
    }

    $student = Student::find($studentId);
    if (!$student) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => __('Student not found')]);
        exit;
    }

    if (!in_array($student->grado, $event->grades ?? [])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => __('Student grade not allowed for this event')]);
        exit;
    }

    $familyId = (int)Session::id();

    $classesRecords = Classes::where('grado', $student->grado)
        ->where('ss', $student->ss)
        ->get();

    $subjectCodes = [];
    foreach ($classesRecords as $class) {
        $subject = trim((string)($class->curso ?? ''));
        if ($subject !== '') {
            $subjectCodes[] = $subject;
        }
    }

    $subjectCodes = array_values(array_unique($subjectCodes));
    $subjectDescriptionMap = [];

    if (!empty($subjectCodes)) {
        $subjects = Subject::whereIn('curso', $subjectCodes)->get();

        foreach ($subjects as $subjectModel) {
            $code = trim((string)($subjectModel->curso ?? ''));
            $description = trim((string)($subjectModel->descripcion ?? ''));

            if ($code !== '') {
                $subjectDescriptionMap[$code] = $description;
            }
        }
    }

    $teachersMap = [];
    foreach ($classesRecords as $class) {
        $teacherId = $class->id;
        if (empty($teacherId)) {
            continue;
        }

        if (!array_key_exists($teacherId, $teachersMap)) {
            $teachersMap[$teacherId] = [
                'id' => $teacherId,
                'name' => $class->profesor ?? 'Unknown',
                'subjects' => [],
                'slots' => [],
            ];
        }

        $subject = trim((string)($class->curso ?? ''));
        if ($subject !== '' && !in_array($subject, $teachersMap[$teacherId]['subjects'], true)) {
            $teachersMap[$teacherId]['subjects'][] = $subject;
        }
    }

    $teacherIds = array_keys($teachersMap);
    if (!empty($teacherIds)) {
        $existingAppointments = Appointment::where('family_id', $familyId)
            ->whereHas('slot', function ($query) use ($event, $teacherIds) {
                $query->where('appointment_event_id', $event->id)
                    ->whereIn('teacher_id', $teacherIds);
            })
            ->with('slot')
            ->orderByDesc('updated_at')
            ->get();

        $existingByTeacher = [];
        foreach ($existingAppointments as $existingAppointment) {
            $slot = $existingAppointment->slot;
            if (!$slot) {
                continue;
            }

            $teacherId = (int)$slot->teacher_id;
            if (!isset($existingByTeacher[$teacherId])) {
                $existingByTeacher[$teacherId] = $existingAppointment;
            }
        }

        $slots = AppointmentSlot::where('appointment_event_id', $event->id)
            ->whereIn('teacher_id', $teacherIds)
            ->with('appointment')
            ->orderBy('starts_at', 'asc')
            ->get();

        foreach ($slots as $slot) {
            $teacherId = $slot->teacher_id;
            if (!isset($teachersMap[$teacherId])) {
                continue;
            }

            if ($slot->appointment && (int)$slot->appointment->family_id !== $familyId) {
                continue;
            }

            $existingSelection = $existingByTeacher[(int)$teacherId] ?? null;
            $isSelected = $existingSelection && (int)$existingSelection->appointment_slot_id === (int)$slot->id;

            $teachersMap[$teacherId]['slots'][] = [
                'id' => $slot->id,
                'date' => $event->date,
                'start_time' => $slot->starts_at->format('H:i'),
                'end_time' => $slot->ends_at->format('H:i'),
                'selected' => $isSelected,
            ];
        }

        foreach ($teachersMap as $teacherId => &$teacherData) {
            $existingSelection = $existingByTeacher[(int)$teacherId] ?? null;
            if (!$existingSelection || !$existingSelection->slot) {
                continue;
            }

            $teacherData['existing_selection'] = [
                'appointment_id' => $existingSelection->id,
                'slot_id' => $existingSelection->appointment_slot_id,
                'start_time' => $existingSelection->slot->starts_at->format('H:i'),
                'end_time' => $existingSelection->slot->ends_at->format('H:i'),
                'member' => is_object($existingSelection->family_member) ? $existingSelection->family_member->value : (string)$existingSelection->family_member,
                'note' => (string)($existingSelection->notes ?? ''),
            ];
        }
        unset($teacherData);
    }

    $teachers = [];
    foreach ($teachersMap as $teacher) {
        $displaySubjects = [];
        foreach ($teacher['subjects'] as $subjectCode) {
            $description = $subjectDescriptionMap[$subjectCode] ?? '';
            $displaySubjects[] = $description !== ''
                ? $subjectCode . ' - ' . $description
                : $subjectCode;
        }

        $teacher['subjects'] = $displaySubjects;
        $teacher['subject'] = implode(', ', $displaySubjects);
        $teachers[] = $teacher;
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'teachers' => $teachers,
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
