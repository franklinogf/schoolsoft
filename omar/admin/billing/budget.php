<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Teacher;

Session::is_logged();
$lang = new Lang([
    ["Códigos de Presupuesto", "Budget codes"],
    ['Grabar', 'Save'],
    ['Código', 'Code'],
    ['Editar', 'Edit'],
    ['Añadir', 'Add'],
    ['Borrar', 'Delete'],
    ['Debe de llenar todos los campos', 'You must fill all fields'],
    ['Lista de codigos', 'Codes list'],
    ['Descripción', 'Description'],
    ['Precio', 'Price'],
    ['Cantidad', 'Amount'],
    ['Opciones', 'Options'],
]);

$school = new School(Session::id());
$year = $school->info('year2');
$add2=0;

if (isset($_POST['borra']))
   {
   DB::table('presupuesto')->where('mt', $_POST['mt'])->delete();
   }
if ($add2==0)
   {$add2=$_GET['add2'];}
if (isset($_POST['add']) and $add2==0)
   {
    DB::table('presupuesto')->insert([
        'codigo' => $_POST['codigo'],
        'descripcion' => $_POST['descripcion'],
        'cantidad' => $_POST['bajo_nivel'],
        'costo' => $_POST['sobre_nivel'],
        'year' => $year,
    ]);
  }

if (isset($_POST['add']) and $add2==1)
   {
    $thisCourse = DB::table('presupuesto')->where('mt', $_POST['mt'])->update([
        'codigo' => $_POST['codigo'],
        'descripcion' => $_POST['descripcion'],
        'cantidad' => $_POST['bajo_nivel'],
        'costo' => $_POST['sobre_nivel'],
    ]);
  }

$add2=0;
if (isset($_POST['cambiar'])){
  $reg4 = DB::table('presupuesto')->where('mt', $_POST['mt'])->first();
  $add2=1;
}

$codes = DB::table('presupuesto')->where('year', $year)->orderBy('codigo')->get();
//var_dump($codes);
//exit;
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<script language="JavaScript">
function confirmar ( mensaje ) {
return confirm( mensaje );
}
  function supports_input_placeholder()
  {
    var i = document.createElement('input');
    return 'placeholder' in i;
  }

  if(!supports_input_placeholder()) {
    var fields = document.getElementsByTagName('INPUT');
    for(var i=0; i < fields.length; i++) {
      if(fields[i].hasAttribute('placeholder')) {
        fields[i].defaultValue = fields[i].getAttribute('placeholder');
        fields[i].onfocus = function() { if(this.value == this.defaultValue) this.value = ''; }
        fields[i].onblur = function() { if(this.value == '') this.value = this.defaultValue; }
      }
    }
  }
document.oncontextmenu = function(){return false}
</script> 

<head>
    <?php
    $title = $lang->translation('Códigos de Presupuesto');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Códigos de Presupuesto') ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">




<form method="post">
	<table align="center" cellspacing="0" style="width: 71%">
		<tr>
			<td style="width: 50px"><strong><?= $lang->translation('Código') ?></strong></td>
			<td style="width: 500px"><strong><?= $lang->translation('Descripción') ?></strong></td>
			<td style="width: 50px"><strong><?= $lang->translation('Cantidad') ?></strong></td>
			<td style="width: 50px"><strong><?= $lang->translation('Precio') ?></strong></td>
			<td style="width: 100px" colspan="2"><strong><center><?= $lang->translation('Opciones') ?></center></strong></td>
		</tr>
<?php foreach ($codes as $code): ?>
      <form method="post">
		<tr>
			<td style="width: 50px">
			<?= $code->codigo ?>
			</td>
			<td style="width: 100px">
			<?= $code->descripcion ?>
			&nbsp;</td>
			<td style="width: 50px">
			<?= number_format($code->cantidad,2) ?>
			</td>
			<td style="width: 50px">
			<?= number_format($code->costo,2) ?>
			</td>
			<td style="width: 100px">
			<input class="btn btn-danger" name="borra" style="width: 90px;" type="submit" formnovalidate value="<?= $lang->translation('Borrar') ?>" onclick="return confirmar('&iquest;Est&aacute; seguro que desea eliminar los dependientes?')" />
			</td>
			<td style="width: 100px">
            <input class="btn btn-primary" name="cambiar" style="width: 90px" type="submit" formnovalidate value="<?= $lang->translation('Editar') ?>" />
			</td>
		</tr>
