<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class VaccineExemption
 * 
 * @property string $ss
 * @property string $vacuna
 * @property string $year
 * @property string|null $excencion
 * @property \Carbon\Carbon|null $fechaEntrega
 * @property \Carbon\Carbon|null $fechaExpiracion
 * 
 * @property-read Student $student
 */
class VaccineExemption extends Model
{
    protected $table = 'excencionesVacunas';

    // Composite primary key - Eloquent doesn't support this natively
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'ss',
        'vacuna',
        'year',
        'excencion',
        'fechaEntrega',
        'fechaExpiracion',
    ];

    protected $casts = [
        'fechaEntrega' => 'datetime',
        'fechaExpiracion' => 'datetime',
    ];

    /**
     * Get the student that owns the vaccine exemption
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'ss', 'ss');
    }

    /**
     * Find a vaccine exemption by composite key
     */
    public static function findByCompositeKey(string $ss, string $vacuna, string $year): ?self
    {
        return self::where('ss', $ss)
            ->where('vacuna', $vacuna)
            ->where('year', $year)
            ->first();
    }

    /**
     * Update or create a vaccine exemption
     */
    public static function updateOrCreateExemption(string $ss, string $vacuna, string $year, array $data): self
    {
        $exemption = self::findByCompositeKey($ss, $vacuna, $year);

        if ($exemption) {
            $exemption->update($data);
            return $exemption;
        }

        return self::create($data);
    }

    /**
     * Delete a vaccine exemption by composite key
     */
    public static function deleteByCompositeKey(string $ss, string $vacuna, string $year): bool
    {
        return self::where('ss', $ss)
            ->where('vacuna', $vacuna)
            ->where('year', $year)
            ->delete() > 0;
    }
}