<?php

class Grade {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function getAssessmentTypes() {
        $stmt = $this->db->query("SELECT * FROM assessment_types ORDER BY weight DESC");
        return $stmt->fetchAll();
    }
    
    public function createAssessmentType($data) {
        $stmt = $this->db->prepare("
            INSERT INTO assessment_types (name, abbreviation, weight)
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([
            $data['name'],
            $data['abbreviation'],
            $data['weight']
        ]);
    }
    
    public function updateAssessmentType($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE assessment_types SET name = ?, abbreviation = ?, weight = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['name'],
            $data['abbreviation'],
            $data['weight'],
            $id
        ]);
    }
    
    public function deleteAssessmentType($id) {
        $stmt = $this->db->prepare("DELETE FROM assessment_types WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function createAssessment($data) {
        $stmt = $this->db->prepare("
            INSERT INTO assessments (section_id, assessment_type_id, name, max_score, due_date)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['section_id'],
            $data['assessment_type_id'],
            $data['name'],
            $data['max_score'],
            $data['due_date'] ?? null
        ]);
    }
    
    public function updateAssessment($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE assessments SET section_id = ?, assessment_type_id = ?, name = ?, max_score = ?, due_date = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['section_id'],
            $data['assessment_type_id'],
            $data['name'],
            $data['max_score'],
            $data['due_date'] ?? null,
            $id
        ]);
    }
    
    public function deleteAssessment($id) {
        $stmt = $this->db->prepare("DELETE FROM assessments WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getAssessments($sectionId) {
        $stmt = $this->db->prepare("
            SELECT a.*, at.name as type_name, at.abbreviation, at.weight as type_weight
            FROM assessments a
            JOIN assessment_types at ON a.assessment_type_id = at.id
            WHERE a.section_id = ?
            ORDER BY a.due_date, a.name
        ");
        $stmt->execute([$sectionId]);
        return $stmt->fetchAll();
    }
    
    public function setGrade($studentId, $assessmentId, $score, $isLate = false) {
        // Get enrollment id for this student and assessment's section
        $stmt = $this->db->prepare("
            SELECT e.id as enrollment_id 
            FROM enrollments e
            JOIN assessments a ON a.section_id = e.section_id
            WHERE e.student_id = ? AND a.id = ? AND e.status = 'active'
            LIMIT 1
        ");
        $stmt->execute([$studentId, $assessmentId]);
        $enrollment = $stmt->fetch();
        
        if (!$enrollment) {
            return false;
        }
        
        $enrollmentId = $enrollment['enrollment_id'];
        
        $stmt = $this->db->prepare("
            INSERT INTO grades (enrollment_id, assessment_id, score, is_late)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE score = ?, is_late = ?, graded_at = CURRENT_TIMESTAMP
        ");
        return $stmt->execute([$enrollmentId, $assessmentId, $score, $isLate ? 1 : 0, $score, $isLate ? 1 : 0]);
    }
    
    public function getGrades($studentId, $sectionId) {
        $stmt = $this->db->prepare("
            SELECT g.*, a.name as assessment_name, a.max_score, at.name as type_name, at.weight as type_weight
            FROM grades g
            JOIN assessments a ON g.assessment_id = a.id
            JOIN assessment_types at ON a.assessment_type_id = at.id
            JOIN enrollments e ON g.enrollment_id = e.id
            WHERE e.student_id = ? AND a.section_id = ?
            ORDER BY a.due_date, a.name
        ");
        $stmt->execute([$studentId, $sectionId]);
        return $stmt->fetchAll();
    }
    
    public function getStudentGrade($studentId, $sectionId) {
        // Get enrollment for this student and section
        $stmt = $this->db->prepare("
            SELECT id FROM enrollments 
            WHERE student_id = ? AND section_id = ? AND status = 'active' 
            LIMIT 1
        ");
        $stmt->execute([$studentId, $sectionId]);
        $enrollment = $stmt->fetch();
        
        if (!$enrollment) {
            return ['total' => 0, 'letter' => 'N/A', 'items' => []];
        }
        
        $grades = $this->getGrades($studentId, $sectionId);
        
        if (empty($grades)) {
            return ['total' => 0, 'letter' => 'N/A', 'items' => []];
        }
        
        $weightedSum = 0;
        $totalWeight = 0;
        
        foreach ($grades as $grade) {
            if ($grade['score'] !== null) {
                $percentage = ($grade['score'] / $grade['max_score']) * 100;
                $weight = $grade['type_weight'] ?? 1;
                $weightedSum += $percentage * $weight;
                $totalWeight += $weight;
            }
        }
        
        $total = $totalWeight > 0 ? round($weightedSum / $totalWeight, 2) : 0;
        
        return [
            'total' => $total,
            'letter' => $this->getLetterGrade($total),
            'items' => $grades
        ];
    }
    
    private function getLetterGrade($percentage) {
        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }
    
    public function getSectionGrades($sectionId) {
        $stmt = $this->db->prepare("
            SELECT u.id, u.first_name, u.last_name, u.email,
                   e.id as enrollment_id
            FROM enrollments e
            JOIN users u ON e.student_id = u.id
            WHERE e.section_id = ? AND e.status = 'active'
            ORDER BY u.last_name, u.first_name
        ");
        $stmt->execute([$sectionId]);
        return $stmt->fetchAll();
    }
    
    public function getGradeDistribution($sectionId) {
        $students = $this->getSectionGrades($sectionId);
        $distribution = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0, 'N/A' => 0];
        
        foreach ($students as $student) {
            $grade = $this->getStudentGrade($student['id'], $sectionId);
            $distribution[$grade['letter']]++;
        }
        
        return $distribution;
    }
}
