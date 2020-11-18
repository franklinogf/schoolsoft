<?
session_start();
$id=$_SESSION['id1'];
$usua=$_SESSION['usua1'];
if ($usua == "")
   {
   echo '<br><b><center>El tiempo de 15 minutos a caducado, tardo mucho sin grabar</center></b>';
   exit;
   }
?>
<html>

<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<title>Entrada de Notas</title>
<link rel="stylesheet" type="text/css" href="../../jv/botones.css" />
<script type="text/javascript" src="calendar.js"></script>
<script type="text/javascript" src="calendar-setup.js"></script>
<script type="text/javascript" src="lang/calendar-es.js"></script>
<style type="text/css"> @import url("calendar-win2k-cold-1.css"); 
* {
	margin: 0;
	padding: 0;
	font-family: 'Roboto', Arial, sans-serif;
}
p {
	font-weight: 900;
	font-size: xx-large;
	font: x-large serif;
	text-align: center;
}
div {
	font: x-small serif;
	text-align: center;
}
button {
	padding: 10px;
	background-color: #BECDFE;
	border: none;
}
input[type="text"] {
	font-weight: 400;
}
h1 {
	font-size: 28px;
	font: small serif;
}
h1.chronometerTitle {
	margin-top: 4%;
}


.style1 {
	text-align: right;
}
.style2 {
	color: #F80303;
}
.style3 {
	border-right-style: solid;
	border-bottom-style: solid;
}
.style4 {
	font-size: medium;
}
</style>
<script>
document.oncontextmenu = function(){return false}
var se = 0;
var chronometerInterval = null;
var countdownInterval = null;

var countdowntimer = {
	seconds: 0,
	active: false,
	start: function() {
		if(!this.active) {
			countdownInterval = setInterval(this.update, 1000);
			this.active = true;
		}
	},
	stop: function() {
		if(countdownInterval != null && this.active) {
			clearInterval(countdownInterval);
			this.active = false;
		}
	},
	reset: function() {
		if(this.active) {
			countdowntimer.stop();
		}
		countdowntimer.seconds = 0;
		this.active = false;
		var d2 = new Date(countdowntimer.seconds * 1000);
		document.getElementById("txtcountdown").innerHTML = (0) + ":" + d2.getMinutes() + ":" + d2.getSeconds();
	},
	update: function() {
		if(countdowntimer.seconds != 0) {
			countdowntimer.seconds--;
			var date = new Date(countdowntimer.seconds * 1000);
			document.getElementById("txtcountdown").innerHTML = (0) + ":" + date.getMinutes() + ":" + date.getSeconds();
		}
		if(countdowntimer.seconds <= 30 && countdowntimer.seconds > 0) {

document.getElementById("sound_element").innerHTML= 
"<embed src='../../jv/alerta.mp3' hidden=true autostart=true loop=false>";
		}
		if(countdowntimer.seconds == 0 && se == 0) {

  document.getElementById('Gra').click();

document.getElementById("sound_element").innerHTML= 
"<embed src='../../jv/alerta.mp3' hidden=true autostart=true loop=false>";
			setTimeout("alert('El tiempo está llegando al final, grabe antes que pierda la información o ya es tarde..')", 3000);
         se = 1;
		}else{
//			alert("El tiempo no puede ser negativo o 0");
		}
	},
	set: function(hours, minutes, seconds) {
		var hours = 0;
		var minutes = parseInt(minutes);
		var seconds = parseInt(seconds);

		if(isNaN(hours)) {
			hours = 0;
		}

		if(isNaN(minutes)) {
			minutes = 15;
		}

		if(isNaN(seconds)) {
			seconds = 0;
		}

		countdowntimer.seconds = (hours * 0) + (minutes * 60) + seconds;

		if(countdowntimer.seconds > 0) {
			this.start();
		}else{
			alert("El tiempo no puede ser negativo o 0");
		}
	}
};


</script>

</head>

<body>
<?
if(isset($_POST['Grabar'])){
  include('../control.php');
  include('ins_notas.php');
}

list($curso,$mes) = explode(", ",$_POST[curso]);
date_default_timezone_set("America/puerto_rico");

  if ($_POST[tri] == "Selección" OR $_POST[tra] == "Selección")
  {
     echo "<CENTER>No has seleccionado las opciones para la entradas de notas.<br>Por favor vuelve e inténtalo de nuevo.";
     echo "<body onload='FP_preloadImgs(/*url*/'../../images/button4.gif', /*url*/'../../images/button11.gif')'>";
     echo '<p align="center">';
     echo "<a href='javascript:history.back()'>";
     echo "<img border='0' id='img1' src='../../images/button12.gif' height='26' width='200' alt='Pagina Anterior' onmouseover='FP_swapImg(1,0,/*id*/'img1',/*url*/'../../images/button4.gif')' onmouseout='FP_swapImg(0,0,/*id*/'img1',/*url*/'../../images/button12.gif')' onmousedown='FP_swapImg(1,0,/*id*/'img1',/*url*/'../../images/button11.gif')' onmouseup='FP_swapImg(0,0,/*id*/'img1',/*url*/'../../images/button4.gif')' fp-style='fp-btn: Embossed Capsule 6; fp-font-style: Bold; fp-font-size: 12; fp-transparent: 1; fp-proportional: 0' fp-title='Pagina Anterior'></a></p>";
     exit;
  }

include('../control.php');

mysql_query("ALTER TABLE `profesor` ADD `fechas` BOOLEAN NOT NULL DEFAULT 0");
mysql_query("ALTER TABLE `profesor` ADD `tri` TINYINT(1) NULL AFTER `fechas`");

$data6 = "select * from colegio where usuario = 'administrador'";
$tabla6 = mysql_query($data6, $con) or die ("problema con query0") ;
$reg6 = mysql_fetch_row($tabla6);
if ($_POST[tri]=="Trimestre-1" OR $_POST[tri]=="Verano"){$c=0;$en=78;$ct=82;}
if ($_POST[tri]=="Trimestre-2"){$c=10;$en=79;$ct=83;}
if ($_POST[tri]=="Trimestre-3"){$c=20;$en=80;$ct=84;}
if ($_POST[tri]=="Trimestre-4"){$c=30;$en=81;$ct=85;}
if ($_POST[tra] == "Cond-Asis")
   {
   $sql = "SELECT * from comentarios ORDER BY codigo";
   $res = mysql_query($sql) or die ("problema con query 71") ;
   $cursos = array();
   while ($cur = mysql_fetch_object($res)) {
	     $cursos[] = $cur;
         }
//   $cursos = json_decode(json_encode($cursos));
   }


IF ($_POST[tri]=="Verano")
   {$data1 = "select * from padres where curso = '$curso' AND year='$reg6[43]' AND baja='' AND verano='2' ORDER BY apellidos, nombre";}
 ELSE
   {$data1 = "select * from padres where curso = '$curso' AND year='$reg6[43]' AND baja='' AND verano='' ORDER BY apellidos, nombre";}
  $data2 = "select * from profesor where usuario = '$usua'";
  $data3 = "select * from valores where curso = '$curso' AND trimestre = '$_POST[tri]' AND year='$reg6[43]' AND nivel = '$_POST[tra]'";
  $data4 = "select * from padres2 where curso = '$curso' AND year='$reg6[43]' AND baja='' AND verano='' ORDER BY apellidos, nombre";
  $data5 = "select * from padres3 where curso = '$curso' AND year='$reg6[43]' AND baja='' AND verano='' ORDER BY apellidos, nombre";
  $data7 = "select * from padres4 where curso = '$curso' AND year='$reg6[43]' AND baja='' AND verano='' ORDER BY apellidos, nombre";
  if ($reg6[87] == "SI")
     {
     $data8 = "select * from comentarios ORDER BY code";
     $result8=mysql_query($data8);      
     }

if ($reg6[166]=='SI' and $_POST[tra] == "Trab-Diarios2")
   {
   $data4 = "select * from padres5 where curso = '$curso' AND year='$reg6[43]' AND baja='' AND verano='' ORDER BY apellidos, nombre";
   }
if ($reg6[166]=='SI' and $_POST[tra] == "Trab-Libreta2")
   {
   $data5 = "select * from padres6 where curso = '$curso' AND year='$reg6[43]' AND baja='' AND verano='' ORDER BY apellidos, nombre";
   }


$tabla1 = mysql_query($data1, $con) or die ("problema con query1") ;
$tabla2 = mysql_query($data2, $con) or die ("problema con query2") ;
$tabla3 = mysql_query($data3, $con) or die ("problema con query3") ;
$tabla4 = mysql_query($data4, $con) or die ("problema con query4") ;
$tabla5 = mysql_query($data5, $con) or die ("problema con query5") ;
$tabla7 = mysql_query($data7, $con) or die ("problema con query6") ;
if ($reg6[87] == "SI")
   {$tabla8 = mysql_query($data8, $con) or die ("problema con query7");}

$reg = mysql_fetch_row($tabla1);
$reg2 = mysql_fetch_row($tabla2);
$reg3 = mysql_fetch_row($tabla3);
$resul_valor = mysql_num_rows($tabla3);
$num_resultados = mysql_num_rows($tabla1);
$a2 = $num_resultados;
$result=mysql_query($data1);
if ($_POST[tra] == "Trab-Diarios2" OR $_POST[tra] == "Trab-Libreta" OR $_POST[tra] == "Trab-Libreta2" OR $_POST[tra] == "Trab-Diarios" OR $_POST[tra] == "Pruebas-Cortas" OR $_POST[tra] == "Ex-Final")
   {
   if ($_POST[tra] == "Trab-Diarios" OR $_POST[tra] == "Trab-Diarios2")
      {
      $reg4 = mysql_fetch_row($tabla4);
      $num_resultados2 = mysql_num_rows($tabla4);
      $result2=mysql_query($data4);
      $est2 = array($num_resultados2,50);
      }
   ELSE
      {
      if ($_POST[tra] == "Trab-Libreta" or $_POST[tra] == "Trab-Libreta2")
         {
         $reg4 = mysql_fetch_row($tabla5);
         $num_resultados2 = mysql_num_rows($tabla5);
         $result2=mysql_query($data5);
         $est2 = array($num_resultados2,50);
         }
      ELSE
         {
         $reg4 = mysql_fetch_row($tabla7);
         $num_resultados2 = mysql_num_rows($tabla7);
         $result2=mysql_query($data7);
         $est2 = array($num_resultados2,50);
         }
      }
   }
$est = array($num_resultados,50);

if ($resul_valor==0)
   {
   $query = "insert into valores (curso,trimestre,nivel,year)
   values  ('$curso','$_POST[tri]','$_POST[tra]','$reg6[43]')";
   $result1 = mysql_query($query);

   $resul_valor = mysql_num_rows($tabla3);
   $reg3 = mysql_fetch_row($tabla3);
   }
?>

<script src="js/main.js"></script>
<link rel="stylesheet" href="css/styles.css">
<link href='http://fonts.googleapis.com/css?family=Roboto:400,700' rel='stylesheet' type='text/css'>
	

<?
echo '<FORM  name="enot" id="enot" METHOD="POST" ACTION="ent_notas.php"><br>';
echo "<input type=hidden name=en value=$reg6[$en]>";
echo "<input type=hidden name=ct value=$reg6[$ct]>";
?>
<div id="sound_element"></div>

<? 
echo "<input type=hidden name=year value=$reg6[43]>";

echo '<table border="0" cellpadding="2" cellspacing="0" width="70%">';
echo 	'<tr>';
echo		'<td width="170" bgcolor="#CCCCCC" align="right"><b><font size="4">Curso:</font></b></td>';
echo		'<td width="260" bgcolor="#FFFFCC"><font size="5">'.$reg[7].' ,      '.$_POST[tri].'</font></td>';
echo		'<td width="170" bgcolor="#CCCCCC" align="right"><b><font size="4">';
echo		'Entrada:</font></b></td>';
echo		'<td bgcolor="#FFFFCC"><font size="5">'.$_POST[tra].'</font></td>';
echo		'<td bgcolor="#FFFFCC"><center>Cuenta atrás</center></td>';
echo	'</tr>';
echo	'<tr>';
echo		'<td width="170" bgcolor="#CCCCCC" align="right"><b><font size="4">Total ';
echo		'Estud.:</font></b></td>';
echo		'<td width="260" bgcolor="#FFFFCC"><font size="5">';
if ($_POST[tra] == "Notas" OR $_POST[tra] == "Cond-Asis" OR $_POST[tra] == "V-Nota")
   {
   echo $num_resultados.'</td>';
   }
