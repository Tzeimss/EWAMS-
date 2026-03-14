<?php
$pageTitle = 'Faculty Dashboard';
$currentPage = 'dashboard';

include __DIR__ . '/../layouts/header.php';
?>

<div class="dashboard">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count($dashboardData['sections'] ?? []); ?></h3>
                <p>My Sections</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon red">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $dashboardData['at_risk_count'] ?? 0; ?></h3>
                <p>At-Risk Students</p>
            </div>
        </div>
    </div>
    
    <div class="dashboard-grid">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-book"></i> My Courses</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($dashboardData['sections'])): ?>
                <div class="course-list">
                    <?php foreach ($dashboardData['sections'] as $section): ?>
                    <div class="course-item">
                        <div class="course-info">
                            <h4><?php echo $section['course_code']; ?> - <?php echo $section['course_name']; ?></h4>
                            <p>Section <?php echo $section['section_number']; ?> | <?php echo $section['term_name']; ?></p>
                        </div>
                        <div class="course-meta">
                            <span class="students-count">
                                <i class="fas fa-users"></i>
                                <?php echo $section['enrolled_count']; ?>/<?php echo $section['capacity']; ?>
                            </span>
                            <a href="<?php echo BASE_URL; ?>/grades?section_id=<?php echo $section['id']; ?>" class="btn btn-sm btn-primary">
                                Manage Grades
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-book-open"></i>
                    <p>No sections assigned</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-bell"></i> Recent Alerts</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($dashboardData['recent_alerts'])): ?>
                <div class="alerts-list">
                    <?php foreach ($dashboardData['recent_alerts'] as $alert): ?>
                    <div class="alert-item <?php echo $alert['severity']; ?>">
                        <i class="fas fa-exclamation-circle"></i>
                        <div class="alert-content">
                            <p><?php echo $alert['title']; ?></p>
                            <small><?php echo date('M d, Y', strtotime($alert['created_at'])); ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <p>No recent alerts</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
