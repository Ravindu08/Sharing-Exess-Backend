<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

echo "<h2>PHPMailer Installation Guide</h2>";

// Check if PHPMailer files exist
$phpmailerPath = 'phpmailer/src/';
$requiredFiles = [
    'Exception.php',
    'PHPMailer.php', 
    'SMTP.php'
];

echo "<h3>Checking PHPMailer Files:</h3>";
$allFilesExist = true;

foreach ($requiredFiles as $file) {
    $filePath = $phpmailerPath . $file;
    if (file_exists($filePath)) {
        echo "✅ {$file} - Found<br>";
    } else {
        echo "❌ {$file} - Missing<br>";
        $allFilesExist = false;
    }
}

if (!$allFilesExist) {
    echo "<h3>📥 How to Install PHPMailer:</h3>";
    echo "<ol>";
    echo "<li>Download PHPMailer from: <a href='https://github.com/PHPMailer/PHPMailer' target='_blank'>https://github.com/PHPMailer/PHPMailer</a></li>";
    echo "<li>Extract the downloaded ZIP file</li>";
    echo "<li>Copy the 'src' folder to: <code>backend/phpmailer/</code></li>";
    echo "<li>Your folder structure should look like:</li>";
    echo "<ul>";
    echo "<li>backend/phpmailer/src/Exception.php</li>";
    echo "<li>backend/phpmailer/src/PHPMailer.php</li>";
    echo "<li>backend/phpmailer/src/SMTP.php</li>";
    echo "</ul>";
    echo "</ol>";
} else {
    echo "<h3>✅ PHPMailer is properly installed!</h3>";
    echo "<p>You can now test the email functionality.</p>";
}

echo "<h3>🔧 Gmail App Password Setup:</h3>";
echo "<ol>";
echo "<li>Go to your Google Account settings</li>";
echo "<li>Enable 2-Step Verification if not already enabled</li>";
echo "<li>Go to Security → App passwords</li>";
echo "<li>Generate a new app password for 'Mail'</li>";
echo "<li>Use this app password in your Mailer.php file</li>";
echo "</ol>";

echo "<h3>🧪 Test Email Functionality:</h3>";
echo "<p>Visit: <a href='test_email.php' target='_blank'>test_email.php</a></p>";

echo "<h3>📧 Current Email Configuration:</h3>";
echo "<ul>";
echo "<li><strong>SMTP Host:</strong> smtp.gmail.com</li>";
echo "<li><strong>SMTP Port:</strong> 465</li>";
echo "<li><strong>Security:</strong> SSL</li>";
echo "<li><strong>Username:</strong> muralitharanabinath7@gmail.com</li>";
echo "<li><strong>Password:</strong> [App Password]</li>";
echo "</ul>";

echo "<h3>🔍 Troubleshooting:</h3>";
echo "<ul>";
echo "<li>Check if your Gmail app password is correct</li>";
echo "<li>Ensure 2-Step Verification is enabled</li>";
echo "<li>Check if your Gmail account allows 'less secure app access' (if not using app password)</li>";
echo "<li>Verify that your XAMPP PHP has OpenSSL enabled</li>";
echo "</ul>";
?> 