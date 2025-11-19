<?php

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\Route;

require_once __DIR__ . '/../../app.php';

$lang = new Lang([
  ['Caja', 'Cash register'],
  ['Buscar pagos', 'Search payments'],
  ['Buscar', 'Search'],
  ['Eliminar compra', 'Delete purchase'],
  ['Modificar el Total', 'Edit total'],
  ['Modificar', 'Edit'],
  ['Eliminar', 'Delete'],
  ['Cancelar', 'Cancel'],
  ['Enviar recibos', 'Send receipts']
]);
// agregar nueva columna a la tabla de detalles
DB::table('compra_cafeteria_detalle')->alter("ADD `precio_final` FLOAT(7,2) NULL DEFAULT NULL AFTER `precio`;");

$colegio = new School();
$year = $colegio->year();
$estudiantes = [];
$btns = DB::table('T_cafeteria')->orderBy('orden')->get();


$estudiantes = DB::table('year')->select('ss,nombre,apellidos')->where('year', $year)->orderBy('apellidos')->get();


?>
<!doctype html>
<html lang="<?= __LANG ?>">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>Cafetería - Sistema de Caja</title>

  <?php
  $title = __("Caja");
  Route::includeFile('/cafeteria/includes/layouts/header.php');
  Route::selectPicker();
  ?>
  <!-- Icons -->
  <link rel="stylesheet" type="text/css" href="../css/all.css">
  <link rel="stylesheet" href="caja.css">

</head>

