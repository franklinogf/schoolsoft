<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int|string $id Student ID
 * @property string $ss Student social security
 * @property \Carbon\Carbon|null $fecha Date of visit
 * @property string $hora Time of visit
 * @property string|null $razon Reason for visit
 * @property string|null $tratamiento Treatment given
 * @property string|null $notif_padres Parents notified (Si/No)
 * @property string|null $recomendacion Recommendation
 * @property string|null $padre_contacto Parent contacted
 * @property string|null $telefono Phone number
 * @property string|null $observaciones Observations
 * @property-read Student|null $student
 */
class InfirmaryVisit extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'visitas_enfermeria';

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
     * Get the student that owns the visit record.
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
     * Find a specific visit record by its composite key.
     */
    public static function findByCompositeKey(string $ss, string $fecha, string $hora): ?self
    {
        return static::where('ss', $ss)
            ->where('fecha', $fecha)
            ->where('hora', $hora)
            ->first();
    }

    /**
     * Delete a visit record by composite key.
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
     * Update a visit record by composite key.
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
