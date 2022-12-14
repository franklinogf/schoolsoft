<?
session_start();
$usua=$_SESSION['usua1'];
$id=$_SESSION['id1'];
if ($usua == "")
   {
   exit;
   }

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<script type="text/javascript" src="calendar.js"></script>
<script type="text/javascript" src="calendar-setup.js"></script>
<script type="text/javascript" src="lang/calendar-es.js"></script>
<style type="text/css"> @import url("calendar-win2k-cold-1.css"); </style>
<link rel="stylesheet" type="text/css" href="../../jv/botones.css" />

<title></title>
<script language="Javascript">
document.oncontextmenu = function(){return false}
</script>

<style type="text/css">
.style2 {
	background-color: #FFFFCC;
}
.style3 {
	background-color: #FFFFCC;
	text-align: center;
}
.style4 {
	background-color: #CCCCCC;
	text-align: center;
}
.style5 {
	text-align: center;
}
.style7 {
	background-color: #FFFFCC;
	text-align: left;
}
.style9 {
	text-align: center;
	font-size: medium;
}
.style10 {
	background-color: #FFFFCC;
	text-align: right;
}
.style11 {
	border-right-style: solid;
	border-bottom-style: solid;
}
</style>
</head>

<body>
<?
$grado=$_POST['grado'];
include('../control.php');
$aa="ALTER TABLE `asispp` ADD `curso` VARCHAR(8) NOT NULL AFTER `p8`";
mysql_query($aa);

$data6 = "select * from colegio where usuario = 'administrador'";
$tabla6 = mysql_query($data6, $con) or die ("problema con query0") ;
$reg6 = mysql_fetch_row($tabla6);

if (isset($_POST['guar'])){
for ($a=1;$a<=$_POST[num];$a++)
 {
 $n1 = 'est('.$a.',1)';
 $n2 = 'est('.$a.',2)';
 $n3 = 'est('.$a.',3)';
 $n4 = 'est('.$a.',4)';
 $n5 = 'est('.$a.',5)';

 $n6 = 'est('.$a.',6)';
 $n7 = 'est('.$a.',7)';
 $n8 = 'est('.$a.',8)';
 $n9 = 'est('.$a.',9)';
 $n10 = 'est('.$a.',10)';
 $n11 = 'est('.$a.',11)';
 $n12 = 'est('.$a.',12)';
 $n13 = 'est('.$a.',13)';
 $n14 = 'est('.$a.',14)';
 $n15 = 'est('.$a.',15)';

 IF ($_POST[dia] < 10)
    {
    list($me,$di) = explode("0",$_POST[dia]);
    $dia='d'.$di;
    }
  ELSE
    {$dia='d'.$_POST[dia];}
  $q = "update asisdia set ".$dia."='$_POST[$n4]' 
  where ss = '$_POST[$n1]' AND grado='$_POST[$n2]' AND mes='$_POST[mes]'";
  mysql_query($q, $db) or die ("problema con query 0");
  $pp = $_POST[$n6].$_POST[$n7].$_POST[$n8].$_POST[$n9].$_POST[$n10].$_POST[$n11].$_POST[$n14].$_POST[$n15];
  $cur='';
  if ($reg6[69]=='3')
     {
     $cur=$_POST['grado'];
     }

  if ($_POST[$n4] > 0 or !empty($pp))
     {
     $sql="insert into asispp (ss, grado, year, fecha, p1, p2, p3, p4, p5, p6, codigo, nombre, apellidos, p7, p8, curso) values ('$_POST[$n1]', '$_POST[$n2]', '$_POST[year]', '$_POST[fecha_ini]', '$_POST[$n6]', '$_POST[$n7]', '$_POST[$n8]', '$_POST[$n9]', '$_POST[$n10]', '$_POST[$n11]', '$_POST[$n4]', '$_POST[$n12]', '$_POST[$n13]', '$_POST[$n14]', '$_POST[$n15]', '$cur')";
     mysql_query($sql);
     $sql="update asispp set p7='$_POST[$n14]',p8='$_POST[$n15]',p1='$_POST[$n6]', p2='$_POST[$n7]', p3='$_POST[$n8]', p4='$_POST[$n9]', p5='$_POST[$n10]', p6='$_POST[$n11]', codigo='$_POST[$n4]', nombre='$_POST[$n12]', apellidos='$_POST[$n13]' where grado = '$_POST[$n2]' AND year='$_POST[year]' AND ss='$_POST[$n1]' AND fecha='$_POST[fecha_ini]'";
     mysql_query($sql);
     }
  else
     {
     $sql="delete from asispp where grado = '$_POST[$n2]' AND year='$_POST[year]' AND ss='$_POST[$n1]' AND fecha='$_POST[fecha_ini]'";
     mysql_query($sql);
     }
  
 }

}
list($anyo,$mes,$dia) = explode("-",$_POST[fecha_ini]);

