<?php
require_once '../../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Lang;

Session::is_logged();

$options = [
    [
        'title' => ["es" => 'Informes', "en" => 'Reports'],
        'buttons' => [ 
            [
                'name' => ["es" => 'Lista de estudiantes',   "en" => "Students list"],
                'link' => 'pdf/studentsList.php',
                'target' => 'studentsList'
            ],
            [
                'name' => ["es" => 'Salón hogar',   "en" => "Home classroom"],
                'link' => 'pdf/homeClassroom.php',
                'target' => 'homeClassroom'
            ],
            [
                'name' => ["es" => 'Totales por grado',   "en" => "Totals by grade"],
                'link' => 'pdf/totalsByGrade.php',
                'target' => 'totalsByGrade'
            ],
            [
                'name' => ["es" => 'Lista de firmas',   "en" => "list of signs"],
                'link' => 'pdf/listOfSigns.php',
                'target' => 'listOfSigns'
            ],
            [
                'name' => ["es" => 'Lista de usuarios',   "en" => "Users list"],
                'link' => 'pdf/usersList.php',
                'target' => 'usersList'
            ],
            [
                'name' => ["es" => 'Lista Re-Matrícula',   "en" => "Re-enroll list"],
                'link' => 'pdf/reEnroll.php',
                'target' => 'reEnroll'
            ],
            [
                'name' => ["es" => 'Asistencia diaria',   "en" => "Daily attendance"],
                'link' => 'dailyAttendance.php'
            ],
            [
                'name' => ["es" => 'Cuentas accesadas',   "en" => "Accessed accounts"],
                'link' => 'pdf/accessedAccounts.php',
                'target' => 'accessedAccounts'
            ],
            [
                'name' => ["es" => 'Informe de encuestas',   "en" => "Survey report"],
                'link' => 'survey.php'
            ],
            [
                'name' => ["es" => 'Informe de cuentas de padres',   "en" => "Parents accounts"],
                'link' => 'pdf/parentsAccounts.php',
                'target' => 'parentsAccounts'
            ],
            [
                'name' => ["es" => 'Cuentas incompletas',   "en" => "Incomplete accounts"],
                'link' => 'pdf/incompleteAccounts.php',
                'target' => 'incompleteAccounts'
            ],
            [
                'name' => ["es" => 'Label',   "en" => "Label"],
                'link' => 'label.php',
            ],
            [
                'name' => ["es" => 'Informe de familia por grado',   "en" => "Family report by grade"],
                'link' => 'pdf/familyByGrade.php',
                'target' => 'familyByGrade'
            ],
            [
                'name' => ["es" => 'Lista por cuentas',   "en" => "Accounts list"],
                'link' => 'pdf/accountsList.php',
                'target' => 'accountsList'
            ],
            [
                'name' => ["es" => 'Hoja de matrícula',   "en" => "Enrollment sheet"],
                'link' => 'enrollment.php'
            ],
            [
                'name' => ["es" => 'Estudiantes nuevos',   "en" => "New students"],
                'link' => 'newStudent.php'
            ],
            [
                'name' => ["es" => 'Lista de descuentos',   "en" => "Discount list"],
                'link' => 'pdf/discountList.php',
                'target' => 'discountList'
            ],
            [
                'name' => ["es" => 'Medicamentos / Recetas',   "en" => "Medicine / Prescriptions"],
                'link' => 'pdf/medicineStudent.php',
                'target' => 'medicineStudent'
            ],
            [
                'name' => ["es" => 'Lista de bajas',   "en" => "Drop out list"],
                'link' => 'pdf/dropList.php',
                'target' => 'dropList'
            ],
            [
                'name' => ["es" => 'Condiciones / alergias',   "en" => "Conditions / Allergy"],
                'link' => 'pdf/allergyStudent.php',
                'target' => 'allergyStudent'
            ],
            [
                'name' => ["es" => 'Lista de teléfonos',   "en" => "Phone list"],
                'link' => 'pdf/phoneList.php',
                'target' => 'phoneList'
            ],
            [
                'name' => ["es" => 'Lista de cumpleaños',   "en" => "Birthday list"],
                'link' => 'birthdayList.php'
            ],
            [
                'name' => ["es" => 'Matrícula por salón',   "en" => "Enrollment classroom list"],
                'link' => 'enrollmentClassroom.php'
            ],
            [
                'name' => ["es" => 'Lista de correos',   "en" => "E-Mail list"],
                'link' => 'emailList.php'
            ],
            [
                'name' => ["es" => 'Lista de padres',   "en" => "Parents list"],
                'link' => 'parentList.php'
            ],
            [
                'name' => ["es" => 'Lista de trabajos de padres',   "en" => "Parents work list"],
                'link' => 'jobList.php'
            ],
            [
                'name' => ["es" => 'Lista de dirección postal',   "en" => "Postal address list"],
                'link' => 'addressList.php'
            ],
            [
                'name' => ["es" => 'Listado de religión',   "en" => "Religion list"],
                'link' => 'religionList.php'
            ],
            [
                'name' => ["es" => 'Usuarios y claves',   "en" => "Users and passwords"],
                'link' => 'UsersList.php'
            ],
            [
                'name' => ["es" => 'Carta certificada',   "en" => "Registered letter"],
                'link' => 'registeredLetter.php'
            ],
            [
                'name' => ["es" => 'Lista de maestros',   "en" => "Teachers list"],
                'link' => 'pdf/teacherList.php',
                'target' => 'teacherList'
            ],
            [
                'name' => ["es" => 'Correos de maestros',   "en" => "Teacher E-Mail"],
                'link' => 'pdf/teacherEmails.php',
                'target' => 'teacherEmails'
            ],
            [
                'name' => ["es" => 'Teléfonos de maestros',   "en" => "Teacher Phone"],
                'link' => 'pdf/telProfesor.php',
                'target' => 'telProfesor'
            ],
            [
                'name' => ["es" => 'Lista de niveles',   "en" => "Level List"],
                'link' => 'pdf/levelList.php',
                'target' => 'levelList'
            ],
            [
                'name' => ["es" => 'Salón hogar de maestros',   "en" => "Teachers home room list"],
                'link' => 'pdf/homeProfesor.php',
                'target' => 'homeProfesor'
            ],
            [
                'name' => ["es" => 'Lista de firmas de maestros',   "en" => "List oe signatures Teachers"],
                'link' => 'pdf/firmaProfesor.php',
                'target' => 'firmaProfesor'
            ],
            [
                'name' => ["es" => 'Direcciones de maestros',   "en" => "Teacher address"],
                'link' => 'addressProfesor.php'
            ],
            [
                'name' => ["es" => 'Preparación de maestros',   "en" => "Teachers preparation"],
                'link' => 'pdf/preProfesor.php',
                'target' => 'preProfesor'
            ],
            [
                'name' => ["es" => 'Lista de club de maestros',   "en" => "Teachers clud List"],
                'link' => 'pdf/clubProfesor.php',
                'target' => 'clubProfesor'
            ],
            [
                'name' => ["es" => 'Informe socioeconómico',   "en" => "Socioeconomic report"],
                'link' => 'socioEconomicReport.php',
            ],
            [
                'name' => ["es" => 'Licencia de maestros',   "en" => "Teacher licenses"],
                'link' => 'licenseTeacher.php'
            ],
            [
                'name' => ["es" => 'No docentes',   "en" => "Not teachers"],
                'link' => 'notTeachers.php'
            ],
            [
                'name' => ["es" => 'Total por grado',   "en" => "Total grade"],
                'link' => 'pdf/totalGrade.php',
                'target' => 'totalGrade'
            ],
            [
                'name' => ["es" => 'Lista con fotos',   "en" => "Lista con fotos"],
                'link' => '#'
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
                'name' => ["es" => 'Documentos no entregados',   "en" => "Documentos no entregados"],
                'link' => '#'
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
                'name' => ["es" => 'Movimiento de matrícula',   "en" => "Movimiento de matrícula"],
                'link' => '#'
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
                'name' => ["es" => 'Pruebas diagnósticas',   "en" => "Pruebas diagnósticas"],
                'link' => '#'
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
                'name' => ["es" => 'Formulario de matrícula',   "en" => "Formulario de matrícula"],
                'link' => '#'
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
                'name' => ["es" => 'Informe de documentos',   "en" => "Informe de documentos"],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'ID del estudiante',   "en" => "Student ID"],
                'link' => 'studentID.php'
            ],
            [
                'name' => ["es" => 'ID del maestro',   "en" => "ID del maestro"],
                'link' => '#'
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