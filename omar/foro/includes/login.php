<?php


use Classes\Login;
use Classes\Route;
include '../../app.php';
// var_dump($_POST);
if($_SERVER["REQUEST_METHOD"] == 'POST'){

   Login::login($_POST,'foro');

}else{
   Route::error();
}