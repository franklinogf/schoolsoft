<?php
require_once '../../../../../app.php';

use App\Models\Admin;
use App\Models\Teacher;
use App\Models\WeeklyPlan;
use Classes\Server;
use Classes\Session;
use Classes\Util;

Session::is_logged();
Server::is_post();

$teacher = Teacher::find(Session::id());
$school = Admin::primaryAdmin();
$year = $school->year;

// Get weekly plan
if (isset($_POST['getWeeklyPlan'])) {
    $planId = $_POST['getWeeklyPlan'];
    $weeklyPlan = WeeklyPlan::find($planId);

    if ($weeklyPlan && $weeklyPlan->id == $teacher->id) {
        echo Util::toJson($weeklyPlan);
    } else {
        http_response_code(404);
        echo Util::toJson(['error' => __('Plan no encontrado')]);
    }
    exit;
}

// Create weekly plan
if (isset($_POST['createWeeklyPlan'])) {
    // Procesar materiales
    $mat1 = WeeklyPlan::combineMaterials([
        $_POST['material1-0'] ?? '',
        $_POST['material1-1'] ?? '',
        $_POST['material1-2'] ?? ''
    ]);
    $mat2 = WeeklyPlan::combineMaterials([
        $_POST['material2-0'] ?? '',
        $_POST['material2-1'] ?? '',
        $_POST['material2-2'] ?? ''
    ]);
    $mat3 = WeeklyPlan::combineMaterials([
        $_POST['material3-0'] ?? '',
        $_POST['material3-1'] ?? '',
        $_POST['material3-2'] ?? ''
    ]);
    $mat4 = WeeklyPlan::combineMaterials([
        $_POST['material4-0'] ?? '',
        $_POST['material4-1'] ?? '',
        $_POST['material4-2'] ?? ''
    ]);
    $mat5 = WeeklyPlan::combineMaterials([
        $_POST['material5-0'] ?? '',
        $_POST['material5-1'] ?? '',
        $_POST['material5-2'] ?? ''
    ]);

    // Procesar inicio
    $ini1 = WeeklyPlan::combineMaterials([
        $_POST['inicio1-0'] ?? '',
        $_POST['inicio1-1'] ?? ''
    ]);
    $ini2 = WeeklyPlan::combineMaterials([
        $_POST['inicio2-0'] ?? '',
        $_POST['inicio2-1'] ?? ''
    ]);
    $ini3 = WeeklyPlan::combineMaterials([
        $_POST['inicio3-0'] ?? '',
        $_POST['inicio3-1'] ?? ''
    ]);
    $ini4 = WeeklyPlan::combineMaterials([
        $_POST['inicio4-0'] ?? '',
        $_POST['inicio4-1'] ?? ''
    ]);
    $ini5 = WeeklyPlan::combineMaterials([
        $_POST['inicio5-0'] ?? '',
        $_POST['inicio5-1'] ?? ''
    ]);

    // Procesar desarrollo
    $des1 = WeeklyPlan::combineMaterials([
        $_POST['desarrollo1-0'] ?? '',
        $_POST['desarrollo1-1'] ?? ''
    ]);
    $des2 = WeeklyPlan::combineMaterials([
        $_POST['desarrollo2-0'] ?? '',
        $_POST['desarrollo2-1'] ?? ''
    ]);
    $des3 = WeeklyPlan::combineMaterials([
        $_POST['desarrollo3-0'] ?? '',
        $_POST['desarrollo3-1'] ?? ''
    ]);
    $des4 = WeeklyPlan::combineMaterials([
        $_POST['desarrollo4-0'] ?? '',
        $_POST['desarrollo4-1'] ?? ''
    ]);
    $des5 = WeeklyPlan::combineMaterials([
        $_POST['desarrollo5-0'] ?? '',
        $_POST['desarrollo5-1'] ?? ''
    ]);

    // Procesar cierre
    $cie1 = WeeklyPlan::combineMaterials([
        $_POST['cierre1-0'] ?? '',
        $_POST['cierre1-1'] ?? ''
    ]);
    $cie2 = WeeklyPlan::combineMaterials([
        $_POST['cierre2-0'] ?? '',
        $_POST['cierre2-1'] ?? ''
    ]);
    $cie3 = WeeklyPlan::combineMaterials([
        $_POST['cierre3-0'] ?? '',
        $_POST['cierre3-1'] ?? ''
    ]);
    $cie4 = WeeklyPlan::combineMaterials([
        $_POST['cierre4-0'] ?? '',
        $_POST['cierre4-1'] ?? ''
    ]);
    $cie5 = WeeklyPlan::combineMaterials([
        $_POST['cierre5-0'] ?? '',
        $_POST['cierre5-1'] ?? ''
    ]);

    // Procesar assessment
    $asse1 = WeeklyPlan::combineMaterials([
        $_POST['assess1-0'] ?? '',
        $_POST['assess1-1'] ?? ''
    ]);
    $asse2 = WeeklyPlan::combineMaterials([
        $_POST['assess2-0'] ?? '',
        $_POST['assess2-1'] ?? ''
    ]);
    $asse3 = WeeklyPlan::combineMaterials([
        $_POST['assess3-0'] ?? '',
        $_POST['assess3-1'] ?? ''
    ]);
    $asse4 = WeeklyPlan::combineMaterials([
        $_POST['assess4-0'] ?? '',
        $_POST['assess4-1'] ?? ''
    ]);
    $asse5 = WeeklyPlan::combineMaterials([
        $_POST['assess5-0'] ?? '',
        $_POST['assess5-1'] ?? ''
    ]);

    $weeklyPlan = WeeklyPlan::create([
        'id' => $teacher->id,
        'year' => $year,
        'clase' => $_POST['clase'] ?? '',
        'grado' => $_POST['grado'] ?? '',
        'tema' => $_POST['tema'] ?? '',
        'fecha' => $_POST['fecha'] ?? '',
        'leccion' => $_POST['leccion'] ?? '',
        'est' => $_POST['estand'] ?? '',
        'exp' => $_POST['expec'] ?? '',
        'obj_gen' => $_POST['objGen'] ?? '',
        'nivel1' => $_POST['nivel1'] ?? '',
        'nivel2' => $_POST['nivel2'] ?? '',
        'nivel3' => $_POST['nivel3'] ?? '',
        'nivel4' => $_POST['nivel4'] ?? '',
        'lst_v1' => $_POST['list1'] ?? '',
        'lst_v2' => $_POST['list2'] ?? '',
        'lst_v3' => $_POST['list3'] ?? '',
        'lst_v4' => $_POST['list4'] ?? '',
        'act1' => $_POST['activi1'] ?? '',
        'act2' => $_POST['activi2'] ?? '',
        'act3' => $_POST['activi3'] ?? '',
        'act4' => $_POST['activi4'] ?? '',
        'act5' => $_POST['activi5'] ?? '',
        'mat1' => $mat1,
        'mat2' => $mat2,
        'mat3' => $mat3,
        'mat4' => $mat4,
        'mat5' => $mat5,
        'ini1' => $ini1,
        'ini2' => $ini2,
        'ini3' => $ini3,
        'ini4' => $ini4,
        'ini5' => $ini5,
        'des1' => $des1,
        'des2' => $des2,
        'des3' => $des3,
        'des4' => $des4,
        'des5' => $des5,
        'cie1' => $cie1,
        'cie2' => $cie2,
        'cie3' => $cie3,
        'cie4' => $cie4,
        'cie5' => $cie5,
        'asse1' => $asse1,
        'asse2' => $asse2,
        'asse3' => $asse3,
        'asse4' => $asse4,
        'asse5' => $asse5,
        'otros_m1' => $_POST['otros_m1'] ?? '',
        'otros_m2' => $_POST['otros_m2'] ?? '',
        'otros_m3' => $_POST['otros_m3'] ?? '',
        'otros_m4' => $_POST['otros_m4'] ?? '',
        'otros_m5' => $_POST['otros_m5'] ?? '',
        'otros_i1' => $_POST['otros_i1'] ?? '',
        'otros_i2' => $_POST['otros_i2'] ?? '',
        'otros_i3' => $_POST['otros_i3'] ?? '',
        'otros_i4' => $_POST['otros_i4'] ?? '',
        'otros_i5' => $_POST['otros_i5'] ?? '',
        'otros_d1' => $_POST['otros_d1'] ?? '',
        'otros_d2' => $_POST['otros_d2'] ?? '',
        'otros_d3' => $_POST['otros_d3'] ?? '',
        'otros_d4' => $_POST['otros_d4'] ?? '',
        'otros_d5' => $_POST['otros_d5'] ?? '',
        'otros_c1' => $_POST['otros_c1'] ?? '',
        'otros_c2' => $_POST['otros_c2'] ?? '',
        'otros_c3' => $_POST['otros_c3'] ?? '',
        'otros_c4' => $_POST['otros_c4'] ?? '',
        'otros_c5' => $_POST['otros_c5'] ?? '',
        'otros_a1' => $_POST['otros_a1'] ?? '',
        'otros_a2' => $_POST['otros_a2'] ?? '',
        'otros_a3' => $_POST['otros_a3'] ?? '',
        'otros_a4' => $_POST['otros_a4'] ?? '',
        'otros_a5' => $_POST['otros_a5'] ?? '',
        'coment' => $_POST['coment'] ?? '',
    ]);

    echo Util::toJson(['success' => true, 'id' => $weeklyPlan->id2]);
    exit;
}

