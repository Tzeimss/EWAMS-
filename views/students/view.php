<?php if (!isset($student)): ?>
<div class="page-content">
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-circle"></i>
        Student not found.
    </div>
    <a href="<?php echo BASE_URL; ?>/students" class="btn btn-secondary">Back to Students</a>
</div>
<?php return; endif; ?>

<div class="page-content">
    <div class="profile-header">
        <div class="profile-info">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($student['first_name'], 0, 1) . substr($student['last_name'], 0, 1)); ?>
            </div>
            <div>
                <h1 class="profile-name"><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></h1>
                <p class="profile-meta">
                    <i class="fas fa-envelope"></i> <?php echo $student['email']; ?>
                    <?php if ($student['phone']): ?>
                    | <i class="fas fa-phone"></i> <?php echo $student['phone']; ?>
                    <?php endif; ?>
                </p>
                <?php if ($studentRisk): ?>
                <p class="profile-meta">
                    Risk Level: 
                    <span class="risk-badge <?php echo $studentRisk['risk_level']; ?>">
                        <?php echo ucfirst($studentRisk['risk_level']); ?> 
                        (<?php echo round($studentRisk['risk_score']); ?>%)
                    </span>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="nav-tabs">
        <div class="nav-tab active" data-tab="overview">Overview</div>
        <div class="nav-tab" data-tab="grades">Grades</div>
        <div class="nav-tab" data-tab="attendance">Attendance</div>
        <div class="nav-tab" data-tab="alerts">Alerts</div>
        <div class="nav-tab" data-tab="interventions">Interventions</div>
    </div>

    <div class="tab-content active" id="overview">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo count($studentEnrollments); ?></h3>
                    <p>Enrolled Courses</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo count($studentInterventions); ?></h3>
                    <p>Interventions</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon yellow">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo count($studentAlerts); ?></h3>
                    <p>Alerts</p>
                </div>
            </div>
        </div>

        <?php if ($studentRisk): ?>
        <div class="card">
            <div class="card-header">
                <h2>Risk Assessment</h2>
            </div>
            <div class="card-body">
                <div class="risk-factors">
                    <div class="risk-factor">
                        <div class="risk-factor-value"><?php echo round($studentRisk['grade_score']); ?>%</div>
                        <div class="risk-factor-label">Academic Performance</div>
                    </div>
                    <div class="risk-factor">
                        <div class="risk-factor-value"><?php echo round($studentRisk['attendance_score']); ?>%</div>
                        <div class="risk-factor-label">Attendance</div>
                    </div>
                    <div class="risk-factor">
                        <div class="risk-factor-value"><?php echo round($studentRisk['submission_score'] ?? 100); ?>%</div>
                        <div class="risk-factor-label">Assignment Submission</div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="tab-content" id="grades">
        <div class="card">
            <div class="card-header">
                <h2>Course Grades</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($studentEnrollments)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Section</th>
                            <th>Grade</th>
                            <th>Letter</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($studentEnrollments as $enrollment): 
                            $grade = $studentGrades[$enrollment['id']] ?? ['total' => 0, 'letter' => 'N/A'];
                        ?>
                        <tr>
                            <td><?php echo $enrollment['course_code']; ?> - <?php echo $enrollment['course_name']; ?></td>
                            <td><?php echo $enrollment['section_number']; ?></td>
                            <td><?php echo $grade['total']; ?>%</td>
                            <td><?php echo $grade['letter']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-book-open"></i>
                    <p>No enrollments found</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="tab-content" id="attendance">
        <div class="card">
            <div class="card-header">
                <h2>Attendance Record</h2>
            </div>
            <div class="card-body">
                <div class="empty-state">
                    <i class="fas fa-calendar-check"></i>
                    <p>Attendance data will appear here</p>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content" id="alerts">
        <div class="card">
            <div class="card-header">
                <h2>Alert History</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($studentAlerts)): ?>
                <div class="alerts-list">
                    <?php foreach ($studentAlerts as $alert): ?>
                    <div class="alert-item <?php echo $alert['severity']; ?>">
                        <i class="fas fa-bell"></i>
                        <div class="alert-content">
                            <p><?php echo $alert['title']; ?></p>
                            <small><?php echo $alert['message']; ?></small>
                            <small class="text-muted"><?php echo date('M d, Y', strtotime($alert['created_at'])); ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <p>No alerts</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="tab-content" id="interventions">
        <div class="card">
            <div class="card-header">
                <h2>Intervention History</h2>
                <?php if (getUserRole() === 'advisor' || getUserRole() === 'administrator'): ?>
                <button class="btn btn-primary btn-sm" onclick="openModal('createInterventionModal')">
                    <i class="fas fa-plus"></i> New Intervention
                </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (!empty($studentInterventions)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($studentInterventions as $intervention): ?>
                        <tr>
                            <td><?php echo $intervention['type']; ?></td>
                            <td><?php echo $intervention['description']; ?></td>
                            <td>
                                <span class="status-badge <?php echo $intervention['status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $intervention['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($intervention['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-hand-holding-heart"></i>
                    <p>No interventions recorded</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.nav-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        document.getElementById(this.dataset.tab).classList.add('active');
    });
});

function openModal(id) {
    document.getElementById(id).classList.add('active');
}
</script>
