<?php

class Course {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO courses (code, name, description, credits, program_id)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['code'],
            $data['name'],
            $data['description'] ?? null,
            $data['credits'],
            $data['program_id'] ?? null
        ]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE courses SET code = ?, name = ?, description = ?, credits = ?, program_id = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['code'],
            $data['name'],
            $data['description'] ?? null,
            $data['credits'],
            $data['program_id'] ?? null,
            $id
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM courses WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT c.*, p.name as program_name FROM courses c LEFT JOIN programs p ON c.program_id = p.id ORDER BY c.code");
        return $stmt->fetchAll();
    }
    
    public function getSections($courseId) {
        $stmt = $this->db->prepare("
            SELECT s.*, u.first_name, u.last_name, t.name as term_name
            FROM sections s
            LEFT JOIN users u ON s.instructor_id = u.id
            LEFT JOIN academic_terms t ON s.term_id = t.id
            WHERE s.course_id = ?
            ORDER BY s.section_number
        ");
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }
}
