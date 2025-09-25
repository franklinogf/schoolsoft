<?php

require_once '../../../../app.php';

use App\Models\Admin;
use App\Models\School;
use Classes\File;
use Classes\Util;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use App\Models\Student;

Session::is_logged();
Server::is_post();


if (isset($_POST['searchSs'])) {
    $studentExist = Student::query()->bySS($_POST['searchSs'])->exists();
    echo Util::toJson(['exist' => $studentExist]);
} else if (isset($_POST['edit'])) {
    $student = Student::find($_POST['pk']);
    Session::set('editedStudent', "{$_POST['name']} {$_POST['surnames']}");
} else if (isset($_POST['save'])) {
    $schoolYear = Admin::primaryAdmin()->year();
    $student = new Student();
    $student->year = $schoolYear;
    $student->id = $_POST['accountNumber'];
    Session::set('addedStudent', "{$_POST['name']} {$_POST['surnames']}");
}

if (isset($_POST['save']) || isset($_POST['edit'])) {

    /* --------------------------- General information -------------------------- */
    $student->ss = $_POST['ss'];
    $student->grado = $_POST['grade'];
    $student->nombre = $_POST['name'];
    $student->apellidos = $_POST['surnames'];
    $student->nuref = $_POST['reference'];
    $student->fecha = $_POST['dateOfBirth'];
    $student->fecha_matri = $_POST['enrollmentDate'];
    $student->lugar_nac = $_POST['placeOfBirth'];
    $student->genero = $_POST['gender'];
    $student->vivecon = $_POST['liveWith'];
    $student->nuevo = $_POST['new'];
    $student->cel = $_POST['cell'];
    $student->comp = $_POST['cellCompany'];
    $student->raza = $_POST['race'];

    // Discounts
    $student->desc1 = $_POST['discount1'];
    $student->desc_men = $_POST['men'];
    $student->cdb1 = $_POST['cdb1'];
    $student->desc2 = $_POST['discount2'];
    $student->desc_mat = $_POST['mat'];
    $student->cdb1 = $_POST['cdb2'];
    $student->desc3 = $_POST['discount3'];
    $student->desc_otro1 = $_POST['otro1'];
    $student->cdb1 = $_POST['cdb3'];

    $student->pop = $_POST['school'];
    $student->colpro = $_POST['colpro'];
    $student->municipio = $_POST['municipio'];
    $student->transporte = $_POST['transporte'];

    /* ------------------------ Diseases and medications ------------------------ */
    $student->medico = $_POST['doctor'];
    $student->acomodo = $_POST['accommodation'];
    $student->tel1 = $_POST['tel1'];
    $student->tel2 = $_POST['tel2'];
    $student->trajo = $_POST['professionalEvaluation'];
    for ($i = 1; $i <= 4; $i++) {
        $student->{"imp$i"} = $_POST["imp$i"];
        $student->{"med$i"} = $_POST["med$i"];
        $student->{"enf$i"} = $_POST["enf$i"];
        $student->{"rec$i"} = $_POST["rec$i"];
    }

    /* ------------------------------ Denomination ------------------------------ */
    $student->religion = $_POST['religion'];
    $student->iglesia = $_POST['church'];
    $student->bau = $_POST['baptism'];
    $student->fbau = $_POST['baptismDate'];
    $student->com = $_POST['communion'];
    $student->fcom = $_POST['communionDate'];
    $student->con = $_POST['confirmation'];
    $student->fcon = $_POST['confirmationDate'];

    /* ------------------------ Biological father/mother ------------------------ */
    $student->padre = $_POST['biologicalParent'];
    $student->nombre_padre = $_POST['biologicalParentName'];
    $student->dir1 = $_POST["dir1"];
    $student->dir2 = $_POST["dir2"];
    $student->pueblo = $_POST["city"];
    $student->estado = $_POST["state"];
    $student->zip = $_POST["zip"];
    $student->emailp = $_POST["biologicalParentEmail"];
    $student->telp = $_POST["biologicalParentPhone"];
    $student->celp = $_POST["biologicalParentCell"];

    /* ------------------------------ Re-enrollment ----------------------------- */
    $student->mat_retenida = $_POST["retainedEnrollment"];
    $student->alias = $_POST["reason"];



    // echo '<pre>';print_r($student);echo '</pre>';
    $student->save();
    if (isset($_POST['save'])) {
        $student->family()->update(['id2' => '']);
    }
    $student->refresh();
    /* ----------------------------- Profile picture ---------------------------- */
    $file = new File('picture');
    if ($file->amount > 0) {
        $newName = "$student->mt.jpg";
        $file::upload($file->files, __STUDENT_PROFILE_PICTURE_PATH, $newName);

        $student->update(['imagen' => $newName]);
    }

    Session::set('accountNumber', $_POST['accountNumber']);
    Route::redirect('/users/accounts/');
}
