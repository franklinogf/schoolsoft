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
   ["Crear cargos", "Create costs"],
   ['Grabar', 'Save'],
   ['Código', 'Code'],
   ['Todos', 'All'],
   ['Borrar', 'Delete'],
   ['Debe de llenar todos los campos', 'You must fill all fields'],
   ['Lista de codigos', 'Codes list'],
   ['Descripción', 'Description'],
   ['Activo', 'Active'],
   ['Costos', 'Costs'],
   ['Opciones', 'Options'],
   ['Agosto', 'August'],
   ['Septiembre', 'September'],
   ['Octubre', 'October'],
   ['Noviembre', 'November'],
   ['Diciembre', 'December'],
   ['Enero', 'January'],
   ['Febrero', 'February'],
   ['Marzo', 'March'],
   ['Abril', 'Abril'],
   ['Mayo', 'May'],
   ['Junio', 'June'],
   ['Julio', 'July'],
   ['Grados', 'Grades'],
   ['Matri/Junio', 'Regis/June'],
   ['Por Familia', 'Per Family'],
   ['Procesar', 'Process'],
   ['Grado', 'Grade'],
   ['Selección', 'Selection'],
   ['Si', 'Yes'],
   ['No', 'No'],
   ['Cambiar estado', 'Change Status'],
   ['Selección de Meses', 'Month Selection'],
   ['Borrar todos los Cargos', 'Eliminate all costs'],
   ['Estás seguro que desea eliminar el costo?', 'Are you sure you want to eliminate the cost?'],



]);

$school = new School(Session::id());
$year = $school->info('year2');
$grades = $school->allGrades();

$resultado3 = DB::table('presupuesto')->where('year', $year)->orderBy('codigo')->get();
$resultado2 = DB::table('costos')->where('year', $year)->orderBy('grado, codigo')->get();
$row3 = DB::table('colegio')->where('usuario', 'administrador')->orderBy('usuario')->first();


