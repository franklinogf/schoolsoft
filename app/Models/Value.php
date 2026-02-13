<?php

namespace App\Models;

use App\Enums\GradePageEnum;
use App\Enums\TrimesterEnum;
use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $curso
 * @property string $trimestre
 * @property string $nivel
 * @property string $year
 * @property string|null $tema1
 * @property string|null $tema2
 * @property string|null $tema3
 * @property string|null $tema4
 * @property string|null $tema5
 * @property string|null $tema6
 * @property string|null $tema7
 * @property string|null $tema8
 * @property string|null $tema9
 * @property string|null $tema10
 * @property string|null $val1
 * @property string|null $val2
 * @property string|null $val3
 * @property string|null $val4
 * @property string|null $val5
 * @property string|null $val6
 * @property string|null $val7
 * @property string|null $val8
 * @property string|null $val9
 * @property string|null $val10
 * @property string|null $fec1
 * @property string|null $fec2
 * @property string|null $fec3
 * @property string|null $fec4
 * @property string|null $fec5
 * @property string|null $fec6
 * @property string|null $fec7
 * @property string|null $fec8
 * @property string|null $fec9
 * @property string|null $fec10
 */
class Value extends Model
{
    protected $table = 'valores';
    public $timestamps = false;
    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope(new YearScope);
    }

    /**
     * Scope to filter by class
     */
    protected function scopeOfClass(Builder $query, string $class): void
    {
        $query->where('curso', $class);
    }

    /**
     * Scope to filter by trimester
     */
    protected function scopeOfTrimester(Builder $query, TrimesterEnum|string $trimester): void
    {
        $value = $trimester instanceof TrimesterEnum ? $trimester->value : $trimester;
        $query->where('trimestre', $value);
    }

    /**
     * Scope to filter by report/level
     */
    protected function scopeOfReport(Builder $query, GradePageEnum|string $report): void
    {
        $value = $report instanceof GradePageEnum ? $report->value : $report;
        $query->where('nivel', $value);
    }

    /**
     * Find or create a value record for the given parameters
     */
    public static function findOrCreateFor(string $class, TrimesterEnum|string $trimester, GradePageEnum|string $report, string $year): self
    {
        $trimesterValue = $trimester instanceof TrimesterEnum ? $trimester->value : $trimester;
        $reportValue = $report instanceof GradePageEnum ? $report->value : $report;

        return static::firstOrCreate([
            'curso' => $class,
            'trimestre' => $trimesterValue,
            'nivel' => $reportValue,
            'year' => $year
        ]);
    }
}
