<?php
require_once '../../../../app.php';

use Classes\Session;
use Classes\Server;

Server::is_post();
Session::is_logged();

$students = $_POST['students'];


foreach ($students as $ss) {
    echo "$ss <br>";
}
