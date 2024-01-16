<?
session_start();
$id=$_SESSION['id1'];
$usua=$_SESSION['usua1'];

if ($usua == "")
   {
   exit;
   }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="en-us" http-equiv="Content-Language" />
<meta content="text/html; charset=windows-1252" http-equiv="Content-Type" />
<title>Untitled 1</title>
<link rel="stylesheet" type="text/css" href="../../jv/botones.css" />
<script language="JavaScript">
document.oncontextmenu = function(){return false}
function confirmar( mensaje ) {
return confirm( mensaje );
}
</script> 
<style type="text/css">
.style1 {
	background-color: #CCCCCC;
	text-align: center;
}
.style2 {
	text-align: center;
	background-color: #FFFFCC;
}
.style4 {
	font-size: x-large;
	text-align: center;
}
.style7 {
	font-weight: bold;
	background-color: #FFFFCC;
	text-align: center;
}
</style>
</head>
<?

list($ape,$nom,$gr) = explode(", ",$_POST['nombre']);
$ss=$_POST['nombre'];
include('../control.php');
$consult1 = "select * from colegio where usuario = '$usua'";
$resultad1 = mysql_query($consult1);
$reg=mysql_fetch_array($resultad1);

if(isset($_POST['addes']))
  {
  $ad_est=$_POST['nombre'];
  $ad_est=$_POST['nombre'];
  list($ape,$nom,$gr) = explode(", ",$ad_est);

  $sSQL1="select * from year where apellidos='$ape' AND nombre='$nom' AND grado='$gr' AND year='$reg[116]' Order By apellidos";
  $sSQL1="select * from year where ss='$ss' AND year='$reg[116]' Order By apellidos";
  $result33=mysql_query($sSQL1);
  $row22=mysql_fetch_array($result33);
  $ape=$row22[4];
  $nom=$row22[3];
  $gr=$row22[2];

  list($cur,$des,$id) = explode(", ",$_POST['ad_est']);

//  $sSQL5="select * from padres where apellidos='$ape' AND nombre='$nom' AND grado='$gr' AND year='$reg[116]' and ss='$row22[0]' Order By orden DESC LIMIT 1";
//  $result55=mysql_query($sSQL5);
//  $num_cur = mysql_num_rows($result55);
//  $num_cur2 = $num_cur + 1;
   $num_cur2 = $_POST['ord'];
   $consult1 = "select * from profesor where id='$id'";
   $resultad1 = mysql_query($consult1);
   $row4=mysql_fetch_array($resultad1);

   $consult2 = "select * from cursos where curso = '$cur' AND id='$id'";
   $resultad2 = mysql_query($consult2);
   $row5=mysql_fetch_array($resultad2);

   $clase='';
   if ($_POST['clase']==2){$clase='2';}

   $query = "insert into padres (id,nombre,apellidos,descripcion,grado,curso,credito,ss,year,id2,profesor,email,desc2,orden,ava,valor,verano,hora,dias)
   values ('$id','$nom','$ape','$des','$gr','$cur','$row5[5]','$row22[0]','$reg[116]','$row22[5]','".$row4[1].' '.$row4[2]."','$row5[6]','$row5[4]','$num_cur2','$row5[13]','$row5[14]','$clase','$row5[7]','$row5[9]')";
   $result1 = mysql_query($query);
   $query = "insert into padres2 (id,nombre,apellidos,descripcion,grado,curso,profesor,ss,year,verano)
   values ('$id','$nom','$ape','$des','$gr','$cur','".$row4[1].' '.$row4[2]."','$row22[0]','$reg[116]','$clase')";
   $result1 = mysql_query($query);
   $query = "insert into padres3 (id,nombre,apellidos,descripcion,grado,curso,profesor,ss,year,verano)
   values ('$id','$nom','$ape','$des','$gr','$cur','".$row4[1].' '.$row4[2]."','$row22[0]','$reg[116]','$clase')";
   $result1 = mysql_query($query);
   $query = "insert into padres4 (id,nombre,apellidos,descripcion,grado,curso,profesor,ss,year,verano)
   values ('$id','$nom','$ape','$des','$gr','$cur','".$row4[1].' '.$row4[2]."','$row22[0]','$reg[116]','$clase')";
   $result1 = mysql_query($query);
   $query = "insert into padres5 (id,nombre,apellidos,descripcion,grado,curso,profesor,ss,year,verano)
   values ('$id','$nom','$ape','$des','$gr','$cur','".$row4[1].' '.$row4[2]."','$row22[0]','$reg[116]','$clase')";
   $result1 = mysql_query($query);
   $query = "insert into padres6 (id,nombre,apellidos,descripcion,grado,curso,profesor,ss,year,verano)
   values ('$id','$nom','$ape','$des','$gr','$cur','".$row4[1].' '.$row4[2]."','$row22[0]','$reg[116]','$clase')";
   $result1 = mysql_query($query);
  }

