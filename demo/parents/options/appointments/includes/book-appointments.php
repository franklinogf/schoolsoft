<?php

require_once __DIR__ . '/../../../../app.php';

use Classes\Session;
use App\Models\Student;
use App\Models\Appointments\Appointment;
use App\Models\Appointments\AppointmentSlot;
use App\Models\Appointments\AppointmentEvent;
use App\Enums\AppointmentMemberEnum;
use App\Enums\AppointmentStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;

header('Content-Type: application/json; charset=utf-8');

try {
    Session::is_logged();

    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['event_id']) || empty($data['student_id']) || empty($data['slot_ids']) || !is_array($data['slot_ids']) || empty($data['members_by_teacher']) || !is_array($data['members_by_teacher'])) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => __('Missing required fields')]);
        exit;
    }

    $event = AppointmentEvent::find($data['event_id']);
    if (!$event || !$event->is_active) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => __('Event not found')]);
        exit;
    }

    $student = Student::find($data['student_id']);
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

    $slotIds = array_values(array_unique(array_map('intval', $data['slot_ids'])));
    if (empty($slotIds)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => __('No slots selected')]);
        exit;
    }

    $familyId = (int)Session::id();

    $membersByTeacher = [];
    foreach ($data['members_by_teacher'] as $teacherId => $memberValue) {
        $teacherIdKey = (string)(int)$teacherId;
        if ($teacherIdKey === '') {
            continue;
        }

        $memberValue = trim((string)$memberValue);
        if ($memberValue === '') {
            continue;
        }

        $memberEnum = AppointmentMemberEnum::tryFrom($memberValue);
        if (!$memberEnum) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => __('Invalid member type')]);
            exit;
        }

        $membersByTeacher[$teacherIdKey] = $memberEnum;
    }

    $notesByTeacher = [];
    if (isset($data['notes_by_teacher']) && is_array($data['notes_by_teacher'])) {
        foreach ($data['notes_by_teacher'] as $teacherId => $note) {
            $teacherIdKey = (string)(int)$teacherId;
            if ($teacherIdKey !== '') {
                $noteText = trim((string)$note);
                $notesByTeacher[$teacherIdKey] = mb_substr($noteText, 0, 500);
            }
        }
    }

    $appointmentIds = DB::connection()->transaction(function () use ($event, $slotIds, $student, $notesByTeacher, $familyId, $membersByTeacher) {
        $slots = AppointmentSlot::whereIn('id', $slotIds)
            ->where('appointment_event_id', $event->id)
            ->with('appointment')
            ->lockForUpdate()
            ->get();

        if ($slots->count() !== count($slotIds)) {
            throw new RuntimeException(__('One or more selected slots are invalid for this event'));
        }

        $teacherSlotCount = [];
        $selectedSlotsByTeacher = [];
        foreach ($slots as $slot) {
            $teacherId = (int)$slot->teacher_id;
            $teacherSlotCount[$teacherId] = ($teacherSlotCount[$teacherId] ?? 0) + 1;
            $selectedSlotsByTeacher[$teacherId] = $slot;
        }

        foreach ($teacherSlotCount as $count) {
            if ($count > 1) {
                throw new RuntimeException(__('Only one slot per teacher is allowed'));
            }
        }

        foreach ($slots as $slot) {
            if ($slot->appointment && (int)$slot->appointment->family_id !== $familyId) {
                throw new RuntimeException(__('One or more selected slots are already booked'));
            }
        }

        $selectedTeacherIds = array_keys($teacherSlotCount);

        foreach ($selectedTeacherIds as $teacherId) {
            if (!isset($membersByTeacher[(string)$teacherId])) {
                throw new RuntimeException(__('Member is required for each selected teacher'));
            }
        }

        $existingAppointments = Appointment::where('family_id', $familyId)
            ->whereHas('slot', function ($query) use ($event) {
                $query->where('appointment_event_id', $event->id)
                    ->whereNotNull('teacher_id');
            })
            ->with('slot')
            ->lockForUpdate()
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

        $scheduleByMemberTime = [];

        foreach ($existingByTeacher as $teacherId => $existingAppointment) {
            if (in_array($teacherId, $selectedTeacherIds, true)) {
                continue;
            }

            $existingSlot = $existingAppointment->slot;
            if (!$existingSlot) {
                continue;
            }

            $memberValue = is_object($existingAppointment->family_member)
                ? $existingAppointment->family_member->value
                : (string)$existingAppointment->family_member;
            $timeValue = $existingSlot->starts_at->format('Y-m-d H:i:s');
            $key = $memberValue . '|' . $timeValue;
            $scheduleByMemberTime[$key] = $teacherId;
        }

        foreach ($selectedSlotsByTeacher as $teacherId => $slot) {
            $memberValue = $membersByTeacher[(string)$teacherId]->value;
            $timeValue = $slot->starts_at->format('Y-m-d H:i:s');
            $key = $memberValue . '|' . $timeValue;

            if (isset($scheduleByMemberTime[$key]) && $scheduleByMemberTime[$key] !== $teacherId) {
                throw new RuntimeException(__('The same family member cannot select the same time with different teachers'));
            }

            $scheduleByMemberTime[$key] = $teacherId;
        }

        $ids = [];
        foreach ($slots as $slot) {
            $teacherId = (int)$slot->teacher_id;
            $noteKey = (string)$teacherId;
            $memberEnum = $membersByTeacher[$noteKey];
            $hasProvidedNote = array_key_exists($noteKey, $notesByTeacher);
            $teacherNote = $hasProvidedNote
                ? (trim((string)$notesByTeacher[$noteKey]) !== '' ? trim((string)$notesByTeacher[$noteKey]) : null)
                : null;

            $existingAppointment = $existingByTeacher[$teacherId] ?? null;

            if ($existingAppointment) {
                $existingAppointment->appointment_slot_id = $slot->id;
                $existingAppointment->student_id = $student->mt;
                $existingAppointment->family_member = $memberEnum;
                $existingAppointment->status = AppointmentStatusEnum::BOOKED;
                $existingAppointment->status_at = Carbon::now();

                if ($hasProvidedNote) {
                    $existingAppointment->notes = $teacherNote;
                }

                $existingAppointment->save();
                $appointment = $existingAppointment;
            } else {
                $appointment = Appointment::create([
                    'appointment_slot_id' => $slot->id,
                    'student_id' => $student->mt,
                    'family_id' => $familyId,
                    'family_member' => $memberEnum,
                    'status' => AppointmentStatusEnum::BOOKED,
                    'notes' => $teacherNote,
                    'status_at' => Carbon::now(),
                ]);
            }

            $ids[] = $appointment->id;
        }

        return $ids;
    });

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => __('Appointments booked successfully'),
        'appointment_ids' => $appointmentIds,
        'count' => count($appointmentIds),
    ]);
} catch (RuntimeException $e) {
    http_response_code(409);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
