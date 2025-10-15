<?php
require_once '../../../app.php';

use Classes\DataBase\DB;
use Classes\Route;
use Classes\Session;
use Classes\Lang;

Session::is_logged();
$lang = new Lang([
  ["Códigos de Alias", "Alias codes"],
  ['Grabar', 'Save'],
  ['Código', 'Code'],
  ['Editar', 'Edit'],
  ['Borrar', 'Delete'],
  ['Debe de llenar todos los campos', 'You must fill all fields'],
  ['Lista de codigos', 'Codes list'],
  ['', ''],
  ['Grado', 'Grade'],
  ['', ''],
  ['Opciones', 'Options'],
]);

if (isset($_POST['borra'])) {
  DB::table('alias')->where('id', $_POST['mt'])->delete();
}
$add2 = $_POST['add2'] ?? null;
if (isset($_POST['add']) and $add2 == 0) {
  DB::table('alias')->insert([
    'alias' => $_POST['alias'],
    'grado' => $_POST['grado'],
    'seccion' => $_POST['seccion'],
  ]);
}

if (isset($_POST['add']) and $add2 == 1) {
  $thisCourse = DB::table('alias')->where('id', $_POST['mt'])->update([
    'alias' => $_POST['alias'],
    'grado' => $_POST['grado'],
    'seccion' => $_POST['seccion'],
  ]);
}

$add2 = 0;
if (isset($_POST['cambiar'])) {
  $reg4 = DB::table('alias')->where('id', $_POST['mt'])->first();
  $add2 = 1;
}

$codes = DB::table('alias')->orderBy('alias')->get();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />


<head>
  <?php
  $title = $lang->translation('Códigos de Alias');
  Route::includeFile('/admin/includes/layouts/header.php');
  ?>
</head>

<body>
  <?php
  Route::includeFile('/admin/includes/layouts/menu.php');
  ?>
  <div class="container-lg mt-lg-3 mb-5 px-0">
    <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Códigos de Alias') ?></h1>
    <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">




      <form method="post" action="">
        <table align="center" cellspacing="0" style="width: 80%">
          <tr>
            <th style="width: 200px"><?= $lang->translation('Alias') ?></th>
            <th style="width: 80px"><?= $lang->translation('Grado') ?></th>
            <th style="width: 80px"><?= $lang->translation('Sección') ?></th>
            <th style="width: 100px" colspan="2">
              <center><?= $lang->translation('Opciones') ?></center>
            </th>
          </tr>
          <?php foreach ($codes as $code): ?>
            <tr>
              <form method="post">
                <td style="width: 200px">
                  <?= $code->alias ?>
                </td>
                <td style="width: 80px">
                  <?= $code->grado ?>
                  &nbsp;
                </td>
                <td style="width: 80px">
                  <?= $code->seccion ?>
                </td>
                <td style="width: 100px">
                  <input class="btn btn-sm btn-danger delete" name="borra" style="width: 90px;" type="submit" formnovalidate value="<?= $lang->translation('Borrar') ?>" />
                </td>
                <td style="width: 100px">
                  <input class="btn btn-sm btn-primary" name="cambiar" style="width: 90px" type="submit" formnovalidate value="<?= $lang->translation('Editar') ?>" />
                </td>
                <input type=hidden name=nn value='<?= $code->dependientes ?>'>
                <input type=hidden name=mt value='<?= $code->id ?>'>
                <input type=hidden name=add2 value='<?= $add2 ?>'>
              </form>
            </tr>
          <?php endforeach; ?>
          <tr>
            <th style="width: 200px"><?= $lang->translation('Alias') ?></th>
            <th style="width: 80px"><?= $lang->translation('Grado') ?></th>
            <th style="width: 80px"><?= $lang->translation('Sección') ?></th>
            <th style="width: 100px" colspan="2">
              <center><?= $lang->translation('Opciones') ?></center>
            </th>
          </tr>

          <tr>
            <form method="post">
              <td style="width: 200px">
                <input class="form-control" maxlength="20" name="alias" size="20" type="text" required value="<?= $reg4->alias ?? '' ?>" />
              </td>
              <td style="width: 80px">
                <input class="form-control" maxlength="5" name="grado" size="5" required value="<?= $reg4->grado ?? '' ?>">
              </td>
              <td style="width: 80px">
                <input class="form-control" id="ex-91" name="seccion" class="text" size="2" type="text" maxlength="2" value="<?= $reg4->seccion ?? '' ?>" />
              </td>
              <td style="width: 100px">
                <input type=hidden name=nn0 value='<?= $reg4->alias ?>'>
                <input type=hidden name=mt value='<?= $reg4->id ?>'>
                <input type=hidden name=add2 value='<?= $add2 ?>'>

                <input class="btn btn-primary" name="add" style="width: 90px" type="submit" value="<?= $lang->translation('Grabar') ?>" />

              </td>
              <td style="width: 100px"></td>
            </form>
          </tr>


        </table>
      </form>
    </div>



  </div>
  <?php
  $jqMask = true;
  Route::includeFile('/includes/layouts/scripts.php', true);
  ?>


</body>

</html>