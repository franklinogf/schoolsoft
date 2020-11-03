<?php
require_once '../../app.php';

$_class = $_POST['class'];
$_trimester = $_POST['tri'];
$_report = $_POST['tra'];

switch ($_report) {
    case 'Notas':
       require_once 'pdf/report1.php';
        break;
    
    case 'Notas-2':
        require_once 'pdf/report2.php';
    break;

    case 'Trab-Diarios':
        $_title = "Trabajos Diaros";
        $_table = "padres2";
        require_once 'pdf/report3.php';
    break;
    
    case 'Trab-Libreta':
        $_title = "Trabajos de Libreta";
        $_table = "padres3";
        require_once 'pdf/report3.php';
    break;

    case 'Pruebas-Cortas':
        $_title = "Pruebas Cortas";
        $_table = "padres4";
        require_once 'pdf/report3.php';
    break;

    case 'Semestre-1':
        $_title = "Semestre 1";
        $_table = "padres";
        require_once 'pdf/report4.php';
    break;

    case 'Semestre-2':
        $_title = "Semestre 2";
        $_table = "padres";
        require_once 'pdf/report4.php';
    break;

    case 'V-Nota':
    $_title = "Nota de verano";
    $_table = "padres";
    require_once 'pdf/report4.php';

    case 'Finales':       
        require_once 'pdf/report5.php';
    break;

    case 'Sem-Por-1':
        require_once 'pdf/report6.php';
    break;

    case 'Notas-Porciento':
        require_once 'pdf/report7.php';
    break;

    case 'Notas-P-Decimal':
        require_once 'pdf/report8.php';
    break;

   
break;
}
exit;