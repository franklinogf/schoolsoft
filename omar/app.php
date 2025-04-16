<?php

use App\Enums\LanguageCode;
use App\Models\Admin;
use Core\Database;
use Core\TranslatorFactory;
use Dotenv\Dotenv;
use Illuminate\Database\Eloquent\Relations\Relation;

session_start();
require_once 'database.php';



/* -------------------------------------------------------------------------- */
/*                              Root directories                              */
/* -------------------------------------------------------------------------- */

/* ------------------------------ don't change ------------------------------ */

define('__ROOT_SCHOOL', str_replace('/', DIRECTORY_SEPARATOR, __DIR__)); # /home/admin/public_html/demo
define('__ROOT', str_replace('/', DIRECTORY_SEPARATOR, dirname(__DIR__))); # /home/admin/public_html
define('__SCHOOL_URL', substr($_SERVER['PHP_SELF'], 0, - (strlen($_SERVER['SCRIPT_FILENAME']) - strlen(__ROOT_SCHOOL)))); # /demo
$root = str_replace(__ROOT_SCHOOL, '', str_replace('/', DIRECTORY_SEPARATOR, $_SERVER['SCRIPT_FILENAME']));
define('__SUB_ROOT_URL', str_replace('\\', '/', substr($root, 0, strpos($root, DIRECTORY_SEPARATOR, 1)))); #  /foro
define('__SCHOOL_ACRONYM', str_replace('/', '', __SCHOOL_URL)); # demo


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
define('__DEFAULT_LOGO_REGIWEB', str_replace('/', DIRECTORY_SEPARATOR, '/images/logo-regiweb.gif'));
define('__DEFAULT_LOGO_SCHOOLSOFT', str_replace('/', DIRECTORY_SEPARATOR, '/images/logo-schoolsoft.gif'));

/* ---------------------------------- Cosey --------------------------------- */
define('__COSEY', false);

/* ---------------------------- Different schools --------------------------- */
define('__ONLY_CBTM__', false);


require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/core/translator.php';

$dotenv = Dotenv::createImmutable(dirname(__DIR__)); // path to your .env file
$dotenv->load();

new Database();

Relation::enforceMorphMap([
    'student' => \App\Models\Student::class,
    'admin' => Admin::class,
]);

$_admin = Admin::primaryAdmin()->first();
$_locale = strtolower($_admin->idioma) ?? LanguageCode::SPANISH->value;

TranslatorFactory::get()->setLocale($_locale);

define('__LANG', $_locale);

date_default_timezone_set(school_config('app.timezone', 'America/Puerto_Rico'));
