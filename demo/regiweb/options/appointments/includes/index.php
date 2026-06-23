<?php

require_once __DIR__ . '/../../../../app.php';

use App\Enums\AppointmentMemberEnum;
use App\Enums\AppointmentStatusEnum;
use App\Models\Appointments\Appointment;
use App\Models\Appointments\AppointmentEvent;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Teacher;
use Carbon\Carbon;
use Classes\Session;
use Illuminate\Database\Eloquent\Collection;

header('Content-Type: application/json; charset=utf-8');

try {
    Session::is_logged();

    $teacher = Teacher::find((int) Session::id());
    if (!$teacher) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => __('Teacher not found'),
        ]);
        exit;
    }

    $action = trim((string) ($_POST['action'] ?? ''));

    if ($action === 'getEvents') {
        $events = AppointmentEvent::query()->active()
            ->whereDate('date', '>=', date('Y-m-d'))
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get()
            ->map(static fn(AppointmentEvent $event): array => [
                'id' => $event->id,
                'name' => (string) $event->name,
                'date' => (string) $event->date,
            ])
            ->values();

        echo json_encode([
            'success' => true,
            'events' => $events,
        ]);
        exit;
    }

    if ($action === 'getAppointmentsByEvent') {
        $eventId = (int) ($_POST['event_id'] ?? 0);
        if ($eventId <= 0) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => __('Event is required'),
            ]);
            exit;
        }

        $event = AppointmentEvent::query()
            ->active()
            ->whereTodayOrAfter('date')
            ->find($eventId);

        if (!$event) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => __('Event not found'),
            ]);
            exit;
        }

        /**
         * @var Collection<int, Appointment> $appointments
         */
        $appointments = Appointment::query()
            ->whereHas('slot', static function ($query) use ($teacher, $event): void {
                $query->where('teacher_id', $teacher->id)
                    ->where('appointment_event_id', $event->id);
            })
            ->with(['slot.event', 'student', 'family'])
            ->get()
            ->sortBy(static function (Appointment $appointment): int {
                if (!$appointment->slot || !$appointment->slot->starts_at) {
                    return PHP_INT_MAX;
                }

                return $appointment->slot->starts_at->getTimestamp();
            })
            ->values();

        $eventGrades = array_values($event->grades ?? []);

        $classesQuery = Classes::query()
            ->where('id', $teacher->id);

        if (!empty($eventGrades)) {
            $classesQuery->whereIn('grado', $eventGrades);
        }

        $classes = $classesQuery->get(['grado', 'ss', 'curso']);

        $courseCodesByStudentKey = [];
        $allCourseCodes = [];

        foreach ($classes as $class) {
            $grade = trim((string) ($class->grado ?? ''));
            $section = trim((string) ($class->ss ?? ''));
            $courseCode = trim((string) ($class->curso ?? ''));

            if ($grade === '' || $section === '' || $courseCode === '') {
                continue;
            }

            $studentKey = $grade . '|' . $section;
            if (!isset($courseCodesByStudentKey[$studentKey])) {
                $courseCodesByStudentKey[$studentKey] = [];
            }

            if (!in_array($courseCode, $courseCodesByStudentKey[$studentKey], true)) {
                $courseCodesByStudentKey[$studentKey][] = $courseCode;
                $allCourseCodes[] = $courseCode;
            }
        }

        $allCourseCodes = array_values(array_unique($allCourseCodes));
        $subjectDescriptionByCode = [];

        if (!empty($allCourseCodes)) {
            $subjects = Subject::query()->whereIn('curso', $allCourseCodes)->get(['curso', 'desc1', 'desc2']);
            foreach ($subjects as $subject) {
                $code = trim((string) ($subject->curso ?? ''));
                if ($code !== '') {
                    $subjectDescriptionByCode[$code] = trim((string) ($subject->descripcion ?? ''));
                }
            }
        }

        $involvedSubjects = [];
        foreach ($allCourseCodes as $courseCode) {
            $description = trim((string) ($subjectDescriptionByCode[$courseCode] ?? ''));
            $involvedSubjects[] = $description !== ''
                ? $courseCode . ' - ' . $description
                : $courseCode;
        }
        sort($involvedSubjects);

        $rows = [];
        $subjectGroupsMap = [];

        foreach ($appointments as $appointment) {
            if (!$appointment->slot || !$appointment->student || !$appointment->family) {
                continue;
            }

            $student = $appointment->student;
            $family = $appointment->family;
            $slot = $appointment->slot;

            $studentKey = trim((string) ($student->grado ?? '')) . '|' . trim((string) ($student->ss ?? ''));
            $courseCodes = $courseCodesByStudentKey[$studentKey] ?? [];

            $displayCourses = [];
            foreach ($courseCodes as $courseCode) {
                $description = trim((string) ($subjectDescriptionByCode[$courseCode] ?? ''));
                $displayCourses[] = $description !== ''
                    ? $courseCode . ' - ' . $description
                    : $courseCode;
            }

            sort($displayCourses);

            $appointmentSubjects = !empty($displayCourses)
                ? array_values(array_unique($displayCourses))
                : [__('No subject')];

            $row = [
                'id' => $appointment->id,
                'parent_name' => $appointment->attendee()->name,
                'student_name' => trim((string) $student->nombre . ' ' . (string) $student->apellidos),
                'student_grade' => (string) ($student->grado ?? ''),
                'subject' => !empty($displayCourses) ? implode(', ', $displayCourses) : __('No subject'),
                'subjects' => $appointmentSubjects,
                'time_range' => $slot->starts_at->format('Y-m-d H:i') . ' - ' . $slot->ends_at->format('H:i'),
                'family_member_label' => $appointment->family_member->getLabel(),
                'notes' => (string) ($appointment->notes ?? ''),
                'status_value' => $appointment->status->value,
                'status_label' => $appointment->status->getLabel(),
            ];

            $rows[] = $row;

            foreach ($appointmentSubjects as $subjectLabel) {
                if (!isset($subjectGroupsMap[$subjectLabel])) {
                    $subjectGroupsMap[$subjectLabel] = [];
                }

                $subjectGroupsMap[$subjectLabel][] = $row;
            }
        }

        ksort($subjectGroupsMap);
        $subjectGroups = [];
        foreach ($subjectGroupsMap as $subjectLabel => $appointmentsBySubject) {
            $subjectGroups[] = [
                'subject' => $subjectLabel,
                'appointments' => array_values($appointmentsBySubject),
            ];
        }

        echo json_encode([
            'success' => true,
            'event' => [
                'id' => $event->id,
                'name' => (string) $event->name,
                'date' => (string) $event->date,
                'grades' => $eventGrades,
            ],
            'subjects' => $involvedSubjects,
            'subject_groups' => $subjectGroups,
            'appointments' => $rows,
        ]);
        exit;
    }

    if ($action === 'updateStatus') {
        $appointmentId = (int) ($_POST['appointment_id'] ?? 0);
        $statusRaw = trim((string) ($_POST['status'] ?? ''));

        if ($appointmentId <= 0 || $statusRaw === '') {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => __('Appointment and status are required'),
            ]);
            exit;
        }

        $newStatus = AppointmentStatusEnum::tryFrom($statusRaw);
        $allowedStatuses = [
            AppointmentStatusEnum::DONE,
            AppointmentStatusEnum::CANCELLED,
            AppointmentStatusEnum::NO_SHOW,
        ];

        if (!$newStatus || !in_array($newStatus, $allowedStatuses, true)) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => __('Invalid status'),
            ]);
            exit;
        }

        $appointment = Appointment::query()
            ->where('id', $appointmentId)
            ->whereHas('slot', static function ($query) use ($teacher): void {
                $query->where('teacher_id', $teacher->id);
            })
            ->first();

        if (!$appointment) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => __('Appointment not found'),
            ]);
            exit;
        }

        $currentStatus = $appointment->status instanceof AppointmentStatusEnum
            ? $appointment->status
            : AppointmentStatusEnum::tryFrom((string) $appointment->status);

        if ($currentStatus !== AppointmentStatusEnum::BOOKED) {
            http_response_code(409);
            echo json_encode([
                'success' => false,
                'message' => __('Only booked appointments can be updated'),
            ]);
            exit;
        }

        $appointment->update([
            'status' => $newStatus,
            'status_at' => Carbon::now(),
        ]);

        echo json_encode([
            'success' => true,
            'message' => __('Appointment status updated successfully'),
            'status_value' => $newStatus->value,
            'status_label' => __($newStatus->getLabel()),
        ]);
        exit;
    }

    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => __('Invalid action'),
    ]);
} catch (Throwable $th) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $th->getMessage(),
    ]);
}
