<?php
require_once '../../../../app.php';

use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;

Session::is_logged();
Server::is_post();
if (isset($_POST['unenroll'])) {
    $code = $_POST['code'];
    $date = $_POST['date'];
    $studentSS = $_POST['unenroll'];
    $student = new Student($studentSS);
    $year = $student->info('year');
    if ($code !== '') {
        $thisCode = DB::table('codigo_bajas')->select('id')->where(['codigo', $code])->first();
        DB::table('year')->where([
            ['year', $year],
            ['ss', $studentSS]
        ])->update([
            'id3' => $thisCode->id,
            'codigobaja' => $code,
            'fecha_baja' => $date
        ]);

        DB::table('pagos')->where([
            ['year', $year],
            ['ss', $studentSS]
        ])->update([
            'baja' => $code
        ]);
    } else {
        DB::table('year')->where([
            ['year', $year],
            ['ss', $studentSS]
        ])->update([
            'id3' => '',
            'codigobaja' => '',
            'fecha_baja' => ''
        ]);
        DB::table('pagos')->where([
            ['year', $year],
            ['ss', $studentSS],
            ['fecha_d', '>=', date('Y-m-d')]
        ])->update([
            'baja' => $code
        ]);
    }

    $tables = ['padres', 'padres2', 'padres3', 'padres4', 'padres5', 'padres6', 'asisdia', 'asispp'];
    foreach ($tables as $table) {
        DB::table($table)->where([
            ['year', $year],
            ['ss', $studentSS]
        ])->update([
            'baja' => $code
        ]);
    }
}
