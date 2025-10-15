<?php
require_once '../../../app.php';

use Classes\DataBase\DB;
use Classes\Route;
use Classes\Session;
use Classes\Lang;
use Classes\Controllers\School;

Session::is_logged();
$lang = new Lang([
  ["Códigos de Alias", "Alias codes"],
  ['Grabar', 'Save'],
  ['Código', 'Code'],
  ['Editar', 'Edit'],
  ['Borrar', 'Delete'],
  ['Debe de llenar todos los campos', 'You must fill all fields'],
  ['Lista de codigos', 'Codes list'],
  ['', ''],
  ['Grado', 'Grade'],
  ['', ''],
  ['Opciones', 'Options'],
]);

$school = new School(Session::id());

header('Content-type: text/html; charset=UTF-8');
$idioma = 'Es';

if ($idioma == "Es") {
	$btn = "Buscar";
	$btn2 = "Cambiar grado";
	$header1 = "Alias";
	$header2 = "Estudiantes";
} else {
	$btn = "Search";
	$header1 = "Grade";
	$header2 = "Students";
	$btn2 = "Change grade";
}
$year = $school->info('year2');

if (isset($_POST['cambiar'])) {
	$alias = utf8_decode($_POST['alias']);

    $estudiantes = DB::table('year')->where([
        ['alias', $alias],
        ['codigobaja', 0],
        ['year', $year]
   ])->orderBy('apellidos')->get();

        foreach ($estudiantes as $row ) {
		$grado = $_POST["estu$row->mt"];
		$alias = utf8_decode($_POST["alias$row->mt"]);

       $thisStu = DB::table('year')->where('mt', $row->mt)->update([
         'alias' => $alias,
         'grado' => $grado,
       ]);

       $thisStu = DB::table('pagos')->where([
        ['ss', $row->ss],
        ['year', $year]
       ])->update([
         'grado' => $grado,
       ]);

       $thisStu = DB::table('padres')->where([
        ['ss', $row->ss],
        ['year', $year]
       ])->update([
         'grado' => $grado,
       ]);

       $thisStu = DB::table('padres2')->where([
        ['ss', $row->ss],
        ['year', $year]
       ])->update([
         'grado' => $grado,
       ]);

       $thisStu = DB::table('padres3')->where([
        ['ss', $row->ss],
        ['year', $year]
       ])->update([
         'grado' => $grado,
       ]);

       $thisStu = DB::table('padres4')->where([
        ['ss', $row->ss],
        ['year', $year]
       ])->update([
         'grado' => $grado,
       ]);

       $thisStu = DB::table('padres5')->where([
        ['ss', $row->ss],
        ['year', $year]
       ])->update([
         'grado' => $grado,
       ]);

       $thisStu = DB::table('padres6')->where([
        ['ss', $row->ss],
        ['year', $year]
       ])->update([
         'grado' => $grado,
       ]);
		
       $thisStu = DB::table('asisdia')->where([
        ['ss', $row->ss],
        ['year', $year]
       ])->update([
         'grado' => $grado,
       ]);

       $thisStu = DB::table('asispp')->where([
        ['ss', $row->ss],
        ['year', $year]
       ])->update([
         'grado' => $grado,
       ]);

	}
}

$grados = DB::table('year')->select("DISTINCT alias")->where([
        ['codigobaja', 0],
        ['year', $year]
   ])->orderBy('alias')->get();

$aliases = DB::table('alias')->where([
        ['year', $year]
   ])->orderBy('alias')->get();
?>

<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../../jv/botones.css" />
	<link rel="stylesheet" href="datatable/css/jquery.dataTables.min.css">
	<link rel="stylesheet" href="label.css">

	<style type="text/css">
		.gris {
			text-align: center;
			font-size: large;
			background-color: #CCCCCC;
		}

		.color {
			text-align: center;
			background-color: #ffffcc;
		}

		.centrar {
			margin: 0 auto;
			width: 700px;
		}

		.margen {
			margin-top: 30px;
		}

		.gra {
			width: 50px;
			text-align: center;
		}
	</style>
    <?php
    $title = $lang->translation('Cambiar de Alias');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>

</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
	$alias = utf8_decode($_POST['alias'] ?? '');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5">
            <?= $lang->translation('Cambiar de Alias') ?>
        </h1>
        <a href="<?= Route::url('/admin/users/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">

	<form method="POST">
		<table class="centrar margen">
			<thead>
				<tr class="gris">
					<th colspan="2"><?php echo $header1 ?> <?php echo $year ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<select style="width: 200px;" required="" name="alias">
							<option value="">Seleccionar</option>
                            <?php foreach ($grados as $row) { ?>
								<option <?= ($alias === utf8_encode($row->alias)) ? "selected" : ''; ?> value="<?= utf8_encode($row->alias) ?>"> <?= utf8_encode($row->alias) ?></option>
                            <?php } ?>
						</select>
					</td>
					<td>
						<input name="buscar" class="btn btn-primary d-block mx-auto" style="width:200px" type="submit" value="<?= $btn ?>">
					</td>
				</tr>
			</tbody>
		</table>
	</form>
	<!-- Lista de estudiantes -->
	<?php if (isset($_POST['alias'])) :
		$alias = utf8_decode($_POST['alias']);
	?>
		<?php 
   $estudiantes = DB::table('year')->where([
        ['alias', $alias],
        ['codigobaja', 0],
        ['year', $year]
   ])->orderBy('apellidos')->get();

		?>
		<form method="POST">
			<input type="hidden" name="alias" value="<?php echo $_POST['alias'] ?>">
			<div class="container margen">
				<table id="tabla" class="display compact" width="100%">
					<thead>
						<tr style="text-align:left;">
							<th colspan="3"><?php echo "$header2 $header1 {$_POST['alias']}"; ?></th>
						</tr>
					</thead>
					<tbody>
                            <?php foreach ($estudiantes as $estu) { ?>
							<tr>
								<td><?= strtoupper(utf8_encode("$estu->apellidos $estu->nombre")); ?></td>
								<td>
									<input style="width: 100px;" type="text" class="gra" required="" name="estu<?= $estu->mt ?>" value="<?= $estu->grado ?>">
								</td>
								<td>
									<select name="alias<?= $estu->mt ?>">
									<option value="">Selecciona un alias</option>
										<?php
                                        foreach ($aliases as $alias) { ?>
											<option <?= $alias->alias === $estu->alias && "$alias->grado-$alias->seccion" === $estu->grado ? 'selected' : '' ?> value="<?= utf8_encode($alias->alias) ?>"><?= utf8_encode("$alias->alias $alias->grado-$alias->seccion") ?></option>
										<?php } ?>
									</select>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
				<table class="centrar">
					<tbody>
						<tr>
							<td class="gris">
								<input name="cambiar" class="btn btn-primary d-block mx-auto" style="width:200px" type="submit" value="<?php echo $btn2 ?>">
							</td>
						</tr>
					</tbody>
				</table>
          </div>
		</form>
	<?php endif ?>

	</div>
	</div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>
</html>