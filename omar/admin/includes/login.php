<?php

use Classes\Login;
use Classes\Route;

include '../../app.php';


if ($_SERVER["REQUEST_METHOD"] == 'POST') {

   Login::login($_POST, 'admin');
} else {
   Route::error();
}
