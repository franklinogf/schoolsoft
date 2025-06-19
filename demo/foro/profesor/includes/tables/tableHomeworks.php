<?php

use Classes\Lang;
$lang = new Lang([
   ['Tarea', 'Homework'],
   ['Fecha', 'Date'],
   ['Atrás', 'Go back'],
]);
?>
<!-- homework table -->
<div class="table_wrap">
   <table class="homeworksTable table table-striped table-hover cell-border w-100 shadow table-pointer">
      <thead class="bg-gradient-primary bg-primary border-0">
         <tr>
            <th><?= $lang->translation("Tarea") ?></th>
            <th><?= $lang->translation("Fecha") ?></th>
         </tr>
      </thead>
      <tbody>
      </tbody>
      <tfoot>
         <tr class="bg-gradient-secondary bg-secondary">
            <th><?= $lang->translation("Tarea") ?></th>
            <th><?= $lang->translation("Fecha") ?></th>
         </tr>
         <tr class="bg-gradient-light bg-light">
            <td colspan="2"><button id="back" type="button" class="btn btn-block btn-primary"><?= $lang->translation("Atrás") ?></button></td>
         </tr>
      </tfoot>
   </table>
</div>