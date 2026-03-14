<?php
$pageTitle = 'Grade Management';
$currentPage = 'grades';
?>

<div class="page-content">
    <div class="page-header">
        <h1>Grade Management</h1>
    </div>

    <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if (!isset($sectionId)): ?>
    <div class="card">
        <div class="card-header">
            <h2>Select Section</h2>
        </div>
        <div class="card-body">
            <p>Please select a section to manage grades:</p>
            <form method="GET" action="">
                <input type="hidden" name="page" value="grades">
                <div class="form-group">
                    <select name="section_id" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Select Section --</option>
                        <?php 
                        $allSections = $sectionModel->getAll();
                        foreach ($allSections as $s): 
                        ?>
                        <option value="<?php echo $s['id']; ?>">
                            <?php echo $s['course_code']; ?> - <?php echo $s['course_name']; ?> (Section <?php echo $s['section_number']; ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>
    <?php return; endif; ?>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count($students); ?></h3>
                <p>Enrolled Students</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count($assessments); ?></h3>
                <p>Assessments</p>
            </div>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <div class="card-header">
                <h2>Assessments</h2>
                <button class="btn btn-primary btn-sm" onclick="openModal('createAssessmentModal')">
                    <i class="fas fa-plus"></i> Add
                </button>
            </div>
            <div class="card-body">
                <?php if (!empty($assessments)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Max Score</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assessments as $assessment): ?>
                        <tr>
                            <td><?php echo $assessment['name']; ?></td>
                            <td><?php echo $assessment['abbreviation']; ?></td>
                            <td><?php echo $assessment['max_score']; ?></td>
                            <td><?php echo $assessment['due_date'] ? date('M d, Y', strtotime($assessment['due_date'])) : 'N/A'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-clipboard"></i>
                    <p>No assessments created</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Grade Distribution</h2>
            </div>
            <div class="card-body">
                <div class="grade-distribution">
                    <?php foreach ($gradeDistribution as $letter => $count): ?>
                    <?php if ($count > 0 || $letter !== 'N/A'): ?>
                    <div class="grade-bar">
                        <div class="grade-bar-count"><?php echo $count; ?></div>
                        <div class="grade-bar-fill" style="height: <?php echo max(10, $count * 15); ?>px; background: <?php 
                            echo $letter === 'A' ? '#38a169' : ($letter === 'B' ? '#4299e1' : ($letter === 'C' ? '#d69e2e' : ($letter === 'D' ? '#ed8936' : '#e53e3e'))); 
                        ?>;"></div>
                        <div class="grade-bar-label"><?php echo $letter; ?></div>
                    </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Student Grades</h2>
        </div>
        <div class="card-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Email</th>
                        <th>Current Grade</th>
                        <th>Risk Level</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): 
                        $grade = $gradeModel->getStudentGrade($student['id'], $sectionId);
                    ?>
                    <tr>
                        <td><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></td>
                        <td><?php echo $student['email']; ?></td>
                        <td>
                            <strong><?php echo $grade['letter']; ?></strong>
                            <small><?php echo $grade['total']; ?>%</small>
                        </td>
                        <td>
                            <?php if (isset($student['risk_level'])): ?>
                            <span class="risk-badge <?php echo $student['risk_level']; ?>">
                                <?php echo ucfirst($student['risk_level']); ?>
                            </span>
                            <?php else: ?>
                            <span class="risk-badge low">Low</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline" onclick="viewStudentGrades(<?php echo $student['id']; ?>)">
                                <i class="fas fa-eye"></i> Details
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Assessment Modal -->
<div class="modal-overlay" id="createAssessmentModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Add Assessment</h3>
            <button class="modal-close" onclick="closeModal('createAssessmentModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="create_assessment">
                <input type="hidden" name="section_id" value="<?php echo $sectionId; ?>">
                <div class="form-group">
                    <label>Assessment Type</label>
                    <select name="assessment_type_id" class="form-control" required>
                        <?php foreach ($assessmentTypes as $type): ?>
                        <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?> (<?php echo $type['abbreviation']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g., Quiz 1">
                </div>
                <div class="form-group">
                    <label>Max Score</label>
                    <input type="number" name="max_score" class="form-control" required value="100" min="1">
                </div>
                <div class="form-group">
                    <label>Due Date</label>
                    <input type="datetime-local" name="due_date" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-cancel" onclick="closeModal('createAssessmentModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }
function viewStudentGrades(studentId) {
    alert('Grade details for student ' + studentId);
}
</script>
