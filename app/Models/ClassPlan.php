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
 * @property string $tema
 * @property string $fecha
 * @property int $duracion
 * @property string $estrategia
 * @property string $antes
 * @property string $durante
 * @property string $despues
 * @property string $tarea1
 * @property string $tarea2
 * @property string $tarea3
 * @property string $tarea4
 * @property string $tarea5
 * @property string $t5
 * @property string $tarea6
 * @property string $tarea7
 * @property string $tarea8
 * @property string $tarea9
 * @property string $tarea10
 * @property string $tarea11
 * @property string $tarea12
 * @property string $tarea13
 * @property string $tarea14
 * @property string $t14
 * @property string $fechaG
 * @property int $duracionG
 * @property int $valorG
 * @property string $estandares1
 * @property string $estandares2
 * @property string $estandares3
 * @property string $estandares4
 * @property string $estandares5
 * @property string $expectativa1
 * @property string $expectativa2
 * @property string $expectativa3
 * @property string $expectativa4
 * @property string $expectativa5
 * @property string $objetivos1
 * @property string $objetivos2
 * @property string $objetivos3
 * @property string $objetivos4
 * @property string $objetivos5
 * @property string $pensamiento1_1
 * @property string $pensamiento1_2
 * @property string $pensamiento1_3
 * @property string $pensamiento1_4
 * @property string $pensamiento2_1
 * @property string $pensamiento2_2
 * @property string $pensamiento2_3
 * @property string $pensamiento2_4
 * @property string $pensamiento3_1
 * @property string $pensamiento3_2
 * @property string $pensamiento3_3
 * @property string $pensamiento3_4
 * @property string $pensamiento4_1
 * @property string $pensamiento4_2
 * @property string $pensamiento4_3
 * @property string $pensamiento4_4
 * @property string $pensamiento5_1
 * @property string $pensamiento5_2
 * @property string $pensamiento5_3
 * @property string $pensamiento5_4
 * @property string $antes1
 * @property string $antes2
 * @property string $antes3
 * @property string $antes4
 * @property string $antes5
 * @property string $durante1
 * @property string $durante2
 * @property string $durante3
 * @property string $durante4
 * @property string $durante5
 * @property string $despues1
 * @property string $despues2
 * @property string $despues3
 * @property string $despues4
 * @property string $despues5
 * @property string $estrategia_a1_1
 * @property string $estrategia_a1_2
 * @property string $estrategia_a1_3
 * @property string $estrategia_a1_4
 * @property string $estrategia_a1_41
 * @property string $estrategia_a2_1
 * @property string $estrategia_a2_2
 * @property string $estrategia_a2_3
 * @property string $estrategia_a2_4
 * @property string $estrategia_a2_41
 * @property string $estrategia_a3_1
 * @property string $estrategia_a3_2
 * @property string $estrategia_a3_3
 * @property string $estrategia_a3_4
 * @property string $estrategia_a3_41
 * @property string $estrategia_a4_1
 * @property string $estrategia_a4_2
 * @property string $estrategia_a4_3
 * @property string $estrategia_a4_4
 * @property string $estrategia_a4_41
 * @property string $estrategia_a5_1
 * @property string $estrategia_a5_2
 * @property string $estrategia_a5_3
 * @property string $estrategia_a5_4
 * @property string $estrategia_a5_41
 * @property string $valores1
 * @property string $valores2
 * @property string $valores3
 * @property string $valores4
 * @property string $valores5
 * @property string $estrategia_e1_1
 * @property string $estrategia_e1_2
 * @property string $estrategia_e1_3
 * @property string $estrategia_e1_4
 * @property string $estrategia_e1_5
 * @property string $estrategia_e1_6
 * @property string $estrategia_e1_7
 * @property string $estrategia_e1_8
 * @property string $estrategia_e1_9
 * @property string $estrategia_e1_10
 * @property string $estrategia_e1_11
 * @property string $estrategia_e1_12
 * @property string $estrategia_e1_13
 * @property string $estrategia_e1_131
 * @property string $estrategia_e2_1
 * @property string $estrategia_e2_2
 * @property string $estrategia_e2_3
 * @property string $estrategia_e2_4
 * @property string $estrategia_e2_5
 * @property string $estrategia_e2_6
 * @property string $estrategia_e2_7
 * @property string $estrategia_e2_8
 * @property string $estrategia_e2_9
 * @property string $estrategia_e2_10
 * @property string $estrategia_e2_11
 * @property string $estrategia_e2_12
 * @property string $estrategia_e2_13
 * @property string $estrategia_e2_131
 * @property string $estrategia_e3_1
 * @property string $estrategia_e3_2
 * @property string $estrategia_e3_3
 * @property string $estrategia_e3_4
 * @property string $estrategia_e3_5
 * @property string $estrategia_e3_6
 * @property string $estrategia_e3_7
 * @property string $estrategia_e3_8
 * @property string $estrategia_e3_9
 * @property string $estrategia_e3_10
 * @property string $estrategia_e3_11
 * @property string $estrategia_e3_12
 * @property string $estrategia_e3_13
 * @property string $estrategia_e3_131
 * @property string $estrategia_e4_1
 * @property string $estrategia_e4_2
 * @property string $estrategia_e4_3
 * @property string $estrategia_e4_4
 * @property string $estrategia_e4_5
 * @property string $estrategia_e4_6
 * @property string $estrategia_e4_7
 * @property string $estrategia_e4_8
 * @property string $estrategia_e4_9
 * @property string $estrategia_e4_10
 * @property string $estrategia_e4_11
 * @property string $estrategia_e4_12
 * @property string $estrategia_e4_13
 * @property string $estrategia_e4_131
 * @property string $estrategia_e5_1
 * @property string $estrategia_e5_2
 * @property string $estrategia_e5_3
 * @property string $estrategia_e5_4
 * @property string $estrategia_e5_5
 * @property string $estrategia_e5_6
 * @property string $estrategia_e5_7
 * @property string $estrategia_e5_8
 * @property string $estrategia_e5_9
 * @property string $estrategia_e5_10
 * @property string $estrategia_e5_11
 * @property string $estrategia_e5_12
 * @property string $estrategia_e5_13
 * @property string $estrategia_e5_131
 * @property string $conceptos1_1
 * @property string $conceptos1_2
 * @property string $conceptos1_3
 * @property string $conceptos1_4
 * @property string $conceptos1_5
 * @property string $conceptos1_6
 * @property string $conceptos1_7
 * @property string $conceptos1_8
 * @property string $conceptos2_1
 * @property string $conceptos2_2
 * @property string $conceptos2_3
 * @property string $conceptos2_4
 * @property string $conceptos2_5
 * @property string $conceptos2_6
 * @property string $conceptos2_7
 * @property string $conceptos2_8
 * @property string $conceptos3_1
 * @property string $conceptos3_2
 * @property string $conceptos3_3
 * @property string $conceptos3_4
 * @property string $conceptos3_5
 * @property string $conceptos3_6
 * @property string $conceptos3_7
 * @property string $conceptos3_8
 * @property string $conceptos4_1
 * @property string $conceptos4_2
 * @property string $conceptos4_3
 * @property string $conceptos4_4
 * @property string $conceptos4_5
 * @property string $conceptos4_6
 * @property string $conceptos4_7
 * @property string $conceptos4_8
 * @property string $conceptos5_1
 * @property string $conceptos5_2
 * @property string $conceptos5_3
 * @property string $conceptos5_4
 * @property string $conceptos5_5
 * @property string $conceptos5_6
 * @property string $conceptos5_7
 * @property string $conceptos5_8
 * @property string $temas1_1
 * @property string $temas1_2
 * @property string $temas1_3
 * @property string $temas1_4
 * @property string $temas1_5
 * @property string $temas1_6
 * @property string $temas1_7
 * @property string $temas1_8
 * @property string $temas2_1
 * @property string $temas2_2
 * @property string $temas2_3
 * @property string $temas2_4
 * @property string $temas2_5
 * @property string $temas2_6
 * @property string $temas2_7
 * @property string $temas2_8
 * @property string $temas3_1
 * @property string $temas3_2
 * @property string $temas3_3
 * @property string $temas3_4
 * @property string $temas3_5
 * @property string $temas3_6
 * @property string $temas3_7
 * @property string $temas3_8
 * @property string $temas4_1
 * @property string $temas4_2
 * @property string $temas4_3
 * @property string $temas4_4
 * @property string $temas4_5
 * @property string $temas4_6
 * @property string $temas4_7
 * @property string $temas4_8
 * @property string $temas5_1
 * @property string $temas5_2
 * @property string $temas5_3
 * @property string $temas5_4
 * @property string $temas5_5
 * @property string $temas5_6
 * @property string $temas5_7
 * @property string $temas5_8
 * @property string $materiales1_1
 * @property string $materiales1_2
 * @property string $materiales1_3
 * @property string $materiales1_31
 * @property string $materiales1_4
 * @property string $materiales1_41
 * @property string $materiales1_5
 * @property string $materiales1_51
 * @property string $materiales1_6
 * @property string $materiales1_7
 * @property string $materiales1_71
 * @property string $materiales2_1
 * @property string $materiales2_2
 * @property string $materiales2_3
 * @property string $materiales2_31
 * @property string $materiales2_4
 * @property string $materiales2_41
 * @property string $materiales2_5
 * @property string $materiales2_51
 * @property string $materiales2_6
 * @property string $materiales2_7
 * @property string $materiales2_71
 * @property string $materiales3_1
 * @property string $materiales3_2
 * @property string $materiales3_3
 * @property string $materiales3_31
 * @property string $materiales3_4
 * @property string $materiales3_41
 * @property string $materiales3_5
 * @property string $materiales3_51
 * @property string $materiales3_6
 * @property string $materiales3_7
 * @property string $materiales3_71
 * @property string $materiales4_1
 * @property string $materiales4_2
 * @property string $materiales4_3
 * @property string $materiales4_31
 * @property string $materiales4_4
 * @property string $materiales4_41
 * @property string $materiales4_5
 * @property string $materiales4_51
 * @property string $materiales4_6
 * @property string $materiales4_7
 * @property string $materiales4_71
 * @property string $materiales5_1
 * @property string $materiales5_2
 * @property string $materiales5_3
 * @property string $materiales5_31
 * @property string $materiales5_4
 * @property string $materiales5_41
 * @property string $materiales5_5
 * @property string $materiales5_51
 * @property string $materiales5_6
 * @property string $materiales5_7
 * @property string $materiales5_71
 * @property string $tareas1_1
 * @property string $tareas1_11
 * @property string $tareas1_2
 * @property string $tareas1_21
 * @property string $tareas1_3
 * @property string $tareas1_31
 * @property string $tareas2_1
 * @property string $tareas2_11
 * @property string $tareas2_2
 * @property string $tareas2_21
 * @property string $tareas2_3
 * @property string $tareas2_31
 * @property string $tareas3_1
 * @property string $tareas3_11
 * @property string $tareas3_2
 * @property string $tareas3_21
 * @property string $tareas3_3
 * @property string $tareas3_31
 * @property string $tareas4_1
 * @property string $tareas4_11
 * @property string $tareas4_2
 * @property string $tareas4_21
 * @property string $tareas4_3
 * @property string $tareas4_31
 * @property string $tareas5_1
 * @property string $tareas5_11
 * @property string $tareas5_2
 * @property string $tareas5_21
 * @property string $tareas5_3
 * @property string $tareas5_31
 * @property string $actividad_antes1
 * @property string $actividad_antes2
 * @property string $actividad_antes21
 * @property string $actividad_antes3
 * @property string $actividad_antes4
 * @property string $actividad_antes5
 * @property string $actividad_antes51
 * @property string $actividad_antes6
 * @property string $actividad_antes7
 * @property string $actividad_antes8
 * @property string $actividad_antes81
 * @property string $actividad_durante1
 * @property string $actividad_durante2
 * @property string $actividad_durante3
 * @property string $actividad_durante4
 * @property string $actividad_durante5
 * @property string $actividad_durante6
 * @property string $actividad_durante7
 * @property string $actividad_durante8
 * @property string $actividad_durante9
 * @property string $actividad_durante91
 * @property string $actividad_despues1
 * @property string $actividad_despues2
 * @property string $actividad_despues3
 * @property string $actividad_despues4
 * @property string $actividad_despues5
 * @property string $actividad_despues6
 * @property string $actividad_despues7
 * @property string $actividad_despues71
 * @property string $year
 * @property Teacher|null $teacher
 */