<input type=hidden name=nn  value='<?= $code->codigo ?>'>
<input type=hidden name=mt value='<?= $code->mt ?> '>
<input type=hidden name=add2 value='<?= $add2 ?>'>
</form>
      <?php endforeach ?>
		<tr>
			<td style="width: 50px"><strong><?= $lang->translation('Código') ?></strong></td>
			<td style="width: 100px"><strong><?= $lang->translation('Descripción') ?></strong></td>
			<td style="width: 50px"><strong><?= $lang->translation('Cantidad') ?></strong></td>
			<td style="width: 50px"><strong><?= $lang->translation('Precio') ?></strong></td>
			<td style="width: 100px" colspan="2"><strong><center><?= $lang->translation('Opciones') ?></center></strong></td>
		</tr>
<?php if ($add2==0): ?>
    <form method="post" action="budget.php?add2=<?= $add2 ?>">
		<tr>
			<td style="width: 50px">
			<input maxlength="2" name="codigo" size="2" type="text" required value="<?= $reg4->codigo ?? '' ?>" /></td>
			<td style="width: 100px">
			<input maxlength="50" name="descripcion" size="30" type="text" required value="<?= $reg4->descripcion ?? '' ?>" /></td>
			<td style="width: 50px">
			<input maxlength="10" name="bajo_nivel" size="10" style="width: 80px" type="text" placeholder="999.99" value="<?= $reg4->cantidad ?? '' ?>"></td>
			<td style="width: 50px">
			<input id="ex-91" name="sobre_nivel" class="text" size="10" type="text" style="width: 80px" maxlength="10" placeholder="999.99" value="<?= $reg4->costo ?? '' ?>" /></td>
			<td style="width: 100px" colspan="2">
			<strong><center>
 
	<input type=hidden name=nn0  value='<?= $reg4->codigo ?> '>
	<input type=hidden name=mt value='<?= $reg4->mt ?> '>
	<input type=hidden name=add2 value='<?= $add2 ?> '>
			<input class="btn btn-primary" name="add" style="width: 90px" type="submit" value="<?= $lang->translation('Grabar') ?>" /></center></strong></td>
		</tr>
</form>
<?php endif ?>
<?php if ($add2==1): ?>
    <form method="post" action="budget.php?add2=<?= $add2 ?>">
		<tr>
			<td style="width: 50px">
			<input maxlength="2" name="codigo" size="2" type="text" required value="" /></td>
			<td style="width: 100px">
			<input maxlength="50" name="descripcion" size="30" type="text" required value="" /></td>
			<td style="width: 50px">
			<input maxlength="10" name="bajo_nivel" size="10" style="width: 80px" type="text" placeholder="999.99" value=""></td>
			<td style="width: 50px">
			<input id="ex-91" name="sobre_nivel" class="text" size="10" type="text" style="width: 80px" maxlength="10" placeholder="999.99" value="" /></td>
			<td style="width: 100px" colspan="2">
			<strong><center>
 
	<input type=hidden name=nn0  value='<?= $reg4->codigo ?> '>
	<input type=hidden name=mt value='<?= $reg4->mt ?> '>
	<input type=hidden name=add2 value='<?= $add2 ?> '>
			<input class="btn btn-primary" name="add" style="width: 90px" type="submit" value="<?= $lang->translation('Grabar') ?>" /></center></strong></td>
		</tr>
</form>
<?php endif ?>
		<tr>
			<td class="style7" style="width: 80px">&nbsp;</td>
			<td class="style7" style="width: 80px">&nbsp;</td>
			<td class="style7" style="width: 80px">&nbsp;</td>
			<td class="style1" style="width: 100px">&nbsp;</td>
			<td class="style1" style="width: 100px">&nbsp;</td>
		</tr>
	</table>
	<br>
	<br />
	<div class="style5">
		<br><br><br><br><br />
	</div>
</form>







        </div>



    </div>
    <?php
    $jqMask = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>