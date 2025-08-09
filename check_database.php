<?php
header('Content-Type: text/html; charset=utf-8');

echo "<h1>Database Structure Check</h1>";

include 'db.php';

if (!$conn) {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
    exit;
}

echo "<p style='color: green;'>✅ Database connected successfully</p>";

// Check if users table exists
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows == 0) {
    echo "<p style='color: red;'>❌ Users table does not exist</p>";
    echo "<p>Please run the database setup script first.</p>";
    exit;
}

echo "<p style='color: green;'>✅ Users table exists</p>";

// Check if verification_code column exists
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'verification_code'");
if ($result->num_rows == 0) {
    echo "<p style='color: orange;'>⚠️ verification_code column missing</p>";
    
    // Add the missing column
    $sql = "ALTER TABLE users ADD COLUMN verification_code VARCHAR(10) NULL AFTER role";
    if ($conn->query($sql)) {
        echo "<p style='color: green;'>✅ Added verification_code column</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to add verification_code column: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color: green;'>✅ verification_code column exists</p>";
}

// Check if status column exists
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'status'");
if ($result->num_rows == 0) {
    echo "<p style='color: orange;'>⚠️ status column missing</p>";
    
    // Add the missing column
    $sql = "ALTER TABLE users ADD COLUMN status ENUM('pending', 'active') DEFAULT 'pending' AFTER verification_code";
    if ($conn->query($sql)) {
        echo "<p style='color: green;'>✅ Added status column</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to add status column: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color: green;'>✅ status column exists</p>";
}

// Show current table structure
echo "<h2>Current Users Table Structure:</h2>";
$result = $conn->query("DESCRIBE users");
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . $row['Default'] . "</td>";
    echo "<td>" . $row['Extra'] . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>Test Email Verification:</h2>";
echo "<form method='post' action='test_email_sending.php'>";
echo "<input type='email' name='test_email' placeholder='Enter your email' required>";
echo "<input type='submit' value='Send Test Email'>";
echo "</form>";

$conn->close();
?> 