class ClassPlan extends Model
{
    protected $table = 'plan_clase';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope(new YearScope);
    }

    /**
     * Relationship with Teacher model
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'id_profesor', 'id');
    }

    /**
     * Scope to filter by teacher
     */
    public function scopeByTeacher(Builder $query, int $teacherId): Builder
    {
        return $query->where('id_profesor', $teacherId);
    }

    /**
     * Scope to filter by subject
     */
    public function scopeBySubject(Builder $query, string $materia): Builder
    {
        return $query->where('materia', $materia);
    }

    /**
     * Get plan title (tema - materia)
     */
    public function getTitle(): string
    {
        return "{$this->tema} - {$this->materia}";
    }

    /**
     * Get formatted date
     */
    public function getFormattedDate(): string
    {
        if (!$this->fecha) {
            return '';
        }

        $months = [
            "01" => "Enero",
            "02" => "Febrero",
            "03" => "Marzo",
            "04" => "Abril",
            "05" => "Mayo",
            "06" => "Junio",
            "07" => "Julio",
            "08" => "Agosto",
            "09" => "Septiembre",
            "10" => "Octubre",
            "11" => "Noviembre",
            "12" => "Diciembre"
        ];

        $date = date_create($this->fecha);
        $day = date_format($date, 'd');
        $month = $months[date_format($date, 'm')];
        $year = date_format($date, 'Y');

        return "{$day} de {$month} de {$year}";
    }

    /**
     * Get weekly guide formatted date
     */
    public function getFormattedWeeklyGuideDate(): string
    {
        if (!$this->fechaG) {
            return '';
        }

        $months = [
            "01" => "Enero",
            "02" => "Febrero",
            "03" => "Marzo",
            "04" => "Abril",
            "05" => "Mayo",
            "06" => "Junio",
            "07" => "Julio",
            "08" => "Agosto",
            "09" => "Septiembre",
            "10" => "Octubre",
            "11" => "Noviembre",
            "12" => "Diciembre"
        ];

        $date = date_create($this->fechaG);
        $day = date_format($date, 'd');
        $month = $months[date_format($date, 'm')];
        $year = date_format($date, 'Y');

        return "{$day} de {$month} de {$year}";
    }

    /**
     * Get all task checkboxes as array
     */
    public function getTasks(): array
    {
        $tasks = [
            'tarea1' => 'Prueba',
            'tarea2' => 'Quizz',
            'tarea3' => 'Proyecto',
            'tarea4' => 'Mapa de conceptos',
            'tarea5' => 'Organizador gráfico',
            'tarea6' => 'Ejercicios de práctica',
            'tarea7' => 'Tirilla cómica',
            'tarea8' => 'Pregunta abierta',
            'tarea9' => 'Laboratorio',
            'tarea10' => 'Construcción de modelos',
            'tarea11' => 'Debate',
            'tarea12' => 'Dibujo',
            'tarea13' => 'Trabajo Creativo',
            'tarea14' => 'Otros'
        ];

        $result = [];
        foreach ($tasks as $field => $label) {
            if ($this->$field === 'si') {
                $result[] = [
                    'field' => $field,
                    'label' => $label,
                    'value' => $field === 'tarea5' ? $this->t5 : ($field === 'tarea14' ? $this->t14 : '')
                ];
            }
        }

        return $result;
    }

    /**
     * Get weekly data for a specific day (1-5)
     */
    public function getWeeklyDayData(int $day): array
    {
        if ($day < 1 || $day > 5) {
            return [];
        }

        return [
            'estandares' => $this->{"estandares{$day}"} ?? '',
            'expectativa' => $this->{"expectativa{$day}"} ?? '',
            'objetivos' => $this->{"objetivos{$day}"} ?? '',
            'antes' => $this->{"antes{$day}"} ?? '',
            'durante' => $this->{"durante{$day}"} ?? '',
            'despues' => $this->{"despues{$day}"} ?? '',
            'valores' => $this->{"valores{$day}"} ?? '',
        ];
    }

    /**
     * Get thinking levels for a specific day
     */
    public function getThinkingLevels(int $day): array
    {
        if ($day < 1 || $day > 5) {
            return [];
        }

        return [
            'memoristico' => $this->{"pensamiento{$day}_1"} === 'si',
            'procesamiento' => $this->{"pensamiento{$day}_2"} === 'si',
            'estrategico' => $this->{"pensamiento{$day}_3"} === 'si',
            'extendido' => $this->{"pensamiento{$day}_4"} === 'si',
        ];
    }

    /**
     * Get academic strategies for a specific day
     */
    public function getAcademicStrategies(int $day): array
    {
        if ($day < 1 || $day > 5) {
            return [];
        }

        return [
            'aprendizaje_problemas' => $this->{"estrategia_a{$day}_1"} === 'si',
            'trabajo_cooperativo' => $this->{"estrategia_a{$day}_2"} === 'si',
            'ciclos_aprendizaje' => $this->{"estrategia_a{$day}_3"} === 'si',
            'eca' => $this->{"estrategia_a{$day}_4"} === 'si',
            'eca_value' => $this->{"estrategia_a{$day}_41"} ?? '',
        ];
    }

    /**
     * Get differentiated education strategies for a specific day
     */
    public function getDifferentiatedStrategies(int $day): array
    {
        if ($day < 1 || $day > 5) {
            return [];
        }

        $strategies = [];
        for ($i = 1; $i <= 13; $i++) {
            if ($this->{"estrategia_e{$day}_{$i}"} === 'si') {
                $strategies[] = [
                    'index' => $i,
                    'value' => $this->{"estrategia_e{$day}_{$i}1"} ?? ''
                ];
            }
        }

        return $strategies;
    }

    /**
     * Get concepts and skills for a specific day
     */
    public function getConcepts(int $day): array
    {
        if ($day < 1 || $day > 5) {
            return [];
        }

        $concepts = [];
        for ($i = 1; $i <= 8; $i++) {
            if ($this->{"conceptos{$day}_{$i}"} === 'si') {
                $concepts[] = $i;
            }
        }

        return $concepts;
    }

    /**
     * Get transversal themes for a specific day
     */
    public function getThemes(int $day): array
    {
        if ($day < 1 || $day > 5) {
            return [];
        }

        $themes = [];
        for ($i = 1; $i <= 8; $i++) {
            if ($this->{"temas{$day}_{$i}"} === 'si') {
                $themes[] = $i;
            }
        }

        return $themes;
    }

    /**
     * Get materials/resources for a specific day
     */
    public function getMaterials(int $day): array
    {
        if ($day < 1 || $day > 5) {
            return [];
        }

        $materials = [];
        for ($i = 1; $i <= 7; $i++) {
            if ($this->{"materiales{$day}_{$i}"} === 'si') {
                $materials[] = [
                    'index' => $i,
                    'value' => $this->{"materiales{$day}_{$i}1"} ?? ''
                ];
            }
        }

        return $materials;
    }

    /**
     * Get assignments for a specific day
     */
    public function getAssignments(int $day): array
    {
        if ($day < 1 || $day > 5) {
            return [];
        }

        $assignments = [];
        for ($i = 1; $i <= 3; $i++) {
            if ($this->{"tareas{$day}_{$i}"} === 'si') {
                $assignments[] = [
                    'index' => $i,
                    'value' => $this->{"tareas{$day}_{$i}1"} ?? ''
                ];
            }
        }

        return $assignments;
    }

    /**
     * Get activities (before/during/after) data
     */
    public function getActivities(): array
    {
        $before = [];
        for ($i = 1; $i <= 8; $i++) {
            if ($this->{"actividad_antes{$i}"} === 'si') {
                $before[] = [
                    'index' => $i,
                    'value' => $this->{"actividad_antes{$i}1"} ?? ''
                ];
            }
        }

        $during = [];
        for ($i = 1; $i <= 9; $i++) {
            if ($this->{"actividad_durante{$i}"} === 'si') {
                $during[] = [
                    'index' => $i,
                    'value' => $this->{"actividad_durante{$i}1"} ?? ''
                ];
            }
        }

        $after = [];
        for ($i = 1; $i <= 7; $i++) {
            if ($this->{"actividad_despues{$i}"} === 'si') {
                $after[] = [
                    'index' => $i,
                    'value' => $this->{"actividad_despues{$i}1"} ?? ''
                ];
            }
        }

        return [
            'before' => $before,
            'during' => $during,
            'after' => $after,
        ];
    }

    /**
     * Update plan data with comprehensive checkbox handling
     */
    public function updatePlanData(array $data): bool
    {
        // Process all checkboxes and set to 'si' or ''
        $checkboxFields = [];

        // Tasks (tarea1-tarea14)
        for ($i = 1; $i <= 14; $i++) {
            $checkboxFields["tarea{$i}"] = isset($data["tarea{$i}"]) ? 'si' : '';
        }

        // Thinking levels (pensamiento1_1 to pensamiento5_4)
        for ($i = 1; $i <= 5; $i++) {
            for ($j = 1; $j <= 4; $j++) {
                $checkboxFields["pensamiento{$i}_{$j}"] = isset($data["pensamiento{$i}_{$j}"]) ? 'si' : '';
            }
        }

        // Academic strategies
        for ($i = 1; $i <= 5; $i++) {
            for ($j = 1; $j <= 4; $j++) {
                $checkboxFields["estrategia_a{$i}_{$j}"] = isset($data["estrategia_a{$i}_{$j}"]) ? 'si' : '';
            }
        }

        // Differentiated strategies
        for ($i = 1; $i <= 5; $i++) {
            for ($j = 1; $j <= 13; $j++) {
                $checkboxFields["estrategia_e{$i}_{$j}"] = isset($data["estrategia_e{$i}_{$j}"]) ? 'si' : '';
            }
        }

        // Concepts
        for ($i = 1; $i <= 5; $i++) {
            for ($j = 1; $j <= 8; $j++) {
                $checkboxFields["conceptos{$i}_{$j}"] = isset($data["conceptos{$i}_{$j}"]) ? 'si' : '';
            }
        }

        // Themes
        for ($i = 1; $i <= 5; $i++) {
            for ($j = 1; $j <= 8; $j++) {
                $checkboxFields["temas{$i}_{$j}"] = isset($data["temas{$i}_{$j}"]) ? 'si' : '';
            }
        }

        // Materials
        for ($i = 1; $i <= 5; $i++) {
            for ($j = 1; $j <= 7; $j++) {
                $checkboxFields["materiales{$i}_{$j}"] = isset($data["materiales{$i}_{$j}"]) ? 'si' : '';
            }
        }

        // Assignments
        for ($i = 1; $i <= 5; $i++) {
            for ($j = 1; $j <= 3; $j++) {
                $checkboxFields["tareas{$i}_{$j}"] = isset($data["tareas{$i}_{$j}"]) ? 'si' : '';
            }
        }

        // Activities
        for ($i = 1; $i <= 8; $i++) {
            $checkboxFields["actividad_antes{$i}"] = isset($data["actividad_antes{$i}"]) ? 'si' : '';
        }
        for ($i = 1; $i <= 9; $i++) {
            $checkboxFields["actividad_durante{$i}"] = isset($data["actividad_durante{$i}"]) ? 'si' : '';
        }
        for ($i = 1; $i <= 7; $i++) {
            $checkboxFields["actividad_despues{$i}"] = isset($data["actividad_despues{$i}"]) ? 'si' : '';
        }

        // Merge checkbox fields with regular data
        $updateData = array_merge($data, $checkboxFields);

        return $this->update($updateData);
    }
}
