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

// Get the verification code for the test user
$email = 'test@example.com';
$stmt = $conn->prepare('SELECT verification_code FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

$user = $result->fetch_assoc();
$code = $user['verification_code'];

// Verify the user
$stmt = $conn->prepare('UPDATE users SET status = "active", verification_code = NULL WHERE email = ? AND verification_code = ?');
$stmt->bind_param('ss', $email, $code);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    // Get user data
    $stmt = $conn->prepare('SELECT id, name, email, role FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true, 
        'message' => 'User verified successfully',
        'user' => $userData
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Verification failed']);
}
?> 