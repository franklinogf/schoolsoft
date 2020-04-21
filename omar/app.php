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

define('__ROOT_SCHOOL', __DIR__);
define('__ROOT', dirname(__DIR__));
define('__ROOT_URL', substr($_SERVER['PHP_SELF'], 0, - (strlen($_SERVER['SCRIPT_FILENAME']) - strlen(__ROOT_SCHOOL))));


/* -------------------------------------------------------------------------- */
/*                              Files Directories                             */
/* -------------------------------------------------------------------------- */

define('__teacherProfilePicture', str_replace('/',DIRECTORY_SEPARATOR,__ROOT_URL.'/pictures/teachers/'));
define('__studentProfilePicture', str_replace('/',DIRECTORY_SEPARATOR,__ROOT_URL.'/pictures/students/'));
define('__noProfilePicture', str_replace('/',DIRECTORY_SEPARATOR,'/images/none.jpg'));
define('__logo', str_replace('/',DIRECTORY_SEPARATOR,__ROOT_URL.'/logo/'));


/* -------------------------------------------------------------------------- */
/*                                   Config                                   */
/* -------------------------------------------------------------------------- */

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
