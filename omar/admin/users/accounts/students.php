<?php
require_once '../../../app.php';

use App\Enums\Gender;
use App\Models\School;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use App\Models\Student;
use Illuminate\Database\Capsule\Manager;

Session::is_logged();

$accountNumber = $_GET['id'] ?? null;
$mt = $_GET['pk'] ?? null;

if ($mt) {
    $student = Student::find($mt);
}
$discounts = Manager::table("presupuesto")->where('year', School::admin()->first()->year())->orderBy('codigo')->get();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Estudiante");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-fluid px-3">
        <div class="text-center my-5">
            <h1><?= __("Información del estudiante") ?> <i class="far fa-id-card"></i></h1>
            <?php if (isset($student)): ?>
                <p class="text-muted"><?= strtoupper("$student->nombre $student->apellidos") ?></p>
            <?php endif; ?>
        </div>
        <ul class="nav nav-tabs nav-fill" role="tablist">
            <li class="nav-item" role="presentation"><a class="nav-link active" data-toggle="tab" data-target="#nav-general" type="button" role="tab" aria-controls="nav-general" aria-selected="true"><?= __("Información general") ?></a></li>
            <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" data-target="#nav-illness" type="button" role="tab" aria-controls="nav-illness" aria-selected="false"><?= __("Enfermedades y medicamentos") ?></a></li>
            <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" data-target="#nav-denomination" type="button" role="tab" aria-controls="nav-denomination" aria-selected="false"><?= __("Denominación") ?></a></li>
            <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" data-target="#nav-parents" type="button" role="tab" aria-controls="nav-parents" aria-selected="false"><?= __("Padre/Madre biológico") ?></a></li>
            <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" data-target="#nav-enrollment" type="button" role="tab" aria-controls="nav-enrollment" aria-selected="false"><?= __("Re-matricula") ?></a></li>
        </ul>
        <form method="post" action="<?= Route::url("/admin/users/accounts/includes/students.php") ?>" enctype="multipart/form-data">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="nav-general" role="tabpanel" aria-labelledby="nav-general-tab">
                    <div class="container-fluid p-3">
                        <div class="row">
                            <div class="col-12">
                                <img src="<?= isset($student) ? $student->profile_picture : __NO_PROFILE_PICTURE_STUDENT_MALE ?>" alt="Profile Picture" class="profile-picture img-thumbnail rounded mx-auto d-block" width="250" height="250">
                                <div class="form-group text-center mt-2">
                                    <button id="pictureBtn" type='button' class="btn btn-secondary"><?= __("Cambiar foto de perfil") ?></button>
                                    <button id="pictureCancel" type='button' hidden class="btn btn-danger"><i class="fas fa-times"></i></button>
                                    <input type="file" hidden name="picture" id="picture" accept="image/jpg,image/png,image/gif,image/jpeg">
                                </div>
                            </div>
                            <div class="card col-12 col-lg-6 p-3 rounded-0">
                                <h2 class="card-title text-center"><?= __("Información general") ?></h2>
                                <div class="row">
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="ss"><?= __("Seguro social") ?></label>
                                        <input type="text" class="form-control socialSecurity" name="ss" id="ss" required value="<?= isset($student) ? $student->ss : '' ?>">
                                        <div class="invalid-feedback">
                                            <?= __("Este seguro social ya existe") ?>
                                        </div>
                                        <div class="valid-feedback">
                                            <?= __("Este seguro social esta disponible") ?>
                                        </div>
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="grade"><?= __("Grado") ?></label>
                                        <input type="text" class="form-control" name="grade" id="grade" required value="<?= isset($student) ? $student->grado : '' ?>">
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="name"><?= __("Nombre") ?></label>
                                        <input type="text" class="form-control" name="name" id="name" required value="<?= isset($student) ? $student->nombre : '' ?>">
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="surnames"><?= __("Apellidos") ?></label>
                                        <input type="text" class="form-control" name="surnames" id="surnames" required value="<?= isset($student) ? $student->apellidos : '' ?>">
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="reference"><?= __("Referencia") ?></label>
                                        <input type="text" class="form-control" name="reference" id="reference" value="<?= isset($student) ? $student->nuref : '' ?>">
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="accountNumber"><?= __("Número de cuenta") ?></label>
                                        <input type="text" class="form-control" name="accountNumber" id="accountNumber" value="<?= $accountNumber ?>" readonly>
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="dateOfBirth"><?= __("Fecha de nacimiento") ?></label>
                                        <input type="date" class="form-control" name="dateOfBirth" id="dateOfBirth" required value="<?= isset($student) ? $student->fecha->format('Y-m-d') : '' ?>">
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="enrollmentDate"><?= __("Fecha de matrícula") ?></label>
                                        <input type="date" class="form-control" name="enrollmentDate" id="enrollmentDate" required value="<?= isset($student) ? $student->fecha_matri->format('Y-m-d') : '' ?>">
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="placeOfBirth"><?= __("Lugar de nacimiento") ?></label>
                                        <input type="text" class="form-control" name="placeOfBirth" id="placeOfBirth" value="<?= isset($student) ? $student->lugar_nac : '' ?>">
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="gender"><?= __("Género") ?></label>
                                        <select name="gender" id="gender" class="form-control" required>
                                            <?php foreach (Gender::cases() as $gender): ?>
                                                <option <?= isset($student) && $student->genero === $gender->value ? 'selected' : '' ?> value="<?= $gender->value ?>"><?= $gender->label() ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="liveWith"><?= __("Vive con") ?></label>
                                        <select name="liveWith" id="liveWith" class="form-control" required>
                                            <option value=""><?= __("Seleccionar") ?></option>
                                            <option <?= isset($student) && $student->vivecon === 'Ambos Padres' ? 'selected' : '' ?> value="Ambos Padres"><?= __("Ambos padres") ?></option>
                                            <option <?= isset($student) && $student->vivecon === 'Madre' ? 'selected' : '' ?> value="Madre"><?= __("Madre") ?></option>
                                            <option <?= isset($student) && $student->vivecon === 'Padre' ? 'selected' : '' ?> value="Padre"><?= __("Padre") ?></option>
                                            <option <?= isset($student) && $student->vivecon === 'Encargado' ? 'selected' : '' ?> value="Encargado"><?= __("Encargado") ?></option>
                                        </select>
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="new"><?= __("Nuevo") ?>?</label>
                                        <select name="new" id="new" class="form-control" required>
                                            <option <?= isset($student) && $student->nuevo === 'Si' ? 'selected' : '' ?> value="Si"><?= __("Si") ?></option>
                                            <option <?= isset($student) && $student->nuevo === 'No' ? 'selected' : '' ?> value="No">No</option>
                                            <option <?= isset($student) && $student->nuevo === 'M1' ? 'selected' : '' ?> value="M1">M-1</option>
                                            <option <?= isset($student) && $student->nuevo === 'M2' ? 'selected' : '' ?> value="M2">M-2</option>
                                            <option <?= isset($student) && $student->nuevo === 'M3' ? 'selected' : '' ?> value="M3">M-3</option>
                                            <option <?= isset($student) && $student->nuevo === 'R1' ? 'selected' : '' ?> value="R1">R-1</option>
                                            <option <?= isset($student) && $student->nuevo === 'R2' ? 'selected' : '' ?> value="R2">R-2</option>
                                            <option <?= isset($student) && $student->nuevo === 'R3' ? 'selected' : '' ?> value="R3">R-3</option>
                                            <option <?= isset($student) && $student->nuevo === 'R4' ? 'selected' : '' ?> value="R4">R-4</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="cell"><?= __("Celular") ?></label>
                                        <input type="tel" value="<?= isset($student) ? $student->cel : '' ?>" class="form-control phone" id="cell" name="cell" />
                                    </div>
                                    <div class="form-group col-12 col-lg-6">
                                        <label for="cellCompany"><?= __("Compañia telefonica") ?></label>
                                        <select id="cellCompany" class="form-control" name="cellCompany">
                                            <option value=""><?= __("Seleccionar") ?></option>
                                            <?php foreach (Util::phoneCompanies() as $company): ?>
                                                <option <?= isset($student) && $student->comp === $company ? 'selected=""' : '' ?> value="<?= $company ?>"><?= $company ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="race"><?= __("Raza") ?></label>
                                        <select name="race" class="form-control">
                                            <option <?= isset($student) && $student->raza === '1' ? 'selected' : '' ?> value="1"><?= __("Blanco") ?></option>
                                            <option <?= isset($student) && $student->raza === '2' ? 'selected' : '' ?> value="2"><?= __("Negro") ?></option>
                                            <option <?= isset($student) && $student->raza === '3' ? 'selected' : '' ?> value="3"><?= __("Mestizo") ?></option>
                                            <option <?= isset($student) && $student->raza === '4' ? 'selected' : '' ?> value="4"><?= __("Indígena Norteamericano") ?></option>
                                            <option <?= isset($student) && $student->raza === '5' ? 'selected' : '' ?> value="5"><?= __("Otros") ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card col-12 col-lg-6 p-3 rounded-0">
                                <h2 class="card-title text-center"><?= __("Descuentos") ?></h2>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <select name="discount1" class="form-control">
                                            <option value=""><?= __("Seleccionar") ?></option>
                                            <?php foreach ($discounts as $discount): ?>
                                                <option <?= isset($student) && $student->desc1 === strval($discount->codigo) ? 'selected' : '' ?> value="<?= $discount->codigo ?>"><?= $discount->descripcion ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-6">
                                        <input type="text" class="form-control" name="men" value="<?= isset($student) ? $student->desc_men : '' ?>">
                                    </div>
                                    <div class="form-group col-6">
                                        <input type="text" class="form-control" name="cdb1" value="<?= isset($student) ? $student->cdb1 : '' ?>">
                                    </div>
                                    <div class="form-group col-12">
                                        <select name="discount2" class="form-control">
                                            <option value=""><?= __("Seleccionar") ?></option>
                                            <?php foreach ($discounts as $discount): ?>
                                                <option <?= isset($student) && $student->desc2 === strval($discount->codigo) ? 'selected' : '' ?> value="<?= $discount->codigo ?>"><?= $discount->descripcion ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-6">
                                        <input type="text" class="form-control" name="mat" value="<?= isset($student) ? $student->desc_mat : '' ?>">
                                    </div>
                                    <div class="form-group col-6">
                                        <input type="text" class="form-control" name="cdb2" value="<?= isset($student) ? $student->cdb2 : '' ?>">
                                    </div>
                                    <div class="form-group col-12">
                                        <select name="discount3" class="form-control">
                                            <option value=""><?= __("Seleccionar") ?></option>
                                            <?php foreach ($discounts as $discount): ?>
                                                <option <?= isset($student) && $student->desc3 === strval($discount->codigo) ? 'selected' : '' ?> value="<?= $discount->codigo ?>"><?= $discount->descripcion ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-6">
                                        <input type="text" class="form-control" name="otro1" value="<?= isset($student) ? $student->desc_otro1 : '' ?>">
                                    </div>
                                    <div class="form-group col-6">
                                        <input type="text" class="form-control" name="cdb3" value="<?= isset($student) ? $student->cdb3 : '' ?>">
                                    </div>
                                    <div class="form-group col-12 row">
                                        <div class="col-3"><label><?= __("Escuela") ?>:</label></div>
                                        <div class="col">
                                            <select name="school" class="form-control">
                                                <option value=""><?= __("Seleccionar") ?></option>
                                                <option <?= isset($student) && $student->pop === '1' ? 'selected' : '' ?> value="1"><?= __("Privada") ?></option>
                                                <option <?= isset($student) && $student->pop === '2' ? 'selected' : '' ?> value="2"><?= __("Publica") ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-12">
                                        <input type="text" class="form-control" name="colpro" value="<?= isset($student) ? $student->colpro : '' ?>">
                                    </div>
                                    <div class="form-group col-12 row">
                                        <div class="col-3"><label><?= __("Municipio") ?>:</label></div>
                                        <div class="col">
                                            <input type="text" class="form-control" name="municipio" value="<?= isset($student) ? $student->municipio : '' ?>">
                                        </div>
                                    </div>
                                    <div class="form-group col-12 row">
                                        <div class="col-3"><label><?= __("Transporte") ?>:</label></div>
                                        <div class="col">
                                            <select name="transporte" class="form-control">
                                                <option value=""><?= __("Ninguno") ?></option>
                                                <option <?= isset($student) && $student->transporte === 1 ? 'selected' : '' ?> value="1"><?= __("Interno") ?></option>
                                                <option <?= isset($student) && $student->transporte === 2 ? 'selected' : '' ?> value="2"><?= __("Externo") ?></option>
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
                            <h2 class="card-title text-center"><?= __("Enfermedades y medicamentos") ?></h2>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="doctor" class="text-center"><?= __("Médico") ?></label>
                                        <input type="text" class="form-control" name="doctor" id="doctor" value="<?= isset($student) ? $student->medico : '' ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="accommodation" class="text-center"><?= __("Acomodo razonable") ?></label>
                                        <select class="form-control" name="accommodation" id="accommodation">
                                            <option value=""><?= __("Seleccionar") ?></option>
                                            <option <?= isset($student) && $student->acomodo === 'Si' ? 'selected' : '' ?> value="Si"><?= __("Si") ?></option>
                                            <option <?= isset($student) && $student->acomodo === 'No' ? 'selected' : '' ?> value="No">No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label><?= __("Impedimentos/Condiciones") ?></label>
                                        <?php for ($i = 1; $i <= 4; $i++): ?>
                                            <input type="text" class="form-control" name="<?= "imp$i" ?>" value="<?= isset($student) ? $student->{"imp$i"} : '' ?>">
                                        <?php endfor ?>
                                    </div>
                                    <div class="form-group">
                                        <label><?= __("Medicamentos") ?></label>
                                        <?php for ($i = 1; $i <= 4; $i++): ?>
                                            <input type="text" class="form-control" name="<?= "med$i" ?>" value="<?= isset($student) ? $student->{"med$i"} : '' ?>">
                                        <?php endfor ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <label><?= __("Teléfonos") ?></label>
                                        </div>
                                        <div class="col">
                                            <input type="tel" class="form-control phone" name="tel1" value="<?= isset($student) ? $student->tel1 : '' ?>">
                                        </div>
                                        <div class="col">
                                            <input type="tel" class="form-control phone" name="tel2" value="<?= isset($student) ? $student->tel2 : '' ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="professionalEvaluation" class="text-center"><?= __("Trajo Evaluación Profesional") ?></label>
                                        <select class="form-control" name="professionalEvaluation" id="professionalEvaluation">
                                            <option value=""><?= __("Seleccionar") ?></option>
                                            <option <?= isset($student) && $student->trajo === 'Si' ? 'selected' : '' ?> value="Si"><?= __("Si") ?></option>
                                            <option <?= isset($student) && $student->trajo === 'No' ? 'selected' : '' ?> value="No">No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label><?= __("Enfermedades") ?></label>
                                        <?php for ($i = 1; $i <= 4; $i++): ?>
                                            <input type="text" class="form-control" name="<?= "enf$i" ?>" value="<?= isset($student) ? $student->{"enf$i"} : '' ?>">
                                        <?php endfor ?>
                                    </div>
                                    <div class="form-group">
                                        <label><?= __("Recetas") ?></label>
                                        <?php for ($i = 1; $i <= 4; $i++): ?>
                                            <input type="text" class="form-control" name="<?= "rec$i" ?>" value="<?= isset($student) ? $student->{"rec$i"} : '' ?>">
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
                            <h2 class="card-title text-center"><?= __("Denominación") ?></h2>

                            <div class="form-group">
                                <label for="religion" class="text-center"><?= __("Religión") ?></label>
                                <select class="form-control" name="religion" id="religion">
                                    <option value=""><?= __("Seleccionar") ?></option>
                                    <option <?= isset($student) && $student->religion === '1' ? 'selected' : '' ?> value="1"><?= __("Adventista") ?></option>
                                    <option <?= isset($student) && $student->religion === '2' ? 'selected' : '' ?> value="2"><?= __("Bautista") ?></option>
                                    <option <?= isset($student) && $student->religion === '3' ? 'selected' : '' ?> value="3"><?= __("Católico") ?></option>
                                    <option <?= isset($student) && $student->religion === '4' ? 'selected' : '' ?> value="4"><?= __("Evangélico") ?></option>
                                    <option <?= isset($student) && $student->religion === '5' ? 'selected' : '' ?> value="5"><?= __("Mita") ?></option>
                                    <option <?= isset($student) && $student->religion === '6' ? 'selected' : '' ?> value="6"><?= __("Metodista") ?></option>
                                    <option <?= isset($student) && $student->religion === '7' ? 'selected' : '' ?> value="7"><?= __("Pentecostal") ?></option>
                                    <option <?= isset($student) && $student->religion === '8' ? 'selected' : '' ?> value="8"><?= __("Luterano") ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="church"><?= __("Iglesia") ?></label>
                                <input type="text" class="form-control" name="church" id="church" value="<?= isset($student) ? $student->iglesia : '' ?>">
                            </div>
                            <div class="form-group row">
                                <label class="col-12"><?= __("Bautismo") ?></label>
                                <div class="col">
                                    <input type="text" class="form-control" name="baptism" id="baptism" value="<?= isset($student) ? $student->bau : '' ?>">
                                </div>
                                <div class="col">
                                    <input class="form-control" type="date" name="baptismDate" value="<?= isset($student) ? $student->fbau : '' ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-12"><?= __("Comunión") ?></label>
                                <div class="col">
                                    <input type="text" class="form-control" name="communion" id="communion" value="<?= isset($student) ? $student->com : '' ?>">
                                </div>
                                <div class="col">
                                    <input class="form-control" type="date" name="communionDate" value="<?= isset($student) ? $student->fcom : '' ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-12"><?= __("Confirmación") ?></label>
                                <div class="col">
                                    <input type="text" class="form-control" name="confirmation" id="confirmation" value="<?= isset($student) ? $student->con : '' ?>">
                                </div>
                                <div class="col">
                                    <input class="form-control" type="date" name="confirmationDate" value="<?= isset($student) ? $student->fcon : '' ?>">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-parents" role="tabpanel" aria-labelledby="nav-parents-tab">
                    <div class="container-fluid p-3">
                        <div class="card col-12 offset-md-3 col-md-6 p-3 rounded-0">
                            <h2 class="card-title text-center"><?= __("Padre/Madre biológico") ?></h2>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-6">
                                        <label for="biologicalParent"><?= __("Quien es?") ?></label>
                                        <select name="biologicalParent" id="biologicalParent" class="form-control">
                                            <option value=""><?= __("Seleccionar") ?></option>
                                            <option <?= isset($student) && $student->padre === 'P' ? 'selected' : '' ?> value="P"><?= __("Padre") ?></option>
                                            <option <?= isset($student) && $student->padre === 'M' ? 'selected' : '' ?> value="M"><?= __("Madre") ?></option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label for="biologicalParentName"><?= __("Nombre Completo") ?></label>
                                        <input type="text" class="form-control" name='biologicalParentName' id="biologicalParentName" value="<?= isset($student) ? $student->nombre_padre : '' ?>">
                                    </div>

                                </div>
                            </div>
                            <label class="my-2"><?= __("Dirección") ?></label>
                            <div class="form-row">
                                <div class="col-12">
                                    <input class="form-control" type="text" placeholder="<?= __("Dirección") ?> 1" name="dir1" value="<?= isset($student) ? $student->dir1 : '' ?>">
                                </div>
                                <div class="col-12 mt-1">
                                    <input class="form-control" type="text" placeholder="<?= __("Dirección") ?> 2" name="dir2" value="<?= isset($student) ? $student->dir2 : '' ?>">
                                </div>
                                <div class="col-5 mt-1">
                                    <input class="form-control" type="text" placeholder="<?= __("Ciudad") ?>" name="city" value="<?= isset($student) ? $student->pueblo : '' ?>">
                                </div>
                                <div class="col-3 mt-1">
                                    <input class="form-control" type="text" placeholder="<?= __("Estado") ?>" name="state" value="<?= isset($student) ? $student->estado : '' ?>">
                                </div>
                                <div class="col-4 mt-1">
                                    <input class="form-control" type="text" placeholder="<?= __("Codigo Postal") ?>" name="zip" value="<?= isset($student) ? $student->zip : '' ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="biologicalParentEmail"><?= __("Correo electrónico") ?></label>
                                <input type="email" class="form-control" name='biologicalParentEmail' id="biologicalParentEmail" value="<?= isset($student) ? $student->emailp : '' ?>">
                            </div>
                            <div class="form-group">
                                <label for="biologicalParentPhone"><?= __("Teléfono") ?></label>
                                <input type="tel" class="form-control phone" name='biologicalParentPhone' id="biologicalParentPhone" value="<?= isset($student) ? $student->telp : '' ?>">
                            </div>
                            <div class="form-group">
                                <label for="biologicalParentCell"><?= __("Celular") ?></label>
                                <input type="tel" class="form-control phone" name='biologicalParentCell' id="biologicalParentCell" value="<?= isset($student) ? $student->celp : '' ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-enrollment" role="tabpanel" aria-labelledby="nav-enrollment-tab">
                    <div class="container-fluid p-3">
                        <div class="card col-12 offset-md-3 col-md-6 p-3 rounded-0">
                            <h2 class="card-title text-center"><?= __("Re-matricula") ?></h2>
                            <div class="form-group">
                                <label for="retainedEnrollment" class="text-center"><?= __("Matrícula retenida") ?></label>
                                <select class="form-control" name="retainedEnrollment" id="retainedEnrollment">
                                    <option value=""><?= __("Seleccionar") ?></option>
                                    <option <?= isset($student) && $student->mat_retenida === 'Si' ? 'selected' : '' ?> value="Si"><?= __("Si") ?></option>
                                    <option <?= isset($student) && $student->mat_retenida === 'No' ? 'selected' : '' ?> value="No"><?= __("No") ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="reason" class="text-center"><?= __("Razón") ?></label>
                                <textarea class="form-control" name="reason" id="reason"><?= isset($student) ? $student->alias : '' ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center p-5">
                <button id="submit" name="<?= isset($mt) ? 'edit' : 'save' ?>" class="btn btn-primary" <?= isset($mt) ? '' : 'disabled' ?>><?= __("Guardar") ?></button>
                <a href="<?= Route::url("/admin/users/accounts/index.php?student=$accountNumber") ?>" class="btn btn-secondary"><?= __("Atrás") ?></a>
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