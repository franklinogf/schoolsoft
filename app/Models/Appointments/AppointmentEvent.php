<?php

declare(strict_types=1);

namespace App\Models\Appointments;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string $date
 * @property-read int $slot_duration
 * @property-read string $start_time
 * @property-read string $end_time
 * @property-read string|null $break_start_time
 * @property-read string|null $break_end_time
 * @property-read array<int, string> $grades
 * @property-read bool $is_active
 * @property-read CarbonInterface|null $created_at
 * @property-read CarbonInterface|null $updated_at
 * @property-read Collection<int, AppointmentSlot> $slots
 */
final class AppointmentEvent extends Model
{
    protected $guarded = [];

    public function slots(): HasMany
    {
        return $this->hasMany(AppointmentSlot::class);
    }

    protected function casts(): array
    {
        return [
            'grades' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