else
   {
   echo $num_resultados2.'</td>';
   }
echo		'</font><td width="170" bgcolor="#CCCCCC" align="right"><b><font size="4">Fecha ';
echo		'Inicio:</font></b></td>';
echo		'<td bgcolor="#FFFFCC"><font size="5">';
if ($_POST[tri] == "Trimestre-1"){echo $reg6[29];}
if ($_POST[tri] == "Trimestre-2"){echo $reg6[31];}
if ($_POST[tri] == "Trimestre-3"){echo $reg6[33];}
if ($_POST[tri] == "Trimestre-4"){echo $reg6[35];}
if ($_POST[tri] == "Verano"){echo $reg6[94];}
echo        '</font></td>';
echo		'<td bgcolor="#FFFFCC">';
?>
<p id="txtcountdown">0:00:00</p>
<?
echo        '</td>';

echo	'</tr>';
echo	'<tr>';
echo		'<td width="170" bgcolor="#CCCCCC" align="right"><b><font size="4">';
echo		'Profesor(a):</font></b></td>';
echo		'<td width="260" bgcolor="#FFFFCC"><font size="5">'.$reg2[1]." ".$reg2[2].'</font></td>';
echo		'<td width="170" bgcolor="#CCCCCC" align="right"><b><font size="4">Fecha ';
echo		'Cierre:</font></b></td>';
echo		'<td bgcolor="#FFFFCC"><font size="5">';
if ($_POST[tri] == "Trimestre-1"){echo $reg6[30];}
if ($_POST[tri] == "Trimestre-2"){echo $reg6[32];}
if ($_POST[tri] == "Trimestre-3"){echo $reg6[34];}
if ($_POST[tri] == "Trimestre-4"){echo $reg6[36];}
if ($_POST[tri] == "Verano"){echo $reg6[95];}
echo        '</font></td>';
echo		'<td bgcolor="#FFFFCC">';
?>
<div>
	Hours: <input type="hidden" size="10" id="inputHours" name="hor" value="00"> Minutes: 
	<input type="hidden" size="10" id="inputMinutes" name="min" value="15"> Seconds: 
	<input type="hidden" size="10" id="inputSeconds" name="seg" value="00"><br><br>
</div>
<script>
	var countdownHours = document.getElementById("inputHours").value;
	var countdownMinutes = document.getElementById("inputMinutes").value;
	var countdownSeconds = document.getElementById("inputSeconds").value;
	countdowntimer.set(countdownHours, countdownMinutes, countdownSeconds);
</script>
<?

echo        '</td>';
echo	'</tr>';
echo '</table>';

$a=0;
if ($_POST[tra] == "Notas" OR $_POST[tra] == "V-Nota")
   {
   echo '<br><table border="0" cellpadding="2" cellspacing="0" width="50%">';
   echo	'<tr>';
   echo		"<td width='170' align='right' bgcolor='#CCCCCC'><b>Pasar Letras </b>";

   $chk=""; IF($reg[77]=="ON") {$chk="checked=checked";}
   echo "<input name=letra type='checkbox' value='ON' ".$chk.'/>';

   echo		'</td>';
   if ($_POST[tra] == "Notas")
      {
      echo '<td>Está opción se aplica en la columna <b>"Nota-9"</b> exclusivamente.</td>';
      }
   ELSE
      {
      echo '<td>Está opción se aplica en la columna <b>"Nota-7"</b> exclusivamente.</td>';
      }
   echo	'</tr>';
   echo '</table>';

   echo '<br><table border="0" cellpadding="2" cellspacing="0" width="50%">';
   echo	'<tr>';
   echo		"<td width='170' align='right' bgcolor='#CCCCCC'><b>Conversión </b>";

   $chk1=""; IF($reg[105]=="ON") {$chk1="checked=checked";}
   echo "<input name=pal type='checkbox' value='ON' ".$chk1.'/>';

   echo		'</td>';
   echo		'<td>Está opción es para convertir de numero a letra.</td>';
   echo	'</tr>';
   echo '</table>';
if ($_POST['tri'] == "Trimestre-2" and $reg6[61]=="NO" OR $_POST['tri'] == "Trimestre-4" and $reg6[61]=="NO")
   {
   echo '<br><table border="0" cellpadding="2" cellspacing="0" width="50%">';
   echo	'<tr>';
   echo		"<td width='170' align='right' bgcolor='#CCCCCC'><b>Suma Trim.</b>";
   $chk=""; IF($reg6[61]=="NO") {$chk="checked=checked";}
   ?>
   <input name="sutri" type="checkbox" value="ON" <? echo $chk ?>/>
   <?

   echo		'</td>';
   echo		'<td>Está opción suma T-1+T-2 / T-3+T-4.</td>';
   echo	'</tr>';
   echo '</table>';
   }
}

if ($reg6[67]=='Si' AND $_POST[tra] == "Notas")
   {
   echo '<br><table border="0" cellpadding="2" cellspacing="0" width="50%">';
   echo	'<tr>';
   echo		"<td width='170' align='right' bgcolor='#CCCCCC'><b>Aviso Terminar </b>";

   if($reg6[68]=='4')
     {

if ($_POST[tri] == "Trimestre-1")
   {
   $chk2=""; IF($reg[107]=="X") {$chk2="checked=checked";}
   echo "<input name=sie1 type='checkbox' value='X' ".$chk2.'/>';
   echo		'</td>';
   echo		'<td>Cuando termine el trimestre marque está Opción.</td>';
   echo	'</tr>';
   echo '</table>';
   }

if ($_POST[tri] == "Trimestre-2")
   {
   $chk3=""; IF($reg[108]=="X") {$chk3="checked=checked";}
   echo "<input name=sie2 type='checkbox' value='X' ".$chk3.'/>';
   echo		'</td>';
   echo		'<td>Cuando termine el trimestre marque está Opción.</td>';
   echo	'</tr>';
   echo '</table>';
   }

if ($_POST[tri] == "Trimestre-3")
   {
   $chk4=""; IF($reg[109]=="X") {$chk4="checked=checked";}
   echo "<input name=sie3 type='checkbox' value='X' ".$chk4.'/>';
   echo		'</td>';
   echo		'<td>Cuando termine el trimestre marque está Opción.</td>';
   echo	'</tr>';
   echo '</table>';
   }
if ($_POST[tri] == "Trimestre-4")
   {
   $chk5=""; IF($reg[110]=="X") {$chk5="checked=checked";}
   echo "<input name=sie4 type='checkbox' value='X' ".$chk5.'/>';
   echo		'</td>';
   echo		'<td>Cuando termine el trimestre marque está Opción.</td>';
   echo	'</tr>';
   echo '</table>';
   }
     }

  }

if ($_POST[tra] == "Trab-Diarios2" OR $_POST[tra] == "Trab-Diarios" OR $_POST[tra] == "Trab-Libreta" OR $_POST[tra] == "Trab-Libreta2" OR $_POST[tra] == "Pruebas-Cortas")
   {
   IF ($reg[96]=='1')
      {
      echo "<br><font size='4'>&nbsp;¿Quieres que estas notas sean?&nbsp;&nbsp;<input type='radio' name='nota_por' value='1' checked>Porciento&nbsp;&nbsp;";
      echo "<input type='radio' name='nota_por' value='2'>Suma</font><br>";
      }
   ELSE
      {
      echo "<br><font size='4'>&nbsp;¿Quieres que estas notas sean?&nbsp;&nbsp;<input type='radio' name='nota_por' value='1'>Porciento&nbsp;&nbsp;";
      echo "<input type='radio' name='nota_por' value='2' checked>Suma</font><br>";
      }
   }
echo '<br>';
if ($_POST[tra] == "Cond-Asis")
   {
   echo '<table border="0" cellpadding="2" cellspacing="0" width="950">';
   echo	'<tr>';
   echo		'<td width="25" align="right" bgcolor="#CCCCCC"><b></b></td>';
   echo		'<td width="350" align="center" bgcolor="#CCCCCC"><b>Nombre del Estudiante</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Conducta</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Ausencias</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Tardanzas</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Deméritos</b></td>';
   if ($reg6[87] == "SI")
      {
      echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Comentarios</b></td>';
      echo		'<td width="350" align="center" bgcolor="#CCCCCC"><b>Codigos de Comentarios</b></td>';
      }
   }
if ($_POST[tra] == "V-Nota")
   {
   echo '<table border="0" cellpadding="2" cellspacing="0" width="1500">';
   echo	'<tr>';
   echo		'<td width="25" align="right" bgcolor="#CCCCCC"><b></b></td>';
   echo		'<td width="350" align="center" bgcolor="#CCCCCC"><b>Nombre del Estudiante</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Nota-1</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Nota-2</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Nota-3</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Nota-4</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Nota-5</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Nota-6</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Nota-7</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Bono</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>TPA</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>%</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Nota</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Conducta</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Ausencias</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Tardanzas</b></td>';
   if ($reg6[87] == "SI")
      {
      echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Comentarios</b></td>';
      echo		'<td width="350" align="center" bgcolor="#CCCCCC"><b>Codigos de Comentarios</b></td>';
      }
   }
if ($_POST[tra] == "Ex-Final")
   {
   echo '<table border="0" cellpadding="2" cellspacing="0" width="700">';
   echo	'<tr>';
   echo		'<td width="25" align="right" bgcolor="#CCCCCC"><b></b></td>';
   echo		'<td width="350" align="center" bgcolor="#CCCCCC"><b>Nombre del Estudiante</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b></b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Ex. Final</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b></b></td>';
   }

if ($_POST[tra] == "Notas")
   {
   echo '<table border="1" cellpadding="2" cellspacing="0.3" width="1300">';
   echo	'<tr>';
   echo		'<td width="35" align="center" bgcolor="#CCCCCC"> </td>';
   echo		'<td width="425" align="center" bgcolor="#CCCCCC"><b>Nombre del Estudiante</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Not-1</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Not-2</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Not-3</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Not-4</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Not-5</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Not-6</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Not-7</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Not-8</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Not-9</b></td>';
if (!empty($reg6[88]))
   {
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Nota</b></td>';
   }
else
   {
   if ($reg6[168]=='Si')
      {
      echo		'<td width="65" align="center" bgcolor="#CCCCCC"><b>Not-10</b></td>';
      echo		'<td width="65" align="center" bgcolor="#CCCCCC"><b>Not-11</b></td>';
      echo		'<td width="65" align="center" bgcolor="#CCCCCC"><b>Not-12</b></td>';
      echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Bono</b></td>';
      }
   else
      {
      echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>Bono</b></td>';
      }
   }
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>T-Dia</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>T-Lib</b></td>';
   echo		'<td width="50" align="center" bgcolor="#CCCCCC"><b>P-Cor</b></td>';
   echo		'<td width="46" align="center" bgcolor="#CCCCCC"><b>TPA</b></td>';
   echo		'<td width="46" align="center" bgcolor="#CCCCCC"><b>%</b></td>';
   echo		'<td width="51" align="center" bgcolor="#CCCCCC"><b>Nota</b></td>';
   }
