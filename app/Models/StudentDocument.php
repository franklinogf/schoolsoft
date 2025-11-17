<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * @property int $id
 * @property string $ss
 * @property string $grado
 * @property int $codigo
 * @property string $desc1
 * @property string $entrego
 * @property string $year
 * @property string $fecha
 * @property string $nap
 * @property string $fesp
 * @property Student|null $student
 */
class StudentDocument extends Model
{
    
    protected $table = 'docu_estudiantes';

    
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
