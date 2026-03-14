<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/RiskAssessment.php';
require_once __DIR__ . '/../models/Intervention.php';
require_once __DIR__ . '/../models/Section.php';
require_once __DIR__ . '/../models/Grade.php';
require_once __DIR__ . '/../models/AcademicTerm.php';

requireRole(['administrator', 'faculty', 'advisor']);

$userModel = new User();
$riskModel = new RiskAssessment();
$interventionModel = new Intervention();
$sectionModel = new Section();
$gradeModel = new Grade();
$termModel = new AcademicTerm();

$dashboardData = [
    'total_students' => count($userModel->getStudents()),
    'total_faculty' => count($userModel->getFaculty()),
    'total_advisors' => count($userModel->getAdvisors())
];

$terms = $termModel->getAll() ?? [];
$currentTerm = $termModel->getCurrent() ?? ['id' => null];
$reportType = $_GET['type'] ?? 'at_risk';

$reportData = [];

if ($reportType === 'at_risk') {
    $reportData['title'] = 'At-Risk Students Report';
    $reportData['students'] = $riskModel->getAllAtRisk($currentTerm['id'] ?? null);
    $reportData['high_risk'] = array_filter($reportData['students'], fn($s) => $s['risk_level'] === 'high');
    $reportData['moderate_risk'] = array_filter($reportData['students'], fn($s) => $s['risk_level'] === 'moderate');
    
} elseif ($reportType === 'interventions') {
    $reportData['title'] = 'Intervention Summary Report';
    $reportData['interventions'] = $interventionModel->getAll();
    $reportData['stats'] = $interventionModel->getStats();
    
} elseif ($reportType === 'course_performance') {
    $termId = $_GET['term_id'] ?? ($currentTerm['id'] ?? null);
    $reportData['title'] = 'Course Performance Report';
    $reportData['sections'] = $sectionModel->getAll($termId);
    
    foreach ($reportData['sections'] as &$section) {
        $section['grade_dist'] = $gradeModel->getGradeDistribution($section['id']);
        $enrolled = $sectionModel->getEnrolledStudents($section['id']);
        $section['enrolled_count'] = count($enrolled);
        
        $atRisk = 0;
        foreach ($enrolled as $student) {
            if (isset($student['risk_level']) && $student['risk_level'] === 'high') {
                $atRisk++;
            }
        }
        $section['at_risk_count'] = $atRisk;
    }
    
} elseif ($reportType === 'retention') {
    $reportData['title'] = 'Retention Prediction Report';
    $allStudents = $userModel->getStudents();
    
    $reportData['high_risk_count'] = 0;
    $reportData['moderate_risk_count'] = 0;
    $reportData['low_risk_count'] = 0;
    
    foreach ($allStudents as $student) {
        $risk = $riskModel->getRiskAssessment($student['id'], $currentTerm['id'] ?? null);
        if ($risk) {
            if ($risk['risk_level'] === 'high') $reportData['high_risk_count']++;
            elseif ($risk['risk_level'] === 'moderate') $reportData['moderate_risk_count']++;
            else $reportData['low_risk_count']++;
        }
    }
    
    $reportData['total'] = count($allStudents);
    $reportData['at_risk_percentage'] = $reportData['total'] > 0 ? round(($reportData['high_risk_count'] + $reportData['moderate_risk_count']) / $reportData['total'] * 100, 1) : 0;
}

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $reportType . '_report.csv"');
    
    $output = fopen('php://output', 'w');
    
    if ($reportType === 'at_risk') {
        fputcsv($output, ['Student ID', 'Name', 'Email', 'Risk Score', 'Risk Level', 'Grade Score', 'Attendance Score']);
        foreach ($reportData['students'] as $student) {
            fputcsv($output, [
                $student['student_number'] ?? $student['id'],
                $student['first_name'] . ' ' . $student['last_name'],
                $student['email'],
                $student['risk_score'],
                $student['risk_level'],
                $student['grade_score'],
                $student['attendance_score']
            ]);
        }
    }
    
    fclose($output);
    exit;
}
