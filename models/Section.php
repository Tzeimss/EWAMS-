<?php

class Section {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO sections (course_id, term_id, instructor_id, section_number, capacity)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['course_id'],
            $data['term_id'],
            $data['instructor_id'] ?? null,
            $data['section_number'],
            $data['capacity'] ?? 30
        ]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE sections SET course_id = ?, term_id = ?, instructor_id = ?, section_number = ?, capacity = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['course_id'],
            $data['term_id'],
            $data['instructor_id'] ?? null,
            $data['section_number'],
            $data['capacity'] ?? 30,
            $id
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM sections WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT s.*, c.code as course_code, c.name as course_name, c.credits, 
                   u.first_name as instructor_first, u.last_name as instructor_last,
                   t.name as term_name
            FROM sections s
            JOIN courses c ON s.course_id = c.id
            LEFT JOIN users u ON s.instructor_id = u.id
            LEFT JOIN academic_terms t ON s.term_id = t.id
            WHERE s.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getAll($termId = null) {
        $sql = "
            SELECT s.*, c.code as course_code, c.name as course_name, c.credits,
                   u.first_name as instructor_first, u.last_name as instructor_last,
                   t.name as term_name,
                   (SELECT COUNT(*) FROM enrollments WHERE section_id = s.id AND status = 'active') as enrolled_count
            FROM sections s
            JOIN courses c ON s.course_id = c.id
            LEFT JOIN users u ON s.instructor_id = u.id
            LEFT JOIN academic_terms t ON s.term_id = t.id
        ";
        
        if ($termId) {
            $sql .= " WHERE s.term_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$termId]);
        } else {
            $stmt = $this->db->query($sql);
        }
        return $stmt->fetchAll();
    }
    
    public function getByInstructor($instructorId, $termId = null) {
        $sql = "
            SELECT s.*, c.code as course_code, c.name as course_name, c.credits, t.name as term_name,
                   (SELECT COUNT(*) FROM enrollments WHERE section_id = s.id AND status = 'active') as enrolled_count
            FROM sections s
            JOIN courses c ON s.course_id = c.id
            LEFT JOIN academic_terms t ON s.term_id = t.id
            WHERE s.instructor_id = ?
        ";
        
        if ($termId) {
            $sql .= " AND s.term_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$instructorId, $termId]);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$instructorId]);
        }
        return $stmt->fetchAll();
    }
    
    public function getEnrolledStudents($sectionId) {
        $stmt = $this->db->prepare("
            SELECT u.*, e.enrollment_date, e.status as enrollment_status,
                   ra.risk_score, ra.risk_level
            FROM enrollments e
            JOIN users u ON e.student_id = u.id
            LEFT JOIN risk_assessments ra ON u.id = ra.student_id
            WHERE e.section_id = ? AND e.status = 'active'
            ORDER BY u.last_name, u.first_name
        ");
        $stmt->execute([$sectionId]);
        return $stmt->fetchAll();
    }
}
