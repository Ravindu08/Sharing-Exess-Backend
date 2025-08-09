<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

echo "Testing backend...\n";

// Test database connection
try {
    include 'db.php';
    if ($conn) {
        echo "Database connection: OK\n";
    } else {
        echo "Database connection: FAILED\n";
    }
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

// Test Mailer class
try {
    include 'Mailer.php';
    $mailer = new Mailer();
    echo "Mailer class: OK\n";
} catch (Exception $e) {
    echo "Mailer error: " . $e->getMessage() . "\n";
}

// Test JSON response
$testData = [
    'success' => true,
    'message' => 'Backend is working correctly',
    'timestamp' => date('Y-m-d H:i:s')
];

echo json_encode($testData);
?> 