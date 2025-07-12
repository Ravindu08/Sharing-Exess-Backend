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

$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$role = $data['role'] ?? '';

if (!$name || !$email || !$password || !$role) {
    echo json_encode(['success' => false, 'message' => 'Missing fields']);
    exit;
}

// Check if user exists
$stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email already registered']);
    exit;
}

$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare('INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, "active")');
$stmt->bind_param('ssss', $name, $email, $hashed, $role);
if ($stmt->execute()) {
    $user_id = $conn->insert_id;
    echo json_encode([
        'success' => true, 
        'message' => 'Signup successful!',
        'user' => [
            'id' => $user_id,
            'email' => $email,
            'role' => $role,
            'name' => $name
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Signup failed']);
}
?> 