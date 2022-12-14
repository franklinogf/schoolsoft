<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Teacher;

Session::is_logged();
$teachers = new Teacher();
$year = $teachers->info('year');
$lang = new Lang([
    ['Maestros', 'Teachers'],
    ['Maestro', 'Teacher'],
    ['Guardar', 'Save'],

    ["Buscar información", "Search information"],
    ["Agregar un maestro nuevo", "Add a new teacher"],

    ['Se ha agregado con éxito', "It has been successfully added"],
    ['Se ha actualizado con éxito', "It has been successfully updated"],
    ["Cambiar foto de perfil", "Change profile picture"],

    ["Información del maestro", "Teacher's information"],
    ["Número de cuenta", "Account number"],
    ["Usuario y contraseña", "Username and password"],
    ['Tipo', 'Type'],
    ['Docente', 'Teaching'],
    ['No docente', 'Non teaching'],


    ["Nombre", "Name"],
    ["Apellidos", "Surnames"],
    ["Alias", "Alias"],
    ["Género", "Gender"],
    ["Masculino", "Male"],
    ["Femenino", "Female"],
    ['Nivel', "Level"],
    ['Preescolar', 'Preschool'],
    ['Elemental', 'Elemental school'],
    ['Intermedia', 'Middle school'],
    ['Superior', 'Higher school'],
    ['Secundaria', 'Secondary school'],
    ["Fecha de nacimiento", "Date of Birth"],
    ['Fecha de inicio', 'Start date'],
    ['Fecha de baja', 'Discharge date'],
    ["Preparación", "Preparation"],

    ["Salón hogar", "Home classroom"],
    ["Departamento", "Departament"],
    ["Posición", "Position"],
    ['Teléfono residencial', 'Residential phone'],
    ['Teléfono emergencias', 'Emergency phone'],
    ['Celular', 'Cell'],
    ['Compañia telefonica', 'Cellphone company'],
    ['Correo electrónico', 'Email'],
    ["Recibir correo electrónico", "Receive Email"],
    ["Activo", "Active"],
    ["De baja", "Discharged"],
    ["Si", "Yes"],

    ["Dirección residencial", "Residential address"],
    ["Dirección postal", "Postal address"],
    ["Dirección", "Address"],
    ["Ciudad", "City"],
    ["Estado", "State"],
    ["Codigo Postal", "Postal Code"],
    ["Pasar dirección", "Pass address"],

    ['Licencias de los maestros', "Teacher's licences"],
    ['Información de los Clubes', 'Club information'],
    ['Nombre del club', 'Club name'],
    ['Presidente', 'President'],
    ['Vice presindente', 'Vice president'],
    ['Secretario(a)', 'Secretary'],


]);
$grades = DB::table("year")->select('DISTINCT grado')->where('year', $year)->orderBy('grado')->get();
$departments = DB::table("departamentos")->orderBy('codigo')->get();
if (Session::get('accountNumber')) {
    $_REQUEST['teacherId'] = Session::get('accountNumber', true);
}
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Maestros");
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>

</head>

