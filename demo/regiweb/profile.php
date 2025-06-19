<?php
require_once '../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;

Session::is_logged();

$teacher = new Teacher(Session::id());
$lang = new Lang([
  ["Mi Perfil", "My profile"],
  ["Información importante", "Important information"],
  ["Usuario:", "User:"],
  ["SI", "YES"],
  ["Correo electrónico principal", "Primary email"],
  ["Correo electrónico secundario", "Secondary email"],
  ["Teléfono residencial", "Residential telephone"],
  ["Celular", "Cellphone"],
  ["Compañia telefonica", "Cellphone company"],
  ["Dirección Residencial", "Residential Address"],
  ["Dirección Postal", "Postal Address"],
  ["Dirección", "Address"],
  ["Direcciones", "Addresses"],
  ["Ciudad", "City"],
  ["Estado", "State"],
  ["Codigo Postal", "Postal Code"],
  ["Posición", "Position"],
  ["Teléfono de emergencia", "Emergency phone"],
  ["Nueva contraseña", "New Password"],
  ["Confirmar contraseña", "Confirm Password"],
  ["Las contraseñas no coinciden", "Passwords do not match"],
  ["Salon hogar:", "Home grade:"],
  ["Fecha de nacimiento:", "Date of Birth:"],
  ["Guardar", "Save"],
  ["Cambiar foto", "Change picture"],
  ["Total de estudiantes:", "Total number of students:"],
  ["Genero", "Gender"],
  ["Masculino", "Male"],
  ["Femenino", "Female"],
  ["Información personal", "Personal information"],
  ["Nombre", "Name"],
  ["Apellidos", "Surnames"],
  ["Misma dirección que la residencial", "Same address as the residential"],
  ["Otras", "Others"],
  ["Alias", "Alias"],
  ["Nivel", "Level"],
  ["Elemental", "Elemental School"],
  ["Preescolar", "Preschool"],
  ["Intermedia", "Middle School"],
  ["Superior", "High School"],
  ["Preparación", "Preparation"],
  ["Fecha de inicio","Start date"],
  ["Fecha de baja","End date"],
  ["Recibir correos electronicos","Receive email"],
  ["Clubes","Clubs"],
  ["Presidente","President"],
  ["Vice Presidente","Vice president"],
  ["Secretario(a)","Secretary"],
  ["Licencias","Licenses"]
  
]);

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
  <?php
  $title = $lang->translation("Mi Perfil");
  Route::includeFile('/regiweb/includes/layouts/header.php');
  ?>
</head>