if(isset($_POST['Borrar']))
  {
  list($gra,$dd,$id) = explode(", ",$_POST['borr_cursos1']);
  $ad_est=$_POST['nombre'];
  list($ape,$nom,$gr) = explode(", ",$ad_est);

  $q = "delete FROM padres2 where ss='$ss' AND curso='$gra' AND year='$reg[116]'";
  mysql_query($q, $db);
  $q = "delete FROM padres3 where ss='$ss' AND curso='$gra' AND year='$reg[116]'";
  mysql_query($q, $db);
  $q = "delete FROM padres4 where ss='$ss' AND curso='$gra' AND year='$reg[116]'";
  mysql_query($q, $db);
  $q = "delete FROM padres5 where ss='$ss' AND curso='$gra' AND year='$reg[116]'";
  mysql_query($q);
  $q = "delete FROM padres6 where ss='$ss' AND curso='$gra' AND year='$reg[116]'";
  mysql_query($q);
  $q = "delete FROM padres where ss='$ss' AND curso='$gra' AND year='$reg[116]'";
  mysql_query($q, $db);
  }

if(isset($_POST['Borrar4']))
  {
  list($gra,$dd,$id) = explode(", ",$_POST['borr_cursos1']);
  $ad_est=$_POST['nombre'];
  list($ape,$nom,$gr) = explode(", ",$ad_est);

  $q = "delete FROM padres2 where ss='$ss' AND year='$reg[116]'";
  mysql_query($q, $db);
  $q = "delete FROM padres3 where ss='$ss' AND year='$reg[116]'";
  mysql_query($q, $db);
  $q = "delete FROM padres4 where ss='$ss' AND year='$reg[116]'";
  mysql_query($q, $db);
  $q = "delete FROM padres5 where ss='$ss' AND year='$reg[116]'";
  mysql_query($q);
  $q = "delete FROM padres6 where ss='$ss' AND year='$reg[116]'";
  mysql_query($q);
  $q = "delete FROM padres where ss='$ss' AND year='$reg[116]'";
  mysql_query($q, $db);
  }




$consult2 = "select * from year where year = '$reg[116]' Order By apellidos, nombre";
$resultad2 = mysql_query($consult2);

$sSQL2="Select * From cursos where year='$reg[116]' Order By curso";
$result3=mysql_query($sSQL2);

$sSQL3="Select * From padres where year='$reg[116]' AND apellidos = '$ape' AND nombre = '$nom' Order By orden";
$sSQL3="Select * From padres where year='$reg[116]' AND ss = '$ss' Order By orden";
$result11=mysql_query($sSQL3);

?>
<body>

<p class="style4"><strong>Programas Especiales <? echo $reg[116]?></strong></p>
<form method="post">

<table style="width: 47%" align="center">
	<tr>
		<td class="style1" style="height: 30px; width: 387px">
			<select name="nombre" style="width: 330px">
<? 
      echo  '<option>'.'</option>';
while ($row3=mysql_fetch_array($resultad2))
      { $es='';
      if ($ss == $row3[0]){$es='selected=""';}
      echo  '<option '.$es.' value="'.$row3[0].'">'.$row3[4].', '.$row3[3].', '.$row3[2].'</option>';
      }
?>
			</select>
		</td>
		<td class="style2" style="height: 30px"><strong>
		<?
		$s1='';
		if ($_POST['tns']=='Si'){$s1='selected=""';}
		?>
			<select name="tns">
			<option value="No">No Ver Todas</option>
			<option <? echo $s1; ?> value="Si">Si Ver Todas</option>
			</select>&nbsp;
		<input class="myButton" name="buscar" style="width: 100px; height: 26px;" type="submit" value="Buscar" /></strong></td>
	</tr>
</table>
</form>
<?
if(isset($_POST['Borrar']) or isset($_POST['addes']) or isset($_POST['buscar']))
  {}
  else
  {
  exit;
  }
