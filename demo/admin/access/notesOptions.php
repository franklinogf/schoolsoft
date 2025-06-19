<?php
require_once '../../app.php';

use Classes\Controllers\School;
use Classes\Lang;
use Classes\Route;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ['Opciones de notas', 'Notes options'],
    ['Tarjeta de notas', 'Report Card'],
    ['Notas', 'Grades'],
    ['Tarjeta', 'Card'],
    ['Opciones de promedio', 'Average Options'],
    ['Notas en porciento', 'Grades in Percentage'],
    ['Porciento Examen Final Inte.', 'Final Exam Percentage (Intermediate)'],
    ['Porciento Examen Final Elem.', 'Final Exam Percentage (Elementary)'],
    ['Porciento Examen Final Supe.', 'Final Exam Percentage (Superior)'],
    ['Opciones adicionales', 'Additional Options'],
    ['Suma de Trimistre', 'Trimester Sum'],
    ['Suma Anual de Trimestre', 'Annual Trimester Sum'],
    ['No', 'No'],
    ['Si', 'Yes'],
    ['Aviso Finalizar Trimestre', 'Notice to End Trimester'],
    ['Cantidad de cierre', 'Closure Amount'],
    ['Converción de notas', 'Grade Conversion'],
    ['De Número a Letras', 'From Numbers to Letters'],
    ['Maestro Salón hogar', 'Teacher Home Room'],
    ['Ver todas las clases', 'View All Classes'],
    ['Imprimir tarjeta de notas', 'Print Report Card'],
    ['Forzar notas del trimestre', 'Force Trimester Grades'],
    ['Nota final', 'Final Grade'],
    ['Nota Maxima', 'Maximum Grade'],
    ['Pasar Notas a Punto Decimal.', 'Convert Grades to Decimal Point.'],
    ['Cambiar Porciento a Punto Decimal', 'Change Percentage to Decimal Point'],
    ['Notas Individuales', 'Individual Grades'],
    ['Mensajes individual por clase', 'Individual Messages by Class'],
    ['Expandir Trabajos diarios a 20 notas', 'Expand Daily Work to 20 Grades'],
    ['Notas no pasar del valor de los Temas', 'Grades Should Not Exceed Topic Values'],
    ['Notas en letras', 'Grades in Letters'],
    ['Opciones de Tarjeta a los Padres', 'Parent Report Card Options'],
    ['Activar Tarjeta de Notas a los Padres', 'Activate Parent Report Card'],
    ['Semestre', 'Semester'],
    ['Final', 'Final'],
    ['Trimestre', 'Trimester'],
    ['Fecha de expiración', 'Expiration Date'],
    ['Informe de deficiencia a padres', 'Deficiency Report to Parents'],
    ['Menor o igual a', 'Less Than or Equal To'],
    ['Hoja de progreso', 'Progress Sheet'],
    ['Activación', 'Activation'],
    ['Guardar', 'Save'],

]);

$school = new School();
?>
<!DOCTYPE html>
<html lang="<?=__LANG?>">

<head>
    <?php
$title = $lang->translation("Opciones de notas");
Route::includeFile('/admin/includes/layouts/header.php');
?>

</head>

<body>
    <?php
