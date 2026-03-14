<?php

require_once __DIR__ . '/config/config.php';

$db = getDB();

echo "Seeding enrollments and grades...\n\n";

$db->query("DELETE FROM grades");
$db->query("DELETE FROM enrollments");
$db->query("DELETE FROM risk_assessments WHERE term_id = 1");

$enrollments = [
    ['student_id' => 12, 'section_id' => 1],
    ['student_id' => 12, 'section_id' => 2],
    ['student_id' => 13, 'section_id' => 1],
    ['student_id' => 13, 'section_id' => 2],
    ['student_id' => 14, 'section_id' => 1],
    ['student_id' => 14, 'section_id' => 2],
    ['student_id' => 15, 'section_id' => 1],
    ['student_id' => 15, 'section_id' => 2],
    ['student_id' => 16, 'section_id' => 1],
    ['student_id' => 16, 'section_id' => 2],
];

$enrollmentIds = [];
foreach ($enrollments as $e) {
    $stmt = $db->prepare("INSERT INTO enrollments (student_id, section_id, enrollment_date, status) VALUES (?, ?, '2025-08-20', 'active')");
    $stmt->execute([$e['student_id'], $e['section_id']]);
    $enrollmentIds[] = $db->lastInsertId();
}

$grades = [
    ['enrollment_id' => $enrollmentIds[0], 'assessment_id' => 1, 'score' => 88],
    ['enrollment_id' => $enrollmentIds[0], 'assessment_id' => 2, 'score' => 92],
    ['enrollment_id' => $enrollmentIds[0], 'assessment_id' => 3, 'score' => 85],
    ['enrollment_id' => $enrollmentIds[0], 'assessment_id' => 4, 'score' => 90],
    ['enrollment_id' => $enrollmentIds[0], 'assessment_id' => 5, 'score' => 78],
    
    ['enrollment_id' => $enrollmentIds[1], 'assessment_id' => 1, 'score' => 45],
    ['enrollment_id' => $enrollmentIds[1], 'assessment_id' => 2, 'score' => 88],
    ['enrollment_id' => $enrollmentIds[1], 'assessment_id' => 3, 'score' => 70],
    ['enrollment_id' => $enrollmentIds[1], 'assessment_id' => 4, 'score' => 80],
    ['enrollment_id' => $enrollmentIds[1], 'assessment_id' => 5, 'score' => 75],
    
    ['enrollment_id' => $enrollmentIds[2], 'assessment_id' => 1, 'score' => 95],
    ['enrollment_id' => $enrollmentIds[2], 'assessment_id' => 2, 'score' => 98],
    ['enrollment_id' => $enrollmentIds[2], 'assessment_id' => 3, 'score' => 92],
    ['enrollment_id' => $enrollmentIds[2], 'assessment_id' => 4, 'score' => 100],
    ['enrollment_id' => $enrollmentIds[2], 'assessment_id' => 5, 'score' => 88],
    
    ['enrollment_id' => $enrollmentIds[3], 'assessment_id' => 1, 'score' => 90],
    ['enrollment_id' => $enrollmentIds[3], 'assessment_id' => 2, 'score' => 85],
    ['enrollment_id' => $enrollmentIds[3], 'assessment_id' => 3, 'score' => 78],
    ['enrollment_id' => $enrollmentIds[3], 'assessment_id' => 4, 'score' => 82],
    ['enrollment_id' => $enrollmentIds[3], 'assessment_id' => 5, 'score' => 80],
    
    ['enrollment_id' => $enrollmentIds[4], 'assessment_id' => 1, 'score' => 55],
    ['enrollment_id' => $enrollmentIds[4], 'assessment_id' => 2, 'score' => 48],
    ['enrollment_id' => $enrollmentIds[4], 'assessment_id' => 3, 'score' => 42],
    ['enrollment_id' => $enrollmentIds[4], 'assessment_id' => 4, 'score' => 60],
    ['enrollment_id' => $enrollmentIds[4], 'assessment_id' => 5, 'score' => 35],
    
    ['enrollment_id' => $enrollmentIds[5], 'assessment_id' => 1, 'score' => 30],
    ['enrollment_id' => $enrollmentIds[5], 'assessment_id' => 2, 'score' => 25],
    ['enrollment_id' => $enrollmentIds[5], 'assessment_id' => 3, 'score' => 40],
    ['enrollment_id' => $enrollmentIds[5], 'assessment_id' => 4, 'score' => 50],
    ['enrollment_id' => $enrollmentIds[5], 'assessment_id' => 5, 'score' => 45],
    
    ['enrollment_id' => $enrollmentIds[6], 'assessment_id' => 1, 'score' => 45],
    ['enrollment_id' => $enrollmentIds[6], 'assessment_id' => 2, 'score' => 38],
    ['enrollment_id' => $enrollmentIds[6], 'assessment_id' => 3, 'score' => 30],
    ['enrollment_id' => $enrollmentIds[6], 'assessment_id' => 4, 'score' => 55],
    ['enrollment_id' => $enrollmentIds[6], 'assessment_id' => 5, 'score' => 42],
    
    ['enrollment_id' => $enrollmentIds[7], 'assessment_id' => 1, 'score' => 35],
    ['enrollment_id' => $enrollmentIds[7], 'assessment_id' => 2, 'score' => 55],
    ['enrollment_id' => $enrollmentIds[7], 'assessment_id' => 3, 'score' => 45],
    ['enrollment_id' => $enrollmentIds[7], 'assessment_id' => 4, 'score' => 60],
    ['enrollment_id' => $enrollmentIds[7], 'assessment_id' => 5, 'score' => 50],
    
    ['enrollment_id' => $enrollmentIds[8], 'assessment_id' => 1, 'score' => 80],
    ['enrollment_id' => $enrollmentIds[8], 'assessment_id' => 2, 'score' => 75],
    ['enrollment_id' => $enrollmentIds[8], 'assessment_id' => 3, 'score' => 70],
    ['enrollment_id' => $enrollmentIds[8], 'assessment_id' => 4, 'score' => 85],
    ['enrollment_id' => $enrollmentIds[8], 'assessment_id' => 5, 'score' => 72],
    
    ['enrollment_id' => $enrollmentIds[9], 'assessment_id' => 1, 'score' => 78],
    ['enrollment_id' => $enrollmentIds[9], 'assessment_id' => 2, 'score' => 82],
    ['enrollment_id' => $enrollmentIds[9], 'assessment_id' => 3, 'score' => 75],
    ['enrollment_id' => $enrollmentIds[9], 'assessment_id' => 4, 'score' => 80],
    ['enrollment_id' => $enrollmentIds[9], 'assessment_id' => 5, 'score' => 70],
];

