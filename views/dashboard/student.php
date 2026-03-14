<?php
$pageTitle = 'My Dashboard';
$currentPage = 'dashboard';

include __DIR__ . '/../layouts/header.php';
?>

<div class="dashboard student-dashboard">
    <div class="risk-banner <?php echo $dashboardData['risk']['risk_level'] ?? 'low'; ?>">
        <div class="risk-info">
            <div>
                <h3>Your Risk Status</h3>
                <p>
                    <?php 
                    $level = $dashboardData['risk']['risk_level'] ?? 'low';
                    if ($level === 'high'):
                        echo 'You are currently at high risk. Please contact your advisor.';
                    elseif ($level === 'moderate'):
                        echo 'You are at moderate risk. Consider seeking academic support.';
                    else:
                        echo 'You are doing well! Keep up the good work.';
                    endif;
                    ?>
                </p>
            </div>
        </div>
        <div class="risk-score">
            <span class="score"><?php echo round($dashboardData['risk']['risk_score'] ?? 0); ?></span>
            <span class="label">Risk Score</span>
        </div>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-content">
                <h3><?php echo count($dashboardData['enrollments'] ?? []); ?></h3>
                <p>Enrolled Courses</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-content">
                <h3><?php 
                $avg = 0;
                $count = 0;
                foreach ($dashboardData['grades'] ?? [] as $g) {
                    if ($g['total'] > 0) {
                        $avg += $g['total'];
                        $count++;
                    }
                }
                echo $count > 0 ? round($avg / $count, 1) . '%' : 'N/A';
                ?></h3>
                <p>Average Grade</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-content">
                <h3><?php echo count($dashboardData['alerts'] ?? []); ?></h3>
                <p>Alerts</p>
            </div>
        </div>
    </div>
    
    <div class="dashboard-grid">
        <div class="card">
            <div class="card-header">
                <h2>My Grades</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($dashboardData['enrollments'])): ?>
                <div class="grades-list">
                    <?php foreach ($dashboardData['enrollments'] as $section): 
                        $grade = $dashboardData['grades'][$section['id']] ?? ['total' => 0, 'letter' => 'N/A'];
                    ?>
                    <div class="grade-item">
                        <div class="grade-info">
                            <h4><?php echo $section['course_code']; ?> - <?php echo $section['course_name']; ?></h4>
                            <p>Section <?php echo $section['section_number']; ?></p>
                        </div>
                        <div class="grade-value">
                            <span class="letter"><?php echo $grade['letter']; ?></span>
                            <span class="percentage"><?php echo $grade['total']; ?>%</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <p>No enrolled courses</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2>Alerts & Notices</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($dashboardData['alerts'])): ?>
                <div class="alerts-list">
                    <?php foreach ($dashboardData['alerts'] as $alert): ?>
                    <div class="alert-item <?php echo $alert['severity']; ?>">
                        <div class="alert-content">
                            <p><?php echo $alert['title']; ?></p>
                            <small><?php echo $alert['message']; ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <p>No alerts</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php if (!empty($dashboardData['risk'])): ?>
    <div class="card">
        <div class="card-header">
            <h2>Risk Factors</h2>
        </div>
        <div class="card-body">
            <div class="risk-factors">
                <div class="factor">
                    <div class="factor-info">
                        <span>Academic Performance</span>
                        <span class="score"><?php echo round($dashboardData['risk']['grade_score'] ?? 0); ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress" style="width: <?php echo $dashboardData['risk']['grade_score'] ?? 0; ?>%"></div>
                    </div>
                </div>
                <div class="factor">
                    <div class="factor-info">
                        <span>Attendance</span>
                        <span class="score"><?php echo round($dashboardData['risk']['attendance_score'] ?? 0); ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress" style="width: <?php echo $dashboardData['risk']['attendance_score'] ?? 0; ?>%"></div>
                    </div>
                </div>
                <div class="factor">
                    <div class="factor-info">
                        <span>Assignment Submission</span>
                        <span class="score"><?php echo round($dashboardData['risk']['submission_score'] ?? 0); ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress" style="width: <?php echo $dashboardData['risk']['submission_score'] ?? 0; ?>%"></div>
                    </div>
                </div>
                <div class="factor">
                    <div class="factor-info">
                        <span>Late Submissions</span>
                        <span class="score"><?php echo round($dashboardData['risk']['late_score'] ?? 0); ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress" style="width: <?php echo $dashboardData['risk']['late_score'] ?? 0; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
