<?php
$pageTitle = 'Risk Assessment';
$currentPage = 'risk';
?>

<div class="page-content">
    <div class="page-header">
        <h1>Risk Assessment</h1>
        <div class="page-actions">
            <form method="POST" action="">
                <input type="hidden" name="action" value="recalculate_all">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-calculator"></i> Recalculate All
                </button>
            </form>
        </div>
    </div>

    <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
            <h2><i class="fas fa-chart-pie"></i> Risk Distribution</h2>
        </div>
        <div class="card-body">
            <canvas id="riskDistributionChart" height="100"></canvas>
        </div>
    </div>

    <script>
    var riskCtx = document.getElementById('riskDistributionChart').getContext('2d');
    new Chart(riskCtx, {
        type: 'doughnut',
        data: {
            labels: ['High Risk', 'Moderate Risk', 'Low Risk'],
            datasets: [{
                data: [
                    <?php echo $riskStats['high']; ?>,
                    <?php echo $riskStats['moderate']; ?>,
                    <?php echo $riskStats['low']; ?>
                ],
                backgroundColor: ['#e53e3e', '#d69e2e', '#38a169'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
    </script>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon red">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count($highRisk); ?></h3>
                <p>High Risk</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon yellow">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count($moderateRisk); ?></h3>
                <p>Moderate Risk</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count($lowRisk); ?></h3>
                <p>Low Risk</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>All At-Risk Students</h2>
        </div>
        <div class="card-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Email</th>
                        <th>Risk Score</th>
                        <th>Risk Level</th>
                        <th>Grade Score</th>
                        <th>Attendance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($atRiskStudents)): ?>
                    <?php foreach ($atRiskStudents as $student): ?>
                    <tr>
                        <td><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></td>
                        <td><?php echo $student['email']; ?></td>
                        <td><?php echo round($student['risk_score']); ?>%</td>
                        <td>
                            <span class="risk-badge <?php echo $student['risk_level']; ?>">
                                <?php echo ucfirst($student['risk_level']); ?>
                            </span>
                        </td>
                        <td><?php echo round($student['grade_score'] ?? 0); ?>%</td>
                        <td><?php echo round($student['attendance_score'] ?? 0); ?>%</td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>/students?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-outline">
                                View
                            </a>
                            <?php if (getUserRole() === 'advisor'): ?>
                            <a href="<?php echo BASE_URL; ?>/interventions?student_id=<?php echo $student['id']; ?>" class="btn btn-sm btn-primary">
                                Intervene
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="7">
                            <div class="example-risk-row">
                                <div class="example-badge">Example</div>
                                <div class="example-info">
                                    <strong>Michael Chen</strong><br>
                                    michael.chen@university.edu
                                </div>
                                <div class="example-metrics">
                                    <span class="metric">78%</span>
                                    <span class="risk-badge high">High Risk</span>
                                    <span class="metric">62%</span>
                                    <span class="metric">45%</span>
                                </div>
                                <div class="example-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Multiple failing grades, low attendance, missing assignments
                                </div>
                            </div>
                            <p class="text-muted text-center">No real risk assessment data found. Above is an example of what an at-risk student looks like.</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
