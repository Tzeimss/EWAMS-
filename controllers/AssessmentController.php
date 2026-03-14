<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Section.php';
require_once __DIR__ . '/../models/AcademicTerm.php';

requireRole(['administrator', 'faculty']);

$sectionModel = new Section();

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_assessment') {
        $data = [
            'section_id' => (int)$_POST['section_id'],
            'assessment_type_id' => (int)$_POST['assessment_type_id'],
            'name' => sanitize($_POST['name']),
            'max_score' => (float)$_POST['max_score'],
            'due_date' => !empty($_POST['due_date']) ? $_POST['due_date'] : null
        ];
        
        require_once __DIR__ . '/../models/Grade.php';
        $gradeModel = new Grade();
        
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
        
        require_once __DIR__ . '/../models/Grade.php';
        $gradeModel = new Grade();
        
        if ($gradeModel->updateAssessment($id, $data)) {
            $success = 'Assessment updated successfully.';
        } else {
            $error = 'Failed to update assessment.';
        }
    }
    
    if ($action === 'delete_assessment') {
        $id = (int)$_POST['assessment_id'];
        
        require_once __DIR__ . '/../models/Grade.php';
        $gradeModel = new Grade();
        
        if ($gradeModel->deleteAssessment($id)) {
            $success = 'Assessment deleted successfully.';
        } else {
            $error = 'Failed to delete assessment.';
        }
    }
}

$termModel = new AcademicTerm();
$currentTerm = $termModel->getCurrent();
$sections = $sectionModel->getByInstructor(getCurrentUserId(), $currentTerm['id'] ?? null);

$sectionId = $_GET['section_id'] ?? ($sections[0]['id'] ?? null);
$assessments = [];
$selectedSection = null;

if ($sectionId) {
    require_once __DIR__ . '/../models/Grade.php';
    $gradeModel = new Grade();
    $assessments = $gradeModel->getAssessments($sectionId);
    $selectedSection = array_filter($sections, fn($s) => $s['id'] == $sectionId);
    $selectedSection = reset($selectedSection);
}
