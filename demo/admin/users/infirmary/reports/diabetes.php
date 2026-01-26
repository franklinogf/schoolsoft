<?php

require_once __DIR__ . '/../../../../app.php';

use App\Models\Student;
use App\Pdfs\Infirmary\DiabetesReportPdf;
use Classes\Route;
use Classes\Session;

Session::is_logged();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_POST['student_id'] ?? null;
    $reportType = (int) ($_POST['report_type'] ?? 1);

    if ($studentId) {
        $student = Student::find($studentId);

        if ($student) {
            $pdf = new DiabetesReportPdf($student, $reportType);
            $pdf->generate();
            $pdf->Output();
            exit;
        }
    }
}

$students = Student::query()->orderBy('apellidos')->orderBy('nombre')->get();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Informe de Diabetes");
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
                <form method="post">
                    <div class="form-group mb-3">
                        <label for="student_id" class="form-label"><?= __('Estudiante') ?></label>
                        <select name="student_id" id="student_id" class="form-control" required>
                            <option value=""><?= __('Seleccione un estudiante') ?></option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?= $student->mt ?>">
                                    <?= htmlspecialchars($student->apellidos . ', ' . $student->nombre . ' (' . $student->grado . ')') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="report_type" class="form-label"><?= __('Tipo de Informe') ?></label>
                        <select name="report_type" id="report_type" class="form-control" required>
                            <option value="1"><?= __('Diabetes Parte 1 - Información General') ?></option>
                            <option value="2"><?= __('Diabetes Parte 2 - Insulina') ?></option>
                            <option value="3"><?= __('Ejercicio y Deportes') ?></option>
                        </select>
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
</body>

</html>