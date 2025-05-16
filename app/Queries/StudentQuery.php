<?php

namespace App\Queries;

use App\Models\Student;

class StudentQuery
{
    public static function getAllGrades(): array
    {
        return Student::select('grado')
            ->distinct()
            ->orderBy('grado')
            ->pluck('grado')
            ->toArray();
    }
}
