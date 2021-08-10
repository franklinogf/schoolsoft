<?php

use Classes\DataBase\DB;

global $student;
global $tableClassesCheckbox;
global $virtual;
?>
<!-- classes table -->
<div class="table_wrap">
   <table class="classesTable table table-striped table-hover cell-border w-100 shadow">
      <thead class="bg-gradient-primary bg-primary border-0">
         <tr>
            <?php if ($tableClassesCheckbox) : ?>
               <th class="checkbox">
                  <div class="custom-control custom-checkbox">
                     <input class="custom-control-input bg-success checkAll" type="checkbox" id="check1">
                     <label class="custom-control-label" for="check1"></label>
                  </div>
               </th>
            <?php endif ?>
            <th>Curso</th>
            <th>Descripción</th>
         </tr>
      </thead>
      <tbody>
         <?php foreach ($student->classes() as $class) : ?>

            <?php
            if ($virtual) :
               $virtualClass = DB::table('virtual')->where([
                  ['curso', $class->curso],
                  ['year', $student->info('year')],
                  ['id_profesor', $class->id],
               ])->first();

            ?>
               <?php if ($virtualClass) : ?>
                  <tr data-teacher-id="<?= $class->id ?>">
                     <td><?= $class->curso ?></td>
                     <td><?= $class->descripcion ?></td>
                  </tr>
               <?php endif ?>
            <?php else : ?>
               <tr data-id="<?= $class->id ?>">
                  <?php if ($tableClassesCheckbox) : ?>
                     <td>
                        <div class="custom-control custom-checkbox">
                           <input class="custom-control-input check bg-success" type="checkbox" id="<?= $class->curso ?>" name="class[]" value="<?= $class->curso ?>">
                           <label class=" custom-control-label" for="<?= $class->curso ?>"></label>
                        </div>
                     </td>
                  <?php endif ?>
                  <td><?= $class->curso ?></td>
                  <td><?= $class->descripcion ?></td>
               </tr>
            <?php endif ?>
         <?php endforeach ?>
      </tbody>
      <tfoot>
         <tr class="bg-gradient-secondary bg-secondary">
            <?php if ($tableClassesCheckbox) : ?>
               <th>
                  <div class="custom-control custom-checkbox">
                     <input class="custom-control-input bg-success checkAll" type="checkbox" id="check2">
                     <label class="custom-control-label" for="check2"></label>
                  </div>
               </th>
            <?php endif ?>
            <th>Curso</th>
            <th>Descripción</th>
         </tr>
         <?php if ($tableClassesCheckbox) : ?>
            <tr class="bg-gradient-light bg-light">
               <td colspan="3"><button type="submit" class="btn btn-block btn-primary">Continuar</button></td>
            </tr>
         <?php endif ?>
      </tfoot>
   </table>
</div>