<?php

namespace App\Models;

use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $curso
 * @property int $id_profesor
 * @property string $week
 * @property string $dia1_1
 * @property string $dia1_2
 * @property string $dia2_1
 * @property string $dia2_2
 * @property string $dia3_1
 * @property string $dia3_2
 * @property string $dia4_1
 * @property string $dia4_2
 * @property string $dia5_1
 * @property string $dia5_2
 * @property string $nota
 * @property string $year
 * @property Teacher|null $teacher
 * @property Subject|null $subject
 */
class WeeklyPlan3 extends Model
{
    protected $table = 'plansemanal3';
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
     * Scope to filter by course
     */
    public function scopeByCourse(Builder $query, string $curso): Builder
    {
        return $query->where('curso', $curso);
    }

    /**
     * Scope to filter by week
     */
    public function scopeByWeek(Builder $query, string $week): Builder
    {
        return $query->where('week', $week);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'curso', 'curso');
    }

    /**
     * Get formatted week range with month name
     */
    public function getFormattedWeek(): string
    {
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

        if (!$this->week) {
            return '';
        }

        $week = strstr($this->week, "W");
        $y = str_replace('-', '', strstr($this->week, "W", true));

        $startDate = date('d', strtotime($y . $week . "1"));
        $endDate = date('d', strtotime($y . $week . "5"));
        $month = $months[date("m", strtotime($y . $week . "1"))];

        return "{$month} ({$startDate} - {$endDate})";
    }

    /**
     * Get approval status
     */
    public function getApprovalStatus(): string
    {
        if ($this->aprobacion === 'si') {
            return ' - OK';
        } elseif ($this->aprobacion === 'no') {
            return ' - VERIFICAR';
        }
        return '';
    }

    /**
     * Check if plan is approved
     */
    public function isApproved(): bool
    {
        return $this->aprobacion === 'si';
    }

    /**
     * Check if plan needs verification
     */
    public function needsVerification(): bool
    {
        return $this->aprobacion === 'no';
    }

    /**
     * Get days data as array
     */
    public function getDaysData(): array
    {
        $days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        $data = [];

        for ($i = 1; $i <= 5; $i++) {
            $data[] = [
                'name' => $days[$i - 1],
                'tema' => $this->{"dia{$i}_1"} ?? '',
                'objetivo' => $this->{"dia{$i}_2"} ?? '',
            ];
        }

        return $data;
    }

    /**
     * Update approval status
     */
    public function updateApproval(string $comentario, string $aprobacion): bool
    {
        return $this->update([
            'comentario' => $comentario,
            'aprobacion' => $aprobacion
        ]);
    }

    /**
     * Get week dates array
     */
    public function getWeekDates(): array
    {
        if (!$this->week) {
            return [];
        }

        $week = strstr($this->week, "W");
        $y = str_replace('-', '', strstr($this->week, "W", true));
        $days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        $dates = [];

        for ($i = 1; $i <= 5; $i++) {
            $dates[] = [
                'day' => $days[$i - 1],
                'date' => date('d', strtotime($y . $week . $i)),
                'full_date' => date('Y-m-d', strtotime($y . $week . $i))
            ];
        }

        return $dates;
    }
}
