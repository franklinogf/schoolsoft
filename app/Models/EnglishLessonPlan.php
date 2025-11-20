<?php

namespace App\Models;

use App\Models\Scopes\YearScope;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $id_profesor
 * @property string $profesor Teacher's full name
 * @property string $materia Subject/Class
 * @property string $titulo Plan title
 * @property CarbonInterface|null $fecha Plan date
 * @property int|null $duracion Duration in weeks
 * @property string|null $transversal1 Educate to love each other
 * @property string|null $transversal2 Educate for citizenship
 * @property string|null $transversal3 Educate for healthy communion
 * @property string|null $transversal4 Educate for conservation of Environment
 * @property string|null $transversal5 Educate for Promotion of Life
 * @property string|null $transversal6 Educate for Transcendence
 * @property string|null $transversal7 Educate for Ethical Leadership
 * @property string|null $integracion1 Spanish integration
 * @property string|null $integracion2 History integration
 * @property string|null $integracion3 Science integration
 * @property string|null $integracion4 Math integration
 * @property string|null $integracion5 Art integration
 * @property string|null $integracion6 Physical Education integration
 * @property string|null $integracion7 Health integration
 * @property string|null $integracion8 Technology integration
 * @property string|null $integracion9 Others integration
 * @property string|null $resumen Summary/Overview
 * @property string|null $pe1 Essential Question 1
 * @property string|null $ed1 Essential Understanding 1
 * @property string|null $pe2 Essential Question 2
 * @property string|null $ed2 Essential Understanding 2
 * @property string|null $pe3 Essential Question 3
 * @property string|null $ed3 Essential Understanding 3
 * @property string|null $pe4 Essential Question 4
 * @property string|null $ed4 Essential Understanding 4
 * @property string|null $pe5 Essential Question 5
 * @property string|null $ed5 Essential Understanding 5
 * @property string|null $objetivo_general General objectives
 * @property string|null $tareas Performance tasks
 * @property string|null $otra Other evidence
 * @property string|null $expectativa Standard
 * @property string|null $estrategia Depth of Knowledge
 * @property string|null $objetivos Objectives
 * @property CarbonInterface|null $fecha1 Monday date
 * @property CarbonInterface|null $fecha2 Tuesday date
 * @property CarbonInterface|null $fecha3 Wednesday date
 * @property CarbonInterface|null $fecha4 Thursday date
 * @property CarbonInterface|null $fecha5 Friday date
 * @property string|null $actividades1 Monday activities
 * @property string|null $actividades2 Tuesday activities
 * @property string|null $actividades3 Wednesday activities
 * @property string|null $actividades4 Thursday activities
 * @property string|null $actividades5 Friday activities
 * @property string|null $acomodo1_1 Monday - Seating
 * @property string|null $acomodo1_2 Monday - Additional Time
 * @property string|null $acomodo1_3 Monday - Individualized help
 * @property string|null $acomodo1_4 Monday - Additional time for tests
 * @property string|null $acomodo1_5 Monday - Positive reinforcement
 * @property string|null $acomodo1_6 Monday - Others
 * @property string|null $otro1 Monday - Other modification text
 * @property string|null $acomodo2_1 Tuesday - Seating
 * @property string|null $acomodo2_2 Tuesday - Additional Time
 * @property string|null $acomodo2_3 Tuesday - Individualized help
 * @property string|null $acomodo2_4 Tuesday - Additional time for tests
 * @property string|null $acomodo2_5 Tuesday - Positive reinforcement
 * @property string|null $acomodo2_6 Tuesday - Others
 * @property string|null $otro2 Tuesday - Other modification text
 * @property string|null $acomodo3_1 Wednesday - Seating
 * @property string|null $acomodo3_2 Wednesday - Additional Time
 * @property string|null $acomodo3_3 Wednesday - Individualized help
 * @property string|null $acomodo3_4 Wednesday - Additional time for tests
 * @property string|null $acomodo3_5 Wednesday - Positive reinforcement
 * @property string|null $acomodo3_6 Wednesday - Others
 * @property string|null $otro3 Wednesday - Other modification text
 * @property string|null $acomodo4_1 Thursday - Seating
 * @property string|null $acomodo4_2 Thursday - Additional Time
 * @property string|null $acomodo4_3 Thursday - Individualized help
 * @property string|null $acomodo4_4 Thursday - Additional time for tests
 * @property string|null $acomodo4_5 Thursday - Positive reinforcement
 * @property string|null $acomodo4_6 Thursday - Others
 * @property string|null $otro4 Thursday - Other modification text
 * @property string|null $acomodo5_1 Friday - Seating
 * @property string|null $acomodo5_2 Friday - Additional Time
 * @property string|null $acomodo5_3 Friday - Individualized help
 * @property string|null $acomodo5_4 Friday - Additional time for tests
 * @property string|null $acomodo5_5 Friday - Positive reinforcement
 * @property string|null $acomodo5_6 Friday - Others
 * @property string|null $otro5 Friday - Other modification text
 * @property string $year Academic year
 * @property Teacher|null $teacher Teacher relationship
 */
