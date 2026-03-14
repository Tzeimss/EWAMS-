<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/helpers/functions.php';

$request = $_SERVER['REQUEST_URI'];
$basePath = '/opencode-php';
$path = str_replace($basePath, '', $request);
$path = parse_url($path, PHP_URL_PATH);
$path = trim($path, '/');

$page = $path ?: 'dashboard';

if ($page === 'auth/login' || $page === 'login') {
    $pageTitle = 'Login';
    $isAuthPage = true;
    
    if (isLoggedIn()) {
        redirect('dashboard');
    }
    
    $error = null;
    $success = null;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
        require_once __DIR__ . '/config/config.php';
        require_once __DIR__ . '/models/User.php';
        
        $userModel = new User();
        $username = sanitize($_POST['username']);
        $password = $_POST['password'];
        
        $user = $userModel->findByUsername($username);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['is_active']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                
                redirect('dashboard');
            } else {
                $error = 'Account is deactivated. Please contact administrator.';
            }
        } else {
            $error = 'Invalid username or password.';
        }
    }
    
    include __DIR__ . '/views/auth/login.php';
    exit;
}

if ($page === 'auth/logout' || $page === 'logout') {
    session_destroy();
    redirect('auth/login');
}

requireLogin();

if ($page === 'dashboard') {
    include __DIR__ . '/controllers/DashboardController.php';
    exit;
}

if ($page === 'users' || $page === 'users/index') {
    requireRole('administrator');
    $pageTitle = 'User Management';
    $currentPage = 'users';
    
    require_once __DIR__ . '/controllers/AuthController.php';
    require_once __DIR__ . '/models/User.php';
    $userModel = new User();
    $users = $userModel->getAll();
    
    include __DIR__ . '/views/layouts/header.php';
    include __DIR__ . '/views/users/index.php';
    include __DIR__ . '/views/layouts/footer.php';
    exit;
}

if ($page === 'students' || $page === 'students/index') {
    requireRole(['administrator', 'faculty', 'advisor']);
    $pageTitle = 'Students';
    $currentPage = 'students';
    
    include __DIR__ . '/controllers/StudentController.php';
    include __DIR__ . '/views/layouts/header.php';
    include __DIR__ . '/views/students/index.php';
    include __DIR__ . '/views/layouts/footer.php';
    exit;
}

if ($page === 'students/view' || (isset($_GET['id']) && $page === 'students')) {
    requireRole(['administrator', 'faculty', 'advisor']);
    $pageTitle = 'Student Profile';
    $currentPage = 'students';
    
    include __DIR__ . '/controllers/StudentController.php';
    include __DIR__ . '/views/layouts/header.php';
    include __DIR__ . '/views/students/view.php';
    include __DIR__ . '/views/layouts/footer.php';
    exit;
}

if ($page === 'courses' || $page === 'courses/index') {
    requireRole(['administrator', 'faculty']);
    $pageTitle = 'Courses';
    $currentPage = 'courses';
    
    include __DIR__ . '/controllers/CourseController.php';
    include __DIR__ . '/views/layouts/header.php';
    include __DIR__ . '/views/courses/index.php';
    include __DIR__ . '/views/layouts/footer.php';
    exit;
}

if ($page === 'sections' || $page === 'sections/index') {
    requireRole(['administrator', 'faculty']);
    $pageTitle = 'Sections';
    $currentPage = 'sections';
    
    include __DIR__ . '/controllers/CourseController.php';
    include __DIR__ . '/views/layouts/header.php';
    include __DIR__ . '/views/sections/index.php';
    include __DIR__ . '/views/layouts/footer.php';
    exit;
}

if ($page === 'sections/view' && isset($_GET['id'])) {
    requireRole(['administrator', 'faculty']);
    $pageTitle = 'Section Details';
    $currentPage = 'sections';
    
    include __DIR__ . '/controllers/CourseController.php';
    include __DIR__ . '/views/layouts/header.php';
    include __DIR__ . '/views/sections/view.php';
    include __DIR__ . '/views/layouts/footer.php';
    exit;
}

