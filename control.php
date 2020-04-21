<?php

$con = mysql_connect("localhost","admin_democol","Amc123456");
$db = mysql_connect("localhost","admin_democol","Amc123456");
mysql_connect("localhost","admin_democol","Amc123456");
mysql_select_db("admin_democol");
$dbh=mysql_connect ("localhost", "admin_democol", "Amc123456") or die ('problema conectando porque :'.mysql_error());

if (basename(dirname(getcwd())) === 'foro'){
  include_once 'foro/control-config.php';
}
date_default_timezone_set('America/Puerto_Rico');