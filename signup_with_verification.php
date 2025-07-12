<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include 'db.php';
include 'Mailer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $name = $data['name'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $role = $data['role'] ?? 'recipient';
    
    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    // Check if email already exists
    $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit;
    }
    
    // Generate verification code
    $verificationCode = sprintf("%06d", mt_rand(100000, 999999));
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user with verification code
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, verification_code, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->bind_param("sssss", $name, $email, $hashedPassword, $role, $verificationCode);
    
    if ($stmt->execute()) {
        $userId = $conn->insert_id;
        
        // Send verification email
        $mailer = new Mailer();
        
        $subject = "Email Verification - Sharing Excess";
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .verification-code { background: #4CAF50; color: white; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🍽️ Sharing Excess</h1>
                    <p>Email Verification Required</p>
                </div>
                <div class='content'>
                    <h2>Hello $name!</h2>
                    <p>Thank you for registering with Sharing Excess. To complete your registration, please verify your email address.</p>
                    
                    <div class='verification-code'>
                        $verificationCode
                    </div>
                    
                    <p>Enter this verification code in the app to activate your account.</p>
                    
                    <p>If you didn't create this account, please ignore this email.</p>
                    
                    <p>Best regards,<br>The Sharing Excess Team</p>
                </div>
                <div class='footer'>
                    <p>This is an automated email from Sharing Excess platform</p>
                </div>
            </div>
        </body>
        </html>";
        
        $mailer->setInfo($email, $subject, $message);
        
        if ($mailer->send()) {
            echo json_encode([
                'success' => true, 
                'message' => 'Registration successful! Please check your email for verification code.',
                'user_id' => $userId,
                'email' => $email
            ]);
        } else {
            // If email fails, still create user but inform about email issue
            echo json_encode([
                'success' => true, 
                'message' => 'Registration successful! However, verification email could not be sent. Please contact support.',
                'user_id' => $userId,
                'email' => $email,
                'verification_code' => $verificationCode // For testing purposes
            ]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?> 