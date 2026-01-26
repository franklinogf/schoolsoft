<?php

require_once __DIR__ . '/../../../../app.php';

use App\Models\School;
use App\Pdfs\Infirmary\VaccinationStatusPdf;
use App\Services\SchoolService;
use Classes\Route;
use Classes\Session;

Session::is_logged();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grade = $_POST['grade'] ?? null;
    $orderBy = $_POST['order_by'] ?? 'apellidos';

    $pdf = new VaccinationStatusPdf(
        grade: $grade,
        orderBy: $orderBy
    );
    $pdf->generate();
    $pdf->Output();
    exit;
}

$grades = SchoolService::getAllGrades();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Estado de Vacunación");
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
                        <label for="grade" class="form-label"><?= __('Grado') ?></label>
                        <select name="grade" id="grade" class="form-control">
                            <option value="Todos"><?= __('Todos') ?></option>
                            <?php foreach ($grades as $g) : ?>
                                <option value="<?= $g ?>"><?= $g ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="order_by" class="form-label"><?= __('Ordenar por') ?></label>
                        <select name="order_by" id="order_by" class="form-control">
                            <option value="apellidos"><?= __('Apellidos') ?></option>
                            <option value="Grados"><?= __('Grados') ?></option>
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