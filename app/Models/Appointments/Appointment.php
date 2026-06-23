<?php

declare(strict_types=1);

namespace App\Models\Appointments;

use App\Dtos\FamilyMember;
use App\Enums\AppointmentMemberEnum;
use App\Enums\AppointmentStatusEnum;
use App\Models\Family;
use App\Models\Student;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property-read int $appointment_slot_id
 * @property-read int $student_id
 * @property-read int $family_id
 * @property-read AppointmentMemberEnum $family_member
 * @property-read AppointmentStatusEnum $status
 * @property-read string|null $notes
 * @property-read CarbonInterface|null $status_at
 * @property-read CarbonInterface|null $created_at
 * @property-read CarbonInterface|null $updated_at
 * @property-read AppointmentSlot $slot
 * @property-read Student $student
 * @property-read Family $family
 */
final class Appointment extends Model
{
    protected $guarded = [];

    public function slot(): BelongsTo
    {
        return $this->belongsTo(AppointmentSlot::class, 'appointment_slot_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'mt');
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class, 'family_id');
    }

    public function attendee(): FamilyMember
    {
        return match ($this->family_member) {
            AppointmentMemberEnum::Father => $this->family->father(),
            AppointmentMemberEnum::Mother => $this->family->mother(),
            AppointmentMemberEnum::Guardian => $this->family->guardian(),
        };
    }

    protected function casts(): array
    {
        return [
            'family_member' => AppointmentMemberEnum::class,
            'status' => AppointmentStatusEnum::class,
            'status_at' => 'datetime',
        ];
    }
}
