<?php

use App\Models\Admin;
use App\Models\Family;
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
        $family = Family::find(Session::id());
        $school = Admin::primaryAdmin();
        $year = $school->year;
        $date = date('Y-m-d');
        $student = $family->kids()->first();

        // Save the response to the database
        Manager::table('respuestas')->insert([
            'year' => $year,
            'id2' => $family->id,
            'codigo' => $surveyId,
            'dijo' => $answer,
            'fecha' => $date,
            'apellidos' => $student->apellidos,
            'nombre' => $student->nombre,
            'grado' => $student->grado,
            'comentario' => $comment,
        ]);
    }
    Route::redirect('home.php');
}
