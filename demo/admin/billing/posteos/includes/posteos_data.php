<?php
require_once '../../../../app.php';

use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\DataBase\DB;

$school = new School();
$year = $school->year();

if (isset($_POST['addPaymentMethod'])) {

    if ($_POST['type'] === 'save') {
        $data = isset($_POST['dayOfPayment']) ? ["diaDePago" => $_POST['dayOfPayment']] : [];
        if ($_POST['addPaymentMethod'] === 'tarjeta') { // Tarjeta
            $insertData = array_merge($data, [
                'cuenta' => $_POST['account'],
                'year' => $year,
                'email' => $_POST['email'],
                'tipoDePago' => $_POST['addPaymentMethod'],
                'ccNombre' => $_POST['name'],
                'ccNumero' => $_POST['number'],
                'fechaExpiracion' => $_POST['expiration'],
                'cvv' => $_POST['cvv'],
                'ccZip' => $_POST['zip'],
                'formaDePago' => $_POST['paymentType'],

            ]);
        } else { // ACH
            $insertData = array_merge($data, [
                'cuenta' => $_POST['account'],
                'year' => $year,
                'email' => $_POST['email'],
                'tipoDePago' => $_POST['addPaymentMethod'],
                'achNombre' => $_POST['name'],
                'achNumero' => $_POST['number'],
                'tipoCuenta' => $_POST['accountType'],
                'numeroRuta' => $_POST['routeNumber'],
                'achZip' => $_POST['zip'],
                'formaDePago' => $_POST['paymentType'],

            ]);
        }

        echo DB::table('posteos')->insertGetId($insertData);
    } else {
        $paymentDay = isset($_POST['dayOfPayment']) ? $_POST['dayOfPayment'] : 'NULL';
        if ($_POST['addPaymentMethod'] === 'tarjeta') {

            DB::table('posteos')->where('id', $_POST['id'])->update([
                'email' => $_POST['email'],
                'tipoDePago' => $_POST['addPaymentMethod'],
                'ccNombre' => $_POST['name'],
                'ccNumero' => $_POST['number'],
                'fechaExpiracion' => $_POST['expiration'],
                'cvv' => $_POST['cvv'],
                'ccZip' => $_POST['zip'],
                'formaDePago' => $_POST['paymentType'],
                'diaDePago' => $paymentDay,
            ]);
        } else {
            DB::table('posteos')->where('id', $_POST['id'])->update([
                'email' => $_POST['email'],
                'tipoDePago' => $_POST['addPaymentMethod'],
                'achNombre' => $_POST['name'],
                'achNumero' => $_POST['number'],
                'tipoCuenta' => $_POST['accountType'],
                'numeroRuta' => $_POST['routeNumber'],
                'achZip' => $_POST['zip'],
                'formaDePago' => $_POST['paymentType'],
                'diaDePago' => $paymentDay,
            ]);
        }
    }
} else if (isset($_POST['addPost'])) {
    $postId = $_POST['post'];
    $posteoId = $_POST['addPost'];
    $amount = $_POST['amount'];

    if ($postId === '') {
        DB::table('posteos_detalles')->insert([
            'posteoId' => $posteoId,
            'estudianteId' => $_POST['student'],
            'presupuesto' => $_POST['budget'],
            'cantidad' => $amount,
        ]);
    } else {
        DB::table('posteos_detalles')->where('id', $postId)->update([
            'cantidad'  => $amount
        ]);
    }

    $post = DB::table('posteos_detalles')->select('SUM(cantidad) as totalAmount')->where('posteoId', $posteoId)->first();
    DB::table('posteos')->where('id', $posteoId)->update(['total' => $post->totalAmount]);

    $data = [
        'totalAmount' => $post->totalAmount
    ];
    echo json_encode($data);
} else if (isset($_GET['getPosts'])) {
    $postId = $_GET['getPosts'];
    $studentId = $_GET['student'];
    $posts = DB::table('posteos_detalles')->where([
        ['posteoId', $postId],
        ['estudianteId', $studentId],
    ])->orderBy('id', 'DESC')->get();
    if (!$posts || count($posts) === 0) {
        echo json_encode([]);
        return;
    }
    $data = [];
    foreach ($posts as $post) {
        $bud = DB::table('presupuesto')->select('descripcion')->where([['codigo', $post->presupuesto], ['year', $year]])->first();

        $student = new Student($studentId);

        $data[] = [
            "id" => $post->id,
            "name" => "$student->apellidos $student->nombre",
            "description" => $bud->descripcion,
            "budget" => $post->presupuesto,
            "amount" => $post->cantidad,
        ];
    }



    echo json_encode($data);
} else if (isset($_POST['deletePost'])) {
    $postId = $_POST['deletePost'];
    $posteoId = $_POST['posteoId'];
    DB::table('posteos_detalles')->where('id', $postId)->delete();
    $post = DB::table('posteos_detalles')->select('SUM(cantidad) as totalAmount')->where('posteoId', $posteoId)->first();
    DB::table('posteos')->where('id', $posteoId)->update(['total' => $post->totalAmount]);

    $data = [
        'totalAmount' => $post->totalAmount
    ];
    echo json_encode($data);
}
