<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

include 'db.php';

// Create a recipient user
$recipientData = [
    'name' => 'Hope Foundation',
    'email' => 'hope@foundation.com',
    'password' => 'password123',
    'role' => 'recipient'
];

try {
    // Check if recipient already exists
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->bind_param('s', $recipientData['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Recipient user already exists',
            'email' => $recipientData['email']
        ]);
        exit;
    }
    
    // Create recipient user
    $hashed = password_hash($recipientData['password'], PASSWORD_DEFAULT);
    $code = rand(100000, 999999);
    
    $stmt = $conn->prepare('INSERT INTO users (name, email, password, role, status, verification_code) VALUES (?, ?, ?, ?, "active", ?)');
    $stmt->bind_param('sssss', 
        $recipientData['name'],
        $recipientData['email'],
        $hashed,
        $recipientData['role'],
        $code
    );
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Recipient user created successfully',
            'user' => [
                'name' => $recipientData['name'],
                'email' => $recipientData['email'],
                'role' => $recipientData['role']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create recipient user',
            'error' => $conn->error
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 