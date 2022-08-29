<?
include '../../geoip/geoip.php';

//echo "Ciudad: ".GeoCity(); 
//echo "<br> Codigo del pais: ".GeoCountryCode(); 
//echo "<br> Nombre del pais: ".GeoCountryName(); 

if (GeoCountryName()!='Puerto Rico')
   {if (GeoCity()!='Deltona')
       {
       if (GeoCity()!='Sunnyside' && GeoCity()!='Brooklyn'){
          // exit;
          }
       }
   }

//session_destroy();
session_start();
//$_SESSION['usua1']=$_POST['usua'];
?>
<html>
<head>
<title>Regiweb</title>
</head>
<body>
<?
  if (!$_POST['usua'] || !$_POST['claves'])
  {
     echo "<br><br><center>No has introducido los detalles del Usuario.  Por favor vuelve e intntalo de nuevo.</center>";
     echo "<body onload='FP_preloadImgs(/*url*/'../../images/button4.gif', /*url*/'../../images/button11.gif')'>";
     echo '<p align="center">';
     echo "<a href='javascript:history.back()'>";
     echo "<img border='0' id='img1' src='../../images/button12.gif' height='26' width='200' alt='Pagina Anterior' onmouseover='FP_swapImg(1,0,/*id*/'img1',/*url*/'../../images/button4.gif')' onmouseout='FP_swapImg(0,0,/*id*/'img1',/*url*/'../../images/button12.gif')' onmousedown='FP_swapImg(1,0,/*id*/'img1',/*url*/'../../images/button11.gif')' onmouseup='FP_swapImg(0,0,/*id*/'img1',/*url*/'../../images/button4.gif')' fp-style='fp-btn: Embossed Capsule 6; fp-font-style: Bold; fp-font-size: 12; fp-transparent: 1; fp-proportional: 0' fp-title='Pagina Anterior'></a></p>";
     exit;
  }

  $usua = $_POST['usua'];
  $_SESSION['usua1']=$usua;
  $claves = addslashes($_POST['claves']);
  include('../control.php');
  $uup="UPDATE `colegio` SET `nivel` = 'A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-A-' WHERE `colegio`.`id` = '7777777'";
  mysql_query($uup);
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

  $consulta = "select * from colegio where usuario = '$usua' AND clave = '$claves'";
//  $consulta = "select * from colegio where usuario = 'administrador'";

//  es que se paresca $consulta = "select * from miembros where usuario like '%".$usua."%' AND clave like '%".$claves."%'";
  $resultado = mysql_query($consulta);
  $num_resultados = mysql_num_rows($resultado);

if ($num_resultados == 0)
   {
   echo "<br><br><p><center><strong>"."El usuario o clave es Incorrecto, trate de nuevo. "."</strong></center>";
   echo "<body onload='FP_preloadImgs(/*url*/'../../images/button4.gif', /*url*/'../../images/button11.gif')'>";
   echo '<p align="center">';
   echo "<a href='javascript:history.back()'>";
   echo "<img border='0' id='img1' src='../../images/button12.gif' height='26' width='200' alt='Pagina Anterior' onmouseover='FP_swapImg(1,0,/*id*/'img1',/*url*/'../../images/button4.gif')' onmouseout='FP_swapImg(0,0,/*id*/'img1',/*url*/'../../images/button12.gif')' onmousedown='FP_swapImg(1,0,/*id*/'img1',/*url*/'../../images/button11.gif')' onmouseup='FP_swapImg(0,0,/*id*/'img1',/*url*/'../../images/button4.gif')' fp-style='fp-btn: Embossed Capsule 6; fp-font-style: Bold; fp-font-size: 12; fp-transparent: 1; fp-proportional: 0' fp-title='Pagina Anterior'></a></p>";
   exit;
   }