Route::includeFile('/admin/includes/layouts/menu.php');
?>
    <div class="container-lg mt-lg-3  px-0">
        <h1 class="display-4 mt-5 text-center"><?=$lang->translation("Opciones de notas")?></h1>

        <div class="container">
            <?php if (Session::get('saved')): ?>
            <div id="alert" class="alert alert-success mr-auto p-2 hidden" role="alert">
                <i class="fa-solid fa-square-check"></i> <?=Session::get('saved', true)?>
            </div>
            <?php endif?>
        <form method="POST" action="<?=Route::url('/admin/access/includes/notesOptions.php')?>" class="p-5">
            <div class="row mt-4">
                <div class="col-12">
                    <div class="form-group">
                        <label for="tarj"><?=$lang->translation("Tarjeta de notas");?></label>
                        <select class="form-control" name="tarj" id="tarj" required>
                            <?php for ($i = 1; $i <= 50; $i++): ?>
                            <option <?=$school->info('tar') == $i ? 'selected' : ''?> value="<?=$i?>"><?=$lang->translation("Tarjeta");?> <?=$i?></option>
                            <?php endfor?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <h2><?=$lang->translation("Notas");?></h2>
                </div>
                <div class="col-4 mb-1">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">A</span>
                        </div>
                        <input type="number" name="vala" class="form-control" required value="<?=$school->info('vala')?>">
                    </div>
                </div>
                <div class="col-4 mb-1">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">B</span>
                        </div>
                        <input type="number" name="valb" class="form-control" required value="<?=$school->info('valb')?>">
                    </div>
                </div>
                <div class="col-4 mb-1">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">C</span>
                        </div>
                        <input type="number" name="valc" class="form-control" required value="<?=$school->info('valc')?>">
                    </div>
                </div>
                <div class="col-4 mb-1">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">D</span>
                        </div>
                        <input type="number" name="vald" class="form-control" required value="<?=$school->info('vald')?>">
                    </div>
                </div>
                <div class="col-4 mb-1">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">F</span>
                        </div>
                        <input type="number" name="valf" class="form-control" required value="<?=$school->info('valf')?>">
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <h2><?=$lang->translation("Opciones de promedio");?></h2>
                </div>
                <div class="col-12">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="np"><?=$lang->translation("Notas en porciento");?></label>
                        <select class="form-control col-sm-3" name="np" id="np">
                            <option <?=$school->info('np') === 'No' ? 'selected' : ''?> value="No"><?=$lang->translation("No");?></option>
                            <option <?=$school->info('np') === 'Si' ? 'selected' : ''?> value="Si"><?=$lang->translation("Si");?></option>
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="por1"><?=$lang->translation("Porciento Examen Final Inte.");?></label>
                        <input type="number" class="form-control col-sm-3" name="por1" id="por1" value="<?=$school->info('por1')?>">
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="por2"><?=$lang->translation("Porciento Examen Final Elem.");?></label>
                        <input type="number" class="form-control col-sm-3" name="por2" id="por2" value="<?=$school->info('por2')?>">
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="por3"><?=$lang->translation("Porciento Examen Final Supe.");?></label>
                        <input type="number" class="form-control col-sm-3" name="por3" id="por3" value="<?=$school->info('por3')?>">
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <h2><?=$lang->translation("Opciones adicionales");?></h2>
                </div>
                <div class="col-12">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="sutri" id="sutri" value="NO" <?=$school->info('sutri') === 'NO' ? 'checked' : ''?>>
                        <label class="custom-control-label" for="sutri"><?=$lang->translation("Suma de Trimistre");?></label>
                    </div>
                </div>
                <div class="col-12">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="suantri" id="suantri" value="NO" <?=$school->info('suantri') === 'NO' ? 'checked' : ''?>>
                        <label class="custom-control-label" for="suantri"><?=$lang->translation("Suma Anual de Trimestre");?></label>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="sie"><?=$lang->translation("Aviso Finalizar Trimestre");?></label>
                        <select class="form-control col-sm-3" name="sie" id="sie">
                            <option <?=$school->info('sie') === 'No' ? 'selected' : ''?> value="No"><?=$lang->translation("No");?></option>
                            <option <?=$school->info('sie') === 'Si' ? 'selected' : ''?> value="Si"><?=$lang->translation("Si");?></option>
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="sieab"><?=$lang->translation("Cantidad de cierre");?></label>
                        <select class="form-control col-sm-3" name="sieab" id="sieab">
                            <option <?=$school->info('sieab') == '4' ? 'selected' : ''?> value="4">4</option>
                            <option <?=$school->info('sieab') == '8' ? 'selected' : ''?> value="8">8</option>
                        </select>
                    </div>
                </div>

            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <h2><?=$lang->translation("Converción de notas");?></h2>
                </div>
                <div class="col-12">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="cv"><?=$lang->translation("De Número a Letras");?></label>
                        <select class="form-control col-sm-3" name="cv" id="cv">
                            <option <?=$school->info('cv') === 'No' ? 'selected' : ''?> value="No"><?=$lang->translation("No");?></option>
                            <option <?=$school->info('cv') === 'Si' ? 'selected' : ''?> value="Si"><?=$lang->translation("Si");?></option>
                        </select>
                    </div>
                </div>

            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <h2><?=$lang->translation("Maestro Salón hogar");?></h2>
                </div>
                <div class="col-6">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="teg" id="teg" value="NO" <?=$school->info('teg') === 'NO' ? 'checked' : ''?>>
                        <label class="custom-control-label" for="teg"><?=$lang->translation("Ver todas las clases");?></label>
                    </div>
                </div>
                <div class="col-6">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="tarjeta" id="tarjeta" value="SI" <?=$school->info('tarjeta') === 'SI' ? 'checked' : ''?>>
                        <label class="custom-control-label" for="tarjeta"><?=$lang->translation("Imprimir tarjeta de notas");?></label>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <h2><?=$lang->translation("Forzar notas del trimestre");?></h2>
                </div>
                <div class="col-12">
                    <div class="row">
                        <div class="custom-control custom-checkbox col-3">
                            <input type="checkbox" class="custom-control-input" name="tr1" id="tr1" value="NO" <?=$school->info('tr1') === 'NO' ? 'checked' : ''?>>
                            <label class="custom-control-label" for="tr1"><?=$lang->translation("Trimestre");?> 1</label>
                        </div>
                        <div class="col-3">
                            <input class="form-control" type="number" name="vt1" value="<?=$school->info('vt1')?>">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row">
                        <div class="custom-control custom-checkbox col-3">
                            <input type="checkbox" class="custom-control-input" name="tr2" id="tr2" value="NO" <?=$school->info('tr2') === 'NO' ? 'checked' : ''?>>
                            <label class="custom-control-label" for="tr2"><?=$lang->translation("Trimestre");?> 2</label>
                        </div>
                        <div class="col-3">
                            <input class="form-control" type="number" name="vt2" value="<?=$school->info('vt2')?>">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row">
                        <div class="custom-control custom-checkbox col-3">
                            <input type="checkbox" class="custom-control-input" name="tr3" id="tr3" value="NO" <?=$school->info('tr3') === 'NO' ? 'checked' : ''?>>
                            <label class="custom-control-label" for="tr3"><?=$lang->translation("Trimestre");?> 3</label>
                        </div>
                        <div class="col-3">
                            <input class="form-control" type="number" name="vt3" value="<?=$school->info('vt3')?>">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row">
                        <div class="custom-control custom-checkbox col-3">
                            <input type="checkbox" class="custom-control-input" name="tr4" id="tr4" value="NO" <?=$school->info('tr4') === 'NO' ? 'checked' : ''?>>
                            <label class="custom-control-label" for="tr4"><?=$lang->translation("Trimestre");?> 4</label>
                        </div>
                        <div class="col-3">
                            <input class="form-control" type="number" name="vt4" value="<?=$school->info('vt4')?>">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row">
                        <div class="custom-control custom-checkbox col-3">
                            <input type="checkbox" class="custom-control-input" name="ns1" id="ns1" value="NO" <?=$school->info('ns1') === 'NO' ? 'checked' : ''?>>
                            <label class="custom-control-label" for="ns1"><?=$lang->translation("Semestre");?> 1</label>
                        </div>
                        <div class="col-3">
                            <input class="form-control" type="number" name="vs1" value="<?=$school->info('vs1')?>">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row">
                        <div class="custom-control custom-checkbox col-3">
                            <input type="checkbox" class="custom-control-input" name="ns2" id="ns2" value="NO" <?=$school->info('ns2') === 'NO' ? 'checked' : ''?>>
                            <label class="custom-control-label" for="ns2"><?=$lang->translation("Semestre");?> 2</label>
                        </div>
                        <div class="col-3">
                            <input class="form-control" type="number" name="vs2" value="<?=$school->info('vs2')?>">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row">
                        <div class="custom-control custom-checkbox col-3">
                            <input type="checkbox" class="custom-control-input" name="nf" id="nf" value="NO" <?=$school->info('nf') === 'NO' ? 'checked' : ''?>>
                            <label class="custom-control-label" for="nf"><?=$lang->translation("Nota final");?></label>
                        </div>
                        <div class="col-3">
                            <input class="form-control" type="number" name="vf" value="<?=$school->info('vf')?>">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row">
                        <div class="custom-control custom-checkbox col-3">
                            <input type="checkbox" class="custom-control-input" name="enf" id="enf" value="NO" <?=$school->info('enf') === 'NO' ? 'checked' : ''?>>
                            <label class="custom-control-label" for="enf"><?=$lang->translation("Nota Maxima");?></label>
                        </div>
                        <div class="col-3">
                            <input class="form-control" type="number" name="nmf" value="<?=$school->info('nmf')?>">
                        </div>
                    </div>
                </div>

            </div>

            <div class="row mt-5">
                <div class="custom-control custom-checkbox col-12">
                    <input type="checkbox" class="custom-control-input" name="tpa" id="tpa" value="NO" <?=$school->info('tpa') === 'NO' ? 'checked' : ''?>>
                    <label class="custom-control-label" for="tpa"><?=$lang->translation("Pasar Notas a Punto Decimal.");?></label>
                </div>
                <div class="custom-control custom-checkbox col-12">
                    <input type="checkbox" class="custom-control-input" name="cppd" id="cppd" value="SI" <?=$school->info('cppd') === 'SI' ? 'checked' : ''?>>
                    <label class="custom-control-label" for="cppd"><?=$lang->translation("Cambiar Porciento a Punto Decimal");?></label>
                </div>
                <div class="custom-control custom-checkbox col-12">
                    <input type="checkbox" class="custom-control-input" name="nin" id="nin" value="NO" <?=$school->info('nin') === 'NO' ? 'checked' : ''?>>
                    <label class="custom-control-label" for="nin"><?=$lang->translation("Notas Individuales");?></label>
                </div>
                <div class="custom-control custom-checkbox col-12">
                    <input type="checkbox" class="custom-control-input" name="cm" id="cm" value="SI" <?=$school->info('cm') === 'SI' ? 'checked' : ''?>>
                    <label class="custom-control-label" for="cm"><?=$lang->translation("Mensajes individual por clase");?></label>
                </div>
                <div class="custom-control custom-checkbox col-12">
                    <input type="checkbox" class="custom-control-input" name="etd" id="etd" value="SI" <?=$school->info('etd') === 'SI' ? 'checked' : ''?>>
                    <label class="custom-control-label" for="etd"><?=$lang->translation("Expandir Trabajos diarios a 20 notas");?></label>
                </div>
                <div class="custom-control custom-checkbox col-12">
                    <input type="checkbox" class="custom-control-input" name="npn" id="npn" value="SI" <?=$school->info('npn') === 'SI' ? 'checked' : ''?>>
                    <label class="custom-control-label" for="npn"><?=$lang->translation("Notas no pasar del valor de los Temas");?></label>
                </div>
                <div class="custom-control custom-checkbox col-12">
                    <input type="checkbox" class="custom-control-input" name="nel" id="nel" value="SI" <?=$school->info('nel') === 'SI' ? 'checked' : ''?>>
                    <label class="custom-control-label" for="nel"><?=$lang->translation("Notas en letras");?></label>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <h2><?=$lang->translation("Opciones de Tarjeta a los Padres");?></h2>
                </div>
                <div class="col-6">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="logo" id="logo" value="NO" <?=$school->info('logo') === 'NO' ? 'checked' : ''?>>
                        <label class="custom-control-label" for="logo"><?=$lang->translation("Activar Tarjeta de Notas a los Padres");?></label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="se1" id="se1" value="NO" <?=$school->info('se1') === 'NO' ? 'checked' : ''?>>
                        <label class="custom-control-label" for="se1"><?=$lang->translation("Semestre");?> 1</label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="se2" id="se2" value="NO" <?=$school->info('se2') === 'NO' ? 'checked' : ''?>>
                        <label class="custom-control-label" for="se2"><?=$lang->translation("Semestre");?> 2</label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="fin" id="fin" value="NO" <?=$school->info('fin') === 'NO' ? 'checked' : ''?>>
                        <label class="custom-control-label" for="fin"><?=$lang->translation("Final");?></label>
                    </div>
                </div>
                <div class="col-12 mt-2">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="tri"><?=$lang->translation("Trimestre");?></label>
                        <select class="form-control col-sm-3" name="tri" id="tri">
                            <option <?=$school->info('tri') == '1' ? 'selected' : ''?> value="1">T-1</option>
                            <option <?=$school->info('tri') == '2' ? 'selected' : ''?> value="2">T-2</option>
                            <option <?=$school->info('tri') == '3' ? 'selected' : ''?> value="3">T-3</option>
                            <option <?=$school->info('tri') == '4' ? 'selected' : ''?> value="4">T-4</option>
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="fec_t"><?=$lang->translation("Fecha de expiración");?></label>
                        <input class="form-control col-sm-3" type="date" name="fec_t" id="fec_t" value="<?=$school->info('fec_t')?>">
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <h2><?=$lang->translation("Informe de deficiencia a padres");?></h2>
                </div>
                <div class="col-12">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="tri1"><?=$lang->translation("Trimestre");?></label>
                        <select class="form-control col-sm-3" name="tri1" id="tri1">
                            <option <?=$school->info('fra') == '1' ? 'selected' : ''?> value="1">T-1</option>
                            <option <?=$school->info('fra') == '2' ? 'selected' : ''?> value="2">T-2</option>
                            <option <?=$school->info('fra') == '3' ? 'selected' : ''?> value="3">T-3</option>
                            <option <?=$school->info('fra') == '4' ? 'selected' : ''?> value="4">T-4</option>
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="vnf"><?=$lang->translation("Menor o igual a");?></label>
                        <input type="number" class="form-control col-sm-3" name="vnf" id="vnf" value="<?=$school->info('vnf')?>">
                    </div>
                </div>

            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <h2><?=$lang->translation("Hoja de progreso");?></h2>
                </div>
                <div class="col-12">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="hoja" id="hoja" value="NO" <?=$school->info('hdp') === 'NO' ? 'checked' : ''?>>
                        <label class="custom-control-label" for="hoja"><?=$lang->translation("Activación");?></label>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="tri2"><?=$lang->translation("Trimestre");?></label>
                        <select class="form-control col-sm-3" name="tri2" id="tri2">
                            <option <?=$school->info('hdt') == '1' ? 'selected' : ''?> value="1">T-1</option>
                            <option <?=$school->info('hdt') == '2' ? 'selected' : ''?> value="2">T-2</option>
                            <option <?=$school->info('hdt') == '3' ? 'selected' : ''?> value="3">T-3</option>
                            <option <?=$school->info('hdt') == '4' ? 'selected' : ''?> value="4">T-4</option>
                        </select>
                    </div>
                </div>


            </div>
            <div class="row">
                <div class="col-12">
                    <button class="btn btn-primary" type="submit"><?=$lang->translation("Guardar");?></button>
                </div>
            </div>
        </form>
        </div>
    </div>


    <?php
Route::includeFile('/includes/layouts/scripts.php', true);
?>

</body>

</html>