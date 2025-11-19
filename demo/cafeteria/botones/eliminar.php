<?php

use App\Models\CafeteriaButton;

require_once __DIR__ . '/../../app.php';

$id = $_POST['id'];

CafeteriaButton::find($id)->delete();

header("Location: index.php");
