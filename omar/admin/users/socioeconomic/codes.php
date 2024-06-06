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

if (isset($_POST['borra']))
   {
   DB::table('socio_economico')->where('mt', $_POST['mt'])->delete();
   }
$add2=$_POST['add2'];
if (isset($_POST['add']) and $add2==0)
   {
    DB::table('socio_economico')->insert([
        'dependientes' => $_POST['dependientes'],
        'bajo_nivel' => $_POST['bajo_nivel'],
        'sobre_nivel' => $_POST['sobre_nivel'],
    ]);
  }

if (isset($_POST['add']) and $add2==1)
   {
    $thisCourse = DB::table('socio_economico')->where('mt', $_POST['mt'])->update([
        'dependientes' => $_POST['dependientes'],
        'bajo_nivel' => $_POST['bajo_nivel'],
        'sobre_nivel' => $_POST['sobre_nivel'],
    ]);
  }

$add2=0;
if (isset($_POST['cambiar'])){
  $reg4 = DB::table('socio_economico')->where('mt', $_POST['mt'])->first();
  $add2=1;
}

$codes = DB::table('socio_economico')->orderBy('dependientes')->get();

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
			<td style="width: 80px"><strong><?= $lang->translation('Dependientes') ?></strong></td>
			<td style="width: 80px"><strong><?= $lang->translation('Bajo nivel de pobreza') ?></strong></td>
			<td style="width: 80px"><strong><?= $lang->translation('Sobre nivel de pobreza') ?></strong></td>
			<td style="width: 100px" colspan="2"><strong><center><?= $lang->translation('Opciones') ?></center></strong></td>
		</tr>
<? 
      foreach ($codes as $code)
              {

    echo '<form method="post">';

?>
		<tr>
			<td style="width: 80px">
			<? echo $code->dependientes ?>
			</td>
			<td style="width: 80px">
			<? echo $code->bajo_nivel ?>
			&nbsp;</td>
			<td style="width: 80px">
			<? echo $code->sobre_nivel ?>
			</td>
			<td style="width: 100px">
			<input class="btn btn-danger" name="borra" style="width: 90px;" type="submit" formnovalidate value="<?= $lang->translation('Borrar') ?>" onclick="return confirmar('&iquest;Est&aacute; seguro que desea eliminar los dependientes?')" />
			</td>
			<td style="width: 100px">
            <input class="btn btn-primary" name="cambiar" style="width: 90px" type="submit" formnovalidate value="<?= $lang->translation('Editar') ?>" />
			</td>
		</tr>
<?
	echo "<input type=hidden name=nn  value='$code->dependientes'>";
//	echo "<input type=hidden name=nn1 value='$row2[1]'>";
	echo "<input type=hidden name=mt value='$code->mt'>";
	echo "<input type=hidden name=add2 value='$add2'>";
echo '</form>';
       }
?>
		<tr>
			<td style="width: 80px"><strong><?= $lang->translation('Dependientes') ?></strong></td>
			<td style="width: 80px"><strong><?= $lang->translation('Bajo nivel de pobreza') ?></strong></td>
			<td style="width: 80px"><strong><?= $lang->translation('Sobre nivel de pobreza') ?></strong></td>
			<td style="width: 100px" colspan="2"><strong><center><?= $lang->translation('Opciones') ?></center></strong></td>
		</tr>
<?
    echo '<form method="post">';
?>		<tr>
			<td style="width: 80px">
			<input maxlength="2" name="dependientes" size="2" type="text" required value="<? echo $reg4->dependientes ?>" /></td>
			<td style="width: 80px">
			<input maxlength="10" name="bajo_nivel" size="12" style="width: 190px" type="text" required placeholder="999999.99" value="<? echo $reg4->bajo_nivel ?>"></td>
			<td style="width: 80px">
			<input id="ex-91" name="sobre_nivel" class="text" size="12" type="text" style="width: 190px" maxlength="10" required placeholder="999999.99" value="<? echo $reg4->sobre_nivel ?>" /></td>
			<td style="width: 100px">
			<strong>
<? 
	echo "<input type=hidden name=nn0  value='$reg4->dependientes'>";
//	echo "<input type=hidden name=nn11 value='$reg4[1]'>";
	echo "<input type=hidden name=mt value='$reg4->mt'>";
	echo "<input type=hidden name=add2 value='$add2'>";
?>

			<input class="btn btn-primary" name="add" style="width: 90px" type="submit" value="<?= $lang->translation('Grabar') ?>" /></strong></td>
			<td style="width: 100px">
			&nbsp;</td>
		</tr>
<? 
echo '</form>';
?>


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