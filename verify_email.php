<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $email = $data['email'] ?? '';
    $verificationCode = $data['verification_code'] ?? '';
    
    if (empty($email) || empty($verificationCode)) {
        echo json_encode(['success' => false, 'message' => 'Email and verification code are required']);
        exit;
    }
    
    // Check if user exists and code matches
    $stmt = $conn->prepare("SELECT id, name, role, status FROM users WHERE email = ? AND verification_code = ?");
    $stmt->bind_param("ss", $email, $verificationCode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if ($user['status'] === 'active') {
            echo json_encode(['success' => false, 'message' => 'Account is already verified']);
            exit;
        }
        
        // Activate the account
        $updateStmt = $conn->prepare("UPDATE users SET status = 'active', verification_code = NULL WHERE email = ?");
        $updateStmt->bind_param("s", $email);
        
        if ($updateStmt->execute()) {
            echo json_encode([
                'success' => true, 
                'message' => 'Email verified successfully! You can now login.',
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $email,
                    'role' => $user['role']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to activate account']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email or verification code']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?> 