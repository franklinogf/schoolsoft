<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class IncompleteVaccine
 * 
 * @property int $id
 * @property string $curso
 * @property string $ss
 * @property string $year
 * @property string|null $vacuna1
 * @property string|null $vacuna2
 * @property string|null $vacuna3
 * @property string|null $vacuna4
 * @property string|null $vacuna5
 * @property string|null $vacuna6
 * @property string|null $vacuna7
 * @property string|null $vacuna8
 * @property string|null $vacuna9
 * @property string|null $vacuna10
 * @property string|null $vacuna11
 * @property string|null $vacuna12
 * @property string|null $vacuna13
 * @property string|null $cert1
 * @property string|null $cert2
 * @property string|null $cert3
 * @property string|null $pvac
 * @property string|null $comentario
 * 
 * @property-read Student $student
 */
class IncompleteVaccine extends Model
{
    protected $table = 'T_incompletas';

    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'curso',
        'ss',
        'year',
        'vacuna1',
        'vacuna2',
        'vacuna3',
        'vacuna4',
        'vacuna5',
        'vacuna6',
        'vacuna7',
        'vacuna8',
        'vacuna9',
        'vacuna10',
        'vacuna11',
        'vacuna12',
        'vacuna13',
        'cert1',
        'cert2',
        'cert3',
        'pvac',
        'comentario',
    ];

    /**
     * Get the student that owns the incomplete vaccine record
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'ss', 'ss');
    }

    /**
     * Find incomplete vaccine record by student, course and year
     */
    public static function findByCourseAndYear(string $ss, string $curso, string $year): ?self
    {
        return self::where('ss', $ss)
            ->where('curso', $curso)
            ->where('year', $year)
            ->first();
    }

    /**
     * Update or create incomplete vaccine record
     */
    public static function updateOrCreateRecord(string $ss, string $curso, string $year, array $data): self
    {
        return self::updateOrCreate(
            [
                'ss' => $ss,
                'curso' => $curso,
                'year' => $year,
            ],
            $data
        );
    }
}
