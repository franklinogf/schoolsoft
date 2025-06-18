<?php
use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Email;

require_once '../../app.php';

$school = new School();
$year = $school->year();
$date = $_REQUEST['date'];

$realizedPurchases = DB::table('compra_cafeteria')
->where([['fecha', $date], ['year', $year]])
->whereRaw('AND (tdp = 3 OR tdp = 4)')
->get();



$mail = new Email();

foreach ($realizedPurchases as $purchase) {

    $id_compra = $purchase->id;
    $estudiante = "$purchase->nombre $purchase->apellido";

    $correoTitulo = "Recibo de compra #{$id_compra}";
    $correoMensaje = "Recibo de compra del estudiante {$estudiante} el {$date}";

    $mensaje = "<center><h1>{$correoTitulo}</h1></center>\n\n";
    $mensaje .= $correoMensaje;   
   
    $subject = "Recibo de compra #$id_compra";
   

    require 'info_recibo.php';   
   
    $parent = DB::table('madre')
    ->select('madre, padre, email_m, email_p, cel_com_m, cel_com_p, cel_m, cel_p')
    ->where('id', $id_estudiante)
    ->first();
$to = [];
    if ($parent->email_m != '') {
        $to[] = $parent->email_m;
    }

    if ($parent->email_p != '') {
        $to[] = $parent->email_p;
    }
    $mail->send(
         to: $to,
        subject: $subject,
        message: $mensaje,
        attachments:[['content' => $pdfdoc, 'filename' => "Recibo {$date}.pdf"]]
    );    
 
}

