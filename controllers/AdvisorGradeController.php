<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Grade.php';
require_once __DIR__ . '/../models/RiskAssessment.php';
require_once __DIR__ . '/../models/Enrollment.php';
require_once __DIR__ . '/../models/AcademicTerm.php';

requireRole('advisor');

$userModel = new User();
$gradeModel = new Grade();
$riskModel = new RiskAssessment();
$enrollmentModel = new Enrollment();
$termModel = new AcademicTerm();

$currentTerm = $termModel->getCurrent();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_grade') {
        $studentId = (int)$_POST['student_id'];
        $sectionId = (int)$_POST['section_id'];
        $assessmentId = (int)$_POST['assessment_id'];
        $score = (float)$_POST['score'];
        
        if ($gradeModel->setGrade($studentId, $assessmentId, $score)) {
            $riskModel->calculateRisk($studentId, $currentTerm['id'] ?? null);
            $success = 'Grade updated successfully.';
        } else {
            $error = 'Failed to update grade.';
        }
    }
    
    if ($action === 'update_attendance') {
        $studentId = (int)$_POST['student_id'];
        $sectionId = (int)$_POST['section_id'];
        $date = $_POST['date'];
        $status = $_POST['status'];
        
        $db = getDB();
        $stmt = $db->prepare("
            INSERT INTO attendance (enrollment_id, date, status)
            SELECT e.id, ?, ? FROM enrollments e
            WHERE e.student_id = ? AND e.section_id = ? AND e.status = 'active'
            ON DUPLICATE KEY UPDATE status = ?
        ");
        $stmt->execute([$date, $status, $studentId, $sectionId, $status]);
        
        $riskModel->calculateRisk($studentId, $currentTerm['id'] ?? null);
        $success = 'Attendance updated successfully.';
    }
    
    if ($action === 'update_final_grade') {
        $studentId = (int)$_POST['student_id'];
        $sectionId = (int)$_POST['section_id'];
        $finalGrade = $_POST['final_grade'];
        
        $stmt = getDB()->prepare("
            INSERT INTO student_final_grades (student_id, section_id, final_grade, term_id)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE final_grade = ?
        ");
        $stmt->execute([$studentId, $sectionId, $finalGrade, $currentTerm['id'] ?? null, $finalGrade]);
        
        $riskModel->calculateRisk($studentId, $currentTerm['id'] ?? null);
        $success = 'Final grade updated successfully.';
    }
    
    if ($action === 'update_class_standing') {
        $studentId = (int)$_POST['student_id'];
        $sectionId = (int)$_POST['section_id'];
        $classStanding = (float)$_POST['class_standing'];
        
        $stmt = getDB()->prepare("
            INSERT INTO student_class_standing (student_id, section_id, standing, term_id)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE standing = ?
        ");
        $stmt->execute([$studentId, $sectionId, $classStanding, $currentTerm['id'] ?? null, $classStanding]);
        
        $riskModel->calculateRisk($studentId, $currentTerm['id'] ?? null);
        $success = 'Class standing updated successfully.';
    }
}

$students = $userModel->getStudents() ?? [];

$studentData = [];
foreach ($students as $student) {
    $sections = $enrollmentModel->getStudentSections($student['id'], $currentTerm['id'] ?? null);
    $risk = $riskModel->getRiskAssessment($student['id'], $currentTerm['id'] ?? null);
    
    $studentGrades = [];
    foreach ($sections as $section) {
        $grades = $gradeModel->getGrades($student['id'], $section['id']);
        $studentGrade = $gradeModel->getStudentGrade($student['id'], $section['id']);
        
        $stmt = getDB()->prepare("SELECT final_grade FROM student_final_grades WHERE student_id = ? AND section_id = ?");
        $stmt->execute([$student['id'], $section['id']]);
        $finalGrade = $stmt->fetch();
        
        $stmt = getDB()->prepare("SELECT standing FROM student_class_standing WHERE student_id = ? AND section_id = ?");
        $stmt->execute([$student['id'], $section['id']]);
        $classStanding = $stmt->fetch();
        
        $studentGrades[] = [
            'section' => $section,
            'grades' => $grades,
            'final_grade' => $finalGrade['final_grade'] ?? $studentGrade['letter'] ?? 'N/A',
            'percentage' => $studentGrade['total'] ?? 0,
            'class_standing' => $classStanding['standing'] ?? null
        ];
    }
    
    $studentData[] = [
        'student' => $student,
        'sections' => $studentGrades,
        'risk' => $risk
    ];
}
