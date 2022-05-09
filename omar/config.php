<?php
/* -------------------------------------------------------------------------- */
/*                                   Config                                   */
/* -------------------------------------------------------------------------- */

// Language: es = Spanish, en = English
define('__LANG', 'es');
define('__DEFAULT_LOGO', 'logo.gif');
// regular is 72 x 72
define('__LOGO_SIZE', 100);
define('__HOME_LOGO_SIZE', 200);
define('__LOGIN_LOGO_SIZE', 300);
/* ----------------------------------- PDF ---------------------------------- */

// RGB $pdf->SetFillColor(red,green,blue)
define('__PDF_FILL_COLOR_RED', 89);
define('__PDF_FILL_COLOR_GREEN', 171);
define('__PDF_FILL_COLOR_BLUE', 227);

define('__PDF_LOGO_SIZE', 30);

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