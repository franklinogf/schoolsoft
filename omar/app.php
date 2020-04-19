<?php
session_start();

if (isset($_SERVER['logged'])) {
   define('__ID', $_SERVER['logged']['user']['id']);
}
/* -------------------------------------------------------------------------- */
/*                              Root directories                              */
/* -------------------------------------------------------------------------- */

define('__ROOT_SCHOOL', __DIR__);
define('__ROOT', dirname(__DIR__));
define('__ROOT_URL', substr($_SERVER['PHP_SELF'], 0, - (strlen($_SERVER['SCRIPT_FILENAME']) - strlen(__ROOT_SCHOOL))));



/* -------------------------------------------------------------------------- */
/*                         @Class DataBase Information                        */
/* -------------------------------------------------------------------------- */

define('__HOST', "localhost");
define('__USERNAME', "root");
define('__PASSWORD', "");
define('__DB_NAME', "demo");

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
 * Hawaii no DST ..... Pacific/Honolulu */

date_default_timezone_set("America/Puerto_Rico");


include __ROOT . '/autoload.php';
