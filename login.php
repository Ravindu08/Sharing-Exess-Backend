<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$role = $data['role'] ?? '';

// Remove role requirement for login
if (!$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Missing fields']);
    exit;
}

if ($email === 'admin@sharingexcess.com') {
    $stmt = $conn->prepare('SELECT id, email, password, role, name FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
} else {
    $stmt = $conn->prepare('SELECT id, email, password, role, name FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
}
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if ($user && password_verify($password, $user['password'])) {
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    echo json_encode(['success' => true, 'user' => [
        'id' => $user['id'],
        'email' => $user['email'],
        'role' => $user['role'],
        'name' => $user['name']
    ]]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
}
?> 