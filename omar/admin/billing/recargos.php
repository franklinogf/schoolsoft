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
    ['Pantalla para aplicar recargos', 'Screen to apply surcharges'],
    ['Fecha desde:', 'Date from:'],
    ['Fecha hasta:', 'Date until:'],
    ['Atrás', 'Go back'],
    ['Todos', 'All'],
    ['Seleccionar', 'Selec'],
    ['El código del recargo.', 'The surcharge code.'],
    ['Seleccione el mes para aplicar el recargo.', 'Select the month to apply the surcharge.'],
    ['A quién le aplicará el recargo?', 'Who will the surcharge apply to?'],
    ['Por familia', 'By family'],
    ['Por estudiante', 'Per student'],
    ['Cantidad de recargo.', 'Additional Fee.'],
    ['Recargo 2do. hermano.', 'Sibling 2nd. fee'],
    ['El código para aplicar el recargo.', 'The code to apply the Fee.'],
    ['Tele. Pago', 'Phone. Pay'],
    ['Beca', 'Scholarship'],
    ['ATH Movil', 'ATH Movil'],
    ['Detallado', 'Detailed'],
    ['Crear', 'Create'],
    ['Buscar', 'Search'],
    ['Selección', 'Selection'],
    ['Bash', 'Bash'],
    ['Estás seguro que quieres borrar el curso?', 'Are you sure you want to delete the course?'],
    ['Resumen fecha', 'Summary date'],
    ['Resumen código', 'Code summary'],
    ['Caja', 'Cash register'],
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
    ['', ''],
    ['', ''],
    ['', ''],
]);
$school = new School(Session::id());
$year = $school->info('year2');

$presupuesto = DB::table('presupuesto')->where([
            ['year', $year]])->orderBy('codigo')->get();

IF(isset($_POST['proce']))
  {
  list($n1,$n3) = explode(", ",$_POST['desc']);
  list($n2,$n4) = explode(", ",$_POST['desc2']);
  list($y1,$y2) = explode("-",$year);

  if ($_POST['recargo']=='1')
     {
     $resultado5 = DB::table('year')->select("DISTINCT id")
        ->whereRaw("year='$year' and activo=''")->get();
     }
  if ($_POST['recargo']=='2')
     {
     $resultado5 = DB::table('year')->select("DISTINCT id, ss")
        ->whereRaw("year='$year' and activo=''")->get();
     }
  $y3=$y1;
  if ($_POST['mes'] < 6){$y3=$y2;}
  $fec1='20'.$y3.'-'.$_POST['mes'].'-01';
  $a=0;
  $idc='';
  $cc=0;
 foreach ($resultado5 as $row5) 
         {
        if ($_POST['recargo']=='1')
           {
           $resultado4 = DB::table('pagos')->where([
           ['codigo', $n2],['fecha_d', $fec1],['id', $row5->id],
           ['baja', '']])->get();
           }
        if ($_POST['recargo']=='2')
           {
           $resultado4 = DB::table('pagos')->where([
           ['ss', $row5->ss],['codigo', $n2],['fecha_d', $fec1],['id', $row5->id],
           ['baja', '']])->get();
           }
        $deu=0;
        $pag=0;
        $nom='';
        $ss='';
        $grado='';
      foreach ($resultado4 as $row4) 
              {
              $deu=$deu+$row4->deuda;
              $pag=$pag+$row4->pago;
              $nom=$row4->nombre;
              $ss=$row4->ss;
              $grado=$row4->grado;
              if ($idc==$row4->id)
                 {
                 $cc=$cc+1;
                 }
               else
                 {
                 $cc=0;
                 }
              }
              if ($deu - $pag > 0)
                 {
                 if ($cc==0)
                    {
                    DB::table('pagos')->insert([
                    'id' => $row5->id,
                    'nombre' => $nom,
                    'desc1' => $n3,
                    'fecha_d' => $fec1,
                    'year' => $year,
                    'codigo' => $n1,
                    'ss' => $ss,
                    'grado' => $grado,
                    'deuda' => $_POST['cantidad'],
                    ]);
                    }
                 if ($cc==1 and $_POST['recargo']=='2')
                    {
                    DB::table('pagos')->insert([
                    'id' => $row5->id,
                    'nombre' => $nom,
                    'desc1' => $n3,
                    'fecha_d' => $fec1,
                    'year' => $year,
                    'codigo' => $n1,
                    'ss' => $ss,
                    'grado' => $grado,
                    'deuda' => $_POST['cantidad2'],
                    ]);
                    }
                 $idc=$row5->id;
                 }
        }
  }


