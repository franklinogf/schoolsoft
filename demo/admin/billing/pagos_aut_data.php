<?php
require_once __DIR__ . '/../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ["Pasar balances", "Pass balances"],
    ['Pantalla para Pasar los Balances', 'Balance Transfer Screen'],
    ['Código', 'Code'],
    ['a', 'to'],
    ['Transferir', 'Transfer'],
    ['Debe de llenar todos los campos', 'You must fill all fields'],
    ['Por Selección', 'By Selection'],
    ['Descripci&oacute;n', 'Description'],
    ['Pasar Todo', 'Pass Everything'],
    ['Por Selección', 'By Selection'],
    ['Costos', 'Costs'],
    ['Opciones', 'Options'],
    ['Agosto', 'August'],
    ['Septiembre', 'September'],
    ['Octubre', 'October'],
    ['Noviembre', 'November'],
    ['Diciembre', 'December'],
    ['Enero', 'January'],
    ['Febrero', 'February'],
    ['Marzo', 'March'],
    ['Abril', 'Abril'],
    ['Mayo', 'May'],
    ['Junio', 'June'],
    ['Julio', 'July'],
    ['Grados', 'Grades'],
    ['Matri/Junio', 'Regis/June'],
    ['Por Familia', 'Per Family'],
    ['Estu. Nuevo', 'New Student'],
    ['Todos', 'All'],
    ['Selecci&oacute;n', 'Selection'],
    ['Si', 'Yes'],
    ['No', 'No'],
    ['Cambiar estado', 'Change Status'],
    ['Guardar cambios', 'Save Changes'],
    ['E', 'I'],
    ['Estás seguro que desea eliminar el costo?', 'Are you sure you want to eliminate the cost?'],
]);

$school = new School(Session::id());
$year = $school->year();
//require_once '../../control.php';
//$school = mysql_fetch_object(mysql_query("SELECT * from colegio where usuario = 'administrador'"));
$months = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

if (isset($_POST['searchPost'])) {
    $postId = $_POST['searchPost'];

    //    $posts =  mysql_query("SELECT * FROM posteos WHERE id = $postId");
    $posts = DB::table('posteos')->where([
        ['id', $postId],
    ])->first();

    //    echo json_encode(mysql_fetch_assoc($posts));
    echo json_encode($posts);
} else if (isset($_POST['makePayment'])) {
    $trxID = $_POST['trxID'];
    $month = date("m");

    if ($_POST['makePayment'] === 'Success') {
        $desc = "
        <h2>Se ha realizado el pago del mes de {$months[$month - 1]}:</h2>
        ";
        //        $r = mysql_query("SELECT * FROM `posteos_detalles` WHERE posteoId = '$trxID'");
        $r = DB::table('posteos_detalles')->whereRaw("posteoId=$trxID")
            ->orderBy('posteoId')->get();
        //        while ($details = mysql_fetch_object($r)) {
        foreach ($details as $r) {
            //            $student = mysql_fetch_object(mysql_query("SELECT * FROM year where mt = '$details->estudianteId'"));
            $student = DB::table('year')->whereRaw("mt = $details->estudianteId")
                ->orderBy('id')->first();
            //            $presupuesto = mysql_fetch_object(mysql_query("SELECT * FROM presupuesto where codigo = '$details->presupuesto' AND year = '$year'"));
            $presupuesto = DB::table('presupuesto')->whereRaw("codigo = $details->presupuesto AND year = '$year'")
                ->orderBy('id')->first();
            $desc .= "<p>{$student->nombre} {$student->apellidos}: pago de {$presupuesto->descripcion} un total de {$details->cantidad}</p>";
        }

        $fullName = $_POST['customerName'];
        $customerEmail = $_POST['customerEmail'];
        $account = $_POST['accountID'];
        $description = $_POST['trxDescription'];
        $authNumber = $_POST['authNumber'];
        $referenceNumber = $_POST['refNumber'];
        $paymentMethod = $_POST['paymentMethod'];
        $cardLast4Digits = $paymentMethod === 'tarjeta' ? substr($_POST['cardNumber'], -4) : $_POST['bankAccount'];
        $zip = $_POST['zipcode'];
        $totalAmount = $_POST['trxAmount'];
        $dateTime = date('Y-m-d H:i:s');
        //        mysql_query("INSERT INTO posteos_historial
        // (,,,,``,,) VALUES
        // (,','',','','','')");

        DB::table('posteos_historial')->insert([
            'posteoId' => $trxID,
            'mensaje' => $_POST['makePayment'],
            'mes' => $month,
            'descripcion' => $desc,
            'dateTime' => $dateTime,
            'authNumber' => $authNumber,
            'referenceNumber' => $referenceNumber,
        ]);

        require_once 'pagos_aut_sendEmail.php';
    } else {
        //        mysql_query("INSERT INTO posteos_historial
        // (posteoId,mensaje,mes) VALUES($trxID,'{$_POST['makePayment']}','$month')");

        DB::table('posteos_historial')->insert([
            'posteoId' => $trxID,
            'mensaje' => $_POST['makePayment'],
            'mes' => $month,
        ]);
    }
} else if (isset($_POST['sendEmail'])) {
    $trxID = $_POST['trxID'];

    //    $msj = mysql_fetch_object(mysql_query("SELECT * FROM posteos_historial WHERE posteoId = '$trxID' and mensaje = 'Success'"));
    $msj = DB::table('posteos_historial')->whereRaw("posteoId = '$trxID' and mensaje = 'Success'")
        ->orderBy('posteoId')->first();
    $desc = $msj->descripcion;

    $fullName = $_POST['customerName'];
    $customerEmail = $_POST['customerEmail'];
    $account = $_POST['accountID'];
    $description = $_POST['trxDescription'];
    $authNumber = $msj->authNumber;
    $referenceNumber = $msj->referenceNumber;
    $paymentMethod = $_POST['paymentMethod'];
    $cardLast4Digits = $paymentMethod === 'tarjeta' ? substr($_POST['cardNumber'], -4) : $_POST['bankAccount'];
    $zip = $_POST['zipcode'];
    $totalAmount = $_POST['trxAmount'];
    $dateTime = $msj->dateTime;

    require_once 'pagos_aut_sendEmail.php';
}