<body class='pb-5'>
  <?php
  Route::includeFile('/regiweb/includes/layouts/menu.php');
  ?>
  <div class="container mt-5">
    <h1 class="text-center"><?= $lang->translation("Mi Perfil") ?> <i class="far fa-id-card"></i></h1>
    <form action="<?= Route::url('/regiweb/includes/profiles.php') ?>" method="POST" enctype="multipart/form-data">
      <div class="row mt-5">
        <div class="col-lg-6 col-sm-12">
          <input type="hidden" name="id_teacher" value="<?= $teacher->id ?>">
          <img src="<?= $teacher->profilePicture() ?>" alt="Profile Picture" class="profile-picture img-thumbnail rounded mx-auto d-block" width="250" height="250">
          <div class="form-group text-center mt-2">
            <button id="pictureBtn" type='button' class="btn btn-secondary"><?= $lang->translation("Cambiar foto") ?></button>
            <button id="pictureCancel" type='button' hidden class="btn btn-danger"><i class="fas fa-times"></i></button>
            <input type="file" hidden name="picture" id="picture" accept="image/jpg,image/png,image/gif,image/jpeg">
          </div>
        </div>
        <div class="offset-lg-2 col-lg-4 col-sm-12 mt-sm-3">
          <hr class="d-lg-none d-sm-block" />
          <div class="card border-info">
            <h6 class="card-header bg-gradient-info bg-info">
              <?= $lang->translation("Información importante") ?>
            </h6>
            <div class="card-body">
              <p class="text-monospace">ID: <span class="badge badge-info"><?= $teacher->id ?> </span></p>
              <p class="text-monospace"><?= $lang->translation("Usuario:") ?> <span class="badge badge-info"><?= $teacher->usuario ?></span></p>
              <p class="text-monospace"><?= $lang->translation("Salon Hogar:") ?> <span class="badge badge-info"><?= $teacher->grado ?></span></p>
              <p class="text-monospace"><?= $lang->translation("Total de estudiantes:") ?> <span class="badge badge-info"><?= sizeof($teacher->homeStudents()) ?></span></p>
            </div>
          </div>
        </div>
      </div>
      <hr class="mb-3" />
      <div class="row">
        <!-- Personal Information -->
        <div class="card col-12 col-lg-6 p-3">
          <h5 class="card-title"><?= $lang->translation("Información Personal") ?></h5>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="name"><?= $lang->translation("Nombre") ?></label>
              <input type="text" value='<?= $teacher->nombre ?>' class="form-control" name='name' id="name" required>
            </div>
            <div class="form-group col-md-6">
              <label for="lastname"><?= $lang->translation("Apellidos") ?></label>
              <input type="text" value='<?= $teacher->apellidos ?>' class="form-control" name='lastName' id="lastname" required>
            </div>
            <div class="form-group col-6">
              <label for="gender"><?= $lang->translation("Genero") ?></label>
              <select class="form-control" value="<?= $teacher->genero ?>" name="gender" id="gender">
                <option value="Femenino"><?= $lang->translation("Femenino") ?></option>
                <option value="Masculino"><?= $lang->translation("Masculino") ?></option>
              </select>
            </div>
            <div class="form-group col-6">
              <label for="dob"><?= $lang->translation("Fecha de nacimiento") ?></label>
              <input class="form-control" type="date" name="dob" id="dob" value="<?= $teacher->fecha_nac ?>" />
            </div>
            <div class="form-group col-12">
              <label for="email1"><?= $lang->translation("Correo electrónico principal") ?></label>
              <input type="email" value='<?= $teacher->email1 ?>' class="form-control" name='email1' id="email1">
              <label for="email2"><?= $lang->translation("Correo electrónico secundario") ?></label>
              <input type="email" value='<?= $teacher->email2 ?>' class="form-control" name='email2' id="email2">
            </div>
            <div class="form-group col-12">
              <label for="residentialPhone"><?= $lang->translation("Teléfono residencial") ?></label>
              <input type="tel" value='<?= $teacher->tel_res ?>' class="form-control" name='residentialPhone' id="residentialPhone" pattern="[0-9]{10}">
              <label for="emergencyPhone"><?= $lang->translation("Teléfono de emergencia") ?></label>
              <input type="tel" value='<?= $teacher->tel_emer ?>' class="form-control" name='emergencyPhone' id="emergencyPhone" pattern="[0-9]{10}">
            </div>
            <div class="form-group col-md-6">
              <label for="cellPhone"><?= $lang->translation("Celular") ?></label>
              <input type="tel" value="<?= $teacher->cel ?>" class="form-control" id="cellPhone" name="cellPhone" pattern="[0-9]{10}" />
            </div>
            <div class="form-group col-md-6">
              <label for="cellCompany"><?= $lang->translation("Compañia telefonica") ?></label>
              <select id="cellCompany" class="form-control" name="cellCompany">
                <option <?= $teacher->comp === '' ? 'selected=""' : '' ?> value=""><?= $lang->translation("Selecionar") ?></option>
                <?php foreach (Util::phoneCompanies() as $company) : ?>
                  <option <?= $teacher->comp === $company ? 'selected=""' : '' ?> value="<?= $company ?>"><?= $company ?></option>
                <?php endforeach ?>
              </select>
            </div>
            <div class="form-group  col-md-6">
              <label for="pass1"><?= $lang->translation("Nueva contraseña") ?></label>
              <input type="password" class="form-control pass" name='password' id="pass1">
              <label for="pass2"><?= $lang->translation("Confirmar contraseña") ?></label>
              <input type="password" class="form-control pass" id="pass2">
              <div class="invalid-feedback"><?= $lang->translation("Las contraseñas no coinciden") ?></div>
            </div>
          </div>
        </div>
        <!-- .Personal Information -->
        <div class="col-12 offset-lg-1 col-lg-5 p-0">
          <!-- Address -->
          <div class="card col-12 p-3 mt-2 mt-lg-0">
            <h5 class="card-title mb-1"><?= $lang->translation("Direcciones") ?></h5>
            <h6 class="card-subtitle mb-2 mt-2"><?= $lang->translation("Dirección Residencial") ?></h6>
            <div class="form-row">
              <div class="col-12">
                <input class="form-control" type="text" placeholder="<?= $lang->translation("Dirección") ?> 1" name="dir1" id="dir1" value="<?= $teacher->dir1 ?>" />
              </div>
              <div class="col-12 mt-1">
                <input class="form-control" type="text" placeholder="<?= $lang->translation("Dirección") ?> 2" name="dir2" id="dir2" value="<?= $teacher->dir2 ?>" />
              </div>
              <div class="col-5 mt-1">
                <input class="form-control" type="text" placeholder="<?= $lang->translation("Ciudad") ?>" name="city1" id="city1" value="<?= $teacher->pueblo1 ?>" />
              </div>
              <div class="col-3 mt-1">
                <input class="form-control" type="text" placeholder="<?= $lang->translation("Estado") ?>" name="state1" id="state1" value="<?= $teacher->esta1 ?>" />
              </div>
              <div class="col-4 mt-1">
                <input class="form-control" type="text" placeholder="<?= $lang->translation("Codigo Postal") ?>" name="zip1" id="zip1" value="<?= $teacher->zip1 ?>" />
              </div>
            </div>
            <hr />
            <h6 class="card-subtitle mb-2 mt-2"><?= $lang->translation("Dirección Postal") ?></h6>
            <div class="custom-control custom-check mb-2">
              <input class="custom-control-input" type="checkbox" id="sameDirection">
              <label class="custom-control-label" for="sameDirection">
                <?= $lang->translation("Misma dirección que la residencial") ?>
              </label>
            </div>
            <div class="form-row">
              <div class="col-12">
                <input class="form-control" type="text" placeholder="<?= $lang->translation("Dirección") ?> 1" name="dir3" id="dir3" value="<?= $teacher->dir3 ?>" />
              </div>
              <div class="col-12 mt-1">
                <input class="form-control" type="text" placeholder="<?= $lang->translation("Dirección") ?> 2" name="dir4" id="dir4" value="<?= $teacher->dir4 ?>" />
              </div>
              <div class="col-5 mt-1">
                <input class="form-control" type="text" placeholder="<?= $lang->translation("Ciudad") ?>" name="city2" id="city2" value="<?= $teacher->pueblo2 ?>" />
              </div>
              <div class="col-3 mt-1">
                <input class="form-control" type="text" placeholder="<?= $lang->translation("Estado") ?>" name="state2" id="state2" value="<?= $teacher->esta2 ?>" />
              </div>
              <div class="col-4 mt-1">
                <input class="form-control" type="text" placeholder="<?= $lang->translation("Codigo Postal") ?>" name="zip2" id="zip2" value="<?= $teacher->zip2 ?>" />
              </div>
            </div>
          </div>
          <!-- .Address -->
          <!-- Other Information -->
          <div class="card col-12 p-3 mt-2">
            <h5 class="card-title mb-1"><?= $lang->translation("Otras") ?></h5>
            <div class="form-row">
              <div class="form-group col-12">
                <label for="alias"><?= $lang->translation("Alias") ?></label>
                <input class="form-control" type="text" name="alias" id="alias" value="<?= $teacher->alias ?>" />
              </div>
              <div class="form-group col-6">
                <label for="position"><?= $lang->translation("Posición") ?></label>
                <input class="form-control" type="text" name="position" id="position" value="<?= $teacher->posicion ?>" />
              </div>
              <div class="form-group col-6">
                <label for="level"><?= $lang->translation("Nivel") ?></label>
                <select class="form-control" value="<?= $teacher->nivel ?>" name="level" id="level">
                  <option value="Elemental"><?= $lang->translation("Elemental") ?></option>
                  <option value="Pre-Escolar"><?= $lang->translation("Pre escolar") ?></option>
                  <option value="Intermedia"><?= $lang->translation("Intermedia") ?></option>
                  <option value="Superior"><?= $lang->translation("Superior") ?></option>
                </select>
              </div>
              <div class="form-group col-12">
                <label for="preparation"><?= $lang->translation("Preparación") ?></label>
                <input class="form-control" type="text" name="preparation1" id="preparation1" value="<?= $teacher->preparacion1 ?>" />
              </div>
              <div class="form-group col-12">
                <input class="form-control" type="text" name="preparation2" id="preparation2" value="<?= $teacher->preparacion2 ?>" />
              </div>
              <div class="col-12">
                <hr />
              </div>
              <div class="form-group col-6">
                <label for="initialDate"><?= $lang->translation("Fecha de inicio") ?></label>
                <input class="form-control" type="date" name="initialDate" id="initialDate" value="<?= $teacher->fecha_ini ?>" />
              </div>
              <div class="form-group col-6">
                <label for="dischargeDate"><?= $lang->translation("Fecha de baja") ?></label>
                <input class="form-control" type="date" name="dischargeDate" id="dischargeDate" value="<?= $teacher->fecha_daja ?>" />
              </div>
              <div class="form-group col-12 row">
                <label class="col-6 col-form-label" for="getEmails"><?= $lang->translation("Recibir correos electronicos") ?></label>
                <select class="form-control col-6" value="<?= $teacher->re_e ?>" name="getEmails" id="getEmails">
                  <option value="NO">NO</option>
                  <option value="SI"><?= $lang->translation("SI") ?></option>
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
                      <label for="<?= "club$i" ?>"><?= $lang->translation("Nombre") ?></label>
                      <input class="form-control" type="text" name="<?= "club$i" ?>" id="<?= "club$i" ?>" value="<?= $teacher->{"club$i"} ?>" />
                    </div>
                    <div class="form-group col-3">
                      <label for="<?= "pre$i" ?>"><?= $lang->translation("Presidente") ?></label>
                      <input class="form-control" type="text" name="<?= "pre$i" ?>" id="<?= "pre$i" ?>" value="<?= $teacher->{"pre$i"} ?>" />
                    </div>
                    <div class="form-group col-3">
                      <label for="<?= "vi$i" ?>"><?= $lang->translation("Vice Presidente") ?></label>
                      <input class="form-control" type="text" name="<?= "vi$i" ?>" id="<?= "vi$i" ?>" value="<?= $teacher->{"vi$i"} ?>" />
                    </div>
                    <div class="form-group col-3">
                      <label for="<?= "se$i" ?>"><?= $lang->translation("Secretario(a)") ?></label>
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
                <?= $lang->translation("Licencias") ?>
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