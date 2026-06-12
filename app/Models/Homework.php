<?php

namespace App\Models;

use App\Models\Traits\HasYear;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_documento
 * @property string $titulo
 * @property string $descripcion
 * @property string $contenido
 * @property int $tamanio
 * @property string $tipo
 * @property string $nombre_archivo
 * @property string $tamanio_unidad
 * @property string $id2
 * @property CarbonInterface $fec_in
 * @property CarbonInterface $fec_out
 * @property string $curso
 * @property string $lin1
 * @property string $lin2
 * @property string $lin3
 * @property string $enviartarea
 * @property string $hora
 * @property string $year
 * @property Teacher $teacher
 */
class Homework extends Model
{
    use HasYear;

    protected $table = 'tbl_documentos';

    protected $primaryKey = 'id_documento';
    public $timestamps = false;
    protected $guarded = [];

    public const string SEND_HOMEWORK = 'si';
    public const string DONT_SEND_HOMEWORK = 'no';

    /**
     * @return BelongsTo<Teacher, $this>
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'id2', 'id');
    }

    /**
     * @param Builder<$this> $query
     */
    public function scopeOfClass(Builder $query, string $class): void
    {
        $query->where('curso', $class);
    }

    /**
     * @return array<string,string>
     */
    protected function casts(): array
    {
        return [
            'id_documento' => 'integer',
            'titulo' => 'string',
            'descripcion' => 'string',
            'contenido' => 'string',
            'tamanio' => 'integer',
            'tipo' => 'string',
            'nombre_archivo' => 'string',
            'tamanio_unidad' => 'string',
            'id2' => 'string',
            'fec_in' => 'date:Y-m-d',
            'fec_out' => 'date:Y-m-d',
            'curso' => 'string',
            'lin1' => 'string',
            'lin2' => 'string',
            'lin3' => 'string',
            'enviartarea' => 'string',
            'hora' => 'string',
            'year' => 'string',
        ];
    }
}
