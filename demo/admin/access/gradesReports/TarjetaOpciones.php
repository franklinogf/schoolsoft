<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();


$lang = new Lang([
    ['Informe de calificaciones', 'Report Card'],
    ['Reporte de Notas', 'Grade Report'],
    ['Idioma', 'Language'],
    ['Grado', 'Grade'],
    ['Opción', 'Option'],
    ['Continuar', 'Continue'],
    ['Semestre 1', 'Semester 1'],
    ['Semestre 2', 'Semester 2'],
    ['Trimestre 1', 'Quarter 1'],
    ['Trimestre 2', 'Quarter 2'],
    ['Trimestre 3', 'Quarter 3'],
    ['Trimestre 4', 'Quarter 4'],
    ['Con promedio final', 'With final average'],
    ['Con Créditos', 'With Credits'],
    ['Con firma', 'With signature'],
    ['Atrás', 'Go back'],
    ['Mensaje', 'Message'],
    ['Comentario', 'Comment'],
    ['Selección', 'Selection'],
    ['Mensajes para las tarjeta', 'Messages for the cards'],

]);
$school = new School(Session::id());
//$grades = DB::table('materias')->where('year', $school->info('year2'))->orderBy('grado')->get();
$grades = $school->allGrades();

$re = $school->info('tar');
$in1 = '';
$in2 = '';
$in3 = '';
$in4 = '';
$in5 = '';
$in6 = '';
$in7 = '';
$in8 = '';
$in9 = '';
$in10 = '';
$in11 = '';
$in12 = '';
$in13 = '';
$in14 = '';
$in15 = '';
$in16 = '';
$in17 = '';
$in18 = '';
$in19 = '';
$in20 = '';
$in31 = '';
if ($re == '1') {
    $in1 = 'selected';
}
if ($re == '2') {
    $in2 = 'selected';
}
if ($re == '3') {
    $in3 = 'selected';
}
if ($re == '5') {
    $in5 = 'selected';
}
if ($re == '7') {
    $in7 = 'selected';
}
if ($re == '10') {
    $in10 = 'selected';
}
if ($re == '13') {
    $in13 = 'selected';
}
if ($re == '14') {
    $in14 = 'selected';
}
if ($re == '17') {
    $in17 = 'selected';
}
if ($re == '18') {
    $in18 = 'selected';
}
if ($re == '31') {
    $in31 = 'selected';
}

if (isset($_POST['bus'])) {
   $mensaj = DB::table('codigos')->where([
      ['idc', ''],
      ['codigo', $_POST['num']],
   ])->orderBy('codigo')->first();
}

if (isset($_POST['bor'])) {
    DB::table('codigos')->where('codigo', $_POST['num1'])->delete();
   }

if (isset($_POST['gra'])) {
    if (empty($_POST['num1'])) {
       DB::table('codigos')->insert([
          't1e' => $_POST['t1e'],
          't2e' => $_POST['t2e'],
          't1i' => $_POST['t1i'],
          't2i' => $_POST['t2i'],
        ]);
       }
    else
       {
       DB::table('codigos')->where([
          ['codigo', $_POST['num1']],
       ])->update([
          't1e' => $_POST['t1e'],
          't2e' => $_POST['t2e'],
          't1i' => $_POST['t1i'],
          't2i' => $_POST['t2i'],
       ]);
       }
   }


$result = DB::table('codigos')->where([
    ['idc', ''],
])->orderBy('codigo')->get();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<script language="JavaScript">
    function activarTrimestre() {
        var dis = document.TarjetaNotas.tarjeta.value;
        if (dis == '2' || dis == '1b') {
            document.TarjetaNotas.tri.disabled = false;
        } else {
            document.TarjetaNotas.tri.disabled = true;
        }

    }
</script>

