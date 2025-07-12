<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include 'db.php';

// Test login data
$email = 'test@example.com';
$password = 'password123';
$role = 'donor';

// Check if user exists and is active
$stmt = $conn->prepare('SELECT id, name, email, password, role FROM users WHERE email = ? AND status = "active"');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'User not found or not verified']);
    exit;
}

$user = $result->fetch_assoc();

// Verify password
if (password_verify($password, $user['password'])) {
    // Check if role matches
    if ($user['role'] === $role) {
        echo json_encode([
            'success' => true, 
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid role']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid password']);
}
?> 