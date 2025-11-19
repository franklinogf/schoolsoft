<?php
require_once __DIR__ . '/../../app.php';

use Classes\Route;

session_destroy();

Route::redirect();