<?php
require_once __DIR__ . '/../../../app.php';

use Classes\DataBase\DB;
use Classes\Session;

echo $examId = $_POST['examId'];

DB::table("T_examenes_terminados")->where([
    ["id_examen", $examId],
    ["id_estudiante", Session::id()]
])->delete();
DB::table("T_examen_terminado_fyv")->where([
    ["id_examen", $examId],
    ["id_estudiante", Session::id()]
])->delete();
DB::table("T_examen_terminado_linea")->where([
    ["id_examen", $examId],
    ["id_estudiante", Session::id()]
])->delete();
DB::table("T_examen_terminado_parea")->where([
    ["id_examen", $examId],
    ["id_estudiante", Session::id()]
])->delete();
DB::table("T_examen_terminado_pregunta")->where([
    ["id_examen", $examId],
    ["id_estudiante", Session::id()]
])->delete();
DB::table("T_examen_terminado_selec")->where([
    ["id_examen", $examId],
    ["id_estudiante", Session::id()]
])->delete();
