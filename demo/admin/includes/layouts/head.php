<?php

use Classes\Route;
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php Route::includeFile('/admin/includes/layouts/header.php'); ?>
</head>

<body>
    <?php Route::includeFile('/admin/includes/layouts/menu.php'); ?>