if (isset($_POST['pro'])) {
   if ($_POST['grado'] == 'Todos') {
      $resultado1 = DB::table('year')->whereRaw("year='$year' and activo = ''")->orderBy('id')->get();
   } else {
      if ($_POST['grado'] == 'Selección') {
         $resultado1 = DB::table('year')->whereRaw("id='" . $_POST['cta'] . "' and year='$year' and activo = ''")->orderBy('grado')->get();
      } else {
         $resultado1 = DB::table('year')->whereRaw("grado='" . $_POST['grado'] . "' and year='$year' and activo = ''")->orderBy('grado')->get();
      }
   }
   list($y3, $y4) = explode("-", $year);
   $y1 = '20' . $y3;
   $y2 = '20' . $y4;
   foreach ($resultado1 as $row1) {
      if ($_POST['borrar'] == 'Si') {
         if ($_POST['m1'] ?? '' == 1) {
            DB::table("pagos")->where([
               ['fecha_d', $y2 . '-01-01'],
               ['id', $row1->id],
               ['ss', $row1->ss],
               ['year', $year],
               ['deuda', '!=', 0]
            ])->delete();
         }
         if ($_POST['m2'] ?? '' == 1) {
            DB::table("pagos")->where([
               ['fecha_d', $y2 . '-02-01'],
               ['id', $row1->id],
               ['ss', $row1->ss],
               ['year', $year],
               ['deuda', '!=', 0]
            ])->delete();
         }
         if ($_POST['m3'] ?? '' == 1) {
            DB::table("pagos")->where([
               ['fecha_d', $y2 . '-03-01'],
               ['id', $row1->id],
               ['ss', $row1->ss],
               ['year', $year],
               ['deuda', '!=', 0]
            ])->delete();
         }
         if ($_POST['m4'] ?? '' == 1) {
            DB::table("pagos")->where([
               ['fecha_d', $y2 . '-04-01'],
               ['id', $row1->id],
               ['ss', $row1->ss],
               ['year', $year],
               ['deuda', '!=', 0]
            ])->delete();
         }
         if ($_POST['m5'] ?? '' == 1) {
            DB::table("pagos")->where([
               ['fecha_d', $y2 . '-05-01'],
               ['id', $row1->id],
               ['ss', $row1->ss],
               ['year', $year],
               ['deuda', '!=', 0]
            ])->delete();
         }
         if ($_POST['m6'] ?? '' == 1) {
            DB::table("pagos")->where([
               ['fecha_d', $y2 . '-06-01'],
               ['id', $row1->id],
               ['ss', $row1->ss],
               ['year', $year],
               ['deuda', '!=', 0]
            ])->delete();
         }
         if ($_POST['m7'] ?? '' == 1) {
            DB::table("pagos")->where([
               ['fecha_d', $y2 . '-07-01'],
               ['id', $row1->id],
               ['ss', $row1->ss],
               ['year', $year],
               ['deuda', '!=', 0]
            ])->delete();
         }
         if ($_POST['m8'] ?? '' == 1) {
            DB::table("pagos")->where([
               ['fecha_d', $y2 . '-08-01'],
               ['id', $row1->id],
               ['ss', $row1->ss],
               ['year', $year],
               ['deuda', '!=', 0]
            ])->delete();
         }
         if ($_POST['m9'] ?? '' == 1) {
            DB::table("pagos")->where([
               ['fecha_d', $y2 . '-09-01'],
               ['id', $row1->id],
               ['ss', $row1->ss],
               ['year', $year],
               ['deuda', '!=', 0]
            ])->delete();
         }
         if ($_POST['m10'] ?? '' == 1) {
            DB::table("pagos")->where([
               ['fecha_d', $y2 . '-10-01'],
               ['id', $row1->id],
               ['ss', $row1->ss],
               ['year', $year],
               ['deuda', '!=', 0]
            ])->delete();
         }
         if ($_POST['m11'] ?? '' == 1) {
            DB::table("pagos")->where([
               ['fecha_d', $y2 . '-11-01'],
               ['id', $row1->id],
               ['ss', $row1->ss],
               ['year', $year],
               ['deuda', '!=', 0]
            ])->delete();
         }
         if ($_POST['m12'] ?? '' == 1) {
            DB::table("pagos")->where([
               ['fecha_d', $y2 . '-12-01'],
               ['id', $row1->id],
               ['ss', $row1->ss],
               ['year', $year],
               ['deuda', '!=', 0]
            ])->delete();
         }
      }
      if ($_POST['cargos'] == 'Todos') {
         $resultado2 = DB::table('costos')->whereRaw("year='$year' and activo='Si' and grado='$row1->grado'")->orderBy('grado, codigo')->get();
      } else {
         $resultado2 = DB::table('costos')->whereRaw("codigo='" . $_POST['cargos'] . "' and year='$year' and activo='Si' and grado='$row1->grado'")->orderBy('grado, codigo')->get();
      }
      $ca1 = '';
      $ca2 = '';
      $cb1 = '';
      $cb2 = '';
      $cc1 = '';
      $cc2 = '';
      $desc1 = $row1->desc1;
      $desc2 = $row1->desc2;
      $desc3 = $row1->desc3;
      if ($desc1 != 'Selección' and $desc1 != '') {
         list($ca1, $ca2) = explode(", ", $desc1);
      }
      if ($desc2 != 'Selección' and $desc2 != '') {
         list($cb1, $cb2) = explode(", ", $desc2);
      }
      if ($desc3 != 'Selección' and $desc3 != '') {
         list($cc1, $cc2) = explode(", ", $desc3);
      }

      if ($row1->nuevo == 'Si' and !empty($row3->esncodigo)) {
         $row5 = DB::table('presupuesto')->whereRaw("codigo='$row3->esncodigo' and year='$year'")->orderBy('codigo')->first();
         $fe = $y2 . '-' . $row3->esnmes . '-01';

         $buscar = DB::table('pagos')->whereRaw("id='$row1->id' and fecha_d='$fe' and year='$year' and codigo=$row3->esncodigo and ss='$row1->ss' and fecha_p='0000-00-00' and grado='$row1->grado' and code1=7 and add1=0 and rec=0 and tdp=''")->orderBy('codigo')->first();

         $bu = 0;
         $bu = $buscar->ss ?? '0';
         if ($bu == 0) {
            DB::table('pagos')->insert([
               'id' => $row1->id,
               'nombre' => $row1->apellidos . ' ' . $row1->nombre,
               'desc1' => $row5->descripcion,
               'fecha_d' => $fe,
               'year' => $year,
               'deuda' => $row3->esn,
               'codigo' => $row3->esncodigo,
               'ss' => $row1->ss,
               'grado' => $row1->grado,
               'code1' => 7,
            ]);
         }
      }

      foreach ($resultado2 as $row2) {
         for ($i = 1; $i <= 12; $i++) {
            if ($i < 8) {
               $i2 = $i + 6;
               $fe = $y2 . '-' . $i . '-01';
            }
            if ($i > 7) {
               $i2 = $i - 6;
               $fe = $y1 . '-' . $i . '-01';
            }
            //                IF ($i==12){$i2=6; $fe=$y1.'-'.$i.'-01';}
            $mes = array($row2->grado, $row2->m1, $row2->m2, $row2->m3, $row2->m4, $row2->m5, $row2->m6, $row2->m7, $row2->m8, $row2->m9, $row2->m10, $row2->m11, $row2->m12);
            if ($row2->esn == 'Si' and $row2->pf == '' and $mes[$i] == 'Si') {
               if ($row1->nuevo == 'Si') {
                  DB::table('pagos')->insert([
                     'id' => $row1->id,
                     'nombre' => $row1->apellidos . ' ' . $row1->nombre,
                     'desc1' => $row2->descripcion,
                     'fecha_d' => $fe,
                     'year' => $year,
                     'deuda' => $row2->costo,
                     'codigo' => $row2->codigo,
                     'ss' => $row1->ss,
                     'grado' => $row1->grado,
                  ]);
               }
            }
            if ($row2->esn == '' and $row2->pf == '' and $mes[$i] == 'Si') {
               $buscar = DB::table('pagos')->whereRaw("id='$row1->id' and fecha_d='$fe' and year='$year' and codigo=$row2->codigo and ss='$row1->ss' and fecha_p='0000-00-00' and grado='$row1->grado' and code1=0 and add1=0 and rec=0 and tdp=''")->orderBy('codigo')->first();
               $bu = $buscar->nombre ?? '0';
               if ($bu == 0) {
                  DB::table('pagos')->insert([
                     'id' => $row1->id,
                     'nombre' => $row1->apellidos . ' ' . $row1->nombre,
                     'desc1' => $row2->descripcion,
                     'fecha_d' => $fe,
                     'year' => $year,
                     'deuda' => $row2->costo,
                     'codigo' => $row2->codigo,
                     'ss' => $row1->ss,
                     'grado' => $row1->grado,
                  ]);
               }
            }
            if ($row2->esn == '' and $row2->pf == '' and $mes[$i] == 'Si' and $row1->desc_men > 0 and $row2->codigo == $ca1) {
               $buscar = DB::table('pagos')->whereRaw("id='$row1->id' and fecha_d='$fe' and year='$year' and codigo=$row2->codigo and ss='$row1->ss' and fecha_p='0000-00-00' and grado='$row1->grado' and code1=$row2->codigo and add1=0 and rec=0 and tdp=''")->orderBy('codigo')->first();
               $bu = $buscar->nombre ?? '0';
               if ($bu == 0) {
                  DB::table('pagos')->insert([
                     'id' => $row1->id,
                     'nombre' => $row1->apellidos . ' ' . $row1->nombre,
                     'desc1' => 'Descuento en ' . $row2->descripcion,
                     'fecha_d' => $fe,
                     'year' => $year,
                     'deuda' => -$row1->desc_men,
                     'codigo' => $row2->codigo,
                     'ss' => $row1->ss,
                     'grado' => $row1->grado,
                     'code1' => $row2->codigo,
                  ]);
               }
            }
            if ($row2->esn == '' and $row2->pf == '' and $mes[$i] == 'Si' and $row1->desc_mat > 0 and $row2->codigo == $cb1) {
               $buscar = DB::table('pagos')->whereRaw("id='$row1->id' and fecha_d='$fe' and year='$year' and codigo=$row2->codigo and ss='$row1->ss' and fecha_p='0000-00-00' and grado='$row1->grado' and code1=$row2->codigo and add1=0 and rec=0 and tdp=''")->orderBy('codigo')->first();
               $bu = $buscar->nombre ?? '0';
               if ($bu == 0) {
                  DB::table('pagos')->insert([
                     'id' => $row1->id,
                     'nombre' => $row1->apellidos . ' ' . $row1->nombre,
                     'desc1' => 'Descuento en ' . $row2->descripcion,
                     'fecha_d' => $fe,
                     'year' => $year,
                     'deuda' => -$row1->desc_mat,
                     'codigo' => $row2->codigo,
                     'ss' => $row1->ss,
                     'grado' => $row1->grado,
                     'code1' => $row2->codigo,
                  ]);
               }
            }
            if ($row2->esn == '' and $row2->pf == '' and $mes[$i] == 'Si' and $row1->desc_otro1 > 0 and $row2->codigo == $cc1) {
               $buscar = DB::table('pagos')->whereRaw("id='$row1->id' and fecha_d='$fe' and year='$year' and codigo=$row2->codigo and ss='$row1->ss' and fecha_p='0000-00-00' and grado='$row1->grado' and code1=$row2->codigo and add1=0 and rec=0 and tdp=''")->orderBy('codigo')->first();
               $bu = $buscar->nombre ?? '0';
               if ($bu == 0) {
                  DB::table('pagos')->insert([
                     'id' => $row1->id,
                     'nombre' => $row1->apellidos . ' ' . $row1->nombre,
                     'desc1' => 'Descuento en ' . $row2->descripcion,
                     'fecha_d' => $fe,
                     'year' => $year,
                     'deuda' => -$row1->desc_otro1,
                     'codigo' => $row2->codigo,
                     'ss' => $row1->ss,
                     'grado' => $row1->grado,
                     'code1' => $row2->codigo,
                  ]);
               }
            }
            if ($row2->esn == '777' and $row2->pf == 'Si' and $mes[$i] == 'Si') {
               $tabla1 = DB::table('pagos')->whereRaw("id='$row1->id' and year='$year' and fecha_d='$fe' and codigo='$row2->codigo' and code1='$row2->codigo'")->orderBy('id')->get();
               $num_resultados = count($tabla1);
               if ($num_resultados == 0) {
                  DB::table('pagos')->insert([
                     'id' => $row1->id,
                     'nombre' => $row1->apellidos . ' ' . $row1->nombre,
                     'desc1' => $row2->descripcion,
                     'fecha_d' => $fe,
                     'year' => $year,
                     'deuda' => $row2->costo,
                     'codigo' => $row2->codigo,
                     'ss' => $row1->ss,
                     'grado' => $row1->grado,
                     'code1' => $row2->codigo,
                  ]);
               }
            }
         }
      }
   }
}

