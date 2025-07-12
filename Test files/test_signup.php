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

// Test data
$testData = [
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => 'password123',
    'role' => 'donor'
];

// Check if user exists
$stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
$stmt->bind_param('s', $testData['email']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode([
        'success' => false, 
        'message' => 'Test user already exists',
        'email' => $testData['email']
    ]);
    exit;
}

$hashed = password_hash($testData['password'], PASSWORD_DEFAULT);
$code = rand(100000, 999999);

$stmt = $conn->prepare('INSERT INTO users (name, email, password, role, status, verification_code) VALUES (?, ?, ?, ?, "pending", ?)');
$stmt->bind_param('sssss', $testData['name'], $testData['email'], $hashed, $testData['role'], $code);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Test user created successfully',
        'user' => [
            'name' => $testData['name'],
            'email' => $testData['email'],
            'role' => $testData['role'],
            'verification_code' => $code
        ]
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to create test user',
        'error' => $conn->error
    ]);
}
?> 