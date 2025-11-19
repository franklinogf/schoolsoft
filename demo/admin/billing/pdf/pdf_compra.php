<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();

$lang = new Lang([
    ["Carta de recomendaci&#65533;n", "Letter of recommendation"],
    ['A quien pueda interesar: ', 'To whom it May concern:'],
    ['Fecha: ', 'Date: '],
    ['Por este medio se certifica que el/la estudiante ', 'It is hereby certified that the student '],
    [' grado.', ' grade.'],



]);

$school = new School(Session::id());
$year = $school->info('year2');
$studentsSS = $_POST['students'];

class nPDF extends PDF
{
    function Header()
    {
        global $year;
        global $lang;
        parent::header();
    }
}

$pdf = new nPDF("P");
$pdf->SetTitle($lang->translation('Lista de deudores 30, 60, 90 ') . '/ ' . $year);
$pdf->AliasNbPages();
$pdf->Fill();

$pdf->SetFont('Arial', 'B', 15);
foreach ($studentsSS as $ss) {
    $transactions = $sort = [];

    $student = DB::table('year')->where([
        ['ss', $ss],
        ['year', $year]
    ])->orderBy('id')->first();

    $deposits = DB::table('depositos')->where([
        ['ss', $ss],
        ['year', $year]
    ])->orderBy('fecha, hora')->get();

    $buys = DB::table('compra_cafeteria')->where([
        ['ss', $ss],
        ['year', $year]
    ])->orderBy('fecha')->get();


    $count = 1;
    foreach ($deposits as $deposit) {
        $transactions[] = [
            'type' => 'deposit',
            'date' => $deposit->fecha,
            'order' => $count,
            'description' => 'Deposito',
            'amount' => $deposit->cantidad
        ];
        $count++;
    }

    $count = 1;
    foreach ($buys as $buy) {
        $buysDetails = DB::table('compra_cafeteria_detalle')->where([
            ['id_compra', $buy->id]
        ])->orderBy('id_compra')->get();

        foreach ($buysDetails as $buysDetail) {
            $transactions[] = [
                'type' => 'buy',
                'date' => $buy->fecha,
                'order' => $count,
                'id' => $buy->id,
                'description' => $buysDetail->descripcion,
                'amount' => $buysDetail->precio
            ];
            $count++;
        }
    }

    foreach ($transactions as $key => $part) {
        $sort[$key] = strtotime($part['date']);
    }
    array_multisort($sort, SORT_ASC, $transactions);

    $transactions = json_decode(json_encode($transactions));
    $pdf->AddPage();
    $pdf->Cell(0, 5, 'Analisis de compra', 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 11);

    $pdf->Cell(80, 5, 'Nombre', 1, 0, 'C', true);
    $pdf->Cell(30, 5, 'ID', 1, 0, 'C', true);
    $pdf->Cell(30, 5, 'Grado', 1, 0, 'C', true);
    $pdf->Cell(30, 5, 'Balance', 1, 1, 'C', true);
    $ape = $student->apellidos ?? '';
    $nom = $student->nombre ?? '';
    $pdf->Cell(80, 5, "$ape $nom", 1, 0, 'C');
    $pdf->Cell(30, 5, $student->id ?? '', 1, 0, 'C');
    $pdf->Cell(30, 5, $student->grado ?? '', 1, 0, 'C');
    $pdf->Cell(30, 5, $student->cantidad ?? 0, 1, 1, 'C');

    $pdf->Ln(5);
    $pdf->Cell(30, 5, 'Fecha', 1, 0, 'C', true);
    $pdf->Cell(15, 5, 'Orden', 1, 0, 'C', true);
    $pdf->Cell(30, 5, utf8_encode('# transacción'), 1, 0, 'C', true);
    $pdf->Cell(45, 5, utf8_encode('Descripción'), 1, 0, 'C', true);
    $pdf->Cell(20, 5, 'Deposito', 1, 0, 'C', true);
    $pdf->Cell(20, 5, 'Compra', 1, 0, 'C', true);
    $pdf->Cell(35, 5, 'Running balance', 1, 1, 'C', true);

    $pdf->Cell(30, 5, '', 1);
    $pdf->Cell(15, 5, '', 1);
    $pdf->Cell(30, 5, '', 1);
    $pdf->Cell(45, 5, 'Balance anterior', 1, 0, 'R');
    $pdf->Cell(20, 5, number_format($student->balance_a ?? 0, 2), 1, 0, 'R');
    $pdf->Cell(20, 5, '', 1);
    if ($student->balance_a ?? 0 < 0) $pdf->SetTextColor(255, 0, 0);
    $pdf->Cell(35, 5, number_format($student->balance_a ?? 0, 2), 1, 1, 'R');
    $pdf->SetTextColor(0);
    $depositsTotal = $balance = $student->balance_a ?? 0;
    $buysTotal = 0;
    $pdf->SetFont('Arial', '', 10);
    foreach ($transactions as $transaction) {
        $balance = $transaction->type === 'buy' ? $balance - $transaction->amount : $balance + $transaction->amount;
        $depositsTotal += $transaction->type === 'deposit' ? $transaction->amount : 0;
        $buysTotal += $transaction->type === 'buy' ? $transaction->amount : 0;

        $pdf->Cell(30, 5, $transaction->date, 1, 0, 'C');
        $pdf->Cell(15, 5, $transaction->order, 1, 0, 'C');
        $pdf->Cell(30, 5, $transaction->type === 'buy' ? $transaction->id : '', 1, 0, 'C');
        $pdf->Cell(45, 5, $transaction->description, 1);
        $pdf->Cell(20, 5, $transaction->type === 'deposit' ? '$' . number_format($transaction->amount, 2) : '', 1, 0, 'R');
        $pdf->Cell(20, 5, $transaction->type === 'buy' ? '$' . number_format($transaction->amount, 2) : '', 1, 0, 'R');
        if ($balance < 0) $pdf->SetTextColor(255, 0, 0);
        $pdf->Cell(35, 5, '$' . number_format($balance, 2), 1, 1, 'R');
        $pdf->SetTextColor(0);
    }
    $pdf->Ln(10);
    $pdf->Cell(40, 5, 'Total deposito', 1, 0, 'C', true);
    $pdf->Cell(40, 5, 'Total compra', 1, 0, 'C', true);
    $pdf->Cell(40, 5, 'Balance total', 1, 1, 'C', true);
    $pdf->Cell(40, 5, '$' . number_format($depositsTotal, 2), 1, 0, 'R');
    $pdf->Cell(40, 5, '$' . number_format($buysTotal, 2), 1, 0, 'R');
    if (($depositsTotal - $buysTotal) < 0) $pdf->SetTextColor(255, 0, 0);
    $pdf->Cell(40, 5, '$' . number_format($depositsTotal - $buysTotal, 2), 1, 1, 'R');
    $pdf->SetTextColor(0);
}

$pdf->Output();