<head>
    <?php
    $title = $lang->translation('Informe de calificaciones');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5">
            <?= $lang->translation('Informe de calificaciones') ?>
        </h1>
        <a href="<?= Route::url('/admin/access/gradesReports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form id="TarjetaNotas" name="TarjetaNotas" method="POST" target="_blank" action="<?= Route::url('/admin/access/gradesReports/pdf/TarjetaOpciones.php') ?>">
                <div class="mx-auto" style="max-width: 500px;">
                    <?php if (Session::get('createGrades')): ?>
                        <div class="alert alert-primary col-6 mx-auto mt-1" role="alert">
                            <i class="fa-solid fa-square-check"></i>
                            <?= Session::get('gradesReports', true) ?>
                        </div>
                    <?php endif ?>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Reporte de Notas') ?>
                            </label>
                        </div>
                        <select id="tarjeta" name="tarjeta" class="form-control" onclick="return activarTrimestre(); return true">
                            <option value='1' <?= $in1 ?>>Tarjeta 1</option>
                            <option value='2' <?= $in2 ?>>Tarjeta 2</option>
                            <option value='3' <?= $in3 ?>>Tarjeta 3</option>
                            <option value='1b' <?= $in4 ?>>Tarjeta 4</option>
                            <option value='5' <?= $in5 ?>>Tarjeta 5</option>
                            <option value='7' <?= $in7 ?>>Tarjeta 7</option>
                            <option value='10' <?= $in10 ?>>Tarjeta 10</option>
                            <option value='13' <?= $in13 ?>>Tarjeta 13</option>
                            <option value='14' <?= $in14 ?>>Tarjeta 14</option>
                            <option value='17' <?= $in17 ?>>Tarjeta 17</option>
                            <option value='18' <?= $in18 ?>>Tarjeta 18</option>
                            <option value='31' <?= $in31 ?>>Tarjeta 31</option>
                        </select>
                        <select id="tri" name="tri" class="form-control" disabled="disable">
                            <option value='1'><?= $lang->translation('Trimestre 1') ?></option>
                            <option value='2'><?= $lang->translation('Trimestre 2') ?></option>
                            <option value='3'><?= $lang->translation('Trimestre 3') ?></option>
                            <option value='4'><?= $lang->translation('Trimestre 4') ?></option>
                            <option value='5'><?= $lang->translation('Semestre 1') ?></option>
                            <option value='6'><?= $lang->translation('Semestre 2') ?></option>
                            <option value='7'><?= $lang->translation('Q-1') ?></option>
                            <option value='8'><?= $lang->translation('Q-2') ?></option>
                            <option value='9'><?= $lang->translation('Q-3') ?></option>
                            <option value='10'><?= $lang->translation('Q-4') ?></option>
                            <option value='11'><?= $lang->translation('Q-5') ?></option>
                            <option value='12'><?= $lang->translation('Q-6') ?></option>
                            <option value='13'><?= $lang->translation('Q-7') ?></option>
                            <option value='14'><?= $lang->translation('Q-8') ?></option>
                            <option value='15'><?= $lang->translation('Final') ?></option>
                        </select>

                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Idioma') ?>
                            </label>
                        </div>
                        <select id="idioma" name="idioma" class="form-control" required>
                            <option value='1'>Español</option>
                            <option value='2'>English</option>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Grado') ?>
                            </label>
                        </div>
                        <select id="grade" name="grade" class="form-control" required>
                            <?php foreach ($grades as $grade): ?>
                                <option value='<?= $grade ?>'>
                                    <?= $grade ?>
                                </option>
                            <?php endforeach ?>
                        </select>

                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Pag. asistencia') ?>
                            </label>
                        </div>
                        <input id="asis" name="asis" type="checkbox" style="height: 30px; width: 30px" value="Si" />


                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Trimestre 1') ?>
                            </label>
                        </div>
                        <input id="tri1" name="tri1" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Trimestre 2') ?>
                            </label>
                        </div>
                        <input id="tri2" name="tri2" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Semestre 1') ?>
                            </label>
                        </div>
                        <input id="sem1" name="sem1" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Trimestre 3') ?>
                            </label>
                        </div>
                        <input id="tri3" name="tri3" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Trimestre 4') ?>
                            </label>
                        </div>
                        <input id="tri4" name="tri4" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Semestre 2') ?>
                            </label>
                        </div>
                        <input id="sem2" name="sem2" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                    </div>


                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Con promedio final') ?>
                            </label>
                        </div>
                        <input id="prof" name="prof" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Con Créditos') ?>
                            </label>
                        </div>
                        <input id="cr" name="cr" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Con firma') ?>
                            </label>
                        </div>
                        <input id="fir" name="fir" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="mensaje">
                                <?= $lang->translation('Mensaje') ?>
                            </label>
                        </div>
                        <select id="grade" name="mensaje" class="form-control">
                            <option><?= $lang->translation('Selección') ?></option>
                            <?php foreach ($mensaj as $mes) { ?>
                                <option value='<?= $mes->codigo ?>'>
                                    <?= $mes->codigo ?>
                                </option>
                            <?php } ?>
                        </select>
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="Comentario">
                                <?= $lang->translation('Comentario') ?>
                            </label>
                        </div>
                        <select id="comentario" name="comentario" class="form-control">
                            <option><?= $lang->translation('Selección') ?></option>
                            <option value='1'>1</option>
                            <option value='2'>2</option>
                            <option value='3'>3</option>
                            <option value='4'>4</option>
                        </select>
                    </div>

                    <button name='create' type="submit" class="btn btn-primary d-block mx-auto">
                        <?= $lang->translation('Continuar') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>


    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5">
            <?= $lang->translation('Mensajes para las tarjeta') ?>
        </h1>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form id="TarjetaNotas2" name="TarjetaNotas2" method="POST" action="">
                <div class="mx-auto" style="max-width: 500px;">
   		        <input type=hidden name=num1 value='<?= $mensaj->codigo ?? '' ?>'>
		&nbsp;
		<table align="center" cellpadding="2" cellspacing="0" style="width: 70%">
			<tr>
				<td class="style2">
					<strong>Mensajes en español</strong>
				</td>
			</tr>
			<tr>
				<td class="style5">
					<input maxlength='100' name='t1e' size='100' style='width: 662px' type='text' value='<?= $mensaj->t1e ?? '' ?> ' >
				</td>
			</tr>
			<tr>
				<td class="style5">
					<input maxlength='100' name='t2e' size='100' style='width: 662px' type='text' value='<?= $mensaj->t2e ?? '' ?> ' >
				</td>
			</tr>
			<tr>
				<td class="style2">
					<strong>En ingles</strong>
				</td>
			</tr>
			<tr>
				<td class="style5" style="height: 23px">
					<input maxlength='100' name='t1i' size='100' style='width: 662px' type='text' value='<?= $mensaj->t1i ?? '' ?>'>
				</td>
			</tr>
			<tr>
				<td class="style5">
					<input maxlength='100' name='t2i' size='100' style='width: 662px' type='text' value='<?= $mensaj->t2i ?? '' ?>'>
				</td>
			</tr>
			<tr>
				<td class="style2" style="height: 23px"></td>
			</tr>
			<tr>
				<td class="style5">
					<input name="gra" type="submit" value="Grabar" class="btn btn-primary" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input name="bor" type="submit" value="Borrar" onclick="return confirmar('&iquest;Está seguro que desea eliminar el mensaje?')" class="btn btn-danger" />&nbsp;&nbsp;&nbsp;
					<select name="num" style="width: 87px">
                      <?php foreach ($result as $row ): ?>
							<?=  '<option>' . $row->codigo . '</option>'; ?>
                      <?php endforeach ?>
					</select>&nbsp;&nbsp;&nbsp;&nbsp;
					<input name="bus" type="submit" value="Buscar" class="btn btn-primary" />
				</td>
			</tr>
			<tr>
				<td class="style2">&nbsp;</td>
			</tr>
		</table>


		<br />


                </div>
            </form>
        </div>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>
<script language="JavaScript">
        function confirmar(mensaje) {
            return confirm(mensaje);
        }


    var dis = document.TarjetaNotas.tarjeta.value;
    if (dis == '2') {
        //   document.TarjetaNotas.tri.disabled=false;
    } else {
        //   document.TarjetaNotas.tri.disabled=true;
    }
</script>

</html>