<?php

namespace App\Models;

use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $id_profesor
 * @property string $teacher
 * @property string $grade
 * @property string $subject
 * @property string $institution
 * @property string $dates
 * @property string $topic
 * @property string $standard1
 * @property string $standard2
 * @property string $standard3
 * @property string $depth1
 * @property string $depth2
 * @property string $depth3
 * @property string $depth4
 * @property string $strategy1
 * @property string $strategy2
 * @property string $strategy3
 * @property string $appraisal1
 * @property string $appraisal2
 * @property string $appraisal3
 * @property string $appraisal4
 * @property string $appraisal5
 * @property string $appraisal6
 * @property string $appraisal7
 * @property string $appraisal8
 * @property string $appraisal9
 * @property string $appraisal10
 * @property string $appraisal11
 * @property string $appraisal12
 * @property string $appraisal13
 * @property string $appraisal14
 * @property string $general
 * @property string $level1_1
 * @property string $level1_2
 * @property string $level2_1
 * @property string $level2_2
 * @property string $level3_1
 * @property string $level3_2
 * @property string $level4_1
 * @property string $level4_2
 * @property string $year
 * @property string $activities1
 * @property string $activities2
 * @property string $activities3
 * @property string $activities4
 * @property string $activities5
 * @property string $activities6
 * @property string $activities7
 * @property string $activities8
 * @property string $activities9
 * @property string $activities10
 * @property string $materials1
 * @property string $materials2
 * @property string $materials3
 * @property string $materials4
 * @property string $materials5
 * @property string $materials6
 * @property string $materials7
 * @property string $materials8
 * @property string $materials9
 * @property string $materials10
 * @property string $materials11
 * @property string $materials12
 * @property string $materials13
 * @property string $materials14
 * @property string $home1
 * @property string $home2
 * @property string $home3
 * @property string $home4
 * @property string $home5
 * @property string $home6
 * @property string $home7
 * @property string $home8
 * @property string $development1
 * @property string $development2
 * @property string $development3
 * @property string $development4
 * @property string $development5
 * @property string $development6
 * @property string $development7
 * @property string $development8
 * @property string $development9
 * @property string $development10
 * @property string $closing1
 * @property string $closing2
 * @property string $closing3
 * @property string $assessment1
 * @property string $assessment2
 * @property string $assessment3
 * @property string $assessment4
 * @property string $assessment5
 * @property string $assessment6
 * @property string $assessment7
 * @property string $assessment8
 * @property string $assessment9
 * @property string $assessment10
 * @property string $assessment11
 * @property string $assessment12
 * @property string $assessment13
 * @property string $assessment14
 * @property string $tuesday
 * @property string $tuesday1
 * @property string $tuesday2
 * @property string $tuesday3
 * @property string $tuesday4
 * @property string $tuesday5
 * @property string $wednesday
 * @property string $wednesday1
 * @property string $wednesday2
 * @property string $wednesday3
 * @property string $wednesday4
 * @property string $wednesday5
 * @property string $thursday
 * @property string $thursday1
 * @property string $thursday2
 * @property string $thursday3
 * @property string $thursday4
 * @property string $thursday5
 * @property string $friday
 * @property string $friday1
 * @property string $friday2
 * @property string $friday3
 * @property string $friday4
 * @property string $friday5
 * @property Teacher|null $teacher_model
 */
class EnglishPlan extends Model
{
    protected $table = 'plan_ingles';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope(new YearScope);
    }

    public function teacher_model(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'id_profesor', 'id');
    }

    public function scopeByTeacher(Builder $query, int $teacherId): Builder
    {
        return $query->where('id_profesor', $teacherId);
    }

    public function scopeByGrade(Builder $query, string $grade): Builder
    {
        return $query->where('grade', $grade);
    }

    public function scopeBySubject(Builder $query, string $subject): Builder
    {
        return $query->where('subject', $subject);
    }

    /**
     * Get display title for the plan
     */
    public function getDisplayTitle(): string
    {
        return "{$this->subject} - {$this->topic}";
    }
}
