<?
session_start();
$id = $_SESSION['id1'];
$usua = $_SESSION['usua1'];

if ($usua == "") {
   exit;
}

include('../control.php');
$consult1 = "select * from colegio where usuario = 'administrador'";
$resultad1 = mysql_query($consult1);
$row = mysql_fetch_array($resultad1);

if (isset($_POST['rank'])) {
   $tde = 0;
   $sq1 = "select * from year where grado LIKE '%" . $_POST['grado2'] . "%' and year = '" . $_POST['year2'] . "'";
   $res1 = mysql_query($sq1);
   while ($row3 = mysql_fetch_array($res1)) {
      $sq2 = "select * from acumulativa where ss = '$row3[0]' and grado = '$row3[2]' and year = '" . $_POST['year2'] . "'";
      $res2 = mysql_query($sq2);
      $let1 = 0;
      $let2 = 0;
      $cnt1 = 0;
      $cnt2 = 0;
      while ($row1 = mysql_fetch_array($res2)) {
         if (substr($row1[5], 0, 3) != 'SHO') {
            if ($row1[8] >= $row[66] and $row1[8] < $row[65]) {
               $s1 = 'F';
               if ($row1[7] > 0) {
                  $let1 = $let1 + 0;
                  $cnt1 = $cnt1 + $row1[7];
               }
            }
            if ($row1[8] >= $row[65] and $row1[8] < $row[64]) {
               $s1 = 'D';
               if ($row1[7] > 0) {
                  $let1 = $let1 + (1 * $row1[7]);
                  $cnt1 = $cnt1 + $row1[7];
               }
            }
            if ($row1[8] >= $row[64] and $row1[8] < $row[63]) {
               $s1 = 'C';
               if ($row1[7] > 0) {
                  $let1 = $let1 + (2 * $row1[7]);
                  $cnt1 = $cnt1 + $row1[7];
               }
            }
            if ($row1[8] >= $row[63] and $row1[8] < $row[62]) {
               $s1 = 'B';
               if ($row1[7] > 0) {
                  $let1 = $let1 + (3 * $row1[7]);
                  $cnt1 = $cnt1 + $row1[7];
               }
            }
            if ($row1[8] >= $row[62]) {
               $s1 = 'A';
               if ($row1[7] > 0) {
                  $let1 = $let1 + (4 * $row1[7]);
                  $cnt1 = $cnt1 + $row1[7];
               }
            }
            if ($row1[9] >= $row[66] and $row1[9] < $row[65]) {
               $s2 = 'F';
               if ($row1[7] > 0) {
                  $let2 = $let2 + 0;
                  $cnt2 = $cnt2 + $row1[7];
               }
            }
            if ($row1[9] >= $row[65] and $row1[9] < $row[64]) {
               $s2 = 'D';
               if ($row1[7] > 0) {
                  $let2 = $let2 + (1 * $row1[7]);
                  $cnt2 = $cnt2 + $row1[7];
               }
            }
            if ($row1[9] >= $row[64] and $row1[9] < $row[63]) {
               $s2 = 'C';
               if ($row1[7] > 0) {
                  $let2 = $let2 + (2 * $row1[7]);
                  $cnt2 = $cnt2 + $row1[7];
               }
            }
            if ($row1[9] >= $row[63] and $row1[9] < $row[62]) {
               $s2 = 'B';
               if ($row1[7] > 0) {
                  $let2 = $let2 + (3 * $row1[7]);
                  $cnt2 = $cnt2 + $row1[7];
               }
            }
            if ($row1[9] >= $row[62]) {
               $s2 = 'A';
               if ($row1[7] > 0) {
                  $let2 = $let2 + (4 * $row1[7]);
                  $cnt2 = $cnt2 + $row1[7];
               }
            }
            if ($row1[22] == 'Si' and $row1[8] >= $row[64]) {
               $let1 = $let1 + 1;
            }
            if ($row1[22] == 'Si' and $row1[9] >= $row[64]) {
               $let2 = $let2 + 1;
            }
         }
      }
      if ($cnt1 > 0 or $cnt2 > 0) {
         $tde = $tde + 1;
         $query = "insert into acumula_totales (ss,nombre,apellidos) values ('$row3[0]','$row3[3]','$row3[4]')";
         $result1 = mysql_query($query);
         if ($_POST['grado2'] == '09') {
            $sql = "UPDATE acumula_totales SET gr1='" . $_POST['grado2'] . "', ye1='" . $_POST['year2'] . "', not1='" . round(($let1 + $let2) / ($cnt1 + $cnt2), 2) . "' WHERE ss='$row3[0]'";
         }
         if ($_POST['grado2'] == '10') {
            $sql = "UPDATE acumula_totales SET gr2='" . $_POST['grado2'] . "', ye2='" . $_POST['year2'] . "', not2='" . round(($let1 + $let2) / ($cnt1 + $cnt2), 2) . "' WHERE ss='$row3[0]'";
         }
         if ($_POST['grado2'] == '11') {
            $sql = "UPDATE acumula_totales SET gr3='" . $_POST['grado2'] . "', ye3='" . $_POST['year2'] . "', not3='" . round(($let1 + $let2) / ($cnt1 + $cnt2), 2) . "' WHERE ss='$row3[0]'";
         }
         if ($_POST['grado2'] == '12') {
            $sql = "UPDATE acumula_totales SET gr4='" . $_POST['grado2'] . "', ye4='" . $_POST['year2'] . "', not4='" . round(($let1 + $let2) / ($cnt1 + $cnt2), 2) . "' WHERE ss='$row3[0]'";
         }
         $result = mysql_query($sql);
      }
   }
   if ($tde > 0) {
      if ($_POST['grado2'] == '09') {
         $sq1 = "select * from acumula_totales where gr1 LIKE '%" . $_POST['grado2'] . "%' and ye1 = '" . $_POST['year2'] . "' ORDER BY not1 DESC";
      }
      if ($_POST['grado2'] == '10') {
         $sq1 = "select * from acumula_totales where gr2 LIKE '%" . $_POST['grado2'] . "%' and ye2 = '" . $_POST['year2'] . "' ORDER BY not2 DESC";
      }
      if ($_POST['grado2'] == '11') {
         $sq1 = "select * from acumula_totales where gr3 LIKE '%" . $_POST['grado2'] . "%' and ye3 = '" . $_POST['year2'] . "' ORDER BY not3 DESC";
      }
      if ($_POST['grado2'] == '12') {
         $sq1 = "select * from acumula_totales where gr4 LIKE '%" . $_POST['grado2'] . "%' and ye4 = '" . $_POST['year2'] . "' ORDER BY not4 DESC";
      }
      $res1 = mysql_query($sq1);
      $res2 = mysql_query($sq2);
      $row4 = mysql_fetch_array($res2);
      $nota = $row4[3];
      $po = 1;
      $po1 = 0;
      while ($row3 = mysql_fetch_array($res1)) {
         if ($nota == $row3[3]) {
            $po1 = $po1 + 1;
         } else {
            $po = $po + $po1;
            $po1 = 1;
            $nota = $row3[3];
         }
         if ($_POST['grado2'] == '09') {
            $sql = "UPDATE acumula_totales SET ran1='" . $po . "', te1='$tde' WHERE ss='$row3[0]'";
         }
         if ($_POST['grado2'] == '10') {
            $sql = "UPDATE acumula_totales SET ran2='" . $po . "', te2='$tde' WHERE ss='$row3[0]'";
         }
         if ($_POST['grado2'] == '11') {
            $sql = "UPDATE acumula_totales SET ran3='" . $po . "', te3='$tde' WHERE ss='$row3[0]'";
         }
         if ($_POST['grado2'] == '12') {
            $sql = "UPDATE acumula_totales SET ran4='" . $po . "', te4='$tde' WHERE ss='$row3[0]'";
         }
         $result = mysql_query($sql);
      }
   }
}



