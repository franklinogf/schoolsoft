<?php

namespace App\Models;

use App\Models\Traits\HasYear;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $id_profesor
 * @property string $curso
 * @property string $link
 * @property string|null $titulo
 * @property string|null $clave
 * @property string|null $informacion
 * @property CarbonInterface $fecha
 * @property string $hora
 * @property string $year
 * @property bool $activo
 * @property Teacher $teacher
 * @property Subject $subject
 */
class VirtualClass extends Model
{
    use HasYear;

    protected $table = 'virtual';
    public $timestamps = false;
    protected $guarded = [];

    /**
     * @return BelongsTo<Teacher, $this>
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'id_profesor', 'id');
    }

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'curso', 'curso');
    }

    /**
     * @param Builder<$this> $query
     */
    public function scopeOfClass(Builder $query, string $class): void
    {
        $query->where('curso', $class);
    }

    /**
     * @param Builder<$this> $query
     */
    protected function scopeActive(Builder $query): void
    {
        $query->where('activo', true);
    }



    /**
     * @return array<string,string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'id_profesor' => 'integer',
            'curso' => 'string',
            'link' => 'string',
            'titulo' => 'string',
            'clave' => 'string',
            'informacion' => 'string',
            'fecha' => 'date:Y-m-d',
            'hora' => 'string',
            'year' => 'string',
            'activo' => 'boolean',
        ];
    }
}
