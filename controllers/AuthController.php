<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';

$userModel = new User();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'login') {
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
    
    if ($action === 'register') {
        requireRole('administrator');
        
        $data = [
            'username' => sanitize($_POST['username']),
            'email' => sanitize($_POST['email']),
            'password' => $_POST['password'],
            'role' => sanitize($_POST['role']),
            'first_name' => sanitize($_POST['first_name']),
            'last_name' => sanitize($_POST['last_name']),
            'phone' => sanitize($_POST['phone'] ?? '')
        ];
        
        if ($userModel->findByUsername($data['username'])) {
            $error = 'Username already exists.';
        } elseif ($userModel->findByEmail($data['email'])) {
            $error = 'Email already exists.';
        } elseif ($userModel->create($data)) {
            $success = 'User created successfully.';
        } else {
            $error = 'Failed to create user.';
        }
    }
    
    if ($action === 'create_user') {
        requireRole('administrator');
        
        $data = [
            'username' => sanitize($_POST['username']),
            'email' => sanitize($_POST['email']),
            'password' => $_POST['password'],
            'role' => sanitize($_POST['role']),
            'first_name' => sanitize($_POST['first_name']),
            'last_name' => sanitize($_POST['last_name']),
            'phone' => sanitize($_POST['phone'] ?? '')
        ];
        
        if ($userModel->findByUsername($data['username'])) {
            $error = 'Username already exists.';
        } elseif ($userModel->findByEmail($data['email'])) {
            $error = 'Email already exists.';
        } elseif ($userModel->create($data)) {
            $success = 'User created successfully.';
        } else {
            $error = 'Failed to create user.';
        }
    }
    
    if ($action === 'update_user') {
        requireRole('administrator');
        
        $id = (int)$_POST['user_id'];
        $data = [
            'username' => sanitize($_POST['username']),
            'email' => sanitize($_POST['email']),
            'first_name' => sanitize($_POST['first_name']),
            'last_name' => sanitize($_POST['last_name']),
            'phone' => sanitize($_POST['phone'] ?? '')
        ];
        
        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
        }
        
        if ($userModel->update($id, $data)) {
            $success = 'User updated successfully.';
        } else {
            $error = 'Failed to update user.';
        }
    }
    
    if ($action === 'toggle_user_status') {
        requireRole('administrator');
        
        $id = (int)$_POST['user_id'];
        $user = $userModel->findById($id);
        
        if ($user) {
            if ($user['is_active']) {
                $userModel->deactivate($id);
                $success = 'User deactivated.';
            } else {
                $userModel->activate($id);
                $success = 'User activated.';
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    redirect('auth/login');
}
