<?php

$directory = __DIR__ . "/attachments";

if (!is_dir($directory)) {
    mkdir($directory, 0777, true);
}

$letterNumber = $_POST['carta'] ?? null;

if (!$letterNumber || !is_numeric($letterNumber) || $letterNumber < 1) {
    die("Invalid letter number.");
}

require "carta{$letterNumber}.php";
