<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle OPTIONS preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$code = $data['code'] ?? '';
$new_password = $data['new_password'] ?? '';

if (!$email || !$code || !$new_password) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

// Check if user and code match
$stmt = $conn->prepare('SELECT id FROM users WHERE email = ? AND verification_code = ?');
$stmt->bind_param('ss', $email, $code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid code or email.']);
    exit;
}

// Hash the new password
$hashed = password_hash($new_password, PASSWORD_DEFAULT);

// Update password and clear verification code
$stmt = $conn->prepare('UPDATE users SET password = ?, verification_code = NULL WHERE email = ?');
$stmt->bind_param('ss', $hashed, $email);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to reset password.']);
}
?>
