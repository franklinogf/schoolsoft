<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $ss
 * @property int $id_estudiante
 * @property string $necesidad
 * @property Student|null $student
 */
class StudentNeed extends Model
{
    protected $table = 'necesidades';
    protected $primaryKey = 'id';
    protected $guarded = [];
    public $timestamps = false;

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'ss', 'ss');
    }
}