<body class='pb-5'>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <form method="POST">
                    <select class="form-control selectpicker w-100" name="teacherId" data-live-search="true" required>
                        <option value=""><?= $lang->translation("Seleccionar") . ' ' . $lang->translation('maestro') ?></option>
                        <?php foreach ($teachers->All() as $teach) : ?>
                            <option <?= isset($_REQUEST['teacherId']) && $_REQUEST['teacherId'] == $teach->id ? 'selected=""' : '' ?> value="<?= $teach->id ?>"><?= "$teach->apellidos $teach->nombre ($teach->id)" ?></option>
                        <?php endforeach ?>
                    </select>
                    <button class="btn btn-primary btn-sm btn-block mt-2" type="submit"><?= $lang->translation("Buscar información") ?></button>
                </form>
                <form method="POST">
                    <button class="btn btn-outline-primary btn-sm btn-block mt-2" name="new" type="submit"><?= $lang->translation("Agregar un maestro nuevo") ?></button>
                </form>
                <?php if (Session::get("edited")) :
                    Session::delete('edited');
                ?>
                    <div class="d-flex align-items-center mt-2">
                        <div class="alert alert-info alert-dismissible fade show flex-fill" role="alert">
                            <?= $lang->translation("Se ha actualizado con éxito") ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (Session::get("added")) :
                    Session::delete('added');
                ?>
                    <div class="d-flex align-items-center mt-2">
                        <div class="alert alert-info alert-dismissible fade show flex-fill" role="alert">
                            <?= $lang->translation("Se ha agregado con éxito") ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
        <?php if (isset($_REQUEST['teacherId']) || isset($_POST['new'])) :
            if (isset($_REQUEST['teacherId'])) {
                $teacher = new Teacher($_REQUEST['teacherId']);
            } else {
                $nextId = DB::getNextIdFromTable('profesor');
            }

        ?>
            <form method="POST" action="<?= Route::url('/admin/users/teachers/includes/index.php') ?>" enctype="multipart/form-data">
                <div class="row mt-5">
                    <div class="col-12 mb-3">
                        <h1 class="text-center mt-3"><?= $lang->translation("Información del maestro") ?> <i class="far fa-id-card"></i></h1>
                    </div>
                    <div class="card col-12 col-md-6 p-3 rounded-0">
                        <div class="form-group row">
                            <label class="col-12 col-md-4" for="accountNumber"><?= $lang->translation("Número de cuenta") ?></label>
                            <div class="col-12 col-md-8">
                                <input type="text" value='<?= isset($_REQUEST['teacherId']) ? $teacher->id : $nextId ?>' class="form-control col" name='accountNumber' id="accountNumber" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-12 col-md-4" for="username"><?= $lang->translation("Usuario y contraseña") ?></label>
                            <div class="col-12 col-md-4">
                                <input type="text" value='<?= isset($_REQUEST['teacherId']) ? $teacher->usuario : "user$nextId" ?>' data-lastusername="<?= isset($_REQUEST['teacherId']) ? $teacher->usuario : "user$nextId" ?>" class="form-control" name='username' id="username" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <input type="text" value='<?= $teacher->clave ?>' class="form-control col" name='password' id="password" required>
                            </div>
                        </div>
                        <div class="form-group col-6">
                            <label for="type"><?= $lang->translation("Tipo") ?></label>
                            <select name="type" id="type" class="form-control" required>
                                <option value=""><?= $lang->translation('Seleccionar') ?></option>
                                <option <?= $teacher->docente === 'Docente' ? 'selected' : '' ?> value="Docente"><?= $lang->translation("Docente") ?></option>
                                <option <?= $teacher->docente === 'No Docente' ? 'selected' : '' ?> value="No Docente"><?= $lang->translation("No docente") ?></option>
                            </select>
                        </div>

                    </div>
                    <div class="col-12 col-md-6">
                        <img src="<?= isset($_REQUEST['teacherId']) ? $teacher->profilePicture() : __NO_PROFILE_PICTURE_TEACHER_MALE ?>" alt="Profile Picture" class="profile-picture img-thumbnail rounded mx-auto d-block" width="250" height="250">
                        <div class="form-group text-center mt-2">
                            <button id="pictureBtn" type='button' class="btn btn-secondary"><?= $lang->translation("Cambiar foto de perfil") ?></button>
                            <button id="pictureCancel" type='button' hidden class="btn btn-danger"><i class="fas fa-times"></i></button>
                            <input type="file" hidden name="picture" id="picture" accept="image/jpg,image/png,image/gif,image/jpeg">
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <!-- Information -->
                    <div class="card col-12 p-3 rounded-0">
                        <div class="row">
                            <div class="col-6">
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="name"><?= $lang->translation("Nombre") ?></label>
                                        <input type="text" value='<?= $teacher->nombre ?>' class="form-control" name='name' id="name" required>
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="surnames"><?= $lang->translation("Apellidos") ?></label>
                                        <input type="text" value='<?= $teacher->apellidos ?>' class="form-control" name='surnames' id="surnames" required>
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="alias"><?= $lang->translation("Alias") ?></label>
                                        <input type="text" value='<?= $teacher->alias ?>' class="form-control" name='alias' id="alias">
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="gender"><?= $lang->translation("Género") ?></label>
                                        <select name="gender" id="gender" class="form-control" required>
                                            <option value=""><?= $lang->translation('Seleccionar') ?></option>
                                            <option <?= $teacher->genero == "Masculino" ? 'selected' : '' ?> value="Masculino"><?= $lang->translation("Masculino") ?></option>
                                            <option <?= $teacher->genero == "Femenino" ? 'selected' : '' ?> value="Femenino"><?= $lang->translation("Femenino") ?></option>
                                        </select>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="dateOfBirth"><?= $lang->translation("Fecha de nacimiento") ?></label>
                                        <input type="date" class="form-control" name="dateOfBirth" id="dateOfBirth" required value="<?= $teacher->fecha_nac ?>">
                                    </div>
                                    <div class="form-group col-12 col-md-6">
                                        <label for="initialDate"><?= $lang->translation("Fecha de inicio") ?></label>
                                        <input type="date" class="form-control" name="initialDate" id="initialDate" value="<?= $teacher->fecha_ini ?>">
                                    </div>
                                    <div class="form-group col-12 col-md-6">
                                        <label for="finalDate"><?= $lang->translation("Fecha de baja") ?></label>
                                        <input type="date" class="form-control" name="finalDate" id="finalDate" value="<?= $teacher->fecha_daja ?>">
                                    </div>
                                    <div class="form-group col-12">
                                        <label><?= $lang->translation("Preparación") ?></label>
                                        <input type="text" class="form-control" name="preparation1" id="preparation1" value="<?= $teacher->preparacion1 ?>">
                                        <input type="text" class="form-control" name="preparation2" id="preparation2" value="<?= $teacher->preparacion2 ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="row">
                                    <div class="form-group col-6">
                                        <label for="level"><?= $lang->translation("Nivel") ?></label>
                                        <select name="level" id="level" class="form-control">
                                            <option value=""><?= $lang->translation('Seleccionar') ?></option>
                                            <option <?= $teacher->nivel === 'Pre-Escolar' ? 'selected' : '' ?> value="Pre-Escolar"><?= $lang->translation("Preescolar") ?></option>
                                            <option <?= $teacher->nivel === 'Elemental' ? 'selected' : '' ?> value="Elemental"><?= $lang->translation("Elemental") ?></option>
                                            <option <?= $teacher->nivel === 'Intermedia' ? 'selected' : '' ?> value="Intermedia"><?= $lang->translation("Intermedia") ?></option>
                                            <option <?= $teacher->nivel === 'Superior' ? 'selected' : '' ?> value="Superior"><?= $lang->translation("Superior") ?></option>
                                            <option <?= $teacher->nivel === 'Secundaria' ? 'selected' : '' ?> value="Secundaria"><?= $lang->translation("Secundaria") ?></option>
                                        </select>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="home"><?= $lang->translation("Salón hogar") ?></label>
                                        <select name="home" id="home" class="form-control">
                                            <option value=""><?= $lang->translation('Seleccionar') ?></option>
                                            <?php foreach ($grades as $grade) : ?>
                                                <option <?= $teacher->grado === $grade->grado ? 'selected=""' : '' ?> value="<?= $grade->grado ?>"><?= $grade->grado ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="department"><?= $lang->translation("Departamento") ?></label>
                                        <select name="department" id="department" class="form-control">
                                            <option value=""><?= $lang->translation('Seleccionar') ?></option>
                                            <option <?= $teacher->dep == '0' ? 'selected=""' : '' ?> value="0"><?= $lang->translation('Maestro') ?></option>
                                            <?php foreach ($departments as $department) : ?>
                                                <option <?= $teacher->dep === $department->codigo ? 'selected=""' : '' ?> value="<?= $department->codigo ?>"><?= $department->descripcion ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="position"><?= $lang->translation("Posición") ?></label>
                                        <input type="text" name="position" id="position" value="<?= $teacher->posicion ?>" class="form-control">
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="residentialPhone"><?= $lang->translation("Teléfono residencial") ?></label>
                                        <input type="tel" value='<?= $teacher->tel_res ?>' class="form-control phone" name='residentialPhone' id="residentialPhone">
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="emergencyPhone"><?= $lang->translation("Teléfono emergencias") ?></label>
                                        <input type="tel" value='<?= $teacher->tel_emer ?>' class="form-control phone" name='emergencyPhone' id="emergencyPhone">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="cellPhone"><?= $lang->translation("Celular") ?></label>
                                        <input type="tel" value="<?= $teacher->cel ?>" class="form-control phone" id="cellPhone" name="cellPhone" />
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="cellCompanyM"><?= $lang->translation("Compañia telefonica") ?></label>
                                        <select id="cellCompanyM" class="form-control" name="cellCompanyM">
                                            <option <?= $teacher->comp === '' ? 'selected=""' : '' ?> value=""><?= $lang->translation("Seleccionar") ?></option>
                                            <?php foreach (Util::phoneCompanies() as $company) : ?>
                                                <option <?= $teacher->comp === $company ? 'selected=""' : '' ?> value="<?= $company ?>"><?= $company ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-12">
                                        <label><?= $lang->translation("Correo electrónico") ?></label>
                                        <input type="email" value='<?= $teacher->email1 ?>' class="form-control" name='email1'>
                                        <input type="email" value='<?= $teacher->email2 ?>' class="form-control" name='email2'>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="receiveEmail"><?= $lang->translation("Recibir correo electrónico") ?></label>
                                        <select class="form-control" name="receiveEmail" id="receiveEmail">
                                            <option <?= $teacher->re_e === 'NO' ? 'selected=""' : '' ?> value="NO">No</option>
                                            <option <?= $teacher->re_e === 'SI' ? 'selected=""' : '' ?> value="SI"><?= $lang->translation("Si") ?></option>
                                        </select>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="active"><?= $lang->translation("Activo") ?></label>
                                        <select class="form-control" name="active" id="active">
                                            <option <?= $teacher->baja === '' ? 'selected=""' : '' ?> value=""><?= $lang->translation("Si") ?></option>
                                            <option <?= $teacher->baja === 'B' ? 'selected=""' : '' ?> value="B">De baja</option>
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- .Information -->
                    <div class="card col-12 p-3 rounded-0">
                        <div class="row">
                            <div class="col-12 col-lg-5">
                                <h5 class="card-subtitle text-center my-1"><?= $lang->translation("Dirección residencial") ?></h5>
                                <div class="form-row col-12">
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
                            </div>
                            <div class="col-12 col-lg-2 d-flex my-3 my-lg-0 justify-content-center">
                                <button id="passAddress" class="btn btn-outline-primary align-self-center"><?= $lang->translation("Pasar dirección") ?> <i class="fas fa-angle-double-right d-none d-lg-block"></i> <i class="fas fa-angle-double-down d-lg-none"></i></button>
                            </div>
                            <div class="col-12 col-lg-5 ">
                                <h5 class="card-subtitle text-center my-1"><?= $lang->translation("Dirección Postal") ?></h5>
                                <div class="form-row col-12">
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
                        </div>
                    </div>
                    <div class="col-12">
                        <h5 class="card-title text-center my-3"><?= $lang->translation("Licencias de los maestros") ?></h5>
                    </div>
                    <div class="card col-12 p-3 rounded-0">
                        <?php for ($i = 1; $i <= 4; $i++) : ?>
                            <div id="licences" class="row">
                                <div class="col-6">
                                    <input class="form-control" type="text" name="licence<?= $i ?>" value="<?= $teacher->{"lic$i"} ?>" />
                                </div>
                                <div class="col-2">
                                    <label for="expire<?= $i ?>">Exp.</label>
                                    <input <?= $teacher->{"lp$i"} ? 'checked' : '' ?> type="checkbox" name="expire<?= $i ?>" id="expire<?= $i ?>" value="Si">
                                </div>
                                <div class="col-4">
                                    <input class="form-control" type="date" name="expireDate<?= $i ?>" value="<?= $teacher->{"fex$i"} ?>" />
                                </div>
                            </div>
                        <?php endfor ?>

                    </div>
                    <div class="col-12">
                        <h5 class="card-title text-center my-3"><?= $lang->translation("Información de los Clubes") ?></h5>
                    </div>
                    <div class="card col-12 p-3 rounded-0">
                        <div class="row">
                            <div class="col-3">
                                <p class="text-center"><?= $lang->translation("Nombre del club") ?></p>
                            </div>
                            <div class="col-3">
                                <p class="text-center"><?= $lang->translation("Presidente") ?></p>
                            </div>
                            <div class="col-3">
                                <p class="text-center"><?= $lang->translation("Vice presindente") ?></p>
                            </div>
                            <div class="col-3">
                                <p class="text-center"><?= $lang->translation("Secretario(a)") ?></p>
                            </div>
                        </div>
                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                            <div id="club" class="row">
                                <div class="col-3">
                                    <input class="form-control" type="text" name="clubName<?= $i ?>" value="<?= $teacher->{"club$i"} ?>" />
                                </div>
                                <div class="col-3">
                                    <input class="form-control" type="text" name="clubPresident<?= $i ?>" value="<?= $teacher->{"pre$i"} ?>" />
                                </div>
                                <div class="col-3">
                                    <input class="form-control" type="text" name="clubVicePresident<?= $i ?>" value="<?= $teacher->{"vi$i"} ?>" />
                                </div>
                                <div class="col-3">
                                    <input class="form-control" type="text" name="clubSecretary<?= $i ?>" value="<?= $teacher->{"se$i"} ?>" />
                                </div>
                            </div>
                        <?php endfor ?>

                    </div>


                </div>
                <div class="col-12 text-center mt-2">
                    <button id="submit" class="btn btn-primary btn-block" name="<?= isset($_REQUEST['teacherId']) ? 'edit' : 'save' ?>" type="submit"><?= $lang->translation("Guardar") ?></button>
                </div>
            </form>



    </div>
<?php endif ?>

</div>


<?php
Route::includeFile('/includes/layouts/scripts.php', true);
Route::selectPicker('js');

?>

</body>

</html>