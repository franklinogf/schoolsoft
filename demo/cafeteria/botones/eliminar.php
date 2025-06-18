<?php

use Classes\DataBase\DB;

require_once '../../app.php';

$id = $_POST['id'];

DB::table('T_cafeteria')->where('id', $id)->delete();

header("Location: index.php");