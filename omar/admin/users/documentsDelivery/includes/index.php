<?php

require_once '../../../../app.php';

use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;
use Classes\Util;

Session::is_logged();
Server::is_post();

if (isset($_POST['saveDocument'])) {
    $documents = DB::table('docu_entregados')->get();
    $studentSS = $_POST['studentSS'];
    $student = new Student($studentSS);
    $year = $student->info('year');
    foreach ($documents as $index => $document) {
        if (DB::table("docu_estudiantes")->where([['ss', $studentSS], ['codigo', $document->codigo]])->first()) {

            $data = [
                'entrego' => isset($_POST["delivered$index"]) && $_POST["delivered$index"] === 'on' ? 'Si' : '',
                'nap' => isset($_POST["doesntApply$index"]) && $_POST["doesntApply$index"] === 'on' ? 'Si' : '',
                'fecha' => isset($_POST["date$index"]) ? $_POST["date$index"] : '',
                'fesp' => isset($_POST["expirationDate$index"]) ? $_POST["expirationDate$index"] : '',
            ];
            var_dump($data);

            DB::table("docu_estudiantes")->where([['ss', $studentSS], ['codigo', $document->codigo]])->update($data);
            // Util::toJson([
            //     'year' => $year,
            //     'entrego' => $_POST["delivered$index"] === 'on' ? 'Si' : '',
            //     'nap' => $_POST["doesntApply$index"] === 'on' ? 'Si' : '',
            //     'fecha' => $_POST["date$index"],
            //     'fesp' => $_POST["expirationDate$index"],
            // ]);
        } else {
            DB::table("docu_estudiantes")->where([['ss', $studentSS], ['codigo', $document->codigo]])->insert([
                'id' => $student->id,
                'ss' => $studentSS,
                'codigo' => $document->codigo,
                'desc1' => $document->desc1,
                'year' => $year,
                'entrego' => isset($_POST["delivered$index"]) && $_POST["delivered$index"] === 'on' ? 'Si' : '',
                'nap' => isset($_POST["doesntApply$index"]) && $_POST["doesntApply$index"] === 'on' ? 'Si' : '',
                'fecha' => isset($_POST["date$index"]) ? $_POST["date$index"] : '',
                'fesp' => isset($_POST["expirationDate$index"]) ? $_POST["expirationDate$index"] : '',
            ]);
        }
    }
}
