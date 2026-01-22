<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class InfirmaryCertification
 * 
 * @property int $id
 * @property string $curso
 * @property string $ss
 * @property string $year
 * @property string|null $cert1
 * @property string|null $cert2
 * @property string|null $dec1
 * @property string|null $dec2
 * @property string|null $dec3
 * @property string|null $dec4
 * @property string|null $dec5
 * @property string|null $dec6
 * @property string|null $dec7
 * @property string|null $dec8
 * @property string|null $tes1
 * @property string|null $tes2
 * @property string|null $tes3
 * @property string|null $tes4
 * @property int|null $tes5
 * @property string|null $tes6
 * @property string|null $tes7
 * 
 * @property-read Student $student
 */
class InfirmaryCertification extends Model
{
    protected $table = 'T_exencion';
    
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'curso',
        'ss',
        'year',
        'cert1',
        'cert2',
        'dec1',
        'dec2',
        'dec3',
        'dec4',
        'dec5',
        'dec6',
        'dec7',
        'dec8',
        'tes1',
        'tes2',
        'tes3',
        'tes4',
        'tes5',
        'tes6',
        'tes7',
    ];

    protected $casts = [
        'tes5' => 'integer',
    ];

    /**
     * Get the student that owns the certification
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'ss', 'ss');
    }

    /**
     * Find certification by student, course and year
     */
    public static function findByCourseAndYear(string $ss, string $curso, string $year): ?static
    {
        return static::where('ss', $ss)
            ->where('curso', $curso)
            ->where('year', $year)
            ->first();
    }

    /**
     * Update or create certification
     */
    public static function updateOrCreateCertification(string $ss, string $curso, string $year, array $data): static
    {
        return static::updateOrCreate(
            [
                'ss' => $ss,
                'curso' => $curso,
                'year' => $year,
            ],
            $data
        );
    }
}


