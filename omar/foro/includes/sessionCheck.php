<?php
require_once '../../app.php';

use Classes\Session;

if (!Session::get('logged')) {
    echo "Expired";
} else {
    echo "Ok";
}
