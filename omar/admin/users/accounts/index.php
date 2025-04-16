<?php
require_once '../../../app.php';


use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use App\Models\Admin;
use App\Models\Family;
use App\Models\Student;

Session::is_logged();


$school = Admin::user(Session::id())->first();
$students = Student::all();
$year = $school->year();
$female = $male = 0;
foreach ($students as $student) {
    if (strtoupper($student->genero) === 'F' || $student->genero === '1') {
        $female++;
    } else {
        $male++;
    }
}

$familiesCount = Student::distinct()->select('id')->count();

if (Session::get('accountNumber')) {
    $_REQUEST['student'] = Session::get('accountNumber', true);
}
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Cuentas");
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
            <div class="col-lg-4 col-sm-12">
                <hr class="d-lg-none d-sm-block" />
                <div class="card border-info">
                    <div class="card-body">
                        <p class="text-monospace"><?= __("Total de estudiantes") ?>: <span class="badge badge-info"><?= sizeof($students) ?></span></p>
                        <p class="text-monospace"><?= __("Femeninas") ?>: <span class="badge badge-info"><?= $female ?></span></p>
                        <p class="text-monospace"><?= __("Masculinos") ?>: <span class="badge badge-info"><?= $male ?></span></p>
                        <p class="text-monospace"><?= __("Total de familias") ?>: <span class="badge badge-info"><?= $familiesCount ?></span></p>
                        <p class="text-monospace"><?= __("Año escolar") ?>: <span class="badge badge-info"><?= $year ?></span></p>
                    </div>
                </div>

            </div>
            <div class="col-lg-8 col-sm-12">
                <form method="POST">
                    <select class="form-control selectpicker w-100" name="student" data-live-search="true" required>
                        <option value=""><?= __("Seleccionar estudiante") ?></option>
                        <?php foreach ($students as $student): ?>
                            <option <?= isset($_REQUEST['student']) && $_REQUEST['student'] == $student->id ? 'selected=""' : '' ?> value="<?= $student->id ?>"><?= "$student->apellidos $student->nombre ($student->id)" ?></option>
                        <?php endforeach ?>
                    </select>
                    <button class="btn btn-primary btn-sm btn-block mt-2" type="submit"><?= __("Buscar información") ?></button>
                </form>
                <form method="POST">
                    <button class="btn btn-outline-primary btn-sm btn-block mt-2" name="new" type="submit"><?= __("Agregar una familia nueva") ?></button>
                </form>
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
                <?php if (Session::get("editedStudent")): ?>
                    <div class="d-flex align-items-center mt-2">
                        <div class="alert alert-info alert-dismissible fade show flex-fill" role="alert">
                            <?= __("Se ha actualizado la información del estudiante con éxito") ?>
                            <hr>
                            <p class="mb-0"><b><?= Session::get("editedStudent", true) ?></b></p>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (Session::get("addedStudent")): ?>
                    <div class="d-flex align-items-center mt-2">
                        <div class="alert alert-info alert-dismissible fade show flex-fill" role="alert">
                            <?= __("Se agregado el estudiante con éxito") ?>
                            <hr>
                            <p class="mb-0"><b><?= Session::get("addedStudent", true) ?></b></p>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php if (isset($_REQUEST['student']) || isset($_POST['new'])):
            if (isset($_REQUEST['student'])) {
                $parents = Family::find($_REQUEST['student']);
            } else {
                $nextId = DB::getNextIdFromTable('madre');
            }
        ?>
            <form method="POST" action="<?= Route::url('/admin/users/accounts/includes/index.php') ?>">
                <div class="row mt-5">
                    <div class="col-12">
                        <h1 class="text-center mt-3"><?= __("Información de los padres") ?> <i class="far fa-id-card"></i></h1>
                    </div>

                    <?php if (!isset($_POST['new'])): ?>
                        <div class="card col-12 p-3 rounded-0">
                            <div class="form-group row">
                                <label class="col-sm-2" for="accountNumber"><?= __("Número de cuenta") ?></label>
                                <div class="col-sm-10">
                                    <input type="text" value='<?= isset($parents) ? $parents->id : '' ?>' class="form-control col" name='accountNumber' id="accountNumber" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4" for="username"><?= __("Usuario y contraseña") ?></label>
                                <div class="col-sm-4">
                                    <input type="text" value='<?= isset($parents) ? $parents->usuario : '' ?>' data-lastusername="<?= isset($parents) ? $parents->usuario : '' ?>" class="form-control" name='username' id="username" required>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" value='<?= isset($parents) ? $parents->clave : '' ?>' class="form-control col" name='password' id="password" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4" for="familyAmount"><?= __("Número de familia") ?></label>
                                <div class="col-sm-2">
                                    <input type="number" value='<?= isset($parents) ? $parents->nfam : '0' ?>' class="form-control" name='familyAmount' id="familyAmount" min='0' required>
                                </div>
                            </div>
                        </div>
                    <?php endif ?>
                </div>
                <div class="row mt-2">
                    <!-- Mother Information -->
                    <div class="card col-12 col-lg-6 p-3 rounded-0">
                        <h5 class="card-title"><?= __("Información de la Madre") ?></h5>
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="nameM"><?= __("Nombre Completo") ?></label>
                                <input type="text" value='<?= isset($parents) ? $parents->madre : '' ?>' class="form-control" name='nameM' id="nameM">
                            </div>
                            <div class="form-group col-12">
                                <label for="reference"><?= __("Referencia") ?></label>
                                <input type="text" value='<?= isset($parents) ? $parents->codigom : '' ?>' class="form-control" name='referenceM' id="referenceM">
                            </div>
                            <div class="form-group col-12">
                                <label for="exM"><?= __("Ex alumno") ?></label>
                                <select class="form-control" name="exM" id="exM">
                                    <option <?= isset($parents) && $parents->ex_m === 'NO' ? 'selected=""' : '' ?> value="NO">NO</option>
                                    <option <?= isset($parents) && $parents->ex_m === 'SI' ? 'selected=""' : '' ?> value="SI"><?= __("SI") ?></option>
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label for="emailM"><?= __("Correo electrónico") ?></label>
                                <input type="email" value='<?= isset($parents) ? $parents->email_m : '' ?>' class="form-control" name='emailM' id="emailM">
                            </div>
                            <div class="form-group col-12">
                                <label for="residentialPhoneM"><?= __("Teléfono residencial") ?></label>
                                <input type="tel" value='<?= isset($parents) ? $parents->tel_m : '' ?>' class="form-control phone" name='residentialPhoneM' id="residentialPhoneM">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="cellPhoneM"><?= __("Celular") ?></label>
                                <input type="tel" value="<?= isset($parents) ? $parents->cel_m : '' ?>" class="form-control phone" id="cellPhoneM" name="cellPhoneM" />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="cellCompanyM"><?= __("Compañia telefonica") ?></label>
                                <select id="cellCompanyM" class="form-control" name="cellCompanyM">
                                    <option <?= isset($parents) && $parents->cel_com_m === '' ? 'selected=""' : '' ?> value=""><?= __("Seleccionar") ?></option>
                                    <?php foreach (Util::phoneCompanies() as $company): ?>
                                        <option <?= isset($parents) && $parents->cel_com_m === $company ? 'selected=""' : '' ?> value="<?= $company ?>"><?= $company ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <h6 class="card-subtitle col-12 my-2"><?= __("Dirección Postal") ?></h6>
                            <div class="form-row col-12">
                                <div class="col-12">
                                    <input class="form-control" type="text" placeholder="<?= __("Dirección") ?> 1" name="dir1" id="dir1" value="<?= isset($parents) ? $parents->dir1 : '' ?>" />
                                </div>
                                <div class="col-12 mt-1">
                                    <input class="form-control" type="text" placeholder="<?= __("Dirección") ?> 2" name="dir3" id="dir3" value="<?= isset($parents) ? $parents->dir3 : '' ?>" />
                                </div>
                                <div class="col-5 mt-1">
                                    <input class="form-control" type="text" placeholder="<?= __("Ciudad") ?>" name="city1" id="city1" value="<?= isset($parents) ? $parents->pueblo1 : '' ?>" />
                                </div>
                                <div class="col-3 mt-1">
                                    <input class="form-control" type="text" placeholder="<?= __("Estado") ?>" name="state1" id="state1" value="<?= isset($parents) ? $parents->est1 : '' ?>" />
                                </div>
                                <div class="col-4 mt-1">
                                    <input class="form-control" type="text" placeholder="<?= __("Codigo Postal") ?>" name="zip1" id="zip1" value="<?= isset($parents) ? $parents->zip1 : '' ?>" />
                                </div>
                            </div>
                            <h6 class="card-subtitle col-12 mt-4 mb-2"><?= __("Información del trabajo") ?></h6>
                            <div class="form-row col-12">
                                <div class="form-group col-6">
                                    <label for="jobM"><?= __("Compañia") ?></label>
                                    <input class="form-control" type="text" name="jobM" id="jobM" value="<?= isset($parents) ? $parents->trabajo_m : '' ?>" />
                                </div>
                                <div class="form-group col-6">
                                    <label for="jobPositionM"><?= __("Posición") ?></label>
                                    <input class="form-control" type="text" name="jobPositionM" id="jobPositionM" value="<?= isset($parents) ? $parents->posicion_m : '' ?>" />
                                </div>
                                <div class="form-group col-6">
                                    <label for="jobPhoneM"><?= __("Teléfono") ?></label>
                                    <input class="form-control phone" type="text" name="jobPhoneM" id="jobPhoneM" value="<?= isset($parents) ? $parents->tel_t_m : '' ?>" />
                                </div>
                                <div class="form-group col-6">
                                    <label for="jobExtM"><?= __("Extensión") ?></label>
                                    <input class="form-control onlyNumbers" type="text" name="jobExtM" id="jobExtM" value="<?= isset($parents) ? $parents->ex_t_d_m : '' ?>" />
                                </div>
                                <div class="form-group col-6">
                                    <label for="salaryM"><?= __("Sueldo") ?></label>
                                    <input class="form-control" type="text" name="salaryM" id="salaryM" value="<?= isset($parents) ? $parents->sueldom : '' ?>" />
                                </div>
                            </div>
                            <h6 class="card-subtitle col-12 my-2"><?= __("Opciones de correo electrónico y SMS") ?></h6>
                            <div class="form-row col-12">
                                <div class="form-group col-6">
                                    <label for="receiveEmailM"><?= __("Recibir correo electrónico") ?></label>
                                    <select class="form-control" name="receiveEmailM" id="receiveEmailM">
                                        <option <?= isset($parents) && $parents->re_e_m === 'NO' ? 'selected=""' : '' ?> value="NO">NO</option>
                                        <option <?= isset($parents) && $parents->re_e_m === 'SI' ? 'selected=""' : '' ?> value="SI"><?= __("SI") ?></option>
                                    </select>
                                </div>
                                <div class="form-group col-6">
                                    <label for="receiveSmsM"><?= __("Recibir SMS") ?></label>
                                    <select class="form-control" name="receiveSmsM" id="receiveSmsM">
                                        <option <?= isset($parents) && $parents->re_mc_m === 'NO' ? 'selected=""' : '' ?> value="NO">NO</option>
                                        <option <?= isset($parents) && $parents->re_mc_m === 'SI' ? 'selected=""' : '' ?> value="SI"><?= __("SI") ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- .Mother Information -->
                    <!-- Father Information -->
                    <div class="card col-12 col-lg-6 p-3 rounded-0">
                        <h5 class="card-title"><?= __("Información del Padre") ?></h5>
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="nameP"><?= __("Nombre Completo") ?></label>
                                <input type="text" value='<?= isset($parents) ? $parents->padre : '' ?>' class="form-control" name='nameP' id="nameP">
                            </div>
                            <div class="form-group col-12">
                                <label for="reference"><?= __("Referencia") ?></label>
                                <input type="text" value='<?= isset($parents) ? $parents->codigop : '' ?>' class="form-control" name='referenceP' id="referenceP">
                            </div>
                            <div class="form-group col-12">
                                <label for="exP"><?= __("Ex alumno") ?></label>
                                <select class="form-control" name="exP" id="exP" required>
                                    <option <?= isset($parents) && $parents->ex_p === 'NO' ? 'selected=""' : '' ?> value="NO">NO</option>
                                    <option <?= isset($parents) && $parents->ex_p === 'SI' ? 'selected=""' : '' ?> value="SI"><?= __("SI") ?></option>
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label for="emailP">E-mail</label>
                                <input type="email" value='<?= isset($parents) ? $parents->email_p : '' ?>' class="form-control" name='emailP' id="emailP">
                            </div>
                            <div class="form-group col-12">
                                <label for="residentialPhone"><?= __("Teléfono residencial") ?></label>
                                <input type="tel" value='<?= isset($parents) ? $parents->tel_p : '' ?>' class="form-control phone" name='residentialPhoneP' id="residentialPhoneP">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="cellPhoneP"><?= __("Celular") ?></label>
                                <input type="tel" value="<?= isset($parents) ? $parents->cel_p : '' ?>" class="form-control phone" id="cellPhoneP" name="cellPhoneP" />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="cellCompanyP"><?= __("Compañia telefonica") ?></label>
                                <select id="cellCompanyP" class="form-control" name="cellCompanyP">
                                    <option <?= isset($parents) && $parents->cel_com_p === '' ? 'selected=""' : '' ?> value=""><?= __("Seleccionar") ?></option>
                                    <?php foreach (Util::phoneCompanies() as $company): ?>
                                        <option <?= isset($parents) && $parents->cel_com_p === $company ? 'selected=""' : '' ?> value="<?= $company ?>"><?= $company ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <h6 class="card-subtitle col-12 my-2"><?= __("Dirección Postal") ?></h6>
                            <div class="form-row col-12">
                                <div class="col-12">
                                    <input class="form-control" type="text" placeholder="<?= __("Dirección") ?> 1" name="dir2" id="dir2" value="<?= isset($parents) ? $parents->dir2 : '' ?>" />
                                </div>
                                <div class="col-12 mt-1">
                                    <input class="form-control" type="text" placeholder="<?= __("Dirección") ?> 2" name="dir4" id="dir4" value="<?= isset($parents) ? $parents->dir4 : '' ?>" />
                                </div>
                                <div class="col-5 mt-1">
                                    <input class="form-control" type="text" placeholder="<?= __("Ciudad") ?>" name="city2" id="city2" value="<?= isset($parents) ? $parents->pueblo2 : '' ?>" />
                                </div>
                                <div class="col-3 mt-1">
                                    <input class="form-control" type="text" placeholder="<?= __("Estado") ?>" name="state2" id="state2" value="<?= isset($parents) ? $parents->est2 : '' ?>" />
                                </div>
                                <div class="col-4 mt-1">
                                    <input class="form-control" type="text" placeholder="<?= __("Codigo Postal") ?>" name="zip2" id="zip2" value="<?= isset($parents) ? $parents->zip2 : '' ?>" />
                                </div>
                            </div>
                            <h6 class="card-subtitle col-12 mt-4 mb-2"><?= __("Información del trabajo") ?></h6>
                            <div class="form-row col-12">
                                <div class="form-group col-6">
                                    <label for="jobP"><?= __("Compañia") ?></label>
                                    <input class="form-control" type="text" name="jobP" id="jobP" value="<?= isset($parents) ? $parents->trabajo_p : '' ?>" />
                                </div>
                                <div class="form-group col-6">
                                    <label for="jobPositionP"><?= __("Posición") ?></label>
                                    <input class="form-control" type="text" name="jobPositionP" id="jobPositionP" value="<?= isset($parents) ? $parents->posicion_p : '' ?>" />
                                </div>
                                <div class="form-group col-6">
                                    <label for="jobPhoneP"><?= __("Teléfono") ?></label>
                                    <input class="form-control phone" type="text" name="jobPhoneP" id="jobPhoneP" value="<?= isset($parents) ? $parents->tel_t_p : '' ?>" />
                                </div>
                                <div class="form-group col-6">
                                    <label for="jobExtP"><?= __("Extensión") ?></label>
                                    <input class="form-control onlyNumbers" type="text" name="jobExtP" id="jobExtP" value="<?= isset($parents) ? $parents->ex_t_d_p : '' ?>" />
                                </div>
                                <div class="form-group col-6">
                                    <label for="salaryP"><?= __("Sueldo") ?></label>
                                    <input class="form-control" type="text" name="salaryP" id="salaryP" value="<?= isset($parents) ? $parents->sueldop : '' ?>" />
                                </div>
                            </div>
                            <h6 class="card-subtitle col-12 my-2"><?= __("Opciones de correo electrónico y SMS") ?></h6>
                            <div class="form-row col-12">
                                <div class="form-group col-6">
                                    <label for="receiveEmailP"><?= __("Recibir correo electrónico") ?></label>
                                    <select class="form-control" name="receiveEmailP" id="receiveEmailP">
                                        <option <?= isset($parents) && $parents->re_e_p === 'NO' ? 'selected=""' : '' ?> value="NO">NO</option>
                                        <option <?= isset($parents) && $parents->re_e_p === 'SI' ? 'selected=""' : '' ?> value="SI"><?= __("SI") ?></option>
                                    </select>
                                </div>
                                <div class="form-group col-6">
                                    <label for="receiveSmsP"><?= __("Recibir SMS") ?></label>
                                    <select class="form-control" name="receiveSmsP" id="receiveSmsP">
                                        <option <?= isset($parents) && $parents->re_mc_p === 'NO' ? 'selected=""' : '' ?> value="NO">NO</option>
                                        <option <?= isset($parents) && $parents->re_mc_p === 'SI' ? 'selected=""' : '' ?> value="SI"><?= __("SI") ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- .Father Information -->
                    <div class="col-12 mt-1">
                        <p class="text-muted text-center mb-0"><small><?= __("Esta opción de SMS se aplica a su contrato o tarifa de mensajes de textos recibidos a su celular.") ?></small></p>
                        <hr>
                    </div>

                    <div class="col-12">
                        <h5 class="card-title text-center my-3"><?= __("Personas autorizadas a recoger") ?></h5>
                    </div>
                    <?php for ($i = 1; $i <= 2; $i++): ?>
                        <div class="card col-12 col-lg-6 p-3 rounded-0">
                            <div class="form-group row">
                                <label for="<?= "person$i" ?>" class="col-4"><?= __("Nombre") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= isset($parents) ? $parents->{"per$i"} : '' ?>" name="<?= "person$i" ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="<?= "relationship$i" ?>" class="col-4"><?= __("Parentesco") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= isset($parents) ? $parents->{"rel$i"} : '' ?>" name="<?= "relationship$i" ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="<?= "celullar$i" ?>" class="col-4"><?= __("Celular") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= isset($parents) ? $parents->{"cel$i"} : '' ?>" name="<?= "celullar$i" ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="<?= "phone$i" ?>" class="col-4"><?= __("Teléfono") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= isset($parents) ? $parents->{"tec$i"} : '' ?>" name="<?= "phone$i" ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="<?= "workPhone$i" ?>" class="col-4"><?= __("Teléfono trabajo") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= isset($parents) ? $parents->{"tet$i"} : '' ?>" name="<?= "workPhone$i" ?>" class="form-control">
                                </div>
                            </div>
                        </div>
                    <?php endfor ?>
                    <?php for ($i = 5; $i <= 6; $i++): ?>
                        <div class="card col-12 col-lg-6 p-3 rounded-0">
                            <div class="form-group row">
                                <label for="<?= "person$i" ?>" class="col-4"><?= __("Nombre") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= isset($parents) ? $parents->{"per$i"} : '' ?>" name="<?= "person$i" ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="<?= "relationship$i" ?>" class="col-4"><?= __("Parentesco") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= isset($parents) ? $parents->{"rel$i"} : '' ?>" name="<?= "relationship$i" ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="<?= "celullar$i" ?>" class="col-4"><?= __("Celular") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= isset($parents) ? $parents->{"cel$i"} : '' ?>" name="<?= "celullar$i" ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="<?= "phone$i" ?>" class="col-4"><?= __("Teléfono") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= isset($parents) ? $parents->{"tec$i"} : '' ?>" name="<?= "phone$i" ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="<?= "workPhone$i" ?>" class="col-4"><?= __("Teléfono trabajo") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= isset($parents) ? $parents->{"tet$i"} : '' ?>" name="<?= "workPhone$i" ?>" class="form-control">
                                </div>
                            </div>
                        </div>
                    <?php endfor ?>
                    <div class="col-12">
                        <h5 class="card-title text-center my-3"><?= __("Información de emergencia") ?></h5>
                    </div>
                    <?php for ($i = 3; $i <= 4; $i++): ?>
                        <div class="card col-12 col-lg-6 p-3 rounded-0">
                            <div class="form-group row">
                                <label for="<?= "person$i" ?>" class="col-4"><?= __("Nombre") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= isset($parents) ? $parents->{"per$i"} : '' ?>" name="<?= "person$i" ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="<?= "relationship$i" ?>" class="col-4"><?= __("Parentesco") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= isset($parents) ? $parents->{"rel$i"} : '' ?>" name="<?= "relationship$i" ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="<?= "celullar$i" ?>" class="col-4"><?= __("Celular") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= isset($parents) ? $parents->{"cel$i"} : '' ?>" name="<?= "celullar$i" ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="<?= "phone$i" ?>" class="col-4"><?= __("Teléfono") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= isset($parents) ? $parents->{"tec$i"} : '' ?>" name="<?= "phone$i" ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="<?= "workPhone$i" ?>" class="col-4"><?= __("Teléfono trabajo") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= isset($parents) ? $parents->{"tet$i"} : '' ?>" name="<?= "workPhone$i" ?>" class="form-control">
                                </div>
                            </div>
                        </div>
                    <?php endfor ?>
                    <div class="col-12">
                        <h5 class="card-title text-center my-3"><?= __("Persona responsable a pagar") ?></h5>
                    </div>
                    <div class="card col-12 p-3 rounded-0">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group row">
                                    <label for="personToPay" class="col-12 col-md-4"><?= __("Quién paga?") ?></label>
                                    <div class="col-12 col-md-6">
                                        <select name="personToPay" id="personToPay" class="form-control w-100" required>
                                            <option value=""><?= __("Seleccionar") ?></option>
                                            <option <?= $parents->qpaga === "M" ? 'selected=""' : '' ?> value="M">Madre</option>
                                            <option <?= $parents->qpaga === "P" ? 'selected=""' : '' ?> value="P">Padre</option>
                                            <option <?= $parents->qpaga === "E" ? 'selected=""' : '' ?> value="E">Otro</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inChargeName"><?= __("Nombre") ?>:</label>
                                    <input type="text" name="inChargeName" id="inChargeName" value="<?= isset($parents) ? $parents->encargado : '' ?>" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="inChargeRelationship"><?= __("Parentesco") ?>:</label>
                                    <input type="text" name="inChargeRelationship" id="inChargeRelationship" value="<?= isset($parents) ? $parents->parentesco : '' ?>" class="form-control" readonly>
                                </div>
                                <div class="form-group row">
                                    <label for="inChargeEmail" class="col-12 col-md-3"><?= __("Correo electrónico") ?>:</label>
                                    <div class="col-12 col-md-9">
                                        <input type="text" name="inChargeEmail" id="inChargeEmail" value="<?= isset($parents) ? $parents->email_e : '' ?>" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group row">
                                    <label for="inChargePhone" class="col-12 col-md-4"><?= __("Teléfono") ?>:</label>
                                    <div class="col-12 col-md-8">
                                        <input type="text" name="inChargePhone" id="inChargePhone" value="<?= isset($parents) ? $parents->tel_en : '' ?>" class="form-control phone">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inChargeWorkPhone" class="col-12 col-md-4"><?= __("Teléfono trabajo") ?>:</label>
                                    <div class="col-12 col-md-8">
                                        <input type="text" name="inChargeWorkPhone" id="inChargeWorkPhone" value="<?= isset($parents) ? $parents->tel_t_e : '' ?>" class="form-control phone">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="inChargeCellPhone"><?= __("Celular") ?></label>
                                        <input type="tel" value="<?= isset($parents) ? $parents->cel_e : '' ?>" class="form-control phone" id="inChargeCellPhone" name="inChargeCellPhone" />
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inChargeCellCompany"><?= __("Compañia telefonica") ?></label>
                                        <select id="inChargeCellCompany" class="form-control" name="inChargeCellCompany">
                                            <option <?= isset($parents) && $parents->cel_com_m === '' ? 'selected=""' : '' ?> value=""><?= __("Seleccionar") ?></option>
                                            <?php foreach (Util::phoneCompanies() as $company): ?>
                                                <option <?= isset($parents) && $parents->cel_com_m === $company ? 'selected=""' : '' ?> value="<?= $company ?>"><?= $company ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <h6 class="card-subtitle col-12 my-2"><?= __("Dirección Postal") ?></h6>
                                <div class="form-row col-12">
                                    <div class="col-12">
                                        <input class="form-control" type="text" placeholder="<?= __("Dirección") ?> 1" name="inChargeDir1" id="inChargeDir1" value="<?= isset($parents) ? $parents->dir_e1 : '' ?>" />
                                    </div>
                                    <div class="col-12 mt-1">
                                        <input class="form-control" type="text" placeholder="<?= __("Dirección") ?> 2" name="inChargeDir2" id="inChargeDir2" value="<?= isset($parents) ? $parents->dir_e2 : '' ?>" />
                                    </div>
                                    <div class="col-5 mt-1">
                                        <input class="form-control" type="text" placeholder="<?= __("Ciudad") ?>" name="inChageCity" id="inChageCity" value="<?= isset($parents) ? $parents->pue_e : '' ?>" />
                                    </div>
                                    <div class="col-3 mt-1">
                                        <input class="form-control" type="text" placeholder="<?= __("Estado") ?>" name="inChageState" id="inChageState" value="<?= isset($parents) ? $parents->esta_e : '' ?>" />
                                    </div>
                                    <div class="col-4 mt-1">
                                        <input class="form-control" type="text" placeholder="<?= __("Codigo Postal") ?>" name="inChageZip" id="inChageZip" value="<?= isset($parents) ? $parents->zip_e : '' ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 text-center mt-2">
                    <button id="submit" class="btn btn-primary btn-block" name="<?= isset($_REQUEST['student']) ? 'edit' : 'save' ?>" type="submit"><?= __("Guardar") ?></button>
                </div>
            </form>
            <div class="col-12 my-1">
                <hr>
            </div>

            <?php if (isset($_REQUEST['student']) && $parents): ?>
                <div class="col-12 my-1">
                    <h2 class="text-center mb-3"><?= __("Información de los hijos") ?></h2>
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3">
                        <?php foreach ($parents->kids as $kid): ?>
                            <div class="col mt-1">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <img src="<?= Util::studentProfilePicture($kid) ?>" class="rounded-circle img-thumbnail d-block mx-auto mb-3 img-fluid" alt="Profile Picture" style="width:150px;height:150px" />
                                        <h6 class="card-title"><?= "$kid->nombre $kid->apellidos" ?></h6>
                                        <p class="card-text"><?= __("Grado:") ?> <?= $kid->grado ?></p>
                                        <p class="card-text"><?= __("Fecha de nacimiento:") ?> <?= $kid->fecha->format('Y-m-d') ?></p>
                                    </div>
                                    <div class="card-footer">
                                        <a href="<?= Route::url("/admin/users/accounts/students.php?pk=$kid->mt&id={$kid->id}") ?>" class="btn btn-primary btn-block stretched-link"><?= __("Editar estudiante") ?></a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                        <div class="col mt-1" style="height:448px !important">
                            <div class="card h-100">
                                <div class="card-body d-flex flex-wrap align-content-center justify-content-center">
                                    <a href="<?= Route::url("/admin/users/accounts/students.php?id={$kid->id}") ?>" class="btn btn-primary stretched-link"><?= __("Agregar") ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>

    </div>
<?php endif ?>

</div>


<?php
Route::includeFile('/includes/layouts/scripts.php', true);
Route::selectPicker('js');

?>

</body>

</html>