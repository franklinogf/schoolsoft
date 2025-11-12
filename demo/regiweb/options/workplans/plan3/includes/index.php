<?php
require_once '../../../../../app.php';

use App\Models\Admin;
use App\Models\Teacher;
use App\Models\WorkPlan;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\Util;

Session::is_logged();
Server::is_post();

$teacher = Teacher::find(Session::id());
$school = Admin::primaryAdmin();
$year = $school->year2;

if (isset($_POST['getWorkPlan'])) {
    $workPlanId = $_POST['getWorkPlan'];
    $workPlan = WorkPlan::find($workPlanId);
    echo Util::toJson($workPlan);
} else if (isset($_POST['createWorkPlan'])) {
    $workPlan = WorkPlan::create([
        'id' => $teacher->id,
        'year' => $year,
        'plan' => $_POST['plan'],
        'grado' => $_POST['grado'],
        'asignatura' => $_POST['asignatura'],
        'mes' => $_POST['mes'],
        'dia1' => $_POST['dia1'],
        'dia2' => $_POST['dia2'],
        'estandares' => $_POST['estandares'] ?? '',
        'tema1' => $_POST['tema1'] ?? '',
        'tema2' => $_POST['tema2'] ?? '',
        'espectativas' => $_POST['espectativas'] ?? '',
        'np1' => $_POST['np1'] ?? '',
        'np2' => $_POST['np2'] ?? '',
        'np3' => $_POST['np3'] ?? '',
        'np4' => $_POST['np4'] ?? '',
        'np5' => $_POST['np5'] ?? '',
        'tema' => $_POST['tema'] ?? '',
        'pre1' => $_POST['pre1'] ?? '',
        'obj1' => $_POST['obj1'] ?? '',
        'obj2' => $_POST['obj2'] ?? '',
        'obj3' => $_POST['obj3'] ?? '',
        'ent1' => $_POST['ent1'] ?? '',
        'ent2' => $_POST['ent2'] ?? '',
        'ent3' => $_POST['ent3'] ?? '',
        'ent4' => $_POST['ent4'] ?? '',
        'ent5' => $_POST['ent5'] ?? '',
        'ent6' => $_POST['ent6'] ?? '',
        'ent7' => $_POST['ent7'] ?? '',
        'ent8' => $_POST['ent8'] ?? '',
        'ent9' => $_POST['ent9'] ?? '',
        'ent10' => $_POST['ent10'] ?? '',
        'ent11' => $_POST['ent11'] ?? '',
        'ent12' => $_POST['ent12'] ?? '',
        'integracion' => $_POST['integracion'] ?? '',
        'act1' => $_POST['act1'] ?? '',
        'act2' => $_POST['act2'] ?? '',
        'act3' => $_POST['act3'] ?? '',
        'act4' => $_POST['act4'] ?? '',
        'ini1' => $_POST['ini1'] ?? '',
        'ini2' => $_POST['ini2'] ?? '',
        'ini3' => $_POST['ini3'] ?? '',
        'ini4' => $_POST['ini4'] ?? '',
        'ini5' => $_POST['ini5'] ?? '',
        'ini6' => $_POST['ini6'] ?? '',
        'ini7' => $_POST['ini7'] ?? '',
        'des1' => $_POST['des1'] ?? '',
        'des2' => $_POST['des2'] ?? '',
        'des3' => $_POST['des3'] ?? '',
        'des4' => $_POST['des4'] ?? '',
        'des5' => $_POST['des5'] ?? '',
        'des6' => $_POST['des6'] ?? '',
        'des7' => $_POST['des7'] ?? '',
        'cie1' => $_POST['cie1'] ?? '',
        'cie2' => $_POST['cie2'] ?? '',
        'cie3' => $_POST['cie3'] ?? '',
        'cie4' => $_POST['cie4'] ?? '',
        'cie5' => $_POST['cie5'] ?? '',
        'eva1' => $_POST['eva1'] ?? '',
        'eva2' => $_POST['eva2'] ?? '',
        'eva3' => $_POST['eva3'] ?? '',
        'eva4' => $_POST['eva4'] ?? '',
        'tab1' => $_POST['tab1'] ?? '',
        'tab2' => $_POST['tab2'] ?? '',
        'tab3' => $_POST['tab3'] ?? '',
        'tab4' => $_POST['tab4'] ?? '',
        'tab5' => $_POST['tab5'] ?? '',
        'tab6' => $_POST['tab6'] ?? '',
        'tab7' => $_POST['tab7'] ?? '',
        'tab8' => $_POST['tab8'] ?? '',
        'sel1' => $_POST['sel1'] ?? '',
        'sel2' => $_POST['sel2'] ?? '',
        'sel3' => $_POST['sel3'] ?? '',
        'sel4' => $_POST['sel4'] ?? '',
        'sel5' => $_POST['sel5'] ?? '',
        'pro1' => $_POST['pro1'] ?? '',
        'pro2' => $_POST['pro2'] ?? '',
        'otro' => $_POST['otro'] ?? '',
        'autoeva' => $_POST['autoeva'] ?? '',
        'as1' => $_POST['as1'] ?? '',
        'as2' => $_POST['as2'] ?? '',
        'as3' => $_POST['as3'] ?? '',
        'as4' => $_POST['as4'] ?? '',
        'as5' => $_POST['as5'] ?? '',
        'as6' => $_POST['as6'] ?? '',
        'as7' => $_POST['as7'] ?? '',
        'as8' => $_POST['as8'] ?? '',
        'ot1' => $_POST['ot1'] ?? '',
        'ot2' => $_POST['ot2'] ?? '',
        'ot3' => $_POST['ot3'] ?? '',
        'ot4' => $_POST['ot4'] ?? '',
        'otr1' => $_POST['otr1'] ?? '',
        'otr2' => $_POST['otr2'] ?? '',
        'otr3' => $_POST['otr3'] ?? '',
        'otr4' => $_POST['otr4'] ?? '',
        'otr5' => $_POST['otr5'] ?? '',
        'otr6' => $_POST['otr6'] ?? '',
        'otr7' => $_POST['otr7'] ?? '',
        'otr8' => $_POST['otr8'] ?? '',
        'fecha' => date('Y-m-d')
    ]);

    echo Util::toJson(['success' => true, 'id' => $workPlan->id2]);
} else if (isset($_POST['updateWorkPlan'])) {
    $workPlanId = $_POST['workPlanId'];
    $workPlan = WorkPlan::find($workPlanId);

    if ($workPlan && $workPlan->id == $teacher->id) {
        $workPlan->update([
            'plan' => $_POST['plan'],
            'grado' => $_POST['grado'],
            'asignatura' => $_POST['asignatura'],
            'mes' => $_POST['mes'],
            'dia1' => $_POST['dia1'],
            'dia2' => $_POST['dia2'],
            'estandares' => $_POST['estandares'] ?? '',
            'tema1' => $_POST['tema1'] ?? '',
            'tema2' => $_POST['tema2'] ?? '',
            'espectativas' => $_POST['espectativas'] ?? '',
            'np1' => $_POST['np1'] ?? '',
            'np2' => $_POST['np2'] ?? '',
            'np3' => $_POST['np3'] ?? '',
            'np4' => $_POST['np4'] ?? '',
            'np5' => $_POST['np5'] ?? '',
            'tema' => $_POST['tema'] ?? '',
            'pre1' => $_POST['pre1'] ?? '',
            'obj1' => $_POST['obj1'] ?? '',
            'obj2' => $_POST['obj2'] ?? '',
            'obj3' => $_POST['obj3'] ?? '',
            'ent1' => $_POST['ent1'] ?? '',
            'ent2' => $_POST['ent2'] ?? '',
            'ent3' => $_POST['ent3'] ?? '',
            'ent4' => $_POST['ent4'] ?? '',
            'ent5' => $_POST['ent5'] ?? '',
            'ent6' => $_POST['ent6'] ?? '',
            'ent7' => $_POST['ent7'] ?? '',
            'ent8' => $_POST['ent8'] ?? '',
            'ent9' => $_POST['ent9'] ?? '',
            'ent10' => $_POST['ent10'] ?? '',
            'ent11' => $_POST['ent11'] ?? '',
            'ent12' => $_POST['ent12'] ?? '',
            'integracion' => $_POST['integracion'] ?? '',
            'act1' => $_POST['act1'] ?? '',
            'act2' => $_POST['act2'] ?? '',
            'act3' => $_POST['act3'] ?? '',
            'act4' => $_POST['act4'] ?? '',
            'ini1' => $_POST['ini1'] ?? '',
            'ini2' => $_POST['ini2'] ?? '',
            'ini3' => $_POST['ini3'] ?? '',
            'ini4' => $_POST['ini4'] ?? '',
            'ini5' => $_POST['ini5'] ?? '',
            'ini6' => $_POST['ini6'] ?? '',
            'ini7' => $_POST['ini7'] ?? '',
            'des1' => $_POST['des1'] ?? '',
            'des2' => $_POST['des2'] ?? '',
            'des3' => $_POST['des3'] ?? '',
            'des4' => $_POST['des4'] ?? '',
            'des5' => $_POST['des5'] ?? '',
            'des6' => $_POST['des6'] ?? '',
            'des7' => $_POST['des7'] ?? '',
            'cie1' => $_POST['cie1'] ?? '',
            'cie2' => $_POST['cie2'] ?? '',
            'cie3' => $_POST['cie3'] ?? '',
            'cie4' => $_POST['cie4'] ?? '',
            'cie5' => $_POST['cie5'] ?? '',
            'eva1' => $_POST['eva1'] ?? '',
            'eva2' => $_POST['eva2'] ?? '',
            'eva3' => $_POST['eva3'] ?? '',
            'eva4' => $_POST['eva4'] ?? '',
            'tab1' => $_POST['tab1'] ?? '',
            'tab2' => $_POST['tab2'] ?? '',
            'tab3' => $_POST['tab3'] ?? '',
            'tab4' => $_POST['tab4'] ?? '',
            'tab5' => $_POST['tab5'] ?? '',
            'tab6' => $_POST['tab6'] ?? '',
            'tab7' => $_POST['tab7'] ?? '',
            'tab8' => $_POST['tab8'] ?? '',
            'sel1' => $_POST['sel1'] ?? '',
            'sel2' => $_POST['sel2'] ?? '',
            'sel3' => $_POST['sel3'] ?? '',
            'sel4' => $_POST['sel4'] ?? '',
            'sel5' => $_POST['sel5'] ?? '',
            'pro1' => $_POST['pro1'] ?? '',
            'pro2' => $_POST['pro2'] ?? '',
            'otro' => $_POST['otro'] ?? '',
            'autoeva' => $_POST['autoeva'] ?? '',
            'as1' => $_POST['as1'] ?? '',
            'as2' => $_POST['as2'] ?? '',
            'as3' => $_POST['as3'] ?? '',
            'as4' => $_POST['as4'] ?? '',
            'as5' => $_POST['as5'] ?? '',
            'as6' => $_POST['as6'] ?? '',
            'as7' => $_POST['as7'] ?? '',
            'as8' => $_POST['as8'] ?? '',
            'ot1' => $_POST['ot1'] ?? '',
            'ot2' => $_POST['ot2'] ?? '',
            'ot3' => $_POST['ot3'] ?? '',
            'ot4' => $_POST['ot4'] ?? '',
            'otr1' => $_POST['otr1'] ?? '',
            'otr2' => $_POST['otr2'] ?? '',
            'otr3' => $_POST['otr3'] ?? '',
            'otr4' => $_POST['otr4'] ?? '',
            'otr5' => $_POST['otr5'] ?? '',
            'otr6' => $_POST['otr6'] ?? '',
            'otr7' => $_POST['otr7'] ?? '',
            'otr8' => $_POST['otr8'] ?? '',
        ]);

        echo Util::toJson(['success' => true, 'id' => $workPlan->id2]);
    } else {
        echo Util::toJson(['success' => false, 'message' => 'Plan de trabajo no encontrado']);
    }
} else if (isset($_POST['deleteWorkPlan'])) {
    $workPlanId = $_POST['deleteWorkPlan'];
    $workPlan = WorkPlan::find($workPlanId);

    if ($workPlan && $workPlan->id == $teacher->id) {
        $workPlan->delete();
        echo Util::toJson(['success' => true]);
    } else {
        echo Util::toJson(['success' => false, 'message' => 'Plan de trabajo no encontrado']);
    }
}
