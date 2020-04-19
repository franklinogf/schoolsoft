<?php

spl_autoload_register(function ($className) {

  $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);

  $fullPath = __ROOT . DIRECTORY_SEPARATOR . $className . '.php';

  if (!file_exists($fullPath)) {
    return false;
  }
  require_once $fullPath;
});
