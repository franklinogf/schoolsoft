<?php

namespace App\Models\Foro;

use App\Models\Teacher;
use App\Models\Traits\HasYear;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $creador_id creator's id
 * @property string $tipo created's type (p = Teacher model)
 * @property string $curso
 * @property string $titulo
 * @property string $descripcion
 * @property CarbonInterface $fecha
 * @property string $hora
 * @property string $year
 * @property string $estado (a = activo, c= inactivo)
 * @property CarbonInterface $desde
 * @property-read Teacher $teacher
 * @property-read Collection<int,TopicComment> $comments
 */
class Topic extends Model
{
    use HasYear;
    protected $table = 'foro_entradas';

    protected $guarded = [];

    public $timestamps = false;

    public const string ACTIVE = 'a';
    public const string INACTIVE = 'c';
    public const string TEACHER_TYPE = 'p';

    /**
     * @return BelongsTo<Teacher, $this>
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'creador_id', 'id');
    }

    /**
     * @return HasMany<TopicComment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TopicComment::class, 'entrada_id', 'id');
    }

    /**
     * @param Builder<$this> $query
     */
    protected function scopeActive(Builder $query): void
    {
        $query->where('estado', self::ACTIVE);
    }

    /**
     * @param Builder<$this> $query
     */
    protected function scopeByClass(Builder $query, string $class): void
    {
        $query->where('curso', $class);
    }


    /**
     * @return array<string,string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'creador_id' => 'integer',
            'tipo' => 'string',
            'curso' => 'string',
            'titulo' => 'string',
            'descripcion' => 'string',
            'fecha' => 'date:Y-m-d',
            'hora' => 'string',
            'year' => 'string',
            'estado' => 'string',
            'desde' => 'date:Y-m-d',
        ];
    }
}
