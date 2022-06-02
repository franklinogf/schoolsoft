<?php
require_once '../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\Parents;

Session::is_logged();

$parents = new Parents(Session::id());
$lang = new Lang([
  ["Mi Perfil", "My profile"],
  ["Información importante", "Important information"],
  ["Usuario:", "User:"],
  ["Información de la Madre", "Mother's information"],
  ["Nombre Completo", "Full name"],
  ["Ex alumno", "Ex student"],
  ["SI", "YES"],
  ["Correo electrónico", "Email"],
  ["Teléfono residencial", "Residential telephone"],
  ["Celular", "Cellphone"],
  ["Compañia telefonica", "Cellphone company"],
  ["Solo usar números", "Only use numbers"],
  ["Dirección Postal", "Postal Address"],
  ["Dirección", "Address"],
  ["Ciudad", "City"],
  ["Estado", "State"],
  ["Codigo Postal", "Postal Code"],
  ["Información del trabajo", "Job information"],
  ["Compañia", "Company"],
  ["Posición", "Position"],
  ["Teléfono", "Telephone"],
  ["Opciones de correo electrónico y SMS", "Email and SMS options"],
  ["Recibir correo electrónico", "Receive Email"],
  ["Recibir SMS", "Receive SMS"],
  ["Información del Padre", "Father's information"],
  ["Esta opción de SMS se aplica a su contrato o tarifa de mensajes de textos recibidos a su celular.", "This SMS option is applied to your contract or text messagge rate received to your cell phone."],
  ["Teléfono de emergencia", "Emergency phone"],
  ["Nueva contraseña", "New Password"],
  ["Confirmar contraseña", "Confirm Password"],
  ["Las contraseñas no coinciden", "Passwords do not match"],
  ["Información de los hijos", "Children's information"],
  ["Grado:", "Grade:"],
  ["Fecha de nacimiento:", "Date of Birth:"],
  ["Guardar", "Save"],
]);

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
  <?php
  $title = $lang->translation('Mi Perfil');
  Route::includeFile('/parents/includes/layouts/header.php');
  ?>
</head>

