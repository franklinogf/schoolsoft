<?php

use App\Models\Admin;
use App\Models\CafeteriaOrder;
use Classes\Email;
use Classes\PDF;

require_once '../../app.php';

$school = Admin::primaryAdmin();
$year = $school->year;
$date = $_REQUEST['date'];

$count = 0;

$realizedPurchases = CafeteriaOrder::query()
    ->with(['items', 'buyer.family'])
    ->whereDate('fecha', $date)
    ->whereIn('tdp', [3, 4])
    ->get();

foreach ($realizedPurchases as $purchase) {

    $id_compra = $purchase->id;

    $correoTitulo = "Recibo de compra #{$id_compra}";
    $correoMensaje = "Recibo de compra del estudiante {$purchase->buyer->full_name} el {$date}";

    $mensaje = "<center><h1>{$correoTitulo}</h1></center>\n\n";
    $mensaje .= $correoMensaje;

    $subject = "Recibo de compra #$id_compra";


    $pdf = new PDF();
    $pdf->SetAutoPageBreak(true, 5);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->Fill();

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(30, 10, 'RECIBO DE COMPRA', 0, 1, 'C');
    $pdf->Ln(8);


    $pdf->SetFont('Times', 'B', 12);

    $pdf->Cell(0, 5, "{$purchase->buyer->full_name} {$purchase->buyer->grado}", 0, 1);
    $pdf->Cell(0, 5, "Recibo #{$id_compra}", 0, 1, 'R');
    $pdf->Cell(70, 5, 'Artículo', 1, 0, 'C', true);
    $pdf->Cell(30, 5, 'Fecha', 1, 0, 'C', true);
    $pdf->Cell(30, 5, 'Precio', 1, 1, 'C', true);

    $pdf->SetFont('Times', '', 12);


    foreach ($purchase->items as $item) {
        $pdf->Cell(70, 5, $item->descripcion, 0);
        $pdf->Cell(30, 5, $purchase->fecha, 0, 0, 'C');
        $pdf->Cell(30, 5, $item->precio, 0, 1, 'C');
    }

    $pdf->Ln(5);
    $TOTAL = 0;
    $pdf->Cell(80, 1, '', 'B', 2);
    $pdf->Cell(80, 1, '', 'B', 2);
    $pdf->Ln(2);
    if ($purchase->pago1 > 0) {
        $pdf->Cell(30, 5, 'Pago con deposito');
        $pdf->Cell(20);
        $pdf->Cell(30, 5, '$' . number_format($purchase->pago1, 2), 0, 1);
    }
    if ($purchase->pago2 > 0) {
        $pdf->Cell(30, 5, "Pago con " . $purchase->tdp2);
        $pdf->Cell(20);
        $pdf->Cell(30, 5, '$' . number_format($purchase->pago2, 2), 0, 1);
    }

    $pdf->SetFont('Times', 'B', 12);

    $pdf->Cell(50);
    $pdf->Cell(30, 3, '', 'B', 1);
    $pdf->Cell(30, 5, 'Total', 0, 0);
    $pdf->Cell(20);
    $pdf->Cell(30, 5, '$' . number_format($purchase->total, 2), 0, 1);
    $pdf->Cell(80, 1, '', 'B', 2);
    $pdf->Cell(80, 1, '', 'B', 2);



    $pdf->Cell(0, 5, "Balance disponible $" . $purchase->buyer->cantidad);


    $pdfdoc = $pdf->saveAsAttachment('cafeteria/receipts');
    $pdf->Close();

    $family = $purchase->buyer->family;

    $to = [];
    if ($family->email_m != '') {
        $to[] = $family->email_m;
    }

    if ($family->email_p != '') {
        $to[] = $family->email_p;
    }

    Email::to(['franklinomarflores@gmail.com'])
        ->subject($subject)
        ->body($mensaje)
        ->attach($pdfdoc, "Recibo {$date}.pdf")
        ->queue($family->id);

    $count++;
}

echo json_encode(['count' => $count]);
