<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Student;
use Classes\Session;
use Illuminate\Support\Collection;

class SchoolService
{
    public static function getAllGrades(): Collection
    {
        return Student::query()->distinct()->orderBy('grado')->pluck('grado');
    }

    public static function getCurrentYear(): string
    {
        if (Session::location() !== 'admin') {
            return Admin::primaryAdmin()->year;
        }

        $user = Admin::user(Session::id())->first();


        return $user->year2;
    }
}
