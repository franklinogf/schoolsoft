<?php

namespace App\Enums;

enum PicturePathEnum: string
{
    case TEACHER_PROFILE_PICTURE_PATH = 'pictures/teachers';
    case STUDENT_PROFILE_PICTURE = 'pictures/students';
}
