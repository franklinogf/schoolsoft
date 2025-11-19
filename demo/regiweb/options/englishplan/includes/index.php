<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\Admin;
use App\Models\EnglishPlan;
use App\Models\Teacher;
use Classes\Server;
use Classes\Session;
use Classes\Util;

Session::is_logged();
Server::is_post();

$teacher = Teacher::find(Session::id());


if (isset($_POST['getEnglishPlan'])) {
    $planId = $_POST['getEnglishPlan'];
    $plan = EnglishPlan::find($planId);
    echo Util::toJson($plan);
} else if (isset($_POST['createEnglishPlan'])) {
    $school = Admin::primaryAdmin();
    $year = $school->year;
    $plan = EnglishPlan::create([
        'id_profesor' => $teacher->id,
        'year' => $year,
        'teacher' => $_POST['teacher'],
        'institution' => $_POST['institution'],
        'grade' => $_POST['grade'] ?? '',
        'dates' => $_POST['dates'] ?? '',
        'subject' => $_POST['subject'] ?? '',
        'topic' => $_POST['topic'] ?? '',

        // Standards
        'standard1' => $_POST['standard1'] ?? '',
        'standard2' => $_POST['standard2'] ?? '',
        'standard3' => $_POST['standard3'] ?? '',

        // Strategy
        'strategy1' => $_POST['strategy1'] ?? '',
        'strategy2' => $_POST['strategy2'] ?? '',
        'strategy3' => $_POST['strategy3'] ?? '',

        // Depth
        'depth1' => $_POST['depth1'] ?? '',
        'depth2' => $_POST['depth2'] ?? '',
        'depth3' => $_POST['depth3'] ?? '',
        'depth4' => $_POST['depth4'] ?? '',

        // Appraisal (1-14)
        'appraisal1' => $_POST['appraisal1'] ?? '',
        'appraisal2' => $_POST['appraisal2'] ?? '',
        'appraisal3' => $_POST['appraisal3'] ?? '',
        'appraisal4' => $_POST['appraisal4'] ?? '',
        'appraisal5' => $_POST['appraisal5'] ?? '',
        'appraisal6' => $_POST['appraisal6'] ?? '',
        'appraisal7' => $_POST['appraisal7'] ?? '',
        'appraisal8' => $_POST['appraisal8'] ?? '',
        'appraisal9' => $_POST['appraisal9'] ?? '',
        'appraisal10' => $_POST['appraisal10'] ?? '',
        'appraisal11' => $_POST['appraisal11'] ?? '',
        'appraisal12' => $_POST['appraisal12'] ?? '',
        'appraisal13' => $_POST['appraisal13'] ?? '',
        'appraisal14' => $_POST['appraisal14'] ?? '',

        // General objectives
        'general' => $_POST['general'] ?? '',

        // Specific objectives levels (1-4, each with 2 fields)
        'level1_1' => $_POST['level1_1'] ?? '',
        'level1_2' => $_POST['level1_2'] ?? '',
        'level2_1' => $_POST['level2_1'] ?? '',
        'level2_2' => $_POST['level2_2'] ?? '',
        'level3_1' => $_POST['level3_1'] ?? '',
        'level3_2' => $_POST['level3_2'] ?? '',
        'level4_1' => $_POST['level4_1'] ?? '',
        'level4_2' => $_POST['level4_2'] ?? '',

        // Activities (1-10)
        'activities1' => $_POST['activities1'] ?? '',
        'activities2' => $_POST['activities2'] ?? '',
        'activities3' => $_POST['activities3'] ?? '',
        'activities4' => $_POST['activities4'] ?? '',
        'activities5' => $_POST['activities5'] ?? '',
        'activities6' => $_POST['activities6'] ?? '',
        'activities7' => $_POST['activities7'] ?? '',
        'activities8' => $_POST['activities8'] ?? '',
        'activities9' => $_POST['activities9'] ?? '',
        'activities10' => $_POST['activities10'] ?? '',

        // Materials (1-14)
        'materials1' => $_POST['materials1'] ?? '',
        'materials2' => $_POST['materials2'] ?? '',
        'materials3' => $_POST['materials3'] ?? '',
        'materials4' => $_POST['materials4'] ?? '',
        'materials5' => $_POST['materials5'] ?? '',
        'materials6' => $_POST['materials6'] ?? '',
        'materials7' => $_POST['materials7'] ?? '',
        'materials8' => $_POST['materials8'] ?? '',
        'materials9' => $_POST['materials9'] ?? '',
        'materials10' => $_POST['materials10'] ?? '',
        'materials11' => $_POST['materials11'] ?? '',
        'materials12' => $_POST['materials12'] ?? '',
        'materials13' => $_POST['materials13'] ?? '',
        'materials14' => $_POST['materials14'] ?? '',

        // Home (1-8)
        'home1' => $_POST['home1'] ?? '',
        'home2' => $_POST['home2'] ?? '',
        'home3' => $_POST['home3'] ?? '',
        'home4' => $_POST['home4'] ?? '',
        'home5' => $_POST['home5'] ?? '',
        'home6' => $_POST['home6'] ?? '',
        'home7' => $_POST['home7'] ?? '',
        'home8' => $_POST['home8'] ?? '',

        // Development (1-10)
        'development1' => $_POST['development1'] ?? '',
        'development2' => $_POST['development2'] ?? '',
        'development3' => $_POST['development3'] ?? '',
        'development4' => $_POST['development4'] ?? '',
        'development5' => $_POST['development5'] ?? '',
        'development6' => $_POST['development6'] ?? '',
        'development7' => $_POST['development7'] ?? '',
        'development8' => $_POST['development8'] ?? '',
        'development9' => $_POST['development9'] ?? '',
        'development10' => $_POST['development10'] ?? '',

        // Closing (1-3)
        'closing1' => $_POST['closing1'] ?? '',
        'closing2' => $_POST['closing2'] ?? '',
        'closing3' => $_POST['closing3'] ?? '',

        // Assessment (1-14)
        'assessment1' => $_POST['assessment1'] ?? '',
        'assessment2' => $_POST['assessment2'] ?? '',
        'assessment3' => $_POST['assessment3'] ?? '',
        'assessment4' => $_POST['assessment4'] ?? '',
        'assessment5' => $_POST['assessment5'] ?? '',
        'assessment6' => $_POST['assessment6'] ?? '',
        'assessment7' => $_POST['assessment7'] ?? '',
        'assessment8' => $_POST['assessment8'] ?? '',
        'assessment9' => $_POST['assessment9'] ?? '',
        'assessment10' => $_POST['assessment10'] ?? '',
        'assessment11' => $_POST['assessment11'] ?? '',
        'assessment12' => $_POST['assessment12'] ?? '',
        'assessment13' => $_POST['assessment13'] ?? '',
        'assessment14' => $_POST['assessment14'] ?? '',

        // Daily activities - Tuesday
        'tuesday' => $_POST['tuesday'] ?? '',
        'tuesday1' => $_POST['tuesday1'] ?? '',
        'tuesday2' => $_POST['tuesday2'] ?? '',
        'tuesday3' => $_POST['tuesday3'] ?? '',
        'tuesday4' => $_POST['tuesday4'] ?? '',
        'tuesday5' => $_POST['tuesday5'] ?? '',

        // Wednesday
        'wednesday' => $_POST['wednesday'] ?? '',
        'wednesday1' => $_POST['wednesday1'] ?? '',
        'wednesday2' => $_POST['wednesday2'] ?? '',
        'wednesday3' => $_POST['wednesday3'] ?? '',
        'wednesday4' => $_POST['wednesday4'] ?? '',
        'wednesday5' => $_POST['wednesday5'] ?? '',

        // Thursday
        'thursday' => $_POST['thursday'] ?? '',
        'thursday1' => $_POST['thursday1'] ?? '',
        'thursday2' => $_POST['thursday2'] ?? '',
        'thursday3' => $_POST['thursday3'] ?? '',
        'thursday4' => $_POST['thursday4'] ?? '',
        'thursday5' => $_POST['thursday5'] ?? '',

        // Friday
        'friday' => $_POST['friday'] ?? '',
        'friday1' => $_POST['friday1'] ?? '',
        'friday2' => $_POST['friday2'] ?? '',
        'friday3' => $_POST['friday3'] ?? '',
        'friday4' => $_POST['friday4'] ?? '',
        'friday5' => $_POST['friday5'] ?? ''
    ]);

    echo Util::toJson(['success' => true, 'id' => $plan->id]);
} else if (isset($_POST['updateEnglishPlan'])) {
    $planId = $_POST['planId'];
    $plan = EnglishPlan::find($planId);

    if ($plan && $plan->id_profesor == $teacher->id) {
        $plan->update([
            'teacher' => $_POST['teacher'],
            'institution' => $_POST['institution'],
            'grade' => $_POST['grade'] ?? '',
            'dates' => $_POST['dates'] ?? '',
            'subject' => $_POST['subject'] ?? '',
            'topic' => $_POST['topic'] ?? '',

            // Standards
            'standard1' => $_POST['standard1'] ?? '',
            'standard2' => $_POST['standard2'] ?? '',
            'standard3' => $_POST['standard3'] ?? '',

            // Strategy
            'strategy1' => $_POST['strategy1'] ?? '',
            'strategy2' => $_POST['strategy2'] ?? '',
            'strategy3' => $_POST['strategy3'] ?? '',

            // Depth
            'depth1' => $_POST['depth1'] ?? '',
            'depth2' => $_POST['depth2'] ?? '',
            'depth3' => $_POST['depth3'] ?? '',
            'depth4' => $_POST['depth4'] ?? '',

            // Appraisal (1-14)
            'appraisal1' => $_POST['appraisal1'] ?? '',
            'appraisal2' => $_POST['appraisal2'] ?? '',
            'appraisal3' => $_POST['appraisal3'] ?? '',
            'appraisal4' => $_POST['appraisal4'] ?? '',
            'appraisal5' => $_POST['appraisal5'] ?? '',
            'appraisal6' => $_POST['appraisal6'] ?? '',
            'appraisal7' => $_POST['appraisal7'] ?? '',
            'appraisal8' => $_POST['appraisal8'] ?? '',
            'appraisal9' => $_POST['appraisal9'] ?? '',
            'appraisal10' => $_POST['appraisal10'] ?? '',
            'appraisal11' => $_POST['appraisal11'] ?? '',
            'appraisal12' => $_POST['appraisal12'] ?? '',
            'appraisal13' => $_POST['appraisal13'] ?? '',
            'appraisal14' => $_POST['appraisal14'] ?? '',

            // General objectives
            'general' => $_POST['general'] ?? '',

            // Specific objectives levels
            'level1_1' => $_POST['level1_1'] ?? '',
            'level1_2' => $_POST['level1_2'] ?? '',
            'level2_1' => $_POST['level2_1'] ?? '',
            'level2_2' => $_POST['level2_2'] ?? '',
            'level3_1' => $_POST['level3_1'] ?? '',
            'level3_2' => $_POST['level3_2'] ?? '',
            'level4_1' => $_POST['level4_1'] ?? '',
            'level4_2' => $_POST['level4_2'] ?? '',

            // Activities
            'activities1' => $_POST['activities1'] ?? '',
            'activities2' => $_POST['activities2'] ?? '',
            'activities3' => $_POST['activities3'] ?? '',
            'activities4' => $_POST['activities4'] ?? '',
            'activities5' => $_POST['activities5'] ?? '',
            'activities6' => $_POST['activities6'] ?? '',
            'activities7' => $_POST['activities7'] ?? '',
            'activities8' => $_POST['activities8'] ?? '',
            'activities9' => $_POST['activities9'] ?? '',
            'activities10' => $_POST['activities10'] ?? '',

            // Materials
            'materials1' => $_POST['materials1'] ?? '',
            'materials2' => $_POST['materials2'] ?? '',
            'materials3' => $_POST['materials3'] ?? '',
            'materials4' => $_POST['materials4'] ?? '',
            'materials5' => $_POST['materials5'] ?? '',
            'materials6' => $_POST['materials6'] ?? '',
            'materials7' => $_POST['materials7'] ?? '',
            'materials8' => $_POST['materials8'] ?? '',
            'materials9' => $_POST['materials9'] ?? '',
            'materials10' => $_POST['materials10'] ?? '',
            'materials11' => $_POST['materials11'] ?? '',
            'materials12' => $_POST['materials12'] ?? '',
            'materials13' => $_POST['materials13'] ?? '',
            'materials14' => $_POST['materials14'] ?? '',

            // Home
            'home1' => $_POST['home1'] ?? '',
            'home2' => $_POST['home2'] ?? '',
            'home3' => $_POST['home3'] ?? '',
            'home4' => $_POST['home4'] ?? '',
            'home5' => $_POST['home5'] ?? '',
            'home6' => $_POST['home6'] ?? '',
            'home7' => $_POST['home7'] ?? '',
            'home8' => $_POST['home8'] ?? '',

            // Development
            'development1' => $_POST['development1'] ?? '',
            'development2' => $_POST['development2'] ?? '',
            'development3' => $_POST['development3'] ?? '',
            'development4' => $_POST['development4'] ?? '',
            'development5' => $_POST['development5'] ?? '',
            'development6' => $_POST['development6'] ?? '',
            'development7' => $_POST['development7'] ?? '',
            'development8' => $_POST['development8'] ?? '',
            'development9' => $_POST['development9'] ?? '',
            'development10' => $_POST['development10'] ?? '',

            // Closing
            'closing1' => $_POST['closing1'] ?? '',
            'closing2' => $_POST['closing2'] ?? '',
            'closing3' => $_POST['closing3'] ?? '',

            // Assessment
            'assessment1' => $_POST['assessment1'] ?? '',
            'assessment2' => $_POST['assessment2'] ?? '',
            'assessment3' => $_POST['assessment3'] ?? '',
            'assessment4' => $_POST['assessment4'] ?? '',
            'assessment5' => $_POST['assessment5'] ?? '',
            'assessment6' => $_POST['assessment6'] ?? '',
            'assessment7' => $_POST['assessment7'] ?? '',
            'assessment8' => $_POST['assessment8'] ?? '',
            'assessment9' => $_POST['assessment9'] ?? '',
            'assessment10' => $_POST['assessment10'] ?? '',
            'assessment11' => $_POST['assessment11'] ?? '',
            'assessment12' => $_POST['assessment12'] ?? '',
            'assessment13' => $_POST['assessment13'] ?? '',
            'assessment14' => $_POST['assessment14'] ?? '',

            // Daily activities
            'tuesday' => $_POST['tuesday'] ?? '',
            'tuesday1' => $_POST['tuesday1'] ?? '',
            'tuesday2' => $_POST['tuesday2'] ?? '',
            'tuesday3' => $_POST['tuesday3'] ?? '',
            'tuesday4' => $_POST['tuesday4'] ?? '',
            'tuesday5' => $_POST['tuesday5'] ?? '',

            'wednesday' => $_POST['wednesday'] ?? '',
            'wednesday1' => $_POST['wednesday1'] ?? '',
            'wednesday2' => $_POST['wednesday2'] ?? '',
            'wednesday3' => $_POST['wednesday3'] ?? '',
            'wednesday4' => $_POST['wednesday4'] ?? '',
            'wednesday5' => $_POST['wednesday5'] ?? '',

            'thursday' => $_POST['thursday'] ?? '',
            'thursday1' => $_POST['thursday1'] ?? '',
            'thursday2' => $_POST['thursday2'] ?? '',
            'thursday3' => $_POST['thursday3'] ?? '',
            'thursday4' => $_POST['thursday4'] ?? '',
            'thursday5' => $_POST['thursday5'] ?? '',

            'friday' => $_POST['friday'] ?? '',
            'friday1' => $_POST['friday1'] ?? '',
            'friday2' => $_POST['friday2'] ?? '',
            'friday3' => $_POST['friday3'] ?? '',
            'friday4' => $_POST['friday4'] ?? '',
            'friday5' => $_POST['friday5'] ?? ''
        ]);

        echo Util::toJson(['success' => true, 'id' => $plan->id]);
    } else {
        echo Util::toJson(['success' => false, 'message' => 'Plan not found']);
    }
} else if (isset($_POST['deleteEnglishPlan'])) {
    $planId = $_POST['deleteEnglishPlan'];
    $plan = EnglishPlan::find($planId);

    if ($plan && $plan->id_profesor == $teacher->id) {
        $plan->delete();
        echo Util::toJson(['success' => true]);
    } else {
        echo Util::toJson(['success' => false, 'message' => 'Plan not found']);
    }
}