foreach ($grades as $g) {
    $stmt = $db->prepare("INSERT INTO grades (enrollment_id, assessment_id, score, is_late, graded_by) VALUES (?, ?, ?, 0, 10)");
    $stmt->execute([$g['enrollment_id'], $g['assessment_id'], $g['score']]);
}

$riskAssessments = [
    ['student_id' => 12, 'risk_score' => 12.00, 'risk_level' => 'low', 'attendance_score' => 95.00, 'grade_score' => 86.00, 'submission_score' => 100.00, 'late_score' => 100.00],
    ['student_id' => 13, 'risk_score' => 5.00, 'risk_level' => 'low', 'attendance_score' => 100.00, 'grade_score' => 94.00, 'submission_score' => 100.00, 'late_score' => 100.00],
    ['student_id' => 14, 'risk_score' => 72.00, 'risk_level' => 'high', 'attendance_score' => 70.00, 'grade_score' => 42.00, 'submission_score' => 60.00, 'late_score' => 67.00],
    ['student_id' => 15, 'risk_score' => 78.00, 'risk_level' => 'high', 'attendance_score' => 65.00, 'grade_score' => 38.00, 'submission_score' => 80.00, 'late_score' => 50.00],
    ['student_id' => 16, 'risk_score' => 35.00, 'risk_level' => 'moderate', 'attendance_score' => 80.00, 'grade_score' => 75.00, 'submission_score' => 90.00, 'late_score' => 85.00],
];

foreach ($riskAssessments as $r) {
    $stmt = $db->prepare("INSERT INTO risk_assessments (student_id, term_id, risk_score, risk_level, attendance_score, grade_score, submission_score, late_score) VALUES (?, 1, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$r['student_id'], $r['risk_score'], $r['risk_level'], $r['attendance_score'], $r['grade_score'], $r['submission_score'], $r['late_score']]);
}

echo "Seeded data:\n";
echo "- Enrollments: " . count($enrollments) . " records\n";
echo "- Grades: " . count($grades) . " grade records\n";
echo "- Risk Assessments: " . count($riskAssessments) . " students\n\n";

echo "Student Summary (login as Advisor: sjohnson / Password123):\n";
echo "----------------------------------------------------\n";
echo "1. Mike Williams - Risk: LOW (12) - Grades: 86% - Doing well\n";
echo "2. Emily Brown   - Risk: LOW (5)  - Grades: 94% - Excellent\n";
echo "3. David Jones   - Risk: HIGH (72) - Grades: 42% - Struggling\n";
echo "4. Jennifer Davis - Risk: HIGH (78) - Grades: 38% - Needs help\n";
echo "5. Chris Miller  - Risk: MODERATE (35) - Grades: 75% - Average\n";
echo "\nAdvisor can edit grades at: " . BASE_URL . "/advisor/grades\n";
