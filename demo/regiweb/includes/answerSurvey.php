<?php

use App\Models\Admin;
use App\Models\Teacher;
use Classes\Route;
use Classes\Session;
use Illuminate\Database\Capsule\Manager;

require '../../app.php';

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $surveyId = $_POST['survey_id'];
    $answer = $_POST['answer'];
    $comment = $_POST['comment'] ?? null;

    // Validate and process the survey response
    if (!empty($surveyId) && !empty($answer)) {
        $teacher = Teacher::find(Session::id());
        $school = Admin::primaryAdmin()->first();
        $year = $school->year;
        $date = date('Y-m-d');

        // Save the response to the database
        Manager::table('respuestas')->insert([
            'year' => $year,
            'id2' => $teacher->id,
            'codigo' => $surveyId,
            'dijo' => $answer,
            'fecha' => $date,
            'apellidos' => $teacher->apellidos,
            'nombre' => $teacher->nombre,
            'comentario' => $comment,
        ]);
    }
    Route::redirect('home.php');
}
