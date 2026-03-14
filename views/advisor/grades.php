<?php
$pageTitle = 'Student Grades';
$currentPage = 'advisor-grades';
?>

<div class="page-content">
    <div class="page-header">
        <h1>Student Grade Management</h1>
    </div>

    <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="search-bar">
        <input type="text" class="search-input" placeholder="Search students..." id="studentSearch">
        <select class="filter-select" id="riskFilter">
            <option value="">All Risk Levels</option>
            <option value="high">High Risk</option>
            <option value="moderate">Moderate Risk</option>
            <option value="low">Low Risk</option>
        </select>
    </div>

    <?php foreach ($studentData as $sd): ?>
    <div class="card student-card" data-risk="<?php echo $sd['risk']['risk_level'] ?? ''; ?>">
        <div class="card-header student-header">
            <div class="student-info">
                <h3><?php echo $sd['student']['first_name'] . ' ' . $sd['student']['last_name']; ?></h3>
                <span class="student-email"><?php echo $sd['student']['email']; ?></span>
            </div>
            <div class="student-risk">
                <?php if ($sd['risk']): ?>
                <span class="risk-badge <?php echo $sd['risk']['risk_level']; ?>">
                    <?php echo strtoupper($sd['risk']['risk_level']); ?> RISK
                    (Score: <?php echo $sd['risk']['risk_score']; ?>)
                </span>
                <?php else: ?>
                <span class="risk-badge low">NO DATA</span>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (empty($sd['sections'])): ?>
        <div class="card-body">
            <p class="text-muted">No enrolled courses for this term.</p>
        </div>
        <?php else: ?>
        
        <?php foreach ($sd['sections'] as $section): ?>
        <div class="card-body section-card">
            <div class="section-header">
                <h4><?php echo $section['section']['course_code']; ?> - <?php echo $section['section']['course_name']; ?></h4>
                <span class="section-grade">
                    Final: <strong><?php echo $section['final_grade']; ?></strong>
                    (<?php echo number_format($section['percentage'], 1); ?>%)
                </span>
            </div>
            
            <form method="POST" class="grade-form">
                <input type="hidden" name="action" value="update_final_grade">
                <input type="hidden" name="student_id" value="<?php echo $sd['student']['id']; ?>">
                <input type="hidden" name="section_id" value="<?php echo $section['section']['id']; ?>">
                
                <div class="grade-row">
                    <div class="grade-field">
                        <label>Final Grade</label>
                        <select name="final_grade" class="form-control">
                            <option value="A" <?php echo $section['final_grade'] === 'A' ? 'selected' : ''; ?>>A</option>
                            <option value="A-" <?php echo $section['final_grade'] === 'A-' ? 'selected' : ''; ?>>A-</option>
                            <option value="B+" <?php echo $section['final_grade'] === 'B+' ? 'selected' : ''; ?>>B+</option>
                            <option value="B" <?php echo $section['final_grade'] === 'B' ? 'selected' : ''; ?>>B</option>
                            <option value="B-" <?php echo $section['final_grade'] === 'B-' ? 'selected' : ''; ?>>B-</option>
                            <option value="C+" <?php echo $section['final_grade'] === 'C+' ? 'selected' : ''; ?>>C+</option>
                            <option value="C" <?php echo $section['final_grade'] === 'C' ? 'selected' : ''; ?>>C</option>
                            <option value="C-" <?php echo $section['final_grade'] === 'C-' ? 'selected' : ''; ?>>C-</option>
                            <option value="D" <?php echo $section['final_grade'] === 'D' ? 'selected' : ''; ?>>D</option>
                            <option value="F" <?php echo $section['final_grade'] === 'F' ? 'selected' : ''; ?>>F</option>
                        </select>
                    </div>
                    
                    <div class="grade-field">
                        <label>Class Standing (%)</label>
                        <input type="number" name="class_standing" class="form-control" 
                               value="<?php echo $section['class_standing'] ?? ''; ?>" 
                               min="0" max="100" step="0.01" placeholder="0-100">
                    </div>
                    
                    <div class="grade-field grade-actions">
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    </div>
                </div>
            </form>
            
            <?php if (!empty($section['grades'])): ?>
            <table class="data-table grades-table">
                <thead>
                    <tr>
                        <th>Assessment</th>
                        <th>Type</th>
                        <th>Score</th>
                        <th>Max Score</th>
                        <th>Percentage</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($section['grades'] as $grade): ?>
                    <?php $percentage = $grade['max_score'] > 0 ? ($grade['score'] / $grade['max_score']) * 100 : 0; ?>
                    <tr>
                        <td><?php echo $grade['assessment_name']; ?></td>
                        <td><?php echo $grade['type_name']; ?></td>
                        <td><?php echo $grade['score'] !== null ? $grade['score'] : '-'; ?></td>
                        <td><?php echo $grade['max_score']; ?></td>
                        <td><?php echo number_format($percentage, 1); ?>%</td>
                        <td>
                            <form method="POST" class="inline-form">
                                <input type="hidden" name="action" value="update_grade">
                                <input type="hidden" name="student_id" value="<?php echo $sd['student']['id']; ?>">
                                <input type="hidden" name="section_id" value="<?php echo $section['section']['id']; ?>">
                                <input type="hidden" name="assessment_id" value="<?php echo $grade['assessment_id']; ?>">
                                <input type="number" name="score" class="form-control form-control-sm" 
                                       value="<?php echo $grade['score'] ?? ''; ?>" 
                                       min="0" max="<?php echo $grade['max_score']; ?>" step="0.01" style="width: 80px;">
                                <button type="submit" class="btn btn-sm btn-outline">Save</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="text-muted">No grades recorded yet.</p>
            <?php endif; ?>
            
            <form method="POST" class="attendance-form">
                <input type="hidden" name="action" value="update_attendance">
                <input type="hidden" name="student_id" value="<?php echo $sd['student']['id']; ?>">
                <input type="hidden" name="section_id" value="<?php echo $section['section']['id']; ?>">
                
                <div class="attendance-row">
                    <label>Quick Attendance Update:</label>
                    <input type="date" name="date" class="form-control" required>
                    <select name="status" class="form-control" required>
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="late">Late</option>
                        <option value="excused">Excused</option>
                    </select>
                    <button type="submit" class="btn btn-secondary btn-sm">Update</button>
                </div>
            </form>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>

<script>
document.getElementById('studentSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const cards = document.querySelectorAll('.student-card');
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

document.getElementById('riskFilter').addEventListener('change', function(e) {
    const risk = e.target.value;
    const cards = document.querySelectorAll('.student-card');
    cards.forEach(card => {
        if (!risk || card.dataset.risk === risk) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>
