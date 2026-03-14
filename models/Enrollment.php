<?php

class Enrollment {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function enroll($studentId, $sectionId) {
        $stmt = $this->db->prepare("
            INSERT INTO enrollments (student_id, section_id, enrollment_date, status)
            VALUES (?, ?, CURDATE(), 'active')
        ");
        try {
            return $stmt->execute([$studentId, $sectionId]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function unenroll($studentId, $sectionId) {
        $stmt = $this->db->prepare("UPDATE enrollments SET status = 'withdrawn' WHERE student_id = ? AND section_id = ?");
        return $stmt->execute([$studentId, $sectionId]);
    }
    
    public function bulkEnroll($studentIds, $sectionId) {
        $this->db->beginTransaction();
        try {
            foreach ($studentIds as $studentId) {
                $this->enroll($studentId, $sectionId);
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    public function getStudentSections($studentId, $termId = null) {
        $sql = "
            SELECT s.*, c.code as course_code, c.name as course_name, c.credits,
                   u.first_name as instructor_first, u.last_name as instructor_last,
                   t.name as term_name
            FROM enrollments e
            JOIN sections s ON e.section_id = s.id
            JOIN courses c ON s.course_id = c.id
            LEFT JOIN users u ON s.instructor_id = u.id
            LEFT JOIN academic_terms t ON s.term_id = t.id
            WHERE e.student_id = ? AND e.status = 'active'
        ";
        
        if ($termId) {
            $sql .= " AND s.term_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$studentId, $termId]);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$studentId]);
        }
        return $stmt->fetchAll();
    }
    
    public function isEnrolled($studentId, $sectionId) {
        $stmt = $this->db->prepare("
            SELECT id FROM enrollments 
            WHERE student_id = ? AND section_id = ? AND status = 'active'
        ");
        $stmt->execute([$studentId, $sectionId]);
        return $stmt->fetch() !== false;
    }
}
