<?php

use Classes\Lang;
global $tableStudentsCheckbox;
global $students;

$lang = new Lang([
['Estudiante','Student'],
['Usuario','Username'],
['Atrás','Go back']
]);
?>
<!-- Students table -->

<div class="table_wrap">
   <table class="studentsTable table table-striped table-hover cell-border w-100 shadow table-pointer">
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
            <th><?= $lang->translation("Estudiante") ?></th>
            <th><?= $lang->translation("Usuario") ?></th>
         </tr>
      </thead>
      <tbody>
         <?php if ($students) : ?>
            <?php foreach ($students as $student) : ?>
               <tr id="<?= $student->mt ?>">
                  <td><?= "$student->apellidos $student->nombre" ?></td>
                  <td><?= $student->usuario ?></td>
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
            <th><?= $lang->translation("Estudiante") ?></th>
            <th><?= $lang->translation("Usuario") ?></th>
         </tr>
         <?php if (!$students) : ?>
            <tr class="bg-gradient-light bg-light">
               <td colspan="<?= $tableStudentsCheckbox ? '3' : '2' ?>"><button id="back" type="button" class="btn btn-block btn-primary"><?= utf8_encode($lang->translation("Atrás")) ?></button></td>
            </tr>
         <?php endif ?>
         <?php if ($tableStudentsCheckbox) : ?>
            <tr class="bg-gradient-light bg-light">
               <td colspan="3"><button type="button" class="btn btn-block btn-primary continueBtn"><?= $lang->translation("Continuar") ?></button></td>
            </tr>
         <?php endif ?>
      </tfoot>
   </table>
</div>