<body class='pb-5'>
  <?php
  Route::includeFile('/parents/includes/layouts/menu.php');
  ?>
  <div class="container mt-5">
    <h1 class="text-center"><?= $lang->translation("Mi Perfil") ?> <i class="far fa-id-card"></i></h1>
    <form action="<?= Route::url('/parents/includes/profile.php') ?>" method="POST">
      <div class="row mt-3">
        <div class="offset-lg-4 col-lg-4 col-sm-12 mt-sm-3">
          <hr class="d-lg-none d-sm-block" />
          <div class="card border-info">
            <h6 class="card-header bg-gradient-info bg-info">
              <?= $lang->translation("Información importante") ?>
            </h6>
            <div class="card-body">
              <p class="text-monospace">ID: <span class="badge badge-info"><?= $parents->id ?> </span></p>
              <p class="text-monospace"><?= $lang->translation("Usuario:") ?> <span class="badge badge-info"><?= $parents->usuario ?></span></p>
            </div>
          </div>
        </div>
      </div>
      <hr class="mb-3" />
      <div class="row">
        <!-- Mother Information -->
        <div class="card col-12 col-lg-6 p-3 rounded-0">
          <h5 class="card-title"><?= $lang->translation("Información de la Madre") ?></h5>
          <div class="row">
            <div class="form-group col-12">
              <label for="nameM"><?= $lang->translation("Nombre Completo") ?></label>
              <input type="text" value='<?= $parents->madre ?>' class="form-control" name='nameM' id="nameM" required>
            </div>
            <div class="form-group col-12">
              <label for="exM"><?= $lang->translation("Ex alumno") ?></label>
              <select class="form-control" name="exM" id="exM" required>
                <option <?= $parents->ex_m === 'NO' ? 'selected=""' : '' ?> value="NO">NO</option>
                <option <?= $parents->ex_m === 'SI' ? 'selected=""' : '' ?> value="SI"><?= $lang->translation("SI") ?></option>
              </select>
            </div>
            <div class="form-group col-12">
              <label for="emailM"><?= $lang->translation("Correo electrónico") ?></label>
              <input type="email" value='<?= $parents->email_m ?>' class="form-control" name='emailM' id="emailM">
            </div>
            <div class="form-group col-12">
              <label for="residentialPhoneM"><?= $lang->translation("Teléfono residencial") ?></label>
              <input type="tel" value='<?= $parents->tel_m ?>' class="form-control" name='residentialPhoneM' id="residentialPhoneM" pattern="[0-9]{10}">
              <small class="text-muted"><?= $lang->translation("Solo usar números") ?></small>
            </div>
            <div class="form-group col-md-6">
              <label for="cellPhoneM"><?= $lang->translation("Celular") ?></label>
              <input type="tel" value="<?= $parents->cel_m ?>" class="form-control" id="cellPhoneM" name="cellPhoneM" pattern="[0-9]{10}" />
              <small class="text-muted"><?= $lang->translation("Solo usar números") ?></small>
            </div>
            <div class="form-group col-md-6">
              <label for="cellCompanyM"><?= $lang->translation("Compañia telefonica") ?></label>
              <select id="cellCompanyM" class="form-control" name="cellCompanyM">
                <option <?= $parents->cel_com_m === '' ? 'selected=""' : '' ?> value=""><?= $lang->translation("Seleccionar") ?></option>
                <?php foreach (Util::phoneCompanies() as $company) : ?>
                  <option <?= $parents->cel_com_m === $company ? 'selected=""' : '' ?> value="<?= $company ?>"><?= $company ?></option>
                <?php endforeach ?>
              </select>
            </div>
            <h6 class="card-subtitle col-12 my-2"><?= $lang->translation("Dirección Postal") ?></h6>
            <div class="form-row col-12">
              <div class="col-12">
                <input class="form-control" type="text" placeholder="<?= $lang->translation("Dirección") ?> 1" name="dir1" id="dir1" value="<?= $parents->dir1 ?>" />
              </div>
              <div class="col-12 mt-1">
                <input class="form-control" type="text" placeholder="<?= $lang->translation("Dirección") ?> 2" name="dir3" id="dir3" value="<?= $parents->dir3 ?>" />
              </div>
              <div class="col-5 mt-1">
                <input class="form-control" type="text" placeholder="<?= $lang->translation("Ciudad") ?>" name="city1" id="city1" value="<?= $parents->pueblo1 ?>" />
              </div>
              <div class="col-3 mt-1">
                <input class="form-control" type="text" placeholder="<?= $lang->translation("Estado") ?>" name="state1" id="state1" value="<?= $parents->est1 ?>" />
              </div>
              <div class="col-4 mt-1">
                <input class="form-control" type="text" placeholder="<?= $lang->translation("Codigo Postal") ?>" name="zip1" id="zip1" value="<?= $parents->zip1 ?>" />
              </div>
            </div>
            <h6 class="card-subtitle col-12 my-2"><?= $lang->translation("Información del trabajo") ?></h6>
            <div class="form-row col-12">
              <div class="form-group col-6">
                <label for="jobM"><?= $lang->translation("Compañia") ?></label>
                <input class="form-control" type="text" name="jobM" id="jobM" value="<?= $parents->trabajo_m ?>" />
              </div>
              <div class="form-group col-6">
                <label for="jobPositionM"><?= $lang->translation("Posición") ?></label>
                <input class="form-control" type="text" name="jobPositionM" id="jobPositionM" value="<?= $parents->posicion_m ?>" />
              </div>
              <div class="form-group col-6">
                <label for="jobPhoneM"><?= $lang->translation("Teléfono") ?></label>
                <input class="form-control" type="text" name="jobPhoneM" id="jobPhoneM" value="<?= $parents->tel_t_m ?>" pattern="[0-9]{10}" />
                <small class="text-muted"><?= $lang->translation("Solo usar números") ?></small>
              </div>
            </div>
            <h6 class="card-subtitle col-12 my-2"><?= $lang->translation("Opciones de correo electrónico y SMS") ?></h6>
            <div class="form-row col-12">
              <div class="form-group col-6">
                <label for="receiveEmailM"><?= $lang->translation("Recibir correo electrónico") ?></label>
                <select class="form-control" name="receiveEmailM" id="receiveEmailM">
                  <option <?= $parents->re_e_m === 'NO' ? 'selected=""' : '' ?> value="NO">NO</option>
                  <option <?= $parents->re_e_m === 'SI' ? 'selected=""' : '' ?> value="SI"><?= $lang->translation("SI") ?></option>
                </select>
              </div>
              <div class="form-group col-6">
                <label for="receiveSmsM"><?= $lang->translation("Recibir SMS") ?></label>
                <select class="form-control" name="receiveSmsM" id="receiveSmsM">
                  <option <?= $parents->re_mc_m === 'NO' ? 'selected=""' : '' ?> value="NO">NO</option>
                  <option <?= $parents->re_mc_m === 'SI' ? 'selected=""' : '' ?> value="SI"><?= $lang->translation("SI") ?></option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <!-- .Mother Information -->
        <!-- Father Information -->
        <div class="card col-12 col-lg-6 p-3 rounded-0">
          <h5 class="card-title">Información del Padre</h5>
          <div class="row">
            <div class="form-group col-12">
              <label for="nameP"><?= $lang->translation("Nombre Completo") ?></label>
              <input type="text" value='<?= $parents->padre ?>' class="form-control" name='nameP' id="nameP" required>
            </div>
            <div class="form-group col-12">
              <label for="exP"><?= $lang->translation("Ex alumno") ?></label>
              <select class="form-control" name="exP" id="exP" required>
                <option <?= $parents->ex_p === 'NO' ? 'selected=""' : '' ?> value="NO">NO</option>
                <option <?= $parents->ex_p === 'SI' ? 'selected=""' : '' ?> value="SI"><?= $lang->translation("SI") ?></option>
              </select>
            </div>
            <div class="form-group col-12">
              <label for="emailP">E-mail</label>
              <input type="email" value='<?= $parents->email_p ?>' class="form-control" name='emailP' id="emailP">
            </div>
            <div class="form-group col-12">
              <label for="residentialPhone"><?= $lang->translation("Teléfono residencial") ?></label>
              <input type="tel" value='<?= $parents->tel_p ?>' class="form-control" name='residentialPhoneP' id="residentialPhoneP" pattern="[0-9]{10}">
              <small class="text-muted"><?= $lang->translation("Solo usar números") ?></small>
            </div>
            <div class="form-group col-md-6">
              <label for="cellPhoneP"><?= $lang->translation("Celular") ?></label>
              <input type="tel" value="<?= $parents->cel_p ?>" class="form-control" id="cellPhoneP" name="cellPhoneP" pattern="[0-9]{10}" />
              <small class="text-muted"><?= $lang->translation("Solo usar números") ?></small>
            </div>
            <div class="form-group col-md-6">
              <label for="cellCompanyP"><?= $lang->translation("Compañia telefonica") ?></label>
              <select id="cellCompanyP" class="form-control" name="cellCompanyP">
                <option <?= $parents->cel_com_p === '' ? 'selected=""' : '' ?> value=""><?= $lang->translation("Seleccionar") ?></option>
                <?php foreach (Util::phoneCompanies() as $company) : ?>
                  <option <?= $parents->cel_com_p === $company ? 'selected=""' : '' ?> value="<?= $company ?>"><?= $company ?></option>
                <?php endforeach ?>
              </select>
            </div>
            <h6 class="card-subtitle col-12 my-2"><?= $lang->translation("Dirección Postal") ?></h6>
            <div class="form-row col-12">
              <div class="col-12">
                <input class="form-control" type="text" placeholder="Dirección 1" name="dir2" id="dir2" value="<?= $parents->dir2 ?>" />
              </div>
              <div class="col-12 mt-1">
                <input class="form-control" type="text" placeholder="Dirección 2" name="dir4" id="dir4" value="<?= $parents->dir4 ?>" />
              </div>
              <div class="col-5 mt-1">
                <input class="form-control" type="text" placeholder="<?= $lang->translation("Ciudad") ?>" name="city2" id="city2" value="<?= $parents->pueblo2 ?>" />
              </div>
              <div class="col-3 mt-1">
                <input class="form-control" type="text" placeholder="<?= $lang->translation("Estado") ?>" name="state2" id="state2" value="<?= $parents->est2 ?>" />
              </div>
              <div class="col-4 mt-1">
                <input class="form-control" type="text" placeholder="<?= $lang->translation("Codigo Postal") ?>" name="zip2" id="zip2" value="<?= $parents->zip2 ?>" />
              </div>
            </div>
            <h6 class="card-subtitle col-12 my-2"><?= $lang->translation("Información del trabajo") ?></h6>
            <div class="form-row col-12">
              <div class="form-group col-6">
                <label for="jobP"><?= $lang->translation("Compañia") ?></label>
                <input class="form-control" type="text" name="jobP" id="jobP" value="<?= $parents->trabajo_p ?>" />
              </div>
              <div class="form-group col-6">
                <label for="jobPositionP"><?= $lang->translation("Posición") ?></label>
                <input class="form-control" type="text" name="jobPositionP" id="jobPositionP" value="<?= $parents->posicion_p ?>" />
              </div>
              <div class="form-group col-6">
                <label for="jobPhoneP"><?= $lang->translation("Teléfono") ?></label>
                <input class="form-control" type="text" name="jobPhoneP" id="jobPhoneP" value="<?= $parents->tel_t_p ?>" pattern="[0-9]{10}" />
                <small class="text-muted"><?= $lang->translation("Solo usar números") ?></small>
              </div>
            </div>
            <h6 class="card-subtitle col-12 my-2"><?= $lang->translation("Opciones de E-mail y SMS") ?></h6>
            <div class="form-row col-12">
              <div class="form-group col-6">
                <label for="receiveEmailP"><?= $lang->translation("Recibir E-mail") ?></label>
                <select class="form-control" name="receiveEmailP" id="receiveEmailP">
                  <option <?= $parents->re_e_p === 'NO' ? 'selected=""' : '' ?> value="NO">NO</option>
                  <option <?= $parents->re_e_p === 'SI' ? 'selected=""' : '' ?> value="SI"><?= $lang->translation("SI") ?></option>
                </select>
              </div>
              <div class="form-group col-6">
                <label for="receiveSmsP"><?= $lang->translation("Recibir SMS") ?></label>
                <select class="form-control" name="receiveSmsP" id="receiveSmsP">
                  <option <?= $parents->re_mc_p === 'NO' ? 'selected=""' : '' ?> value="NO">NO</option>
                  <option <?= $parents->re_mc_p === 'SI' ? 'selected=""' : '' ?> value="SI"><?= $lang->translation("SI") ?></option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <!-- .Father Information -->
        <div class="col-12 my-1">
          <p class="text-muted text-center mb-0"><small><?= $lang->translation("Esta opción de SMS se aplica a su contrato o tarifa de mensajes de textos recibidos a su celular.") ?></small></p>
          <hr>
        </div>
        <div class="card col-12 p-3">
          <div class="form-row">
            <div class="form-group col-12 col-md-6 px-3">
              <label for="emergencyPhone"><?= $lang->translation("Teléfono de emergencia") ?></label>
              <input type="tel" value='<?= $parents->tel_e ?>' class="form-control" name='emergencyPhone' id="emergencyPhone" pattern="[0-9]{10}">
              <small class="text-muted"><?= $lang->translation("Solo usar números") ?></small>
            </div>
            <div class="form-group col-12 col-md-6 p-4">
              <label for="pass1"><?= $lang->translation("Nueva contraseña") ?></label>
              <input type="password" class="form-control pass" name='password' id="pass1">
              <label for="pass2"><?= $lang->translation("Confirmar contraseña") ?></label>
              <input type="password" class="form-control pass" id="pass2">
              <div class="invalid-feedback"><?= $lang->translation("Las contraseñas no coinciden") ?></div>
            </div>
          </div>
        </div>

        <div class="col-12 my-1">
          <hr>
        </div>

        <div class="col-12 my-1">
          <h2 class="text-center mb-3"><?= $lang->translation("Información de los hijos") ?></h2>
          <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3">
            <!-- <?php foreach ($parents->kids() as $kid) : ?> -->
            <div class="col">
              <div class="card">
                <div class="card-body">
                  <img src="<?= Util::studentProfilePicture($kid) ?>" class="rounded-circle img-thumbnail d-block mx-auto mb-3 img-fluid" alt="Profile Picture" style="width:150px;height:150px" />
                  <h6 class="card-title"><?= "$kid->nombre $kid->apellidos" ?></h6>
                  <p class="card-text"><?= $lang->translation("Grado:") ?> <?= $kid->grado ?></p>
                  <p class="card-text"><?= $lang->translation("Fecha de nacimiento:") ?> <?= $kid->fecha ?></p>
                </div>
              </div>
            </div>
            <!-- <?php endforeach ?> -->
          </div>
        </div>

      </div>
      <!-- .Row -->
      <div class="mt-3">
        <button type="submit" class="btn btn-primary btn-lg btn-block"><?= $lang->translation("Guardar") ?></button>
      </div>
    </form>
  </div>


  <?php
  Route::includeFile('/includes/layouts/progressBar.php', true);
  Route::includeFile('/includes/layouts/scripts.php', true);
  ?>
</body>

</html>