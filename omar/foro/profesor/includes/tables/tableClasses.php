<?php
global $teacher;
?>
<!-- classes table -->
<table class="classesTable table table-striped table-hover cell-border w-100 shadow">
   <thead class="bg-gradient-primary bg-primary border-0">
      <tr>
         <th>Curso</th>
         <th>Descripción</th>
      </tr>
   </thead>
   <tbody>
      <?php foreach ($teacher->classes() as $class) : ?>
         <tr>
            <td><?= $class->curso ?></td>
            <td><?= $class->desc1 ?></td>
         </tr>
      <?php endforeach ?>
   </tbody>
   <tfoot>
      <tr class="bg-gradient-secondary bg-secondary">
         <th>Curso</th>
         <th>Descripción</th>
      </tr>
   </tfoot>
</table>