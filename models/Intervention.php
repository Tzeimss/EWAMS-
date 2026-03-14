<?php

class Intervention {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO interventions (student_id, advisor_id, type, description, follow_up_date, status)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['student_id'],
            $data['advisor_id'] ?? null,
            $data['type'],
            $data['description'],
            $data['follow_up_date'] ?? null,
            $data['status'] ?? 'planned'
        ]);
    }
    
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        foreach (['type', 'description', 'outcome', 'follow_up_date', 'status', 'advisor_id'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }
        
        $values[] = $id;
        $stmt = $this->db->prepare("UPDATE interventions SET " . implode(', ', $fields) . " WHERE id = ?");
        return $stmt->execute($values);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM interventions WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT i.*, u.first_name, u.last_name, u.email
            FROM interventions i
            JOIN users u ON i.student_id = u.id
            WHERE i.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getByStudent($studentId) {
        $stmt = $this->db->prepare("
            SELECT i.*, u.first_name as advisor_first, u.last_name as advisor_last
            FROM interventions i
            LEFT JOIN users u ON i.advisor_id = u.id
            WHERE i.student_id = ?
            ORDER BY i.created_at DESC
        ");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }
    
    public function getByAdvisor($advisorId, $status = null) {
        $sql = "
            SELECT i.*, u.first_name, u.last_name, u.email,
                   ra.risk_score, ra.risk_level
            FROM interventions i
            JOIN users u ON i.student_id = u.id
            LEFT JOIN risk_assessments ra ON u.id = ra.student_id
            WHERE i.advisor_id = ?
        ";
        
        if ($status) {
            $sql .= " AND i.status = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$advisorId, $status]);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$advisorId]);
        }
        return $stmt->fetchAll();
    }
    
    public function getAll($status = null) {
        $sql = "
            SELECT i.*, u.first_name, u.last_name, u.email,
                   ua.first_name as advisor_first, ua.last_name as advisor_last,
                   ra.risk_score, ra.risk_level
            FROM interventions i
            JOIN users u ON i.student_id = u.id
            LEFT JOIN users ua ON i.advisor_id = ua.id
            LEFT JOIN risk_assessments ra ON u.id = ra.student_id
            WHERE 1=1
        ";
        
        if ($status) {
            $sql .= " AND i.status = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$status]);
        } else {
            $stmt = $this->db->query($sql);
        }
        return $stmt->fetchAll();
    }
    
    public function getStats($advisorId = null) {
        $sql = "SELECT status, COUNT(*) as count FROM interventions";
        if ($advisorId) {
            $sql .= " WHERE advisor_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$advisorId]);
        } else {
            $stmt = $this->db->query($sql);
        }
        
        $results = $stmt->fetchAll();
        $stats = ['planned' => 0, 'in_progress' => 0, 'completed' => 0, 'cancelled' => 0];
        
        foreach ($results as $row) {
            $stats[$row['status']] = $row['count'];
        }
        
        return $stats;
    }
}
