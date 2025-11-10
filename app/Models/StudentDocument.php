<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * StudentDocument Model
 * 
 * @property int $id
 * @property string $ss
 * @property string $grado
 * @property string $codigo
 * @property string $desc1
 * @property string $entrego
 * @property string $year
 * @property string $fecha
 * @property string $nap
 * @property string $fesp
 */
class StudentDocument extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'docu_estudiantes';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the student that owns the document.
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'ss', 'ss');
    }
}
