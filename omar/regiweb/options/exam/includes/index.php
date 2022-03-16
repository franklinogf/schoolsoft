<?php
require_once '../../../../app.php';

use Classes\Controllers\Exam;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Teacher;
use Classes\Util;

Session::is_logged();
Server::is_post();
$teacher = new Teacher(Session::id());

if (isset($_POST['searchExam'])) {
    $examId = $_POST['searchExam'];
    $exam = DB::table('T_examenes')->where('id', $examId)->first();
    echo Util::toJson($exam);
} else if (isset($_POST['changeTitle'])) {
    $examId = $_POST['examId'];
    $title = trim($_POST['changeTitle']);
    if (DB::table('T_examenes')->where('id', $examId)->update([
        'titulo' => $title
    ])) {
        echo "Title changed";
    } else {
        echo "Error changing title";
    }
} else if (isset($_POST['menu'])) {
    $examId = $_POST['menu'];
    $array = [];
    foreach (Exam::$tables as $table) {
        $option = DB::table($table)->where('id_examen', $examId)->get();
        $array[] = ['amount' => sizeof($option), 'value' => array_sum(array_column($option, 'valor'))];
    }

    echo Util::toJson($array);
} else if (isset($_POST['searchOption'])) {
    $examId = $_POST['examId'];
    $data = DB::table(Exam::$tables[$_POST['searchOption'] - 1])->where('id_examen', $examId)->orderBy('id DESC')->get();
    if ($_POST['searchOption'] === '3') {
        $data2 = DB::table(Exam::$allTables[3])->where('id_examen', $examId)->orderBy('id DESC')->get();
        if (!$data2) {
            $data2 = null;
        }
    } else {
        $data2 = null;
    }
    $array = [];
    if ($data) {
        $array = [
            'response' => true,
            'data' => [$data, $data2]
        ];
    } else {
        $array = ['response' => false];
    }
    echo Util::toJson($array);
} else if (isset($_POST['editQuestion'])) {
    $questionId = $_POST['editQuestion'];
    $optionNumber = (int)$_POST['optionNumber'];
    $answerColum = '';
    if ($optionNumber === 1) {
        $answerColum = "respuesta";
    } else if ($optionNumber === 2) {
        $answerColum = "correcta";
    } else if ($optionNumber === 3) {
        $answerColum = "respuesta_c";
    }
    $dataArray = [
        "pregunta" => trim($_POST['question']),
        "valor" => $_POST['value'],
    ];
    if ($optionNumber <= 3) {
        $dataArray[$answerColum] = trim($_POST['answer']);
    }
    if ($optionNumber === 2) {
        for ($i = 1; $i <= 8; $i++) {
            $dataArray["respuesta{$i}"] = trim($_POST["answer{$i}"]);
        }
    } else if ($optionNumber === 4) {
        for ($i = 1; $i <= (int)$_POST['answerAmount']; $i++) {
            $dataArray["respuesta{$i}"] = trim($_POST["answer{$i}"]);
        }
    } else if ($optionNumber === 5) {
        $dataArray["lineas"] = $_POST["amountOfLines"];
    }
    DB::table(Exam::$tables[$optionNumber - 1])->where('id', $questionId)->update($dataArray);
} else if (isset($_POST['addQuestion'])) {
    $examId = $_POST['addQuestion'];
    $optionNumber = (int)$_POST['optionNumber'];
    $answerColum = '';
    if ($optionNumber === 1) {
        $answerColum = "respuesta";
    } else if ($optionNumber === 2) {
        $answerColum = "correcta";
    } else if ($optionNumber === 3) {
        $answerColum = "respuesta_c";
    }
    $dataArray = [
        "id_examen" => $examId,
        "pregunta" => trim($_POST['question']),
        "valor" => $_POST['value'],
    ];
    if ($optionNumber <= 3) {
        $dataArray[$answerColum] = trim($_POST['answer']);
    }
    if ($optionNumber === 2) {
        for ($i = 1; $i <= 8; $i++) {
            $dataArray["respuesta{$i}"] = trim($_POST["answer{$i}"]);
        }
    } else if ($optionNumber === 4) {
        for ($i = 1; $i <= (int)$_POST['answerAmount']; $i++) {
            $dataArray["respuesta{$i}"] = trim($_POST["answer{$i}"]);
        }
    } else if ($optionNumber === 5) {
        $dataArray["lineas"] = $_POST["amountOfLines"];
    }
    DB::table(Exam::$tables[$optionNumber - 1])->insert($dataArray);
} else if (isset($_POST['editAnswer'])) {
    $answerId = $_POST['editAnswer'];
    DB::table(Exam::$allTables[3])->where('id', $answerId)->update(["respuesta" => trim($_POST['answer'])]);
} else if (isset($_POST['addAnswer'])) {
    $examId = $_POST['addAnswer'];
    DB::table(Exam::$allTables[3])->insert([
        "respuesta" => trim($_POST['answer']),
        "id_examen" => $examId,
    ]);
} else if (isset($_POST['deleteQuestion'])) {
    $questionId = $_POST['deleteQuestion'];
    $optionNumber = (int)$_POST['optionNumber'];
    $type = $_POST['type'];
    if ($type === 'question') {
        DB::table(Exam::$tables[$optionNumber - 1])->where('id', $questionId)->delete();
    } else {
        DB::table(Exam::$allTables[3])->where('id', $questionId)->delete();
    }
} else if (isset($_POST['examInfo'])) {
    $examId = $_POST['examInfo'];
    DB::table('T_examenes')->where('id', $examId)->update([
        'curso' => $_POST['grade'],
        'hora' => $_POST['startTime'],
        'hora_final' => $_POST['endTime'],
        'fecha' => $_POST['date'],
        'tiempo' => $_POST['time'],
        'ver_nota' => $_POST['previewGrade'],
        'activo' => $_POST['availability'],
    ]);
} else if (isset($_POST['newExam'])) {
    $date = Util::date();
    $examId = DB::table('T_examenes')->insertGetId([
        'titulo' => trim($_POST['title']),
        'id_maestro' => $teacher->id,
        'curso' => $_POST['grade'],
        'fecha' => $date,
    ]);
    $array = [
        'examId' => $examId,
        'date' => $date,
        'title' => trim($_POST['title']),
        'grade' => $_POST['grade'],
    ];
    echo Util::toJson($array);
} else if (isset($_POST['examTotal'])) {
    $examId = $_POST['examTotal'];
    DB::table('T_examenes')->where('id', $examId)->update([
        'valor' => $_POST['totalValue']
    ]);
} else if (isset($_POST['duplicateExam'])) {
    $examId = $_POST['duplicateExam'];
    $date = Util::date();
    $exam = DB::table('T_examenes')->where('id', $examId)->first();
    $db = new DB();
    $duplicatedExamId = DB::table('T_examenes')->insertGetId([
        'titulo' => trim($_POST['title']),
        'id_maestro' => $teacher->id,
        'curso' => $_POST['grade'],
        'valor' => $exam->valor,
        'valor2' => $exam->valor2,
        'ver_nota' => $exam->ver_nota,
        'fecha' => $exam->fecha,
        'hora' => $exam->hora,
        'activo' => $exam->activo,
        'desc1_1' => $exam->desc1_1,
        'desc2' => $exam->desc2,
        'desc2_1' => $exam->desc2_1,
        'desc3' => $exam->desc3,
        'desc3_1' => $exam->desc3_1,
        'desc4' => $exam->desc4,
        'desc4_1' => $exam->desc4_1,
        'desc5' => $exam->desc5,
        'desc5_1' => $exam->desc5_1,
        'tiempo' => $exam->tiempo,
    ]);


    $db->query("INSERT INTO `" . Exam::$allTables[0] . "` (`id_examen`, `pregunta`, `respuesta`, `valor`) 
    SELECT '$duplicatedExamId',`pregunta`, `respuesta`, `valor` FROM T_examen_fyv WHERE id_examen = '$examId'");

    $db->query("INSERT INTO `" . Exam::$allTables[1] . "` (`id_examen`, `pregunta`, `respuesta1`, `respuesta2`, `respuesta3`, `respuesta4`, `respuesta5`,
     `respuesta6`, `respuesta7`, `respuesta8`, `correcta`, `valor`)
     SELECT '$duplicatedExamId', `pregunta`, `respuesta1`, `respuesta2`, `respuesta3`, `respuesta4`, `respuesta5`, `respuesta6`, `respuesta7`,
      `respuesta8`, `correcta`, `valor` FROM `T_examen_selec` WHERE id_examen = '$examId'");

    $db->query("INSERT INTO `" . Exam::$allTables[2] . "` (`id_examen`, `pregunta`, `respuesta_c`, `valor`)
    SELECT '$duplicatedExamId', `pregunta`, `respuesta_c`, `valor` FROM `T_examen_parea` WHERE id_examen = '$examId'");

    $option3 = DB::table(Exam::$allTables[2])->where('id_examen', $duplicatedExamId)->get();
    foreach ($option3 as $option) {
        $answerCode = DB::table(Exam::$allTables[3])->where('id', $option->respuesta_c)->first();
        DB::table(Exam::$allTables[3])->insert([
            "id_examen" => $duplicatedExamId,
            "respuesta" => $answerCode->respuesta
        ]);
    }
    $db->query("INSERT INTO `" . Exam::$allTables[4] . "` (`id_examen`, `pregunta`, `respuesta1`, `respuesta2`, `respuesta3`, `respuesta4`, `respuesta5`, `valor`)
    SELECT '$duplicatedExamId', `pregunta`, `respuesta1`, `respuesta2`, `respuesta3`, `respuesta4`, `respuesta5`, `valor` FROM `T_examen_linea`
     WHERE id_examen = '$examId'");


    $db->query("INSERT INTO `" . Exam::$allTables[5] . "` (`id_examen`, `pregunta`, `lineas`, `valor`) 
    SELECT '$duplicatedExamId',`pregunta`, `lineas`, `valor` FROM `T_examen_pregunta` WHERE id_examen = '$examId'");



    $duplicatedExam = DB::table('T_examenes')->where('id', $duplicatedExamId)->first();
    echo Util::toJson($duplicatedExam);
} else if (isset($_POST['gradeOptionsSearch'])) {
    $grade = $_POST['grade'];
    $trimester = $_POST['trimester'];
    $type = $_POST['type'];
    $data = DB::table('valores')->where([
        ['year', $teacher->info('year')],
        ['curso', $grade],
        ['trimestre', $trimester],
        ['nivel', $type]
    ])->first();
    $array = [];
    if ($data) {
        $array = [
            'response' => true,
            'data' => $data,
            'grade' => $grade,
            'trimester' => $trimester,
            'type' => $type
        ];
    } else {
        $array = [
            'response' => false,
            'grade' => $grade,
            'trimester' => $trimester,
            'type' => $type
        ];
    }

    echo Util::toJson($array);
} else if (isset($_POST['gradeOptions'])) {
    $action = $_POST['gradeOptions'];


    $selected = $_POST['selected'];
    $dataArray = [];
    if ($selected !== '') {
        $dataArray["nota$selected"] = $_POST['examId'];
    }

    for ($i = 1; $i <= 10; $i++) {
        $dataArray["tema$i"] = trim($_POST["description$i"]);
        $dataArray["val$i"] = $_POST["value$i"];
        $dataArray["fec$i"] = $_POST["date$i"];
    }

    if ($action === 'save') {
        $dataArray['trimestre'] = $_POST['trimester'];
        $dataArray['nivel'] = $_POST['type'];
        $dataArray['curso'] = $_POST['grade'];
        $dataArray['year'] = $teacher->info('year');


        DB::table('valores')->insert($dataArray);
    } else {
        $selectedBefore = $_POST['selectedBefore'];
        if ($selectedBefore !== '') {
            $dataArray["nota$selectedBefore"] = '';
        }
        DB::table('valores')->where('id', $_POST['optionId'])->update($dataArray);
    }
    echo Util::toJson($dataArray);
} else if (isset($_POST['fillCorrectExams'])) {
    $examId = $_POST['fillCorrectExams'];
    $examGrade = $_POST['grade'];
    $data = DB::table('T_examenes_terminados')
        ->select('year.nombre,year.apellidos,year.mt,T_examenes_terminados.id,T_examenes_terminados.terminado_el,T_examenes_terminados.puntos,T_examenes_terminados.bonos')
        ->join('year', 'year.mt', '=', 'T_examenes_terminados.id_estudiante')->where([
            ['curso', $examGrade],
            ['id_examen', $examId]
        ])->get();
    $array = [];
    if ($data) {
        $array = [
            'response' => true,
            'data' => $data
        ];
    } else {
        $array = ['response' => false];
    }
    echo Util::toJson($array);
} else if (isset($_POST['correctExams'])) {
    $examId = $_POST['correctExams'];
    $exam = new Exam($examId);
    $doneExams = DB::table('T_examenes_terminados')->where('id_examen', $examId)->get();
    $examTotalPoints = 0;
    foreach ($doneExams as $doneExam) {
        $doneExamId = $doneExam->id;
        $studentId = $doneExam->id_estudiante;

        if (isset($exam->fvs->topics)) {
            foreach ($exam->fvs->topics as $topic) {
                $done = DB::table('T_examen_terminado_fyv')->where([
                    ['id_examen', $doneExamId],
                    ['id_pregunta', $topic->id],
                    ['id_estudiante', $studentId]
                ])->first();
                $examTotalPoints += $topic->respuesta == $done->respuesta ? $topic->valor : 0;
            }
        }
        if (isset($exam->selects->topics)) {
            foreach ($exam->selects->topics as $topic) {
                $done = DB::table('T_examen_terminado_selec')->where([
                    ['id_examen', $doneExamId],
                    ['id_pregunta', $topic->id],
                    ['id_estudiante', $studentId]
                ])->first();
                $examTotalPoints += $topic->correcta == $done->respuesta ? $topic->valor : 0;
            }
        }
        if (isset($exam->pairs->topics)) {
            foreach ($exam->pairs->topics as $topic) {
                $done = DB::table('T_examen_terminado_parea')->where([
                    ['id_examen', $doneExamId],
                    ['id_pregunta', $topic->id],
                    ['id_estudiante', $studentId]
                ])->first();
                $examTotalPoints += $topic->respuesta_c == $done->respuesta ? $topic->valor : 0;
            }
        }
        if (isset($exam->lines->topics)) {
            foreach ($exam->lines->topics as $topic) {
                $done = DB::table('T_examen_terminado_linea')->where([
                    ['id_examen', $doneExamId],
                    ['id_pregunta', $topic->id],
                    ['id_estudiante', $studentId]
                ])->first();
                for ($i = 1; $i <= 5; $i++) {
                    if ($topic->{"respuesta$i"} !== '') {
                        $correct = strtolower($topic->{"respuesta$i"}) == strtolower($done->{"respuesta$i"}) ? true : false;
                    }
                }
                $examTotalPoints += $correct ? $topic->valor : 0;
            }
        }
        if (isset($exam->qas->topics)) {
            foreach ($exam->qas->topics as $topic) {
                $done = DB::table('T_examen_terminado_pregunta')->where([
                    ['id_examen', $doneExamId],
                    ['id_pregunta', $topic->id],
                    ['id_estudiante', $studentId]
                ])->first();
                $examTotalPoints += $done->puntos_ganados ? $done->puntos_ganados : 0;
            }
        }

        DB::table("T_examenes_terminados")->where('id', $doneExamId)->update(["puntos" => $examTotalPoints]);
    }
    echo $examTotalPoints;
} else if (isset($_POST['passPoints'])) {
    $examId = $_POST['passPoints'];
    $exam = new Exam($examId);
    $class = $exam->curso;
    $year = $exam->info('year');
    $doneExamsSS = DB::table('T_examenes_terminados')->select('DISTINCT ss_estudiante')->where([
        ['id_examen', $examId],
        ['curso', $class],
        ['year', $year]
    ])->get();
    foreach ($doneExamsSS as $doneExamSS) {
        $ss = $doneExamSS->ss_estudiante;
        $doneExam = DB::table('T_examenes_terminados')->whereRaw("id_examen='$examId' AND ss_estudiante='$ss' AND curso='$class' AND year='$year' AND count = (SELECT MAX(count) FROM T_examenes_terminados WHERE id_examen='$examId' AND ss_estudiante='$ss' AND curso='$class' AND year='$year')")->first();
        $value = DB::table('valores')->whereRaw("curso='$class' AND (nota1 ='$examId' OR nota2 ='$examId' OR nota3 ='$examId' OR nota4 ='$examId' OR nota5 ='$examId' OR nota6 ='$examId' OR nota7 ='$examId' OR nota8 ='$examId' OR nota9 ='$examId' OR nota10 ='$examId')")->first();
        $table = $value->nivel === 'Notas' ? 'padres' : 'padres4';

        if ($value->trimestre === 'Trimestre-1') {
            $totalValue = 0;
        } elseif ($value->trimestre === 'Trimestre-2') {
            $totalValue = 9;
        } elseif ($value->trimestre === 'Trimestre-3') {
            $totalValue = 18;
        } elseif ($value->trimestre === 'Trimestre-4') {
            $totalValue = 27;
        }
        if ($value->nota1 == $examId) {
            $note = 1;
        } elseif ($value->nota2 == $examId) {
            $note = 2;
        } elseif ($value->nota3 == $examId) {
            $note = 3;
        } elseif ($value->nota4 == $examId) {
            $note = 4;
        } elseif ($value->nota5 == $examId) {
            $note = 5;
        } elseif ($value->nota6 == $examId) {
            $note = 6;
        } elseif ($value->nota7 == $examId) {
            $note = 7;
        } elseif ($value->nota8 == $examId) {
            $note = 8;
        } elseif ($value->nota9 == $examId) {
            $note = 9;
        } elseif ($value->nota10 == $examId) {
            $note = 10;
        }

        $totalValue += $note;
        $points = $doneExam->puntos + $doneExam->bonos;
        $points = !isset($_POST['passPorcent']) ? number_format(($points /$exam->valor * 100),0) : $points;
        $examValue = !isset($_POST['passPorcent']) ? $exam->valor : '100';
        $examValue2 = !isset($_POST['passPorcent']) ? '0' : '100';
        DB::table('valores')->where('id', $value->id)->update([
            "val{$totalValue}" => $examValue
        ]);
        DB::table('T_examenes')->where('id', $examId)->update([
            "valor2" => $examValue
        ]);
        DB::table($table)->where([
            ['curso', $doneExam->curso],
            ['ss',$doneExam->ss_estudiante],
            ['year',$year]
        ])->update([
            "not{$totalValue}" => $points
        ]);
    }
}
