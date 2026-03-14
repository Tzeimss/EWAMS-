<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Grade.php';
require_once __DIR__ . '/../models/Section.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/RiskAssessment.php';
require_once __DIR__ . '/../models/Alert.php';

requireRole(['administrator', 'faculty']);

$gradeModel = new Grade();
$sectionModel = new Section();
$userModel = new User();
$riskModel = new RiskAssessment();
$alertModel = new Alert();

$db = getDB();

$assessmentTypes = $db->query("SELECT * FROM assessment_types ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create_assessment') {
        $data = [
            'section_id' => (int)$_POST['section_id'],
            'assessment_type_id' => (int)$_POST['assessment_type_id'],
            'name' => sanitize($_POST['name']),
            'max_score' => (float)$_POST['max_score'],
            'due_date' => !empty($_POST['due_date']) ? $_POST['due_date'] : null
        ];
        
        if ($gradeModel->createAssessment($data)) {
            $success = 'Assessment created successfully.';
        } else {
            $error = 'Failed to create assessment.';
        }
    }
    
    if ($action === 'update_assessment') {
        $id = (int)$_POST['assessment_id'];
        $data = [
            'section_id' => (int)$_POST['section_id'],
            'assessment_type_id' => (int)$_POST['assessment_type_id'],
            'name' => sanitize($_POST['name']),
            'max_score' => (float)$_POST['max_score'],
            'due_date' => !empty($_POST['due_date']) ? $_POST['due_date'] : null
        ];
        
        if ($gradeModel->updateAssessment($id, $data)) {
            $success = 'Assessment updated successfully.';
        } else {
            $error = 'Failed to update assessment.';
        }
    }
    
    if ($action === 'delete_assessment') {
        $id = (int)$_POST['assessment_id'];
        
        if ($gradeModel->deleteAssessment($id)) {
            $success = 'Assessment deleted successfully.';
        } else {
            $error = 'Failed to delete assessment.';
        }
    }
    
    if ($action === 'set_grade') {
        $studentId = (int)$_POST['student_id'];
        $assessmentId = (int)$_POST['assessment_id'];
        $score = $_POST['score'] !== '' ? (float)$_POST['score'] : null;
        $isLate = isset($_POST['is_late']) ? 1 : 0;
        $sectionId = (int)$_POST['section_id'];
        
        // Get enrollment ID
        $stmt = $db->prepare("SELECT id FROM enrollments WHERE student_id = ? AND section_id = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$studentId, $sectionId]);
        $enrollment = $stmt->fetch();
        
        if ($enrollment && $gradeModel->setGrade($enrollment['id'], $assessmentId, $score, $isLate)) {
            $currentTerm = $db->query("SELECT id FROM academic_terms WHERE is_current = 1 LIMIT 1")->fetch();
            if ($currentTerm) {
                $riskModel->calculateRisk($studentId, $currentTerm['id']);
            }
            $success = 'Grade saved successfully.';
        } else {
            $error = 'Failed to save grade.';
        }
    }
    
    if ($action === 'bulk_set_grade') {
        $assessmentId = (int)$_POST['assessment_id'];
        $grades = $_POST['grades'] ?? [];
        
        foreach ($grades as $studentId => $score) {
            if ($score !== '') {
                $gradeModel->setGrade($studentId, $assessmentId, (float)$score, false);
            }
        }
        
        $assessment = $db->prepare("SELECT section_id FROM assessments WHERE id = ?")->fetch($assessmentId);
        if ($assessment) {
            $enrollments = $db->prepare("SELECT student_id FROM enrollments WHERE section_id = ? AND status = 'active'")->fetchAll($assessment['section_id']);
            $currentTerm = $db->query("SELECT id FROM academic_terms WHERE is_current = 1 LIMIT 1")->fetch();
            if ($currentTerm) {
                foreach ($enrollments as $enrollment) {
                    $riskModel->calculateRisk($enrollment['student_id'], $currentTerm['id']);
                }
            }
        }
        
        $success = 'Grades saved successfully.';
    }
    
    if ($action === 'create_assessment_type') {
        requireRole('administrator');
        
        $stmt = $db->prepare("INSERT INTO assessment_types (name, abbreviation, weight) VALUES (?, ?, ?)");
        if ($stmt->execute([sanitize($_POST['name']), sanitize($_POST['abbreviation']), (float)$_POST['weight']])) {
            $success = 'Assessment type created successfully.';
            $assessmentTypes = $db->query("SELECT * FROM assessment_types ORDER BY name")->fetchAll();
        } else {
            $error = 'Failed to create assessment type.';
        }
    }
}

if (isset($_GET['section_id'])) {
    $sectionId = (int)$_GET['section_id'];
    $section = $sectionModel->findById($sectionId);
    $assessments = $gradeModel->getAssessments($sectionId);
    $students = $sectionModel->getEnrolledStudents($sectionId);
    
    $studentGrades = [];
    foreach ($students as $student) {
        $studentGrades[$student['id']] = $gradeModel->getStudentGrade($student['id'], $sectionId);
    }
    
    $gradeDistribution = $gradeModel->getGradeDistribution($sectionId);
}
