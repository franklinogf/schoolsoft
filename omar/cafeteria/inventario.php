<?php

use Classes\DataBase\DB;
use Classes\Route;

require_once '../app.php';

$items = DB::table('inventario')->orderBy('articulo')->get();
$isAdding = isset($_GET['add']);
$item_id = $_GET['item'] ?? null;
// if (isset($_POST['barra'])) {
//     $ssqla = "select * from inventario where cbarra='" . $_POST['barra'] . "'";
//     $rssqla = mysql_query($ssqla);
//     $data = mysql_fetch_array($rssqla);
//     //echo $ssqla;
// }

// if (isset($_POST['buscar'])) {
//     $ssqla = "select * from inventario where id='" . $_POST['inv'] . "'";
//     $rssqla = mysql_query($ssqla);
//     $data = mysql_fetch_array($rssqla);
//     //echo $ssqla;
// }
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
	<?php $title = "Inventario Cafetería" ?>
	<?php Route::includeFile("/cafeteria/includes/layouts/header.php"); ?>
</head>
<style>

</style>

<body>
	<div class="container-lg mt-lg-3 mb-5 px-0">
		<h1 class="text-center mb-3 mt-5">Pantalla para guardar artículos de cafetería</h1>
		<div class="mx-auto" style="width: 25rem;">
			<div class="mb-4 text-center">
				<a class="btn btn-sm btn-link" href="./menu.php">Regresar</a>
				<?php if (!$isAdding): ?>
					<a class="btn btn-sm btn-primary" href="./inventario.php?add">Agregar</a>
				<?php else: ?>
					<a class="btn btn-sm btn-primary" href="./inventario.php">Cancelar</a>
				<?php endif; ?>
			</div>
			<div class="container bg-white shadow-lg py-3 rounded">
				<?php if (!$isAdding): ?>
					<form method="GET">
						<select name="item" class="form-control" required>
							<?php foreach ($items as $item) : ?>
								<option <?= $item_id !== null && intval($item_id) === $item->id ? 'selected' : '' ?> value="<?= $item->id ?>"><?= $item->articulo ?></option>
							<?php endforeach; ?>
						</select>
						<button class="btn btn-primary btn-sm btn-block mt-2" type="submit">Buscar</button>
					</form>
				<?php endif ?>
				<?php if ($item_id !== null || $isAdding):
					if (!$isAdding) {
						$item = DB::table('inventario')->where('id', $item_id)->first();
					}

				?>
					<div class="mt-4">
						<form action="<?= Route::url('/cafeteria/includes/inventario.php') ?>" method="POST">
							<?php if (!$isAdding) : ?>
								<input type="hidden" name="id" value="<?= $item->id ?>">
							<?php endif; ?>
							<div class="form-group">
								<label for="id2">ID</label>
								<input type="text" class="form-control" name="id2" id="id2" value="<?= $item->id2 ?? '' ?>">
							</div>
							<div class="form-group">
								<label for="articulo">Nombre</label>
								<input type="text" class="form-control" name="articulo" id="articulo" value="<?= $item->articulo ?? '' ?>">
							</div>
							<div class="form-group">
								<label for="precio">Precio</label>
								<input type="text" class="form-control" name="precio" id="precio" value="<?= $item->precio ?? '' ?>">
							</div>
							<div class="form-group">
								<label for="cantidad">Cantidad</label>
								<input type="text" class="form-control" name="cantidad" id="cantidad" value="<?= $item->cantidad ?? '' ?>">
							</div>
							<div class="form-group">
								<label for="minimo">Cantidad Minimo</label>
								<input type="text" class="form-control" name="minimo" id="minimo" value="<?= $item->minimo ?? '' ?>">
							</div>
							<div class="form-group">
								<label for="cbarra">Código de barras</label>
								<input type="text" class="form-control" name="cbarra" id="cbarra" value="<?= $item->cbarra ?? '' ?>">
							</div>
							<button class="btn btn-primary btn-sm btn-block mt-2" type="submit" name="<?= $isAdding ? 'add' : 'edit' ?>">Guardar</button>
							<?php if (!$isAdding) : ?>
								<button class="btn btn-danger btn-sm btn-block mt-2" type="submit" name="delete">Eliminar</button>
							<?php endif; ?>
						</form>
					</div>
				<?php endif; ?>
			</div>
		</div>

	</div>
</body>

</html>