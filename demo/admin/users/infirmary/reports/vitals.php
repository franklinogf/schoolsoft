<?php

require_once __DIR__ . '/../../../../app.php';

use App\Models\Student;
use App\Pdfs\Infirmary\VitalsReportPdf;
use Classes\Route;
use Classes\Session;

Session::is_logged();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_POST['student_id'] ?? null;
    $withLines = ($_POST['with_lines'] ?? '1') === '1';
    $additionalLines = ($_POST['additional_lines'] ?? '1') === '1';

    if ($studentId) {
        $student = Student::with('infirmary')->find($studentId);

        if ($student) {
            $pdf = new VitalsReportPdf($student, $withLines, $additionalLines);
            $pdf->generate();
            $pdf->Output();
            exit;
        }
    }
}

$students = Student::orderBy('apellidos')->orderBy('nombre')->get();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Informe de Signos Vitales");
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
                        <label for="with_lines" class="form-label"><?= __('Formato de Tabla') ?></label>
                        <select name="with_lines" id="with_lines" class="form-control">
                            <option value="1"><?= __('Con líneas') ?></option>
                            <option value="0"><?= __('Sin líneas') ?></option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label"><?= __('Líneas adicionales') ?></label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="additional_lines" id="lines_yes" value="1" checked>
                                <label class="form-check-label" for="lines_yes"><?= __('Sí') ?></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="additional_lines" id="lines_no" value="0">
                                <label class="form-check-label" for="lines_no"><?= __('No') ?></label>
                            </div>
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
</body>

</html>