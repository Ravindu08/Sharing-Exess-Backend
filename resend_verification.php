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
    
    $email = $data['email'] ?? '';
    
    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Email is required']);
        exit;
    }
    
    // Check if user exists and is pending verification
    $stmt = $conn->prepare("SELECT id, name, role, status FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if ($user['status'] === 'active') {
            echo json_encode(['success' => false, 'message' => 'Account is already verified']);
            exit;
        }
        
        // Generate new verification code
        $verificationCode = sprintf("%06d", mt_rand(100000, 999999));
        
        // Update verification code
        $updateStmt = $conn->prepare("UPDATE users SET verification_code = ? WHERE email = ?");
        $updateStmt->bind_param("ss", $verificationCode, $email);
        
        if ($updateStmt->execute()) {
            // Send new verification email
            $mailer = new Mailer();
            
            $subject = "Email Verification - Sharing Excess (Resent)";
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
                        <p>Email Verification (Resent)</p>
                    </div>
                    <div class='content'>
                        <h2>Hello {$user['name']}!</h2>
                        <p>You requested a new verification code. Here's your new verification code:</p>
                        
                        <div class='verification-code'>
                            $verificationCode
                        </div>
                        
                        <p>Enter this verification code in the app to activate your account.</p>
                        
                        <p>If you didn't request this code, please ignore this email.</p>
                        
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
                    'message' => 'New verification code sent to your email.',
                    'email' => $email
                ]);
            } else {
                echo json_encode([
                    'success' => true, 
                    'message' => 'New verification code generated but email could not be sent. Please contact support.',
                    'email' => $email,
                    'verification_code' => $verificationCode // For testing purposes
                ]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update verification code']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Email not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?> 