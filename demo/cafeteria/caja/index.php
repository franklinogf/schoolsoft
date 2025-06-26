<?php

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\Route;

require_once '../../app.php';

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
$foods = DB::table('T_cafeteria')->orderBy('orden')->get();


$estudiantes = DB::table('year')->select('ss,nombre,apellidos')->where('year', $year)->orderBy('apellidos')->get();


?>
<!doctype html>
<html lang="<?= __LANG ?>">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>Cafeteria</title>

  <?php
  $title = $lang->translation("Caja");
  Route::includeFile('/cafeteria/includes/layouts/header.php');
  Route::selectPicker();
  ?>
  <!-- Icons -->
  <link rel="stylesheet" type="text/css" href="../css/all.css">
  <link rel="stylesheet" href="caja.css">

</head>

<body class="bg-light">
  <div class="container">
    <div class="py-5 text-center">
      <h2>CAFETERIA <i class="fas fa-utensils"></i></h2>
    </div>

    <form id="pagarForm" method="POST" action="pagar.php">
      <div class="row">

        <!-- LIST START -->
        <div class="col-md-8 order-md-1 ">
          <!-- CODIGO DE BARRA -->
          <div style="margin-bottom: 10px" class="w-50">
            <!-- <label class="sr-only" for="barcode"></label> -->
            <div class="input-group mb-2">
              <div class="input-group-prepend">
                <div class="input-group-text"><i class="fas fa-barcode"></i></div>
              </div>
              <input id="barcode" type="text" class="form-control" id="barcode" placeholder="Codigo de barra" autocomplete="no">
            </div>
          </div>

          <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4">
            <?php foreach ($foods as $food) : ?>
              <div class="col mb-3">
                <div class="card  h-100">
                  <input type="hidden" class="id" value="<?= $food->id ?>">
                  <a href="#"><img class="card-img-top mx-auto d-block" src="<?= isset($food->foto) ? "../../../cafeteria_im/$food->foto" : '../../../cafeteria_im/no-image.png' ?>" alt="Card image cap"></a>
                  <div class="card-body">
                    <p class="card-title"><?= $food->articulo ?></p>
                    <span class="price"><b><?= $food->precio ?></b></span>
                  </div>
                </div>
              </div>
            <?php endforeach ?>
          </div>




        </div>
        <!-- LIST END -->

        <div class="col-md-4 order-md-2 mb-4 d-flex flex-column">
          <a href="../menu.php" class="btn btn-secondary btn-md btn-block">Salir <i class="fas fa-angle-left"></i></a>
          <a href="#" id="pagar" class="btn btn-info btn-lg btn-block disabled" data-toggle="modal" data-target="#pagarModal">Pagar</a>
          <h4 class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted">Compra <i class="fas fa-shopping-cart"></i></span>
            <span id="cant" class="badge badge-secondary badge-pill">0</span>
          </h4>
          <ul id="cart" class="list-group mb-3 shadow skillsDouble">
            <li class="list-group-item d-flex justify-content-between active">
              <span>Total <i class="fas fa-dollar-sign"></i></span>
              <strong id="total">$0</strong>
            </li>
          </ul>
          <button type="button" class="btn btn-outline-info btn-block mt-auto" data-toggle="modal" data-target="#searchModal">Buscar pagos</button>

          <div class="input-group my-3">
            <input type="date" id="date" class="form-control" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" aria-describedby="button-addon">
            <div class="input-group-append">
              <button id="sendReceipts" class="btn btn-outline-secondary" type="button" id="button-addon">Enviar recibos</button>
            </div>
          </div>
        </div>
        <!-- END SHOPPING CART -->
      </div>

      <!-- SEARCH MODAL -->
      <div class="modal fade" id="searchModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content border border-info">
            <div class="modal-header">
              <h5 class="modal-title" id="searchModalLabel">Buscar un pago</h5>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label for="searchDate">Fecha</label>
                <input id="searchDate" type="date" class="form-control" value="<?= date('Y-m-d') ?>" autocomplete="no">
              </div>
              <ul class="nav nav-tabs" id="searchTab" role="tablist">
                <li class="nav-item" role="presentation">
                  <a class="nav-link active" id="barcode-tab" data-toggle="tab" href="#barcodeContent" role="tab" aria-controls="barcodeContent" aria-selected="true">Por codigo de barra</a>
                </li>
                <li class="nav-item" role="presentation">
                  <a class="nav-link" id="name-tab" data-toggle="tab" href="#nameContent" role="tab" aria-controls="nameContent" aria-selected="false">Por nombre</a>
                </li>
                <li class="nav-item" role="presentation">
                  <a class="nav-link" id="id-tab" data-toggle="tab" href="#idContent" role="tab" aria-controls="idContent" aria-selected="false">Por ID</a>
                </li>
              </ul>
              <div class="tab-content mt-3" id="searchTabContent">
                <div class="tab-pane fade show active" id="barcodeContent" role="tabpanel" aria-labelledby="barcode-tab">
                  <div class="form-group">
                    <label for="searchBarcode">Buscar con el codigo de barra</label>
                    <div class="input-group w-75 mb-2">
                      <div class="input-group-prepend">
                        <div class="input-group-text"><i class="fas fa-barcode"></i></div>
                      </div>
                      <input id="searchBarcode" type="text" class="form-control" placeholder="Codigo de barra" autocomplete="no">
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade" id="nameContent" role="tabpanel" aria-labelledby="name-tab">
                  <div class="form-group">
                    <label for="searchEstu">Buscar por Nombre</label>
                    <div class="input-group">
                      <select class="selectpicker w-75" id="searchEstu" title="-Seleccionar al estudiante-" aria-label="Buscar pago por nombre de estudiante" data-live-search="true">
                        <?php foreach ($estudiantes as $estu) : ?>
                          <option value="<?php echo $estu->ss ?>"><?= "$estu->apellidos $estu->nombre" ?></option>
                        <?php endforeach ?>
                      </select>
                      <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="button" id="searchBtn">Buscar</button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade" id="idContent" role="tabpanel" aria-labelledby="id-tab">
                  <div class="form-group">
                    <label for="searchEstu">Buscar por ID de la compra</label>
                    <div class="input-group w-50">
                      <input class="form-control" id="searchId" />
                      <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="button" id="searchIdBtn">Buscar</button>
                      </div>
                    </div>
                    <input type="hidden" id="searchSs">

                  </div>
                </div>

                <table id="payments" class="table table-sm d-none">
                  <thead>
                    <tr class="thead-dark">
                      <th>ID</th>
                      <th>Total</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
                <div id="alert"></div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Del Modal -->
      <div class="modal fade" id="delSearchModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="delSearchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered ">
          <div class="modal-content border border-danger">
            <div class="modal-header">
              <h5 class="modal-title" id="delSearchModalLabel">Eliminar compra</h5>
            </div>
            <div class="modal-body">
              <p></p>
              <input type="hidden" id="delId">
              <input type="hidden" id="delTotal">
            </div>
            <div class="modal-footer">
              <button type="button" id="delBtn" class="btn btn-danger btn-sm">Eliminar</button>
              <button type="button" class="btn btn-secondary btn-sm">Cancelar</button>
            </div>
          </div>
        </div>
      </div>
      <!-- Edit Modal -->
      <div class="modal fade" id="editSearchModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="editSearchModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content border border-warning">
            <div class="modal-header">
              <h5 class="modal-title" id="editSearchModalLabel">Modificar el Total</h5>
            </div>
            <div class="modal-body">
              <h6 class="mb-2">Articulos de la compra</h6>
              <ul id="editItems"></ul>
              <div class="form-group">
                <label for="editTotal">Total de la compra</label>
                <input type="text" class="form-control" id="editTotal" disabled>
              </div>
              <input type="hidden" id="editId">
              <input type="hidden" id="editBefore">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary btn-sm">Cerrar</button>
              <button class="btn btn-primary btn-sm" type="button" id="editBtn">Actualizar</button>
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

              <div id="metodos" class="btn-group btn-group-toggle flex-wrap" data-toggle="buttons">
                <label class="lblmetodo btn btn-light active">
                  <input type="radio" name="metodo" class="metodo" id="metodo1" value="1" checked> <img src="metodo1.png" alt="Efectivo">
                </label>
                <label class="lblmetodo btn btn-light">
                  <input type="radio" name="metodo" class="metodo" id="metodo2" value="2"> <img src="metodo2.png" alt="Tarjeta de credito/debito">
                </label>
                <label class="lblmetodo btn btn-light">
                  <input type="radio" name="metodo" class="metodo" id="metodo3" value="3"> <img src="metodo3.png" alt="ID">
                </label>
                <label class="lblmetodo btn btn-light">
                  <input type="radio" name="metodo" class="metodo" id="metodo4" value="4"> <img src="metodo4.jpg" alt="Nombre">
                </label>
                <label class="lblmetodo btn btn-light">
                  <input type="radio" name="metodo" class="metodo" id="metodo5" value="5"> <img src="metodo5.png" alt="Ath">
                </label>
              </div>

              <!--  -->
              <div id="IDestu" class="form-group">
                <label for="estu">Nombre de estudiante</label>
                <div class="input-group">
                  <select class="selectpicker w-75" name="estu" id="estu" title="-Seleccionar al estudiante-" aria-label="Buscar estudiante" data-live-search="true">
                    <?php foreach ($estudiantes as $estu) : ?>
                      <option value="<?php echo $estu->ss ?>"><?php echo "$estu->apellidos $estu->nombre" ?></option>
                    <?php endforeach ?>
                  </select>
                  <div class="input-group-append">
                    <button class="btn btn-outline-primary" type="button" id="buscarEstu">Buscar</button>
                  </div>
                </div>
              </div>


              <div id="IDestudiante" class="form-row">
                <div class="form-group col-md-6">
                  <label for="estudiante">Codigo de barra del Estudiante</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                    <input type="text" class="form-control" name="estudiante" id="estudiante" placeholder="ID">
                  </div>
                </div>
              </div>

              <div id="credito" class="mb-2 d-none">
                Pagar con crédito
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" name="credito" class="custom-control-input" id="checkCredit" value="si">
                  <label class="custom-control-label" for="checkCredit">Si</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" name="credito" class="custom-control-input" id="checkCredit2" value="no" checked>
                  <label class="custom-control-label" for="checkCredit2">No</label>
                </div>
              </div>


              <div class="row">
                <div class="col-4 mx-auto">
                  <img id="profilePicture" class="img-fluid img-thumbnail d-none mb-3" src="#" style="width: 7rem;" alt="Profile Picture">
                </div>
                <div class="row">
                  <div class="col-8">
                    <h4 id="nombre_estudiante"></h4>
                  </div>
                </div>
              </div>



              <input type="hidden" name="cbarra" id="cbarra">

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="cantidadPagar">Total a pagar</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">$</span>
                    </div>
                    <input type="text" class="form-control" name="cantidadPagar" readonly="" id="cantidadPagar" placeholder="$0.00">
                  </div>
                </div>
                <div class="form-group col-md-6 deposito">
                  <label for="cantidadDeposito">Balance disponible</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">$</span>
                    </div>
                    <input type="text" class="form-control" name="cantidadDeposito" readonly="" id="cantidadDeposito" placeholder="$0.00">
                  </div>

                </div>

                <div class="form-group col-12 deposito">
                  <label for="cantidadEfectivo">Pagar con</label>
                  <div class="radios">
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" id="customRadio1" name="tdp2" value="Efectivo" disabled class="custom-control-input" checked>
                      <label class="custom-control-label" for="customRadio1">Efectivo</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" id="customRadio2" name="tdp2" value="Credito" disabled class="custom-control-input">
                      <label class="custom-control-label" for="customRadio2">Tarjeta</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" id="customRadio3" name="tdp2" value="ATH" disabled class="custom-control-input">
                      <label class="custom-control-label" for="customRadio3">ATH</label>
                    </div>
                  </div>
                </div>

                <div class="form-group col-12 text-right deposito">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">$</span>
                    </div>
                    <input type="text" class="form-control" name="cantidadEfectivo" readonly="" id="cantidadEfectivo" placeholder="$0.00">
                  </div>
                  <small id="creditoAdicional" class="text-muted d-none">$1 adicional por pagar con credito</small>
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" id="btnPagar" class="btn btn-primary pagar">Pagar</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- PROGRESSBAR MODAL -->
      <div id="progressModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-body">
              <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">%</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::selectPicker('js');
    ?>
    <script src='caja.js' type="text/javascript"></script>

</body>

</html>