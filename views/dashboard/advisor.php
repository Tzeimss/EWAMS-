<?php
$pageTitle = 'Advisor Dashboard';
$currentPage = 'dashboard';

include __DIR__ . '/../layouts/header.php';
?>

<div class="dashboard">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon red">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count($dashboardData['high_risk'] ?? []); ?></h3>
                <p>High Risk Students</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon yellow">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count($dashboardData['moderate_risk'] ?? []); ?></h3>
                <p>Moderate Risk</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fas fa-handshake"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $dashboardData['intervention_stats']['in_progress'] ?? 0; ?></h3>
                <p>Active Interventions</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $dashboardData['intervention_stats']['completed'] ?? 0; ?></h3>
                <p>Completed Interventions</p>
            </div>
        </div>
    </div>
    
    <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr; margin-bottom: 24px;">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-chart-pie"></i> Risk Overview</h2>
            </div>
            <div class="card-body">
                <canvas id="advisorRiskChart"></canvas>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-chart-bar"></i> Intervention Status</h2>
            </div>
            <div class="card-body">
                <canvas id="interventionChart"></canvas>
            </div>
        </div>
    </div>
    
    <script>
    var riskCtx = document.getElementById('advisorRiskChart').getContext('2d');
    new Chart(riskCtx, {
        type: 'doughnut',
        data: {
            labels: ['High Risk', 'Moderate Risk', 'Low Risk'],
            datasets: [{
                data: [
                    <?php echo count($dashboardData['high_risk'] ?? []); ?>,
                    <?php echo count($dashboardData['moderate_risk'] ?? []); ?>,
                    <?php echo max(0, 50 - count($dashboardData['high_risk'] ?? []) - count($dashboardData['moderate_risk'] ?? [])); ?>
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
    
    var intCtx = document.getElementById('interventionChart').getContext('2d');
    new Chart(intCtx, {
        type: 'bar',
        data: {
            labels: ['Planned', 'In Progress', 'Completed', 'Cancelled'],
            datasets: [{
                label: 'Interventions',
                data: [
                    <?php echo $dashboardData['intervention_stats']['planned'] ?? 0; ?>,
                    <?php echo $dashboardData['intervention_stats']['in_progress'] ?? 0; ?>,
                    <?php echo $dashboardData['intervention_stats']['completed'] ?? 0; ?>,
                    <?php echo $dashboardData['intervention_stats']['cancelled'] ?? 0; ?>
                ],
                backgroundColor: ['#3182ce', '#805ad5', '#38a169', '#718096']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
    </script>
    
    <div class="dashboard-grid">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-user-shield"></i> At-Risk Students</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($dashboardData['at_risk_students'])): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Email</th>
                            <th>Risk Score</th>
                            <th>Risk Level</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($dashboardData['at_risk_students'], 0, 10) as $student): ?>
                        <tr>
                            <td><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></td>
                            <td><?php echo $student['email']; ?></td>
                            <td><?php echo round($student['risk_score']); ?>%</td>
                            <td>
                                <span class="risk-badge <?php echo $student['risk_level']; ?>">
                                    <?php echo ucfirst($student['risk_level']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>/students/view?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-outline">
                                    View
                                </a>
                                <a href="<?php echo BASE_URL; ?>/interventions?student_id=<?php echo $student['id']; ?>" class="btn btn-sm btn-primary">
                                    Intervene
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="example-student-card">
                    <div class="example-badge">Example</div>
                    <h4>Sarah Johnson</h4>
                    <p class="example-email">sarah.johnson@university.edu</p>
                    <div class="example-scores">
                        <div class="score-item">
                            <span class="score-label">Risk Score</span>
                            <span class="score-value high">85%</span>
                        </div>
                        <div class="score-item">
                            <span class="score-label">Grade Score</span>
                            <span class="score-value">58%</span>
                        </div>
                        <div class="score-item">
                            <span class="score-label">Attendance</span>
                            <span class="score-value">40%</span>
                        </div>
                    </div>
                    <span class="risk-badge high">High Risk</span>
                    <p class="example-reason">
                        <i class="fas fa-exclamation-circle"></i>
                        Failing 2 courses, attendance below 50%, 5 missing assignments
                    </p>
                    <a href="<?php echo BASE_URL; ?>/interventions" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Create Intervention
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-tasks"></i> My Interventions</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($dashboardData['interventions'])): ?>
                <div class="intervention-list">
                    <?php foreach (array_slice($dashboardData['interventions'], 0, 5) as $intervention): ?>
                    <div class="intervention-item">
                        <div class="intervention-info">
                            <h4><?php echo $intervention['first_name'] . ' ' . $intervention['last_name']; ?></h4>
                            <p><?php echo $intervention['type']; ?></p>
                        </div>
                        <span class="status-badge <?php echo $intervention['status']; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $intervention['status'])); ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="card-footer">
                    <a href="<?php echo BASE_URL; ?>/interventions" class="btn btn-outline btn-block">View All</a>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-hand-holding-heart"></i>
                    <p>No interventions yet</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