$tabla12 = DB::table('presupuesto')->whereRaw("year='$year'")->orderBy('codigo')->get();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<script language="JavaScript">
   document.oncontextmenu = function() {
      return false
   }

   function confirmar(mensaje) {
      return confirm(mensaje);
   }

   function cambiaPalabra() {
      var dis = document.algunNombre.grado.value;
      if (dis == 'Selección') {
         document.algunNombre.cta.disabled = false;
      } else {
         document.algunNombre.cta.disabled = true;
      }
   }

   function cambiaPalabra2() {
      var dis = document.algunNombre.borrar.value;
      if (dis == 'Si') {
         document.algunNombre.m1.disabled = false;
         document.algunNombre.m2.disabled = false;
         document.algunNombre.m3.disabled = false;
         document.algunNombre.m4.disabled = false;
         document.algunNombre.m5.disabled = false;
         document.algunNombre.m6.disabled = false;
         document.algunNombre.m7.disabled = false;
         document.algunNombre.m8.disabled = false;
         document.algunNombre.m9.disabled = false;
         document.algunNombre.m10.disabled = false;
         document.algunNombre.m11.disabled = false;
         document.algunNombre.m12.disabled = false;
      } else {
         document.algunNombre.m1.disabled = true;
         document.algunNombre.m2.disabled = true;
         document.algunNombre.m3.disabled = true;
         document.algunNombre.m4.disabled = true;
         document.algunNombre.m5.disabled = true;
         document.algunNombre.m6.disabled = true;
         document.algunNombre.m7.disabled = true;
         document.algunNombre.m8.disabled = true;
         document.algunNombre.m9.disabled = true;
         document.algunNombre.m10.disabled = true;
         document.algunNombre.m11.disabled = true;
         document.algunNombre.m12.disabled = true;
      }
   }
