<?php
require_once __DIR__ . '/../../../app.php';

use App\Enums\AdminPermission;

use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use App\Models\Admin;
use App\Models\Teacher;

Session::is_logged();
$user = Admin::user(Session::id())->first();

if (!$user->hasPermissionTo(AdminPermission::USERS_TEACHERS)) {
    Route::forbidden();
}

$teachers = new Teacher();
$school = Admin::primaryAdmin();
$year = $school->year();

$grades = DB::table("year")->select('DISTINCT grado')->where(['year', $year])->orderBy('grado')->get();
$departments = DB::table("departamentos")->orderBy('codigo')->get();
if (Session::get('accountNumber')) {
    $_REQUEST['teacherId'] = Session::get('accountNumber', true);
}
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Maestros");
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
                        <option value="">
                            <?= __("Seleccionar") . ' ' . __('maestro') ?>
                        </option>
                        <?php foreach ($teachers->All() as $teach): ?>
                            <option <?= isset($_REQUEST['teacherId']) && $_REQUEST['teacherId'] == $teach->id ? 'selected=""' : '' ?> value="<?= $teach->id ?>">
                                <?= "$teach->apellidos $teach->nombre ($teach->id)" ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                    <button class="btn btn-primary btn-sm btn-block mt-2" type="submit">
                        <?= __("Buscar información") ?>
                    </button>
                </form>
                <?php if ($user->hasPermissionTo(AdminPermission::USERS_TEACHERS_ADD)): ?>
                    <form method="POST">
                        <button class="btn btn-outline-primary btn-sm btn-block mt-2" name="new" type="submit">
                            <?= __("Agregar un maestro nuevo") ?>
                        </button>
                    </form>
                <?php endif; ?>
                <?php if (Session::get("edited")):
                    Session::delete('edited');
                ?>
                    <div class="d-flex align-items-center mt-2">
                        <div class="alert alert-info alert-dismissible fade show flex-fill" role="alert">
                            <?= __("Se ha actualizado con éxito") ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (Session::get("added")):
                    Session::delete('added');
                ?>
                    <div class="d-flex align-items-center mt-2">
                        <div class="alert alert-info alert-dismissible fade show flex-fill" role="alert">
                            <?= __("Se ha agregado con éxito") ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
        <?php if (isset($_REQUEST['teacherId']) || isset($_POST['new'])):
            if (isset($_REQUEST['teacherId'])) {
                $teacher = Teacher::find($_REQUEST['teacherId']);
            } else {
                $nextId = DB::getNextIdFromTable('profesor');
            }

        ?>
            <form method="POST" action="<?= Route::url('/admin/users/teachers/includes/index.php') ?>" enctype="multipart/form-data">
                <div class="row mt-5">
                    <div class="col-12 mb-3">
                        <h1 class="text-center mt-3">
                            <?= __("Información del maestro") ?> <i class="far fa-id-card"></i>
                        </h1>
                    </div>
                    <div class="card col-12 col-md-6 p-3 rounded-0">
                        <div class="form-group row">
                            <label class="col-12 col-md-4" for="accountNumber">
                                <?= __("Número de cuenta") ?>
                            </label>
                            <div class="col-12 col-md-8">
                                <input type="text" value='<?= isset($_REQUEST['teacherId']) ? $teacher->id : $nextId ?>' class="form-control col" name='accountNumber' id="accountNumber" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-12 col-md-4" for="username">
                                <?= __("Usuario y contraseña") ?>
                            </label>
                            <div class="col-12 col-md-4">
                                <input type="text" value='<?= isset($_REQUEST['teacherId']) ? $teacher->usuario : "user$nextId" ?>' data-lastusername="<?= isset($_REQUEST['teacherId']) ? $teacher->usuario : "user$nextId" ?>" class="form-control" name='username' id="username" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <input type="text" value='<?= isset($_REQUEST['teacherId']) ? $teacher->clave : '' ?>' class="form-control col" name='password' id="password" required>
                            </div>
                        </div>
                        <div class="form-group col-6">
                            <label for="type">
                                <?= __("Tipo") ?>
                            </label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="">
                                    <?= __('Seleccionar') ?>
                                </option>
                                <option <?= isset($_REQUEST['teacherId']) && $teacher->docente === 'Docente' ? 'selected' : '' ?> value="Docente">
                                    <?= __("Docente") ?>
                                </option>
                                <option <?= isset($_REQUEST['teacherId']) && $teacher->docente === 'No Docente' ? 'selected' : '' ?> value="No Docente">
                                    <?= __("No docente") ?>
                                </option>
                            </select>
                        </div>

                    </div>
                    <div class="col-12 col-md-6">
                        <img src="<?= isset($_REQUEST['teacherId']) ? $teacher->profile_picture : __NO_PROFILE_PICTURE_TEACHER_MALE ?>" alt="Profile Picture" class="profile-picture img-thumbnail rounded mx-auto d-block" width="250" height="250">
                        <div class="form-group text-center mt-2">
                            <button id="pictureBtn" type='button' class="btn btn-secondary">
                                <?= __("Cambiar foto de perfil") ?>
                            </button>
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
                                        <label for="name">
                                            <?= __("Nombre") ?>
                                        </label>
                                        <input type="text" value='<?= isset($_REQUEST['teacherId']) ? $teacher->nombre : '' ?>' class="form-control" name='name' id="name" required>
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="surnames">
                                            <?= __("Apellidos") ?>
                                        </label>
                                        <input type="text" value='<?= isset($_REQUEST['teacherId']) ? $teacher->apellidos : '' ?>' class="form-control" name='surnames' id="surnames" required>
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="alias">
                                            <?= __("Alias") ?>
                                        </label>
                                        <input type="text" value='<?= isset($_REQUEST['teacherId']) ? $teacher->alias : '' ?>' class="form-control" name='alias' id="alias">
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="gender">
                                            <?= __("Género") ?>
                                        </label>
                                        <select name="gender" id="gender" class="form-control" required>
                                            <option value="">
                                                <?= __('Seleccionar') ?>
                                            </option>
                                            <option <?= isset($_REQUEST['teacherId']) && $teacher->genero == "Masculino" ? 'selected' : '' ?> value="Masculino">
                                                <?= __("Masculino") ?>
                                            </option>
                                            <option <?= isset($_REQUEST['teacherId']) && $teacher->genero == "Femenino" ? 'selected' : '' ?> value="Femenino">
                                                <?= __("Femenino") ?>
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="dateOfBirth">
                                            <?= __("Fecha de nacimiento") ?>
                                        </label>
                                        <input type="date" class="form-control" name="dateOfBirth" id="dateOfBirth" required value="<?= isset($_REQUEST['teacherId']) ? $teacher->fecha_nac : '' ?>">
                                    </div>
                                    <div class="form-group col-12 col-md-6">
                                        <label for="initialDate">
                                            <?= __("Fecha de inicio") ?>
                                        </label>
                                        <input type="date" class="form-control" name="initialDate" id="initialDate" value="<?= isset($_REQUEST['teacherId']) ? $teacher->fecha_ini : '' ?>">
                                    </div>
                                    <div class="form-group col-12 col-md-6">
                                        <label for="finalDate">
                                            <?= __("Fecha de baja") ?>
                                        </label>
                                        <input type="date" class="form-control" name="finalDate" id="finalDate" value="<?= isset($_REQUEST['teacherId']) ? $teacher->fecha_daja : '' ?>">
                                    </div>
                                    <div class="form-group col-12">
                                        <label>
                                            <?= __("Preparación") ?>
                                        </label>
                                        <input type="text" class="form-control" name="preparation1" id="preparation1" value="<?= isset($_REQUEST['teacherId']) ? $teacher->preparacion1 : '' ?>">
                                        <input type="text" class="form-control" name="preparation2" id="preparation2" value="<?= isset($_REQUEST['teacherId']) ? $teacher->preparacion2 : '' ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="row">
                                    <div class="form-group col-6">
                                        <label for="level">
                                            <?= __("Nivel") ?>
                                        </label>
                                        <select name="level" id="level" class="form-control">
                                            <option value="">
                                                <?= __('Seleccionar') ?>
                                            </option>
                                            <option <?= isset($_REQUEST['teacherId']) && $teacher->nivel === 'Pre-Escolar' ? 'selected' : '' ?> value="Pre-Escolar">
                                                <?= __("Preescolar") ?>
                                            </option>
                                            <option <?= isset($_REQUEST['teacherId']) && $teacher->nivel === 'Elemental' ? 'selected' : '' ?> value="Elemental">
                                                <?= __("Elemental") ?>
                                            </option>
                                            <option <?= isset($_REQUEST['teacherId']) && $teacher->nivel === 'Intermedia' ? 'selected' : '' ?> value="Intermedia">
                                                <?= __("Intermedia") ?>
                                            </option>
                                            <option <?= isset($_REQUEST['teacherId']) && $teacher->nivel === 'Superior' ? 'selected' : '' ?> value="Superior">
                                                <?= __("Superior") ?>
                                            </option>
                                            <option <?= isset($_REQUEST['teacherId']) && $teacher->nivel === 'Secundaria' ? 'selected' : '' ?> value="Secundaria">
                                                <?= __("Secundaria") ?>
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="home">
                                            <?= __("Salón hogar") ?>
                                        </label>
                                        <select name="home" id="home" class="form-control">
                                            <option value="">
                                                <?= __('Seleccionar') ?>
                                            </option>
                                            <?php foreach ($grades as $grade): ?>
                                                <option <?= isset($_REQUEST['teacherId']) && $teacher->grado === $grade->grado ? 'selected=""' : '' ?> value="<?= $grade->grado ?>">
                                                    <?= $grade->grado ?>
                                                </option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="department">
                                            <?= __("Departamento") ?>
                                        </label>
                                        <select name="department" id="department" class="form-control">
                                            <option value="">
                                                <?= __('Seleccionar') ?>
                                            </option>
                                            <option <?= isset($_REQUEST['teacherId']) && $teacher->dep == '0' ? 'selected=""' : '' ?> value="0">
                                                <?= __('Maestro') ?>
                                            </option>
                                            <?php foreach ($departments as $department): ?>
                                                <option <?= isset($_REQUEST['teacherId']) && $teacher->dep === $department->codigo ? 'selected=""' : '' ?> value="<?= $department->codigo ?>">
                                                    <?= $department->descripcion ?>
                                                </option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="position">
                                            <?= __("Posición") ?>
                                        </label>
                                        <input type="text" name="position" id="position" value="<?= isset($_REQUEST['teacherId']) ? $teacher->posicion : '' ?>" class="form-control">
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="residentialPhone">
                                            <?= __("Teléfono residencial") ?>
                                        </label>
                                        <input type="tel" value='<?= isset($_REQUEST['teacherId']) ? $teacher->tel_res : '' ?>' class="form-control phone" name='residentialPhone' id="residentialPhone">
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="emergencyPhone">
                                            <?= __("Teléfono emergencias") ?>
                                        </label>
                                        <input type="tel" value='<?= isset($_REQUEST['teacherId']) ? $teacher->tel_emer : '' ?>' class="form-control phone" name='emergencyPhone' id="emergencyPhone">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="cellPhone">
                                            <?= __("Celular") ?>
                                        </label>
                                        <input type="tel" value="<?= isset($_REQUEST['teacherId']) ? $teacher->cel : '' ?>" class="form-control phone" id="cellPhone" name="cellPhone" />
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="cellCompanyM">
                                            <?= __("Compañia telefonica") ?>
                                        </label>
                                        <select id="cellCompanyM" class="form-control" name="cellCompanyM">
                                            <option <?= isset($_REQUEST['teacherId']) && $teacher->comp === '' ? 'selected=""' : '' ?> value="">
                                                <?= __("Seleccionar") ?>
                                            </option>
                                            <?php foreach (Util::phoneCompanies() as $company): ?>
                                                <option <?= isset($_REQUEST['teacherId']) && $teacher->comp === $company ? 'selected=""' : '' ?> value="<?= $company ?>">
                                                    <?= $company ?>
                                                </option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-12">
                                        <label>
                                            <?= __("Correo electrónico") ?>
                                        </label>
                                        <input type="email" value='<?= isset($_REQUEST['teacherId']) ? $teacher->email1 : '' ?>' class="form-control" name='email1'>
                                        <input type="email" value='<?= isset($_REQUEST['teacherId']) ? $teacher->email2 : '' ?>' class="form-control" name='email2'>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="receiveEmail">
                                            <?= __("Recibir correo electrónico") ?>
                                        </label>
                                        <select class="form-control" name="receiveEmail" id="receiveEmail">
                                            <option <?= isset($_REQUEST['teacherId']) && $teacher->re_e === 'NO' ? 'selected=""' : '' ?> value="NO">No</option>
                                            <option <?= isset($_REQUEST['teacherId']) && $teacher->re_e === 'SI' ? 'selected=""' : '' ?> value="SI">
                                                <?= __("Si") ?>
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="active">
                                            <?= __("Activo") ?>
                                        </label>
                                        <select class="form-control" name="active" id="active">
                                            <option <?= isset($_REQUEST['teacherId']) && $teacher->baja === '' ? 'selected=""' : '' ?> value="">
                                                <?= __("Si") ?>
                                            </option>
                                            <option <?= isset($_REQUEST['teacherId']) && $teacher->baja === 'B' ? 'selected=""' : '' ?> value="B">De baja</option>
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
                                <h5 class="card-subtitle text-center my-1">
                                    <?= __("Dirección residencial") ?>
                                </h5>
                                <div class="form-row col-12">
                                    <div class="col-12">
                                        <input class="form-control" type="text" placeholder="<?= __("Dirección") ?> 1" name="dir1" id="dir1" value="<?= isset($_REQUEST['teacherId']) ? $teacher->dir1 : '' ?>" />
                                    </div>
                                    <div class="col-12 mt-1">
                                        <input class="form-control" type="text" placeholder="<?= __("Dirección") ?> 2" name="dir2" id="dir2" value="<?= isset($_REQUEST['teacherId']) ? $teacher->dir2 : '' ?>" />
                                    </div>
                                    <div class="col-5 mt-1">
                                        <input class="form-control" type="text" placeholder="<?= __("Ciudad") ?>" name="city1" id="city1" value="<?= isset($_REQUEST['teacherId']) ? $teacher->pueblo1 : '' ?>" />
                                    </div>
                                    <div class="col-3 mt-1">
                                        <input class="form-control" type="text" placeholder="<?= __("Estado") ?>" name="state1" id="state1" value="<?= isset($_REQUEST['teacherId']) ? $teacher->esta1 : '' ?>" />
                                    </div>
                                    <div class="col-4 mt-1">
                                        <input class="form-control" type="text" placeholder="<?= __("Codigo Postal") ?>" name="zip1" id="zip1" value="<?= isset($_REQUEST['teacherId']) ? $teacher->zip1 : '' ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-2 d-flex my-3 my-lg-0 justify-content-center">
                                <button id="passAddress" class="btn btn-outline-primary align-self-center">
                                    <?= __("Pasar dirección") ?> <i class="fas fa-angle-double-right d-none d-lg-block"></i> <i class="fas fa-angle-double-down d-lg-none"></i>
                                </button>
                            </div>
                            <div class="col-12 col-lg-5 ">
                                <h5 class="card-subtitle text-center my-1">
                                    <?= __("Dirección Postal") ?>
                                </h5>
                                <div class="form-row col-12">
                                    <div class="col-12">
                                        <input class="form-control" type="text" placeholder="<?= __("Dirección") ?> 1" name="dir3" id="dir3" value="<?= isset($_REQUEST['teacherId']) ? $teacher->dir3 : '' ?>" />
                                    </div>
                                    <div class="col-12 mt-1">
                                        <input class="form-control" type="text" placeholder="<?= __("Dirección") ?> 2" name="dir4" id="dir4" value="<?= isset($_REQUEST['teacherId']) ? $teacher->dir4 : '' ?>" />
                                    </div>
                                    <div class="col-5 mt-1">
                                        <input class="form-control" type="text" placeholder="<?= __("Ciudad") ?>" name="city2" id="city2" value="<?= isset($_REQUEST['teacherId']) ? $teacher->pueblo2 : '' ?>" />
                                    </div>
                                    <div class="col-3 mt-1">
                                        <input class="form-control" type="text" placeholder="<?= __("Estado") ?>" name="state2" id="state2" value="<?= isset($_REQUEST['teacherId']) ? $teacher->esta2 : '' ?>" />
                                    </div>
                                    <div class="col-4 mt-1">
                                        <input class="form-control" type="text" placeholder="<?= __("Codigo Postal") ?>" name="zip2" id="zip2" value="<?= isset($_REQUEST['teacherId']) ? $teacher->zip2 : '' ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <h5 class="card-title text-center my-3">
                            <?= __("Licencias de los maestros") ?>
                        </h5>
                    </div>
                    <div class="card col-12 p-3 rounded-0">
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                            <div id="licences" class="row">
                                <div class="col-6">
                                    <input class="form-control" type="text" name="licence<?= $i ?>" value="<?= isset($_REQUEST['teacherId']) ? $teacher->{"lic$i"} : '' ?>" />
                                </div>
                                <div class="col-2">
                                    <label for="expire<?= $i ?>">Exp.</label>
                                    <input class="expire" <?= isset($_REQUEST['teacherId']) && $teacher->{"lp$i"} ? 'checked' : '' ?> type="checkbox" name="expire<?= $i ?>" id="expire<?= $i ?>" value="Si">
                                </div>
                                <div class="col-4">
                                    <input class="form-control expireDate" type="date" name="expireDate<?= $i ?>" value="<?= isset($_REQUEST['teacherId']) ? $teacher->{"fex$i"} : '' ?>" <?= isset($_REQUEST['teacherId']) && $teacher->{"lp$i"} === 'Si' ? '' : 'disabled' ?> />
                                </div>
                            </div>
                        <?php endfor ?>

                    </div>
                    <div class="col-12">
                        <h5 class="card-title text-center my-3">
                            <?= __("Información de los Clubes") ?>
                        </h5>
                    </div>
                    <div class="card col-12 p-3 rounded-0">
                        <div class="row">
                            <div class="col-3">
                                <p class="text-center">
                                    <?= __("Nombre del club") ?>
                                </p>
                            </div>
                            <div class="col-3">
                                <p class="text-center">
                                    <?= __("Presidente") ?>
                                </p>
                            </div>
                            <div class="col-3">
                                <p class="text-center">
                                    <?= __("Vice presindente") ?>
                                </p>
                            </div>
                            <div class="col-3">
                                <p class="text-center">
                                    <?= __("Secretario(a)") ?>
                                </p>
                            </div>
                        </div>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <div id="club" class="row">
                                <div class="col-3">
                                    <input class="form-control" type="text" name="clubName<?= $i ?>" value="<?= isset($_REQUEST['teacherId']) ? $teacher->{"club$i"} : '' ?>" />
                                </div>
                                <div class="col-3">
                                    <input class="form-control" type="text" name="clubPresident<?= $i ?>" value="<?= isset($_REQUEST['teacherId']) ? $teacher->{"pre$i"} : '' ?>" />
                                </div>
                                <div class="col-3">
                                    <input class="form-control" type="text" name="clubVicePresident<?= $i ?>" value="<?= isset($_REQUEST['teacherId']) ? $teacher->{"vi$i"} : '' ?>" />
                                </div>
                                <div class="col-3">
                                    <input class="form-control" type="text" name="clubSecretary<?= $i ?>" value="<?= isset($_REQUEST['teacherId']) ? $teacher->{"se$i"} : '' ?>" />
                                </div>
                            </div>
                        <?php endfor ?>

                    </div>


                </div>
                <div class="col-12 text-center mt-2">
                    <?php if ($user->hasPermissionTo(AdminPermission::USERS_TEACHERS_EDIT) && isset($_REQUEST['teacherId'])): ?>
                        <button id="submit" class="btn btn-primary btn-block" name="edit" type="submit"><?= __("Guardar") ?></button>
                    <?php endif ?>
                    <?php if ($user->hasPermissionTo(AdminPermission::USERS_TEACHERS_ADD) && !isset($_REQUEST['teacherId'])): ?>
                        <button id="submit" class="btn btn-primary btn-block" name="save" type="submit"><?= __("Guardar") ?></button>
                    <?php endif ?>

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