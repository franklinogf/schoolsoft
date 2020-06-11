<?php
session_start();

/* -------------------------------------------------------------------------- */
/*                         @Class DataBase Information                        */
/* -------------------------------------------------------------------------- */

define('__HOST', "localhost");
define('__USERNAME', "root");
define('__PASSWORD', "");
define('__DB_NAME', "demo");

/* -------------------------------------------------------------------------- */
/*                              Root directories                              */
/* -------------------------------------------------------------------------- */

/* ------------------------------ don't change ------------------------------ */

define('__ROOT_SCHOOL', str_replace('/', DIRECTORY_SEPARATOR, __DIR__));
define('__ROOT', str_replace('/', DIRECTORY_SEPARATOR, dirname(__DIR__)));
define('__SCHOOL_URL', substr($_SERVER['PHP_SELF'], 0, - (strlen($_SERVER['SCRIPT_FILENAME']) - strlen(__ROOT_SCHOOL))));
$root = str_replace(__ROOT_SCHOOL, '', str_replace('/', DIRECTORY_SEPARATOR, $_SERVER['SCRIPT_FILENAME']));
define('__SUB_ROOT_URL', str_replace('\\', '/', substr($root, 0, strpos($root, DIRECTORY_SEPARATOR, 1))));


/* -------------------------------------------------------------------------- */
/*                              Files Directories                             */
/* -------------------------------------------------------------------------- */

define('__TEACHER_PROFILE_PICTURE_PATH', str_replace('/', DIRECTORY_SEPARATOR, '/pictures/teachers/'));
define('__TEACHER_PROFILE_PICTURE_URL', str_replace('/', DIRECTORY_SEPARATOR, __SCHOOL_URL . __TEACHER_PROFILE_PICTURE_PATH));
define('__STUDENT_PROFILE_PICTURE_PATH', str_replace('/', DIRECTORY_SEPARATOR, '/pictures/students/'));
define('__STUDENT_PROFILE_PICTURE_URL', str_replace('/', DIRECTORY_SEPARATOR, __SCHOOL_URL . __STUDENT_PROFILE_PICTURE_PATH));
define('__LOGO_PATH', str_replace('/', DIRECTORY_SEPARATOR, __SCHOOL_URL . '/logo/'));
define('__TEACHER_MESSAGES_FILES_DIRECTORY', str_replace('/', DIRECTORY_SEPARATOR, 'documentos/mensaje/'));
define('__TEACHER_MESSAGES_FILES_DIRECTORY_URL', str_replace('/', DIRECTORY_SEPARATOR, __SCHOOL_URL . DIRECTORY_SEPARATOR . __TEACHER_MESSAGES_FILES_DIRECTORY));
define('__STUDENT_MESSAGES_FILES_DIRECTORY', str_replace('/', DIRECTORY_SEPARATOR, 'documentos/mensaje/estudiante/'));
define('__STUDENT_MESSAGES_FILES_DIRECTORY_URL', str_replace('/', DIRECTORY_SEPARATOR, __SCHOOL_URL . DIRECTORY_SEPARATOR . __STUDENT_MESSAGES_FILES_DIRECTORY));
define('__TEACHER_HOMEWORKS_DIRECTORY', str_replace('/', DIRECTORY_SEPARATOR, 'documentos/'));
define('__TEACHER_HOMEWORKS_DIRECTORY_URL', str_replace('/', DIRECTORY_SEPARATOR, __SCHOOL_URL . DIRECTORY_SEPARATOR . __TEACHER_HOMEWORKS_DIRECTORY));
define('__STUDENT_HOMEWORKS_DIRECTORY', str_replace('/', DIRECTORY_SEPARATOR, 'tareas/'));
define('__STUDENT_HOMEWORKS_DIRECTORY_URL', str_replace('/', DIRECTORY_SEPARATOR, __SCHOOL_URL . DIRECTORY_SEPARATOR . __STUDENT_HOMEWORKS_DIRECTORY));
define('__NO_PROFILE_PICTURE_TEACHER_MALE', str_replace('/', DIRECTORY_SEPARATOR, '/images/no-picture-teacher.png'));
define('__NO_PROFILE_PICTURE_TEACHER_FEMALE', str_replace('/', DIRECTORY_SEPARATOR, '/images/no-picture-teacher.png'));
define('__NO_PROFILE_PICTURE_STUDENT_MALE', str_replace('/', DIRECTORY_SEPARATOR, '/images/no-picture-boy.png'));
define('__NO_PROFILE_PICTURE_STUDENT_FEMALE', str_replace('/', DIRECTORY_SEPARATOR, '/images/no-picture-girl.png'));


/* -------------------------------------------------------------------------- */
/*                                   Config                                   */
/* -------------------------------------------------------------------------- */

// Language: es = Spanish, en = English
define('__LANG', 'es');
define('__DEFAULT_LOGO', 'logo.gif');
define('__DEFAULT_LOGO_REGIWEB', str_replace('/', DIRECTORY_SEPARATOR, '/images/logo-regiweb.gif'));
// regular is 72 x 72
define('__LOGO_SIZE', 200);

define('__LOGIN_LOGO_SIZE', 400);
/* ----------------------------------- PDF ---------------------------------- */

// RGB $pdf->SetFillColor(red,green,blue)
define('__PDF_FILL_COLOR_RED', 89);
define('__PDF_FILL_COLOR_GREEN', 171);
define('__PDF_FILL_COLOR_BLUE', 227);

define('__PDF_LOGO_SIZE', 40);

/* -------------------------------- time zone ------------------------------- */

/** For the United States:
 * Eastern ........... America/New_York
 * Central ........... America/Chicago
 * Mountain .......... America/Denver
 * Mountain no DST ... America/Phoenix
 * Pacific ........... America/Los_Angeles
 * Alaska ............ America/Anchorage
 * Hawaii ............ America/Adak
 * Hawaii no DST ..... Pacific/Honolulu
 * Puerto Rico ....... America/Puerto_Rico*/

date_default_timezone_set("America/Puerto_Rico");


include __ROOT . '/autoload.php';
