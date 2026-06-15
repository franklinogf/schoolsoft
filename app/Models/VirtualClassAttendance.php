<?php

namespace App\Models;

use App\Models\Traits\HasYear;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $id_virtual
 * @property string $ss_estudiante
 * @property CarbonInterface $fecha
 * @property string $hora
 * @property string $year
 * @property Student $student
 * @property VirtualClass $virtualClass
 */
class VirtualClassAttendance extends Model
{
    use HasYear;

    protected $table = 'asistencia_virtual';
    public $timestamps = false;
    protected $guarded = [];

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'ss_estudiante', 'ss');
    }

    /**
     * @return BelongsTo<VirtualClass, $this>
     */
    public function virtualClass(): BelongsTo
    {
        return $this->belongsTo(VirtualClass::class, 'id_virtual');
    }

    /**
     * @return array<string,string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'ss_estudiante' => 'string',
            'fecha' => 'date:Y-m-d',
            'hora' => 'string',
            'year' => 'string',
        ];
    }
}