?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<script language="JavaScript">
function cambiaPalabra() {
var dis = document.recargos.recargo.value;
if (dis == '2')
   {
   document.recargos.cantidad2.disabled=false;
   }
 else
   {
   document.recargos.cantidad2.disabled=true;
   }

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

</script> 

<style type="text/css">
.style1 {
	text-align: center;
	font-size: x-large;
}
.style2 {
	background-color: #FFFFCC;
}
.style3 {
	text-align: center;
	background-color: #CCCCCC;
}
.style4 {
	text-align: center;
}
</style>

<style type="text/css">

  input:required:invalid, input:focus:invalid {
    background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAeVJREFUeNqkU01oE1EQ/mazSTdRmqSxLVSJVKU9RYoHD8WfHr16kh5EFA8eSy6hXrwUPBSKZ6E9V1CU4tGf0DZWDEQrGkhprRDbCvlpavan3ezu+LLSUnADLZnHwHvzmJlvvpkhZkY7IqFNaTuAfPhhP/8Uo87SGSaDsP27hgYM/lUpy6lHdqsAtM+BPfvqKp3ufYKwcgmWCug6oKmrrG3PoaqngWjdd/922hOBs5C/jJA6x7AiUt8VYVUAVQXXShfIqCYRMZO8/N1N+B8H1sOUwivpSUSVCJ2MAjtVwBAIdv+AQkHQqbOgc+fBvorjyQENDcch16/BtkQdAlC4E6jrYHGgGU18Io3gmhzJuwub6/fQJYNi/YBpCifhbDaAPXFvCBVxXbvfbNGFeN8DkjogWAd8DljV3KRutcEAeHMN/HXZ4p9bhncJHCyhNx52R0Kv/XNuQvYBnM+CP7xddXL5KaJw0TMAF8qjnMvegeK/SLHubhpKDKIrJDlvXoMX3y9xcSMZyBQ+tpyk5hzsa2Ns7LGdfWdbL6fZvHn92d7dgROH/730YBLtiZmEdGPkFnhX4kxmjVe2xgPfCtrRd6GHRtEh9zsL8xVe+pwSzj+OtwvletZZ/wLeKD71L+ZeHHWZ/gowABkp7AwwnEjFAAAAAElFTkSuQmCC);
    background-position: right top;
    background-repeat: no-repeat;
    -moz-box-shadow: none;
  }
  input:required:valid {
    background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAepJREFUeNrEk79PFEEUx9/uDDd7v/AAQQnEQokmJCRGwc7/QeM/YGVxsZJQYI/EhCChICYmUJigNBSGzobQaI5SaYRw6imne0d2D/bYmZ3dGd+YQKEHYiyc5GUyb3Y+77vfeWNpreFfhvXfAWAAJtbKi7dff1rWK9vPHx3mThP2Iaipk5EzTg8Qmru38H7izmkFHAF4WH1R52654PR0Oamzj2dKxYt/Bbg1OPZuY3d9aU82VGem/5LtnJscLxWzfzRxaWNqWJP0XUadIbSzu5DuvUJpzq7sfYBKsP1GJeLB+PWpt8cCXm4+2+zLXx4guKiLXWA2Nc5ChOuacMEPv20FkT+dIawyenVi5VcAbcigWzXLeNiDRCdwId0LFm5IUMBIBgrp8wOEsFlfeCGm23/zoBZWn9a4C314A1nCoM1OAVccuGyCkPs/P+pIdVIOkG9pIh6YlyqCrwhRKD3GygK9PUBImIQQxRi4b2O+JcCLg8+e8NZiLVEygwCrWpYF0jQJziYU/ho2TUuCPTn8hHcQNuZy1/94sAMOzQHDeqaij7Cd8Dt8CatGhX3iWxgtFW/m29pnUjR7TSQcRCIAVW1FSr6KAVYdi+5Pj8yunviYHq7f72po3Y9dbi7CxzDO1+duzCXH9cEPAQYAhJELY/AqBtwAAAAASUVORK5CYII=);
    background-position: right top;
    background-repeat: no-repeat;
  }

