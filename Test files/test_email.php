<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Test email functionality
include 'Mailer.php';

// Test email configuration
$testEmail = 'muralitharanabinath7@gmail.com'; // Your email for testing
$testSubject = "Test Email - Sharing Excess";
$testMessage = "
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .test-code { background: #4CAF50; color: white; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🍽️ Sharing Excess</h1>
            <p>Email Test</p>
        </div>
        <div class='content'>
            <h2>Email Test Successful!</h2>
            <p>This is a test email to verify that the email functionality is working properly.</p>
            
            <div class='test-code'>
                TEST123
            </div>
            
            <p>If you received this email, the email verification system is working correctly.</p>
            
            <p>Best regards,<br>The Sharing Excess Team</p>
        </div>
    </div>
</body>
</html>";

try {
    $mailer = new Mailer();
    $mailer->setInfo($testEmail, $testSubject, $testMessage);
    
    if ($mailer->send()) {
        echo json_encode([
            'success' => true,
            'message' => 'Test email sent successfully! Check your inbox.',
            'test_email' => $testEmail
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to send test email. Check PHPMailer configuration.',
            'error' => 'Mailer send() returned false'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Exception occurred while sending email',
        'error' => $e->getMessage()
    ]);
}
?> 