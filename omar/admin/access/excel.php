<?php 
require_once '../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase0\DB;
use Classes\Controllers\School;

Session::is_logged();
$lang = new Lang([
    ['Exportación de data a Excel', 'Data export to Excel'],
    ['Selección de Base de datos', 'Database Selection'],
    ['Seleccióna el año', 'Select the year'],
    ['Selección', 'Selection'],
    ['Padres', 'Parents'],
    ['Estudiantes', 'Students'],
    ['Año para transferir datos', 'Year to transfer data'],
    ['Notas', 'Grades'],
    ['Pagos', 'Payments'],
    ['Transferir', 'Transfer'],
    ['Documentos de estudiantes', 'Student documents'],
    ['Padres/Estudiantes', 'Parents/Students'],
    ['Comedor escolar', 'School cafeteria'],
    ['Verano', 'Summer'],
    ['Si', 'Yes'],
    ['Lista', 'List'],
    ['Guardar', 'Save'],
    ['Crear', 'Create'],
    ['Buscar', 'Search'],
    ['Limpiar', 'Clear'],
    ['Eliminar', 'Delete'],
    ['Estás seguro que quieres borrar el curso?', 'Are you sure you want to delete the course?'],
]);
$school = new School(Session::id());



//echo $tabla =  $_COOKIE["variable9"];
//echo $year  =  $_COOKIE["variable10"];
if ($tabla=='Seleccion')
   {
   ?> <script language="javascript">setTimeout("self.close();",00070)</script>
   <?
   exit;
   }
//include('../control.php');


header("Content-type: application/vnd.ms-excel" ) ; 
header("Content-Disposition: attachment; filename=archivo.xls" ) ; 
?>
<head>
<style type="text/css">
.num {
  mso-number-format:General;
}
.text{
  mso-number-format:"\@";
}</style>
</head>

<?

if ($tabla=='madre' || $tabla == 'docu_estudiantes')
   {
//   $qry=mysql_query("select * from $tabla") ;

   $qry = DB::table($tabla)->get();

   }
else
   {
//   $qry=mysql_query("select * from $tabla WHERE year='$year'") ;

      $qry = DB::table($tabla)->where([
        ['year', $year]
      ])->get();

   }
//***************************************
$campos = mysql_num_fields($qry) ; 
//***************************************

$i=0; 
echo "<table><tr>"; 
while($i<$campos)
     { 
//***************************************
     echo "<td>". mysql_field_name($qry, $i) ; 
//***************************************
     echo "</td>"; 
     $i++; 
     } 
echo "</tr>"; 

if ($tabla=='madre')
   {
//   $qry2=mysql_query("select * from year WHERE codigobaja=0 and year='$year' Order By id") ;
//   $qry2=mysql_query("select * from year WHERE codigobaja=0 and year='$year'") ;
   $students = DB::table('year')->where([
        ['year', $year],
        ['codigobaja', 0]
      ])->get();


      foreach ($students as $row1)
              {

//   while($row1=mysql_fetch_array($qry2))
//        {
//        $qry=mysql_query("select * from madre WHERE id='$row1[5]'") ;

            $madre = DB::table('madre')->where([
              ['id', $$row1->id]
            ])->first();

      foreach ($madre as $row)
              {

//        while($row=mysql_fetch_array($qry))
//             { 
             echo "<tr>"; 
             for($j=0; $j<$campos; $j++) 
                { 
                echo "<td class='text'>".$row[$j]."</td>"; 
                }
             echo "</tr>"; 
             } 
        }
   }
else
   {

   foreach ($qry as $row)
           {

//   while($row=mysql_fetch_array($qry))
//        { 
        echo "<tr>"; 
        for($j=0; $j<$campos; $j++) 
           { 
           echo "<td class='text'>".$row[$j]."</td>"; 
           }
        echo "</tr>"; 
        } 
   }
echo "</table>"; 
?> 