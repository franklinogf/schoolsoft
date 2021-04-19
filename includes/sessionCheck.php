<?php
session_start();


if (!isset($_SESSION['logged'])) {   
    echo "Expired";
} else {
    echo "Ok";
}
