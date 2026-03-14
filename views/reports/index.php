<?php
$pageTitle = 'Reports';
$currentPage = 'reports';
?>

<div class="page-content">
    <div class="page-header">
        <h1>Reports & Analytics</h1>
    </div>

    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
            <h2><i class="fas fa-chart-line"></i> Overview Statistics</h2>
        </div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
                <canvas id="overviewChart"></canvas>
            </div>
        </div>
    </div>

    <script>
    var overviewCtx = document.getElementById('overviewChart').getContext('2d');
    new Chart(overviewCtx, {
        type: 'bar',
        data: {
            labels: ['At-Risk Students', 'Interventions', 'Active Alerts', 'Total Students'],
            datasets: [{
                label: 'Count',
                data: [
                    <?php echo count($reportData['high_risk'] ?? []) + count($reportData['moderate_risk'] ?? []); ?>,
                    <?php echo ($reportData['stats']['planned'] ?? 0) + ($reportData['stats']['in_progress'] ?? 0); ?>,
                    12,
                    <?php echo $dashboardData['total_students'] ?? 0; ?>
                ],
                backgroundColor: ['#e53e3e', '#805ad5', '#d69e2e', '#3182ce']
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

    <div class="grid-2">
        <div class="card report-card">
            <div class="card-body">
                <i class="fas fa-exclamation-triangle report-icon"></i>
                <h3>At-Risk Students</h3>
                <p>View all students at risk with detailed metrics</p>
                <a href="?type=at_risk" class="btn btn-primary">View Report</a>
            </div>
        </div>
        <div class="card report-card">
            <div class="card-body">
                <i class="fas fa-hand-holding-heart report-icon"></i>
                <h3>Intervention Summary</h3>
                <p>Summary of all interventions and outcomes</p>
                <a href="?type=interventions" class="btn btn-primary">View Report</a>
            </div>
        </div>
        <div class="card report-card">
            <div class="card-body">
                <i class="fas fa-chart-bar report-icon"></i>
                <h3>Course Performance</h3>
                <p>Grade distribution and performance by course</p>
                <a href="?type=course_performance" class="btn btn-primary">View Report</a>
            </div>
        </div>
        <div class="card report-card">
            <div class="card-body">
                <i class="fas fa-user-clock report-icon"></i>
                <h3>Retention Prediction</h3>
                <p>Student retention risk analysis</p>
                <a href="?type=retention" class="btn btn-primary">View Report</a>
            </div>
        </div>
    </div>

    <?php if ($reportType && $reportType !== 'overview'): ?>
    <div class="card">
        <div class="card-header">
            <h2><?php echo $reportData['title'] ?? 'Report'; ?></h2>
            <a href="?type=<?php echo $reportType; ?>&export=csv" class="btn btn-secondary btn-sm">
                <i class="fas fa-download"></i> Export CSV
            </a>
        </div>
        <div class="card-body">
            <?php if ($reportType === 'at_risk'): ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon red"><i class="fas fa-exclamation-circle"></i></div>
                    <div class="stat-content"><h3><?php echo count($reportData['high_risk'] ?? []); ?></h3><p>High Risk</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon yellow"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="stat-content"><h3><?php echo count($reportData['moderate_risk'] ?? []); ?></h3><p>Moderate Risk</p></div>
                </div>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Email</th>
                        <th>Risk Score</th>
                        <th>Risk Level</th>
                        <th>Grade Score</th>
                        <th>Attendance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reportData['students'])): ?>
                    <?php foreach ($reportData['students'] as $student): ?>
                    <tr>
                        <td><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></td>
                        <td><?php echo $student['email']; ?></td>
                        <td><?php echo round($student['risk_score']); ?>%</td>
                        <td><span class="risk-badge <?php echo $student['risk_level']; ?>"><?php echo ucfirst($student['risk_level']); ?></span></td>
                        <td><?php echo round($student['grade_score'] ?? 0); ?>%</td>
                        <td><?php echo round($student['attendance_score'] ?? 0); ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr><td colspan="6" class="text-center">No at-risk students found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php elseif ($reportType === 'interventions'): ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-content"><h3><?php echo $reportData['stats']['planned'] ?? 0; ?></h3><p>Planned</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-content"><h3><?php echo $reportData['stats']['in_progress'] ?? 0; ?></h3><p>In Progress</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-content"><h3><?php echo $reportData['stats']['completed'] ?? 0; ?></h3><p>Completed</p></div>
                </div>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reportData['interventions'])): ?>
                    <?php foreach ($reportData['interventions'] as $i): ?>
                    <tr>
                        <td><?php echo $i['first_name'] . ' ' . $i['last_name']; ?></td>
                        <td><?php echo $i['type']; ?></td>
                        <td><span class="status-badge <?php echo $i['status']; ?>"><?php echo ucfirst($i['status']); ?></span></td>
                        <td><?php echo !empty($i['created_at']) ? date('M d, Y', strtotime($i['created_at'])) : 'N/A'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr><td colspan="4" class="text-center">No interventions found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php elseif ($reportType === 'course_performance'): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Section</th>
                        <th>Enrolled</th>
                        <th>At Risk</th>
                        <th>Grade Dist</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reportData['sections'])): ?>
                    <?php foreach ($reportData['sections'] as $s): ?>
                    <tr>
                        <td><?php echo $s['course_code'] ?? ''; ?> - <?php echo $s['course_name'] ?? ''; ?></td>
                        <td><?php echo $s['section_number'] ?? ''; ?></td>
                        <td><?php echo $s['enrolled_count'] ?? 0; ?></td>
                        <td><?php echo $s['at_risk_count'] ?? 0; ?></td>
                        <td>
                            <?php if (!empty($s['grade_dist'])): ?>
                            <?php foreach ($s['grade_dist'] as $letter => $count): ?>
                            <?php if ($count > 0): ?>
                            <span class="grade-mini"><?php echo $letter; ?>:<?php echo $count; ?></span>
                            <?php endif; ?>
                            <?php endforeach; ?>
                            <?php else: ?>
                            N/A
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr><td colspan="5" class="text-center">No course data found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php elseif ($reportType === 'retention'): ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon red"><i class="fas fa-exclamation-circle"></i></div>
                    <div class="stat-content"><h3><?php echo $reportData['high_risk_count'] ?? 0; ?></h3><p>High Risk</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon yellow"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="stat-content"><h3><?php echo $reportData['moderate_risk_count'] ?? 0; ?></h3><p>Moderate Risk</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-content"><h3><?php echo $reportData['low_risk_count'] ?? 0; ?></h3><p>Low Risk</p></div>
                </div>
            </div>
            <p class="text-center">At-Risk Percentage: <strong><?php echo $reportData['at_risk_percentage'] ?? 0; ?>%</strong></p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.report-card { text-align: center; }
.report-icon { font-size: 48px; color: var(--primary); margin-bottom: 16px; }
.report-card h3 { margin-bottom: 8px; }
.report-card p { margin-bottom: 16px; color: var(--text-secondary); }
.grade-mini { padding: 2px 6px; background: var(--bg); border-radius: 4px; margin-right: 4px; font-size: 12px; }
.text-center { text-align: center; }
</style>
