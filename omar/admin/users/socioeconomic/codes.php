<?php
require_once '../../../app.php';

use Classes\DataBase\DB;
use Classes\Route;
use Classes\Session;
use Classes\Lang;

Session::is_logged();
$lang = new Lang([
  ["Códigos Socio económico", "Socioeconomic codes"],
  ['Grabar', 'Save'],
  ['Código', 'Code'],
  ['Editar', 'Edit'],
  ['Borrar', 'Delete'],
  ['Debe de llenar todos los campos', 'You must fill all fields'],
  ['Lista de codigos', 'Codes list'],
  ['Dependientes', 'Dependents'],
  ['Sobre nivel de pobreza', 'High level of poverty'],
  ['Bajo nivel de pobreza', 'Low poverty level'],
  ['Opciones', 'Options'],
]);

if (isset($_POST['borra'])) {
  DB::table('socio_economico')->where('mt', $_POST['mt'])->delete();
}
$add2 = $_POST['add2'] ?? null;
if (isset($_POST['add']) and $add2 == 0) {
  DB::table('socio_economico')->insert([
    'dependientes' => $_POST['dependientes'],
    'bajo_nivel' => $_POST['bajo_nivel'],
    'sobre_nivel' => $_POST['sobre_nivel'],
  ]);
}

if (isset($_POST['add']) and $add2 == 1) {
  $thisCourse = DB::table('socio_economico')->where('mt', $_POST['mt'])->update([
    'dependientes' => $_POST['dependientes'],
    'bajo_nivel' => $_POST['bajo_nivel'],
    'sobre_nivel' => $_POST['sobre_nivel'],
  ]);
}

$add2 = 0;
if (isset($_POST['cambiar'])) {
  $reg4 = DB::table('socio_economico')->where('mt', $_POST['mt'])->first();
  $add2 = 1;
}

$codes = DB::table('socio_economico')->orderBy('dependientes')->get();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />


<head>
  <?php
  $title = $lang->translation('Códigos Socio económico');
  Route::includeFile('/admin/includes/layouts/header.php');
  ?>
</head>

<body>
  <?php
  Route::includeFile('/admin/includes/layouts/menu.php');
  ?>
  <div class="container-lg mt-lg-3 mb-5 px-0">
    <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Códigos Socio económico') ?></h1>
    <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">




      <form method="post" action="">
        <table align="center" cellspacing="0" style="width: 71%">
          <tr>
            <th style="width: 80px"><?= $lang->translation('Dependientes') ?></th>
            <th style="width: 80px"><?= $lang->translation('Bajo nivel de pobreza') ?></th>
            <th style="width: 80px"><?= $lang->translation('Sobre nivel de pobreza') ?></th>
            <th style="width: 100px" colspan="2">
              <center><?= $lang->translation('Opciones') ?></center>
            </th>
          </tr>
          <?php foreach ($codes as $code): ?>
            <tr>
              <form method="post">
                <td style="width: 80px">
                  <?= $code->dependientes ?>
                </td>
                <td style="width: 80px">
                  <?= $code->bajo_nivel ?>
                  &nbsp;
                </td>
                <td style="width: 80px">
                  <?= $code->sobre_nivel ?>
                </td>
                <td style="width: 100px">
                  <input class="btn btn-sm btn-danger delete" name="borra" style="width: 90px;" type="submit" formnovalidate value="<?= $lang->translation('Borrar') ?>" />
                </td>
                <td style="width: 100px">
                  <input class="btn btn-sm btn-primary" name="cambiar" style="width: 90px" type="submit" formnovalidate value="<?= $lang->translation('Editar') ?>" />
                </td>
                <input type=hidden name=nn value='<?= $code->dependientes ?>'>
                <input type=hidden name=mt value='<?= $code->mt ?>'>
                <input type=hidden name=add2 value='<?= $add2 ?>'>
              </form>
            </tr>
          <?php endforeach; ?>
          <tr>
            <th style="width: 80px"><?= $lang->translation('Dependientes') ?></th>
            <th style="width: 80px"><?= $lang->translation('Bajo nivel de pobreza') ?></th>
            <th style="width: 80px"><?= $lang->translation('Sobre nivel de pobreza') ?></th>
            <th style="width: 100px" colspan="2">
              <center><?= $lang->translation('Opciones') ?></center>
            </th>
          </tr>

          <tr>
            <form method="post">
              <td style="width: 80px">
                <input class="form-control" maxlength="2" name="dependientes" size="2" type="text" required value="<?= $reg4->dependientes ?? '' ?>" />
              </td>
              <td style="width: 80px">
                <input class="form-control" maxlength="10" name="bajo_nivel" size="12" style="width: 190px" type="text" required placeholder="999999.99" value="<?= $reg4->bajo_nivel ?? '' ?>">
              </td>
              <td style="width: 80px">
                <input class="form-control" id="ex-91" name="sobre_nivel" class="text" size="12" type="text" style="width: 190px" maxlength="10" required placeholder="999999.99" value="<?= $reg4->sobre_nivel ?? '' ?>" />
              </td>
              <td style="width: 100px">
                <input type=hidden name=nn0 value='<?= $reg4->dependientes ?>'>
                <input type=hidden name=mt value='<?= $reg4->mt ?>'>
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