$sSQL="Select DISTINCT grado From year where year='$reg6[43]' Order By grado";

if ($reg6[69]=='3')
   {
   $sSQL="Select DISTINCT curso, descripcion From padres where id='$id' and year='$reg6[43]' and baja='' Order By curso";
   }
$tabla11 = mysql_query($sSQL, $dbh) or die ("problema con query") ;


  $data2 = "select * from profesor where usuario = '$usua'";
  $tabla2 = mysql_query($data2, $con) or die ("problema con query2") ;
  $reg2 = mysql_fetch_row($tabla2);
  if ($reg6[69]=='2')
     {
     $data1 = "select * from asisdia where baja='' and grado = '".$_POST['grado']."' AND year='$reg6[43]' AND mes='$mes' ORDER BY apellidos, nombre";
     }
  else
     {
     if ($reg6[69]=='3')
        {
        $data1 = "select * from padres where id='$id' and curso = '".$_POST['grado']."' AND year='$reg6[43]' ORDER BY apellidos, nombre";
        }
     else
        {
        $data1 = "select * from asisdia where baja='' and grado = '$reg2[15]' AND year='$reg6[43]' AND mes='$mes' ORDER BY apellidos, nombre";
        }
     }
  $tabla1 = mysql_query($data1, $con) or die ("problema con query1") ;
  $reg = mysql_fetch_row($tabla1);
  $result=mysql_query($data1);
  echo "<input type=hidden name=grado value=$grado>";

?>

<p class="style9"><strong>Pantalla de Entrada de Asistencia Diaria</strong></p>
<form method="post">
<table align="center" style="width: 39%" cellspacing="0" class="style11">
	<tr>
		<td class="style4"><strong>Fecha de Entrada</strong></td>
	</tr>
	<tr>
		<td class="style3">
		<input type=text id='cal-field-5' name=fecha_ini maxlength=10 size=10 tabindex='3' style="width: 94px" value='<? echo $_POST[fecha_ini]; ?>' /><button type='submit' id='cal-button-5'>...</button></td>
          <script type="text/javascript">
            Calendar.setup({
              inputField    : "cal-field-5",
              button        : "cal-button-5"
            });
          </script>
		
		
	</tr>
	<tr>
		<td class="style3">
<?            if ($reg6[69]=='2')
               {		
//			   echo 'Grado:&nbsp;&nbsp;';
//			   echo "<input maxlength='5' name='grado' size='5' style='width: 43px' type='text' value=$_POST[grado]>";
//			   }
?>

<select name="grado" style="width: 72px">
			<option value="<? echo $_POST['grado'] ?>"><? echo $_POST['grado'] ?></option>
<?
while ($row=mysql_fetch_array($tabla11))
{echo '<option>'.$row["grado"].'</option>';}
}
?>
<?            if ($reg6[69]=='3')
                 {
$gra = $_POST['grado'];

echo '<select name="grado" style="width: 294px">';

while ($row=mysql_fetch_array($tabla11))
      {
      $se1='';
      if ($row[0] == $gra){$se1="selected=''"; echo '111';}
         echo '<option '.$se1.' value="'.$row[0].'">'.$row[0].' '.$row[1].'</option>';
//         }
      }

                 }
?>
				</select>
			</td>
	</tr>
	<tr>
		<td class="style3">
		
			<strong>
		
			<input name="ent" type="submit" value="Entrar" class="myButton" style="width: 100px; height: 28px;" /></strong></td>
	</tr>
</table>
</form>
<? IF ($_POST[fecha_ini]==''){exit;} ?>
<form action="asisentrada.php" method="post">
	<div class="style5">
		<br />
		<table align="center" style="width: 69%" cellspacing="0" class="style11">
			<tr>
				<td class="style4" style="width: 30px; height: 23px;">&nbsp;</td>
				<td class="style4" style="width: 294px; height: 23;"><strong>
				Lista de&nbsp; Estudiantes</strong></td>
				<td class="style4" style="width: 30px; height: 23px;"><strong>Codigo</strong></td>
				<? IF ($reg6[69]=='2'){?>
				<td class="style4" style="width: 30px; height: 23px;"><strong>
				P-1</strong></td>
				<td class="style4" style="width: 30px; height: 23px;"><strong>
				P-2</strong></td>
				<td class="style4" style="width: 30px; height: 23px;"><strong>
				P-3</strong></td>
				<td class="style4" style="width: 30px; height: 23px;"><strong>
				P-4</strong></td>
				<td class="style4" style="width: 30px; height: 23px;"><strong>
				P-5</strong></td>
				<td class="style4" style="width: 30px; height: 23px;"><strong>
				P-6</strong></td>
				<td class="style4" style="width: 30px; height: 23px;"><strong>
				P-7</strong></td>
				<td class="style4" style="width: 30px; height: 23px;"><strong>
				P-8</strong></td>
				<?}?>
			</tr>
