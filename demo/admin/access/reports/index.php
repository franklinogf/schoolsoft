<?php
require_once __DIR__ . '/../../../app.php';

use App\Enums\AdminPermission;
use Classes\Route;
use Classes\Session;
use Classes\Lang;
use App\Models\Admin;

Session::is_logged();

$user  = Admin::user(Session::id())->first();

$options = [
    [
        'title' => __("Informes"),
        'buttons' => [
            [
                'name' => __("Lista de Estudiantes"),
                'desc' => '',
                'link' => 'pdf/studentsList.php',
                'target' => 'studentsList',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_STUDENTS_LIST)
            ],
            [
                'name' => __("Salón hogar"),
                'desc' => '',
                'link' => 'pdf/homeClassroom.php',
                'target' => 'homeClassroom',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_HOMEROOM)
            ],
            [
                'name' => __("Totales por grado"),
                'desc' => '',
                'link' => 'pdf/totalsByGrade.php',
                'target' => 'totalsByGrade',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_TOTALS_BY_GRADE)
            ],
            [
                'name' => __("Lista de firmas"),
                'desc' => '',
                'link' => 'pdf/listOfSigns.php',
                'target' => 'listOfSigns',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_SIGNATURE_LIST)
            ],
            [
                'name' => __("Lista de usuarios"),
                'desc' => '',
                'link' => 'pdf/usersList.php',
                'target' => 'usersList',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_USERS_LIST)
            ],
            [
                'name' => __('Lista Re-Matrícula'),
                'desc' => '',
                'link' => 'pdf/reEnroll.php',
                'target' => 'reEnroll',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_RE_ENROLLMENT_LIST)
            ],
            [
                'name' => __('Asistencia diaria'),
                'desc' => '',
                'link' => 'dailyAttendance.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_DAILY_ATTENDANCE)
            ],
            [
                'name' => __("Cuentas accesadas"),
                'desc' => '',
                'link' => 'pdf/accessedAccounts.php',
                'target' => 'accessedAccounts',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_ACCESSED_ACCOUNTS)
            ],
            [
                'name' => __("Informe de encuestas"),
                'desc' => '',
                'link' => 'survey.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_SURVEY)
            ],
            [
                'name' => __("Informe de cuentas de padres"),
                'desc' => '',
                'link' => 'pdf/parentsAccounts.php',
                'target' => 'parentsAccounts',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_PARENT_ACCOUNTS)
            ],
            [
                'name' => __("Cuentas incompletas"),
                'desc' => '',
                'link' => 'pdf/incompleteAccounts.php',
                'target' => 'incompleteAccounts',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_INCOMPLETE_ACCOUNTS)
            ],
            [
                'name' => __("Label"),
                'desc' => '',
                'link' => 'label.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_LABEL)
            ],
            [
                'name' => __("Informe de familia por grado"),
                'desc' => '',
                'link' => 'pdf/familyByGrade.php',
                'target' => 'familyByGrade',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_FAMILY_GRADE)
            ],
            [
                'name' => __("Lista por cuentas"),
                'desc' => '',
                'link' => 'pdf/accountsList.php',
                'target' => 'accountsList',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_ACCOUNTS_LIST)
            ],
            [
                'name' => __("Hoja de matrícula"),
                'desc' => '',
                'link' => 'enrollment.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_ENROLLMENT_FORM)
            ],
            [
                'name' => __("Estudiantes nuevos"),
                'desc' => '',
                'link' => 'newStudent.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_NEW_STUDENTS)
            ],
            [
                'name' => __("Lista de descuentos"),
                'desc' => '',
                'link' => 'pdf/discountList.php',
                'target' => 'discountList',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_DISCOUNTS_LIST)
            ],
            [
                'name' => __("Medicamentos / Recetas"),
                'desc' => '',
                'link' => 'pdf/medicineStudent.php',
                'target' => 'medicineStudent',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_MEDICATIONS)
            ],
            [
                'name' => __("Lista de bajas"),
                'desc' => '',
                'link' => 'pdf/dropList.php',
                'target' => 'dropList',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_DROPOUT_LIST)
            ],
            [
                'name' => __("Condiciones / alergias"),
                'desc' => '',
                'link' => 'pdf/allergyStudent.php',
                'target' => 'allergyStudent',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_CONDITIONS_ALLERGIES)
            ],
            [
                'name' => __("Lista de teléfonos"),
                'desc' => '',
                'link' => 'pdf/phoneList.php',
                'target' => 'phoneList',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_PHONE_LIST)
            ],
            [
                'name' => __("Lista de cumpleaños"),
                'desc' => '',
                'link' => 'birthdayList.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_BIRTHDAYS_LIST)
            ],
            [
                'name' => __("Matrícula por salón"),
                'desc' => '',
                'link' => 'enrollmentClassroom.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_ENROLLMENT_BY_CLASS)
            ],
            [
                'name' => __("Lista de correos"),
                'desc' => '',
                'link' => 'emailList.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_EMAIL_LIST)
            ],
            [
                'name' => __("Lista de padres"),
                'desc' => '',
                'link' => 'parentList.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_PARENTS_LIST)
            ],
            [
                'name' => __("Lista de trabajos de padres"),
                'desc' => '',
                'link' => 'jobList.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_PARENT_WORK_LIST)
            ],
            [
                'name' => __("Lista de dirección postal"),
                'desc' => '',
                'link' => 'addressList.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_POSTAL_ADDRESS)
            ],
            [
                'name' => __("Listado de religión"),
                'desc' => '',
                'link' => 'religionList.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_RELIGION)
            ],
            [
                'name' => __("Usuarios y claves"),
                'desc' => '',
                'link' => 'UsersList.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_USERS_AND_PASSWORDS)
            ],
            [
                'name' => __("Carta certificada"),
                'desc' => '',
                'link' => 'registeredLetter.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_CERTIFIED_LETTER)
            ],
            [
                'name' => __("Lista de maestros"),
                'desc' => '',
                'link' => 'pdf/teacherList.php',
                'target' => 'teacherList',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_TEACHERS_LIST)
            ],
            [
                'name' => __("Correos de maestros"),
                'desc' => '',
                'link' => 'pdf/teacherEmails.php',
                'target' => 'teacherEmails',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_TEACHERS_EMAIL)
            ],
            [
                'name' => __("Teléfonos de maestros"),
                'desc' => '',
                'link' => 'pdf/telProfesor.php',
                'target' => 'telProfesor',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_TEACHERS_PHONE)
            ],
            [
                'name' => __("Lista de niveles"),
                'desc' => '',
                'link' => 'pdf/levelList.php',
                'target' => 'levelList',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_LEVELS_LIST)
            ],
            [
                'name' => __("Salón hogar de maestros"),
                'desc' => '',
                'link' => 'pdf/homeProfesor.php',
                'target' => 'homeProfesor',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_HOMEROOM_TEACHERS)
            ],
            [
                'name' => __("Lista de firmas de maestros"),
                'desc' => '',
                'link' => 'pdf/firmaProfesor.php',
                'target' => 'firmaProfesor',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_TEACHERS_SIGNATURE_LIST)
            ],
            [
                'name' => __("Direcciones de maestros"),
                'desc' => '',
                'link' => 'addressProfesor.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_TEACHERS_ADDRESS)
            ],
            [
                'name' => __("Preparación de maestros"),
                'desc' => '',
                'link' => 'pdf/preProfesor.php',
                'target' => 'preProfesor',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_TEACHERS_PREPARATION)
            ],
            [
                'name' => __("Lista de club de maestros"),
                'desc' => '',
                'link' => 'pdf/clubProfesor.php',
                'target' => 'clubProfesor',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_TEACHERS_CLUB_LIST)
            ],
            [
                'name' => __("Informe socioeconómico"),
                'desc' => '',
                'link' => 'socioEconomicReport.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_SOCIO_ECONOMIC)
            ],
            [
                'name' => __("Licencia de maestros"),
                'desc' => '',
                'link' => 'licenseTeacher.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_TEACHERS_LICENSE)
            ],
            [
                'name' => __("No docentes"),
                'desc' => '',
                'link' => 'notTeachers.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_NON_TEACHING_STAFF)
            ],
            [
                'name' => __("Total por grado"),
                'desc' => '',
                'link' => 'pdf/totalGrade.php',
                'target' => 'totalGrade',
                'hidden' => false,
            ],
            [
                'name' => __("Lista con fotos"),
                'desc' => '',
                'link' => 'pdf/listPhotos.php',
                'target' => 'listPhotos',
                'hidden' => false,
            ],
            [
                'name' => __("Acuse de recibo"),
                'desc' => '',
                'link' => 'AcknowledgmentReceipt.php',
                'hidden' => false,
            ],
            [
                'name' => __("Informe de grado y edad"),
                'desc' => '',
                'link' => 'pdf/ageClassroom.php',
                'target' => 'ageClassroom',
                'hidden' => false,
            ],
            [
                'name' => __("Listado de clubes"),
                'desc' => '',
                'link' => 'pdf/clubList.php',
                'target' => 'clubList',
                'hidden' => false,
            ],
            [
                'name' => __("Información por grado"),
                'desc' => '',
                'link' => 'InformationByGrade.php',
                'hidden' => false,
            ],
            [
                'name' => __("Lista personas autorizadas"),
                'desc' => '',
                'link' => 'pdf/listOFauthorizedPersons.php',
                'target' => 'listOFauthorizedPersons',
                'hidden' => false,
            ],
            [
                'name' => __("Direcciones y telefonos por grado"),
                'desc' => '',
                'link' => 'pdf/listAddressphone.php',
                'target' => 'listAddressphone',
                'hidden' => false,
            ],
            [
                'name' => __("Lista de exalumnos"),
                'desc' => '',
                'link' => 'pdf/alumniList.php',
                'target' => 'alumniList',
                'hidden' => false,
            ],
            [
                'name' => __("Lista de no becados"),
                'desc' => '',
                'link' => 'pdf/listWithoutScholarships.php',
                'target' => 'listWithoutScholarships',
                'hidden' => false,
            ],
            [
                'name' => __("Lista estudiantes KG-12"),
                'desc' => '',
                'link' => 'pdf/studentListK12.php',
                'target' => 'studentListK12',
                'hidden' => false,
            ],
            [
                'name' => __("Lista de E/P/G/E/T"),
                'desc' => '',
                'link' => 'pdf/listInformation.php',
                'target' => 'listInformation',
                'hidden' => false,
            ],
            [
                'name' => __("Informe de raza"),
                'desc' => '',
                'link' => 'pdf/listInformeRaza.php',
                'target' => 'listInformeRaza',
                'hidden' => false,
            ],
            [
                'name' => __("Informe de religión"),
                'desc' => '',
                'link' => 'pdf/listInformeReligion.php',
                'target' => 'listInformeReligion',
                'hidden' => false,
            ],
            [
                'name' => __("Informe de raza y religión"),
                'desc' => '',
                'link' => 'pdf/listRaceeReligion.php',
                'target' => 'listRaceReligion',
                'hidden' => false,
            ],
            [
                'name' => __("Resumen de raza y religión"),
                'desc' => '',
                'link' => 'pdf/ResumRaceeReligion.php',
                'target' => 'ResumRaceeReligion',
                'hidden' => false,
            ],
            [
                'name' => __("Documentos no entregados"),
                'desc' => '',
                'link' => 'pdf/DocumentsNotDelivered.php',
                'hidden' => false,
            ],
            [
                'name' => __("Formulario socioeconómico"),
                'desc' => '',
                'link' => 'formSocio.php',
                'hidden' => false,
            ],
            [
                'name' => __("Lista de edad y seguro social"),
                'desc' => '',
                'link' => 'pdf/ageSsClassroom.php',
                'target' => 'ageSsClassroom',
                'hidden' => false,
            ],
            [
                'name' => __("Informe de procedencia"),
                'desc' => '',
                'link' => 'pdf/infProcedencia.php',
                'target' => 'infProcedencia',
                'hidden' => false,
            ],
            [
                'name' => __("Carta de recomendación"),
                'desc' => '',
                'link' => 'LetterRecommendation.php',
                'hidden' => false,
            ],
            [
                'name' => __("Informe de acceso de maestros"),
                'desc' => '',
                'link' => 'TeacherAccessReport.php',
                'hidden' => false,
            ],
            [
                'name' => __("Informe de acceso de los padres"),
                'desc' => '',
                'link' => 'ParentsAccessReport.php',
                'hidden' => false,
            ],
            [
                'name' => __("Informe de acceso de los administradores"),
                'desc' => '',
                'link' => 'AdminAccessReport.php',
                'hidden' => false,
            ],
            [
                'name' => __("Informe de documentos"),
                'desc' => '',
                'link' => 'DocumentReport.php',
                'hidden' => false,
            ],
            [
                'name' => __("ID del estudiante"),
                'desc' => '',
                'link' => 'studentID.php',
                'hidden' => false,
            ],
            [
                'name' => __("ID del maestro"),
                'desc' => '',
                'link' => 'teacherID.php',
                'hidden' => false,
            ],
            [
                'name' => __("Acomodo Razonable"),
                'desc' => '',
                'link' => 'pdf/ReasonableAccommodation.php',
                'target' => 'ReasonableAccommodation',
                'hidden' => false,
            ],
            [
                'name' => __("Listado PP - 06"),
                'desc' => '',
                'link' => 'pdf/inf_pp_06.php',
                'target' => 'inf_pp_06',
                'hidden' => false,
            ],
            [
                'name' => __("Movimiento de matrícula"),
                'desc' => '',
                'link' => 'pdf/SchoolMovement.php',
                'target' => 'SchoolMovement',
                'hidden' => false,
            ],
            [
                'name' => __("Informe acumulativo de notas"),
                'desc' => '',
                'link' => 'pdf/CumulativeGradeReport.php',
                'target' => 'CumulativeGradeReport',
                'hidden' => false,
            ],
            [
                'name' => __("Pruebas diagnósticas"),
                'desc' => '',
                'link' => 'pdf/diagnosticTests.php',
                'target' => 'diagnosticTests',
                'hidden' => false,
            ],
            [
                'name' => __("Pruebas de selección"),
                'desc' => '',
                'link' => 'pdf/selectiontests.php',
                'target' => 'selectiontests',
                'hidden' => false,
            ],
            [
                'name' => __("Acomodo Razonable"),
                'desc' => '',
                'link' => 'pdf/ReasonableAccommodation2.php',
                'target' => 'Acomodo Razonable',
                'hidden' => false,
            ],
            [
                'name' => __("Fotos no Autorizadas"),
                'desc' => '',
                'link' => 'pdf/inf_no_fotos.php',
                'target' => 'Fotos no Autorizadas',
                'hidden' => false,
            ],
            [
                'name' => __("Lista Estudios Supervisados"),
                'desc' => '',
                'link' => 'pdf/inf_est_sup.php',
                'target' => 'inf_est_sup',
                'hidden' => false,
            ],
            [
                //                'name' => ["es" => 'Totales información personal',   "en" => "Totales información personal"],
                'name' => __(""),
                'desc' => '',
                'link' => '#',
                'hidden' => false,
            ],
            [
                //                'name' => ["es" => 'Información básica',   "en" => "Información básica"],
                'name' => __(""),
                'desc' => '',
                'link' => '#',
                'hidden' => false,
            ],
            [
                //                'name' => ["es" => 'Totales de información básica',   "en" => "Totales de información básica"],
                'name' => __(""),
                'desc' => '',
                'link' => '#',
                'hidden' => false,
            ],
            [
                //                'name' => ["es" => 'Progreso de educación fisica pasar a notas',   "en" => "Progreso de educación fisica"],
                'name' => __(""),
                'desc' => '',
                'link' => '#',
                'hidden' => false,
            ],
        ]
    ],



];


$lang = new Lang([
    ["Informes", "Reports"]
]);


?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    //    $title = $lang->translation("Informes");
    $title = __("Informes");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-3"><?= __("Informes") ?></h1>

        <div class="mx-2 mx-md-0 justify-content-around">

            <?php foreach ($options as $option) : ?>
                <div class="col mb-4">
                    <fieldset class="border border-secondary rounded-bottom h-100 px-2">
                        <legend class="w-auto"><?= $title ?></legend>
                        <div class="pb-3">
                            <div class="row row-cols-2 row-cols-md-3">
                                <?php foreach ($option['buttons'] as $button) : ?>
                                    <?php if ($button['hidden']) continue  ?>
                                    <div class="col mt-1 d-flex">
                                        <a style="font-size: .8em;" title="<?= isset($button['desc']) ? $button['desc'] : '' ?>" <?= isset($button['target']) ? "target='{$button['target']}'" : '' ?> class="btn btn-primary btn-block" href="<?= Route::url('/admin/access/reports/' . $button['link']) ?>">
                                            <?= mb_strtoupper($button['name'], 'UTF-8') ?>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </fieldset>
                </div>
            <?php endforeach ?>



        </div>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>