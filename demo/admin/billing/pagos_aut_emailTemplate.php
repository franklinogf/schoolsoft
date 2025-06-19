<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Recibo</title>
	<style>
		body {
			font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial,
				"Noto Sans", "Liberation Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji",
				"Segoe UI Symbol", "Noto Color Emoji";
		}

		.table {
			max-width: 1140px;
			margin: 20px auto;
			padding: .75rem;
			padding-top: 2rem;
		}

		.bg-white {
			background-color: #fff;
		}

		.bg-light {
			background-color: #f8f9fa !important;
		}

		.text-primary {
			color: #0d6efd !important;
		}

		.pt-1 {
			padding-top: 1rem;
		}

		.p-1 {
			padding: 1rem;
		}

		.p-2 {
			padding: 2rem;
		}

		.border {
			border: 1px solid black;
		}

		p {
			margin-top: 0;
			margin-bottom: 1rem;
		}

		.m-0 {
			margin: 0;
		}

		.mt-2 {
			margin-top: 1rem;
		}
	</style>
</head>

<body>
	<table class='table bg-light'>
		<tr class="bg-white">
			<th align="left" class="p-1">
				<p>Confirmación de pago</p>
			</th>
			<th align="right" class="p-1">
				<p><?= $schoolName ?></p>
			</th>
		</tr>
		<tr>
			<td colspan="2">
				<hr>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="right">
				<p>Número de Referencia: <?= $referenceNumber ?></p>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="right">
				<p>ID de la transacción: <?= $trxID ?></p>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="left" class="text-primary">
				<p>Hola, <?= $fullName ?></p>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="left">
				<p>Queremos dejarte saber que <b><?= $schoolName ?></b> ha recibido tu pago.</p>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="left" class="text-primary">
				<p>Detalles del pago:</p>
			</td>
		</tr>

		<tr>
			<td colspan="2" class="p-2 border">
				<table align="left" style="width: 33.33%">
					<tr>
						<td>
							<b>Cuenta</b> <br />
							<?= $account ?>
						</td>
					</tr>
					<tr>
						<td class="pt-1">
							<b>Fecha de Transacción</b> <br />
							<?= $dateTime ?>
						</td>
					</tr>
				</table>

				<table align="left" style="width: 33.33%">
					<tr>
						<td>
							<b>Descripción del Pago</b> <br />
							<?= $description ?>
						</td>
					</tr>
					<tr>
						<td class="pt-1">
							<b>Número de Autorización</b> <br />
							<?= $authNumber ?>
						</td>
					</tr>
				</table>

				<table align="right" style="width: 33.33%">
					<tr>
						<td align="right">
							<b>Monto</b> <br />
							$<?= $totalAmount ?>
						</td>
					</tr>
					<tr>
						<td align="right" class="pt-1">
							<b>Método de Pago</b> <br />
							<?php if($paymentMethod === 'tarjeta'):?>
								<?= "Tarjeta (xxxxxxxxxxxx{$cardLast4Digits})" ?>
							<?php else: ?>
							<?= "ACH {$cardLast4Digits}" ?>
							<?php endif ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p class="mt-2"><?= $desc ?></p>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p class="mt-2">Muchas gracias por pagar en linea.</p>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p class="m-0">Sinceramente,</p>
			</td>
		</tr>
		<tr>
			<th colspan="2" align="left">
				<p><?= $schoolName ?></p>
			</th>
		</tr>

	</table>

</body>

</html>