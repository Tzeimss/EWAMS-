<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/RiskAssessment.php';
require_once __DIR__ . '/../models/Alert.php';
require_once __DIR__ . '/../models/Intervention.php';
require_once __DIR__ . '/../models/Section.php';
require_once __DIR__ . '/../models/Grade.php';
require_once __DIR__ . '/../models/Enrollment.php';
require_once __DIR__ . '/../models/AcademicTerm.php';

requireLogin();

$userModel = new User();
$riskModel = new RiskAssessment();
$alertModel = new Alert();
$interventionModel = new Intervention();
$sectionModel = new Section();
$gradeModel = new Grade();
$enrollmentModel = new Enrollment();
$termModel = new AcademicTerm();

$currentTerm = $termModel->getCurrent();
$currentUser = $userModel->findById(getCurrentUserId());

$dashboardData = [];

if (getUserRole() === 'administrator') {
    $dashboardData['total_students'] = count($userModel->getStudents());
    $dashboardData['total_faculty'] = count($userModel->getFaculty());
    $dashboardData['total_advisors'] = count($userModel->getAdvisors());
    $dashboardData['at_risk_students'] = $riskModel->getAllAtRisk($currentTerm['id'] ?? null, 'high');
    $dashboardData['moderate_risk'] = $riskModel->getAllAtRisk($currentTerm['id'] ?? null, 'moderate');
    $dashboardData['recent_alerts'] = $alertModel->getStudentAlerts(getCurrentUserId(), 5);
    $dashboardData['terms'] = $termModel->getAll();
    
    include __DIR__ . '/../views/dashboard/admin.php';
    
} elseif (getUserRole() === 'faculty') {
    $dashboardData['sections'] = $sectionModel->getByInstructor(getCurrentUserId(), $currentTerm['id'] ?? null);
    $dashboardData['at_risk_count'] = 0;
    $dashboardData['recent_alerts'] = $alertModel->getStudentAlerts(getCurrentUserId(), 5);
    
    foreach ($dashboardData['sections'] as $section) {
        $students = $sectionModel->getEnrolledStudents($section['id']);
        foreach ($students as $student) {
            if (isset($student['risk_level']) && $student['risk_level'] === 'high') {
                $dashboardData['at_risk_count']++;
            }
        }
    }
    
    include __DIR__ . '/../views/dashboard/faculty.php';
    
} elseif (getUserRole() === 'advisor') {
    $dashboardData['interventions'] = $interventionModel->getByAdvisor(getCurrentUserId());
    $dashboardData['intervention_stats'] = $interventionModel->getStats(getCurrentUserId());
    $dashboardData['at_risk_students'] = $riskModel->getAllAtRisk($currentTerm['id'] ?? null);
    $dashboardData['high_risk'] = array_filter($dashboardData['at_risk_students'], fn($s) => $s['risk_level'] === 'high');
    $dashboardData['moderate_risk'] = array_filter($dashboardData['at_risk_students'], fn($s) => $s['risk_level'] === 'moderate');
    
    include __DIR__ . '/../views/dashboard/advisor.php';
    
} elseif (getUserRole() === 'student') {
    $dashboardData['enrollments'] = $enrollmentModel->getStudentSections(getCurrentUserId(), $currentTerm['id'] ?? null);
    $dashboardData['risk'] = $riskModel->getRiskAssessment(getCurrentUserId(), $currentTerm['id'] ?? null);
    $dashboardData['alerts'] = $alertModel->getStudentAlerts(getCurrentUserId(), 5);
    
    $dashboardData['grades'] = [];
    foreach ($dashboardData['enrollments'] as $section) {
        $grade = $gradeModel->getStudentGrade(getCurrentUserId(), $section['id']);
        $dashboardData['grades'][$section['id']] = $grade;
    }
    
    include __DIR__ . '/../views/dashboard/student.php';
}
