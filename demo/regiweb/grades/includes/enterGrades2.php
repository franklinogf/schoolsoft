<?php

require_once __DIR__ . '/../../../app.php';

use App\Models\Admin;
use App\Models\Teacher;
use Classes\Server;
use Classes\Session;
use Illuminate\Database\Capsule\Manager as DB;

Server::is_post();

$teacher = Teacher::find(Session::id());
$school = Admin::primaryAdmin();
$year = $school->year();


if (isset($_POST['changeValue'])) {
    $type = $_POST['changeValue'];
    $value = $_POST['value'];
    $id = $_POST['id'];
    DB::table('valores')
        ->where('mt', $id)
        ->update([$type => $value]);
    echo json_encode(['status' => 'success', 'message' => "$type updated successfully."]);
} elseif (isset($_POST['changeOption'])) {
    $type = $_POST['changeOption'];
    $value = $_POST['value'];
    $subjectCode = $_POST['subjectCode'];
    DB::table('padres')->where([
        ['curso', $subjectCode],
        ['year', $year],
    ])->update([$type => $value === 'true' ? 'ON' : '']);
    echo json_encode(['status' => 'success', 'message' => "$type updated successfully."]);
} elseif (isset($_POST['submitForm'])) {
    // echo json_encode($_POST);
    // exit;
    $table = $_POST['table'];
    $report = $_POST['report'];
    $trimester = $_POST['trimester'];
    $sumTrimester = empty($_POST['sumTrimester']) ? false : true;
    $subjectCode = $_POST['subject'];
    $students = $_POST['students'];


    foreach ($students as $ss => $data) {
        DB::table($table)
            ->where([
                ['ss', $ss],
                ['curso', $subjectCode],
                ['year', $year]
            ])->update($data);
    }

    echo json_encode(['status' => 'success', 'message' => 'Grades updated successfully.']);
}
