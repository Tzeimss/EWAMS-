<?php

class AcademicTerm {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO academic_terms (name, start_date, end_date, is_current)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['name'],
            $data['start_date'],
            $data['end_date'],
            $data['is_current'] ?? 0
        ]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE academic_terms SET name = ?, start_date = ?, end_date = ?, is_current = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['name'],
            $data['start_date'],
            $data['end_date'],
            $data['is_current'] ?? 0,
            $id
        ]);
    }
    
    public function setCurrent($id) {
        $this->db->beginTransaction();
        try {
            $this->db->query("UPDATE academic_terms SET is_current = 0");
            $stmt = $this->db->prepare("UPDATE academic_terms SET is_current = 1 WHERE id = ?");
            $stmt->execute([$id]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM academic_terms WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM academic_terms WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM academic_terms ORDER BY start_date DESC");
        return $stmt->fetchAll();
    }
    
    public function getCurrent() {
        $stmt = $this->db->query("SELECT * FROM academic_terms WHERE is_current = 1 LIMIT 1");
        return $stmt->fetch();
    }
}
