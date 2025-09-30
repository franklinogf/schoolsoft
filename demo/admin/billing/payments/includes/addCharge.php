<?php

use App\Models\Admin;
use App\Models\Student;
use Classes\Route;
use Classes\DataBase\DB;
use Classes\Session;

// use Classes\Controllers\School;

require_once '../../../../app.php';

if ($_SERVER["REQUEST_METHOD"] === 'POST') {

    $school = Admin::user(Session::id())->first();
    $year = $school->year2;
    $year1 = "{$year[0]}{$year[1]}";
    $year2 = "{$year[3]}{$year[4]}";
    $code = $_POST['code'];
    $codeDescription = $_POST['codeDescription'];
    $chargeTo = $_POST['chargeTo'];
    $amount = $_POST['amount'];
    $month = $_POST['month'];

    $student = Student::find($chargeTo);

    if (!isset($_POST['allMonths'])) {
        $date = date("Y-$month-01");
        try {
            $dataToInsert = [
                'id' => $student->id,
                'nombre' => "$student->nombre $student->apellidos",
                'desc1' => $codeDescription,
                'fecha_d' => $date,
                'year' => $year,
                'codigo' => $code,
                'ss' => $student->ss,
                'grado' => $student->grado,
                'deuda' => $amount,
                'add1' => $amount < 0 ? 2 : 1,
            ];
            $mt = DB::table('pagos')->insertGetId($dataToInsert);
            $insertedData = array_merge($dataToInsert, ['mt' => $mt, "month" => $month]);
            echo json_encode(["message" => "Pago insertado con exito", "rows" => [$insertedData]]);
        } catch (\Throwable $th) {
            echo json_encode(["error" => true, "message" => $th->getMessage()]);
        }
    } else {
        $months = [];
        $monthNumber = (int) $month;
        $nextYearMonths = $monthNumber >= 8;
        $lastMonth = $monthNumber <= 6 ? 5 : 12;
        for ($i = $monthNumber; $i <= $lastMonth; $i++) {
            array_push($months, $i < 10 ? "0$i" : strval($i));
        }
        if ($nextYearMonths) {
            for ($i = 1; $i < 6; $i++) {
                array_push($months, "0$i");
            }
        }

        $rows = [];
        try {
            foreach ($months as $month) {
                $date = '20';
                $date .= intval($month) >= 1 && intval($month) <= 5 ? $year2 : $year1;
                $date .= "-$month-01";
                $dataToInsert = [
                    'id' => $student->id,
                    'nombre' => "$student->nombre $student->apellidos",
                    'desc1' => $codeDescription,
                    'fecha_d' => $date,
                    'year' => $year,
                    'codigo' => $code,
                    'ss' => $student->ss,
                    'grado' => $student->grado,
                    'deuda' => $amount,
                ];
                $mt = DB::table('pagos')->insertGetId($dataToInsert);
                $rows[] = $insertedData = array_merge($dataToInsert, ['mt' => $mt, "month" => $month]);
            }
            echo json_encode(["message" => "Pagos insertados con exito", "rows" => $rows]);
        } catch (\Throwable $th) {
            echo json_encode(["error" => true, "message" => $th->getMessage()]);
        }
    }


    // Route::redirect("/billing/payments?accountId={$student->id}&month={$month}");


} else {
    Route::error();
}
