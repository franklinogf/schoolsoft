<?php
require_once '../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;

Session::is_logged();

$teacher = new Teacher(Session::id());

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
  <?php
  $title = "Mi Perfil";
  Route::includeFile('/regiweb/includes/layouts/header.php');
  ?>
</head>

<body class='pb-5'>
  <?php
  Route::includeFile('/regiweb/includes/layouts/menu.php');
  ?>
  <div class="container mt-5">
    <h1 class="text-center">Mi Perfil <i class="far fa-id-card"></i></h1>
    <form action="<?= Route::url('/regiweb/includes/profiles.php') ?>" method="POST" enctype="multipart/form-data">
      <div class="row mt-5">
        <div class="col-lg-6 col-sm-12">
          <input type="hidden" name="id_teacher" value="<?= $teacher->id ?>">
          <img src="<?= $teacher->profilePicture() ?>" alt="Profile Picture" class="profile-picture img-thumbnail rounded mx-auto d-block" width="250" height="250">
          <div class="form-group text-center mt-2">
            <button id="pictureBtn" type='button' class="btn btn-secondary">Cambiar Foto</button>
            <button id="pictureCancel" type='button' hidden class="btn btn-danger"><i class="fas fa-times"></i></button>
            <input type="file" hidden name="picture" id="picture" accept="image/jpg,image/png,image/gif,image/jpeg">
          </div>
        </div>
        <div class="offset-lg-2 col-lg-4 col-sm-12 mt-sm-3">
          <hr class="d-lg-none d-sm-block" />
          <div class="card border-info">
            <h6 class="card-header bg-gradient-info bg-info">
              Información importante
            </h6>
            <div class="card-body">
              <p class="text-monospace">ID: <span class="badge badge-info"><?= $teacher->id ?> </span></p>
              <p class="text-monospace">Usuario: <span class="badge badge-info"><?= $teacher->usuario ?></span></p>
              <p class="text-monospace">Salon Hogar: <span class="badge badge-info"><?= $teacher->grado ?></span></p>
              <p class="text-monospace">Total de estudiantes: <span class="badge badge-info"><?= sizeof($teacher->homeStudents()) ?></span></p>
            </div>
          </div>
        </div>
      </div>
      <hr class="mb-3" />
      <div class="row">
        <!-- Personal Information -->
        <div class="card col-12 col-lg-6 p-3">
          <h5 class="card-title">Información Personal</h5>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="name">Nombre</label>
              <input type="text" value='<?= $teacher->nombre ?>' class="form-control" name='name' id="name" required>
            </div>
            <div class="form-group col-md-6">
              <label for="lastname">Apellidos</label>
              <input type="text" value='<?= $teacher->apellidos ?>' class="form-control" name='lastName' id="lastname" required>
            </div>
            <div class="form-group col-6">
              <label for="gender">Genero</label>
              <select class="form-control" value="<?= $teacher->genero ?>" name="gender" id="gender">
                <option value="Femenino">Femenino</option>
                <option value="Masculino">Masculino</option>
              </select>
            </div>
            <div class="form-group col-6">
              <label for="dob">Fecha de nacimiento</label>
              <input class="form-control" type="date" name="dob" id="dob" value="<?= $teacher->fecha_nac ?>" />
            </div>
            <div class="form-group col-12">
              <label for="email1">E-mail Principal</label>
              <input type="email" value='<?= $teacher->email1 ?>' class="form-control" name='email1' id="email1">
              <label for="email2">E-mail Secundario</label>
              <input type="email" value='<?= $teacher->email2 ?>' class="form-control" name='email2' id="email2">
            </div>
            <div class="form-group col-12">
              <label for="residentialPhone">Teléfono residencial</label>
              <input type="tel" value='<?= $teacher->tel_res ?>' class="form-control" name='residentialPhone' id="residentialPhone" pattern="[0-9]{10}">
              <label for="emergencyPhone">Teléfono de emergencia</label>
              <input type="tel" value='<?= $teacher->tel_emer ?>' class="form-control" name='emergencyPhone' id="emergencyPhone" pattern="[0-9]{10}">
            </div>
            <div class="form-group col-md-6">
              <label for="cellPhone">Celular</label>
              <input type="tel" value="<?= $teacher->cel ?>" class="form-control" id="cellPhone" name="cellPhone" pattern="[0-9]{10}" />
            </div>
            <div class="form-group col-md-6">
              <label for="cellCompany">Compañia telefonica</label>
              <select id="cellCompany" class="form-control" name="cellCompany">
                <option <?= $teacher->comp === '' ? 'selected=""' : '' ?> value="">Seleciona una</option>
                <option <?= $teacher->comp === 'T-Movil' ? 'selected=""' : '' ?> value="T-Movil">T-Mobile</option>
                <option <?= $teacher->comp === 'AT&T' ? 'selected=""' : '' ?> value="AT&T">AT&T</option>
                <option <?= $teacher->comp === 'Sprint' ? 'selected=""' : '' ?> value="Sprint">Sprint</option>
                <option <?= $teacher->comp === 'Open M.' ? 'selected=""' : '' ?> value="Open M.">Open M.</option>
                <option <?= $teacher->comp === 'Moviltar' ? 'selected=""' : '' ?> value="Movistar">Movistar</option>
                <option <?= $teacher->comp === 'Claro' ? 'selected=""' : '' ?> value="Claro">Claro</option>
                <option <?= $teacher->comp === 'Suncom' ? 'selected=""' : '' ?> value="Suncom">Suncom</option>
                <option <?= $teacher->comp === 'Verizon' ? 'selected=""' : '' ?> value="Verizon">Verizon</option>
                <option <?= $teacher->comp === 'Boost' ? 'selected=""' : '' ?> value="Boost">Boost Mobile</option>
              </select>
            </div>
            <div class="form-group  col-md-6">
              <label for="pass1">Nueva Clave</label>
              <input type="password" class="form-control pass" name='password' id="pass1">
              <label for="pass2">Confirmar Clave</label>
              <input type="password" class="form-control pass" id="pass2">
              <div class="invalid-feedback">Las claves no coinciden</div>
            </div>
          </div>
        </div>
        <!-- .Personal Information -->
        <div class="col-12 offset-lg-1 col-lg-5 p-0">
          <!-- Address -->
          <div class="card col-12 p-3 mt-2 mt-lg-0">
            <h5 class="card-title mb-1">Direcciones</h5>
            <h6 class="card-subtitle mb-2 mt-2">Dirección Residencial</h6>
            <div class="form-row">
              <div class="col-12">
                <input class="form-control" type="text" placeholder="Dirección 1" name="dir1" id="dir1" value="<?= $teacher->dir1 ?>" />
              </div>
              <div class="col-12 mt-1">
                <input class="form-control" type="text" placeholder="Dirección 2" name="dir2" id="dir2" value="<?= $teacher->dir2 ?>" />
              </div>
              <div class="col-5 mt-1">
                <input class="form-control" type="text" placeholder="Ciudad" name="city1" id="city1" value="<?= $teacher->pueblo1 ?>" />
              </div>
              <div class="col-3 mt-1">
                <input class="form-control" type="text" placeholder="Estado" name="state1" id="state1" value="<?= $teacher->esta1 ?>" />
              </div>
              <div class="col-4 mt-1">
                <input class="form-control" type="text" placeholder="Codigo Postal" name="zip1" id="zip1" value="<?= $teacher->zip1 ?>" />
              </div>
            </div>
            <hr />
            <h6 class="card-subtitle mb-2 mt-2">Dirección Postal</h6>
            <div class="custom-control custom-check mb-2">
              <input class="custom-control-input" type="checkbox" id="sameDirection">
              <label class="custom-control-label" for="sameDirection">
                Misma dirección que la residencial
              </label>
            </div>
            <div class="form-row">
              <div class="col-12">
                <input class="form-control" type="text" placeholder="Dirección 1" name="dir3" id="dir3" value="<?= $teacher->dir3 ?>" />
              </div>
              <div class="col-12 mt-1">
                <input class="form-control" type="text" placeholder="Dirección 2" name="dir4" id="dir4" value="<?= $teacher->dir4 ?>" />
              </div>
              <div class="col-5 mt-1">
                <input class="form-control" type="text" placeholder="Ciudad" name="city2" id="city2" value="<?= $teacher->pueblo2 ?>" />
              </div>
              <div class="col-3 mt-1">
                <input class="form-control" type="text" placeholder="Estado" name="state2" id="state2" value="<?= $teacher->esta2 ?>" />
              </div>
              <div class="col-4 mt-1">
                <input class="form-control" type="text" placeholder="Codigo Postal" name="zip2" id="zip2" value="<?= $teacher->zip2 ?>" />
              </div>
            </div>
          </div>
          <!-- .Address -->
          <!-- Other Information -->
          <div class="card col-12 p-3 mt-2">
            <h5 class="card-title mb-1">Otras</h5>
            <div class="form-row">
              <div class="form-group col-12">
                <label for="alias">Alias</label>
                <input class="form-control" type="text" name="alias" id="alias" value="<?= $teacher->alias ?>" />
              </div>
              <div class="form-group col-6">
                <label for="position">Posición</label>
                <input class="form-control" type="text" name="position" id="position" value="<?= $teacher->posicion ?>" />
              </div>
              <div class="form-group col-6">
                <label for="level">Nivel</label>
                <select class="form-control" value="<?= $teacher->nivel ?>" name="level" id="level">
                  <option value="Elemental">Elemental</option>
                  <option value="Pre-Escolar">Pre-Escolar</option>
                  <option value="Elemental">Elemental</option>
                  <option value="Intermedia">Intermedia</option>
                  <option value="Superior">Superior</option>
                </select>
              </div>
              <div class="form-group col-12">
                <label for="preparation">Preparación</label>
                <input class="form-control" type="text" name="preparation1" id="preparation1" value="<?= $teacher->preparacion1 ?>" />
              </div>
              <div class="form-group col-12">
                <input class="form-control" type="text" name="preparation2" id="preparation2" value="<?= $teacher->preparacion2 ?>" />
              </div>
              <div class="col-12">
                <hr />
              </div>
              <div class="form-group col-6">
                <label for="initialDate">Fecha de inicio</label>
                <input class="form-control" type="date" name="initialDate" id="initialDate" value="<?= $teacher->fecha_ini ?>" />
              </div>
              <div class="form-group col-6">
                <label for="dischargeDate">Fecha de baja</label>
                <input class="form-control" type="date" name="dischargeDate" id="dischargeDate" value="<?= $teacher->fecha_daja ?>" />
              </div>
              <div class="form-group col-12 row">
                <label class="col-6 col-form-label" for="getEmails">Recibir correos</label>
                <select class="form-control col-6" value="<?= $teacher->re_e ?>" name="getEmails" id="getEmails">
                  <option value="NO">NO</option>
                  <option value="SI">SI</option>
                </select>
              </div>
            </div>
            <!--- .form-row -->
          </div>
          <!-- .Other Information -->
        </div>
        <!-- .Row -->        
      </div>
      <!-- .Row -->
      <!-- Accordion -->
      <div class="accordion mt-2 p-0" id="profileAccordion">
          <div class="card">
            <div class="card-header bg-white" id="clubsHead">
              <h2 class="mb-0">
                <button class="btn btn-link btn-block text-left text-dark font-weight-bold" type="button" data-toggle="collapse" data-target="#clubs" aria-expanded="true" aria-controls="clubs">
                  Clubes
                </button>
              </h2>
            </div>
            <div id="clubs" class="collapse" aria-labelledby="clubsHead" data-parent="#profileAccordion">
              <div class="card-body">
                <div class="form-row">
                  <?php for ($i = 1; $i <= 5; $i++) : ?>
                    <div class="form-row col-12 mb-2">
                      <div class="form-group col-3">
                        <label for="<?= "club$i" ?>">Nombre</label>
                        <input class="form-control" type="text" name="<?= "club$i" ?>" id="<?= "club$i" ?>" value="<?= $teacher->{"club$i"} ?>" />
                      </div>
                      <div class="form-group col-3">
                        <label for="<?= "pre$i" ?>">Presidente</label>
                        <input class="form-control" type="text" name="<?= "pre$i" ?>" id="<?= "pre$i" ?>" value="<?= $teacher->{"pre$i"} ?>" />
                      </div>
                      <div class="form-group col-3">
                        <label for="<?= "vi$i" ?>">Vice Presidente</label>
                        <input class="form-control" type="text" name="<?= "vi$i" ?>" id="<?= "vi$i" ?>" value="<?= $teacher->{"vi$i"} ?>" />
                      </div>
                      <div class="form-group col-3">
                        <label for="<?= "se$i" ?>">Secretario(a)</label>
                        <input class="form-control" type="text" name="<?= "se$i" ?>" id="<?= "se$i" ?>" value="<?= $teacher->{"se$i"} ?>" />
                      </div>
                    </div>
                  <?php endfor ?>
                </div>
              </div>
            </div>
          </div>
          <div class="card">
            <div class="card-header bg-white" id="licencesHead">
              <h2 class="mb-0">
                <button class="btn btn-link btn-block text-left text-dark font-weight-bold" type="button" data-toggle="collapse" data-target="#licences" aria-expanded="true" aria-controls="licences">
                  Licencias
                </button>
              </h2>
            </div>
            <div id="licences" class="collapse" aria-labelledby="licencesHead" data-parent="#profileAccordion">
              <div class="card-body">
                <div class="form-row">
                  <?php for ($i = 1; $i <= 4; $i++) : ?>
                    <div class="form-row col-12 mb-2">
                      <input type="text" class="form-control mr-3 col-6" value="<?= $teacher->{"lic$i"} ?>" disabled>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="<?= "lp$i" ?>" value="Si" <?= $teacher->{"lp$i"} === 'Si' ? "checked" : "checked" ?> disabled>
                        <label class="form-check-label" for="<?= "lp$i" ?>">
                          Exp.
                        </label>
                      </div>
                      <input class="form-control ml-3 col-2" type="date" id="<?= "fex$i" ?>" value="<?= $teacher->{"fex$i"} ?>" disabled />
                    </div>
                  <?php endfor ?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- .Accordion -->
      <div class="mt-3">
        <button type="submit" class="btn btn-primary btn-lg btn-block">Guardar</button>
      </div>
    </form>
  </div>


  <?php
  Route::includeFile('/includes/layouts/progressBar.php', true);
  Route::includeFile('/regiweb/includes/layouts/scripts.php');
  ?>
</body>

</html>