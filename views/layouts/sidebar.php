<div class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <span>EWAMS</span>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <a href="<?php echo BASE_URL; ?>/dashboard" class="nav-item <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
            <span>Dashboard</span>
        </a>
        
        <?php if (getUserRole() === 'administrator'): ?>
        <a href="<?php echo BASE_URL; ?>/users" class="nav-item <?php echo $currentPage === 'users' ? 'active' : ''; ?>">
            <span>Users</span>
        </a>
        <?php endif; ?>
        
        <?php if (in_array(getUserRole(), ['administrator', 'faculty', 'advisor'])): ?>
        <a href="<?php echo BASE_URL; ?>/students" class="nav-item <?php echo $currentPage === 'students' ? 'active' : ''; ?>">
            <span>Students</span>
        </a>
        <?php endif; ?>
        
        <?php if (in_array(getUserRole(), ['administrator', 'faculty'])): ?>
        <a href="<?php echo BASE_URL; ?>/courses" class="nav-item <?php echo $currentPage === 'courses' ? 'active' : ''; ?>">
            <span>Courses</span>
        </a>
        
        <a href="<?php echo BASE_URL; ?>/sections" class="nav-item <?php echo $currentPage === 'sections' ? 'active' : ''; ?>">
            <span>Sections</span>
        </a>
        
        <a href="<?php echo BASE_URL; ?>/grades" class="nav-item <?php echo $currentPage === 'grades' ? 'active' : ''; ?>">
            <span>Grades</span>
        </a>
        
        <a href="<?php echo BASE_URL; ?>/grades/recording" class="nav-item <?php echo $currentPage === 'grades-recording' ? 'active' : ''; ?>">
            <span>Grade Recording</span>
        </a>
        <?php endif; ?>
        
        <?php if (getUserRole() === 'advisor'): ?>
        <a href="<?php echo BASE_URL; ?>/advisor/grades" class="nav-item <?php echo $currentPage === 'advisor-grades' ? 'active' : ''; ?>">
            <span>Student Grades</span>
        </a>
        <?php endif; ?>
        
        <?php if (in_array(getUserRole(), ['administrator', 'faculty', 'advisor'])): ?>
        <a href="<?php echo BASE_URL; ?>/risk" class="nav-item <?php echo $currentPage === 'risk' ? 'active' : ''; ?>">
            <span>Risk Assessment</span>
        </a>
        <?php endif; ?>
        
        <?php if (getUserRole() === 'advisor'): ?>
        <a href="<?php echo BASE_URL; ?>/interventions" class="nav-item <?php echo $currentPage === 'interventions' ? 'active' : ''; ?>">
            <span>Interventions</span>
        </a>
        <?php endif; ?>
        
        <?php if (in_array(getUserRole(), ['administrator', 'faculty', 'advisor'])): ?>
        <a href="<?php echo BASE_URL; ?>/reports" class="nav-item <?php echo $currentPage === 'reports' ? 'active' : ''; ?>">
            <span>Reports</span>
        </a>
        <?php endif; ?>
        
        <?php if (getUserRole() === 'student'): ?>
        <a href="<?php echo BASE_URL; ?>/my-grades" class="nav-item <?php echo $currentPage === 'my-grades' ? 'active' : ''; ?>">
            <span>My Grades</span>
        </a>
        
        <a href="<?php echo BASE_URL; ?>/my-courses" class="nav-item <?php echo $currentPage === 'my-courses' ? 'active' : ''; ?>">
            <span>My Courses</span>
        </a>
        <?php endif; ?>
    </nav>
    
    <div class="sidebar-footer">
        <a href="<?php echo BASE_URL; ?>/auth/logout" class="nav-item">
            <span>Logout</span>
        </a>
    </div>
</div>