<body class="bg-light">
  <div class="container-fluid">
    <!-- Enhanced Header -->
    <div class="cafeteria-header text-center">
      <div class="container">
        <h1 class="display-4 mb-0">
          <i class="fas fa-utensils mr-3"></i>
          CAFETERÍA
        </h1>
        <p class="lead mb-0">Sistema de Caja Registradora</p>
      </div>
    </div>

    <div class="container">
      <form id="pagarForm" method="POST" action="pagar.php">
        <div class="row">

          <!-- LIST START -->
          <div class="col-lg-8 order-md-1">
            <!-- BARCODE INPUT -->
            <div class="card mb-4">
              <div class="card-body">
                <div class="barcode-input">
                  <label for="barcode" class="font-weight-bold">
                    <i class="fas fa-barcode text-primary mr-2"></i>
                    Código de Barra
                  </label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text bg-primary text-white">
                        <i class="fas fa-barcode"></i>
                      </span>
                    </div>
                    <input id="barcode" type="text" class="form-control" placeholder="Escanear o ingresar código de barra" autocomplete="off">
                  </div>
                </div>
              </div>
            </div>

            <!-- FOOD ITEMS GRID -->
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
              <?php foreach ($btns as $btn) : ?>
                <div class="col">
                  <div class="card food-card h-100">
                    <input type="hidden" class="id" value="<?= $btn->id ?>">
                    <a href="#" class="text-decoration-none">
                      <img class="card-img-top" src="<?= isset($btn->foto) ? "../../../cafeteria_im/$btn->foto" : '../../../cafeteria_im/no-image.png' ?>" alt="<?= htmlspecialchars($btn->articulo) ?>">
                    </a>
                    <div class="card-body">
                      <h6 class="card-title text-truncate"><?= htmlspecialchars($btn->articulo) ?></h6>
                      <div class="price">
                        <span class="font-weight-bold">$<?= number_format($btn->precio, 2) ?></span>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach ?>
            </div>




          </div>
          <!-- LIST END -->

          <!-- SHOPPING CART SECTION -->
          <div class="col-lg-4 order-md-2">
            <div class="cart-section sticky-top">
              <!-- Action Buttons -->
              <div class="mb-4">
                <div class="btn-group-vertical w-100" role="group" aria-label="Cart Actions">
                  <a href="../menu.php" class="btn btn-outline-secondary btn-lg mb-2 d-flex align-items-center justify-content-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    <span>Salir</span>
                  </a>
                  <a href="#" id="pagar" class="btn btn-success btn-lg d-flex align-items-center justify-content-center disabled" data-toggle="modal" data-target="#pagarModal">
                    <i class="fas fa-credit-card mr-2"></i>
                    <span>Pagar</span>
                  </a>
                </div>
              </div>

              <!-- Cart Header -->
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                  <i class="fas fa-shopping-cart text-primary mr-2"></i>
                  Carrito de Compras
                </h5>
                <span id="cant" class="badge badge-primary badge-pill">0</span>
              </div>

              <!-- Shopping Cart Items -->
              <div class="shopping-cart mb-3">
                <ul id="cart" class="list-group shadow-sm">
                  <li class="list-group-item cart-total d-flex justify-content-between align-items-center">
                    <span>
                      <i class="fas fa-calculator mr-2"></i>Total
                    </span>
                    <strong id="total">$0.00</strong>
                  </li>
                </ul>
              </div>

              <!-- Search Payments Button -->
              <button type="button" class="btn btn-outline-info btn-block mb-3" data-toggle="modal" data-target="#searchModal">
                <i class="fas fa-search mr-2"></i><?= __("Buscar pagos") ?>
              </button>

              <!-- Send Receipts Section -->
              <div class="card">
                <div class="card-body">
                  <h6 class="card-title">
                    <i class="fas fa-receipt mr-2"></i><?= __("Enviar recibos") ?>
                  </h6>
                  <div class="input-group">
                    <input type="date" id="date" class="form-control" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>">
                    <div class="input-group-append">
                      <button id="sendReceipts" class="btn btn-primary" type="button">
                        <i class="fas fa-paper-plane"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- END SHOPPING CART -->
        </div>

        <!-- ENHANCED SEARCH MODAL -->
        <div class="modal fade" id="searchModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="searchModalLabel">
                  <i class="fas fa-search mr-2"></i><?= __("Buscar pagos") ?>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <!-- Date Filter -->
                <div class="card mb-3">
                  <div class="card-body">
                    <div class="form-group mb-0">
                      <label for="searchDate" class="font-weight-bold">
                        <i class="fas fa-calendar-alt mr-2"></i>Fecha de búsqueda
                      </label>
                      <input id="searchDate" type="date" class="form-control" value="<?= date('Y-m-d') ?>" autocomplete="off">
                    </div>
                  </div>
                </div>

                <!-- Search Tabs -->
                <ul class="nav nav-tabs nav-fill" id="searchTab" role="tablist">
                  <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="barcode-tab" data-toggle="tab" href="#barcodeContent" role="tab" aria-controls="barcodeContent" aria-selected="true">
                      <i class="fas fa-barcode mr-2"></i>Código de Barra
                    </a>
                  </li>
                  <li class="nav-item" role="presentation">
                    <a class="nav-link" id="name-tab" data-toggle="tab" href="#nameContent" role="tab" aria-controls="nameContent" aria-selected="false">
                      <i class="fas fa-user mr-2"></i>Por Nombre
                    </a>
                  </li>
                  <li class="nav-item" role="presentation">
                    <a class="nav-link" id="id-tab" data-toggle="tab" href="#idContent" role="tab" aria-controls="idContent" aria-selected="false">
                      <i class="fas fa-hashtag mr-2"></i>Por ID
                    </a>
                  </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content mt-3" id="searchTabContent">
                  <!-- Barcode Search -->
                  <div class="tab-pane fade show active" id="barcodeContent" role="tabpanel" aria-labelledby="barcode-tab">
                    <div class="card">
                      <div class="card-body">
                        <div class="form-group">
                          <label for="searchBarcode" class="font-weight-bold">Buscar con el código de barra</label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary text-white">
                                <i class="fas fa-barcode"></i>
                              </span>
                            </div>
                            <input id="searchBarcode" type="text" class="form-control" placeholder="Escanear o ingresar código de barra" autocomplete="off">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Name Search -->
                  <div class="tab-pane fade" id="nameContent" role="tabpanel" aria-labelledby="name-tab">
                    <div class="card">
                      <div class="card-body">
                        <div class="form-group">
                          <label for="searchEstu" class="font-weight-bold">Buscar por Nombre</label>
                          <div class="input-group">
                            <select class="selectpicker form-control" id="searchEstu" title="-Seleccionar al estudiante-" aria-label="Buscar pago por nombre de estudiante" data-live-search="true">
                              <?php foreach ($estudiantes as $estu) : ?>
                                <option value="<?php echo $estu->ss ?>"><?= "$estu->apellidos $estu->nombre" ?></option>
                              <?php endforeach ?>
                            </select>
                            <div class="input-group-append">
                              <button class="btn btn-primary" type="button" id="searchBtn">
                                <i class="fas fa-search mr-2"></i><?= __("Buscar") ?>
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- ID Search -->
                  <div class="tab-pane fade" id="idContent" role="tabpanel" aria-labelledby="id-tab">
                    <div class="card">
                      <div class="card-body">
                        <div class="form-group">
                          <label for="searchId" class="font-weight-bold">Buscar por ID de la compra</label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text">
                                <i class="fas fa-hashtag"></i>
                              </span>
                            </div>
                            <input class="form-control" id="searchId" placeholder="Ingrese el ID de la compra" type="number" />
                            <div class="input-group-append">
                              <button class="btn btn-primary" type="button" id="searchIdBtn">
                                <i class="fas fa-search mr-2"></i><?= __("Buscar") ?>
                              </button>
                            </div>
                          </div>
                          <input type="hidden" id="searchSs">
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Results Table -->
                  <div class="mt-4">
                    <div class="table-responsive">
                      <table id="payments" class="table table-hover d-none">
                        <thead class="thead-dark">
                          <tr>
                            <th><i class="fas fa-hashtag mr-1"></i>ID</th>
                            <th><i class="fas fa-dollar-sign mr-1"></i>Total</th>
                            <th><i class="fas fa-cogs mr-1"></i>Acciones</th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                      </table>
                    </div>
                    <div id="alert"></div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                  <i class="fas fa-times mr-2"></i>Cerrar
                </button>
              </div>
            </div>
          </div>
        </div>
        <!-- ENHANCED DELETE MODAL -->
        <div class="modal fade" id="delSearchModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="delSearchModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="delSearchModalLabel">
                  <i class="fas fa-exclamation-triangle mr-2"></i><?= __("Eliminar compra") ?>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body text-center">
                <div class="mb-4">
                  <i class="fas fa-trash-alt text-danger" style="font-size: 3rem;"></i>
                </div>
                <p class="lead mb-3"></p>
                <div class="alert alert-warning">
                  <i class="fas fa-exclamation-triangle mr-2"></i>
                  <strong>¡Atención!</strong> Esta acción no se puede deshacer.
                </div>
                <input type="hidden" id="delId">
                <input type="hidden" id="delTotal">
              </div>
              <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary mr-3" data-dismiss="modal">
                  <i class="fas fa-times mr-2"></i><?= __("Cancelar") ?>
                </button>
                <button type="button" id="delBtn" class="btn btn-danger">
                  <i class="fas fa-trash mr-2"></i><?= __("Eliminar") ?>
                </button>
              </div>
            </div>
          </div>
        </div>
        <!-- ENHANCED EDIT MODAL -->
        <div class="modal fade" id="editSearchModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="editSearchModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editSearchModalLabel">
                  <i class="fas fa-edit mr-2"></i><?= __("Modificar el Total") ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="card mb-3">
                  <div class="card-header">
                    <h6 class="mb-0">
                      <i class="fas fa-shopping-basket mr-2"></i>Artículos de la compra
                    </h6>
                  </div>
                  <div class="card-body">
                    <ul id="editItems" class="list-group list-group-flush"></ul>
                  </div>
                </div>

                <div class="form-group">
                  <label for="editTotal" class="font-weight-bold">
                    <i class="fas fa-calculator mr-2"></i>Total de la compra
                  </label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">$</span>
                    </div>
                    <input type="text" class="form-control form-control-lg" id="editTotal" readonly>
                  </div>
                </div>

                <input type="hidden" id="editId">
                <input type="hidden" id="editBefore">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                  <i class="fas fa-times mr-2"></i>Cerrar
                </button>
                <button class="btn btn-success" type="button" id="editBtn">
                  <i class="fas fa-save mr-2"></i>Actualizar
                </button>
              </div>
            </div>
          </div>
        </div>
        <!-- MODAL -->
        <div class="modal fade" id="pagarModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="pagarModalTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="pagarModalTitle">Pagar orden</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <!-- Payment Methods -->
                <div class="mb-4">
                  <h6 class="mb-3">
                    <i class="fas fa-credit-card mr-2"></i>Método de Pago
                  </h6>
                  <div id="metodos" class="btn-group btn-group-toggle d-flex flex-wrap" data-toggle="buttons">
                    <label class="lblmetodo btn btn-outline-primary payment-method active flex-fill">
                      <input type="radio" name="metodo" class="metodo" id="metodo1" value="1" checked>
                      <img src="metodo1.png" alt="Efectivo" class="d-block mx-auto">
                      <small class="d-block mt-1">Efectivo</small>
                    </label>
                    <label class="lblmetodo btn btn-outline-primary payment-method flex-fill">
                      <input type="radio" name="metodo" class="metodo" id="metodo2" value="2">
                      <img src="metodo2.png" alt="Tarjeta de credito/debito" class="d-block mx-auto">
                      <small class="d-block mt-1">Tarjeta</small>
                    </label>
                    <label class="lblmetodo btn btn-outline-primary payment-method flex-fill">
                      <input type="radio" name="metodo" class="metodo" id="metodo3" value="3">
                      <img src="metodo3.png" alt="ID" class="d-block mx-auto">
                      <small class="d-block mt-1">ID</small>
                    </label>
                    <label class="lblmetodo btn btn-outline-primary payment-method flex-fill">
                      <input type="radio" name="metodo" class="metodo" id="metodo4" value="4">
                      <img src="metodo4.jpg" alt="Nombre" class="d-block mx-auto">
                      <small class="d-block mt-1">Nombre</small>
                    </label>
                    <label class="lblmetodo btn btn-outline-primary payment-method flex-fill">
                      <input type="radio" name="metodo" class="metodo" id="metodo5" value="5">
                      <img src="metodo5.png" alt="ATH" class="d-block mx-auto">
                      <small class="d-block mt-1">ATH</small>
                    </label>
                  </div>
                </div>

                <!-- Student Selection Section -->
                <div class="card mb-3">
                  <div class="card-body">
                    <!-- Name-based Student Selection -->
                    <div id="IDestu" class="form-group">
                      <label for="estu" class="font-weight-bold">
                        <i class="fas fa-user-graduate mr-2"></i>Seleccionar Estudiante
                      </label>
                      <div class="input-group">
                        <select class="selectpicker form-control" name="estu" id="estu" title="-Seleccionar al estudiante-" aria-label="Buscar estudiante" data-live-search="true">
                          <?php foreach ($estudiantes as $estu) : ?>
                            <option value="<?php echo $estu->ss ?>"><?php echo "$estu->apellidos $estu->nombre" ?></option>
                          <?php endforeach ?>
                        </select>
                        <div class="input-group-append">
                          <button class="btn btn-primary" type="button" id="buscarEstu">
                            <i class="fas fa-search mr-2"></i><?= __("Buscar") ?>
                          </button>
                        </div>
                      </div>
                    </div>

                    <!-- Barcode-based Student Selection -->
                    <div id="IDestudiante" class="form-row">
                      <div class="form-group col-12">
                        <label for="estudiante" class="font-weight-bold">
                          <i class="fas fa-barcode mr-2"></i>Código de Barra del Estudiante
                        </label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text bg-primary text-white">
                              <i class="fas fa-id-card"></i>
                            </span>
                          </div>
                          <input type="text" class="form-control" name="estudiante" id="estudiante" placeholder="Escanear o ingresar ID del estudiante">
                        </div>
                      </div>
                    </div>

                    <!-- Credit Payment Option -->
                    <div id="credito" class="card border-info d-none">
                      <div class="card-body">
                        <h6 class="card-title">
                          <i class="fas fa-credit-card mr-2"></i>Opciones de Crédito
                        </h6>
                        <div class="form-group mb-0">
                          <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" name="credito" class="custom-control-input" id="checkCredit" value="si">
                            <label class="custom-control-label" for="checkCredit">
                              <i class="fas fa-check-circle text-success mr-1"></i>Pagar con crédito
                            </label>
                          </div>
                          <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" name="credito" class="custom-control-input" id="checkCredit2" value="no" checked>
                            <label class="custom-control-label" for="checkCredit2">
                              <i class="fas fa-times-circle text-danger mr-1"></i>No usar crédito
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Student Profile Section -->
                <div class="card mb-3 d-none" id="studentProfile">
                  <div class="card-body text-center">
                    <div class="row align-items-center">
                      <div class="col-4">
                        <img id="profilePicture" class="img-fluid img-thumbnail profile-picture" src="#" alt="Foto del estudiante">
                      </div>
                      <div class="col-8">
                        <h5 id="nombre_estudiante" class="mb-0"></h5>
                        <small class="text-muted">Estudiante seleccionado</small>
                      </div>
                    </div>
                  </div>
                </div>



                <input type="hidden" name="cbarra" id="cbarra">

                <!-- Payment Information -->
                <div class="card mb-3">
                  <div class="card-header">
                    <h6 class="mb-0">
                      <i class="fas fa-money-bill-wave mr-2"></i>Información de Pago
                    </h6>
                  </div>
                  <div class="card-body">
                    <div class="form-row">
                      <!-- Total Amount -->
                      <div class="form-group col-md-6">
                        <label for="cantidadPagar" class="font-weight-bold">
                          <i class="fas fa-calculator mr-2"></i>Total a pagar
                        </label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text bg-success text-white">$</span>
                          </div>
                          <input type="text" class="form-control form-control-lg" name="cantidadPagar" readonly id="cantidadPagar" placeholder="0.00">
                        </div>
                      </div>

                      <!-- Available Balance -->
                      <div class="form-group col-md-6 deposito">
                        <label for="cantidadDeposito" class="font-weight-bold">
                          <i class="fas fa-wallet mr-2"></i>Balance disponible
                        </label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text bg-info text-white">$</span>
                          </div>
                          <input type="text" class="form-control form-control-lg" name="cantidadDeposito" readonly id="cantidadDeposito" placeholder="0.00">
                        </div>
                      </div>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="form-group deposito">
                      <label class="font-weight-bold">
                        <i class="fas fa-hand-holding-usd mr-2"></i>Método de pago específico
                      </label>
                      <div class="card border-light">
                        <div class="card-body">
                          <div class="form-check form-check-inline">
                            <input type="radio" id="customRadio1" name="tdp2" value="Efectivo" disabled class="form-check-input" checked>
                            <label class="form-check-label" for="customRadio1">
                              <i class="fas fa-money-bill text-success mr-1"></i>Efectivo
                            </label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input type="radio" id="customRadio2" name="tdp2" value="Credito" disabled class="form-check-input">
                            <label class="form-check-label" for="customRadio2">
                              <i class="fas fa-credit-card text-primary mr-1"></i>Tarjeta
                            </label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input type="radio" id="customRadio3" name="tdp2" value="ATH" disabled class="form-check-input">
                            <label class="form-check-label" for="customRadio3">
                              <i class="fas fa-university text-info mr-1"></i>ATH
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Final Amount -->
                    <div class="form-group deposito">
                      <label for="cantidadEfectivo" class="font-weight-bold">
                        <i class="fas fa-receipt mr-2"></i>Cantidad final
                      </label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text bg-warning text-dark">$</span>
                        </div>
                        <input type="text" class="form-control form-control-lg" name="cantidadEfectivo" readonly id="cantidadEfectivo" placeholder="0.00">
                      </div>
                      <small id="creditoAdicional" class="form-text text-warning d-none">
                        <i class="fas fa-exclamation-triangle mr-1"></i>$1.00 adicional por pagar con crédito
                      </small>
                    </div>
                  </div>
                </div>

                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i><?= __("Cancelar") ?>
                  </button>
                  <button type="submit" id="btnPagar" class="btn btn-success btn-lg pagar">
                    <i class="fas fa-credit-card mr-2"></i>Procesar Pago
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    <!-- PROGRESSBAR MODAL -->
    <div id="progressModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body text-center">
            <h6 class="mb-3">Procesando...</h6>
            <div class="progress">
              <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    </form>
  </div> <!-- Close container -->
  </div> <!-- Close container-fluid -->

  <?php
  Route::includeFile('/includes/layouts/scripts.php', true);
  Route::selectPicker('js');
  ?>
  <script src='caja.js' type="text/javascript"></script>

</body>

</html>