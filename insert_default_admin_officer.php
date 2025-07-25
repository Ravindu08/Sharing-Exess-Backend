<?php
require_once __DIR__ . '/db.php';

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

$users = [
    [
        'name' => 'Admin User',
        'email' => 'admin@sharingexcess.com',
        'password' => hashPassword('admin123'),
        'role' => 'admin',
        'status' => 'active'
    ],
    [
        'name' => 'Officer User',
        'email' => 'officer@sharingexcess.com',
        'password' => hashPassword('officer123'),
        'role' => 'officer',
        'status' => 'active'
    ]
];

foreach ($users as $user) {
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->bind_param('s', $user['email']);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 0) {
        $stmt = $conn->prepare('INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $user['name'], $user['email'], $user['password'], $user['role'], $user['status']);
        $stmt->execute();
    }
}
echo 'Default admin and officer inserted.';
