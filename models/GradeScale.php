<?php

class GradeScale {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM grade_scales ORDER BY min_percentage DESC");
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM grade_scales WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO grade_scales (letter_grade, min_percentage, max_percentage, grade_points, description)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['letter_grade'],
            $data['min_percentage'],
            $data['max_percentage'],
            $data['grade_points'],
            $data['description'] ?? null
        ]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE grade_scales SET letter_grade = ?, min_percentage = ?, max_percentage = ?, grade_points = ?, description = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['letter_grade'],
            $data['min_percentage'],
            $data['max_percentage'],
            $data['grade_points'],
            $data['description'] ?? null,
            $id
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM grade_scales WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getLetterGrade($percentage) {
        $stmt = $this->db->query("SELECT * FROM grade_scales ORDER BY min_percentage DESC");
        $scales = $stmt->fetchAll();
        
        foreach ($scales as $scale) {
            if ($percentage >= $scale['min_percentage']) {
                return $scale;
            }
        }
        
        return ['letter_grade' => 'F', 'grade_points' => 0.0];
    }
    
    public function calculateGPA($grades, $credits) {
        $totalPoints = 0;
        $totalCredits = 0;
        
        foreach ($grades as $index => $grade) {
            $scale = $this->getLetterGrade($grade);
            $points = $scale['grade_points'] * $credits[$index];
            $totalPoints += $points;
            $totalCredits += $credits[$index];
        }
        
        return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0;
    }
}
