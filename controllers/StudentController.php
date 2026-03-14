<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Enrollment.php';
require_once __DIR__ . '/../models/RiskAssessment.php';
require_once __DIR__ . '/../models/Alert.php';
require_once __DIR__ . '/../models/Intervention.php';
require_once __DIR__ . '/../models/Grade.php';
require_once __DIR__ . '/../models/AcademicTerm.php';

requireRole(['administrator', 'faculty', 'advisor']);

$userModel = new User();
$enrollmentModel = new Enrollment();
$riskModel = new RiskAssessment();
$alertModel = new Alert();
$interventionModel = new Intervention();
$gradeModel = new Grade();
$termModel = new AcademicTerm();

$currentTerm = $termModel->getCurrent();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create_student') {
        requireRole('administrator');
        
        $data = [
            'username' => sanitize($_POST['username']),
            'email' => sanitize($_POST['email']),
            'password' => $_POST['password'],
            'role' => 'student',
            'first_name' => sanitize($_POST['first_name']),
            'last_name' => sanitize($_POST['last_name']),
            'phone' => sanitize($_POST['phone'] ?? '')
        ];
        
        if ($userModel->findByUsername($data['username'])) {
            $error = 'Username already exists.';
        } elseif ($userModel->findByEmail($data['email'])) {
            $error = 'Email already exists.';
        } elseif ($userModel->create($data)) {
            $success = 'Student created successfully.';
        } else {
            $error = 'Failed to create student.';
        }
    }
    
    if ($action === 'import_students') {
        requireRole('administrator');
        
        if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
            $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
            $headers = fgetcsv($file);
            $imported = 0;
            $errors = [];
            
            while (($row = fgetcsv($file)) !== false) {
                $data = array_combine($headers, $row);
                
                if (!isset($data['username']) || !isset($data['email']) || !isset($data['first_name']) || !isset($data['last_name'])) {
                    $errors[] = 'Missing required fields in row';
                    continue;
                }
                
                if ($userModel->findByUsername($data['username'])) {
                    $errors[] = "Username {$data['username']} already exists";
                    continue;
                }
                
                $userData = [
                    'username' => sanitize($data['username']),
                    'email' => sanitize($data['email']),
                    'password' => $data['password'] ?? 'changeme123',
                    'role' => 'student',
                    'first_name' => sanitize($data['first_name']),
                    'last_name' => sanitize($data['last_name']),
                    'phone' => sanitize($data['phone'] ?? '')
                ];
                
                if ($userModel->create($userData)) {
                    $imported++;
                } else {
                    $errors[] = "Failed to import {$data['username']}";
                }
            }
            
            fclose($file);
            
            if ($imported > 0) {
                $success = "Successfully imported $imported students.";
            }
            if (!empty($errors)) {
                $error = implode(', ', $errors);
            }
        }
    }
}

$students = $userModel->getStudents();

if (isset($_GET['id'])) {
    $studentId = (int)$_GET['id'];
    $student = $userModel->findById($studentId);
    
    if ($student) {
        $studentEnrollments = $enrollmentModel->getStudentSections($studentId, $currentTerm['id'] ?? null);
        $studentRisk = $riskModel->getRiskAssessment($studentId, $currentTerm['id'] ?? null);
        $studentAlerts = $alertModel->getStudentAlerts($studentId);
        $studentInterventions = $interventionModel->getByStudent($studentId);
        $riskTrend = $riskModel->getRiskTrend($studentId);
        
        $studentGrades = [];
        foreach ($studentEnrollments as $enrollment) {
            $studentGrades[$enrollment['id']] = $gradeModel->getStudentGrade($studentId, $enrollment['id']);
        }
    }
}