</script>

<head>
   <?php
   $title = $lang->translation('Crear cargos');
   Route::includeFile('/admin/includes/layouts/header.php');
   ?>
</head>

<body>
   <?php
   Route::includeFile('/admin/includes/layouts/menu.php');
   ?>
   <div class="container-lg mt-lg-3 mb-5 px-0">
      <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Crear cargos') ?></h1>
      <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">
         <div class="div">

         </div>
         <div class="div">
            <form name="algunNombre" action="" method="post">
               <table align="center" cellpadding="2" cellspacing="0" style="width: 29%">
                  <tr>
                     <td class="style3"><strong><?= $lang->translation('Opciones') ?></strong></td>
                  </tr>
                  <tr>
                     <td class="style5">
                        <select name="grado" style="width: 92px" onclick="return cambiaPalabra(); return true">
                           <option value="Selección"><?= $lang->translation('Selección') ?></option>
                           <option value="Todos">Todos</option>
                           <?php foreach ($grades as $grade): ?>
                              <option value='<?= $grade ?>'>
                                 <?= $grade ?>
                              </option>
                           <?php endforeach ?>
                        </select>
                     </td>
                  </tr>
                  <tr>
                     <td class="style5">
                        <select name="cargos" style="width: 215px" onclick="return cambiaPalabra(); return true">
                           <option value="Selección"><?= $lang->translation('Selección') ?></option>
                           <option value="Todos"><?= $lang->translation('Todos') ?></option>
                           <?php foreach ($tabla12 as $row2): ?>
                              <option value="<?= $row2->codigo ?>"><?= $row2->codigo . ', ' . $row2->descripcion ?></option>
                           <?php endforeach ?>

                        </select>
                     </td>
                  </tr>
                  <tr>
                     <td class="style5">
                        <input maxlength="10" name="cta" size="10" type="text">
                     </td>
                  </tr>
                  <tr>
                     <td class="style3">
                        <strong><?= $lang->translation('Borrar todos los Cargos') ?></strong>
                     </td>
                  </tr>
                  <tr>
                     <td class="style5">
                        <select name="borrar" style="width: 47px" onclick="return cambiaPalabra2(); return true">
                           <option value="No">No</option>
                           <option value="Si"><?= $lang->translation('Si') ?></option>
                        </select>
                     </td>
                  </tr>
                  <tr>
                     <td class="style6">
                        <strong><?= $lang->translation('Selección de Meses') ?></strong>
                     </td>
                  </tr>
               </table>
               <table align="center" cellpadding="2" cellspacing="0" style="width: 550px">
                  <tr>
                     <td class="style1">
                        <center><strong><?= $lang->translation('Agosto') ?></strong></center>
                     </td>
                     <td class="style1">
                        <center><strong><?= $lang->translation('Septiembre') ?></strong></center>
                     </td>
                     <td class="style1">
                        <center><strong><?= $lang->translation('Octubre') ?></strong></center>
                     </td>
                     <td class="style1">
                        <center><strong><?= $lang->translation('Noviembre') ?></strong></center>
                     </td>
                     <td class="style1">
                        <center><strong><?= $lang->translation('Diciembre') ?></strong></center>
                     </td>
                  </tr>
                  <tr>
                     <td class="style5">
                        <center>
                           <input checked="checked" disabled="disabled" name="m8" type="checkbox" value="1" style="width: 20px; height: 20px">
                        </center>
                     </td>
                     <td class="style5">
                        <center>
                           <input checked="checked" disabled="disabled" name="m9" type="checkbox" value="1" style="width: 20px; height: 20px">
                        </center>
                     </td>
                     <td class="style5">
                        <center>
                           <input checked="checked" disabled="disabled" name="m10" type="checkbox" value="1" style="width: 20px; height: 20px">
                        </center>
                     </td>
                     <td class="style5">
                        <center>
                           <input checked="checked" disabled="disabled" name="m11" type="checkbox" value="1" style="width: 20px; height: 20px">
                        </center>
                     </td>
                     <td class="style5">
                        <center>
                           <input checked="checked" disabled="disabled" name="m12" type="checkbox" value="1" style="width: 20px; height: 20px">
                        </center>
                     </td>
                  </tr>

                  <tr>
                     <td class="style1">
                        <center><strong><?= $lang->translation('Enero') ?></strong></center>
                     </td>
                     <td class="style1">
                        <center><strong><?= $lang->translation('Febrero') ?></strong></center>
                     </td>
                     <td class="style1">
                        <center><strong><?= $lang->translation('Marzo') ?></strong></center>
                     </td>
                     <td class="style1">
                        <center><strong><?= $lang->translation('Abril') ?></strong></center>
                     </td>
                     <td class="style1">
                        <center><strong><?= $lang->translation('Mayo') ?></strong></center>
                     </td>
                  </tr>
                  <tr>
                     <td class="style5">
                        <center>
                           <input checked="checked" disabled="disabled" name="m1" type="checkbox" value="1" style="width: 20px; height: 20px">
                        </center>
                     </td>
                     <td class="style5">
                        <center>
                           <input checked="checked" disabled="disabled" name="m2" type="checkbox" value="1" style="width: 20px; height: 20px">
                        </center>
                     </td>
                     <td class="style5">
                        <center>
                           <input checked="checked" disabled="disabled" name="m3" type="checkbox" value="1" style="width: 20px; height: 20px">
                        </center>
                     </td>
                     <td class="style5">
                        <center>
                           <input checked="checked" disabled="disabled" name="m4" type="checkbox" value="1" style="width: 20px; height: 20px">
                        </center>
                     </td>
                     <td class="style5">
                        <center>
                           <input checked="checked" disabled="disabled" name="m5" type="checkbox" value="1" style="width: 20px; height: 20px">
                        </center>
                     </td>
                  </tr>
                  <tr>
                     <td class="style1">
                        <center><strong><?= $lang->translation('Matri/Junio') ?></strong></center>
                     </td>
                     <td class="style1">
                        <center><strong><?= $lang->translation('Julio') ?></strong></center>
                     </td>
                     <td class="style1">
                        <center><strong></strong></center>
                     </td>
                     <td class="style1">
                        <center><strong></strong></center>
                     </td>
                     <td class="style1">
                        <center><strong></strong></center>
                     </td>
                  </tr>
                  <tr>
                     <td class="style5">
                        <center>
                           <input disabled="disabled" name="m6" type="checkbox" value="1" style="width: 20px; height: 20px">
                        </center>
                     </td>
                     <td class="style5">
                        <center>
                           <input disabled="disabled" name="m7" type="checkbox" value="1" style="width: 20px; height: 20px">
                        </center>
                     </td>
                     <td class="style5">
                     </td>
                     <td class="style5">
                     </td>
                     <td class="style5">
                     </td>
                  </tr>
                  <tr>
                     <td class="style3" colspan="5">
                        <center><strong>
                              <input class="btn btn-primary" name="pro" style="width: 140px;" type="submit" value="<?= $lang->translation('Procesar') ?>" /></strong></center>
                     </td>
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