class EnglishLessonPlan extends Model
{
    protected $table = 'plan_lesion_ingles';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'id_profesor' => 'integer',
        'duracion' => 'integer',
        'fecha' => 'date:Y-m-d',
        'fecha1' => 'date:Y-m-d',
        'fecha2' => 'date:Y-m-d',
        'fecha3' => 'date:Y-m-d',
        'fecha4' => 'date:Y-m-d',
        'fecha5' => 'date:Y-m-d',
    ];

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

    public function scopeBySubject(Builder $query, string $subject): Builder
    {
        return $query->where('materia', $subject);
    }

    /**
     * Get display title for the plan
     */
    public function getDisplayTitle(): string
    {
        return "{$this->titulo} - {$this->materia}";
    }

    /**
     * Get all transversal themes as associative array
     */
    public function getTransversalThemes(): array
    {
        return [
            'transversal1' => ['label' => 'Educate to love each other', 'checked' => $this->transversal1 === 'si'],
            'transversal2' => ['label' => 'Educate for citizenship', 'checked' => $this->transversal2 === 'si'],
            'transversal3' => ['label' => 'Educate for healthy communion', 'checked' => $this->transversal3 === 'si'],
            'transversal4' => ['label' => 'Educate for conservation of Environment', 'checked' => $this->transversal4 === 'si'],
            'transversal5' => ['label' => 'Educate for Promotion of Life', 'checked' => $this->transversal5 === 'si'],
            'transversal6' => ['label' => 'Educate for Transcendence', 'checked' => $this->transversal6 === 'si'],
            'transversal7' => ['label' => 'Educate for Ethical Leadership', 'checked' => $this->transversal7 === 'si'],
        ];
    }

    /**
     * Get all integration subjects as associative array
     */
    public function getIntegrations(): array
    {
        return [
            'integracion1' => ['label' => 'Spanish', 'checked' => $this->integracion1 === 'si'],
            'integracion2' => ['label' => 'History', 'checked' => $this->integracion2 === 'si'],
            'integracion3' => ['label' => 'Science', 'checked' => $this->integracion3 === 'si'],
            'integracion4' => ['label' => 'Math', 'checked' => $this->integracion4 === 'si'],
            'integracion5' => ['label' => 'Art', 'checked' => $this->integracion5 === 'si'],
            'integracion6' => ['label' => 'Physical Education', 'checked' => $this->integracion6 === 'si'],
            'integracion7' => ['label' => 'Health', 'checked' => $this->integracion7 === 'si'],
            'integracion8' => ['label' => 'Technology', 'checked' => $this->integracion8 === 'si'],
            'integracion9' => ['label' => 'Others', 'checked' => $this->integracion9 === 'si'],
        ];
    }

    /**
     * Get modification options for a specific day (1-5)
     */
    public function getModificationsForDay(int $day): array
    {
        return [
            'seating' => $this->{"acomodo{$day}_1"} === 'si',
            'additional_time' => $this->{"acomodo{$day}_2"} === 'si',
            'individualized_help' => $this->{"acomodo{$day}_3"} === 'si',
            'test_time' => $this->{"acomodo{$day}_4"} === 'si',
            'positive_reinforcement' => $this->{"acomodo{$day}_5"} === 'si',
            'others' => $this->{"acomodo{$day}_6"} === 'si',
            'other_text' => $this->{"otro{$day}"},
        ];
    }

    /**
     * Get all essential questions (PE1-PE5)
     */
    public function getEssentialQuestions(): array
    {
        $questions = [];
        for ($i = 1; $i <= 5; $i++) {
            if (!empty($this->{"pe{$i}"})) {
                $questions[] = $this->{"pe{$i}"};
            }
        }
        return $questions;
    }

    /**
     * Get all essential understandings (ED1-ED5)
     */
    public function getEssentialUnderstandings(): array
    {
        $understandings = [];
        for ($i = 1; $i <= 5; $i++) {
            if (!empty($this->{"ed{$i}"})) {
                $understandings[] = $this->{"ed{$i}"};
            }
        }
        return $understandings;
    }

    /**
     * Get weekly activities for all days
     */
    public function getWeeklyActivities(): array
    {
        return [
            'monday' => ['date' => $this->fecha1, 'activities' => $this->actividades1],
            'tuesday' => ['date' => $this->fecha2, 'activities' => $this->actividades2],
            'wednesday' => ['date' => $this->fecha3, 'activities' => $this->actividades3],
            'thursday' => ['date' => $this->fecha4, 'activities' => $this->actividades4],
            'friday' => ['date' => $this->fecha5, 'activities' => $this->actividades5],
        ];
    }
}
