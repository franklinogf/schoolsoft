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

define('__ROOT_SCHOOL',str_replace('/',DIRECTORY_SEPARATOR,__DIR__));
define('__ROOT', str_replace('/',DIRECTORY_SEPARATOR,dirname(__DIR__)));
define('__ROOT_URL', substr($_SERVER['PHP_SELF'], 0, - (strlen($_SERVER['SCRIPT_FILENAME']) - strlen(__ROOT_SCHOOL))));
$root = str_replace(__ROOT_SCHOOL,'', str_replace('/',DIRECTORY_SEPARATOR,$_SERVER['SCRIPT_FILENAME'])); 
define('__SUB_ROOT_URL', str_replace('\\','/',substr($root,0,strpos($root,DIRECTORY_SEPARATOR,1))));


/* -------------------------------------------------------------------------- */
/*                              Files Directories                             */
/* -------------------------------------------------------------------------- */

define('__TEACHER_PROFILE_PICTURE', str_replace('/',DIRECTORY_SEPARATOR,__ROOT_URL.'/pictures/teachers/'));
define('__STUDENT_PROFILE_PICTURE', str_replace('/',DIRECTORY_SEPARATOR,__ROOT_URL.'/pictures/students/'));
define('__NO_PROFILE_PICTURE', str_replace('/',DIRECTORY_SEPARATOR,'/images/none.jpg'));
define('__LOGO_PATH', str_replace('/',DIRECTORY_SEPARATOR,__ROOT_URL.'/logo/'));


/* -------------------------------------------------------------------------- */
/*                                   Config                                   */
/* -------------------------------------------------------------------------- */

// lenguaje a usar
define('__LANG', 'es');
define('__DEFAULT_LOGO', 'logo.gif');

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