if ($_POST[tra] == "Trab-Diarios2" OR $_POST[tra] == "Trab-Libreta" OR $_POST[tra] == "Trab-Libreta2" OR $_POST[tra] == "Trab-Diarios" OR $_POST[tra] == "Pruebas-Cortas")
   {
   echo '<table border="1" cellpadding="2" cellspacing="0.3" width="1000">';
   echo '<tr>';
   echo	'<td width="35" align="right" bgcolor="#CCCCCC"><b></b></td>';
   echo '<td width="320" align="center" bgcolor="#CCCCCC"><b>Nombre del ';
   echo	'Estudiante</b></td>';
   echo	'<td width="50" align="center" bgcolor="#CCCCCC"><b>Nota-1</b></td>';
   echo	'<td width="50" align="center" bgcolor="#CCCCCC"><b>Nota-2</b></td>';
   echo	'<td width="50" align="center" bgcolor="#CCCCCC"><b>Nota-3</b></td>';
   echo	'<td width="50" align="center" bgcolor="#CCCCCC"><b>Nota-4</b></td>';
   echo	'<td width="50" align="center" bgcolor="#CCCCCC"><b>Nota-5</b></td>';
   echo	'<td width="50" align="center" bgcolor="#CCCCCC"><b>Nota-6</b></td>';
   echo	'<td width="50" align="center" bgcolor="#CCCCCC"><b>Nota-7</b></td>';
   echo	'<td width="50" align="center" bgcolor="#CCCCCC"><b>Nota-8</b></td>';
   echo	'<td width="50" align="center" bgcolor="#CCCCCC"><b>Nota-9</b></td>';
   echo	'<td width="53" align="center" bgcolor="#CCCCCC"><b>Nota10</b></td>';
   echo	'<td width="50" align="center" bgcolor="#CCCCCC"><b>TPA</b></td>';
   echo	'<td width="50" align="center" bgcolor="#CCCCCC"><b>%</b></td>';
   echo	'<td width="50" align="center" bgcolor="#CCCCCC"><b>Nota</b></td>';
   }
