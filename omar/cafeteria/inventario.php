<?php

if(isset($_POST['salir']))
  {
  include('menu.php');
  exit;
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="en-us" http-equiv="Content-Language" />
<meta content="text/html; charset=windows-1252" http-equiv="Content-Type" />
<title>Inventario Cafetería</title>
<script language="Javascript" type="text/javascript">
function confirmar ( mensaje ) {
return confirm( mensaje );
}
document.oncontextmenu = function(){return false}
function formsum(){

var form = document.getElementById("pan_barra");

document.getElementById("barra").addEventListener("click", function () {
  form.submit();
});
}

</script> 
<style type="text/css">
.style1 {
	text-align: center;
	background-color: #CCCCCC;
}
.style2 {
	background-color: #FFFFCC;
}
.style3 {
	text-align: center;
	font-size: large;
}
.style4 {
	text-align: center;
}
.style5 {
	text-align: left;
	font-size: large;
}
.style7 {
	border-right-style: solid;
	border-bottom-style: solid;
}
.style9 {
	background-color: #CCCCCC;
	font-size: large;
}
.style10 {
	background-color: #CCCCCC;
	font-size: large;
	text-align: center;
}
</style>
<link href="../../jv/botones.css" rel="stylesheet" type="text/css" />
</head>

<body>

<?php
if(isset($_POST['salir']))
  {
  include('menu.php');
  exit;
  }


include('../control.php');

if(isset($_POST['guardar']))
  {
  $ssql = "insert into inventario(id2, articulo, precio, cbarra, cantidad, minimo) values('" . $_POST['id2'] . "', '" . $_POST['articulo'] . "', '" . $_POST['precio']."', '".$_POST['cbarra']."', '".$_POST['cantidad']."', '".$_POST['minimo']."')";
  if ($_POST['add']==1)
     {
     mysql_query($ssql);
     }
  $ssqlb="update inventario set minimo='".$_POST['minimo']."',cbarra='".$_POST['cbarra']."',id2='".$_POST['id2']."',articulo='".$_POST['articulo']."',precio='".$_POST['precio']."',cantidad='".$_POST['cantidad']."' where id='".$_POST['id']."'";
  mysql_query($ssqlb); 
  }

if (isset($_POST['barra']))
   {
   $ssqla = "select * from inventario where cbarra='".$_POST['barra']."'";
   $rssqla = mysql_query($ssqla);
   $data = mysql_fetch_array($rssqla);
   //echo $ssqla;
   }   

if (isset($_POST['buscar']))
   {
   $ssqla = "select * from inventario where id='".$_POST['inv']."'";
   $rssqla = mysql_query($ssqla);
   $data = mysql_fetch_array($rssqla);
   //echo $ssqla;
   }
   
   
if (isset($_POST['borrar']))
   {
   $ssqlc="delete from inventario where id='".$_POST['id']."'";
   //$ssqlc="delete from inventario where id=$id";
   mysql_query($ssqlc); 
   }
?>

<p class="style3"><strong>&nbsp;Pantalla para guardar artículos de cafetería</strong></p>
<p class="style5">&nbsp;</p>
<form id="pan_barra" name="pan_barra" action="" method="post">
<table style="width: 40%" align="center" cellpadding="2" cellspacing="0" class="style7">
	<tr>
		<td class="style9">Selección código barra</td>
		<td class="style2">
		<input maxlength="20" name="barra" size="30" type="text" onclick="return formsum(); return true" /></td>
		<td class="style2">
			<input class="myButton" name="buscarbarra" style="width: 142px; height: 25px;" type="submit" value="Buscar"/></td>
	</tr>
	<tr>
		<td class="style9">Selección artículo</td>
		<td class="style2">
	    <select name="inv" style="width: 149px; height: 20px">
	    <?php
       $mostrar = "select * from inventario order by articulo";
       $rsmostrar = mysql_query($mostrar);

       while ($fila = mysql_fetch_array($rsmostrar))
       {
       echo '<option value="'.$fila['id'].'">'.$fila["articulo"].'</option>';
      }
      ?> 
        </select></td>
		<td class="style2">
		<input class="myButton" name="buscar" style="width: 142px; height: 25px;" type="submit" value="Buscar" /></td>
	</tr>
	<tr>
		<td class="style10" colspan="3">
		<input class="myButton" name="anadir" style="width: 142px; height: 25px" type="submit" value="Añadir" />&nbsp;&nbsp;
		<input class="myButton" name="salir" type="submit" value="Salir" style="width: 142px; height: 25px" />
</td>

	</tr>
</table>
</form>

<br/>

<?php
$add=0;
if (isset($_POST['buscar']) || ($_POST['anadir']) || ($_POST['buscarbarra']))
   {

if (isset($_POST['anadir']))
   {$add=1;}


?>
      
<form action="<?php echo $_SERVER["PHP_SELF"]?>" method="post" class="style5">
	<table align="center" cellpadding="2" cellspacing="0" style="width: 40%" class="style5">
		<tr>
			<td class="style1" colspan="2">Inventario Cafetería</td>
		</tr>
		<tr>
			<td class="style2" style="width: 204px">ID</td>
			<td class="style2" style="width: 11px">
			<input name="id2" type="text" maxlength="8" size="8" value="<?php echo $data['id2']; ?>" style="height: 22px" /></td>
		</tr>
		<tr>
			<td class="style2" style="width: 204px">Artículo</td>
			<td class="style2" style="width: 11px">
			<input name="articulo" type="text" maxlength="50" size="45" value="<?php echo $data['articulo']; ?>" /></td>
		</tr>
		<tr>
			<td class="style2" style="width: 204px">Precio</td>
			<td class="style2" style="width: 11px">
			<input name="precio" type="text" maxlength="8" size="8" value="<?php echo $data['precio']; ?>" /></td>
		</tr>
		<tr>
			<td class="style2" style="width: 204px">Cantidad</td>
			<td class="style2" style="width: 11px">
			<input name="cantidad" type="text" maxlength="5" size="5" value="<?php echo $data['cantidad']; ?>" /></td>
		</tr>
		<tr>
			<td class="style2" style="width: 204px">Cantidad Minimo:</td>
			<td class="style2" style="width: 11px">
			<input name="minimo" type="text" maxlength="5" size="5" value="<?php echo $data['minimo']; ?>" /></td>
		</tr>
		<tr>
			<td class="style2" style="width: 204px">Código Barra&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
			<td class="style2" style="width: 11px; height: 50px;">
			<input name="cbarra" type="text" maxlength="20" size="30" value="<?php echo $data['cbarra']; ?>" />&nbsp;&nbsp;
			<img alt="" height="64" src="354928412.gif" width="65" /><br />
			<br />
			<br />
			</td>
		</tr>
	</table>
<?
        echo "<input type=hidden name=id value=".$data['id'].">";
        echo "<input type=hidden name=add value=".$add.">";

?>

	<div class="style4">
		<br />
		<input class="myButton" name="guardar" style="width: 142px; height: 25px;" type="submit" value="Guardar" /> 
		<?php

		if (isset($_POST['buscar']) || ($_POST['buscarbarra']))
		{
        ?>		
		<input class="myButton" name="borrar" style="width: 142px; height: 25px" type="submit" value="Borrar" onclick="return confirmar('Está seguro que desea borrar el articulo?')" /></div>		     
        <?php
        }
        ?>

</form>

<?php

}

?>

</body>

</html>
