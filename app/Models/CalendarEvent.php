<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property-read int|null $created_by
 * @property-read string $title
 * @property-read CarbonInterface $start_at
 * @property-read CarbonInterface $end_at
 * @property-read string $color
 * @property-read string|null $description
 * @property-read string|null $location
 * @property-read CarbonInterface|null $created_at
 * @property-read CarbonInterface|null $updated_at
 */
final class CalendarEvent extends Model
{
    protected $guarded = [];

    /**
     * @return BelongsTo<Admin, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_at' => 'immutable_datetime',
            'end_at' => 'immutable_datetime',
        ];
    }
}