if ($page === 'grades' || $page === 'grades/index') {
    requireRole(['administrator', 'faculty']);
    $pageTitle = 'Grade Management';
    $currentPage = 'grades';
    
    include __DIR__ . '/controllers/GradeController.php';
    include __DIR__ . '/views/layouts/header.php';
    include __DIR__ . '/views/grades/index.php';
    include __DIR__ . '/views/layouts/footer.php';
    exit;
}

if ($page === 'grades/entry' && isset($_GET['section_id'])) {
    requireRole(['administrator', 'faculty']);
    $pageTitle = 'Grade Entry';
    $currentPage = 'grades';
    
    include __DIR__ . '/controllers/GradeController.php';
    include __DIR__ . '/views/layouts/header.php';
    include __DIR__ . '/views/grades/entry.php';
    include __DIR__ . '/views/layouts/footer.php';
    exit;
}

if ($page === 'grades/recording' || $page === 'grades/config') {
    requireRole(['administrator', 'faculty']);
    $pageTitle = 'Grade Recording';
    $currentPage = 'grades';
    
    include __DIR__ . '/controllers/AssessmentController.php';
    include __DIR__ . '/views/layouts/header.php';
    include __DIR__ . '/views/grades/recording.php';
    include __DIR__ . '/views/layouts/footer.php';
    exit;
}

if ($page === 'risk' || $page === 'risk/index') {
    requireRole(['administrator', 'faculty', 'advisor']);
    $pageTitle = 'Risk Assessment';
    $currentPage = 'risk';
    
    include __DIR__ . '/controllers/RiskController.php';
    include __DIR__ . '/views/layouts/header.php';
    include __DIR__ . '/views/students/risk.php';
    include __DIR__ . '/views/layouts/footer.php';
    exit;
}

if ($page === 'risk/calculate') {
    requireRole(['administrator', 'faculty', 'advisor']);
    
    include __DIR__ . '/controllers/RiskController.php';
    exit;
}

if ($page === 'interventions' || $page === 'interventions/index') {
    requireRole('advisor');
    $pageTitle = 'Interventions';
    $currentPage = 'interventions';
    
    include __DIR__ . '/controllers/InterventionController.php';
    include __DIR__ . '/views/layouts/header.php';
    include __DIR__ . '/views/students/interventions.php';
    include __DIR__ . '/views/layouts/footer.php';
    exit;
}

if ($page === 'advisor/grades' || $page === 'advisor-grades') {
    requireRole('advisor');
    $pageTitle = 'Student Grades';
    $currentPage = 'advisor-grades';
    
    include __DIR__ . '/controllers/AdvisorGradeController.php';
    include __DIR__ . '/views/layouts/header.php';
    include __DIR__ . '/views/advisor/grades.php';
    include __DIR__ . '/views/layouts/footer.php';
    exit;
}

if ($page === 'reports' || $page === 'reports/index') {
    requireRole(['administrator', 'faculty', 'advisor']);
    $pageTitle = 'Reports';
    $currentPage = 'reports';
    
    include __DIR__ . '/controllers/ReportController.php';
    include __DIR__ . '/views/layouts/header.php';
    include __DIR__ . '/views/reports/index.php';
    include __DIR__ . '/views/layouts/footer.php';
    exit;
}

if ($page === 'my-grades') {
    requireRole('student');
    $pageTitle = 'My Grades';
    $currentPage = 'my-grades';
    
    include __DIR__ . '/controllers/DashboardController.php';
    include __DIR__ . '/views/layouts/header.php';
    include __DIR__ . '/views/students/my-grades.php';
    include __DIR__ . '/views/layouts/footer.php';
    exit;
}

if ($page === 'my-courses') {
    requireRole('student');
    $pageTitle = 'My Courses';
    $currentPage = 'my-courses';
    
    include __DIR__ . '/controllers/DashboardController.php';
    include __DIR__ . '/views/layouts/header.php';
    include __DIR__ . '/views/students/my-courses.php';
    include __DIR__ . '/views/layouts/footer.php';
    exit;
}

header('HTTP/1.0 404 Not Found');
echo 'Page not found';