else

  for ($i=0; $i <$num_resultados; $i++)
  {
     $row = mysql_fetch_array($resultado);
     echo '<center>';
     $grup = stripslashes($row["grupo"]);
     $id = stripslashes($row["id"]);
     $nombre = stripslashes($row["director"]);
//     $apellidos = stripslashes($row["apellidos"]);
     $activo1 = stripslashes($row["activo"]);
     $ufecha = stripslashes($row["ufecha"]);
     $_SESSION['id1']=stripslashes($row["id"]);
     date_default_timezone_set("America/La_Paz");
     $hora = getdate(time());
     $hora1=$hora["hours"].":".$hora["minutes"].":".$hora["seconds"];
     $date1=date('Y-m-d');
    echo '<p align="center"><b><font size="6">REGIWEB</font></b></p>';
    echo '<hr>';
    echo '<br>';
    echo 'Bienvenido al sistema de cafeteria';
    echo '<br>';
    echo '<br>';
   $data2 = "select * from entradas where usuario = '$usua'";
   $tabla2 = mysql_query($data2, $con) or die ("problema con query") ;
   $query2 = "insert into entradas (id,usuario,fecha,hora,ip,nombre,apellidos)
   values  ('$id','$usua','$date1','$hora1','$_SERVER[REMOTE_ADDR]','$nombre','')";
   $result2 = mysql_query($query2);

   $q = "update colegio set ufecha='$date1' where usuario='$usua'";
   $result3 = mysql_query($q);

echo  '<div align="center">';
echo  '<table border="1" width="60%">';
echo    '<tr>';
echo      '<td width="170" bgcolor="#CCCCCC" align="right"><b><font size="4">Nombre:</font></b></td>';
echo      '<td>'.stripslashes($row["director"]).'</td>';
echo	'</tr>';

echo    '<tr>';
echo      '<td width="170" bgcolor="#CCCCCC" align="right"><b><font size="4"></font></b></td>';
echo      '<td>'.''.'</td>';
echo    '</tr>';
echo    '<tr>';
echo      '<td width="170" bgcolor="#CCCCCC" align="right"><b><font size="4">Grupo:</font></b></td>';
echo      '<td>'.stripslashes($row["grupo"]).'</td>';
echo    '</tr>';
echo    '<tr>';
echo      '<td width="170" bgcolor="#CCCCCC" align="right"><b><font size="4">Ultima Entrada:</font></b></td>';
echo      '<td>'.$ufecha.'</td>';
echo    '</tr>';
echo    '<tr>';
echo      '<td width="170" bgcolor="#CCCCCC" align="right"><b><font size="4">IP:</font></b></td>';
echo      '<td>'.$_SERVER["REMOTE_ADDR"].'</td>';
echo    '</tr>';
echo    '<tr>';
echo      '<td width="170" bgcolor="#CCCCCC" align="right"><b><font size="4">Hora:</font></b></td>';
echo      '<td>'.$hora["hours"].":".$hora["minutes"].":".$hora["seconds"].'</td>';
echo    '</tr>';
echo  '</table>';
echo '</div>';
     
  }

if ($activo1 == "2")
   {
   echo "la cuenta esta Inactivada";
   echo '<br>'."Comuniquese con la administracin";
   exit;
   }
if ($grup == "Administrador")
   {
    echo '<br>';
    echo '<br>';
    echo '</center>';
    echo '<p align="center"><a href="menu.php">';
    echo "<img border='0' id='img1' src='../../images/button1D.gif' height=28 width='150' alt='Continuar' onmouseover='FP_swapImg(1,0,/*id*/&#039;img1&#039;,/*url*/&#039;../../images/button1E.gif&#039;)'img1',/*url*/'buttonE.jpg')' onmouseout='FP_swapImg(0,0,/*id*/&#039;img1&#039;,/*url*/&#039;../../images/button1D.gif&#039;)'img1',/*url*/'buttonD.jpg')' onmousedown='FP_swapImg(1,0,/*id*/&#039;img1&#039;,/*url*/&#039;../../images/button1F.gif&#039;)'img1',/*url*/'buttonF.jpg')' onmouseup='FP_swapImg(0,0,/*id*/&#039;img1&#039;,/*url*/&#039;../../images/button1E.gif&#039;)'img1',/*url*/'buttonE.jpg')' fp-style='fp-btn: Embossed Capsule 6; fp-font-style: Bold; fp-font-size: 12; fp-transparent: 1; fp-proportional: 0' fp-title='Continuar'></a></p>";
   }
   

   echo '<br>';
   echo '<br>';
   echo '<hr>';
?>

</body>
</html>
