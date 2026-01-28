<?php

require_once __DIR__ . '/../../../../app.php';

use App\Models\Student;
use App\Pdfs\Infirmary\VisitsReportPdf;
use App\Services\SchoolService;
use Classes\Route;
use Classes\Session;

Session::is_logged();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectionType = $_POST['selection_type'] ?? '';
    $studentId = $_POST['student_id'] ?? null;
    $grade = $_POST['grade'] ?? null;
    $fromDate = $_POST['from_date'] ?? '';
    $toDate = $_POST['to_date'] ?? '';

    $student = null;
    $gradeFilter = null;

    if ($selectionType === 'student' && $studentId) {
        $student = Student::find($studentId);
    } elseif ($selectionType === 'grade') {
        $gradeFilter = $grade;
    }

    $pdf = new VisitsReportPdf(
        student: $student,
        grade: $gradeFilter,
        fromDate: $fromDate,
        toDate: $toDate
    );
    $pdf->generate();
    $pdf->Output();
    exit;
}

$students = Student::query()->orderBy('apellidos')->orderBy('nombre')->get();
$grades = SchoolService::getAllGrades();
$today = date('Y-m-d');

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Informe de Visitas a Enfermería");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5">
        <h2 class="text-center my-3"><?= $title ?></h2>

        <div class="card mx-auto" style="max-width: 500px;">
            <div class="card-body">
                <form method="post" target="_blank">
                    <div class="form-group mb-3">
                        <label for="selection_type" class="form-label"><?= __('Tipo de Selección') ?></label>
                        <select name="selection_type" id="selection_type" class="form-control" required onchange="toggleSelection()">
                            <option value=""><?= __('Seleccione') ?></option>
                            <option value="student"><?= __('Por Estudiante') ?></option>
                            <option value="grade"><?= __('Por Grado') ?></option>
                        </select>
                    </div>

                    <div class="form-group mb-3" id="student_group" style="display: none;">
                        <label for="student_id" class="form-label"><?= __('Estudiante') ?></label>
                        <select name="student_id" id="student_id" class="form-control">
                            <option value=""><?= __('Seleccione un estudiante') ?></option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?= $student->mt ?>">
                                    <?= htmlspecialchars($student->apellidos . ', ' . $student->nombre . ' (' . $student->grado . ')') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-3" id="grade_group" style="display: none;">
                        <label for="grade" class="form-label"><?= __('Grado') ?></label>
                        <select name="grade" id="grade" class="form-control">
                            <option value="Todos"><?= __('Todos') ?></option>
                            <?php foreach ($grades as $g): ?>
                                <option value="<?= $g ?>"><?= $g ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="from_date" class="form-label"><?= __('Desde') ?></label>
                            <input type="date" name="from_date" id="from_date" class="form-control" value="<?= $today ?>" required>
                        </div>
                        <div class="col-6">
                            <label for="to_date" class="form-label"><?= __('Hasta') ?></label>
                            <input type="date" name="to_date" id="to_date" class="form-control" value="<?= $today ?>" required>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            <?= __('Generar Informe') ?>
                        </button>
                        <a href="<?= Route::url('/admin/users/infirmary/reports/') ?>" class="btn btn-secondary">
                            <?= __('Cancelar') ?>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

    <script>
        function toggleSelection() {
            const type = document.getElementById('selection_type').value;
            const studentGroup = document.getElementById('student_group');
            const gradeGroup = document.getElementById('grade_group');
            const studentSelect = document.getElementById('student_id');
            const gradeSelect = document.getElementById('grade');

            if (type === 'student') {
                studentGroup.style.display = 'block';
                gradeGroup.style.display = 'none';
                studentSelect.required = true;
                gradeSelect.required = false;
            } else if (type === 'grade') {
                studentGroup.style.display = 'none';
                gradeGroup.style.display = 'block';
                studentSelect.required = false;
                gradeSelect.required = true;
            } else {
                studentGroup.style.display = 'none';
                gradeGroup.style.display = 'none';
                studentSelect.required = false;
                gradeSelect.required = false;
            }
        }
    </script>
</body>

</html>