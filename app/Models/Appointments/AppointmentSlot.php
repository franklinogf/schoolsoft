<?php

declare(strict_types=1);

namespace App\Models\Appointments;

use App\Models\Teacher;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read int $id
 * @property-read int $appointment_event_id
 * @property-read int $teacher_id
 * @property-read CarbonInterface $starts_at
 * @property-read CarbonInterface $ends_at
 * @property-read CarbonInterface|null $created_at
 * @property-read CarbonInterface|null $updated_at
 * @property-read AppointmentEvent $event
 * @property-read Teacher $teacher
 * @property-read Appointment|null $appointment
 */ final class AppointmentSlot extends Model
{
    protected $guarded = [];

    /**
     * @return BelongsTo<AppointmentEvent, $this>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(AppointmentEvent::class);
    }

    /**
     * @return BelongsTo<Teacher, $this>
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    /**
     * @return HasOne<Appointment, $this>
     */
    public function appointment(): HasOne
    {
        return $this->hasOne(Appointment::class);
    }

    /**
     * @param Builder<$this> $query
     */
    protected function scopeNotBooked(Builder $query): void
    {
        $query->doesntHave('appointment');
    }

    /**
     * @param Builder<$this> $query
     */
    protected function scopeBooked(Builder $query): void
    {
        $query->has('appointment');
    }

    public function isBooked(): bool
    {
        return $this->appointment()->exists();
    }

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }
}