// Update weekly plan
if (isset($_POST['updateWeeklyPlan'])) {
    $weeklyPlanId = $_POST['weeklyPlanId'];
    $weeklyPlan = WeeklyPlan::find($weeklyPlanId);

    if ($weeklyPlan && $weeklyPlan->id == $teacher->id) {
        // Procesar materiales
        $mat1 = WeeklyPlan::combineMaterials([
            $_POST['material1-0'] ?? '',
            $_POST['material1-1'] ?? '',
            $_POST['material1-2'] ?? ''
        ]);
        $mat2 = WeeklyPlan::combineMaterials([
            $_POST['material2-0'] ?? '',
            $_POST['material2-1'] ?? '',
            $_POST['material2-2'] ?? ''
        ]);
        $mat3 = WeeklyPlan::combineMaterials([
            $_POST['material3-0'] ?? '',
            $_POST['material3-1'] ?? '',
            $_POST['material3-2'] ?? ''
        ]);
        $mat4 = WeeklyPlan::combineMaterials([
            $_POST['material4-0'] ?? '',
            $_POST['material4-1'] ?? '',
            $_POST['material4-2'] ?? ''
        ]);
        $mat5 = WeeklyPlan::combineMaterials([
            $_POST['material5-0'] ?? '',
            $_POST['material5-1'] ?? '',
            $_POST['material5-2'] ?? ''
        ]);

        // Procesar inicio
        $ini1 = WeeklyPlan::combineMaterials([
            $_POST['inicio1-0'] ?? '',
            $_POST['inicio1-1'] ?? ''
        ]);
        $ini2 = WeeklyPlan::combineMaterials([
            $_POST['inicio2-0'] ?? '',
            $_POST['inicio2-1'] ?? ''
        ]);
        $ini3 = WeeklyPlan::combineMaterials([
            $_POST['inicio3-0'] ?? '',
            $_POST['inicio3-1'] ?? ''
        ]);
        $ini4 = WeeklyPlan::combineMaterials([
            $_POST['inicio4-0'] ?? '',
            $_POST['inicio4-1'] ?? ''
        ]);
        $ini5 = WeeklyPlan::combineMaterials([
            $_POST['inicio5-0'] ?? '',
            $_POST['inicio5-1'] ?? ''
        ]);

        // Procesar desarrollo
        $des1 = WeeklyPlan::combineMaterials([
            $_POST['desarrollo1-0'] ?? '',
            $_POST['desarrollo1-1'] ?? ''
        ]);
        $des2 = WeeklyPlan::combineMaterials([
            $_POST['desarrollo2-0'] ?? '',
            $_POST['desarrollo2-1'] ?? ''
        ]);
        $des3 = WeeklyPlan::combineMaterials([
            $_POST['desarrollo3-0'] ?? '',
            $_POST['desarrollo3-1'] ?? ''
        ]);
        $des4 = WeeklyPlan::combineMaterials([
            $_POST['desarrollo4-0'] ?? '',
            $_POST['desarrollo4-1'] ?? ''
        ]);
        $des5 = WeeklyPlan::combineMaterials([
            $_POST['desarrollo5-0'] ?? '',
            $_POST['desarrollo5-1'] ?? ''
        ]);

        // Procesar cierre
        $cie1 = WeeklyPlan::combineMaterials([
            $_POST['cierre1-0'] ?? '',
            $_POST['cierre1-1'] ?? ''
        ]);
        $cie2 = WeeklyPlan::combineMaterials([
            $_POST['cierre2-0'] ?? '',
            $_POST['cierre2-1'] ?? ''
        ]);
        $cie3 = WeeklyPlan::combineMaterials([
            $_POST['cierre3-0'] ?? '',
            $_POST['cierre3-1'] ?? ''
        ]);
        $cie4 = WeeklyPlan::combineMaterials([
            $_POST['cierre4-0'] ?? '',
            $_POST['cierre4-1'] ?? ''
        ]);
        $cie5 = WeeklyPlan::combineMaterials([
            $_POST['cierre5-0'] ?? '',
            $_POST['cierre5-1'] ?? ''
        ]);

        // Procesar assessment
        $asse1 = WeeklyPlan::combineMaterials([
            $_POST['assess1-0'] ?? '',
            $_POST['assess1-1'] ?? ''
        ]);
        $asse2 = WeeklyPlan::combineMaterials([
            $_POST['assess2-0'] ?? '',
            $_POST['assess2-1'] ?? ''
        ]);
        $asse3 = WeeklyPlan::combineMaterials([
            $_POST['assess3-0'] ?? '',
            $_POST['assess3-1'] ?? ''
        ]);
        $asse4 = WeeklyPlan::combineMaterials([
            $_POST['assess4-0'] ?? '',
            $_POST['assess4-1'] ?? ''
        ]);
        $asse5 = WeeklyPlan::combineMaterials([
            $_POST['assess5-0'] ?? '',
            $_POST['assess5-1'] ?? ''
        ]);

        $weeklyPlan->update([
            'clase' => $_POST['clase'] ?? '',
            'grado' => $_POST['grado'] ?? '',
            'tema' => $_POST['tema'] ?? '',
            'fecha' => $_POST['fecha'] ?? '',
            'leccion' => $_POST['leccion'] ?? '',
            'est' => $_POST['estand'] ?? '',
            'exp' => $_POST['expec'] ?? '',
            'obj_gen' => $_POST['objGen'] ?? '',
            'nivel1' => $_POST['nivel1'] ?? '',
            'nivel2' => $_POST['nivel2'] ?? '',
            'nivel3' => $_POST['nivel3'] ?? '',
            'nivel4' => $_POST['nivel4'] ?? '',
            'lst_v1' => $_POST['list1'] ?? '',
            'lst_v2' => $_POST['list2'] ?? '',
            'lst_v3' => $_POST['list3'] ?? '',
            'lst_v4' => $_POST['list4'] ?? '',
            'act1' => $_POST['activi1'] ?? '',
            'act2' => $_POST['activi2'] ?? '',
            'act3' => $_POST['activi3'] ?? '',
            'act4' => $_POST['activi4'] ?? '',
            'act5' => $_POST['activi5'] ?? '',
            'mat1' => $mat1,
            'mat2' => $mat2,
            'mat3' => $mat3,
            'mat4' => $mat4,
            'mat5' => $mat5,
            'ini1' => $ini1,
            'ini2' => $ini2,
            'ini3' => $ini3,
            'ini4' => $ini4,
            'ini5' => $ini5,
            'des1' => $des1,
            'des2' => $des2,
            'des3' => $des3,
            'des4' => $des4,
            'des5' => $des5,
            'cie1' => $cie1,
            'cie2' => $cie2,
            'cie3' => $cie3,
            'cie4' => $cie4,
            'cie5' => $cie5,
            'asse1' => $asse1,
            'asse2' => $asse2,
            'asse3' => $asse3,
            'asse4' => $asse4,
            'asse5' => $asse5,
            'otros_m1' => $_POST['otros_m1'] ?? '',
            'otros_m2' => $_POST['otros_m2'] ?? '',
            'otros_m3' => $_POST['otros_m3'] ?? '',
            'otros_m4' => $_POST['otros_m4'] ?? '',
            'otros_m5' => $_POST['otros_m5'] ?? '',
            'otros_i1' => $_POST['otros_i1'] ?? '',
            'otros_i2' => $_POST['otros_i2'] ?? '',
            'otros_i3' => $_POST['otros_i3'] ?? '',
            'otros_i4' => $_POST['otros_i4'] ?? '',
            'otros_i5' => $_POST['otros_i5'] ?? '',
            'otros_d1' => $_POST['otros_d1'] ?? '',
            'otros_d2' => $_POST['otros_d2'] ?? '',
            'otros_d3' => $_POST['otros_d3'] ?? '',
            'otros_d4' => $_POST['otros_d4'] ?? '',
            'otros_d5' => $_POST['otros_d5'] ?? '',
            'otros_c1' => $_POST['otros_c1'] ?? '',
            'otros_c2' => $_POST['otros_c2'] ?? '',
            'otros_c3' => $_POST['otros_c3'] ?? '',
            'otros_c4' => $_POST['otros_c4'] ?? '',
            'otros_c5' => $_POST['otros_c5'] ?? '',
            'otros_a1' => $_POST['otros_a1'] ?? '',
            'otros_a2' => $_POST['otros_a2'] ?? '',
            'otros_a3' => $_POST['otros_a3'] ?? '',
            'otros_a4' => $_POST['otros_a4'] ?? '',
            'otros_a5' => $_POST['otros_a5'] ?? '',
            'coment' => $_POST['coment'] ?? '',
        ]);

        echo Util::toJson(['success' => true, 'id' => $weeklyPlan->id2]);
    } else {
        echo Util::toJson(['success' => false, 'message' => 'Plan semanal no encontrado']);
    }
    exit;
}

// Delete weekly plan
if (isset($_POST['deleteWeeklyPlan'])) {
    $weeklyPlanId = $_POST['deleteWeeklyPlan'];
    $weeklyPlan = WeeklyPlan::find($weeklyPlanId);

    if ($weeklyPlan && $weeklyPlan->id == $teacher->id) {
        $weeklyPlan->delete();
        echo Util::toJson(['success' => true]);
    } else {
        echo Util::toJson(['success' => false, 'message' => 'Plan semanal no encontrado']);
    }
    exit;
}