if (isset($_POST['trans'])) {
   //<script> window.open("acum_tarjeta.php"); </script>
   //echo '<script type="text/javascript" language="javascript"> 
   //window.open("acum_tarjeta.php"); 
   //</script>'; 

   //  require('acum_tarjeta.php');
   //  exit;
}

if (isset($_POST['curs'])) {
   require('acumula_cursos.php');
   exit;
}

if (isset($_POST['acti'])) {
   require('clubes.php');
   exit;
}
if (isset($_POST['add_edit'])) {
   header('Location: add_edit.php');
   exit;
}

$consult1 = "select * from colegio where usuario = '$usua'";
$resultad1 = mysql_query($consult1);
$row2 = mysql_fetch_array($resultad1);


if (isset($_POST['pasar'])) {
   $q = "select * from padres where year='$_POST[year1]' ORDER BY apellidos ASC";
//   $tabla1 = mysql_query($q, $dbh) or die("problema con query 1");
//   $reg = mysql_fetch_row($tabla1);
   $result1 = mysql_query($q);
   $ss='';
   while ($row = mysql_fetch_array($result1)) {

      if ($_POST['data1'] == 'TK1') {
         $q = "select * from tablas where notas='$row[19]'";
         $tabla = mysql_query($q, $dbh) or die("problema con query 20");
         $reg1 = mysql_fetch_row($tabla);
         $se1 = $reg1[1];

         $sql = "INSERT INTO acumulativa (ss, nombre, apellidos, year, grado, curso, desc1, credito, sem1, orden, peso) " .
            "VALUES ('$row[56]', '$row[3]', '$row[4]', '$row[94]', '$row[6]', '$row[7]', '$row[5]', '$row[21]', '$se1', '$row[136]', '$row[26]')";
      }

      if ($_POST['data1'] == 'TK2') {
         $q = "select * from tablas where notas='$row[20]'";
         $tabla = mysql_query($q, $dbh) or die("problema con query 20");
         $reg1 = mysql_fetch_row($tabla);
         $se2 = $reg1[1];
         $sql = "INSERT INTO acumulativa (ss, nombre, apellidos, year, grado, curso, desc1, credito, sem2, orden, peso) " .
            "VALUES ('$row[56]', '$row[3]', '$row[4]', '$row[94]', '$row[6]', '$row[7]', '$row[5]', '$row[21]', '$se2', '$row[136]', '$row[26]')";
      }


      if ($_POST['data1'] == 'D1') {
         $sql = "INSERT INTO acumulativa (ss, nombre, apellidos, year, grado, curso, desc1, credito, sem1, orden, peso) " .
            "VALUES ('$row[56]', '$row[3]', '$row[4]', '$row[94]', '$row[6]', '$row[7]', '$row[5]', '$row[21]', '$row[12]', '$row[136]', '$row[26]')";
      }

      if ($_POST['data1'] == 'D2') {
         $sql = "INSERT INTO acumulativa (ss, nombre, apellidos, year, grado, curso, desc1, credito, sem2, orden, peso) " .
            "VALUES ('$row[56]', '$row[3]', '$row[4]', '$row[94]', '$row[6]', '$row[7]', '$row[5]', '$row[21]', '$row[14]', '$row[136]', '$row[26]')";
      }

      if ($_POST['data1'] == 'D3') {
         $sql = "INSERT INTO acumulativa (ss, nombre, apellidos, year, grado, curso, desc1, credito, sem1, sem2, orden, peso) " .
            "VALUES ('$row[56]', '$row[3]', '$row[4]', '$row[94]', '$row[6]', '$row[7]', '$row[5]', '$row[21]', '$row[12]', '$row[14]', '$row[136]', '$row[26]')";
      }

      if ($_POST['data1'] == 'A') {
         $sql = "INSERT INTO acumulativa (ss, nombre, apellidos, year, grado, curso, desc1, credito, sem1, orden) " .
            "VALUES ('$row[56]', '$row[3]', '$row[4]', '$row[94]', '$row[6]', '$row[7]', '$row[5]', '$row[21]', '$row[19]', '$row[136]')";
      }

      if ($_POST['data1'] == 'B') {
         $sql = "INSERT INTO acumulativa (ss, nombre, apellidos, year, grado, curso, desc1, credito, sem2, orden) " .
            "VALUES ('$row[56]', '$row[3]', '$row[4]', '$row[94]', '$row[6]', '$row[7]', '$row[5]', '$row[21]', '$row[20]', '$row[136]')";
      }

      if ($_POST['data1'] == 'C') {
         $sql = "INSERT INTO acumulativa (ss, nombre, apellidos, year, grado, curso, desc1, credito, sem1, sem2, orden) " .
            "VALUES ('$row[56]', '$row[3]', '$row[4]', '$row[94]', '$row[6]', '$row[7]', '$row[5]', '$row[21]', '$row[19]', '$row[20]', '$row[136]')";
      }

      $result = mysql_query($sql);

      if ($_POST['data1'] == 'D1') {
         $sql = "UPDATE acumulativa SET sem1='$row[12]',orden='$row[136]',peso='$row[26]' WHERE ss='$row[56]' AND year='$row[94]' AND curso='$row[7]'";
      }
      if ($_POST['data1'] == 'D2') {
         $sql = "UPDATE acumulativa SET sem2='$row[14]',orden='$row[136]',peso='$row[26]' WHERE ss='$row[56]' AND year='$row[94]' AND curso='$row[7]'";
      }
      if ($_POST['data1'] == 'D3') {
         $sql = "UPDATE acumulativa SET sem1='$row[12]',sem2='$row[14]',orden='$row[136]',peso='$row[26]' WHERE ss='$row[56]' AND year='$row[94]' AND curso='$row[7]'";
      }

      $aus='';
      $tar='';
      if ($_POST['data1'] == 'A') {
////****************************************
   $q2 = "select * from asisdia where year='$_POST[year1]' and ss='$row[56]'";
   if ($ss!=$row[56])
      {
      $ss=$row[56];
      $result2 = mysql_query($q2);
      while ($row7 = mysql_fetch_array($result2))
            {
            for ($x = 5; $x <= 35; $x++)
                {
                if ($row7[$x]==15 or $row7[$x] > 0 and $row7[$x] < 8) {$aus=$aus+1;}
                if ($row7[$x] > 16 and $row7[$x] < 24) {$aus=$aus+0.50;}
                if ($row7[$x] == 16){$tar=$tar+1;}
                if ($row7[$x] > 7 and $row7[$x] < 15){$tar=$tar+1;}
                }
            }
      }


////****************************************
      
         $nt1=0;$nt2=0;$ntf=$row[19];
         if ($row[19] > 100){$ntf='100';}
         if ($row[11]=='E' and $row[12]=='E'){$ntf='E';}
         if ($row[11]=='S' and $row[12]=='S'){$ntf='S';}
         if ($row[11]=='E' and $row[12]=='S'){$ntf='S';}
         if ($row[11]=='S' and $row[12]=='E'){$ntf='S';}
         if ($row[11]=='E' and $row[12]=='N'){$ntf='S';}
         if ($row[11]=='N' and $row[12]=='E'){$ntf='S';}
         if ($row[11]=='N' and $row[12]=='N'){$ntf='N';}
         if ($row[11]=='S' and $row[12]=='N'){$ntf='N';}
         if ($row[11]=='N' and $row[12]=='S'){$ntf='N';}
         $sql = "UPDATE acumulativa SET desc2='$row[25]',desc1='$row[5]',credito='$row[21]',sem1='$ntf',orden='$row[136]', con1='$row[15]', con2='$row[16]', ta1='$tar', au1='$aus' WHERE ss='$row[56]' AND year='$row[94]' AND curso='$row[7]'";
      }
      if ($_POST['data1'] == 'B') {
         $nt1=0;$nt2=0;$ntf=$row[20];
         if ($row[20] > 100){$ntf='100';}
         if ($row[13]=='E' and $row[14]=='E'){$ntf='E';}
         if ($row[13]=='S' and $row[14]=='S'){$ntf='S';}
         if ($row[13]=='E' and $row[14]=='S'){$ntf='S';}
         if ($row[13]=='S' and $row[14]=='E'){$ntf='S';}
         if ($row[13]=='E' and $row[14]=='N'){$ntf='S';}
         if ($row[13]=='N' and $row[14]=='E'){$ntf='S';}
         if ($row[13]=='N' and $row[14]=='N'){$ntf='N';}
         if ($row[13]=='S' and $row[14]=='N'){$ntf='N';}
         if ($row[13]=='N' and $row[14]=='S'){$ntf='N';}
         $sql = "UPDATE acumulativa SET desc2='$row[25]',desc1='$row[5]',credito='$row[21]',sem2='$ntf',orden='$row[136]', con3='$row[17]', con4='$row[18]', ta1='$tar', au1='$aus' WHERE ss='$row[56]' AND year='$row[94]' AND curso='$row[7]'";
      }
      if ($_POST['data1'] == 'C') {
         $sql = "UPDATE acumulativa SET sem1='$row[19]',sem2='$row[20]', orden='$row[136]', con1='$row[15]', con2='$row[16]', con3='$row[17]', con4='$row[18]' WHERE ss='$row[56]' AND year='$row[94]' AND curso='$row[7]'";
      }

      if ($_POST['data1'] == 'TK1') {
         $q = "select * from tablas where notas='$row[19]'";
         $tabla = mysql_query($q, $dbh) or die("problema con query 20");
         $reg1 = mysql_fetch_row($tabla);
         $se1 = $reg1[1];
         $sql = "UPDATE acumulativa SET sem1='$se1',orden='$row[136]' WHERE ss='$row[56]' AND year='$row[94]' AND curso='$row[7]'";
      }
      if ($_POST['data1'] == 'TK2') {
         $q = "select * from tablas where notas='$row[20]'";
         $tabla = mysql_query($q, $dbh) or die("problema con query 20");
         $reg1 = mysql_fetch_row($tabla);
         $se2 = $reg1[1];
         $sql = "UPDATE acumulativa SET sem2='$se2',orden='$row[136]' WHERE ss='$row[56]' AND year='$row[94]' AND curso='$row[7]'";
      }




      $result = mysql_query($sql);
   }
}
//   <meta charset="UTF-8">