?>
<br>
<form method="post">
	<table align="center" cellpadding="2" cellspacing="0" style="width: 63%">
		<tr>
			<td class="style1" style="height: 20px"></td>
			<td class="style1" style="height: 20px; width: 254px"></td>
			<td class="style1" style="height: 20px"></td>
		</tr>
		<tr>
			<td class="style2">&nbsp;</td>
			<td class="style2" style="width: 254px">&nbsp;</td>
			<td class="style2">&nbsp;</td>
		</tr>
		<tr>
			<td class="style1">
			<select name="ad_est" size="12" tabindex="3" style="width: 311px; height: 255px">
<?
      while ($row2=mysql_fetch_array($result3))
      {echo '<option>'.$row2["curso"].', '.$row2["desc1"].', '.$row2["id"];}
echo '</select></p>';

$cl1='';
$cl2='';
if ($_POST['clase']=='1'){$cl1='selected=""';}
if ($_POST['clase']=='2'){$cl2='selected=""';}
?>
			
			
      </select></td>
			<td class="style7" style="width: 254px">
			<br>
			<input class="myButton" name="addes" type="submit" value="Añadir Curso >>" style="width: 177px; height: 28px;"/><br>
			<br />
			<select name="clase" style="width: 80px">
			<option <? echo $cl1; ?> value="1">Regular</option>
			<option <? echo $cl2; ?> value="2">Verano</option>
			</select><br>
			Posición<br>
<?
  $sSQL5="select * from padres where year='$reg[116]' AND apellidos = '$ape' AND nombre = '$nom' Order By orden DESC LIMIT 1";
  $result55=mysql_query($sSQL5);
  $row2=mysql_fetch_array($result55);
  $nc2 = $row2[136] + 1;
  $o1='';$o2='';$o3='';$o4='';$o5='';$o6='';$o7='';$o8='';$o9='';$o10='';$o11='';$o12='';
  if ($nc2==1){$o1='selected=""';}
  if ($nc2==2){$o2='selected=""';}
  if ($nc2==3){$o3='selected=""';}
  if ($nc2==4){$o4='selected=""';}
  if ($nc2==5){$o5='selected=""';}
  if ($nc2==6){$o6='selected=""';}
  if ($nc2==7){$o7='selected=""';}
  if ($nc2==8){$o8='selected=""';}
  if ($nc2==9){$o9='selected=""';}
  if ($nc2==10){$o10='selected=""';}
  if ($nc2==11){$o11='selected=""';}
  if ($nc2==12){$o12='selected=""';}
?>			
			<select name="ord">
			<option <? echo $o1 ?>>1</option>
			<option <? echo $o2 ?>>2</option>
			<option <? echo $o3 ?>>3</option>
			<option <? echo $o4 ?>>4</option>
			<option <? echo $o5 ?>>5</option>
			<option <? echo $o6 ?>>6</option>
			<option <? echo $o7 ?>>7</option>
			<option <? echo $o8 ?>>8</option>
			<option <? echo $o9 ?>>9</option>
			<option <? echo $o10 ?>>10</option>
			<option <? echo $o11 ?>>11</option>
			<option <? echo $o12 ?>>12</option>
			</select></td>
			<td class="style1">
			<select name="borr_cursos1" size="12" tabindex="3" style="width: 311px; height: 255px">
<?
      while ($row2=mysql_fetch_array($result11))
      {
      $tn='';
      if (!empty($row2["sem1"]) and $_POST['tns']=='No' or !empty($row2["sem2"]) and $_POST['tns']=='No')
         {
         $tn="disabled='disabled'";
         }
      echo '<option '.$tn.' >'.$row2["curso"].', '.$row2["descripcion"].', '.$row2["id"];
      }
echo '</select></p>';

?>
		
      </td>
		</tr>
		<tr>
			<td class="style1">&nbsp;</td>
			<td class="style1" style="width: 254px">&nbsp;</td>
			<td class="style1">
				<input type="submit" value="Borrar Uno" name="Borrar" onclick="return confirmar('&iquest;Está seguro que desea eliminar el curso del Estudiante?')" style="font-size: 1em; font-weight: bold; height: 28px; width: 140px;" class="myButton"/>&nbsp;&nbsp;
				<input type="submit" value="Borrar Todos" name="Borrar4" onclick="return confirmar('&iquest;Está seguro que desea eliminar los cursos completo del el Estudiante?')" style="font-size: 1em; font-weight: bold; width: 140px; height: 27px;" class="myButton"/></td>
		</tr>
	</table>

<?
echo "<input type=hidden name=nombre value='".$_POST['nombre']."'>";
echo "<input type=hidden name=tns value='".$_POST['tns']."'>";

?>
</form>


</body>

</html>
