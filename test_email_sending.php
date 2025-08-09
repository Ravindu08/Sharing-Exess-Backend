<?php
header('Content-Type: text/html; charset=utf-8');

echo "<h1>Email Verification Test</h1>";

// Test PHPMailer installation
try {
    include 'Mailer.php';
    $mailer = new Mailer();
    echo "<p style='color: green;'>✅ PHPMailer loaded successfully!</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ PHPMailer error: " . $e->getMessage() . "</p>";
    exit;
}

// Test email sending
if (isset($_POST['test_email'])) {
    $testEmail = $_POST['test_email'];
    
    try {
        $mailer = new Mailer();
        
        $subject = "Test Email - Sharing Excess";
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🍽️ Sharing Excess</h1>
                    <p>Test Email</p>
                </div>
                <div class='content'>
                    <h2>Hello!</h2>
                    <p>This is a test email to verify that PHPMailer is working correctly.</p>
                    <p>If you received this email, the email verification system is ready!</p>
                    <p>Best regards,<br>The Sharing Excess Team</p>
                </div>
                <div class='footer'>
                    <p>This is a test email from Sharing Excess platform</p>
                </div>
            </div>
        </body>
        </html>";
        
        $mailer->setInfo($testEmail, $subject, $message);
        
        if ($mailer->send()) {
            echo "<p style='color: green;'>✅ Test email sent successfully to $testEmail!</p>";
            echo "<p>Check your email (including spam folder) for the test message.</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to send test email.</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error sending email: " . $e->getMessage() . "</p>";
    }
}

?>

<form method="POST" style="margin: 20px 0; padding: 20px; border: 1px solid #ccc; border-radius: 5px;">
    <h3>Test Email Sending</h3>
    <p>Enter your email address to test if PHPMailer is working:</p>
    <input type="email" name="test_email" placeholder="your-email@gmail.com" required style="width: 300px; padding: 10px; margin: 10px 0;">
    <br>
    <button type="submit" style="background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
        Send Test Email
    </button>
</form>

<h2>Next Steps:</h2>
<ol>
    <li>Enter your Gmail address above and click "Send Test Email"</li>
    <li>Check your email (including spam folder) for the test message</li>
    <li>If you receive the email, the verification system is working!</li>
    <li>Try signing up in your React app with the same email</li>
</ol>

<p><strong>Note:</strong> Make sure XAMPP is running and you're accessing this through <code>http://localhost/Sharing%20Excess/backend/test_email_sending.php</code></p> 