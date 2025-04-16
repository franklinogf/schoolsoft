<?php
require_once '../../../app.php';

use App\Enums\AdminPermission;
use Classes\Route;
use Classes\Session;
use Classes\Lang;
use App\Models\Admin;

Session::is_logged();

$user  = Admin::user(Session::id())->first();

$options = [
    [
        'title' => ["es" => 'Informes', "en" => 'Reports'],
        'buttons' => [
            [
                'name' => ["es" => 'Lista de estudiantes',   "en" => "Students list"],
                'link' => 'pdf/studentsList.php',
                'target' => 'studentsList',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_STUDENTS_LIST)
            ],
            [
                'name' => ["es" => 'Salón hogar',   "en" => "Home classroom"],
                'link' => 'pdf/homeClassroom.php',
                'target' => 'homeClassroom',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_HOMEROOM)
            ],
            [
                'name' => ["es" => 'Totales por grado',   "en" => "Totals by grade"],
                'link' => 'pdf/totalsByGrade.php',
                'target' => 'totalsByGrade',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_TOTALS_BY_GRADE)
            ],
            [
                'name' => ["es" => 'Lista de firmas',   "en" => "list of signs"],
                'link' => 'pdf/listOfSigns.php',
                'target' => 'listOfSigns',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_SIGNATURE_LIST)
            ],
            [
                'name' => ["es" => 'Lista de usuarios',   "en" => "Users list"],
                'link' => 'pdf/usersList.php',
                'target' => 'usersList',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_USERS_LIST)
            ],
            [
                'name' => ["es" => 'Lista Re-Matrícula',   "en" => "Re-enroll list"],
                'link' => 'pdf/reEnroll.php',
                'target' => 'reEnroll',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_RE_ENROLLMENT_LIST)
            ],
            [
                'name' => ["es" => 'Asistencia diaria',   "en" => "Daily attendance"],
                'link' => 'dailyAttendance.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_DAILY_ATTENDANCE)
            ],
            [
                'name' => ["es" => 'Cuentas accesadas',   "en" => "Accessed accounts"],
                'link' => 'pdf/accessedAccounts.php',
                'target' => 'accessedAccounts',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_ACCESSED_ACCOUNTS)
            ],
            [
                'name' => ["es" => 'Informe de encuestas',   "en" => "Survey report"],
                'link' => 'survey.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_SURVEY)
            ],
            [
                'name' => ["es" => 'Informe de cuentas de padres',   "en" => "Parents accounts"],
                'link' => 'pdf/parentsAccounts.php',
                'target' => 'parentsAccounts',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_PARENT_ACCOUNTS)
            ],
            [
                'name' => ["es" => 'Cuentas incompletas',   "en" => "Incomplete accounts"],
                'link' => 'pdf/incompleteAccounts.php',
                'target' => 'incompleteAccounts',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_INCOMPLETE_ACCOUNTS)
            ],
            [
                'name' => ["es" => 'Label',   "en" => "Label"],
                'link' => 'label.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_LABEL)
            ],
            [
                'name' => ["es" => 'Informe de familia por grado',   "en" => "Family report by grade"],
                'link' => 'pdf/familyByGrade.php',
                'target' => 'familyByGrade',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_FAMILY_GRADE)
            ],
            [
                'name' => ["es" => 'Lista por cuentas',   "en" => "Accounts list"],
                'link' => 'pdf/accountsList.php',
                'target' => 'accountsList',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_ACCOUNTS_LIST)
            ],
            [
                'name' => ["es" => 'Hoja de matrícula',   "en" => "Enrollment sheet"],
                'link' => 'enrollment.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_ENROLLMENT_FORM)
            ],
            [
                'name' => ["es" => 'Estudiantes nuevos',   "en" => "New students"],
                'link' => 'newStudent.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_NEW_STUDENTS)
            ],
            [
                'name' => ["es" => 'Lista de descuentos',   "en" => "Discount list"],
                'link' => 'pdf/discountList.php',
                'target' => 'discountList',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_DISCOUNTS_LIST)
            ],
            [
                'name' => ["es" => 'Medicamentos / Recetas',   "en" => "Medicine / Prescriptions"],
                'link' => 'pdf/medicineStudent.php',
                'target' => 'medicineStudent',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_MEDICATIONS)
            ],
            [
                'name' => ["es" => 'Lista de bajas',   "en" => "Drop out list"],
                'link' => 'pdf/dropList.php',
                'target' => 'dropList',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_DROPOUT_LIST)
            ],
            [
                'name' => ["es" => 'Condiciones / alergias',   "en" => "Conditions / Allergy"],
                'link' => 'pdf/allergyStudent.php',
                'target' => 'allergyStudent',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_CONDITIONS_ALLERGIES)
            ],
            [
                'name' => ["es" => 'Lista de teléfonos',   "en" => "Phone list"],
                'link' => 'pdf/phoneList.php',
                'target' => 'phoneList',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_PHONE_LIST)
            ],
            [
                'name' => ["es" => 'Lista de cumpleaños',   "en" => "Birthday list"],
                'link' => 'birthdayList.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_BIRTHDAYS_LIST)
            ],
            [
                'name' => ["es" => 'Matrícula por salón',   "en" => "Enrollment classroom list"],
                'link' => 'enrollmentClassroom.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_ENROLLMENT_BY_CLASS)
            ],
            [
                'name' => ["es" => 'Lista de correos',   "en" => "E-Mail list"],
                'link' => 'emailList.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_EMAIL_LIST)
            ],
            [
                'name' => ["es" => 'Lista de padres',   "en" => "Parents list"],
                'link' => 'parentList.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_PARENTS_LIST)
            ],
            [
                'name' => ["es" => 'Lista de trabajos de padres',   "en" => "Parents work list"],
                'link' => 'jobList.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_PARENT_WORK_LIST)
            ],
            [
                'name' => ["es" => 'Lista de dirección postal',   "en" => "Postal address list"],
                'link' => 'addressList.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_POSTAL_ADDRESS)
            ],
            [
                'name' => ["es" => 'Listado de religión',   "en" => "Religion list"],
                'link' => 'religionList.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_RELIGION)
            ],
            [
                'name' => ["es" => 'Usuarios y claves',   "en" => "Users and passwords"],
                'link' => 'UsersList.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_USERS_AND_PASSWORDS)
            ],
            [
                'name' => ["es" => 'Carta certificada',   "en" => "Registered letter"],
                'link' => 'registeredLetter.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_CERTIFIED_LETTER)
            ],
            [
                'name' => ["es" => 'Lista de maestros',   "en" => "Teachers list"],
                'link' => 'pdf/teacherList.php',
                'target' => 'teacherList',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_TEACHERS_LIST)
            ],
            [
                'name' => ["es" => 'Correos de maestros',   "en" => "Teacher E-Mail"],
                'link' => 'pdf/teacherEmails.php',
                'target' => 'teacherEmails',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_TEACHERS_EMAIL)
            ],
            [
                'name' => ["es" => 'Teléfonos de maestros',   "en" => "Teacher Phone"],
                'link' => 'pdf/telProfesor.php',
                'target' => 'telProfesor',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_TEACHERS_PHONE)
            ],
            [
                'name' => ["es" => 'Lista de niveles',   "en" => "Level List"],
                'link' => 'pdf/levelList.php',
                'target' => 'levelList',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_LEVELS_LIST)
            ],
            [
                'name' => ["es" => 'Salón hogar de maestros',   "en" => "Teachers home room list"],
                'link' => 'pdf/homeProfesor.php',
                'target' => 'homeProfesor',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_HOMEROOM_TEACHERS)
            ],
            [
                'name' => ["es" => 'Lista de firmas de maestros',   "en" => "List oe signatures Teachers"],
                'link' => 'pdf/firmaProfesor.php',
                'target' => 'firmaProfesor',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_TEACHERS_SIGNATURE_LIST)
            ],
            [
                'name' => ["es" => 'Direcciones de maestros',   "en" => "Teacher address"],
                'link' => 'addressProfesor.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_TEACHERS_ADDRESS)
            ],
            [
                'name' => ["es" => 'Preparación de maestros',   "en" => "Teachers preparation"],
                'link' => 'pdf/preProfesor.php',
                'target' => 'preProfesor',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_TEACHERS_PREPARATION)
            ],
            [
                'name' => ["es" => 'Lista de club de maestros',   "en" => "Teachers clud List"],
                'link' => 'pdf/clubProfesor.php',
                'target' => 'clubProfesor',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_TEACHERS_CLUB_LIST)
            ],
            [
                'name' => ["es" => 'Informe socioeconómico',   "en" => "Socioeconomic report"],
                'link' => 'socioEconomicReport.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_SOCIO_ECONOMIC)
            ],
            [
                'name' => ["es" => 'Licencia de maestros',   "en" => "Teacher licenses"],
                'link' => 'licenseTeacher.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_TEACHERS_LICENSE)
            ],
            [
                'name' => ["es" => 'No docentes',   "en" => "Not teachers"],
                'link' => 'notTeachers.php',
                'hidden' => !$user->hasPermissionTo(AdminPermission::ACCESS_REPORTS_NON_TEACHING_STAFF)
            ],
            [
                'name' => ["es" => 'Total por grado',   "en" => "Total grade"],
                'link' => 'pdf/totalGrade.php',
                'target' => 'totalGrade'
            ],
            [
                'name' => ["es" => 'Lista con fotos',   "en" => "List with photos"],
                'link' => 'pdf/ListPhotos.php',
                'target' => 'ListPhotos'
            ],
            [
                'name' => ["es" => 'Acuse de recibo',   "en" => "Acknowledgment of receipt"],
                'link' => 'AcknowledgmentReceipt.php'
            ],
            [
                'name' => ["es" => 'Informe de grado y edad',   "en" => "Report by grade and age"],
                'link' => 'pdf/ageClassroom.php',
                'target' => 'ageClassroom'
            ],
            [
                'name' => ["es" => 'Listado de clubes',   "en" => "Club List"],
                'link' => 'pdf/clubList.php',
                'target' => 'clubList'
            ],
            [
                'name' => ["es" => 'Información por grado',   "en" => "Information by grade"],
                'link' => 'InformationByGrade.php'
            ],
            [
                'name' => ["es" => 'Lista personas autorizadas',   "en" => "List of authorized persons"],
                'link' => 'pdf/listOFauthorizedPersons.php',
                'target' => 'listOFauthorizedPersons'
            ],
            [
                'name' => ["es" => 'Direcciones y telefonos por grado',   "en" => "Addresses and telephones by grade"],
                'link' => 'pdf/listAddressphone.php',
                'target' => 'listAddressphone'
            ],
            [
                'name' => ["es" => 'Lista de exalumnos',   "en" => "Alumni list"],
                'link' => 'pdf/alumniList.php',
                'target' => 'alumniList'
            ],
            [
                'name' => ["es" => 'Lista de no becados',   "en" => "List without scholarships"],
                'link' => 'pdf/listWithoutScholarships.php',
                'target' => 'listWithoutScholarships'
            ],
            [
                'name' => ["es" => 'Lista estudiantes KG-12',   "en" => "KG-12 Student list"],
                'link' => 'pdf/studentListK12.php',
                'target' => 'studentListK12'
            ],
            [
                'name' => ["es" => 'Lista de E/P/G/E/T',   "en" => "S/F/G/P/C list"],
                'link' => 'pdf/listInformation.php',
                'target' => 'listInformation'
            ],
            [
                'name' => ["es" => 'Plan semanal 3 pasar a notas',   "en" => "Plan semanal 3"],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Informe de raza',   "en" => "Race List"],
                'link' => 'pdf/listInformeRaza.php',
                'target' => 'listInformeRaza'
            ],
            [
                'name' => ["es" => 'Informe de religión',   "en" => "Religion Report"],
                'link' => 'pdf/listInformeReligion.php',
                'target' => 'listInformeReligion'
            ],
            [
                'name' => ["es" => 'Informe de raza y religión',   "en" => "Race and Religion Report"],
                'link' => 'pdf/listRaceeReligion.php',
                'target' => 'listRaceReligion'
            ],
            [
                'name' => ["es" => 'Resumen de raza y religión',   "en" => "Summary of race and religion"],
                'link' => 'pdf/ResumRaceeReligion.php',
                'target' => 'ResumRaceeReligion'
            ],
            [
                'name' => ["es" => 'Documentos no entregados',   "en" => "Documents not delivered"],
                'link' => 'pdf/DocumentsNotDelivered.php'
            ],
            [
                'name' => ["es" => 'Formulario socioeconómico',   "en" => "Socioeconomic form"],
                'link' => 'formSocio.php'
            ],
            [
                'name' => ["es" => 'Lista de edad y seguro social',   "en" => "Age and social security list"],
                'link' => 'pdf/ageSsClassroom.php',
                'target' => 'ageSsClassroom'
            ],
            [
                'name' => ["es" => 'Distribución de notas pasar a notas',   "en" => "Distribución de notas"],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Informe de procedencia',   "en" => "Provenance report"],
                'link' => 'pdf/infProcedencia.php',
                'target' => 'infProcedencia'
            ],
            [
                'name' => ["es" => 'Movimiento de matrícula',   "en" => "School enrollment movement"],
                'link' => 'pdf/SchoolMovement.php',
                'target' => 'SchoolMovement'
            ],
            [
                'name' => ["es" => 'Informe acumulativo de notas pasar a notas',   "en" => "Informe acumulativo de notas"],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Progreso de educación fisica pasar a notas',   "en" => "Progreso de educación fisica"],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Evaluación de educación fisica pasar a notas',   "en" => "Evaluación de educación fisica"],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Pruebas diagnósticas',   "en" => "Diagnostic tests"],
                'link' => 'pdf/diagnosticTests.php',
                'target' => 'diagnosticTests'
            ],
            [
                'name' => ["es" => 'Pruebas de selección',   "en" => "Selection tests"],
                'link' => 'pdf/selectiontests.php',
                'target' => 'selectiontests'
            ],
            [
                'name' => ["es" => 'Carta de recomendación',   "en" => "Letter of recommendation"],
                'link' => 'LetterRecommendation.php'
            ],
            [
                'name' => ["es" => 'Información personal',   "en" => "Información personal"],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Totales información personal',   "en" => "Totales información personal"],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Información básica',   "en" => "Información básica"],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Totales de información básica',   "en" => "Totales de información básica"],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Informe de acceso de maestros',   "en" => "Teacher Access Report"],
                'link' => 'TeacherAccessReport.php'
            ],
            [
                'name' => ["es" => 'Informe de acceso de los padres',   "en" => "Parents Access Report"],
                'link' => 'ParentsAccessReport.php'
            ],
            [
                'name' => ["es" => 'Informe de acceso de los administradores',   "en" => "Administrator Access Report"],
                'link' => 'AdminAccessReport.php'
            ],
            [
                'name' => ["es" => 'Informe de documentos',   "en" => "Document report"],
                'link' => 'DocumentReport.php'
            ],
            [
                'name' => ["es" => 'ID del estudiante',   "en" => "Student ID"],
                'link' => 'studentID.php'
            ],
            [
                'name' => ["es" => 'ID del maestro',   "en" => "Teacher ID"],
                'link' => 'teacherID.php'
            ],
            [
                'name' => ["es" => 'Acomodo Razonable',   "en" => "Reasonable accommodation"],
                'link' => 'pdf/ReasonableAccommodation.php',
                'target' => 'ReasonableAccommodation'
            ],
            [
                'name' => ["es" => 'Informe acumulativo de notas pasar a notas',   "en" => "Cumulative grade report"],
                'link' => 'pdf/CumulativeGradeReport.php',
                'target' => 'CumulativeGradeReport'
            ],
            [
                'name' => ["es" => 'Listado PP - 06',   "en" => "List PP - 06"],
                'link' => 'pdf/inf_pp_06.php',
                'target' => 'inf_pp_06'
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
    $title = $lang->translation("Informes");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-3"><?= $lang->translation("Informes") ?></h1>

        <div class="mx-2 mx-md-0 justify-content-around">

            <?php foreach ($options as $option) : ?>
                <div class="col mb-4">
                    <fieldset class="border border-secondary rounded-bottom h-100 px-2">
                        <legend class="w-auto"><?= $option['title'][__LANG] ?></legend>
                        <div class="pb-3">
                            <div class="row row-cols-2 row-cols-md-3">
                                <?php foreach ($option['buttons'] as $button) : ?>
                                    <?php if ($button['hidden']) continue  ?>
                                    <div class="col mt-1 d-flex">
                                        <a style="font-size: .8em;" title="<?= isset($button['desc'][__LANG]) ? $button['desc'][__LANG] : '' ?>" <?= isset($button['target']) ? "target='{$button['target']}'" : '' ?> class="btn btn-primary btn-block flex-shrink-1" href="<?= $button['link'] ?>"><?= mb_strtoupper($button['name'][__LANG], 'UTF-8') ?></a>
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