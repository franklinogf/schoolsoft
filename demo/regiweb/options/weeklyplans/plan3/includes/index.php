<?php
require_once '../../../../../app.php';

use App\Models\Admin;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\WeeklyPlan3;
use Classes\Server;
use Classes\Session;
use Classes\Util;

Session::is_logged();
Server::is_post();

$teacher = Teacher::find(Session::id());
$school = Admin::primaryAdmin();
$year = $school->year;

// Create weekly plan
if (isset($_POST['createWeeklyPlan'])) {
    try {
        $weeklyPlan = WeeklyPlan3::create([
            'id_profesor' => $teacher->id,
            'curso' => $_POST['curso'] ?? '',
            'week' => $_POST['week'] ?? '',
            'year' => $year,
            'dia1_1' => $_POST['dia1_1'] ?? '',
            'dia1_2' => $_POST['dia1_2'] ?? '',
            'dia2_1' => $_POST['dia2_1'] ?? '',
            'dia2_2' => $_POST['dia2_2'] ?? '',
            'dia3_1' => $_POST['dia3_1'] ?? '',
            'dia3_2' => $_POST['dia3_2'] ?? '',
            'dia4_1' => $_POST['dia4_1'] ?? '',
            'dia4_2' => $_POST['dia4_2'] ?? '',
            'dia5_1' => $_POST['dia5_1'] ?? '',
            'dia5_2' => $_POST['dia5_2'] ?? '',
            'nota' => $_POST['nota'] ?? '',
        ]);

        echo Util::toJson([
            'success' => true,
            'id' => $weeklyPlan->id,
            'week' => $weeklyPlan->week
        ]);
    } catch (Exception $e) {
        echo Util::toJson([
            'success' => false,
            'message' => 'Error al crear el plan: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Update weekly plan
if (isset($_POST['updateWeeklyPlan'])) {
    try {
        $planId = $_POST['id'];
        $weeklyPlan = WeeklyPlan3::find($planId);

        if ($weeklyPlan && $weeklyPlan->id_profesor == $teacher->id) {
            $weeklyPlan->update([
                'dia1_1' => $_POST['dia1_1'] ?? '',
                'dia1_2' => $_POST['dia1_2'] ?? '',
                'dia2_1' => $_POST['dia2_1'] ?? '',
                'dia2_2' => $_POST['dia2_2'] ?? '',
                'dia3_1' => $_POST['dia3_1'] ?? '',
                'dia3_2' => $_POST['dia3_2'] ?? '',
                'dia4_1' => $_POST['dia4_1'] ?? '',
                'dia4_2' => $_POST['dia4_2'] ?? '',
                'dia5_1' => $_POST['dia5_1'] ?? '',
                'dia5_2' => $_POST['dia5_2'] ?? '',
                'nota' => $_POST['nota'] ?? '',
            ]);

            echo Util::toJson([
                'success' => true,
                'id' => $weeklyPlan->id,
                'week' => $weeklyPlan->week
            ]);
        } else {
            echo Util::toJson([
                'success' => false,
                'message' => 'Plan semanal no encontrado'
            ]);
        }
    } catch (Exception $e) {
        echo Util::toJson([
            'success' => false,
            'message' => 'Error al actualizar: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Delete weekly plan
if (isset($_POST['deleteWeeklyPlan'])) {
    try {
        $planId = $_POST['deleteWeeklyPlan'];
        $weeklyPlan = WeeklyPlan3::find($planId);

        if ($weeklyPlan && $weeklyPlan->id_profesor == $teacher->id) {
            $weeklyPlan->delete();
            echo Util::toJson(['success' => true]);
        } else {
            echo Util::toJson([
                'success' => false,
                'message' => 'Plan semanal no encontrado'
            ]);
        }
    } catch (Exception $e) {
        echo Util::toJson([
            'success' => false,
            'message' => 'Error al eliminar: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Update approval (for admin use)
if (isset($_POST['updateApproval'])) {
    try {
        $planId = $_POST['planId'];
        $weeklyPlan = WeeklyPlan3::find($planId);

        if ($weeklyPlan) {
            $weeklyPlan->updateApproval(
                $_POST['comentario'] ?? '',
                $_POST['aprobacion'] ?? ''
            );

            echo Util::toJson(['success' => true]);
        } else {
            echo Util::toJson([
                'success' => false,
                'message' => 'Plan semanal no encontrado'
            ]);
        }
    } catch (Exception $e) {
        echo Util::toJson([
            'success' => false,
            'message' => 'Error al actualizar aprobación: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Get student needs
if (isset($_POST['getStudentNeeds'])) {
    try {
        $planId = $_POST['getStudentNeeds'];
        $curso = $_POST['curso'] ?? '';
        $weeklyPlan = WeeklyPlan3::find($planId);

        if (!$weeklyPlan || $weeklyPlan->id_profesor != $teacher->id) {
            echo Util::toJson([
                'success' => false,
                'message' => 'Plan semanal no encontrado'
            ]);
            exit;
        }

        // Get students for this course
        $students = Student::query()
            ->whereHas('classes', function ($query) use ($curso) {
                $query->where('curso', $curso);
            })
            ->orderBy('apellidos', 'asc')
            ->orderBy('nombre', 'asc')
            ->get();

        $studentsArray = [];
        foreach ($students as $student) {
            $studentsArray[] = [
                'id' => $student->ss,
                'name' => htmlspecialchars($student->apellidos . ', ' . $student->nombre),
                'necesidad' => htmlspecialchars($student->needs->necesidad ?? '')
            ];
        }

        echo Util::toJson([
            'success' => true,
            'students' => $studentsArray
        ]);
    } catch (Exception $e) {
        echo Util::toJson([
            'success' => false,
            'message' => 'Error al cargar estudiantes: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Save student needs
if (isset($_POST['saveStudentNeeds'])) {
    try {
        $planId = $_POST['planId'];
        $curso = $_POST['curso'] ?? '';
        $weeklyPlan = WeeklyPlan3::find($planId);

        if (!$weeklyPlan || $weeklyPlan->id_profesor != $teacher->id) {
            echo Util::toJson([
                'success' => false,
                'message' => 'Plan semanal no encontrado'
            ]);
            exit;
        }

        // Get all students for this course
        $students = Student::whereHas('classes', function ($query) use ($curso, $year) {
            $query->where('curso', $curso)
                ->where('year', $year);
        })->get();

        // Update each student's needs
        foreach ($students as $student) {
            $needKey = 'need_' . $student->ss;
            if (isset($_POST[$needKey])) {
                $student->needs()->update([
                    'necesidad' => $_POST[$needKey]
                ]);
            }
        }

        echo Util::toJson(['success' => true]);
    } catch (Exception $e) {
        echo Util::toJson([
            'success' => false,
            'message' => 'Error al guardar necesidades: ' . $e->getMessage()
        ]);
    }
    exit;
}
