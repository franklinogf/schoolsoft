<?php

use Classes\Route;

require_once '../../app.php';


session_destroy();

Route::redirect();