<?php
require_once __DIR__ . '/db.php';

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Define a sample user (replace with real values as needed)
$user = [
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => hashPassword('admin123'),
    'role' => 'admin',
    'status' => 'active'
];

$stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
$stmt->bind_param('s', $user['email']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Update
    $stmt2 = $conn->prepare('UPDATE users SET role=?, password=?, name=?, status=? WHERE email=?');
    $stmt2->bind_param('sssss', $user['role'], $user['password'], $user['name'], $user['status'], $user['email']);
    $stmt2->execute();
} else {
    // Insert
    $stmt2 = $conn->prepare('INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)');
    $stmt2->bind_param('sssss', $user['name'], $user['email'], $user['password'], $user['role'], $user['status']);
    $stmt2->execute();
}

echo 'Admin and officer user fixed.';

