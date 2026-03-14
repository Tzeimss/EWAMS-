<?php

class RiskAssessment {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function calculateRisk($studentId, $termId = null) {
        if (!$termId) {
            $termId = $this->getCurrentTermId();
        }
        
        $gradeScore = $this->calculateGradeScore($studentId, $termId);
        $attendanceScore = $this->calculateAttendanceScore($studentId, $termId);
        $submissionScore = $this->calculateSubmissionScore($studentId, $termId);
        $lateScore = $this->calculateLateFrequencyScore($studentId, $termId);
        
        $weights = RISK_WEIGHTS;
        
        $riskScore = 100 - (
            ($gradeScore * $weights['grades']) +
            ($attendanceScore * $weights['attendance']) +
            ($submissionScore * $weights['submissions']) +
            ($lateScore * $weights['late_frequency'])
        );
        $riskScore = max(0, min(100, $riskScore));
        
        $riskLevel = $this->getRiskLevel($riskScore);
        
        $this->saveRiskAssessment($studentId, $termId, $riskScore, $riskLevel, $gradeScore, $attendanceScore, $submissionScore, $lateScore);
        
        return [
            'score' => $riskScore,
            'level' => $riskLevel,
            'grade_score' => $gradeScore,
            'attendance_score' => $attendanceScore,
            'submission_score' => $submissionScore,
            'late_score' => $lateScore
        ];
    }
    
    private function calculateGradeScore($studentId, $termId) {
        $stmt = $this->db->prepare("
            SELECT g.score, a.max_score, at.weight
            FROM grades g
            JOIN assessments a ON g.assessment_id = a.id
            JOIN sections s ON a.section_id = s.id
            JOIN assessment_types at ON a.assessment_type_id = at.id
            JOIN enrollments e ON g.enrollment_id = e.id
            WHERE e.student_id = ? AND s.term_id = ? AND g.score IS NOT NULL
        ");
        $stmt->execute([$studentId, $termId]);
        $grades = $stmt->fetchAll();
        
        if (empty($grades)) return 75;
        
        $totalWeight = 0;
        $weightedSum = 0;
        
        foreach ($grades as $g) {
            $percentage = ($g['score'] / $g['max_score']) * 100;
            $weight = $g['weight'] ?? 1;
            $weightedSum += $percentage * $weight;
            $totalWeight += $weight;
        }
        
        return $totalWeight > 0 ? $weightedSum / $totalWeight : 75;
    }
    
    private function calculateAttendanceScore($studentId, $termId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total, 
                   SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present
            FROM attendance a
            JOIN enrollments e ON a.enrollment_id = e.id
            JOIN sections s ON e.section_id = s.id
            WHERE e.student_id = ? AND s.term_id = ?
        ");
        $stmt->execute([$studentId, $termId]);
        $result = $stmt->fetch();
        
        if (!$result || $result['total'] == 0) return 100;
        
        return ($result['present'] / $result['total']) * 100;
    }
    
    private function calculateSubmissionScore($studentId, $termId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total,
                   SUM(CASE WHEN g.score IS NULL THEN 1 ELSE 0 END) as missing
            FROM assessments a
            JOIN sections s ON a.section_id = s.id
            LEFT JOIN grades g ON g.assessment_id = a.id 
            LEFT JOIN enrollments e ON g.enrollment_id = e.id AND e.student_id = ?
            WHERE s.term_id = ?
        ");
        $stmt->execute([$studentId, $termId]);
        $result = $stmt->fetch();
        
        if (!$result || $result['total'] == 0) return 100;
        
        $submitted = $result['total'] - $result['missing'];
        return ($submitted / $result['total']) * 100;
    }
    
    private function calculateLateFrequencyScore($studentId, $termId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total,
                   SUM(CASE WHEN g.is_late = 1 THEN 1 ELSE 0 END) as late
            FROM grades g
            JOIN assessments a ON g.assessment_id = a.id
            JOIN sections s ON a.section_id = s.id
            JOIN enrollments e ON g.enrollment_id = e.id
            WHERE e.student_id = ? AND s.term_id = ? AND g.score IS NOT NULL
        ");
        $stmt->execute([$studentId, $termId]);
        $result = $stmt->fetch();
        
        if (!$result || $result['total'] == 0) return 100;
        
        $onTime = $result['total'] - $result['late'];
        return ($onTime / $result['total']) * 100;
    }
    
    private function getRiskLevel($score) {
        if ($score < RISK_THRESHOLDS['low']) return 'low';
        if ($score < RISK_THRESHOLDS['moderate']) return 'moderate';
        return 'high';
    }
    
    private function getCurrentTermId() {
        $stmt = $this->db->query("SELECT id FROM academic_terms WHERE is_current = 1 LIMIT 1");
        $result = $stmt->fetch();
        return $result ? $result['id'] : null;
    }
    
    private function saveRiskAssessment($studentId, $termId, $riskScore, $riskLevel, $gradeScore, $attendanceScore, $submissionScore, $lateScore) {
        $stmt = $this->db->prepare("
            INSERT INTO risk_assessments (student_id, term_id, risk_score, risk_level, grade_score, attendance_score, submission_score, late_score)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                risk_score = ?, risk_level = ?, grade_score = ?, attendance_score = ?, submission_score = ?, late_score = ?, calculated_at = CURRENT_TIMESTAMP
        ");
        $stmt->execute([
            $studentId, $termId, $riskScore, $riskLevel, $gradeScore, $attendanceScore, $submissionScore, $lateScore,
            $riskScore, $riskLevel, $gradeScore, $attendanceScore, $submissionScore, $lateScore
        ]);
    }
    
    public function getRiskAssessment($studentId, $termId = null) {
        if (!$termId) {
            $termId = $this->getCurrentTermId();
        }
        
        $stmt = $this->db->prepare("
            SELECT * FROM risk_assessments WHERE student_id = ? AND term_id = ?
        ");
        $stmt->execute([$studentId, $termId]);
        return $stmt->fetch();
    }
    
    public function getAllAtRisk($termId = null, $level = null) {
        if (!$termId) {
            $termId = $this->getCurrentTermId();
        }
        
        if (!$termId) {
            return [];
        }
        
        $sql = "
            SELECT ra.*, u.first_name, u.last_name, u.email, u.student_id as student_number
            FROM risk_assessments ra
            JOIN users u ON ra.student_id = u.id
            WHERE ra.term_id = ?
        ";
        
        if ($level) {
            $sql .= " AND ra.risk_level = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$termId, $level]);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$termId]);
        }
        
        return $stmt->fetchAll();
    }
    
    public function getRiskTrend($studentId) {
        $stmt = $this->db->prepare("
            SELECT ra.*, t.name as term_name
            FROM risk_assessments ra
            JOIN academic_terms t ON ra.term_id = t.id
            WHERE ra.student_id = ?
            ORDER BY t.start_date DESC
            LIMIT 5
        ");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }
}
