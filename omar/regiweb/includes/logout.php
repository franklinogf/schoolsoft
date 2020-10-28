<?php
require_once '../../app.php';

use Classes\Route;

session_destroy();

Route::redirect('/regiweb/login.php',false);