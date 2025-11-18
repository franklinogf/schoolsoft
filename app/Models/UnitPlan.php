<?php

namespace App\Models;

use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $id_profesor
 * @property string $profesor
 * @property string $materia
 * @property string $titulo
 * @property string $fecha
 * @property int $duracion
 * @property string $transversal1
 * @property string $transversal2
 * @property string $transversal3
 * @property string $transversal4
 * @property string $transversal5
 * @property string $transversal6
 * @property string $transversal7
 * @property string $integracion1
 * @property string $integracion2
 * @property string $integracion3
 * @property string $integracion4
 * @property string $integracion5
 * @property string $integracion6
 * @property string $integracion7
 * @property string $integracion8
 * @property string $integracion9
 * @property string $estandar1
 * @property string $estandar2
 * @property string $meta
 * @property string $resumen
 * @property string $pe1
 * @property string $ed1
 * @property string $pe2
 * @property string $ed2
 * @property string $pe3
 * @property string $ed3
 * @property string $pe4
 * @property string $ed4
 * @property string $pe5
 * @property string $ed5
 * @property string $objetivo_general
 * @property string $objetivo_adquisicion
 * @property string $tareas
 * @property string $otra
 * @property string $actividades
 * @property string $tareas_observaciones
 * @property string $otra_observaciones
 * @property string $actividades_observaciones
 * @property string $expectativa
 * @property string $estrategia
 * @property string $objetivos
 * @property string $fecha1
 * @property string $fecha2
 * @property string $fecha3
 * @property string $fecha4
 * @property string $fecha5
 * @property string $nivel1_1
 * @property string $nivel1_2
 * @property string $nivel1_3
 * @property string $nivel1_4
 * @property string $nivel2_1
 * @property string $nivel2_2
 * @property string $nivel2_3
 * @property string $nivel2_4
 * @property string $nivel3_1
 * @property string $nivel3_2
 * @property string $nivel3_3
 * @property string $nivel3_4
 * @property string $nivel4_1
 * @property string $nivel4_2
 * @property string $nivel4_3
 * @property string $nivel4_4
 * @property string $nivel5_1
 * @property string $nivel5_2
 * @property string $nivel5_3
 * @property string $nivel5_4
 * @property string $inicio1
 * @property string $inicio2
 * @property string $inicio3
 * @property string $inicio4
 * @property string $inicio5
 * @property string $desarrollo1
 * @property string $desarrollo2
 * @property string $desarrollo3
 * @property string $desarrollo4
 * @property string $desarrollo5
 * @property string $cierre1
 * @property string $cierre2
 * @property string $cierre3
 * @property string $cierre4
 * @property string $cierre5
 * @property string $acomodo1_1
 * @property string $acomodo1_2
 * @property string $acomodo1_3
 * @property string $acomodo1_4
 * @property string $acomodo1_5
 * @property string $acomodo1_6
 * @property string $otro1
 * @property string $acomodo2_1
 * @property string $acomodo2_2
 * @property string $acomodo2_3
 * @property string $acomodo2_4
 * @property string $acomodo2_5
 * @property string $acomodo2_6
 * @property string $otro2
 * @property string $acomodo3_1
 * @property string $acomodo3_2
 * @property string $acomodo3_3
 * @property string $acomodo3_4
 * @property string $acomodo3_5
 * @property string $acomodo3_6
 * @property string $otro3
 * @property string $acomodo4_1
 * @property string $acomodo4_2
 * @property string $acomodo4_3
 * @property string $acomodo4_4
 * @property string $acomodo4_5
 * @property string $acomodo4_6
 * @property string $otro4
 * @property string $acomodo5_1
 * @property string $acomodo5_2
 * @property string $acomodo5_3
 * @property string $acomodo5_4
 * @property string $acomodo5_5
 * @property string $acomodo5_6
 * @property string $otro5
 * @property string $year
 * @property Teacher|null $teacher
 */
class UnitPlan extends Model
{
    protected $table = 'plan_unidad';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope(new YearScope);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'id_profesor', 'id');
    }

    public function scopeByTeacher(Builder $query, int $teacherId): Builder
    {
        return $query->where('id_profesor', $teacherId);
    }

    public function scopeBySubject(Builder $query, string $materia): Builder
    {
        return $query->where('materia', $materia);
    }

    /**
     * Get transversal themes as array
     */
    public function getTransversalThemes(): array
    {
        $themes = [];
        $labels = [
            'Educar para el amor al prójimo',
            'Educar para la transcendencia',
            'Educación para la promoción de la vida',
            'Educar para el liderazgo moral',
            'Educación para la ciudadanía consciente y activa',
            'Educar para la comunión',
            'Educar para la conservación del medio ambiente'
        ];

        for ($i = 1; $i <= 7; $i++) {
            if ($this->{"transversal{$i}"} === 'si') {
                $themes[] = $labels[$i - 1];
            }
        }

        return $themes;
    }

    /**
     * Get integration subjects as array
     */
    public function getIntegrationSubjects(): array
    {
        $subjects = [];
        $labels = [
            'Español',
            'Inglés',
            'Estudios Sociales',
            'Ciencia',
            'Matemáticas',
            'Bellas Artes',
            'Educación Física',
            'Salud Escolar',
            'Tecnología'
        ];

        for ($i = 1; $i <= 9; $i++) {
            if ($this->{"integracion{$i}"} === 'si') {
                $subjects[] = $labels[$i - 1];
            }
        }

        return $subjects;
    }

    /**
     * Helper method to get depth levels for a specific day
     */
    public function getDepthLevels(int $day): array
    {
        $levels = [];
        $labels = ['Memorístico', 'Procesamiento', 'Estratégico', 'Extendido'];

        for ($i = 1; $i <= 4; $i++) {
            if ($this->{"nivel{$day}_{$i}"} === 'si') {
                $levels[] = $labels[$i - 1];
            }
        }

        return $levels;
    }

    /**
     * Helper method to get accommodations for a specific day
     */
    public function getAccommodations(int $day): array
    {
        $accommodations = [];
        $labels = [
            'Ubicación adecuada del pupitre',
            'Tiempo adicional',
            'Ayuda individualizada',
            'Tareas y exámenes mas cortos',
            'Refuerzo positivo',
            'Otro'
        ];

        for ($i = 1; $i <= 6; $i++) {
            if ($this->{"acomodo{$day}_{$i}"} === 'si') {
                if ($i === 6 && !empty($this->{"otro{$day}"})) {
                    $accommodations[] = $this->{"otro{$day}"};
                } else {
                    $accommodations[] = $labels[$i - 1];
                }
            }
        }

        return $accommodations;
    }
}