?>
<!DOCTYPE html>
<html lang="es">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <link rel="stylesheet" type="text/css" href="../../jv/botones.css" />
   <title>Acumulativa</title>
   <script language="Javascript" type="text/javascript">
      // document.oncontextmenu = function(){return false};
      function fesi() {
         var dis = document.transd.fes.value;
         if (fes.checked == 1) {
            document.transd.feg.disabled = false;
         } else {
            document.transd.feg.disabled = true;
         }

      }

      function reci11() {
         var now = new Date();
         var time = now.getTime();
         time += 1800 * 1000;
         now.setTime(time);
         var miVariablea = document.transd.nombre.value;
         document.cookie = 'variable9=' + miVariablea + '; expires=' + now.toGMTString() + '; path=/';
         var miVariableb = document.transd.tarj.value;
         document.cookie = 'variable10=' + miVariableb + '; expires=' + now.toGMTString() + '; path=/';
         var miVariablec = document.transd.grados.value;
         document.cookie = 'variable11=' + miVariablec + '; expires=' + now.toGMTString() + '; path=/';
         var miVariabled = document.transd.tnot.value;
         document.cookie = 'variable12=' + miVariabled + '; expires=' + now.toGMTString() + '; path=/';
         var miVariablee = document.transd.idioma.value;
         document.cookie = 'variable13=' + miVariablee + '; expires=' + now.toGMTString() + '; path=/';
         var miVariablef = document.transd.line.value;
         document.cookie = 'variable14=' + miVariablef + '; expires=' + now.toGMTString() + '; path=/';
         var miVariableg = document.transd.anc.value;
         document.cookie = 'variable15=' + miVariableg + '; expires=' + now.toGMTString() + '; path=/';
         var miVariableg = document.transd.prom.value;
         document.cookie = 'variable16=' + miVariableg + '; expires=' + now.toGMTString() + '; path=/';
         var miVariableg = document.transd.fes.value;
         document.cookie = 'variable17=' + miVariableg + '; expires=' + now.toGMTString() + '; path=/';
         if (cep.checked == 1) {
            document.cookie = 'variable18=' + 'Si' + '; expires=' + now.toGMTString() + '; path=/';
         } else {
            document.cookie = 'variable18=' + 'No' + '; expires=' + now.toGMTString() + '; path=/';
         }
         var miVariableg = document.transd.ano7.value;
         document.cookie = 'variable19=' + miVariableg + '; expires=' + now.toGMTString() + '; path=/';
         var miVariablegr = document.transd.grado7.value;
         document.cookie = 'variable20=' + miVariablegr + '; expires=' + now.toGMTString() + '; path=/';
         
      }
   </script>
   <style type="text/css">
      .style8 {
         text-align: center;
      }

      .style9 {
         background-color: #CCCCCC;
      }

      .style10 {
         background-color: #FFFFCC;
      }

      .style12 {
         text-align: center;
         font-size: large;
      }

      .style13 {
         background-color: #CCCCCC;
         text-align: center;
      }

      .style14 {
         background-color: #FFFFCC;
         text-align: left;
      }

      .style15 {
         background-color: #CCCCCC;
         text-align: left;
      }

      .style16 {
         text-align: center;
         background-color: #FFFFCC;
      }
   </style>
