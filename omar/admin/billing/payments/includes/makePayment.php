<?php
require_once '../.././../../app.php';
use Classes\Route;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Session;

Session::is_logged();


if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $school = new School(Session::get('usuario'));
    $year = $school->year();
    $paymentType = $_POST['paymentType'];
    $chkNum = $_POST['chkNum'] ?: '';
    $receiptNum = $_POST['receiptNum'];
    $sameReceipt = $_POST['receiptType'] === '2' ? true : false;
    $bash = $_POST['bash'];
    $paymentDate = $_POST['paymentDate'];
    $monthToPay = $_POST['monthToPay'];
    $accountId = $_POST['accountId'];
    $date = date("Y-m-d");
    $time = date("H:i:s");

    function receiptNumber($receiptNumber, $sameReceipt, $accountId, $year)
    {
        // si no esta activado lo de los recibos manuales, entonces crear uno automaticamente, de lo contrario utilizar el recibo del input
        if (!isset($receiptNumber)) {
            // utilizar el mismo recibo
            if ($sameReceipt) {
                $recQuery = DB::table('pagos')->select('MAX(rec) as rec')->where([
                    ['id', $accountId],
                    ['year', $year],
                    ['deuda', '0000-00-00'],
                ])->first();
                if ($recQuery->rec) {
                    return $recQuery->rec;
                }
            }

            $recQuery = DB::table('pagos')->select('MAX(rec) as rec')->where([
                ['year', $year],
                ['deuda', '0000-00-00'],
            ])->first();

            return $recQuery->rec + 1;
        }

        return $receiptNumber;

    }

    $receipt = receiptNumber($receiptNum, $sameReceipt, $accountId, $year);

    if ($_POST['paymentMode'] === 'completo') {
        // buscar los pagos por el mes seleccionado
        $paymentsQuery = DB::table('pagos')->where([
            ['id', $accountId],
            ['year', $year],
            ['baja', '']
        ])->whereRaw("AND MONTH(fecha_d) = ?", [$monthToPay])->get();
        // crear un array con todos los pagos con la informacion que necesito
        $debtData = [];
        foreach ($paymentsQuery as $row) {
            if ($row->fecha_d !== '0000-00-00' && $row->deuda > 0) {
                $debtData["{$row->codigo}-{$row->grado}"]['id'] = $row->id;
                $debtData["{$row->codigo}-{$row->grado}"]['debt_date'] = $row->fecha_d;
                $debtData["{$row->codigo}-{$row->grado}"]['code'] = $row->codigo;
                $debtData["{$row->codigo}-{$row->grado}"]['grade'] = $row->grado;
                $debtData["{$row->codigo}-{$row->grado}"]['ss'] = $row->ss;
                $debtData["{$row->codigo}-{$row->grado}"]['full_name'] = $row->nombre;
                $debtData["{$row->codigo}-{$row->grado}"]['desc'] = $row->desc1;
            }
            $debtData["{$row->codigo}-{$row->grado}"]['debts'] += floatval($row->deuda);
            $debtData["{$row->codigo}-{$row->grado}"]['payments'] += floatval($row->pago);
        }

        foreach ($debtData as $debt) {
            $totalPayment = $debt['debts'] - $debt['payments'];
            if ($totalPayment > 0) {
                $data = [
                    'id' => $debt['id'],
                    'nombre' => $debt['full_name'],
                    'desc1' => $debt['desc'],
                    'fecha_d' => $debt['debt_date'],
                    'year' => $year,
                    'codigo' => $debt['code'],
                    'ss' => $debt['ss'],
                    'grado' => $debt['grade'],
                    'fecha_p' => $date,
                    'pago' => $totalPayment,
                    'tdp' => $paymentType,
                    'nuchk' => $chkNum,
                    'rec' => $receipt,
                    'id2' => $debt['id'],
                    'bash' => $bash,
                    'caja' => $school->info('caja'),
                    'usuario' => $school->info('usuario'),
                    'hora' => $time,
                    'fecha2' => $date
                ];

                DB::table('pagos')->insert($data);
            }
        }


    } else {
        $paymentDebtCodes = $_POST['parcialPaymentDebtsCodes'];
        $paymentDebtsAmounts = $_POST['parcialPaymentDebtsAmounts'];
        $paymentDebtsGrades = $_POST['parcialPaymentDebtsGrades'];

        foreach ($paymentDebtCodes as $index => $code) {
            $totalPayment = $paymentDebtsAmounts[$index];
            $grade = $paymentDebtsGrades[$index];
            $debt = [];
            //buscar el pago por codigo y el mes
            $debtData = DB::table('pagos')->where([
                ['id', $accountId],
                ['year', $year],
                ['baja', ''],
                ['grado', $grade],
                ['codigo', $code]
            ])->whereRaw("AND MONTH(fecha_d) = ?", [$monthToPay])->first();

            $debt['id'] = $debtData->id;
            $debt['debt_date'] = $debtData->fecha_d;
            $debt['code'] = $debtData->codigo;
            $debt['grade'] = $grade;
            $debt['ss'] = $debtData->ss;
            $debt['full_name'] = $debtData->nombre;
            $debt['desc'] = $debtData->desc1;

            if ($totalPayment > 0) {

                $data = [
                    'id' => $debt['id'],
                    'nombre' => $debt['full_name'],
                    'desc1' => $debt['desc'],
                    'fecha_d' => $debt['debt_date'],
                    'year' => $year,
                    'codigo' => $debt['code'],
                    'ss' => $debt['ss'],
                    'grado' => $debt['grade'],
                    'fecha_p' => $date,
                    'pago' => $totalPayment,
                    'tdp' => $paymentType,
                    'nuchk' => $chkNum,
                    'rec' => $receipt,
                    'id2' => $debt['id'],
                    'bash' => $bash,
                    'caja' => $school->info('caja'),
                    'usuario' => $school->info('usuario'),
                    'hora' => $time,
                    'fecha2' => $date
                ];
                DB::table('pagos')->insert($data);
            }

        }

    }

    Route::redirect("/billing/payments?accountId={$accountId}&month={$monthToPay}");


} else {
    Route::error();
}
