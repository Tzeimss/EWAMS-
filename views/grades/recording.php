<?php
$pageTitle = 'Grade Recording';
$currentPage = 'grades';
?>

<div class="page-content">
    <div class="page-header">
        <h1>Grade Recording</h1>
        <div class="page-actions">
            <button class="btn btn-primary" onclick="openModal('createAssessmentModal')">
                Add Assessment
            </button>
        </div>
    </div>

    <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h2>Select Section</h2>
        </div>
        <div class="card-body">
            <form method="GET" class="section-select-form">
                <select name="section_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Select a section...</option>
                    <?php foreach ($sections as $section): ?>
                    <option value="<?php echo $section['id']; ?>" <?php echo $sectionId == $section['id'] ? 'selected' : ''; ?>>
                        <?php echo $section['course_code']; ?> - <?php echo $section['course_name']; ?> (Section <?php echo $section['section_number']; ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>

    <?php if ($selectedSection): ?>
    <div class="card">
        <div class="card-header">
            <h2>Assessments for <?php echo $selectedSection['course_code']; ?> - <?php echo $selectedSection['course_name']; ?></h2>
            <span class="section-info">Section <?php echo $selectedSection['section_number']; ?></span>
        </div>
        <div class="card-body">
            <?php if (empty($assessments)): ?>
            <div class="empty-state">
                <p>No assessments created yet. Add an assessment to get started.</p>
            </div>
            <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Max Score</th>
                        <th>Due Date</th>
                        <th>Weight</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assessments as $assessment): ?>
                    <tr>
                        <td><?php echo $assessment['name']; ?></td>
                        <td><?php echo $assessment['type_name']; ?> (<?php echo $assessment['abbreviation']; ?>)</td>
                        <td><?php echo $assessment['max_score']; ?></td>
                        <td><?php echo !empty($assessment['due_date']) ? date('M d, Y', strtotime($assessment['due_date'])) : 'Not set'; ?></td>
                        <td><?php echo $assessment['type_weight']; ?>%</td>
                        <td class="actions">
                            <button class="btn btn-sm btn-outline" onclick="editAssessment(<?php echo $assessment['id']; ?>, '<?php echo $assessment['name']; ?>', <?php echo $assessment['max_score']; ?>)">
                                Edit
                            </button>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete_assessment">
                                <input type="hidden" name="assessment_id" value="<?php echo $assessment['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Grade Distribution</h2>
        </div>
        <div class="card-body">
            <?php
            require_once __DIR__ . '/../models/Grade.php';
            $gradeModel = new Grade();
            $distribution = $gradeModel->getGradeDistribution($sectionId);
            ?>
            <div class="grade-distribution">
                <?php foreach ($distribution as $grade => $count): ?>
                <?php if ($count > 0): ?>
                <div class="grade-bar-item">
                    <span class="grade-bar-label"><?php echo $grade; ?></span>
                    <div class="grade-bar">
                        <div class="grade-bar-fill" style="width: <?php echo ($count / array_sum($distribution)) * 100; ?>%"></div>
                    </div>
                    <span class="grade-bar-count"><?php echo $count; ?></span>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Create Assessment Modal -->
<div class="modal-overlay" id="createAssessmentModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Add Assessment</h3>
            <button class="modal-close" onclick="closeModal('createAssessmentModal')">X</button>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="add_assessment">
                <div class="form-group">
                    <label>Section</label>
                    <select name="section_id" class="form-control" required>
                        <?php foreach ($sections as $section): ?>
                        <option value="<?php echo $section['id']; ?>" <?php echo $sectionId == $section['id'] ? 'selected' : ''; ?>>
                            <?php echo $section['course_code']; ?> - <?php echo $section['course_name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Assessment Type</label>
                    <select name="assessment_type_id" class="form-control" required>
                        <?php
                        require_once __DIR__ . '/../models/Grade.php';
                        $gradeModel = new Grade();
                        $types = $gradeModel->getAssessmentTypes();
                        foreach ($types as $type): ?>
                        <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?> (<?php echo $type['abbreviation']; ?>) - Weight: <?php echo $type['weight']; ?>%</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Assessment Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g., Quiz 1, Midterm Exam">
                </div>
                <div class="form-group">
                    <label>Max Score</label>
                    <input type="number" name="max_score" class="form-control" required min="1" step="0.01">
                </div>
                <div class="form-group">
                    <label>Due Date</label>
                    <input type="date" name="due_date" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('createAssessmentModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }
function editAssessment(id, name, maxScore) { alert('Edit assessment ' + id); }
</script>
