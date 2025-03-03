<?php
if ($_POST['tarjeta'] == '1') {
    require_once('TarjetaNotas1.php');
}
if ($_POST['tarjeta'] == '2') {
    require_once('TarjetaNotas2.php');
}
if ($_POST['tarjeta'] == '3') {
    require_once('TarjetaNotas3.php');
}
if ($_POST['tarjeta'] == '7') {
    require_once('TarjetaNotas7.php');
}
if ($_POST['tarjeta'] == '14') {
    require_once('TarjetaNotas14.php');
}
