<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int|string $id Student ID
 * @property string $ss Student social security
 * @property CarbonInterface|null $fecha Date of vital signs
 * @property string $hora Time of vital signs
 * @property string|null $bp Blood pressure
 * @property string|null $p Pulse
 * @property string|null $r Respiration
 * @property string|null $t Temperature
 * @property string|null $dxt Blood glucose (dextrose)
 * @property-read Student|null $student
 */
class Vital extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'vitales';

    /**
     * The primary key for the model.
     * NOTE: Table has composite key (id, ss, fecha, hora) - Eloquent doesn't support composite keys natively
     */
    protected $primaryKey = null;
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'fecha' => 'date:Y-m-d',
        ];
    }

    /**
     * Get the student that owns the vital record.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'ss', 'ss');
    }

    /**
     * Scope to filter by student SS.
     */
    public function scopeBySS($query, string $ss)
    {
        return $query->where('ss', $ss);
    }

    /**
     * Scope to filter by student ID.
     */
    public function scopeByStudentId($query, $id)
    {
        return $query->where('id', $id);
    }

    /**
     * Find a specific vital record by its composite key.
     */
    public static function findByCompositeKey(string $ss, string $fecha, string $hora): ?self
    {
        return static::where('ss', $ss)
            ->where('fecha', $fecha)
            ->where('hora', $hora)
            ->first();
    }

    /**
     * Delete a vital record by composite key.
     */
    public static function deleteByCompositeKey($id, string $ss, string $fecha, string $hora): bool
    {
        return static::where('id', $id)
            ->where('ss', $ss)
            ->where('fecha', $fecha)
            ->where('hora', $hora)
            ->delete() > 0;
    }

    /**
     * Update a vital record by composite key.
     */
    public static function updateByCompositeKey($id, string $ss, string $oldFecha, string $oldHora, array $data): bool
    {
        return static::where('id', $id)
            ->where('ss', $ss)
            ->where('fecha', $oldFecha)
            ->where('hora', $oldHora)
            ->update($data) > 0;
    }
}
