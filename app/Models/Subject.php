<?php

namespace App\Models;

use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $curso
 * @property string $year
 * @property string $desc1
 * @property string $desc2
 * @property float $credito
 * @property float $peso
 * @property string $entrada
 * @property string $salida
 * @property string $dias
 * @property string $maestro
 * @property float $matri
 * @property float $total
 * @property string $ava
 * @property float $valor
 * @property string $orden
 * @property string $verano
 * @property int $mt
 * @property Teacher|null $teacher
 */
class Subject extends Model
{
    protected $table = 'cursos';
    protected $primaryKey = 'mt';
    public $timestamps = false;
    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope(new YearScope);
        static::addGlobalScope('orderByCurso', function (Builder $builder): void {
            $builder->orderBy('curso');
        });
    }

    protected function descripcion(): Attribute
    {
        $isCosey = school_config('app.cosey', false);

        return Attribute::make(
            get: fn($value, array $attributes) => __LANG === 'es' ? $attributes[$isCosey ? 'descripcion' : 'desc1'] : ($attributes['desc2'] ?: $attributes[$isCosey ? 'descripcion' : 'desc1']),
        );
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'id', 'id');
    }
}
