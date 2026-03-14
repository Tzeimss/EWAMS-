<?php
$pageTitle = 'Dashboard';
$currentPage = 'dashboard';

include __DIR__ . '/../layouts/header.php';
?>

<div class="dashboard">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $dashboardData['total_students'] ?? 0; ?></h3>
                <p>Total Students</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $dashboardData['total_faculty'] ?? 0; ?></h3>
                <p>Faculty Members</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $dashboardData['total_advisors'] ?? 0; ?></h3>
                <p>Advisors</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon red">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count($dashboardData['at_risk_students'] ?? []); ?></h3>
                <p>High Risk Students</p>
            </div>
        </div>
    </div>
    
    <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr; margin-bottom: 24px;">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-chart-pie"></i> Risk Distribution</h2>
            </div>
            <div class="card-body">
                <canvas id="riskChart"></canvas>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-chart-bar"></i> User Statistics</h2>
            </div>
            <div class="card-body">
                <canvas id="userChart"></canvas>
            </div>
        </div>
    </div>
    
    <script>
    var riskCtx = document.getElementById('riskChart').getContext('2d');
    new Chart(riskCtx, {
        type: 'doughnut',
        data: {
            labels: ['High Risk', 'Moderate Risk', 'Low Risk'],
            datasets: [{
                data: [
                    <?php echo count($dashboardData['at_risk_students'] ?? []); ?>,
                    <?php echo count($dashboardData['moderate_risk'] ?? []); ?>,
                    <?php echo max(0, ($dashboardData['total_students'] ?? 0) - count($dashboardData['at_risk_students'] ?? []) - count($dashboardData['moderate_risk'] ?? [])); ?>
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
    
    var userCtx = document.getElementById('userChart').getContext('2d');
    new Chart(userCtx, {
        type: 'bar',
        data: {
            labels: ['Students', 'Faculty', 'Advisors'],
            datasets: [{
                label: 'Count',
                data: [
                    <?php echo $dashboardData['total_students'] ?? 0; ?>,
                    <?php echo $dashboardData['total_faculty'] ?? 0; ?>,
                    <?php echo $dashboardData['total_advisors'] ?? 0; ?>
                ],
                backgroundColor: ['#3182ce', '#38a169', '#805ad5']
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
                <h2><i class="fas fa-exclamation-circle"></i> At-Risk Students</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($dashboardData['at_risk_students'])): ?>
                <div class="risk-summary">
                    <div class="risk-item high">
                        <span class="risk-count"><?php echo count($dashboardData['at_risk_students']); ?></span>
                        <span class="risk-label">High Risk</span>
                    </div>
                    <div class="risk-item moderate">
                        <span class="risk-count"><?php echo count($dashboardData['moderate_risk'] ?? []); ?></span>
                        <span class="risk-label">Moderate Risk</span>
                    </div>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Email</th>
                            <th>Risk Score</th>
                            <th>Risk Level</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($dashboardData['at_risk_students'], 0, 5) as $student): ?>
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
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="example-student-card">
                    <div class="example-badge">Example</div>
                    <h4>John Smith</h4>
                    <p class="example-email">john.smith@university.edu</p>
                    <div class="example-scores">
                        <div class="score-item">
                            <span class="score-label">Risk Score</span>
                            <span class="score-value high">78%</span>
                        </div>
                        <div class="score-item">
                            <span class="score-label">Grade Score</span>
                            <span class="score-value">62%</span>
                        </div>
                        <div class="score-item">
                            <span class="score-label">Attendance</span>
                            <span class="score-value">45%</span>
                        </div>
                    </div>
                    <span class="risk-badge high">High Risk</span>
                    <p class="example-reason">
                        <i class="fas fa-exclamation-circle"></i>
                        Low grades (62%), poor attendance (45%), 3 missing assignments
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-calendar-alt"></i> Academic Terms</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($dashboardData['terms'])): ?>
                <div class="terms-list">
                    <?php foreach (array_slice($dashboardData['terms'], 0, 4) as $term): ?>
                    <div class="term-item <?php echo $term['is_current'] ? 'current' : ''; ?>">
                        <span class="term-name"><?php echo $term['name']; ?></span>
                        <?php if ($term['is_current']): ?>
                        <span class="term-badge">Current</span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-calendar"></i>
                    <p>No terms configured</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
