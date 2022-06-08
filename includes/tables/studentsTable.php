<?php

use Classes\Lang;

global $tableStudentsCheckbox;
global $students;
global $studentsInfoForTable;
global $studentId;

$defaultStudentItemsForTable = [
    [
        'title' => "Nombre completo",
        'values' => [
            'apellidos',
            'nombre',
        ]
    ],
];
$studentId = $studentId ?? 'mt';
$studentsInfoForTable = $studentsInfoForTable ?? $defaultStudentItemsForTable;

$lang = new Lang();
?>
<!-- default students table -->

<div class="table_wrap">
    <table class="studentsTable table table-striped table-hover cell-border w-100 shadow">
        <thead class="bg-gradient-primary bg-primary border-0">
            <tr>
                <?php if ($tableStudentsCheckbox) : ?>
                    <th class="checkbox">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input bg-success checkAll" type="checkbox" id="check1">
                            <label class="custom-control-label" for="check1"></label>
                        </div>
                    </th>
                <?php endif ?>
                <?php foreach ($studentsInfoForTable as $studentsInfo) : ?>
                    <th class="text-center">
                        <?= $studentsInfo['title'] ?>
                    </th>
                <?php endforeach ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($students) : ?>
                <?php foreach ($students as $student) : ?>
                    <tr id="<?= $student->{$studentId} ?>">
                        <td>
                            <?php foreach ($studentsInfoForTable as $studentsInfo) : ?>
                                <?php foreach ($studentsInfo['values'] as $index => $value) : ?>
                                    <?= sizeof($studentsInfo['values']) > 1 && $index < sizeof($studentsInfo['values']) ? $student->{$value} . ' ' : $student->{$value} ?>
                                <?php endforeach ?>
                            <?php endforeach ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
        </tbody>
        <tfoot>
            <tr class="bg-gradient-secondary bg-secondary">
                <?php if ($tableStudentsCheckbox) : ?>
                    <th>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input bg-success checkAll" type="checkbox" id="check2">
                            <label class="custom-control-label" for="check2"></label>
                        </div>
                    </th>
                <?php endif ?>
                <?php foreach ($studentsInfoForTable as $studentsInfo) : ?>
                    <th class="text-center">
                        <?= $studentsInfo['title'] ?>
                    </th>
                <?php endforeach ?>
            </tr>
            <?php if ($tableStudentsCheckbox) : ?>
                <tr class="bg-gradient-light bg-light">
                    <td colspan="<?= sizeof($studentsInfoForTable) ?>"><button type="button" class="btn btn-block btn-primary continueBtn"><?= $lang->translation("Continuar") ?></button></td>
                </tr>
            <?php endif ?>
        </tfoot>
    </table>
</div>