echo	'</tr>';
$p1=0;
$p2=$num_resultados;
$p3=$num_resultados*2;
$p4=$num_resultados*3;
$p5=$num_resultados*4;
$p6=$num_resultados*5;
$p7=$num_resultados*6;
$p8=$num_resultados*7;
$p9=$num_resultados*8;
$p10=$num_resultados*9;
$p11=$num_resultados*10;
$p12=$num_resultados*11;
$p13=$num_resultados*12;
if ($_POST[tra] == "Notas" OR $_POST[tra] == "Cond-Asis" OR $_POST[tra] == "Ex-Final" OR $_POST[tra] == "V-Nota")
   {
while ($row=mysql_fetch_array($result))
{

$p1=$p1+1;
$p2=$p2+1;
$p3=$p3+1;
$p4=$p4+1;
$p5=$p5+1;
$p6=$p6+1;
$p7=$p7+1;
$p8=$p8+1;
$p9=$p9+1;
$p10=$p10+1;
$p11=$p11+1;
$p12=$p12+1;
$p13=$p13+1;

$tpa=0;
$por=0;
$not=0;
$a=$a+1;

for ($p=0;$p<=10;$p++)
    {
    if ($row[$p+27+$c]+0 > 0){$tpa=$tpa+$row[$p+27+$c];}
    if ($row[$p+27+$c]+0 > 0){$por=$por+$reg3[$p+$c];}
    }

echo	'<tr id="l'.$a.'" bgcolor="#FFFFCC">';

echo		'<td width="35" align="center"><font size="2">';
echo        $a.' '.'</font></td>';
echo		'<td width="320"><font size="2">';
echo        $row[4]." ".$row[3].'</font></td>';
echo		'<td width="46" align="center">';

if ($_POST[tra] == "Ex-Final")
   {
   echo "<input id-'tde' type=hidden name=num_rec value=$num_resultados>";
   echo "<input type=hidden name=tri value=$_POST[tri]>";
   echo "<input type=hidden name=tra value=$_POST[tra]>";
   if ($_POST[tri]=="Trimestre-2")
      {
      echo "</td>";
      echo '<td width="46" align="center">';
      echo "<input type=text name=est($a,1) maxlength=3 size=3 tabindex='$p1' value='$row[124]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo '<td width="46" align="center">';
      echo "</td>";
      }
   if ($_POST[tri]=="Trimestre-4")
      {
      echo "</td>";
      echo '<td width="46" align="center">';
      echo "<input type=text name=est($a,1) maxlength=3 size=3 tabindex='$p1' value='$row[125]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo '<td width="46" align="center">';
      echo "</td>";
      }

   echo "<input type=hidden name=est($a,14) value=$row[56]>";
   echo "<input type=hidden name=curso value='$row[7]'>";
   
   echo	'</tr>';
   }

if ($_POST[tra] == "V-Nota")
   {
   echo "<input id='tde' type=hidden name=num_rec value=$num_resultados>";
   echo "<input type=hidden name=tri value=$_POST[tri]>";
   echo "<input type=hidden name=tra value=$_POST[tra]>";
   if ($_POST[tri]=="Verano")
      {
echo		"<input type=text name=est($a,1) maxlength=3 size=3 tabindex='$p1' value='$row[28]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text name=est($a,2) maxlength=3 size=3 tabindex='$p2' value='$row[29]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text name=est($a,3) maxlength=3 size=3 tabindex='$p3' value='$row[30]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text name=est($a,4) maxlength=3 size=3 tabindex='$p4' value='$row[31]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text name=est($a,5) maxlength=3 size=3 tabindex='$p5' value='$row[32]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text name=est($a,6) maxlength=3 size=3 tabindex='$p6' value='$row[33]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text name=est($a,7) maxlength=3 size=3 tabindex='$p7' value='$row[34]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text name=est($a,10) maxlength=3 size=3 tabindex='$p8' value='$row[37]' onclick='oscura($a)' onfocus='oscura($a)'></td>";

echo		"<td width='46' align='center'><b><font size='4'>$row[48]</font></b></td>";
echo		"<td width='46' align='center'><b><font size='4'>$row[52]</font></b></td>";
echo		"<td width='51' align='center'><b><font size='4'>$row[11]</font></b></td>";

      echo '<td width="46" align="center">';
      echo "<input type=text name=est($a,11) maxlength=3 size=3 tabindex='$p9' value='$row[15]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo '<td width="46" align="center">';
      echo "<input type=text name=est($a,12) maxlength=3 size=3 tabindex='$p10' value='$row[78]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo '<td width="46" align="center">';
      echo "<input type=text name=est($a,13) maxlength=2 size=2 tabindex='$p11' value='$row[82]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      if ($reg6[87] == "SI")
         {
         $row88=mysql_fetch_array($result8);
         echo '<td width="46" align="center">';
         echo "<input type=text name=est($a,15) maxlength=2 size=2 tabindex='$p12' value='$row[130]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
         echo '<td width="346" align="left">';
         echo $row88[0].' '.$row88[1]."</td>";
         }
      }
   echo "<input type=hidden name=est($a,14) value=$row[56]>";
   echo "<input type=hidden name=curso value='$row[7]'>";
   
   echo	'</tr>';

}

if ($_POST[tra] == "Cond-Asis")
   {
   echo "<input id='tde' type=hidden name=num_rec value=$num_resultados>";
   echo "<input type=hidden name=tri value=$_POST[tri]>";
   echo "<input type=hidden name=tra value=$_POST[tra]>";
   if ($_POST[tri]=="Trimestre-1")
      {
      echo "<input type=text name=est($a,1) maxlength=3 size=3 tabindex='$p1' value='$row[15]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo '<td width="46" align="center">';
      echo "<input type=text name=est($a,2) maxlength=3 size=3 tabindex='$p2' value='$row[78]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo '<td width="46" align="center">';
      echo "<input type=text name=est($a,3) maxlength=3 size=3 tabindex='$p3' value='$row[82]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo '<td width="46" align="center">';
      echo "<input type=text name=est($a,4) maxlength=2 size=2 tabindex='$p4' value='$row[126]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      if ($reg6[87] == "SI")
         {
         echo '<td width="46" align="center">';
         echo "<input type=text name=est($a,5) maxlength=2 size=2 tabindex='$p5' value='$row[130]' onclick='oscura($a)' onfocus='oscura($a)'>";
         echo "</td>";
         echo '<td width="346" align="left">';

echo "<select name=est($a,5) type='text' >"
?>
						<option value=""></option>
						<?php foreach ($cursos as $curso): ?>
								<option <?php echo (strtoupper($row[130]) == $curso->code)?'selected=""':'' ?> value="<?php echo $curso->code; ?>"><?php echo $curso->code.' '.$curso->comenta; ?></option>
							<?php endforeach ?>	
					</select>

<?
         echo "</td>";
         }
      }
   if ($_POST[tri]=="Trimestre-2")
      {
      echo "<input type=text name=est($a,1) maxlength=3 size=3 tabindex='$p1' value='$row[16]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo '<td width="46" align="center">';
      echo "<input type=text name=est($a,2) maxlength=3 size=3 tabindex='$p2' value='$row[79]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo '<td width="46" align="center">';
      echo "<input type=text name=est($a,3) maxlength=3 size=3 tabindex='$p3' value='$row[83]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo '<td width="46" align="center">';
      echo "<input type=text name=est($a,4) maxlength=2 size=2 tabindex='$p4' value='$row[127]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      if ($reg6[87] == "SI")
         {
         echo '<td width="46" align="center">';
         echo "<input type=text name=est($a,5) maxlength=2 size=2 tabindex='$p5' value='$row[131]' onclick='oscura($a)' onfocus='oscura($a)'>";
         echo "</td>";
         echo '<td width="346" align="left">';

echo "<select name=est($a,5) type='text' >"
?>
						<option value=""></option>
						<?php foreach ($cursos as $curso): ?>
								<option <?php echo (strtoupper($row[131]) == $curso->code)?'selected=""':'' ?> value="<?php echo $curso->code; ?>"><?php echo $curso->code.' '.$curso->comenta; ?></option>
							<?php endforeach ?>	
					</select>

<?
         echo "</td>";
         }
      }
   if ($_POST[tri]=="Trimestre-3")
      {
      echo "<input type=text name=est($a,1) maxlength=3 size=3 tabindex='$p1' value='$row[17]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo '<td width="46" align="center">';
      echo "<input type=text name=est($a,2) maxlength=3 size=3 tabindex='$p2' value='$row[80]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo '<td width="46" align="center">';
      echo "<input type=text name=est($a,3) maxlength=3 size=3 tabindex='$p3' value='$row[84]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo '<td width="46" align="center">';
      echo "<input type=text name=est($a,4) maxlength=2 size=2 tabindex='$p4' value='$row[128]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      if ($reg6[87] == "SI")
         {
         echo '<td width="46" align="center">';
         echo "<input type=text name=est($a,5) maxlength=2 size=2 tabindex='$p5' value='$row[132]' onclick='oscura($a)' onfocus='oscura($a)'>";
         echo "</td>";
         echo '<td width="346" align="left">';

echo "<select name=est($a,5) type='text' >"
?>
						<option value=""></option>
						<?php foreach ($cursos as $curso): ?>
								<option <?php echo (strtoupper($row[132]) == $curso->code)?'selected=""':'' ?> value="<?php echo $curso->code; ?>"><?php echo $curso->code.' '.$curso->comenta; ?></option>
							<?php endforeach ?>	
					</select>

<?
         echo "</td>";
         }
      }
   if ($_POST[tri]=="Trimestre-4")
      {
      echo "<input type=text name=est($a,1) maxlength=3 size=3 tabindex='$p1' value='$row[18]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo '<td width="46" align="center">';
      echo "<input type=text name=est($a,2) maxlength=3 size=3 tabindex='$p2' value='$row[81]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo '<td width="46" align="center">';
      echo "<input type=text name=est($a,3) maxlength=3 size=3 tabindex='$p3' value='$row[85]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo '<td width="46" align="center">';
      echo "<input type=text name=est($a,4) maxlength=2 size=2 tabindex='$p4' value='$row[129]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      if ($reg6[87] == "SI")
         {
         echo '<td width="46" align="center">';
         echo "<input type=text name=est($a,5) maxlength=2 size=2 tabindex='$p5' value='$row[133]' onclick='oscura($a)' onfocus='oscura($a)'>";
         echo "</td>";
         echo '<td width="346" align="left">';

echo "<select name=est($a,5) type='text' >"
?>
						<option value=""></option>
						<?php foreach ($cursos as $curso): ?>
								<option <?php echo (strtoupper($row[133]) == $curso->code)?'selected=""':'' ?> value="<?php echo $curso->code; ?>"><?php echo $curso->code.' '.$curso->comenta; ?></option>
							<?php endforeach ?>	
					</select>

<?
         echo "</td>";
         }
      }
   echo "<input type=hidden name=est($a,14) value=$row[56]>";
   echo "<input type=hidden name=curso value='$row[7]'>";
   
   echo	'</tr>';
   }
if ($_POST[tra] == "Notas")
   {
   echo "<input id='tde' type=hidden name=num_rec value=$num_resultados>";
   echo "<input type=hidden name=tri value=$_POST[tri]>";
   echo "<input type=hidden name=tra value=$_POST[tra]>";
if ($_POST[tri]=="Trimestre-1")
{
echo		"<input type=text id='nta".$a."' name=est($a,1) maxlength=3 size=3 tabindex='$p1' value='$row[28]' onblur='verp1()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='ntb".$a."' name=est($a,2) maxlength=3 size=3 tabindex='$p2' value='$row[29]' onblur='verp2()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='ntc".$a."' name=est($a,3) maxlength=3 size=3 tabindex='$p3' value='$row[30]' onblur='verp3()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='ntd".$a."' name=est($a,4) maxlength=3 size=3 tabindex='$p4' value='$row[31]' onblur='verp4()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='nte".$a."' name=est($a,5) maxlength=3 size=3 tabindex='$p5' value='$row[32]' onblur='verp5()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='ntf".$a."' name=est($a,6) maxlength=3 size=3 tabindex='$p6' value='$row[33]' onblur='verp6()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='ntg".$a."' name=est($a,7) maxlength=3 size=3 tabindex='$p7' value='$row[34]' onblur='verp7()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='nth".$a."' name=est($a,8) maxlength=3 size=3 tabindex='$p8' value='$row[35]' onblur='verp8()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='nti".$a."' name=est($a,9) maxlength=3 size=3 tabindex='$p9' value='$row[36]' onblur='verp9()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
if ($reg6[168]=='Si')
   {
   echo		'<td width="46" align="center">';
   echo		"<input type=text id='ntj".$a."' name=est($a,10) maxlength=3 size=3 tabindex='$p10' value='$row[37]' onblur='verp10()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
   echo		'<td width="46" align="center">';
   echo		"<input type=text id='ntk".$a."' name=est($a,16) maxlength=3 size=3 tabindex='$p11' value='$row[38]' onblur='verp11()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
   echo		'<td width="46" align="center">';
   echo		"<input type=text id='ntl".$a."' name=est($a,17) maxlength=3 size=3 tabindex='$p12' value='$row[39]' onblur='verp12()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
   echo		'<td width="50" align="center">';
   echo		"<input type=text id='ntm".$a."' name=est($a,18) maxlength=3 size=3 tabindex='$p13' value='$row[40]' onblur='verp13()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
   }
else
   {
   if (empty($reg6[88]))
      {
      echo		'<td width="50" align="center">';
      echo		"<input type=text name=est($a,10) maxlength=3 size=3 tabindex='$p10' value='$row[37]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      }
   else
      {
      echo		"<td width='46' align='center'><b><font size='4'>$row[37]</font></b></td>";
      }
   }

echo		"<td width='46' align='center'><b><font size='4'>$row[90]</font></b></td>";
echo		"<td width='46' align='center'><b><font size='4'>$row[86]</font></b></td>";
echo		"<td width='46' align='center'><b><font size='4'>$row[115]</font></b></td>";
echo		"<td width='46' align='center'><b><font size='4'>$row[48]</font></b></td>";
echo		"<td width='46' align='center'><b><font size='4'>$row[52]</font></b></td>";
echo		"<td width='51' align='center'><b><font size='4'>$row[11]</font></b></td>";
echo	'</tr>';

$mop=$row[48].' ,-'.$row[52].' ,-'.$row[12].' ,-'.$row[90].' ,-'.$row[86].' ,-'.$row[97].' ,-'.$row[101].' ,-'.$row[12].' ,-'.$row[19].' ,-'.$row[20].' ,-'.$row[115].' ,-'.$row[119];
echo "<input type=hidden name=est($a,11) value='$mop'>";
}

if ($_POST[tri]=="Trimestre-2")
{
echo		"<input type=text id='nta".$a."' name=est($a,1) maxlength=3 size=3 tabindex='$p1' value='$row[38]' onblur='verp1()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='ntb".$a."' name=est($a,2) maxlength=3 size=3 tabindex='$p2' value='$row[39]' onblur='verp2()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='ntc".$a."' name=est($a,3) maxlength=3 size=3 tabindex='$p3' value='$row[40]' onblur='verp3()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='ntd".$a."' name=est($a,4) maxlength=3 size=3 tabindex='$p4' value='$row[41]' onblur='verp4()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='nte".$a."' name=est($a,5) maxlength=3 size=3 tabindex='$p5' value='$row[42]' onblur='verp5()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='ntf".$a."' name=est($a,6) maxlength=3 size=3 tabindex='$p6' value='$row[43]' onblur='verp6()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='ntg".$a."' name=est($a,7) maxlength=3 size=3 tabindex='$p7' value='$row[44]' onblur='verp7()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='nth".$a."' name=est($a,8) maxlength=3 size=3 tabindex='$p8' value='$row[45]' onblur='verp8()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='nti".$a."' name=est($a,9) maxlength=3 size=3 tabindex='$p9' value='$row[46]' onblur='verp9()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
if (empty($reg6[88]))
   {
   echo		'<td width="50" align="center">';
   echo		"<input type=text name=est($a,10) maxlength=3 size=3 tabindex='$p10' value='$row[47]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
   }
else
   {
   echo		"<td width='46' align='center'><b><font size='4'>$row[47]</font></b></td>";
   }


echo		"<td width='46' align='center'><b><font size='4'>$row[91]</font></b></td>";
echo		"<td width='46' align='center'><b><font size='4'>$row[87]</font></b></td>";
echo		"<td width='46' align='center'><b><font size='4'>$row[116]</font></b></td>";
echo		"<td width='46' align='center'><b><font size='4'>$row[49]</font></b></td>";
echo		"<td width='46' align='center'><b><font size='4'>$row[53]</font></b></td>";
echo		"<td width='51' align='center'><b><font size='4'>$row[12]</font></b></td>";
echo	'</tr>';

$mop=$row[49].' ,-'.$row[53].' ,-'.$row[11].' ,-'.$row[91].' ,-'.$row[87].' ,-'.$row[98].' ,-'.$row[102].' ,-'.$row[11].' ,-'.$row[19].' ,-'.$row[20].' ,-'.$row[116].' ,-'.$row[120];
echo "<input type=hidden name=est($a,11) value='$mop'>";

echo "<input type=hidden name=est($a,22) value=$row[48]>";
echo "<input type=hidden name=est($a,23) value=$row[52]>";
}

if ($_POST[tri]=="Trimestre-3")
{

echo		"<input type=text id='nta".$a."' name=est($a,1) maxlength=3 size=3 tabindex='$p1' value='$row[57]' onblur='verp1()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='ntb".$a."' name=est($a,2) maxlength=3 size=3 tabindex='$p2' value='$row[58]' onblur='verp2()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='ntc".$a."' name=est($a,3) maxlength=3 size=3 tabindex='$p3' value='$row[59]' onblur='verp3()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='ntd".$a."' name=est($a,4) maxlength=3 size=3 tabindex='$p4' value='$row[60]' onblur='verp4()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='nte".$a."' name=est($a,5) maxlength=3 size=3 tabindex='$p5' value='$row[61]' onblur='verp5()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='ntf".$a."' name=est($a,6) maxlength=3 size=3 tabindex='$p6' value='$row[62]' onblur='verp6()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='ntg".$a."' name=est($a,7) maxlength=3 size=3 tabindex='$p7' value='$row[63]' onblur='verp7()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='nth".$a."' name=est($a,8) maxlength=3 size=3 tabindex='$p8' value='$row[64]' onblur='verp8()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='nti".$a."' name=est($a,9) maxlength=3 size=3 tabindex='$p9' value='$row[65]' onblur='verp9()' onclick='oscura($a)' onfocus='oscura($a)'></td>";

if ($reg6[168]=='Si')
   {
   echo		'<td width="46" align="center">';
   echo		"<input type=text id='ntj".$a."' name=est($a,10) maxlength=3 size=3 tabindex='$p10' value='$row[66]' onblur='verp10()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
   echo		'<td width="46" align="center">';
   echo		"<input type=text id='ntk".$a."' name=est($a,16) maxlength=3 size=3 tabindex='$p11' value='$row[67]' onblur='verp11()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
   echo		'<td width="46" align="center">';
   echo		"<input type=text id='ntl".$a."' name=est($a,17) maxlength=3 size=3 tabindex='$p12' value='$row[68]' onblur='verp12()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
   echo		'<td width="50" align="center">';
   echo		"<input type=text id='ntm".$a."' name=est($a,18) maxlength=3 size=3 tabindex='$p13' value='$row[69]' onblur='verp13()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
   }
else
   {
   if (empty($reg6[88]))
      {
      echo		'<td width="50" align="center">';
      echo		"<input type=text name=est($a,10) maxlength=3 size=3 tabindex='$p10' value='$row[66]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      }
   else
      {
      echo		"<td width='46' align='center'><b><font size='4'>$row[66]</font></b></td>";
      }
   }

echo		"<td width='46' align='center'><b><font size='4'>$row[92]</font></b></td>";
echo		"<td width='46' align='center'><b><font size='4'>$row[88]</font></b></td>";
echo		"<td width='46' align='center'><b><font size='4'>$row[117]</font></b></td>";
echo		"<td width='46' align='center'><b><font size='4'>$row[50]</font></b></td>";
echo		"<td width='46' align='center'><b><font size='4'>$row[54]</font></b></td>";
echo		"<td width='51' align='center'><b><font size='4'>$row[13]</font></b></td>";
echo	'</tr>';
if ($reg6[171]=="Si")
   {
   $mop=$row[50].' ,-'.$row[54].' ,-'.$row[14].' ,-'.$row[92].' ,-'.$row[88].' ,-'.$row[99].' ,-'.$row[103].' ,-'.$row[14].' ,-'.$row[19].' ,-'.$row[20].' ,-'.$row[117].' ,-'.$row[121].' ,-'.$row[49].' ,-'.$row[53];
   }
else
   {
   $mop=$row[50].' ,-'.$row[54].' ,-'.$row[14].' ,-'.$row[92].' ,-'.$row[88].' ,-'.$row[99].' ,-'.$row[103].' ,-'.$row[14].' ,-'.$row[19].' ,-'.$row[20].' ,-'.$row[117].' ,-'.$row[121];
   }
echo "<input type=hidden name=est($a,11) value='$mop'>";
}

if ($_POST[tri]=="Trimestre-4")
{
echo		"<input type=text id='nta".$a."' name=est($a,1) maxlength=3 size=3 tabindex='$p1' value='$row[67]' onblur='verp1()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='ntb".$a."' name=est($a,2) maxlength=3 size=3 tabindex='$p2' value='$row[68]' onblur='verp2()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='ntc".$a."' name=est($a,3) maxlength=3 size=3 tabindex='$p3' value='$row[69]' onblur='verp3()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='ntd".$a."' name=est($a,4) maxlength=3 size=3 tabindex='$p4' value='$row[70]' onblur='verp4()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='nte".$a."' name=est($a,5) maxlength=3 size=3 tabindex='$p5' value='$row[71]' onblur='verp5()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='ntf".$a."' name=est($a,6) maxlength=3 size=3 tabindex='$p6' value='$row[72]' onblur='verp6()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='ntg".$a."' name=est($a,7) maxlength=3 size=3 tabindex='$p7' value='$row[73]' onblur='verp7()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='nth".$a."' name=est($a,8) maxlength=3 size=3 tabindex='$p8' value='$row[74]' onblur='verp8()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
echo		'<td width="46" align="center">';
echo		"<input type=text id='nti".$a."' name=est($a,9) maxlength=3 size=3 tabindex='$p9' value='$row[75]' onblur='verp9()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
if (empty($reg6[88]))
   {
   echo		'<td width="50" align="center">';
   echo		"<input type=text name=est($a,10) maxlength=3 size=3 tabindex='$p10' value='$row[76]' onclick='oscura($a)' onfocus='oscura($a)'></td>";
   }
else
   {
   echo		"<td width='46' align='center'><b><font size='4'>$row[76]</font></b></td>";
   }

echo		"<td width='46' align='center'><b><font size='4'>$row[93]</font></b></td>";
echo		"<td width='46' align='center'><b><font size='4'>$row[89]</font></b></td>";
echo		"<td width='46' align='center'><b><font size='4'>$row[118]</font></b></td>";
echo		"<td width='46' align='center'><b><font size='4'>$row[51]</font></b></td>";
echo		"<td width='46' align='center'><b><font size='4'>$row[55]</font></b></td>";
echo		"<td width='51' align='center'><b><font size='4'>$row[14]</font></b></td>";
echo	'</tr>';

$mop=$row[51].' ,-'.$row[55].' ,-'.$row[13].' ,-'.$row[93].' ,-'.$row[89].' ,-'.$row[100].' ,-'.$row[104].' ,-'.$row[13].' ,-'.$row[19].' ,-'.$row[20].' ,-'.$row[118].' ,-'.$row[122];
echo "<input type=hidden name=est($a,11) value='$mop'>";

echo "<input type=hidden name=est($a,22) value=$row[50]>";
echo "<input type=hidden name=est($a,23) value=$row[54]>";
}

echo "<input type=hidden name=est($a,14) value=$row[56]>";
echo "<input type=hidden name=tri value=$_POST[tri]>";
echo "<input type=hidden name=curso value='$row[7]'>";
}
}
}

$a=0;
$p1=0;
$p2=$num_resultados;
$p3=$num_resultados*2;
$p4=$num_resultados*3;
$p5=$num_resultados*4;
$p6=$num_resultados*5;
$p7=$num_resultados*6;
$p8=$num_resultados*7;
$p9=$num_resultados*8;
$p10=$num_resultados*9;
$p11=$num_resultados*10;
$p12=$num_resultados*11;

if ($_POST[tra] == "Trab-Diarios2" OR $_POST[tra] == "Trab-Diarios" OR $_POST[tra] == "Trab-Libreta" OR $_POST[tra] == "Trab-Libreta2" OR $_POST[tra] == "Pruebas-Cortas")
   {
   echo "<input id='tde' type=hidden name=num_rec value=$num_resultados2>";
   echo "<input type=hidden name=tri value=$_POST[tri]>";
   echo "<input type=hidden name=tra value=$_POST[tra]>";

   while ($row=mysql_fetch_array($result2))
   {
$p1=$p1+1;
$p2=$p2+1;
$p3=$p3+1;
$p4=$p4+1;
$p5=$p5+1;
$p6=$p6+1;
$p7=$p7+1;
$p8=$p8+1;
$p9=$p9+1;
$p10=$p10+1;
$p11=$p11+1;
$p12=$p12+1;
   $tpa=0;
   $por=0;
   $not=0;
   $a=$a+1;

   echo	'<tr id="l'.$a.'" bgcolor="#FFFFCC">';
   echo	'<td width="35" align="center" ><font size="2">';
   echo $a.' '.'</font></td>';

   echo	'<td width="320"><font size="2">';
   echo $row[4]." ".$row[3].'</font></td>';
   echo	'<td width="46" align="center">';
   
if ($_POST[tra] == "Trab-Diarios2" OR $_POST[tra] == "Trab-Diarios" OR $_POST[tra] == "Pruebas-Cortas")
   {
   if ($_POST[tri]=="Trimestre-1")
      {
      echo	"<input type=text id='nta".$a."' name=est($a,1) maxlength=3 size=3 tabindex='$p1' value='$row[12]' onblur='verp1()' onclick='oscura($a)' onfocus='oscura($a)' onclick='oscurb(1)' onfocus='oscurb(1)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntb".$a."' name=est($a,2) maxlength=3 size=3 tabindex='$p2' value='$row[13]' onblur='verp2()' onclick='oscura($a)' onfocus='oscura($a)' onclick='oscurb(2)' onfocus='oscurb(2)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntc".$a."' name=est($a,3) maxlength=3 size=3 tabindex='$p3' value='$row[14]' onblur='verp3()' onclick='oscura($a)' onfocus='oscura($a)' onclick='oscurb(3)' onfocus='oscurb(3)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntd".$a."' name=est($a,4) maxlength=3 size=3 tabindex='$p4' value='$row[15]' onblur='verp4()' onclick='oscura($a)' onfocus='oscura($a)' onclick='oscurb(4)' onfocus='oscurb(4)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='nte".$a."' name=est($a,5) maxlength=3 size=3 tabindex='$p5' value='$row[16]' onblur='verp5()' onclick='oscura($a)' onfocus='oscura($a)' onclick='oscurb(5)' onfocus='oscurb(5)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntf".$a."' name=est($a,6) maxlength=3 size=3 tabindex='$p6' value='$row[17]' onblur='verp6()' onclick='oscura($a)' onfocus='oscura($a)' onclick='oscurb(6)' onfocus='oscurb(6)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntg".$a."' name=est($a,7) maxlength=3 size=3 tabindex='$p7' value='$row[18]' onblur='verp7()' onclick='oscura($a)' onfocus='oscura($a)' onclick='oscurb(7)' onfocus='oscurb(7)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='nth".$a."' name=est($a,8) maxlength=3 size=3 tabindex='$p8' value='$row[19]' onblur='verp8()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='nti".$a."' name=est($a,9) maxlength=3 size=3 tabindex='$p9' value='$row[20]' onblur='verp9()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="53" align="center">';
      echo	"<input type=text id='ntj".$a."' name=est($a,10) maxlength=3 size=3 tabindex='$p10' value='$row[21]' onblur='verp10()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	"<td width='46' align='center'><b><font size='4'>$row[52]</font></b></td>";
      echo	"<td width='46' align='center'><b><font size='4'>$row[56]</font></b></td>";
      echo	"<td width='51' align='center'><b><font size='4'>$row[60]</font></b></td>";
      echo '</tr>';
      echo "<input type=hidden name=est($a,11) value=$row[56]>";
      echo "<input type=hidden name=est($a,12) value=$row[52]>";
      echo "<input type=hidden name=est($a,13) value=$row[60]>";
      }

   if ($_POST[tri]=="Trimestre-2")
      {
      echo	"<input type=text id='nta".$a."' name=est($a,1) maxlength=3 size=3 tabindex='$p1' value='$row[22]' onblur='verp1()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntb".$a."' name=est($a,2) maxlength=3 size=3 tabindex='$p2' value='$row[23]' onblur='verp2()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntc".$a."' name=est($a,3) maxlength=3 size=3 tabindex='$p3' value='$row[24]' onblur='verp3()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntd".$a."' name=est($a,4) maxlength=3 size=3 tabindex='$p4' value='$row[25]' onblur='verp4()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='nte".$a."' name=est($a,5) maxlength=3 size=3 tabindex='$p5' value='$row[26]' onblur='verp5()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntf".$a."' name=est($a,6) maxlength=3 size=3 tabindex='$p6' value='$row[27]' onblur='verp6()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntg".$a."' name=est($a,7) maxlength=3 size=3 tabindex='$p7' value='$row[28]' onblur='verp7()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='nth".$a."' name=est($a,8) maxlength=3 size=3 tabindex='$p8' value='$row[29]' onblur='verp8()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='nti".$a."' name=est($a,9) maxlength=3 size=3 tabindex='$p9' value='$row[30]' onblur='verp9()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="53" align="center">';
      echo	"<input type=text id='ntj".$a."' name=est($a,10) maxlength=3 size=3 tabindex='$p10' value='$row[31]' onblur='verp10()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	"<td width='46' align='center'><b><font size='4'>$row[53]</font></b></td>";
      echo	"<td width='46' align='center'><b><font size='4'>$row[57]</font></b></td>";
      echo	"<td width='51' align='center'><b><font size='4'>$row[61]</font></b></td>";
      echo '</tr>';
      echo "<input type=hidden name=est($a,11) value=$row[57]>";
      echo "<input type=hidden name=est($a,12) value=$row[53]>";
      echo "<input type=hidden name=est($a,13) value=$row[61]>";
      }

   if ($_POST[tri]=="Trimestre-3")
      {
      echo	"<input type=text id='nta".$a."' name=est($a,1) maxlength=3 size=3 tabindex='$p1' value='$row[32]' onblur='verp1()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntb".$a."' name=est($a,2) maxlength=3 size=3 tabindex='$p2' value='$row[33]' onblur='verp2()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntc".$a."' name=est($a,3) maxlength=3 size=3 tabindex='$p3' value='$row[34]' onblur='verp3()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntd".$a."' name=est($a,4) maxlength=3 size=3 tabindex='$p4' value='$row[35]' onblur='verp4()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='nte".$a."' name=est($a,5) maxlength=3 size=3 tabindex='$p5' value='$row[36]' onblur='verp5()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntf".$a."' name=est($a,6) maxlength=3 size=3 tabindex='$p6' value='$row[37]' onblur='verp6()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntg".$a."' name=est($a,7) maxlength=3 size=3 tabindex='$p7' value='$row[38]' onblur='verp7()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='nth".$a."' name=est($a,8) maxlength=3 size=3 tabindex='$p8' value='$row[39]' onblur='verp8()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='nti".$a."' name=est($a,9) maxlength=3 size=3 tabindex='$p9' value='$row[40]' onblur='verp9()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="53" align="center">';
      echo	"<input type=text id='ntj".$a."' name=est($a,10) maxlength=3 size=3 tabindex='$p10' value='$row[41]' onblur='verp10()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	"<td width='46' align='center'><b><font size='4'>$row[54]</font></b></td>";
      echo	"<td width='46' align='center'><b><font size='4'>$row[58]</font></b></td>";
      echo	"<td width='51' align='center'><b><font size='4'>$row[62]</font></b></td>";
      echo '</tr>';
      echo "<input type=hidden name=est($a,11) value=$row[58]>";
      echo "<input type=hidden name=est($a,12) value=$row[54]>";
      echo "<input type=hidden name=est($a,13) value=$row[62]>";
      }

   if ($_POST[tri]=="Trimestre-4")
      {
      echo	"<input type=text id='nta".$a."' name=est($a,1) maxlength=3 size=3 tabindex='$p1' value='$row[42]' onblur='verp1()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntb".$a."' name=est($a,2) maxlength=3 size=3 tabindex='$p2' value='$row[43]' onblur='verp2()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntc".$a."' name=est($a,3) maxlength=3 size=3 tabindex='$p3' value='$row[44]' onblur='verp3()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntd".$a."' name=est($a,4) maxlength=3 size=3 tabindex='$p4' value='$row[45]' onblur='verp4()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='nte".$a."' name=est($a,5) maxlength=3 size=3 tabindex='$p5' value='$row[46]' onblur='verp5()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntf".$a."' name=est($a,6) maxlength=3 size=3 tabindex='$p6' value='$row[47]' onblur='verp6()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntg".$a."' name=est($a,7) maxlength=3 size=3 tabindex='$p7' value='$row[48]' onblur='verp7()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='nth".$a."' name=est($a,8) maxlength=3 size=3 tabindex='$p8' value='$row[49]' onblur='verp8()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='nti".$a."' name=est($a,9) maxlength=3 size=3 tabindex='$p9' value='$row[50]' onblur='verp9()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="53" align="center">';
      echo	"<input type=text id='ntj".$a."' name=est($a,10) maxlength=3 size=3 tabindex='$p10' value='$row[51]' onblur='verp10()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	"<td width='46' align='center'><b><font size='4'>$row[55]</font></b></td>";
      echo	"<td width='46' align='center'><b><font size='4'>$row[59]</font></b></td>";
      echo	"<td width='51' align='center'><b><font size='4'>$row[63]</font></b></td>";
      echo '</tr>';
      echo "<input type=hidden name=est($a,11) value=$row[59]>";
      echo "<input type=hidden name=est($a,12) value=$row[55]>";
      echo "<input type=hidden name=est($a,13) value=$row[63]>";
      }
      echo "<input type=hidden name=est($a,14) value=$row[5]>";
      echo "<input type=hidden name=curso value='$row[8]'>";

   }

if ($_POST[tra] == "Trab-Libreta" OR $_POST[tra] == "Trab-Libreta2")
   {
   if ($_POST[tri]=="Trimestre-1")
      {
      echo	"<input type=text id='nta".$a."' name=est($a,1) maxlength=3 size=3 tabindex='$p1' value='$row[12]' onblur='verp1()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntb".$a."' name=est($a,2) maxlength=3 size=3 tabindex='$p2' value='$row[13]' onblur='verp2()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntc".$a."' name=est($a,3) maxlength=3 size=3 tabindex='$p3' value='$row[14]' onblur='verp3()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntd".$a."' name=est($a,4) maxlength=3 size=3 tabindex='$p4' value='$row[15]' onblur='verp4()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='nte".$a."' name=est($a,5) maxlength=3 size=3 tabindex='$p5' value='$row[16]' onblur='verp5()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntf".$a."' name=est($a,6) maxlength=3 size=3 tabindex='$p6' value='$row[17]' onblur='verp6()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntg".$a."' name=est($a,7) maxlength=3 size=3 tabindex='$p7' value='$row[18]' onblur='verp7()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='nth".$a."' name=est($a,8) maxlength=3 size=3 tabindex='$p8' value='$row[19]' onblur='verp8()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='nti".$a."' name=est($a,9) maxlength=3 size=3 tabindex='$p9' value='$row[20]' onblur='verp9()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="53" align="center">';
      echo	"<input type=text id='ntj".$a."' name=est($a,10) maxlength=3 size=3 tabindex='$p10' value='$row[21]' onblur='verp10()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	"<td width='46' align='center'><b><font size='4'>$row[52]</font></b></td>";
      echo	"<td width='46' align='center'><b><font size='4'>$row[56]</font></b></td>";
      echo	"<td width='51' align='center'><b><font size='4'>$row[60]</font></b></td>";
      echo '</tr>';
      echo "<input type=hidden name=est($a,11) value=$row[56]>";
      echo "<input type=hidden name=est($a,12) value=$row[52]>";
      echo "<input type=hidden name=est($a,13) value=$row[60]>";
      }

   if ($_POST[tri]=="Trimestre-2")
      {
      echo	"<input type=text id='nta".$a."' name=est($a,1) maxlength=3 size=3 tabindex='$p1' value='$row[22]' onblur='verp1()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntb".$a."' name=est($a,2) maxlength=3 size=3 tabindex='$p2' value='$row[23]' onblur='verp2()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntc".$a."' name=est($a,3) maxlength=3 size=3 tabindex='$p3' value='$row[24]' onblur='verp3()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntd".$a."' name=est($a,4) maxlength=3 size=3 tabindex='$p4' value='$row[25]' onblur='verp4()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='nte".$a."' name=est($a,5) maxlength=3 size=3 tabindex='$p5' value='$row[26]' onblur='verp5()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntf".$a."' name=est($a,6) maxlength=3 size=3 tabindex='$p6' value='$row[27]' onblur='verp6()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntg".$a."' name=est($a,7) maxlength=3 size=3 tabindex='$p7' value='$row[28]' onblur='verp7()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='nth".$a."' name=est($a,8) maxlength=3 size=3 tabindex='$p8' value='$row[29]' onblur='verp8()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='nti".$a."' name=est($a,9) maxlength=3 size=3 tabindex='$p9' value='$row[30]' onblur='verp9()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="53" align="center">';
      echo	"<input type=text id='ntj".$a."' name=est($a,10) maxlength=3 size=3 tabindex='$p10' value='$row[31]' onblur='verp10()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	"<td width='46' align='center'><b><font size='4'>$row[53]</font></b></td>";
      echo	"<td width='46' align='center'><b><font size='4'>$row[57]</font></b></td>";
      echo	"<td width='51' align='center'><b><font size='4'>$row[61]</font></b></td>";
      echo '</tr>';
      echo "<input type=hidden name=est($a,11) value=$row[57]>";
      echo "<input type=hidden name=est($a,12) value=$row[53]>";
      echo "<input type=hidden name=est($a,13) value=$row[61]>";
      }

   if ($_POST[tri]=="Trimestre-3")
      {
      echo	"<input type=text id='nta".$a."' name=est($a,1) maxlength=3 size=3 tabindex='$p1' value='$row[32]' onblur='verp1()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntb".$a."' name=est($a,2) maxlength=3 size=3 tabindex='$p2' value='$row[33]' onblur='verp2()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntc".$a."' name=est($a,3) maxlength=3 size=3 tabindex='$p3' value='$row[34]' onblur='verp3()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntd".$a."' name=est($a,4) maxlength=3 size=3 tabindex='$p4' value='$row[35]' onblur='verp4()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='nte".$a."' name=est($a,5) maxlength=3 size=3 tabindex='$p5' value='$row[36]' onblur='verp5()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntf".$a."' name=est($a,6) maxlength=3 size=3 tabindex='$p6' value='$row[37]' onblur='verp6()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntg".$a."' name=est($a,7) maxlength=3 size=3 tabindex='$p7' value='$row[38]' onblur='verp7()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='nth".$a."' name=est($a,8) maxlength=3 size=3 tabindex='$p8' value='$row[39]' onblur='verp8()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='nti".$a."' name=est($a,9) maxlength=3 size=3 tabindex='$p9' value='$row[40]' onblur='verp9()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="53" align="center">';
      echo	"<input type=text id='ntj".$a."' name=est($a,10) maxlength=3 size=3 tabindex='$p10' value='$row[41]' onblur='verp10()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	"<td width='46' align='center'><b><font size='4'>$row[54]</font></b></td>";
      echo	"<td width='46' align='center'><b><font size='4'>$row[58]</font></b></td>";
      echo	"<td width='51' align='center'><b><font size='4'>$row[62]</font></b></td>";
      echo '</tr>';
      echo "<input type=hidden name=est($a,11) value=$row[58]>";
      echo "<input type=hidden name=est($a,12) value=$row[54]>";
      echo "<input type=hidden name=est($a,13) value=$row[62]>";
      }

   if ($_POST[tri]=="Trimestre-4")
      {
      echo	"<input type=text id='nta".$a."' name=est($a,1) maxlength=3 size=3 tabindex='$p1' value='$row[42]' onblur='verp1()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntb".$a."' name=est($a,2) maxlength=3 size=3 tabindex='$p2' value='$row[43]' onblur='verp2()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntc".$a."' name=est($a,3) maxlength=3 size=3 tabindex='$p3' value='$row[44]' onblur='verp3()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntd".$a."' name=est($a,4) maxlength=3 size=3 tabindex='$p4' value='$row[45]' onblur='verp4()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='nte".$a."' name=est($a,5) maxlength=3 size=3 tabindex='$p5' value='$row[46]' onblur='verp5()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntf".$a."' name=est($a,6) maxlength=3 size=3 tabindex='$p6' value='$row[47]' onblur='verp6()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='ntg".$a."' name=est($a,7) maxlength=3 size=3 tabindex='$p7' value='$row[48]' onblur='verp7()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='nth".$a."' name=est($a,8) maxlength=3 size=3 tabindex='$p8' value='$row[49]' onblur='verp8()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="46" align="center">';
      echo	"<input type=text id='nti".$a."' name=est($a,9) maxlength=3 size=3 tabindex='$p9' value='$row[50]' onblur='verp9()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	'<td width="53" align="center">';
      echo	"<input type=text id='ntj".$a."' name=est($a,10) maxlength=3 size=3 tabindex='$p10' value='$row[51]' onblur='verp10()' onclick='oscura($a)' onfocus='oscura($a)'></td>";
      echo	"<td width='46' align='center'><b><font size='4'>$row[55]</font></b></td>";
      echo	"<td width='46' align='center'><b><font size='4'>$row[59]</font></b></td>";
      echo	"<td width='51' align='center'><b><font size='4'>$row[63]</font></b></td>";
      echo '</tr>';
      echo "<input type=hidden name=est($a,11) value=$row[59]>";
      echo "<input type=hidden name=est($a,12) value=$row[55]>";
      echo "<input type=hidden name=est($a,13) value=$row[63]>";
      }

   echo "<input type=hidden name=est($a,14) value=$row[5]>";
   echo "<input type=hidden name=tri value=$_POST[tri]>";
   echo "<input type=hidden name=curso value='$row[8]'>";

   }
   }

}
echo '</table><br>';

include('valores.php');

echo '<center>';
if ($_POST[tri] == "Trimestre-1")
   {
   if (date("Y-m-d") < $reg6[30] or $reg2[93]==1 and $reg2[94]==1 or $reg2[93]==1 and $reg2[94]==5)
      {
      echo "<INPUT TYPE='SUBMIT' name='Grabar' value='Grabar' class='myButton' style='height: 28px; width: 140px'>";
      }
      ELSE
      {
      echo '<p align="center"><b><font size="5">Lo Sentimos, La fecha Ha Vencido o la ';
      echo 'Selección del trimestre equivocada.</font></b></p>';
      echo '<p align="center"><b><font size="5">Intentelo de Nuevo o Comuniquese con la ';
      echo 'Administración.</font></b></p>';
      }
   }
if ($_POST[tri] == "Trimestre-2" or $reg2[93]==1 and $reg2[94]==2 or $reg2[93]==1 and $reg2[94]==5)
   {
   if (date("Y-m-d") < $reg6[32])
      {
      echo "<INPUT TYPE='SUBMIT' name='Grabar' value='Grabar' class='myButton' style='height: 28px; width: 140px'>";
      }
      ELSE
      {
      echo '<p align="center"><b><font size="5">Lo Sentimos, La fecha Ha Vencido o la ';
      echo 'Selección del trimestre equivocada.</font></b></p>';
      echo '<p align="center"><b><font size="5">Intentelo de Nuevo o Comuniquese con la ';
      echo 'Administración.</font></b></p>';
      }
   }
if ($_POST[tri] == "Trimestre-3")
   {
   if (date("Y-m-d") < $reg6[34] or $reg2[93]==1 and $reg2[94]==3 or $reg2[93]==1 and $reg2[94]==5)
      {
      echo "<INPUT TYPE='SUBMIT' id='Grabar' name='Grabar' value='Grabar' class='myButton' style='height: 28px; width: 140px'>";
      }
      ELSE
      {
      echo '<p align="center"><b><font size="5">Lo Sentimos, La fecha Ha Vencido o la ';
      echo 'Selección del trimestre equivocada.</font></b></p>';
      echo '<p align="center"><b><font size="5">Intentelo de Nuevo o Comuniquese con la ';
      echo 'Administración.</font></b></p>';
      }
   }
if ($_POST[tri] == "Trimestre-4")
   {
   if (date("Y-m-d") < $reg6[36] or $reg2[93]==1 and $reg2[94]==4 or $reg2[93]==1 and $reg2[94]==5)
      {
      echo "<INPUT TYPE='SUBMIT' name='Grabar' value='Grabar' class='myButton' style='height: 28px; width: 140px'>";
      }
      ELSE
      {
      echo '<p align="center"><b><font size="5">Lo Sentimos, La fecha Ha Vencido o la ';
      echo 'Selección del trimestre equivocada.</font></b></p>';
      echo '<p align="center"><b><font size="5">Intentelo de Nuevo o Comuniquese con la ';
      echo 'Administración.</font></b></p>';
      }
   }
echo '<br>';
echo '</center><br>';


if ($_POST[tra] == "Ex-Final")
   {
?>   
  <body style="height: 100%; margin: 0px; padding: 0px;">
    <div align="center">
		<table border="0" width="68%" cellpadding="2" cellspacing="0" class="style3">
			<tr>
				<td bgcolor="#CCCCCC" align="center"><font size="4"><b>Tema</b></font></td>
				<td bgcolor="#CCCCCC" width="69" align="center"><font size="4">
				<b>Valor</b></font></td>
			<td width="150" bgcolor="#CCCCCC" align="center"><font size="4"><b>
			Fecha</b></font></td>
			</tr>
			<tr>
				<td bgcolor="#FFFFCC"> 
<input type='text' name=tema1 maxlength=45 size=55 value="<? echo $reg3[11];?> " tabindex="14"/></td>
				<td width="69" align="center" bgcolor="#FFFFCC">
				<p align="center"> 
<?  echo    "<input type=text name=val1 maxlength=3 size=3 value=$reg3[1]>"?>
               </td>
				<td width="150" bgcolor="#FFFFCC">
<?  echo    "<input type='text' id='cal-field-1' name='fec1' size='10' maxlength='10' value=$reg3[21]>"?>
          <button type="submit" id="cal-button-1" tabindex='15' class="myButton" style="width: 40px">...</button>
          <script type="text/javascript">
            Calendar.setup({
              inputField    : "cal-field-1",
              button        : "cal-button-1",
              align         : "Tr"
            });
          </script>
        		</td>
			</tr>
			<tr>
				<td bgcolor="#FFFFCC"> 
				&nbsp;</td>
				<td width="69" align="center" bgcolor="#FFFFCC">
				&nbsp;</td>
				<td width="150" bgcolor="#FFFFCC">
          	    &nbsp;</td>
			</tr>
			<tr>
				<td bgcolor="#FFFFCC"> 
				&nbsp;</td>
				<td width="69" align="center" bgcolor="#FFFFCC">
				&nbsp;</td>
				<td width="150" bgcolor="#FFFFCC">
          	    &nbsp;</td>
			</tr>
		</table>
   
   
  

<?   
  }
if ($_POST[tra] == "Trab-Diarios2" OR $_POST[tra] == "Notas" OR $_POST[tra] == "Trab-Diarios" OR $_POST[tra] == "Trab-Libreta" OR $_POST[tra] == "Trab-Libreta2" OR $_POST[tra] == "Pruebas-Cortas" OR $_POST[tra] == "V-Nota")
   {

if ($_POST[tra] == "Trab-Diarios2" OR $_POST[tra] == "Trab-Diarios" OR $_POST[tra] == "Trab-Libreta" OR $_POST[tra] == "Trab-Libreta2" OR $_POST[tra] == "Pruebas-Cortas" OR $_POST[tra] == "V-Nota11")
   {

?>   

&nbsp;<span class="style2"><div align="center"><span class="style4"><strong>*Recuerde ir a la pagina de notas y darle grabar para 
		tener los promédios correctos.*
   		</strong></span></div></span>
<? } ?>

    <div align="center">
		<table border="0" style="width: 64%" cellpadding="2" cellspacing="0">
			<tr>
				<td bgcolor="#CCCCCC" align="center" style="width: 36px">&nbsp;</td>
				<td bgcolor="#CCCCCC" align="center" style="width: 287px"><font size="4"><b>Tema</b></font></td>
				<td bgcolor="#CCCCCC" align="center" style="width: 90px"><font size="4">
				<b>Valor</b></font></td>
			<td bgcolor="#CCCCCC" align="center" style="width: 140px"><font size="4"><b>
			Fecha</b></font></td>
			<td bgcolor="#CCCCCC" align="center" style="width: 39px">&nbsp;</td>
			</tr>
			<tr>
				<td bgcolor="#FFFFCC" class="style1" style="width: 36px"> 
				1.<td bgcolor="#FFFFCC" style="width: 287px"> 
				<span class="style2"> 
<input type='text' name=tema1 maxlength=45 size=55 value="<? echo $reg3[11];?> " tabindex="360"/></td>
				<td align="center" bgcolor="#FFFFCC" style="width: 90px">
				<p align="center"> 
<?  echo    "<input type=text id='val1' name=val1 maxlength=3 size=3 tabindex='361' value=$reg3[1]>"?>
               </td>
				<td bgcolor="#FFFFCC" style="width: 140px">
<?  echo    "<input type='text' id='cal-field-1' name='fec1' size='10' maxlength='10' tabindex='362' value=$reg3[21]>"?>
          <button type="submit" id="cal-button-1" tabindex='363' class="myButton" style="width: 40px">
		  ...</button>
          <script type="text/javascript">
            Calendar.setup({
              inputField    : "cal-field-1",
              button        : "cal-button-1",
              align         : "Tr"
            });
          </script>
        		</td>
				<td bgcolor="#FFFFCC" class="style1" style="width: 39px">
				1.</td>
			</tr>
			<tr>
				<td bgcolor="#FFFFCC" class="style1" style="width: 36px"> 
				2.<td bgcolor="#FFFFCC" style="width: 287px"> 
<input type='text' name='tema2' maxlength='45' size='55' value="<? echo $reg3[12];?> " tabindex="364"/></td>
				<td align="center" bgcolor="#FFFFCC" style="width: 90px"> 
<?  echo    "<input type=text name=val2 maxlength=3 size=3 value='$reg3[2]'></td>"?>
				<td bgcolor="#FFFFCC" style="width: 140px">
<?  echo    "<input type='text' id='cal-field-2' name='fec2' size='10' maxlength='10' value=$reg3[22]>"?>
          <button type="submit" id="cal-button-2" tabindex='367' class="myButton" style="width: 40px">...</button>
          <script type="text/javascript">
            Calendar.setup({
              inputField    : "cal-field-2",
              button        : "cal-button-2"
            });
          </script>
        	</td>
				<td bgcolor="#FFFFCC" class="style1" style="width: 39px">
				2.</td>
			</tr>
			<tr>
				<td bgcolor="#FFFFCC" class="style1" style="width: 36px"> 
				3.<td bgcolor="#FFFFCC" style="width: 287px"> 
<input type=text name=tema3 maxlength=45 size=55 value="<? echo $reg3[13];?> " tabindex="368"/></td>
				<td align="center" bgcolor="#FFFFCC" style="width: 90px"> 
<?  echo    "<input type=text name=val3 maxlength=3 size=3 value=$reg3[3]></td>"?>
				<td bgcolor="#FFFFCC" style="width: 140px">
<?  echo    "<input type='text' id='cal-field-3' name='fec3' size='10' maxlength='10' value=$reg3[23]>"?>
          <button type="submit" id="cal-button-3" tabindex='371' class="myButton" style="width: 40px">...</button>
          <script type="text/javascript">
            Calendar.setup({
              inputField    : "cal-field-3",
              button        : "cal-button-3"
            });
          </script>
        	</td>
				<td bgcolor="#FFFFCC" class="style1" style="width: 39px">
				3.</td>
			</tr>
			<tr>
				<td bgcolor="#FFFFCC" class="style1" style="width: 36px"> 
				4.<td bgcolor="#FFFFCC" style="width: 287px"> 
<input type=text name=tema4 maxlength=45 size=55 value="<? echo $reg3[14];?> " tabindex="372"/></td>
				<td align="center" bgcolor="#FFFFCC" style="width: 90px"> 
<?  echo    "<input type=text name=val4 maxlength=3 size=3 value=$reg3[4]></td>"?>
				<td bgcolor="#FFFFCC" style="width: 140px">
<?  echo    "<input type='text' id='cal-field-4' name='fec4' size='10' maxlength='10' value=$reg3[24]>"?>
          <button type="submit" id="cal-button-4" tabindex='375' class="myButton" style="width: 40px">...</button>
          <script type="text/javascript">
            Calendar.setup({
              inputField    : "cal-field-4",
              button        : "cal-button-4"
            });
          </script>
        	</td>
				<td bgcolor="#FFFFCC" class="style1" style="width: 39px">
				4.</td>
			</tr>
			<tr>
				<td bgcolor="#FFFFCC" class="style1" style="width: 36px"> 
				5.<td bgcolor="#FFFFCC" style="width: 287px"> 
<input type=text name=tema5 maxlength=45 size=55 value="<? echo $reg3[15];?> " tabindex="376"/></td>
				<td align="center" bgcolor="#FFFFCC" style="width: 90px">
				<p align="center"> 
<?  echo    "<input type=text name=val5 maxlength=3 size=3 value=$reg3[5]>"?>
                 </td>
				<td bgcolor="#FFFFCC" style="width: 140px">
<?  echo    "<input type='text' id='cal-field-5' name='fec5' size='10' maxlength='10' value=$reg3[25]>"?>
          <button type="submit" id="cal-button-5" tabindex='379' class="myButton" style="width: 40px">...</button>
          <script type="text/javascript">
            Calendar.setup({
              inputField    : "cal-field-5",
              button        : "cal-button-5"
            });
          </script>
        		</td>
				<td bgcolor="#FFFFCC" class="style1" style="width: 39px">
				5.</td>
			</tr>
			<tr>
				<td bgcolor="#FFFFCC" class="style1" style="width: 36px"> 
				6.<td bgcolor="#FFFFCC" style="width: 287px"> 
<input type=text name=tema6 maxlength=45 size=55 value="<? echo $reg3[16];?> " tabindex="380"/></td>
				<td align="center" bgcolor="#FFFFCC" style="width: 90px">
<?  echo    "<input type=text name=val6 maxlength=3 size=3 value=$reg3[6]>"?>
                 </td>
				<td bgcolor="#FFFFCC" style="width: 140px">
<?  echo    "<input type='text' id='cal-field-6' name='fec6' size='10' maxlength='10' value=$reg3[26]>"?>
          <button type="submit" id="cal-button-6" tabindex='383' class="myButton" style="width: 40px">...</button>
          <script type="text/javascript">
            Calendar.setup({
              inputField    : "cal-field-6",
              button        : "cal-button-6"
            });
          </script>
        		</td>
				<td bgcolor="#FFFFCC" class="style1" style="width: 39px">
				6.</td>
			</tr>
			<tr>
				<td bgcolor="#FFFFCC" class="style1" style="width: 36px"> 
				7.<td bgcolor="#FFFFCC" style="width: 287px"> 
<input type=text name=tema7 maxlength=45 size=55 value="<? echo $reg3[17];?> " tabindex="384"/></td>
				<td align="center" bgcolor="#FFFFCC" style="width: 90px">
<?  echo    "<input type=text name=val7 maxlength=3 size=3 value=$reg3[7]>"?>
                </td>
				<td bgcolor="#FFFFCC" style="width: 140px">
<?  echo    "<input type='text' id='cal-field-7' name='fec7' size='10' maxlength='10' value=$reg3[27]>"?>
          <button type="submit" id="cal-button-7" tabindex='387' class="myButton" style="width: 40px">...</button>
          <script type="text/javascript">
            Calendar.setup({
              inputField    : "cal-field-7",
              button        : "cal-button-7"
            });
          </script>
        		</td>
				<td bgcolor="#FFFFCC" class="style1" style="width: 39px">
				7.</td>
			</tr>
<? 
if ($_POST[tra] == "Trab-Diarios2" OR $_POST[tra] == "Notas" OR $_POST[tra] == "Trab-Diarios" OR $_POST[tra] == "Trab-Libreta" OR $_POST[tra] == "Trab-Libreta2" OR $_POST[tra] == "Pruebas-Cortas")
   {
?>
			<tr>
				<td bgcolor="#FFFFCC" class="style1" style="width: 36px"> 
				8.<td bgcolor="#FFFFCC" style="width: 287px"> 
<input type=text name=tema8 maxlength=45 size=55 value="<? echo $reg3[18];?> " tabindex="388"/></td>
				<td align="center" bgcolor="#FFFFCC" style="width: 90px">
<?  echo    "<input type=text name=val8 maxlength=3 size=3 value=$reg3[8]>"?>
                </td>
				<td bgcolor="#FFFFCC" style="width: 140px">
<?  echo    "<input type='text' id='cal-field-8' name='fec8' size='10' maxlength='10' value=$reg3[28]>"?>
          <button type="submit" id="cal-button-8" tabindex='391' class="myButton" style="width: 40px">...</button>
          <script type="text/javascript">
            Calendar.setup({
              inputField    : "cal-field-8",
              button        : "cal-button-8"
            });
          </script>
        		</td>
				<td bgcolor="#FFFFCC" class="style1" style="width: 39px">
				8.</td>
			</tr>
			<tr>
				<td bgcolor="#FFFFCC" class="style1" style="width: 36px"> 
				9.<td bgcolor="#FFFFCC" style="width: 287px"> 
<input type=text name=tema9 maxlength=45 size=55 value="<? echo $reg3[19];?> " tabindex="392"/></td>
				<td align="center" bgcolor="#FFFFCC" style="width: 90px">
<?  echo    "<input type=text name=val9 maxlength=3 size=3 value=$reg3[9]>"?>
                </td>
				<td bgcolor="#FFFFCC" style="width: 140px">
<?  echo    "<input type='text' id='cal-field-9' name='fec9' size='10' maxlength='10' value=$reg3[29]>"?>
          <button type="submit" id="cal-button-9" tabindex='395' class="myButton" style="width: 40px">...</button>
          <script type="text/javascript">
            Calendar.setup({
              inputField    : "cal-field-9",
              button        : "cal-button-9"
            });
          </script>
        		</td>
				<td bgcolor="#FFFFCC" class="style1" style="width: 39px">
				9.</td>
			</tr>
			<tr>
				<td bgcolor="#FFFFCC" class="style1" style="width: 36px"> 
				10.<td bgcolor="#FFFFCC" style="width: 287px"> 
<input type=text name=tema10 maxlength=45 size=55 value="<? echo $reg3[20];?> " tabindex="396"/></td>
				<td align="center" bgcolor="#FFFFCC" style="width: 90px">
<?  echo    "<input type=text name=val10 maxlength=3 size=3 value=$reg3[10]>"?>
                </td>
				<td bgcolor="#FFFFCC" style="width: 140px">
<?  echo    "<input type='text' id='cal-field-10' name='fec10' size='10' maxlength='10' value=$reg3[30]>"?>
          <button type="submit" id="cal-button-10" tabindex='399' class="myButton" style="width: 40px">...</button>
          <script type="text/javascript">
            Calendar.setup({
              inputField    : "cal-field-10",
              button        : "cal-button-10"
            });
          </script>
        		</td>
				<td bgcolor="#FFFFCC" class="style1" style="width: 39px">
				10.</td>
			</tr>
<?
if ($reg6[168]=='Si')
   {
?>

			<tr>
				<td bgcolor="#FFFFCC" class="style1" style="width: 36px"> 
				11.<td bgcolor="#FFFFCC" style="width: 287px"> 
<input type=text name=tema11 maxlength=45 size=55 value="<? echo $reg3[49];?> " tabindex="396"/></td>
				<td align="center" bgcolor="#FFFFCC" style="width: 90px">
<?  echo    "<input type=text name=val11 maxlength=3 size=3 value=$reg3[47]>"?>
                </td>
				<td bgcolor="#FFFFCC" style="width: 140px">
<?  echo    "<input type='text' id='cal-field-11' name='fec11' size='10' maxlength='10' value=$reg3[51]>"?>
          <button type="submit" id="cal-button-11" tabindex='399' class="myButton" style="width: 40px">...</button>
          <script type="text/javascript">
            Calendar.setup({
              inputField    : "cal-field-11",
              button        : "cal-button-11"
            });
          </script>
        		</td>
				<td bgcolor="#FFFFCC" class="style1" style="width: 39px">
				11.</td>
			</tr>
			<tr>
				<td bgcolor="#FFFFCC" class="style1" style="width: 36px"> 
				12.<td bgcolor="#FFFFCC" style="width: 287px"> 
<input type=text name=tema12 maxlength=45 size=55 value="<? echo $reg3[50];?> " tabindex="396"/></td>
				<td align="center" bgcolor="#FFFFCC" style="width: 90px">
<?  echo    "<input type=text name=val12 maxlength=3 size=3 value=$reg3[48]>"?>
                </td>
				<td bgcolor="#FFFFCC" style="width: 140px">
<?  echo    "<input type='text' id='cal-field-12' name='fec12' size='10' maxlength='10' value=$reg3[52]>"?>
          <button type="submit" id="cal-button-12" tabindex='399' class="myButton" style="width: 40px">...</button>
          <script type="text/javascript">
            Calendar.setup({
              inputField    : "cal-field-12",
              button        : "cal-button-12"
            });
          </script>
        		</td>
				<td bgcolor="#FFFFCC" class="style1" style="width: 39px">
				12.</td>
			</tr>

<? } ?>


			<tr>
				<td bgcolor="#FFFFCC" style="width: 36px"> 
				&nbsp;</td>
				<td bgcolor="#FFFFCC" style="width: 287px"> 
				&nbsp;</td>
				<td align="center" bgcolor="#FFFFCC" style="width: 90px">
				&nbsp;</td>
				<td bgcolor="#FFFFCC" style="width: 140px">
          	  &nbsp;</td>
				<td bgcolor="#FFFFCC" style="width: 39px">
          	  &nbsp;</td>
			</tr>
<?     } ?>
		</table><br>
	</div>

<?   
//  </body>

   }
echo '<center>';
if ($_POST[tri] == "Trimestre-1")
   {
   if (date("Y-m-d") < $reg6[30] or $reg2[93]==1 and $reg2[94]==1 or $reg2[93]==1 and $reg2[94]==5)
      {
      echo "<INPUT TYPE='SUBMIT' name='Grabar' id='Gra' value='Grabar' class='myButton' style='height: 28px; width: 140px'>";
      }
      ELSE
      {
      echo '<p align="center"><b><font size="5">Lo Sentimos, La fecha Ha Vencido o la ';
      echo 'Selección del trimestre equivocada.</font></b></p>';
      echo '<p align="center"><b><font size="5">Intentelo de Nuevo o Comuniquese con la ';
      echo 'Administración.</font></b></p>';
      }
   }
if ($_POST[tri] == "Trimestre-2")
   {
   if (date("Y-m-d") < $reg6[32] or $reg2[93]==1 and $reg2[94]==2 or $reg2[93]==1 and $reg2[94]==5)
      {
      echo "<INPUT TYPE='SUBMIT' name='Grabar' value='Grabar' class='myButton' style='height: 28px; width: 140px'>";
      }
      ELSE
      {
      echo '<p align="center"><b><font size="5">Lo Sentimos, La fecha Ha Vencido o la ';
      echo 'Selección del trimestre equivocada.</font></b></p>';
      echo '<p align="center"><b><font size="5">Intentelo de Nuevo o Comuniquese con la ';
      echo 'Administración.</font></b></p>';
      }
   }
if ($_POST[tri] == "Trimestre-3")
   {
   if (date("Y-m-d") < $reg6[34] or $reg2[93]==1 and $reg2[94]==3 or $reg2[93]==1 and $reg2[94]==5)
      {
      echo "<INPUT TYPE='SUBMIT' name='Grabar' value='Grabar' class='myButton' style='height: 28px; width: 140px'>";
      }
      ELSE
      {
      echo '<p align="center"><b><font size="5">Lo Sentimos, La fecha Ha Vencido o la ';
      echo 'Selección del trimestre equivocada.</font></b></p>';
      echo '<p align="center"><b><font size="5">Intentelo de Nuevo o Comuniquese con la ';
      echo 'Administración.</font></b></p>';
      }
   }
if ($_POST[tri] == "Trimestre-4")
   {
   if (date("Y-m-d") < $reg6[36] or $reg2[93]==1 and $reg2[94]==4 or $reg2[93]==1 and $reg2[94]==5)
      {
      echo "<INPUT TYPE='SUBMIT' name='Grabar' value='Grabar' class='myButton' style='height: 28px; width: 140px'>";
      }
      ELSE
      {
      echo '<p align="center"><b><font size="5">Lo Sentimos, La fecha Ha Vencido o la ';
      echo 'Selección del trimestre equivocada.</font></b></p>';
      echo '<p align="center"><b><font size="5">Intentelo de Nuevo o Comuniquese con la ';
      echo 'Administración.</font></b></p>';
      }
   }

if ($_POST[tri] == "Verano")
   {
   if (date("Y-m-d") < $reg6[95])
      {
      echo "<INPUT TYPE='SUBMIT' name='Grabar' id='Gra' value='Grabar' class='myButton' style='height: 28px; width: 140px'>";
      }
      ELSE
      {
      echo '<p align="center"><b><font size="5">Lo Sentimos, La fecha Ha Vencido o la ';
      echo 'Selección del trimestre equivocada.</font></b></p>';
      echo '<p align="center"><b><font size="5">Intentelo de Nuevo o Comuniquese con la ';
      echo 'Administración.</font></b></p>';
      }
   }


echo '<br>';
echo '</center>';
echo "</FORM>";
echo '<br>';
echo '<br>';
echo '<br>';
echo '<br>';
   mysql_close($con);
   mysql_close($db);
   mysql_close($dbh);

?>
      

</body>
</html>

<script src="http://code.jquery.com/jquery-git2.js"></script>
<script>

// Al presionar cualquier tecla en cualquier campo de texto, ejectuamos la siguiente función
$('input').on('keydown', function(e){
    // Solo nos importa si la tecla presionada fue ENTER... (Para ver el código de otras teclas: http://www.webonweboff.com/tips/js/event_key_codes.aspx)
    if(e.keyCode === 13 || e.keyCode === 40)
      {
      // Obtenemos el número del tabindex del campo actual
      var currentTabIndex = $(this).attr('tabindex');
      // Le sumamos 1 :P
      var nextTabIndex    = parseInt(currentTabIndex) + 1;
      // Obtenemos (si existe) el siguiente elemento usando la variable nextTabIndex
      var nextField       = $('[tabindex='+nextTabIndex+']');
      // Si se encontró un elemento:
      if(nextField.length > 0)
        {
        // Hacerle focus / seleccionarlo
        nextField.focus();
        // Ignorar el funcionamiento predeterminado (enviar el formulario)
        e.preventDefault();
        }
      // Si no se encontro ningún elemento, no hacemos nada (se envia el formulario)
      }
    if(e.keyCode === 38 )
      {
      var currentTabIndex = $(this).attr('tabindex');
      // Le sumamos 1 :P
      var nextTabIndex    = parseInt(currentTabIndex) - 1;
      // Obtenemos (si existe) el siguiente elemento usando la variable nextTabIndex
      var nextField       = $('[tabindex='+nextTabIndex+']');
      // Si se encontró un elemento:
      if(nextField.length > 0)
        {
        // Hacerle focus / seleccionarlo
        nextField.focus();
        // Ignorar el funcionamiento predeterminado (enviar el formulario)
        e.preventDefault();
        }
      }
    if(e.keyCode === 37 )
      {
      var currentTabIndex = $(this).attr('tabindex');
      // Le sumamos 1 :P
      var nextTabIndex    = parseInt(currentTabIndex) - <? echo $num_resultados ?>;
      // Obtenemos (si existe) el siguiente elemento usando la variable nextTabIndex
      var nextField       = $('[tabindex='+nextTabIndex+']');
      // Si se encontró un elemento:
      if(nextField.length > 0)
        {
        // Hacerle focus / seleccionarlo
        nextField.focus();
        // Ignorar el funcionamiento predeterminado (enviar el formulario)
        e.preventDefault();
        }
      }
    if(e.keyCode === 39 )
      {
      var currentTabIndex = $(this).attr('tabindex');
      // Le sumamos 1 :P
      var nextTabIndex    = parseInt(currentTabIndex) + <? echo $num_resultados ?>;
      // Obtenemos (si existe) el siguiente elemento usando la variable nextTabIndex
      var nextField       = $('[tabindex='+nextTabIndex+']');
      // Si se encontró un elemento:
      if(nextField.length > 0)
        {
        // Hacerle focus / seleccionarlo
        nextField.focus();
        // Ignorar el funcionamiento predeterminado (enviar el formulario)
        e.preventDefault();
        }
      }

      });

</script>
<script type="text/javascript" src="valores.js"></script>

<?

//<script type="text/javascript" src="valores.js"></script>
?>