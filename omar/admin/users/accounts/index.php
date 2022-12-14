<?php
require_once '../../../app.php';

use Classes\Controllers\Parents;
use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;

Session::is_logged();
$students = new Student();
$lang = new Lang([
    ['Cuentas', 'Accounts'],
    ['estudiante', 'student'],
    ['Seleccionar padres sin estudiantes', 'Select parents with no students added'],
    ['Total de estudiantes', 'Total students'],
    ['Femeninas', 'Female'],
    ['Masculinos', 'Male'],
    ['Total de familias', 'Total families'],
    ['Año escolar', 'School year'],
    ['Buscar información', 'Search information'],
    ['Agregar una familia nueva', 'Add a new family'],
    ["Número de cuenta", "Account number"],
    ["Usuario y contraseña", "Username and password"],
    ["Número de familia", "Family number"],
    ['Información de los padres', 'Parents information'],
    ["Información de la Madre", "Mother's information"],
    ["Nombre Completo", "Full name"],
    ["Referencia", "Reference"],
    ["Ex alumno", "Ex student"],
    ["SI", "YES"],
    ["Correo electrónico", "Email"],
    ["Teléfono residencial", "Residential telephone"],
    ["Celular", "Cellphone"],
    ["Compañia telefonica", "Cellphone company"],
    ["Dirección Postal", "Postal Address"],
    ["Dirección", "Address"],
    ["Ciudad", "City"],
    ["Estado", "State"],
    ["Codigo Postal", "Postal Code"],
    ["Información del trabajo", "Job information"],
    ["Compañia", "Company"],
    ["Posición", "Position"],
    ["Extensión", "Extension"],
    ["Sueldo", "Salary"],
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
    ['Personas autorizadas a recoger', 'Authorized people to pick up'],
    ['Nombre', 'Name'],
    ['Parentesco', 'Relationship'],
    ['Celular', 'Cellphone'],
    ['Teléfono', 'Telephone'],
    ['Teléfono trabajo', 'Work phone'],
    ['Información de emergencia', 'Emergency information'],
    ['Persona responsable a pagar', 'Person responsible to pay'],
    ['Quién paga?', 'Who is paying?'],
    ['Guardar', 'Save'],
    ['Se ha agregado con éxito', "It has been successfully added"],
    ['Se ha actualizado con éxito', "It has been successfully updated"],
    ['Se ha actualizado la información del estudiante con éxito', "Student information has been updated successfully"],
    ["Se agregado el estudiante con éxito", "The student was added successfully"],
    ["Agregar", 'Add'],

]);
$year = $students->info('year');
$female = DB::table('year')->whereRaw("year = '$year' AND activo = '' AND (genero = 'F' OR genero = 1)")->get();
$male = DB::table('year')->whereRaw("year = '$year' AND activo = '' AND (genero = 'M' OR genero = 2)")->get();
$families = DB::table('year')->select("DISTINCT id")->where([
    ['year', $year],
])->get();
$parentsWithNoStudents = DB::table('madre')->whereRaw("not EXISTS(select * from year where year.id = madre.id)")->orderBy('id DESC')->get();
if (Session::get('accountNumber')) {
    $_REQUEST['student'] = Session::get('accountNumber', true);
}
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Cuentas");
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
                        <p class="text-monospace"><?= $lang->translation("Total de estudiantes") ?>: <span class="badge badge-info"><?= sizeof($students->All()) ?></span></p>
                        <p class="text-monospace"><?= $lang->translation("Femeninas") ?>: <span class="badge badge-info"><?= sizeof($female) ?></span></p>
                        <p class="text-monospace"><?= $lang->translation("Masculinos") ?>: <span class="badge badge-info"><?= sizeof($male) ?></span></p>
                        <p class="text-monospace"><?= $lang->translation("Total de familias") ?>: <span class="badge badge-info"><?= sizeof($families) ?></span></p>
                        <p class="text-monospace"><?= $lang->translation("Año escolar") ?>: <span class="badge badge-info"><?= $year ?></span></p>
                    </div>
                </div>

            </div>
            <div class="col-lg-8 col-sm-12">
                <form method="POST">
                    <select class="form-control selectpicker w-100" name="student" data-live-search="true" required>
                        <option value=""><?= $lang->translation("Seleccionar") . ' ' . $lang->translation('estudiante') ?></option>
                        <?php foreach ($students->All() as $student) : ?>
                            <option <?= isset($_REQUEST['student']) && $_REQUEST['student'] == $student->id ? 'selected=""' : '' ?> value="<?= $student->id ?>"><?= "$student->apellidos $student->nombre ($student->id)" ?></option>
                        <?php endforeach ?>
                    </select>
                    <button class="btn btn-primary btn-sm btn-block mt-2" type="submit"><?= $lang->translation("Buscar información") ?></button>
                </form>
                <form class="mt-4" method="POST">
                    <select class="form-control selectpicker w-100" name="student" data-live-search="true" required>
                        <option value=""><?= $lang->translation("Seleccionar padres sin estudiantes") ?></option>
                        <?php foreach ($parentsWithNoStudents as $parent) :
                            $parentInfo = '';
                            if ($parent->madre !== '' && $parent->padre !== '') {
                                $parentInfo = "$parent->madre, $parent->padre";
                            } else {
                                if ($parent->madre !== '') {
                                    $parentInfo = "$parent->madre";
                                } else if ($parent->padre !== '') {
                                    $parentInfo = "$parent->padre";
                                }
                            }
                            $parentInfo .= " ($parent->id)";

                        ?>
                            <option <?= isset($_REQUEST['student']) && $_REQUEST['student'] == $parent->id ? 'selected=""' : '' ?> value="<?= $parent->id ?>"><?= $parentInfo ?></option>
                        <?php endforeach ?>
                    </select>
                    <button class="btn btn-primary btn-sm btn-block mt-2" type="submit"><?= $lang->translation("Buscar información") ?></button>
                </form>
                <form method="POST">
                    <button class="btn btn-outline-primary btn-sm btn-block mt-2" name="new" type="submit"><?= $lang->translation("Agregar una familia nueva") ?></button>
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
                <?php if (Session::get("editedStudent")) : ?>
                    <div class="d-flex align-items-center mt-2">
                        <div class="alert alert-info alert-dismissible fade show flex-fill" role="alert">
                            <?= $lang->translation("Se ha actualizado la información del estudiante con éxito") ?>
                            <hr>
                            <p class="mb-0"><b><?= Session::get("editedStudent", true) ?></b></p>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (Session::get("addedStudent")) : ?>
                    <div class="d-flex align-items-center mt-2">
                        <div class="alert alert-info alert-dismissible fade show flex-fill" role="alert">
                            <?= $lang->translation("Se agregado el estudiante con éxito") ?>
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
        <?php if (isset($_REQUEST['student']) || isset($_POST['new'])) :
            if (isset($_REQUEST['student'])) {
                $parents = new Parents($_REQUEST['student']);
            } else {
                $nextId = DB::getNextIdFromTable('madre');
            }
        ?>
            <form method="POST" action="<?= Route::url('/admin/users/accounts/includes/index.php') ?>">
                <div class="row mt-5">
                    <div class="col-12">
                        <h1 class="text-center mt-3"><?= $lang->translation("Información de los padres") ?> <i class="far fa-id-card"></i></h1>
                    </div>

                    <div class="card col-12 p-3 rounded-0">
                        <div class="form-group row">
                            <label class="col-sm-2" for="accountNumber"><?= $lang->translation("Número de cuenta") ?></label>
                            <div class="col-sm-10">
                                <input type="text" value='<?= isset($_REQUEST['student']) ? $parents->id : $nextId ?>' class="form-control col" name='accountNumber' id="accountNumber" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4" for="username"><?= $lang->translation("Usuario y contraseña") ?></label>
                            <div class="col-sm-4">
                                <input type="text" value='<?= isset($_REQUEST['student']) ? $parents->usuario : $nextId ?>' data-lastusername="<?= isset($_REQUEST['student']) ? $parents->usuario : $nextId ?>" class="form-control" name='username' id="username" required>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" value='<?= $parents->clave ?>' class="form-control col" name='password' id="password" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4" for="familyAmount"><?= $lang->translation("Número de familia") ?></label>
                            <div class="col-sm-2">
                                <input type="number" value='<?= isset($_REQUEST['student']) ? $parents->nfam : '0' ?>' class="form-control" name='familyAmount' id="familyAmount" min='0' required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <!-- Mother Information -->
                    <div class="card col-12 col-lg-6 p-3 rounded-0">
                        <h5 class="card-title"><?= $lang->translation("Información de la Madre") ?></h5>
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="nameM"><?= $lang->translation("Nombre Completo") ?></label>
                                <input type="text" value='<?= $parents->madre ?>' class="form-control" name='nameM' id="nameM">
                            </div>
                            <div class="form-group col-12">
                                <label for="reference"><?= $lang->translation("Referencia") ?></label>
                                <input type="text" value='<?= $parents->codigom ?>' class="form-control" name='referenceM' id="referenceM">
                            </div>
                            <div class="form-group col-12">
                                <label for="exM"><?= $lang->translation("Ex alumno") ?></label>
                                <select class="form-control" name="exM" id="exM">
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
                                <input type="tel" value='<?= $parents->tel_m ?>' class="form-control phone" name='residentialPhoneM' id="residentialPhoneM">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="cellPhoneM"><?= $lang->translation("Celular") ?></label>
                                <input type="tel" value="<?= $parents->cel_m ?>" class="form-control phone" id="cellPhoneM" name="cellPhoneM" />
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
                            <h6 class="card-subtitle col-12 mt-4 mb-2"><?= $lang->translation("Información del trabajo") ?></h6>
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
                                    <input class="form-control phone" type="text" name="jobPhoneM" id="jobPhoneM" value="<?= $parents->tel_t_m ?>" />
                                </div>
                                <div class="form-group col-6">
                                    <label for="jobExtM"><?= $lang->translation("Extensión") ?></label>
                                    <input class="form-control onlyNumbers" type="text" name="jobExtM" id="jobExtM" value="<?= $parents->ex_t_d_m ?>" />
                                </div>
                                <div class="form-group col-6">
                                    <label for="salaryM"><?= $lang->translation("Sueldo") ?></label>
                                    <input class="form-control" type="text" name="salaryM" id="salaryM" value="<?= $parents->sueldom ?>" />
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
                        <h5 class="card-title"><?= $lang->translation("Información del Padre") ?></h5>
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="nameP"><?= $lang->translation("Nombre Completo") ?></label>
                                <input type="text" value='<?= $parents->padre ?>' class="form-control" name='nameP' id="nameP">
                            </div>
                            <div class="form-group col-12">
                                <label for="reference"><?= $lang->translation("Referencia") ?></label>
                                <input type="text" value='<?= $parents->codigop ?>' class="form-control" name='referenceP' id="referenceP">
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
                                <input type="tel" value='<?= $parents->tel_p ?>' class="form-control phone" name='residentialPhoneP' id="residentialPhoneP">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="cellPhoneP"><?= $lang->translation("Celular") ?></label>
                                <input type="tel" value="<?= $parents->cel_p ?>" class="form-control phone" id="cellPhoneP" name="cellPhoneP" />
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
                                    <input class="form-control" type="text" placeholder="<?= $lang->translation("Dirección") ?> 1" name="dir2" id="dir2" value="<?= $parents->dir2 ?>" />
                                </div>
                                <div class="col-12 mt-1">
                                    <input class="form-control" type="text" placeholder="<?= $lang->translation("Dirección") ?> 2" name="dir4" id="dir4" value="<?= $parents->dir4 ?>" />
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
                            <h6 class="card-subtitle col-12 mt-4 mb-2"><?= $lang->translation("Información del trabajo") ?></h6>
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
                                    <input class="form-control phone" type="text" name="jobPhoneP" id="jobPhoneP" value="<?= $parents->tel_t_p ?>" />
                                </div>
                                <div class="form-group col-6">
                                    <label for="jobExtP"><?= $lang->translation("Extensión") ?></label>
                                    <input class="form-control onlyNumbers" type="text" name="jobExtP" id="jobExtP" value="<?= $parents->ex_t_d_p ?>" />
                                </div>
                                <div class="form-group col-6">
                                    <label for="salaryP"><?= $lang->translation("Sueldo") ?></label>
                                    <input class="form-control" type="text" name="salaryP" id="salaryP" value="<?= $parents->sueldop ?>" />
                                </div>
                            </div>
                            <h6 class="card-subtitle col-12 my-2"><?= $lang->translation("Opciones de correo electrónico y SMS") ?></h6>
                            <div class="form-row col-12">
                                <div class="form-group col-6">
                                    <label for="receiveEmailP"><?= $lang->translation("Recibir correo electrónico") ?></label>
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
                    <div class="col-12 mt-1">
                        <p class="text-muted text-center mb-0"><small><?= $lang->translation("Esta opción de SMS se aplica a su contrato o tarifa de mensajes de textos recibidos a su celular.") ?></small></p>
                        <hr>
                    </div>

                    <div class="col-12">
                        <h5 class="card-title text-center my-3"><?= $lang->translation("Personas autorizadas a recoger") ?></h5>
                    </div>
                    <?php for ($i = 1; $i <= 2; $i++) : ?>
                        <div class="card col-12 col-lg-6 p-3 rounded-0">
                            <div class="form-group row">
                                <label for="<?= "person$i" ?>" class="col-4"><?= $lang->translation("Nombre") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= $parents->{"per$i"} ?>" name="<?= "person$i" ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="<?= "relationship$i" ?>" class="col-4"><?= $lang->translation("Parentesco") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= $parents->{"rel$i"} ?>" name="<?= "relationship$i" ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="<?= "celullar$i" ?>" class="col-4"><?= $lang->translation("Celular") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= $parents->{"cel$i"} ?>" name="<?= "celullar$i" ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="<?= "phone$i" ?>" class="col-4"><?= $lang->translation("Teléfono") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= $parents->{"tec$i"} ?>" name="<?= "phone$i" ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="<?= "workPhone$i" ?>" class="col-4"><?= $lang->translation("Teléfono trabajo") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= $parents->{"tet$i"} ?>" name="<?= "workPhone$i" ?>" class="form-control">
                                </div>
                            </div>
                        </div>
                    <?php endfor ?>
                    <?php for ($i = 5; $i <= 6; $i++) : ?>
                        <div class="card col-12 col-lg-6 p-3 rounded-0">
                            <div class="form-group row">
                                <label for="<?= "person$i" ?>" class="col-4"><?= $lang->translation("Nombre") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= $parents->{"per$i"} ?>" name="<?= "person$i" ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="<?= "relationship$i" ?>" class="col-4"><?= $lang->translation("Parentesco") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= $parents->{"rel$i"} ?>" name="<?= "relationship$i" ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="<?= "celullar$i" ?>" class="col-4"><?= $lang->translation("Celular") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= $parents->{"cel$i"} ?>" name="<?= "celullar$i" ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="<?= "phone$i" ?>" class="col-4"><?= $lang->translation("Teléfono") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= $parents->{"tec$i"} ?>" name="<?= "phone$i" ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="<?= "workPhone$i" ?>" class="col-4"><?= $lang->translation("Teléfono trabajo") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= $parents->{"tet$i"} ?>" name="<?= "workPhone$i" ?>" class="form-control">
                                </div>
                            </div>
                        </div>
                    <?php endfor ?>
                    <div class="col-12">
                        <h5 class="card-title text-center my-3"><?= $lang->translation("Información de emergencia") ?></h5>
                    </div>
                    <?php for ($i = 3; $i <= 4; $i++) : ?>
                        <div class="card col-12 col-lg-6 p-3 rounded-0">
                            <div class="form-group row">
                                <label for="<?= "person$i" ?>" class="col-4"><?= $lang->translation("Nombre") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= $parents->{"per$i"} ?>" name="<?= "person$i" ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="<?= "relationship$i" ?>" class="col-4"><?= $lang->translation("Parentesco") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= $parents->{"rel$i"} ?>" name="<?= "relationship$i" ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="<?= "celullar$i" ?>" class="col-4"><?= $lang->translation("Celular") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= $parents->{"cel$i"} ?>" name="<?= "celullar$i" ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="<?= "phone$i" ?>" class="col-4"><?= $lang->translation("Teléfono") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= $parents->{"tec$i"} ?>" name="<?= "phone$i" ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="<?= "workPhone$i" ?>" class="col-4"><?= $lang->translation("Teléfono trabajo") ?>:</label>
                                <div class="col-8">
                                    <input type="text" value="<?= $parents->{"tet$i"} ?>" name="<?= "workPhone$i" ?>" class="form-control">
                                </div>
                            </div>
                        </div>
                    <?php endfor ?>
                    <div class="col-12">
                        <h5 class="card-title text-center my-3"><?= $lang->translation("Persona responsable a pagar") ?></h5>
                    </div>
                    <div class="card col-12 p-3 rounded-0">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group row">
                                    <label for="personToPay" class="col-12 col-md-4"><?= $lang->translation("Quién paga?") ?></label>
                                    <div class="col-12 col-md-6">
                                        <select name="personToPay" id="personToPay" class="form-control w-100" required>
                                            <option value=""><?= $lang->translation("Seleccionar") ?></option>
                                            <option <?= $parents->qpaga === "M" ? 'selected=""' : '' ?> value="M">Madre</option>
                                            <option <?= $parents->qpaga === "P" ? 'selected=""' : '' ?> value="P">Padre</option>
                                            <option <?= $parents->qpaga === "E" ? 'selected=""' : '' ?> value="E">Otro</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inChargeName"><?= $lang->translation("Nombre") ?>:</label>
                                    <input type="text" name="inChargeName" id="inChargeName" value="<?= $parents->encargado ?>" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="inChargeRelationship"><?= $lang->translation("Parentesco") ?>:</label>
                                    <input type="text" name="inChargeRelationship" id="inChargeRelationship" value="<?= $parents->parentesco ?>" class="form-control" readonly>
                                </div>
                                <div class="form-group row">
                                    <label for="inChargeEmail" class="col-12 col-md-3"><?= $lang->translation("Correo electrónico") ?>:</label>
                                    <div class="col-12 col-md-9">
                                        <input type="text" name="inChargeEmail" id="inChargeEmail" value="<?= $parents->email_e ?>" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group row">
                                    <label for="inChargePhone" class="col-12 col-md-4"><?= $lang->translation("Teléfono") ?>:</label>
                                    <div class="col-12 col-md-8">
                                        <input type="text" name="inChargePhone" id="inChargePhone" value="<?= $parents->tel_en ?>" class="form-control phone">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inChargeWorkPhone" class="col-12 col-md-4"><?= $lang->translation("Teléfono trabajo") ?>:</label>
                                    <div class="col-12 col-md-8">
                                        <input type="text" name="inChargeWorkPhone" id="inChargeWorkPhone" value="<?= $parents->tel_t_e ?>" class="form-control phone">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="inChargeCellPhone"><?= $lang->translation("Celular") ?></label>
                                        <input type="tel" value="<?= $parents->cel_e ?>" class="form-control phone" id="inChargeCellPhone" name="inChargeCellPhone" />
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inChargeCellCompany"><?= $lang->translation("Compañia telefonica") ?></label>
                                        <select id="inChargeCellCompany" class="form-control" name="inChargeCellCompany">
                                            <option <?= $parents->cel_com_m === '' ? 'selected=""' : '' ?> value=""><?= $lang->translation("Seleccionar") ?></option>
                                            <?php foreach (Util::phoneCompanies() as $company) : ?>
                                                <option <?= $parents->cel_com_m === $company ? 'selected=""' : '' ?> value="<?= $company ?>"><?= $company ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <h6 class="card-subtitle col-12 my-2"><?= $lang->translation("Dirección Postal") ?></h6>
                                <div class="form-row col-12">
                                    <div class="col-12">
                                        <input class="form-control" type="text" placeholder="<?= $lang->translation("Dirección") ?> 1" name="inChargeDir1" id="inChargeDir1" value="<?= $parents->dir_e1 ?>" />
                                    </div>
                                    <div class="col-12 mt-1">
                                        <input class="form-control" type="text" placeholder="<?= $lang->translation("Dirección") ?> 2" name="inChargeDir2" id="inChargeDir2" value="<?= $parents->dir_e2 ?>" />
                                    </div>
                                    <div class="col-5 mt-1">
                                        <input class="form-control" type="text" placeholder="<?= $lang->translation("Ciudad") ?>" name="inChageCity" id="inChageCity" value="<?= $parents->pue_e ?>" />
                                    </div>
                                    <div class="col-3 mt-1">
                                        <input class="form-control" type="text" placeholder="<?= $lang->translation("Estado") ?>" name="inChageState" id="inChageState" value="<?= $parents->esta_e ?>" />
                                    </div>
                                    <div class="col-4 mt-1">
                                        <input class="form-control" type="text" placeholder="<?= $lang->translation("Codigo Postal") ?>" name="inChageZip" id="inChageZip" value="<?= $parents->zip_e ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 text-center mt-2">
                    <button id="submit" class="btn btn-primary btn-block" name="<?= isset($_REQUEST['student']) ? 'edit' : 'save' ?>" type="submit"><?= $lang->translation("Guardar") ?></button>
                </div>
            </form>
            <div class="col-12 my-1">
                <hr>
            </div>

            <?php if ($_REQUEST['student']) : ?>
                <div class="col-12 my-1">
                    <h2 class="text-center mb-3"><?= $lang->translation("Información de los hijos") ?></h2>
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3">
                        <?php foreach ($parents->kids() as $kid) : ?>
                            <div class="col mt-1">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <img src="<?= Util::studentProfilePicture($kid) ?>" class="rounded-circle img-thumbnail d-block mx-auto mb-3 img-fluid" alt="Profile Picture" style="width:150px;height:150px" />
                                        <h6 class="card-title"><?= "$kid->nombre $kid->apellidos" ?></h6>
                                        <p class="card-text"><?= $lang->translation("Grado:") ?> <?= $kid->grado ?></p>
                                        <p class="card-text"><?= $lang->translation("Fecha de nacimiento:") ?> <?= Util::formatDate($kid->fecha, true, true) ?></p>
                                    </div>
                                    <div class="card-footer">
                                        <a href="<?= Route::url("/admin/users/accounts/students.php?pk=$kid->mt&id=$parents->id") ?>" class="btn btn-primary btn-block stretched-link">Edit student</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                        <div class="col mt-1" style="height:448px !important">
                            <div class="card h-100">
                                <div class="card-body d-flex flex-wrap align-content-center justify-content-center">
                                    <a href="<?= Route::url("/admin/users/accounts/students.php?id=$parents->id") ?>" class="btn btn-primary stretched-link"><?= $lang->translation("Agregar") ?></a>
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