<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Intervention.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/RiskAssessment.php';

$isAdvisor = getUserRole() === 'advisor';

if (!$isAdvisor) {
    header('Location: ' . BASE_URL . '/dashboard');
    exit;
}

requireRole('advisor');

$interventionModel = new Intervention();
$userModel = new User();
$riskModel = new RiskAssessment();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create_intervention') {
        $data = [
            'student_id' => (int)$_POST['student_id'],
            'advisor_id' => getCurrentUserId(),
            'type' => sanitize($_POST['type']),
            'description' => sanitize($_POST['description']),
            'follow_up_date' => !empty($_POST['follow_up_date']) ? $_POST['follow_up_date'] : null,
            'status' => 'planned'
        ];
        
        if ($interventionModel->create($data)) {
            $success = 'Intervention created successfully.';
        } else {
            $error = 'Failed to create intervention.';
        }
    }
    
    if ($action === 'update_intervention') {
        $id = (int)$_POST['intervention_id'];
        $data = [
            'type' => sanitize($_POST['type']),
            'description' => sanitize($_POST['description']),
            'outcome' => sanitize($_POST['outcome'] ?? ''),
            'follow_up_date' => !empty($_POST['follow_up_date']) ? $_POST['follow_up_date'] : null,
            'status' => sanitize($_POST['status'])
        ];
        
        if ($interventionModel->update($id, $data)) {
            $success = 'Intervention updated successfully.';
        } else {
            $error = 'Failed to update intervention.';
        }
    }
    
    if ($action === 'delete_intervention') {
        $id = (int)$_POST['intervention_id'];
        
        if ($interventionModel->delete($id)) {
            $success = 'Intervention deleted successfully.';
        } else {
            $error = 'Failed to delete intervention.';
        }
    }
}

$interventions = $interventionModel->getAll() ?? [];
$advisors = $userModel->getAdvisors() ?? [];
$students = $userModel->getStudents() ?? [];
$interventionStats = $interventionModel->getStats() ?? ['planned' => 0, 'in_progress' => 0, 'completed' => 0, 'cancelled' => 0];

$chartData = [
    'planned' => $interventionStats['planned'],
    'in_progress' => $interventionStats['in_progress'],
    'completed' => $interventionStats['completed'],
    'cancelled' => $interventionStats['cancelled']
];

if (isset($_GET['id'])) {
    $intervention = $interventionModel->findById((int)$_GET['id']);
}
