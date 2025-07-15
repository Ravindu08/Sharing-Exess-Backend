<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$code = $data['code'] ?? '';
$new_password = $data['new_password'] ?? '';

if (!$email || !$code || !$new_password) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

// Check code
$stmt = $conn->prepare('SELECT id FROM users WHERE email = ? AND verification_code = ?');
$stmt->bind_param('ss', $email, $code);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid code or email.']);
    exit;
}

// Update password and clear code
$hashed = password_hash($new_password, PASSWORD_DEFAULT);
$stmt = $conn->prepare('UPDATE users SET password = ?, verification_code = NULL WHERE email = ?');
$stmt->bind_param('ss', $hashed, $email);
$stmt->execute();

echo json_encode(['success' => true]); 