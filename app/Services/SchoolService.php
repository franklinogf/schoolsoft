<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Student;
use Illuminate\Support\Collection;

class SchoolService
{
    public static function getAllGrades(): Collection
    {
        return Student::query()->distinct()->orderBy('grado')->pluck('grado');
    }

    public static function getCurrentYear(): string
    {
        return Admin::primaryAdmin()->year();
    }
}