.style6 {
	text-align: left;
}

</style>

<head>
    <?php
    $title = $lang->translation('Pantalla para aplicar recargos');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5">
            <?= $lang->translation('Pantalla para aplicar recargos') ?>
        </h1>
        <a href="<?= Route::url('/admin/billing/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form id="recargos" name="recargos" method="POST" action="">
                <div class="mx-auto" style="max-width: 500px;">
                    <?php if (Session::get('createGrades')): ?>
                        <div class="alert alert-primary col-6 mx-auto mt-1" role="alert">
                            <i class="fa-solid fa-square-check"></i>
                            <?= Session::get('gradesReports', true) ?>
                        </div>
                    <?php endif ?>
                    <div class="input-group mb-3">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('El código del recargo.') ?>
                            </label>
                        </div>
                        <select id="desc" name="desc" class="form-control" required>
                            <option value=''><?= $lang->translation('Selección') ?></option>
                            <?php foreach ($presupuesto as $pres) { ?>
                                <option>
                                    <?= $pres->codigo.', '.$pres->descripcion ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="Comentario">
                                <?= $lang->translation('Seleccione el mes para aplicar el recargo.') ?>
                            </label>
                        </div>
                        <select id="mes" name="mes" class="form-control" style="width: 301px" required>
                            <option value=''><?= $lang->translation('Selección') ?></option>
                            <option value='08'><?= $lang->translation('Agosto') ?></option>
                            <option value='09'><?= $lang->translation('Septiembre') ?></option>
                            <option value='10'><?= $lang->translation('Octubre') ?></option>
                            <option value='11'><?= $lang->translation('Noviembre') ?></option>
                            <option value='12'><?= $lang->translation('Diciembre') ?></option>
                            <option value='01'><?= $lang->translation('Enero') ?></option>
                            <option value='02'><?= $lang->translation('Febrero') ?></option>
                            <option value='03'><?= $lang->translation('Marzo') ?></option>
                            <option value='04'><?= $lang->translation('Abril') ?></option>
                            <option value='05'><?= $lang->translation('Mayo') ?></option>
                            <option value='06'><?= $lang->translation('Junio') ?></option>
                            <option value='07'><?= $lang->translation('Julio') ?></option>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="Comentario">
                                <?= $lang->translation('A quién le aplicará el recargo?') ?>
                            </label>
                        </div>
                        <select id="recargo" name="recargo" class="form-control" style="width: 301px" required  onclick="return cambiaPalabra(); return true">
                            <option value=''><?= $lang->translation('Selección') ?></option>
                            <option value='1'><?= $lang->translation('Por familia') ?></option>
                            <option value='2'><?= $lang->translation('Por estudiante') ?></option>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="Comentario">
                                <?= $lang->translation('Cantidad de recargo.') ?>
                            </label>
                        </div>
                        <input type="text" value='' class="form-control" name='cantidad' id="cantidad" placeholder="$999.99" required>
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="Comentario">
                                <?= $lang->translation('Recargo 2do. hermano.') ?>
                            </label>
                        </div>
                        <input type="text" value='' class="form-control" name='cantidad2' id="cantidad2" disabled placeholder="$999.99" required>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('El código para aplicar el recargo.') ?>
                            </label>
                        </div>
                        <select id="desc2" name="desc2" class="form-control" required>
                            <option value=''><?= $lang->translation('Selección') ?></option>
                            <?php foreach ($presupuesto as $pres) { ?>
                                <option>
                                    <?= $pres->codigo.', '.$pres->descripcion ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <button name='proce' type="submit" class="btn btn-primary d-block mx-auto">
                        <?= $lang->translation('Continuar') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>