<?
$li=0;
$gra = $_POST['grado'];

$d=$dia+4;
while ($row=mysql_fetch_array($result))
{
$data7 = "select * from asispp where ss = '$row[56]' and curso='$gra' and fecha='$_POST[fecha_ini]'";
//echo $data7;
///$tabla7 = mysql_query($data7, $con) or die ("problema con query 01") ;
$tabla7 = mysql_query($data7, $con);
$reg7 = mysql_fetch_row($tabla7);
$d=10;


$li=$li+1;

/*IF ($row[$d]==''){$val='';}
IF ($row[$d]=='1'){$val='Ausente';}
IF ($row[$d]=='2'){$val='Tarde';}
IF ($row[$d]=='3'){$val='Tarde-Excusa';}
IF ($row[$d]=='4'){$val='Excusado';}
IF ($row[$d]=='5'){$val='Cita';}
IF ($row[$d]=='6'){$val='Enfermo';}*/

  echo "<input type=hidden name=est($li,3) value=$row[4]>";
  echo "<input type=hidden name=est($li,12) value='$row[1]'>";
  echo "<input type=hidden name=est($li,13) value='$row[2]'>";
  if ($reg6[69]=='3')
     {
     echo "<input type=hidden name=est($li,1) value=$row[56]>";
     echo "<input type=hidden name=est($li,2) value=$row[6]>";
     echo "<input type=hidden name=grado value=".$gra.">";
     }
  else
     {
     echo "<input type=hidden name=est($li,1) value=$row[0]>";
     echo "<input type=hidden name=est($li,2) value=$row[3]>";
     echo "<input type=hidden name=grado value=".$grado.">";
     }

?>
			<tr>
				<td class="style10" style="width: 30px; height: 24;"><? echo $li ?>&nbsp;&nbsp; </td>
				<td class="style7" style="width: 80%; height: 24px;"><? echo utf8_decode($row['apellidos'].' '.$row['nombre'])?></td>
				<td class="style3" style="width: 20%; height: 24px">
				<? echo "<select name=est($li,4) style='width: 125px'>" ?>
				<option></option>

<?            if ($reg6[69]=='3')
                 {

?>
				<option <?php echo ($reg7[$d] == '1') ? 'selected=""' : '' ?> value="1">Ausencia-situación en el hogar</option>
				<option <?php echo ($reg7[$d] == '2') ? 'selected=""' : '' ?> value="2">Ausencia-determinación del hogar(viaje)</option>
				<option <?php echo ($reg7[$d] == '3') ? 'selected=""' : '' ?> value="3">Ausencia-actividad con padres(open house)</option>
				<option <?php echo ($reg7[$d] == '4') ? 'selected=""' : '' ?> value="4">Ausencia-enfermedad</option>
				<option <?php echo ($reg7[$d] == '5') ? 'selected=""' : '' ?> value="5">Ausencia-cita</option>
				<option <?php echo ($reg7[$d] == '6') ? 'selected=""' : '' ?> value="6">Ausencia-actividad educativa del colegio</option>
				<option <?php echo ($reg7[$d] == '7') ? 'selected=""' : '' ?> value="7">Ausencia-sin excusa del hogar</option>
				<option <?php echo ($reg7[$d] == '8') ? 'selected=""' : '' ?> value="8">Tardanza-sin excusa del hogar</option>
				<option <?php echo ($reg7[$d] == '9') ? 'selected=""' : '' ?> value="9">Tardanza-situación en el hogar</option>
				<option <?php echo ($reg7[$d] == '10') ? 'selected=""' : '' ?> value="10">Tardanza-problema en la transportación</option>
				<option <?php echo ($reg7[$d] == '11') ? 'selected=""' : '' ?> value="11">Tardanza-enfermedad</option>
				<option <?php echo ($reg7[$d] == '12') ? 'selected=""' : '' ?> value="12">Tardanza-cita</option>
<?  
   }
else
   {

?>

				<option <?php echo ($reg7[$d] == '1') ? 'selected=""' : '' ?> value="1">Ausencia-situación en el hogar</option>
				<option <?php echo ($reg7[$d] == '2') ? 'selected=""' : '' ?> value="2">Ausencia-determinación del hogar(viaje)</option>
				<option <?php echo ($reg7[$d] == '3') ? 'selected=""' : '' ?> value="3">Ausencia-actividad con padres(open house)</option>
				<option <?php echo ($reg7[$d] == '4') ? 'selected=""' : '' ?> value="4">Ausencia-enfermedad</option>
				<option <?php echo ($reg7[$d] == '5') ? 'selected=""' : '' ?> value="5">Ausencia-cita</option>
				<option <?php echo ($reg7[$d] == '6') ? 'selected=""' : '' ?> value="6">Ausencia-actividad educativa del colegio</option>
				<option <?php echo ($reg7[$d] == '7') ? 'selected=""' : '' ?> value="7">Ausencia-sin excusa del hogar</option>
				<option <?php echo ($reg7[$d] == '8') ? 'selected=""' : '' ?> value="8">Tardanza-sin excusa del hogar</option>
				<option <?php echo ($reg7[$d] == '9') ? 'selected=""' : '' ?> value="9">Tardanza-situación en el hogar</option>
				<option <?php echo ($reg7[$d] == '10') ? 'selected=""' : '' ?> value="10">Tardanza-problema en la transportación</option>
				<option <?php echo ($reg7[$d] == '11') ? 'selected=""' : '' ?> value="11">Tardanza-enfermedad</option>
				<option <?php echo ($reg7[$d] == '12') ? 'selected=""' : '' ?> value="12">Tardanza-cita</option>

<?
   echo "<input type=hidden name=est($li,1) value=$row[0]/>";
   }
?>
				</select></td>
				<? IF ($reg6[69]=='2'){
				     $data11 = "select * from asispp where grado = '$_POST[grado]' AND year='$reg6[43]' AND ss='$row[0]' AND fecha='$_POST[fecha_ini]'";
                     $tabla11 = mysql_query($data11, $con) or die ("problema con query1") ;
                     $rpp = mysql_fetch_row($tabla11);

				?>
				<td class="style3" style="height: 24px">
				<? echo "<input maxlength='1' name=est($li,6) size='1' style='width: 23px' type='text' value=$rpp[4]>" ?> </td>
				<td class="style3" style="height: 24px">
				<? echo "<input maxlength='1' name=est($li,7) size='1' style='width: 23px' type='text' value=$rpp[5]>" ?> </td>
				<td class="style3" style="height: 24px">
				<? echo "<input maxlength='1' name=est($li,8) size='1' style='width: 23px' type='text' value=$rpp[6]>" ?> </td>
				<td class="style3" style="height: 24px">
				<? echo "<input maxlength='1' name=est($li,9) size='1' style='width: 23px' type='text' value=$rpp[7]>" ?> </td>
				<td class="style3" style="height: 24px">
				<? echo "<input maxlength='1' name=est($li,10) size='1' style='width: 23px' type='text' value=$rpp[8]>" ?> </td>
				<td class="style3" style="height: 24px">
				<? echo "<input maxlength='1' name=est($li,11) size='1' style='width: 23px' type='text' value=$rpp[9]>" ?> </td>
				<td class="style3" style="height: 24px">
				<? echo "<input maxlength='1' name=est($li,14) size='1' style='width: 23px' type='text' value=$rpp[14]>" ?> </td>
				<td class="style3" style="height: 24px">
				<? echo "<input maxlength='1' name=est($li,15) size='1' style='width: 23px' type='text' value=$rpp[15]>" ?> </td>
				<?}?>
			</tr>
<? }
  echo "<input type=hidden name=num value=".$li.">";
  echo "<input type=hidden name=dia value=".$dia.">";
//  echo "<input type=hidden name=grado value=".$grado.">";
  echo "<input type=hidden name=mes value=".$mes.">";
  echo "<input type=hidden name=year value=".$reg6[43].">";
  echo "<input type=hidden name=fecha_ini value=".$_POST[fecha_ini].">";

?>

			<tr>
				<td class="style2" style="width: 30px">&nbsp;</td>
				<td class="style2" style="width: 294px">&nbsp;</td>
				<td class="style3">&nbsp;</td>
				<? IF ($reg6[69]=='2'){?>
				<td class="style2">&nbsp;</td>
				<td class="style2">&nbsp;</td>
				<td class="style2">&nbsp;</td>
				<td class="style2">&nbsp;</td>
				<td class="style2">&nbsp;</td>
				<td class="style2">&nbsp;</td>
				<td class="style2">&nbsp;</td>
				<td class="style2">&nbsp;</td>
				<?}?>
			</tr>
		</table>
		<br />
		<strong>
		<input class="myButton" name="guar" style="width: 100px; height: 28px;" type="submit" value="Grabar" /></strong><br />
	</div>
</form>
</body>

</html>