</head>

<body>

   <?
   $q = "select DISTINCT year from year ORDER BY year ASC";
   $tabla1 = mysql_query($q, $dbh) or die("problema con query 1");
   $reg = mysql_fetch_row($tabla1);
   $result3 = mysql_query($q);
   $result4 = mysql_query($q);
   $result5 = mysql_query($q);

   $q = "select DISTINCT apellidos, nombre, ss from acumulativa ORDER BY apellidos ASC";
   $tabla1 = mysql_query($q, $dbh) or die("problema con query 2");
   $reg = mysql_fetch_row($tabla1);
   $result = mysql_query($q);


   ?>


   <p class="style12"><strong>Tarjeta Acumulativa</strong></p>
   <form name="transd" action="acumula.php" method="post">
      <div class="style8">
         <br />
         <table align="center" cellspacing="0" style="width: 55%">
            <tr>
               <td class="style13"><strong>Opciones</strong></td>
               <td class="style15" style="width: 61px">&nbsp;</td>
            </tr>
            <tr>
               <td class="style14" style="width: 322px">Selecci<span lang="en-us">ó</span>n de Tarjeta</td>
               <td class="style14" style="width: 61px"><select name="tarj" style="width: 87px">
                     <option value="M" <?= ($row[113] == '13')?'selected':'' ?>>Acumulativa 1</option>
                     <option value="N" <?= ($row[113] == '14')?'selected':'' ?>>Acumulativa 2</option>
                     <option value="A" <?= ($row[113] == '1')?'selected':'' ?>>Tarjeta 1</option>
                     <option value="B" <?= ($row[113] == '2')?'selected':'' ?>>Tarjeta 2</option>
                     <option value="C" <?= ($row[113] == '3')?'selected':'' ?>>Tarjeta 3</option>
                     <option value="D" <?= ($row[113] == '4')?'selected':'' ?>>Tarjeta 4</option>
                     <option value="E" <?= ($row[113] == '5')?'selected':'' ?>>Tarjeta 5</option>
                     <option value="F" <?= ($row[113] == '6')?'selected':'' ?>>Tarjeta 6</option>
                     <option value="G" <?= ($row[113] == '7')?'selected':'' ?>>Tarjeta 7</option>
                     <option value="H" <?= ($row[113] == '8')?'selected':'' ?>>Tarjeta 8</option>
                     <option value="I" <?= ($row[113] == '9')?'selected':'' ?>>Tarjeta 9</option>
                     <option value="J" <?= ($row[113] == '10')?'selected':'' ?>>Tarjeta 10</option>
                     <option value="K" <?= ($row[113] == '11')?'selected':'' ?>>Tarjeta 11</option>
                     <option value="L" <?= ($row[113] == '12')?'selected':'' ?>>Tarjeta 12</option>
                     <option value="O" <?= ($row[113] == '13')?'selected':'' ?>>Tarjeta 13</option>
                     <option value="P" <?= ($row[113] == '14')?'selected':'' ?>>Tarjeta 14</option>
                     <option value="Q" <?= ($row[113] == '15')?'selected':'' ?>>Tarjeta 15</option>
                     <option value="R" <?= ($row[113] == '16')?'selected':'' ?>>Tarjeta 16</option>
                     <option value="S" <?= ($row[113] == '17')?'selected':'' ?>>Tarjeta 17</option>
                     <option value="T" <?= ($row[113] == '18')?'selected':'' ?>>Tarjeta 18</option>
                     <option value="U" <?= ($row[113] == '19')?'selected':'' ?>>Tarjeta 19</option>
                     <option value="V" <?= ($row[113] == '20')?'selected':'' ?>>Tarjeta 20</option>
                     <option value="W" <?= ($row[113] == '21')?'selected':'' ?>>Tarjeta 21</option>
                     <option value="X" <?= ($row[113] == '22')?'selected':'' ?>>Tarjeta 22</option>
                     <option value="Y" <?= ($row[113] == '23')?'selected':'' ?>>Tarjeta 23</option>
                     <option value="Z" <?= ($row[113] == '24')?'selected':'' ?>>Tarjeta 24</option>
                     <option value="AA" <?= ($row[113] == '25')?'selected':'' ?>>Tarjeta 25</option>
                     <option value="AB" <?= ($row[113] == '26')?'selected':'' ?>>Tarjeta 26</option>
                     <option value="AC" <?= ($row[113] == '27')?'selected':'' ?>>Tarjeta 27</option>
                     <option value="AD" <?= ($row[113] == '28')?'selected':'' ?>>Tarjeta 28</option>
                     <option value="AE" <?= ($row[113] == '29')?'selected':'' ?> selected="">Tarjeta 29</option>
                     <!-- next: AF -->
                  </select></td>
            </tr>
            <tr>
               <td class="style14" style="width: 322px">&nbsp;</td>
               <td class="style14" style="width: 61px">
                  <input name="cep" id="cep" type="checkbox" value="Si" /> Cr<span lang="en-us">é</span>ditos. en Progreso</td>
            </tr>
            <tr>
               <td class="style14" style="width: 322px">Grados</td>
               <td class="style14" style="width: 61px"><select name="grados" style="width: 68px">
                     <option value="A">01-04</option>
                     <option value="B">05-08</option>
                     <option value="C" selected="">09-12</option>
                  </select></td>
            </tr>
            <tr>
               <td class="style14" style="width: 322px">Notas</td>
               <td class="style14" style="width: 61px"><select name="tnot" style="width: 66px">
                     <option value="A">Num</option>
                     <option value="B" selected="">Let</option>
                     <option value="C">N-L</option>
                  </select></td>
            </tr>
            <tr>
               <td class="style14" style="width: 322px">Idioma</td>
               <td class="style14" style="width: 61px"><select name="idioma" style="width: 81px">
                     <option value="A" selected="">Español</option>
                     <option value="B">Ingles</option>
                  </select></td>
            </tr>
            <tr>
               <td class="style14" style="width: 322px">Promedios</td>
               <td class="style14" style="width: 61px"><select name="prom" style="width: 81px">
                     <option value="A">Credito</option>
                     <option value="B" selected="">Notas</option>
                     <option value="C">Peso</option>
                  </select></td>
            </tr>
            <tr>
               <td class="style14" style="width: 322px">Creditos</td>
               <td class="style14" style="width: 61px"><select name="cred" style="width: 45px">
                     <option>Si</option>
                     <option>No</option>
                  </select></td>
            </tr>
            <tr>
               <td class="style14" style="width: 322px">Con Logos</td>
               <td class="style14" style="width: 61px"><select name="lo" style="width: 45px">
                     <option>Si</option>
                     <option>No</option>
                  </select></td>
            </tr>
            <tr>
               <td class="style14" style="width: 322px; height: 24px;">Con Lineas</td>
               <td class="style14" style="width: 61px; height: 24px;"><select name="line" style="width: 45px">
                     <option value="1">Si</option>
                     <option value="0">No</option>
                  </select></td>
            </tr>
            <tr>
               <td class="style14" style="width: 322px">Espacio entre Lineas</td>
               <td class="style14" style="width: 61px">
               <select name="anc" style="width: 68px">
                     <option value="4">4</option>
                     <option value="5" selected="">5</option>
                     <option value="6">6</option>
                  </select></td>
            </tr>
            <tr>
               <td class="style14" style="width: 322px">Grado, secci<span lang="en-us">ó</span>n y a<span lang="en-us">ñ</span>o escolar</td>
               <td class="style14" style="width: 61px">
                  <input maxlength="5" name="grado7" size="5" style="width: 45px; height: 20px" type="text" />
                  y&nbsp;
                  <strong>
                     <select name="ano7" style="width: 62px">
                        <?
                        while ($row3 = mysql_fetch_array($result3)) {
                           echo '<option>' . $row3[0] . '</option>';
                        }
                        ?>
                     </select></strong></td>
            </tr>
            <tr>
               <td class="style14" style="width: 322px">
                  <select name="nombre" style="width: 357px">
                     <option selected="" value="Selección">Selección Para grado completos</option>
                     <?
                     while ($row = mysql_fetch_array($result)) {
                        //				echo '<option>'.$row[0].', '.$row[1].'</option>';}
                        echo '<option value="' . $row[2] . '">' . $row[0] . ', ' . $row[1] . '</option>';
                     }

                     ?>
                  </select></td>
               <td class="style14" style="width: 61px">&nbsp;</td>
            </tr>
            <tr>
               <td class="style14" style="width: 322px"><strong>Fecha de
                     Graduaci<span lang="en-us">ó</span>n</strong></td>
               <td class="style14" style="width: 61px">
                  <input name="feg" type="text" style="width: 140px" disabled="disabled" />
                  <input id="fes" name="fes" type="checkbox" value="1" onclick="return fesi(); return true" /></td>
            </tr>
            <tr>
               <td class="style13" colspan="2"><strong>
                     OBSERVACIONES</strong></td>
            </tr>
            <tr>
               <td class="style10" colspan="2">
                  <input name="Text2" type="text" style="width: 454px" /></td>
            </tr>
            <tr>
               <td class="style10" colspan="2">
                  <input name="Text3" style="width: 457px" type="text" /></td>
            </tr>
            <tr>
               <td class="style9" colspan="2">&nbsp;</td>
            </tr>
         </table>
         <br />
         <table align="center" cellpadding="2" cellspacing="0" style="width: 58%">
            <tr>
               <td class="style13">Para Transferir las Notas a la Acumulativa.</td>
               <td>&nbsp;</td>
            </tr>
            <tr>
               <td class="style16">
                  <strong>
                     <select name="year1" style="width: 62px">
                        <?
                        while ($row3 = mysql_fetch_array($result4)) {
                           echo '<option>' . $row3[0] . '</option>';
                        }
                        ?>
                     </select>&nbsp;&nbsp; <select name="data1" style="width: 72px">
                        <option selected="" value="A">Sem-1</option>
                        <option value="B">Sem-2</option>
                        <option value="C">Todo</option>
                        <option value="D1">SSCC-1</option>
                        <option value="D2">SSCC-2</option>
                        <option value="D3">SSCC-T</option>
                        <option value="TK1">TKCA-1</option>
                        <option value="TK2">TKCA-2</option>
                     </select>&nbsp;&nbsp;
                     <input class="myButton" name="pasar" type="submit" value="Transferir" style="width: 128px; height: 27px;" /></strong></td>
               <td class="style8">
                  <strong>
                     <input class="myButton" name="curs" type="submit" value="Cursos" style="width: 155px; height: 27px;" />
                     <input class="myButton" name="trans" type="button" value="Transcripción" style="width: 155px; height: 27px;" onclick="window.open('acum_tarjeta.php','_new')" onmouseover="reci11()" /></strong>
                     <input class="myButton" name="trans" type="button" value="Gpa y Rank" style="width: 155px; height: 27px;" onclick="window.open('gpa_rank.php','_new')" onmouseover="reci11()" /></strong></td>
            </tr>
            <tr>
               <td class="style16">
                  <strong><select name="year2" style="width: 62px">
                        <?
                        while ($row3 = mysql_fetch_array($result5)) {
                           echo '<option>' . $row3[0] . '</option>';
                        }
                        ?>
                     </select>&nbsp;&nbsp; <select name="grado2" style="width: 72px">
                        <option value="Grado">Grado</option>
                        <option value="09">09</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                     </select>&nbsp;&nbsp;
                     <input class="myButton" name="rank" style="width: 128px; height: 27px;" type="submit" value="Ranking" /></strong></td>
               <td class="style8">
                  <strong>
                     <input class="myButton" name="acti" style="width: 155px; height: 27px;" type="submit" value="Actividades" />
                     <input class="myButton" name="add_edit" style="width: 155px; height: 27px;" onclick="window.open('add_edit.php','_self')" type="submit" value="Añadir/Editar" /></strong></td>
            </tr>
         </table>
         <br />
         <strong>
            &nbsp; &nbsp;
            &nbsp;&nbsp;
            &nbsp;&nbsp;
            <br />
         </strong>
      </div>
   </form>


   <p>&nbsp;</p>


</body>

</html>