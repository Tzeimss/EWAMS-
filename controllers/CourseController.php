<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Section.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Enrollment.php';
require_once __DIR__ . '/../models/AcademicTerm.php';

requireRole(['administrator', 'faculty']);

$courseModel = new Course();
$sectionModel = new Section();
$userModel = new User();
$enrollmentModel = new Enrollment();
$termModel = new AcademicTerm();

$terms = $termModel->getAll();
$currentTerm = $termModel->getCurrent();
$faculty = $userModel->getFaculty();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create_course') {
        requireRole('administrator');
        
        $data = [
            'code' => sanitize($_POST['code']),
            'name' => sanitize($_POST['name']),
            'description' => sanitize($_POST['description'] ?? ''),
            'credits' => (int)$_POST['credits'],
            'program_id' => !empty($_POST['program_id']) ? (int)$_POST['program_id'] : null
        ];
        
        if ($courseModel->create($data)) {
            $success = 'Course created successfully.';
        } else {
            $error = 'Failed to create course.';
        }
    }
    
    if ($action === 'update_course') {
        requireRole('administrator');
        
        $id = (int)$_POST['course_id'];
        $data = [
            'code' => sanitize($_POST['code']),
            'name' => sanitize($_POST['name']),
            'description' => sanitize($_POST['description'] ?? ''),
            'credits' => (int)$_POST['credits'],
            'program_id' => !empty($_POST['program_id']) ? (int)$_POST['program_id'] : null
        ];
        
        if ($courseModel->update($id, $data)) {
            $success = 'Course updated successfully.';
        } else {
            $error = 'Failed to update course.';
        }
    }
    
    if ($action === 'create_section') {
        requireRole('administrator');
        
        $data = [
            'course_id' => (int)$_POST['course_id'],
            'term_id' => (int)$_POST['term_id'],
            'instructor_id' => !empty($_POST['instructor_id']) ? (int)$_POST['instructor_id'] : null,
            'section_number' => sanitize($_POST['section_number']),
            'capacity' => (int)($_POST['capacity'] ?? 30)
        ];
        
        if ($sectionModel->create($data)) {
            $success = 'Section created successfully.';
        } else {
            $error = 'Failed to create section.';
        }
    }
    
    if ($action === 'update_section') {
        requireRole('administrator');
        
        $id = (int)$_POST['section_id'];
        $data = [
            'course_id' => (int)$_POST['course_id'],
            'term_id' => (int)$_POST['term_id'],
            'instructor_id' => !empty($_POST['instructor_id']) ? (int)$_POST['instructor_id'] : null,
            'section_number' => sanitize($_POST['section_number']),
            'capacity' => (int)($_POST['capacity'] ?? 30)
        ];
        
        if ($sectionModel->update($id, $data)) {
            $success = 'Section updated successfully.';
        } else {
            $error = 'Failed to update section.';
        }
    }
    
    if ($action === 'enroll_student') {
        requireRole(['administrator', 'faculty']);
        
        $studentId = (int)$_POST['student_id'];
        $sectionId = (int)$_POST['section_id'];
        
        if ($enrollmentModel->enroll($studentId, $sectionId)) {
            $success = 'Student enrolled successfully.';
        } else {
            $error = 'Failed to enroll student or student already enrolled.';
        }
    }
    
    if ($action === 'enroll_bulk') {
        requireRole(['administrator', 'faculty']);
        
        $sectionId = (int)$_POST['section_id'];
        $studentIds = $_POST['student_ids'] ?? [];
        
        if (!empty($studentIds) && $enrollmentModel->bulkEnroll($studentIds, $sectionId)) {
            $success = 'Students enrolled successfully.';
        } else {
            $error = 'Failed to enroll students.';
        }
    }
    
    if ($action === 'unenroll_student') {
        requireRole(['administrator', 'faculty']);
        
        $studentId = (int)$_POST['student_id'];
        $sectionId = (int)$_POST['section_id'];
        
        if ($enrollmentModel->unenroll($studentId, $sectionId)) {
            $success = 'Student unenrolled successfully.';
        } else {
            $error = 'Failed to unenroll student.';
        }
    }
}

$courses = $courseModel->getAll();
$sections = $sectionModel->getAll($currentTerm['id'] ?? null);

if (isset($_GET['section_id'])) {
    $sectionId = (int)$_GET['section_id'];
    $section = $sectionModel->findById($sectionId);
    $enrolledStudents = $sectionModel->getEnrolledStudents($sectionId);
}
