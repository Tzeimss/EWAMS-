<?php

class Alert {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO alerts (student_id, type, title, message, severity)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['student_id'],
            $data['type'],
            $data['title'],
            $data['message'],
            $data['severity'] ?? 'info'
        ]);
    }
    
    public function createForRiskChange($studentId, $oldLevel, $newLevel) {
        $severity = 'info';
        if ($newLevel == 'high' || ($oldLevel == 'low' && $newLevel == 'high')) {
            $severity = 'critical';
        } elseif ($newLevel == 'moderate') {
            $severity = 'warning';
        }
        
        return $this->create([
            'student_id' => $studentId,
            'type' => 'risk_increase',
            'title' => 'Risk Level Changed',
            'message' => "Student risk level changed from $oldLevel to $newLevel",
            'severity' => $severity
        ]);
    }
    
    public function getStudentAlerts($studentId, $limit = 10) {
        $limit = (int) $limit;
        $stmt = $this->db->prepare("
            SELECT * FROM alerts WHERE student_id = ? ORDER BY created_at DESC LIMIT $limit
        ");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }
    
    public function getUnreadCount($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM alerts WHERE student_id = ? AND is_read = 0");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    public function markAsRead($id) {
        $stmt = $this->db->prepare("UPDATE alerts SET is_read = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function markAllAsRead($studentId) {
        $stmt = $this->db->prepare("UPDATE alerts SET is_read = 1 WHERE student_id = ?");
        return $stmt->execute([$studentId]);
    }
}
