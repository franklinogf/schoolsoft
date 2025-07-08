<?php
global $__tableDataCheckbox; # This variable is used for habiltate the checkboxes in the table @boolean variable
global $__tableData;
global $__tableDataInfo;
global $__dataPk;
global $__tableDataCheckboxName;
global $__tableDataName;
global $__tableDataButton;

$defaultTableDataInfo = [
     [
        'title' => ["es" => "Nombre completo", 'en' => "Full name"],
        'values' => [
            'apellidos',
            'nombre',
        ]
     ],
     [
        'title' => ["es" => "Grado", 'en' => "Grade"],
        'values' => [
            'grado',
        ]
    ],
];
$__dataPk = $__dataPk ?: 'mt';
$__tableDataInfo = $__tableDataInfo ?: $defaultTableDataInfo;
$__tableDataCheckboxName = $__tableDataCheckboxName ?: '';
$__tableDataName = $__tableDataName ?: '';
$__tableDataCheckbox = $__tableDataCheckbox ?: false;
$__tableDataButton = $__tableDataButton !== null ? $__tableDataButton : true;

?>
<!-- default table -->

<div class="table_wrap">
    <table class="dataTable table table-sm table-pointer table-striped table-hover cell-border shadow">
        <thead class="bg-gradient-primary bg-primary border-0">
            <tr>
                <?php if ($__tableDataCheckbox) : ?>
                    <th class="checkbox">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input bg-success checkAll" type="checkbox" id="<?= "check1$__tableDataName" ?>">
                            <label class="custom-control-label" for="<?= "check1$__tableDataName" ?>"></label>
                        </div>
                    </th>
                <?php endif ?>
                <?php foreach ($__tableDataInfo as $dataInfo) : ?>
                    <th><?= $dataInfo['title'][__LANG] ?></th>
                <?php endforeach ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($__tableData) : ?>
                <?php foreach ($__tableData as $data) : ?>
                    <tr id="<?= $data->{$__dataPk} ?>">
                        <?php if ($__tableDataCheckbox) : ?>
                            <td>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input check" type="checkbox" id="<?= $data->{$__dataPk} ?>" <?= $__tableDataCheckboxName !== '' ? "name='{$__tableDataCheckboxName}[]'" : '' ?> value="<?= $data->{$__dataPk} ?>">
                                    <label class="custom-control-label" for="<?= $data->{$__dataPk} ?>"></label>
                                </div>
                            </td>
                        <?php endif ?>
                        <?php foreach ($__tableDataInfo as $dataInfo) : ?>
                            <td>
                                <?php foreach ($dataInfo['values'] as $index => $value) : ?>
                                    <?= sizeof($dataInfo['values']) > 1 && $index < sizeof($dataInfo['values']) ? $data->{$value} . ' ' : $data->{$value} ?>
                                <?php endforeach ?>
                            </td>
                        <?php endforeach ?>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
        </tbody>
        <tfoot>
            <tr class="bg-gradient-secondary bg-secondary">
                <?php if ($__tableDataCheckbox) : ?>
                    <th class="checkbox">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input bg-success checkAll" type="checkbox" id="<?= "check2$__tableDataName" ?>">
                            <label class="custom-control-label" for="<?= "check2$__tableDataName" ?>"></label>
                        </div>
                    </th>
                <?php endif ?>
                <?php foreach ($__tableDataInfo as $dataInfo) : ?>
                    <th>
                        <?= $dataInfo['title'][__LANG] ?>
                    </th>
                <?php endforeach ?>
            </tr>
            <?php if ($__tableDataCheckbox && $__tableDataButton === true) : ?>
                <tr>
                    <td colspan="<?= sizeof($__tableDataInfo) + 1 ?>"><button type="submit" class="btn btn-block btn-primary"><?= __LANG === 'es' ? 'Continuar' : 'Continue' ?></button></td>
                </tr>
            <?php endif ?>
        </tfoot>
    </table>
</div>