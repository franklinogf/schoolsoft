<?php
require_once '../../../app.php';

use Classes\Controllers\School;
use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;

Session::is_logged();
$school = new School();
$lang = new Lang([
    ["Este seguro social ya existe", "This social security already exists"],
    ["Este seguro social esta disponible", "This social security is available"],
    ["Estudiante", "Student"],
    ["Denominación", "Denomination"],
    ["Padre/Madre biológico", "Biological father/mother"],
    ["Re-matricula", "Re-enrollment"],

    ["Compañia telefonica", "Cellphone company"],
    ["Información del estudiante", "Student information"],
    ["Información general", "General information"],
    ["Seguro social", "Social Security"],
    ["Grado", "Grade"],
    ["Nombre", "Name"],
    ["Apellidos", "Surnames"],
    ["Referencia", "Reference"],
    ["Número de cuenta", "Account number"],
    ["Fecha de nacimiento", "Date of Birth"],
    ["Fecha de matrícula", "Registration date"],
    ["Lugar de nacimiento", "Place of birth"],
    ["Género", "Gender"],
    ["Masculino", "Male"],
    ["Femenino", "Female"],
    ["Vive con", "Lives with"],
    ["Ambos padres", "Both parents"],
    ["Madre", "Mother"],
    ["Padre", "Father"],
    ["Encargado", "Person in charge"],
    ["Nuevo", "New"],
    ["Si", "Yes"],
    ["Raza", "Race"],
    ["Blanco", "White"],
    ["Negro", "Black"],
    ["Mestizo", "Mestizo"],
    ["Indígena Norteamericano", "American indigenous people"],
    ["Otros", "Others"],


    ["Descuentos", "Discounts"],
    ["Escuela", "School"],
    ["Privada", "Private"],
    ["Publica", "publica"],
    ["Municipio", "Municipality"],
    ["Transporte", "Transport"],
    ["Ninguno", "None"],
    ["Interno", "Internal"],
    ["Externo", "External"],

    ["Cambiar foto de perfil", "Change profile picture"],

    ["Enfermedades y medicamentos", "Diseases and medications"],
    ["Médico", "Doctor"],
    ["Acomodo razonable", "Reasonable accommodation"],
    ["Impedimentos/Condiciones", "Impediments/conditions"],
    ["Medicamentos", "Medicines"],
    ["Teléfonos", "Phones"],
    ["Trajo Evaluación Profesional", "Brought professional evaluation"],
    ["Enfermedades", "Diseases"],
    ["Recetas", "Prescriptions"],

    ["Religión", "Religion"],
    ["Adventista", "Adventist"],
    ["Bautista", "Baptist"],
    ["Católico", "Catholic"],
    ["Evangélico", "Evangelical"],
    ["Mita", "Mita"],
    ["Metodista", "Methodist"],
    ["Pentecostal", "Pentecostal"],
    ["Luterano", "Lutheran"],
    ["Iglesia", "Church"],
    ["Bautismo", "Baptism"],
    ["Comunión", "Communion"],
    ["Confirmación", "Confirmation"],

    ["Matrícula retenida", "Retained enrollment"],
    ["Razón", "Reason"],

    ["Nombre Completo", "Full name"],
    ["Quien es?", "Who is?"],
    ["Padre", "Father"],
    ["Madre", "Mother"],
    ["Dirección", "Address"],
    ["Ciudad", "City"],
    ["Estado", "State"],
    ["Codigo Postal", "Postal Code"],
    ["Correo electrónico", "Email"],
    ["Teléfono", "Telephone"],
    ["Celular", "Cell"],

    ["Guardar", "Save"],
    ["Atrás", "Go back"]
]);
$accountNumber = $_GET['id'];
$mt = $_GET['pk'];
if (isset($mt)) {
    $student = new Student($mt);
}
$discounts = DB::table("presupuesto")->where('year', $school->info('year'))->orderBy('codigo')->get();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Estudiante");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-fluid px-3">
        <div class="text-center my-5">
            <h1><?= $lang->translation("Información del estudiante") ?> <i class="far fa-id-card"></i></h1>
            <?php if (isset($mt)) : ?>
                <p class="text-muted"><?= strtoupper("$student->nombre $student->apellidos") ?></p>
            <?php endif; ?>
        </div>
        <ul class="nav nav-tabs nav-fill" role="tablist">
            <li class="nav-item" role="presentation"><a class="nav-link active" data-toggle="tab" data-target="#nav-general" type="button" role="tab" aria-controls="nav-general" aria-selected="true"><?= $lang->translation("Información general") ?></a></li>
            <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" data-target="#nav-illness" type="button" role="tab" aria-controls="nav-illness" aria-selected="false"><?= $lang->translation("Enfermedades y medicamentos") ?></a></li>
            <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" data-target="#nav-denomination" type="button" role="tab" aria-controls="nav-denomination" aria-selected="false"><?= $lang->translation("Denominación") ?></a></li>
            <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" data-target="#nav-parents" type="button" role="tab" aria-controls="nav-parents" aria-selected="false"><?= $lang->translation("Padre/Madre biológico") ?></a></li>
            <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" data-target="#nav-enrollment" type="button" role="tab" aria-controls="nav-enrollment" aria-selected="false"><?= $lang->translation("Re-matricula") ?></a></li>
        </ul>
        <form method="post" action="<?= Route::url("/admin/users/accounts/includes/students.php") ?>" enctype="multipart/form-data">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="nav-general" role="tabpanel" aria-labelledby="nav-general-tab">
                    <div class="container-fluid p-3">
                        <div class="row">
                            <div class="col-12">
                                <img src="<?= isset($mt) ? $student->profilePicture() : __NO_PROFILE_PICTURE_STUDENT_MALE ?>" alt="Profile Picture" class="profile-picture img-thumbnail rounded mx-auto d-block" width="250" height="250">
                                <div class="form-group text-center mt-2">
                                    <button id="pictureBtn" type='button' class="btn btn-secondary"><?= $lang->translation("Cambiar foto de perfil") ?></button>
                                    <button id="pictureCancel" type='button' hidden class="btn btn-danger"><i class="fas fa-times"></i></button>
                                    <input type="file" hidden name="picture" id="picture" accept="image/jpg,image/png,image/gif,image/jpeg">
                                </div>
                            </div>
                            <div class="card col-12 col-lg-6 p-3 rounded-0">
                                <h2 class="card-title text-center"><?= $lang->translation("Información general") ?></h2>
                                <div class="row">
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="ss"><?= $lang->translation("Seguro social") ?></label>
                                        <input type="text" class="form-control socialSecurity" name="ss" id="ss" required value="<?= $student->ss ?>">
                                        <div class="invalid-feedback">
                                            <?= $lang->translation("Este seguro social ya existe") ?>
                                        </div>
                                        <div class="valid-feedback">
                                            <?= $lang->translation("Este seguro social esta disponible") ?>
                                        </div>
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="grade"><?= $lang->translation("Grado") ?></label>
                                        <input type="text" class="form-control" name="grade" id="grade" required value="<?= $student->grado ?>">
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="name"><?= $lang->translation("Nombre") ?></label>
                                        <input type="text" class="form-control" name="name" id="name" required value="<?= $student->nombre ?>">
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="surnames"><?= $lang->translation("Apellidos") ?></label>
                                        <input type="text" class="form-control" name="surnames" id="surnames" required value="<?= $student->apellidos ?>">
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="reference"><?= $lang->translation("Referencia") ?></label>
                                        <input type="text" class="form-control" name="reference" id="reference" value="<?= $student->nuref ?>">
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="accountNumber"><?= $lang->translation("Número de cuenta") ?></label>
                                        <input type="text" class="form-control" name="accountNumber" id="accountNumber" value="<?= $accountNumber ?>" readonly>
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="dateOfBirth"><?= $lang->translation("Fecha de nacimiento") ?></label>
                                        <input type="date" class="form-control" name="dateOfBirth" id="dateOfBirth" required value="<?= $student->fecha ?>">
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="enrollmentDate"><?= $lang->translation("Fecha de matrícula") ?></label>
                                        <input type="date" class="form-control" name="enrollmentDate" id="enrollmentDate" required value="<?= $student->fecha_matri ?>">
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="placeOfBirth"><?= $lang->translation("Lugar de nacimiento") ?></label>
                                        <input type="text" class="form-control" name="placeOfBirth" id="placeOfBirth" value="<?= $student->lugar_nac ?>">
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="gender"><?= $lang->translation("Género") ?></label>
                                        <select name="gender" id="gender" class="form-control" required>
                                            <option value=""><?= $lang->translation("Seleccionar") ?></option>
                                            <option <?= $student->genero === 'M' || $student->genero === "2" ? 'selected' : '' ?> value="M"><?= $lang->translation("Masculino") ?></option>
                                            <option <?= $student->genero === 'F' || $student->genero === "1" ? 'selected' : '' ?> value="F"><?= $lang->translation("Femenino") ?></option>
                                        </select>
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="liveWith"><?= $lang->translation("Vive con") ?></label>
                                        <select name="liveWith" id="liveWith" class="form-control" required>
                                            <option value=""><?= $lang->translation("Seleccionar") ?></option>
                                            <option <?= $student->vivecon === 'Ambos Padres' ? 'selected' : '' ?> value="Ambos Padres"><?= $lang->translation("Ambos padres") ?></option>
                                            <option <?= $student->vivecon === 'Madre' ? 'selected' : '' ?> value="Madre"><?= $lang->translation("Madre") ?></option>
                                            <option <?= $student->vivecon === 'Padre' ? 'selected' : '' ?> value="Padre"><?= $lang->translation("Padre") ?></option>
                                            <option <?= $student->vivecon === 'Encargado' ? 'selected' : '' ?> value="Encargado"><?= $lang->translation("Encargado") ?></option>
                                        </select>
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="new"><?= $lang->translation("Nuevo") ?>?</label>
                                        <select name="new" id="new" class="form-control" required>
                                            <option <?= $student->nuevo === 'Si' ? 'selected' : '' ?> value="Si"><?= $lang->translation("Si") ?></option>
                                            <option <?= $student->nuevo === 'No' ? 'selected' : '' ?> value="No">No</option>
                                            <option <?= $student->nuevo === 'M1' ? 'selected' : '' ?> value="M1">M-1</option>
                                            <option <?= $student->nuevo === 'M2' ? 'selected' : '' ?> value="M2">M-2</option>
                                            <option <?= $student->nuevo === 'M3' ? 'selected' : '' ?> value="M3">M-3</option>
                                            <option <?= $student->nuevo === 'R1' ? 'selected' : '' ?> value="R1">R-1</option>
                                            <option <?= $student->nuevo === 'R2' ? 'selected' : '' ?> value="R2">R-2</option>
                                            <option <?= $student->nuevo === 'R3' ? 'selected' : '' ?> value="R3">R-3</option>
                                            <option <?= $student->nuevo === 'R4' ? 'selected' : '' ?> value="R4">R-4</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="cell"><?= $lang->translation("Celular") ?></label>
                                        <input type="tel" value="<?= $student->cel ?>" class="form-control phone" id="cell" name="cell" />
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="cellCompany"><?= $lang->translation("Compañia telefonica") ?></label>
                                        <select id="cellCompany" class="form-control" name="cellCompany">
                                            <option value=""><?= $lang->translation("Seleccionar") ?></option>
                                            <?php foreach (Util::phoneCompanies() as $company) : ?>
                                                <option <?= $student->comp === $company ? 'selected=""' : '' ?> value="<?= $company ?>"><?= $company ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="race"><?= $lang->translation("Raza") ?></label>
                                        <select name="race" class="form-control">
                                            <option value=""><?= $lang->translation("Seleccionar") ?></option>
                                            <option <?= $student->raza === '1' ? 'selected' : '' ?> value="1"><?= $lang->translation("Blanco") ?></option>
                                            <option <?= $student->raza === '2' ? 'selected' : '' ?> value="2"><?= $lang->translation("Negro") ?></option>
                                            <option <?= $student->raza === '3' ? 'selected' : '' ?> value="3"><?= $lang->translation("Mestizo") ?></option>
                                            <option <?= $student->raza === '4' ? 'selected' : '' ?> value="4"><?= $lang->translation("Indígena Norteamericano") ?></option>
                                            <option <?= $student->raza === '5' ? 'selected' : '' ?> value="5"><?= $lang->translation("Otros") ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card col-12 col-lg-6 p-3 rounded-0">
                                <h2 class="card-title text-center"><?= $lang->translation("Descuentos") ?></h2>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <select name="discount1" class="form-control">
                                            <option value=""><?= $lang->translation("Seleccionar") ?></option>
                                            <?php foreach ($discounts as $discount) : ?>
                                                <option <?= $student->desc1 === $discount->codigo ? 'selected' : '' ?>value="<?= $discount->codigo ?>"><?= $discount->descripcion ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-6">
                                        <input type="text" class="form-control" name="men" value="<?= $student->desc_men ?>">
                                    </div>
                                    <div class="form-group col-6">
                                        <input type="text" class="form-control" name="cdb1" value="<?= $student->cdb1 ?>">
                                    </div>
                                    <div class="form-group col-12">
                                        <select name="discount2" class="form-control">
                                            <option value=""><?= $lang->translation("Seleccionar") ?></option>
                                            <?php foreach ($discounts as $discount) : ?>
                                                <option <?= $student->desc2 === $discount->codigo ? 'selected' : '' ?>value="<?= $discount->codigo ?>"><?= $discount->descripcion ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-6">
                                        <input type="text" class="form-control" name="mat" value="<?= $student->desc_mat ?>">
                                    </div>
                                    <div class="form-group col-6">
                                        <input type="text" class="form-control" name="cdb2" value="<?= $student->cdb2 ?>">
                                    </div>
                                    <div class="form-group col-12">
                                        <select name="discount3" class="form-control">
                                            <option value=""><?= $lang->translation("Seleccionar") ?></option>
                                            <?php foreach ($discounts as $discount) : ?>
                                                <option <?= $student->desc3 === $discount->codigo ? 'selected' : '' ?>value="<?= $discount->codigo ?>"><?= $discount->descripcion ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-6">
                                        <input type="text" class="form-control" name="otro1" value="<?= $student->desc_otro1 ?>">
                                    </div>
                                    <div class="form-group col-6">
                                        <input type="text" class="form-control" name="cdb3" value="<?= $student->cdb3 ?>">
                                    </div>
                                    <div class="form-group col-12 row">
                                        <div class="col-3"><label><?= $lang->translation("Escuela") ?>:</label></div>
                                        <div class="col">
                                            <select name="school" class="form-control">
                                                <option value=""><?= $lang->translation("Seleccionar") ?></option>
                                                <option <?= $student->pop === '1' ? 'selected' : '' ?> value="1"><?= $lang->translation("Privada") ?></option>
                                                <option <?= $student->pop === '2' ? 'selected' : '' ?> value="2"><?= $lang->translation("Publica") ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-12">
                                        <input type="text" class="form-control" name="colpro" value="<?= $student->colpro ?>">
                                    </div>
                                    <div class="form-group col-12 row">
                                        <div class="col-3"><label><?= $lang->translation("Municipio") ?>:</label></div>
                                        <div class="col">
                                            <input type="text" class="form-control" name="municipio" value="<?= $student->municipio ?>">
                                        </div>
                                    </div>
                                    <div class="form-group col-12 row">
                                        <div class="col-3"><label><?= $lang->translation("Transporte") ?>:</label></div>
                                        <div class="col">
                                            <select name="transporte" class="form-control">
                                                <option value=""><?= $lang->translation("Ninguno") ?></option>
                                                <option <?= $student->transporte === '1' ? 'selected' : '' ?> value="1"><?= $lang->translation("Interno") ?></option>
                                                <option <?= $student->transporte === '2' ? 'selected' : '' ?> value="2"><?= $lang->translation("Externo") ?></option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-illness" role="tabpanel" aria-labelledby="nav-illness-tab">
                    <div class="container-fluid p-3">
                        <div class="card col-12 offset-md-2 col-md-8 p-3 rounded-0">
                            <h2 class="card-title text-center"><?= $lang->translation("Enfermedades y medicamentos") ?></h2>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="doctor" class="text-center"><?= $lang->translation("Médico") ?></label>
                                        <input type="text" class="form-control" name="doctor" id="doctor" value="<?= $student->medico ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="accommodation" class="text-center"><?= $lang->translation("Acomodo razonable") ?></label>
                                        <select class="form-control" name="accommodation" id="accommodation">
                                            <option value=""><?= $lang->translation("Seleccionar") ?></option>
                                            <option <?= $student->acomodo === 'Si' ? 'selected' : '' ?> value="Si"><?= $lang->translation("Si") ?></option>
                                            <option <?= $student->acomodo === 'No' ? 'selected' : '' ?> value="No">No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label><?= $lang->translation("Impedimentos/Condiciones") ?></label>
                                        <?php for ($i = 1; $i <= 4; $i++) : ?>
                                            <input type="text" class="form-control" name="<?= "imp$i" ?>" value="<?= $student->{"imp$i"} ?>">
                                        <?php endfor ?>
                                    </div>
                                    <div class="form-group">
                                        <label><?= $lang->translation("Medicamentos") ?></label>
                                        <?php for ($i = 1; $i <= 4; $i++) : ?>
                                            <input type="text" class="form-control" name="<?= "med$i" ?>" value="<?= $student->{"med$i"} ?>">
                                        <?php endfor ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <label><?= $lang->translation("Teléfonos") ?></label>
                                        </div>
                                        <div class="col">
                                            <input type="tel" class="form-control phone" name="tel1" value="<?= $student->tel1 ?>">
                                        </div>
                                        <div class="col">
                                            <input type="tel" class="form-control phone" name="tel2" value="<?= $student->tel2 ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="professionalEvaluation" class="text-center"><?= $lang->translation("Trajo Evaluación Profesional") ?></label>
                                        <select class="form-control" name="professionalEvaluation" id="professionalEvaluation">
                                            <option value=""><?= $lang->translation("Seleccionar") ?></option>
                                            <option <?= $student->trajo === 'Si' ? 'selected' : '' ?> value="Si"><?= $lang->translation("Si") ?></option>
                                            <option <?= $student->trajo === 'No' ? 'selected' : '' ?> value="No">No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label><?= $lang->translation("Enfermedades") ?></label>
                                        <?php for ($i = 1; $i <= 4; $i++) : ?>
                                            <input type="text" class="form-control" name="<?= "enf$i" ?>" value="<?= $student->{"enf$i"} ?>">
                                        <?php endfor ?>
                                    </div>
                                    <div class="form-group">
                                        <label><?= $lang->translation("Recetas") ?></label>
                                        <?php for ($i = 1; $i <= 4; $i++) : ?>
                                            <input type="text" class="form-control" name="<?= "rec$i" ?>" value="<?= $student->{"rec$i"} ?>">
                                        <?php endfor ?>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-denomination" role="tabpanel" aria-labelledby="nav-denomination-tab">
                    <div class="container-fluid p-3">
                        <div class="card col-12 offset-md-3 col-md-6 p-3 rounded-0">
                            <h2 class="card-title text-center"><?= $lang->translation("Denominación") ?></h2>

                            <div class="form-group">
                                <label for="religion" class="text-center"><?= $lang->translation("Religión") ?></label>
                                <select class="form-control" name="religion" id="religion">
                                    <option value=""><?= $lang->translation("Seleccionar") ?></option>
                                    <option <?= $student->religion === '1' ? 'selected' : '' ?> value="1"><?= $lang->translation("Adventista") ?></option>
                                    <option <?= $student->religion === '2' ? 'selected' : '' ?> value="2"><?= $lang->translation("Bautista") ?></option>
                                    <option <?= $student->religion === '3' ? 'selected' : '' ?> value="3"><?= $lang->translation("Católico") ?></option>
                                    <option <?= $student->religion === '4' ? 'selected' : '' ?> value="4"><?= $lang->translation("Evangélico") ?></option>
                                    <option <?= $student->religion === '5' ? 'selected' : '' ?> value="5"><?= $lang->translation("Mita") ?></option>
                                    <option <?= $student->religion === '6' ? 'selected' : '' ?> value="6"><?= $lang->translation("Metodista") ?></option>
                                    <option <?= $student->religion === '7' ? 'selected' : '' ?> value="7"><?= $lang->translation("Pentecostal") ?></option>
                                    <option <?= $student->religion === '8' ? 'selected' : '' ?> value="8"><?= $lang->translation("Luterano") ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="church"><?= $lang->translation("Iglesia") ?></label>
                                <input type="text" class="form-control" name="church" id="church" value="<?= $student->iglesia ?>">
                            </div>
                            <div class="form-group row">
                                <label class="col-12"><?= $lang->translation("Bautismo") ?></label>
                                <div class="col">
                                    <input type="text" class="form-control" name="baptism" id="baptism" value="<?= $student->bau ?>">
                                </div>
                                <div class="col">
                                    <input class="form-control" type="date" name="baptismDate" value="<?= $student->fbau ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-12"><?= $lang->translation("Comunión") ?></label>
                                <div class="col">
                                    <input type="text" class="form-control" name="communion" id="communion" value="<?= $student->com ?>">
                                </div>
                                <div class="col">
                                    <input class="form-control" type="date" name="communionDate" value="<?= $student->fcom ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-12"><?= $lang->translation("Confirmación") ?></label>
                                <div class="col">
                                    <input type="text" class="form-control" name="confirmation" id="confirmation" value="<?= $student->con ?>">
                                </div>
                                <div class="col">
                                    <input class="form-control" type="date" name="confirmationDate" value="<?= $student->fcon ?>">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-parents" role="tabpanel" aria-labelledby="nav-parents-tab">
                    <div class="container-fluid p-3">
                        <div class="card col-12 offset-md-3 col-md-6 p-3 rounded-0">
                            <h2 class="card-title text-center"><?= $lang->translation("Padre/Madre biológico") ?></h2>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-6">
                                        <label for="biologicalParent"><?= $lang->translation("Quien es?") ?></label>
                                        <select name="biologicalParent" id="biologicalParent" class="form-control">
                                            <option value=""><?= $lang->translation("Seleccionar") ?></option>
                                            <option <?= $student->padre === 'P' ? 'selected' : '' ?> value="P"><?= $lang->translation("Padre") ?></option>
                                            <option <?= $student->padre === 'M' ? 'selected' : '' ?> value="M"><?= $lang->translation("Madre") ?></option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label for="biologicalParentName"><?= $lang->translation("Nombre Completo") ?></label>
                                        <input type="text" class="form-control" name='biologicalParentName' id="biologicalParentName" value="<?= $student->nombre_padre ?>">
                                    </div>

                                </div>
                            </div>
                            <label class="my-2"><?= $lang->translation("Dirección") ?></label>
                            <div class="form-row">
                                <div class="col-12">
                                    <input class="form-control" type="text" placeholder="<?= $lang->translation("Dirección") ?> 1" name="dir1" value="<?= $student->dir1 ?>">
                                </div>
                                <div class="col-12 mt-1">
                                    <input class="form-control" type="text" placeholder="<?= $lang->translation("Dirección") ?> 2" name="dir2" value="<?= $student->dir2 ?>">
                                </div>
                                <div class="col-5 mt-1">
                                    <input class="form-control" type="text" placeholder="<?= $lang->translation("Ciudad") ?>" name="city" value="<?= $student->pueblo ?>">
                                </div>
                                <div class="col-3 mt-1">
                                    <input class="form-control" type="text" placeholder="<?= $lang->translation("Estado") ?>" name="state" value="<?= $student->estado ?>">
                                </div>
                                <div class="col-4 mt-1">
                                    <input class="form-control" type="text" placeholder="<?= $lang->translation("Codigo Postal") ?>" name="zip" value="<?= $student->zip ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="biologicalParentEmail"><?= $lang->translation("Correo electrónico") ?></label>
                                <input type="email" class="form-control" name='biologicalParentEmail' id="biologicalParentEmail" value="<?= $student->emailp ?>">
                            </div>
                            <div class="form-group">
                                <label for="biologicalParentPhone"><?= $lang->translation("Teléfono") ?></label>
                                <input type="tel" class="form-control phone" name='biologicalParentPhone' id="biologicalParentPhone" value="<?= $student->telp ?>">
                            </div>
                            <div class="form-group">
                                <label for="biologicalParentCell"><?= $lang->translation("Celular") ?></label>
                                <input type="tel" class="form-control phone" name='biologicalParentCell' id="biologicalParentCell" value="<?= $student->celp ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-enrollment" role="tabpanel" aria-labelledby="nav-enrollment-tab">
                    <div class="container-fluid p-3">
                        <div class="card col-12 offset-md-3 col-md-6 p-3 rounded-0">
                            <h2 class="card-title text-center"><?= $lang->translation("Re-matricula") ?></h2>
                            <div class="form-group">
                                <label for="retainedEnrollment" class="text-center"><?= $lang->translation("Matrícula retenida") ?></label>
                                <select class="form-control" name="retainedEnrollment" id="retainedEnrollment">
                                    <option value=""><?= $lang->translation("Seleccionar") ?></option>
                                    <option <?= $student->mat_retenida === 'Si' ? 'selected' : '' ?> value="Si"><?= $lang->translation("Si") ?></option>
                                    <option <?= $student->mat_retenida === 'No' ? 'selected' : '' ?> value="No">No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="reason" class="text-center"><?= $lang->translation("Razón") ?></label>
                                <textarea class="form-control" name="reason" id="reason"><?= $student->alias ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center p-5">
                <button id="submit" name="<?= isset($mt) ? 'edit' : 'save' ?>" class="btn btn-primary" <?= isset($mt) ? '' : 'disabled' ?>><?= $lang->translation("Guardar") ?></button>
                <a href="<?= Route::url("/admin/users/accounts/index.php?student=$accountNumber") ?>" class="btn btn-secondary"><?= $lang->translation("Atrás") ?></a>
            </div>
            <input type="hidden" name="pk" value="<?= $mt ?>" />
        </form>
    </div>

    <?php
    $jqMask = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>


</html>