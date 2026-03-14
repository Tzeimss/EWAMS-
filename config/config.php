<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'ewams_db');
define('DB_USER', 'root');
define('DB_PASS', '');

define('SITE_NAME', 'Early Warning Academic Monitoring System');
define('BASE_URL', 'http://localhost/opencode-php');

define('RISK_THRESHOLDS', [
    'low' => 30,
    'moderate' => 60,
    'high' => 100
]);

define('RISK_WEIGHTS', [
    'attendance' => 0.25,
    'grades' => 0.35,
    'submissions' => 0.20,
    'late_frequency' => 0.20
]);

define('GRADING_SCALE', [
    'A' => ['min' => 90, 'max' => 100, 'points' => 4.0],
    'A-' => ['min' => 85, 'max' => 89, 'points' => 3.7],
    'B+' => ['min' => 82, 'max' => 84, 'points' => 3.3],
    'B' => ['min' => 78, 'max' => 81, 'points' => 3.0],
    'B-' => ['min' => 75, 'max' => 77, 'points' => 2.7],
    'C+' => ['min' => 72, 'max' => 74, 'points' => 2.3],
    'C' => ['min' => 68, 'max' => 71, 'points' => 2.0],
    'C-' => ['min' => 65, 'max' => 67, 'points' => 1.7],
    'D' => ['min' => 60, 'max' => 64, 'points' => 1.0],
    'F' => ['min' => 0, 'max' => 59, 'points' => 0.0]
]);

session_start();

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
            );
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    return $pdo;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserRole() {
    return $_SESSION['role'] ?? null;
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }
}

function requireRole($roles) {
    requireLogin();
    if (!in_array(getUserRole(), (array)$roles)) {
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }
}

function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function redirect($path) {
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}
