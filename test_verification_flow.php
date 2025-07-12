<?php
header('Content-Type: text/html; charset=utf-8');

echo "<h1>Email Verification Flow Test</h1>";

// Test database connection
include 'db.php';
if ($conn) {
    echo "<p style='color: green;'>✅ Database connection successful</p>";
} else {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
    exit;
}

// Test Mailer class
include 'Mailer.php';
try {
    $mailer = new Mailer();
    echo "<p style='color: green;'>✅ Mailer class loaded successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Mailer class error: " . $e->getMessage() . "</p>";
}

// Check if required files exist
$requiredFiles = [
    'signup_with_verification.php',
    'verify_email.php', 
    'resend_verification.php',
    'Mailer.php',
    'db.php'
];

echo "<h2>File Check:</h2>";
foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $file exists</p>";
    } else {
        echo "<p style='color: red;'>❌ $file missing</p>";
    }
}

// Check database table structure
echo "<h2>Database Structure:</h2>";
$result = $conn->query("DESCRIBE users");
if ($result) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Could not check table structure</p>";
}

// Test endpoints
echo "<h2>Endpoint Test:</h2>";
echo "<p>To test the verification flow:</p>";
echo "<ol>";
echo "<li>Go to your React app (should be running on http://localhost:5173)</li>";
echo "<li>Try to sign up with a real Gmail address</li>";
echo "<li>Check if you receive the verification email</li>";
echo "<li>Enter the code in the verification modal</li>";
echo "</ol>";

echo "<h2>Manual Test:</h2>";
echo "<p>You can also test the backend directly:</p>";
echo "<ul>";
echo "<li><a href='test_signup.php'>Test Signup</a></li>";
echo "<li><a href='test_verify.php'>Test Verification</a></li>";
echo "<li><a href='test_resend.php'>Test Resend Code</a></li>";
echo "</ul>";
?> 