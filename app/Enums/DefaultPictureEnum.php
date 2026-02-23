<?php

namespace App\Enums;

enum DefaultPictureEnum: string
{
    case NO_PROFILE_PICTURE_TEACHER = 'images/no-picture-teacher.png';
    case NO_PROFILE_PICTURE_TEACHER_MALE = 'images/no-picture-teacher-male.png';
    case NO_PROFILE_PICTURE_TEACHER_FEMALE = 'images/no-picture-teacher-female.png';
    case NO_PROFILE_PICTURE_STUDENT_MALE = 'images/no-picture-boy.png';
    case NO_PROFILE_PICTURE_STUDENT_FEMALE = 'images/no-picture-girl.png';
    case REGIWEB_LOGO = 'images/logo-regiweb.gif';
    case SCHOOLSOFT_LOGO = 'images/logo-schoolsoft.gif';
}
