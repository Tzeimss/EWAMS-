<?php

class User {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function findByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        return $stmt->fetch();
    }
    
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO users (username, email, password_hash, role, first_name, last_name, phone, student_id, program_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['username'] ?? null,
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['role'],
            $data['first_name'],
            $data['last_name'],
            $data['phone'] ?? null,
            $data['student_id'] ?? null,
            $data['program_id'] ?? null
        ]);
    }
    
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        foreach (['email', 'first_name', 'last_name', 'phone', 'is_active', 'student_id', 'program_id'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }
        
        if (!empty($data['password'])) {
            $fields[] = "password_hash = ?";
            $values[] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        
        $values[] = $id;
        $stmt = $this->db->prepare("UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?");
        return $stmt->execute($values);
    }
    
    public function getAll($role = null, $active = null) {
        $sql = "SELECT * FROM users WHERE 1=1";
        $params = [];
        
        if ($role) {
            $sql .= " AND role = ?";
            $params[] = $role;
        }
        if ($active !== null) {
            $sql .= " AND is_active = ?";
            $params[] = $active;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getStudents() {
        return $this->getAll('student');
    }
    
    public function getFaculty() {
        return $this->getAll('faculty');
    }
    
    public function getAdvisors() {
        return $this->getAll('advisor');
    }
    
    public function deactivate($id) {
        $stmt = $this->db->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function activate($id) {
        $stmt = $this->db->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
