<?php

require_once __DIR__ . '/config/config.php';

$db = getDB();

echo "Seeding users with correct password hashes...\n";

$passwordHash = password_hash('Password123', PASSWORD_BCRYPT);

$users = [
    ['admin', 'admin@edu.com', 'System', 'Administrator', 'administrator'],
    ['jsmith', 'john.smith@edu.com', 'John', 'Smith', 'faculty'],
    ['sjohnson', 'sarah.johnson@edu.com', 'Sarah', 'Johnson', 'advisor'],
    ['mwilliams', 'mike.williams@edu.com', 'Mike', 'Williams', 'student'],
    ['ebrown', 'emily.brown@edu.com', 'Emily', 'Brown', 'student'],
    ['djones', 'david.jones@edu.com', 'David', 'Jones', 'student'],
    ['jdavis', 'jennifer.davis@edu.com', 'Jennifer', 'Davis', 'student'],
    ['cmiller', 'chris.miller@edu.com', 'Chris', 'Miller', 'student'],
];

$stmt = $db->prepare("DELETE FROM users WHERE email IN (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(array_column($users, 1));

$stmt = $db->prepare("INSERT INTO users (username, email, password_hash, first_name, last_name, role, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)");

foreach ($users as $user) {
    $stmt->execute([$user[0], $user[1], $passwordHash, $user[2], $user[3], $user[4]]);
    echo "Created user: {$user[0]} ({$user[4]})\n";
}

echo "\nDone! All users created with password: Password123\n";
