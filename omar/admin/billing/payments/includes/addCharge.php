<?php
use Classes\Route;
use Classes\DataBase\DB;
use Classes\Controllers\Student;
use Classes\Controllers\School;

require_once '../../../../app.php';

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    // echo '<pre>';
    // var_dump($_POST);
    // echo '</pre>';
    // exit;
    $school = new School();
    $year = $school->year();
    $code = $_POST['code'];
    $codeDescription = $_POST['codeDescription'];
    $chargeTo = $_POST['chargeTo'];
    $amount = $_POST['amount'];
    $month = $_POST['month'];

    $student = new Student($chargeTo);

    if (!isset($_POST['allMonths'])) {
        $date = date("Y-$month-01");
        DB::table('pagos')->insert([
            'id' => $student->id,
            'nombre' => "$student->nombre $student->apellidos",
            'desc1' => $codeDescription,
            'fecha_d' => $date,
            'year' => $year,
            'codigo' => $code,
            'ss' => $student->ss,
            'grado' => $student->grado,
            'deuda' => $amount,
        ]);

    } else {
        $months = [];
        $monthNumber = (int) $month;
        $nextYearMonths = $monthNumber >= 8;
        $lastMonth = $monthNumber <= 6 ? 6 : 12;
        for ($i = $monthNumber; $i <= $lastMonth; $i++) {
            array_push($months, $i < 10 ? "0$i" : "$i");
        }
        if ($nextYearMonths) {
            for ($i = 1; $i <= 6; $i++) {
                array_push($months, "0$i");
            }
        }
        foreach ($months as $month) {
            $date = date("Y-$month-01");
            DB::table('pagos')->insert([
                'id' => $student->id,
                'nombre' => "$student->nombre $student->apellidos",
                'desc1' => $codeDescription,
                'fecha_d' => $date,
                'year' => $year,
                'codigo' => $code,
                'ss' => $student->ss,
                'grado' => $student->grado,
                'deuda' => $amount,
            ]);
        }

    }


    Route::redirect("/billing/payments?accountId={$student->id}&month={$month}");


} else {
    Route::error();
}
