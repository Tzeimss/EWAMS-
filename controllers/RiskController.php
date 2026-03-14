<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/RiskAssessment.php';
require_once __DIR__ . '/../models/Alert.php';
require_once __DIR__ . '/../models/AcademicTerm.php';

requireRole(['administrator', 'faculty', 'advisor']);

$riskModel = new RiskAssessment();
$alertModel = new Alert();
$termModel = new AcademicTerm();

$currentTerm = $termModel->getCurrent();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'recalculate_risk') {
        requireRole('administrator');
        
        $studentId = (int)$_POST['student_id'];
        $termId = $currentTerm['id'] ?? (int)$_POST['term_id'];
        
        $oldRisk = $riskModel->getRiskAssessment($studentId, $termId);
        $oldLevel = $oldRisk ? $oldRisk['risk_level'] : 'low';
        
        $result = $riskModel->calculateRisk($studentId, $termId);
        
        if ($oldLevel !== $result['level']) {
            $alertModel->createForRiskChange($studentId, $oldLevel, $result['level']);
        }
        
        $success = 'Risk assessment recalculated.';
    }
    
    if ($action === 'recalculate_all') {
        requireRole('administrator');
        
        $termId = $currentTerm['id'] ?? (int)$_POST['term_id'];
        
        $db = getDB();
        $students = $db->query("SELECT id FROM users WHERE role = 'student' AND is_active = 1")->fetchAll();
        
        foreach ($students as $student) {
            $oldRisk = $riskModel->getRiskAssessment($student['id'], $termId);
            $oldLevel = $oldRisk ? $oldRisk['risk_level'] : 'low';
            
            $result = $riskModel->calculateRisk($student['id'], $termId);
            
            if ($oldLevel !== $result['level']) {
                $alertModel->createForRiskChange($student['id'], $oldLevel, $result['level']);
            }
        }
        
        $success = 'All risk assessments recalculated.';
    }
}

$atRiskStudents = $riskModel->getAllAtRisk($currentTerm['id'] ?? null) ?? [];
$highRisk = array_filter($atRiskStudents, fn($s) => $s['risk_level'] === 'high');
$moderateRisk = array_filter($atRiskStudents, fn($s) => $s['risk_level'] === 'moderate');
$lowRisk = array_filter($atRiskStudents, fn($s) => $s['risk_level'] === 'low');

$riskStats = [
    'high' => count($highRisk),
    'moderate' => count($moderateRisk),
    'low' => count($lowRisk),
    'total' => count($atRiskStudents)
];

if (isset($_GET['student_id'])) {
    $studentId = (int)$_GET['student_id'];
    $studentRisk = $riskModel->getRiskAssessment($studentId, $currentTerm['id'] ?? null);
    $riskTrend = $riskModel->getRiskTrend($studentId);
}
