<?php
// Core Functions Helper
require_once __DIR__ . '/../config/config.php';

function db() {
    return getDB();
}

function logActivity($action, $entityType = null, $entityId = null, $details = null) {
    $userId = $_SESSION['user_id'] ?? null;
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    
    $stmt = db()->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $action, $entityType, $entityId, $details, $ip]);
}

function getUserById($id) {
    $stmt = db()->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getCurrentTerm() {
    $stmt = db()->prepare("SELECT * FROM academic_terms WHERE is_current = 1 LIMIT 1");
    $stmt->execute();
    return $stmt->fetch();
}

function calculateStudentRisk($studentId, $termId = null) {
    if (!$termId) {
        $term = getCurrentTerm();
        $termId = $term['id'] ?? null;
    }
    
    // Get enrollments for the student
    $stmt = db()->prepare("
        SELECT e.id, e.section_id 
        FROM enrollments e 
        JOIN sections s ON e.section_id = s.id 
        WHERE e.student_id = ? AND s.term_id = ? AND e.status = 'active'
    ");
    $stmt->execute([$studentId, $termId]);
    $enrollments = $stmt->fetchAll();
    
    if (empty($enrollments)) {
        return ['risk_score' => 0, 'risk_level' => 'low'];
    }
    
    // Calculate attendance score
    $attendanceScore = 100;
    $totalAttendance = 0;
    $presentCount = 0;
    
    foreach ($enrollments as $enrollment) {
        $stmt = db()->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status IN ('present', 'late') THEN 1 ELSE 0 END) as present FROM attendance WHERE enrollment_id = ?");
        $stmt->execute([$enrollment['id']]);
        $att = $stmt->fetch();
        if ($att && $att['total'] > 0) {
            $totalAttendance += $att['total'];
            $presentCount += $att['present'];
        }
    }
    
    if ($totalAttendance > 0) {
        $attendanceScore = ($presentCount / $totalAttendance) * 100;
    }
    
    // Calculate grade score
    $gradeScore = 0;
    $totalWeight = 0;
    $totalScore = 0;
    
    foreach ($enrollments as $enrollment) {
        $stmt = db()->prepare("
            SELECT g.score, a.max_score, at.weight 
            FROM grades g
            JOIN assessments a ON g.assessment_id = a.id
            JOIN assessment_types at ON a.assessment_type_id = at.id
            WHERE g.enrollment_id = ? AND g.score IS NOT NULL
        ");
        $stmt->execute([$enrollment['id']]);
        $grades = $stmt->fetchAll();
        
        foreach ($grades as $grade) {
            if ($grade['max_score'] > 0) {
                $percent = ($grade['score'] / $grade['max_score']) * 100;
                $totalScore += $percent * $grade['weight'];
                $totalWeight += $grade['weight'];
            }
        }
    }
    
    if ($totalWeight > 0) {
        $gradeScore = $totalScore / $totalWeight;
    } else {
        $gradeScore = 100; // No grades yet
    }
    
    // Calculate submission score
    $submissionScore = 100;
    $totalSubmissions = 0;
    $onTimeCount = 0;
    
    foreach ($enrollments as $enrollment) {
        $stmt = db()->prepare("
            SELECT COUNT(*) as total, SUM(CASE WHEN is_late = 0 THEN 1 ELSE 0 END) as on_time 
            FROM grades 
            WHERE enrollment_id = ?
        ");
        $stmt->execute([$enrollment['id']]);
        $sub = $stmt->fetch();
        if ($sub && $sub['total'] > 0) {
            $totalSubmissions += $sub['total'];
            $onTimeCount += $sub['on_time'];
        }
    }
    
    if ($totalSubmissions > 0) {
        $submissionScore = ($onTimeCount / $totalSubmissions) * 100;
    }
    
    // Calculate risk score
    $riskScore = 100 - (
        ($attendanceScore * 0.25) +
        ($gradeScore * 0.35) +
        ($submissionScore * 0.20)
    );
    
    // Clamp risk score
    $riskScore = max(0, min(100, $riskScore));
    
    // Determine risk level
    if ($riskScore <= 30) {
        $riskLevel = 'low';
    } elseif ($riskScore <= 60) {
        $riskLevel = 'moderate';
    } else {
        $riskLevel = 'high';
    }
    
    // Update or insert risk assessment
    $stmt = db()->prepare("
        INSERT INTO risk_assessments (student_id, term_id, risk_score, risk_level, attendance_score, grade_score, submission_score, calculated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE 
            risk_score = VALUES(risk_score),
            risk_level = VALUES(risk_level),
            attendance_score = VALUES(attendance_score),
            grade_score = VALUES(grade_score),
            submission_score = VALUES(submission_score),
            calculated_at = NOW()
    ");
    $stmt->execute([$studentId, $termId, $riskScore, $riskLevel, $attendanceScore, $gradeScore, $submissionScore]);
    
    return [
        'risk_score' => $riskScore,
        'risk_level' => $riskLevel,
        'attendance_score' => $attendanceScore,
        'grade_score' => $gradeScore,
        'submission_score' => $submissionScore
    ];
}

function createAlert($studentId, $type, $title, $message, $severity = 'info') {
    $userId = $_SESSION['user_id'] ?? 1;
    
    $stmt = db()->prepare("INSERT INTO alerts (student_id, alert_type, title, message, severity, created_by) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$studentId, $type, $title, $message, $severity, $userId]);
    
    // Create notification for advisors
    $stmt = db()->prepare("SELECT advisor_id FROM advisor_assignments WHERE student_id = ?");
    $stmt->execute([$studentId]);
    $advisors = $stmt->fetchAll();
    
    $alertId = db()->lastInsertId();
    
    foreach ($advisors as $advisor) {
        $stmt = db()->prepare("INSERT INTO notifications (user_id, alert_id, type, channel, subject, message) VALUES (?, ?, 'alert', 'dashboard', ?, ?)");
        $stmt->execute([$advisor['advisor_id'], $alertId, $title, $message]);
    }
    
    return $alertId;
}

function getRiskColor($level) {
    switch ($level) {
        case 'low': return '#38a169';
        case 'moderate': return '#d69e2e';
        case 'high': return '#e53e3e';
        default: return '#718096';
    }
}

function getGradeLetter($score) {
    if ($score >= 93) return 'A';
    if ($score >= 90) return 'A-';
    if ($score >= 87) return 'B+';
    if ($score >= 83) return 'B';
    if ($score >= 80) return 'B-';
    if ($score >= 77) return 'C+';
    if ($score >= 73) return 'C';
    if ($score >= 70) return 'C-';
    if ($score >= 67) return 'D+';
    if ($score >= 63) return 'D';
    if ($score >= 60) return 'D-';
    return 'F';
}

function formatDate($date) {
    return date('M j, Y', strtotime($date));
}

function getUserName($userId) {
    $user = getUserById($userId);
    return $user ? $user['first_name'] . ' ' . $user['last_name'] : 'Unknown';
}

function getUserFullName($user) {
    return $user['first_name'] . ' ' . $user['last_name'];
}
