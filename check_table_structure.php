<?php
header('Content-Type: text/plain');

include 'db.php';

// Check if food_requests table exists
$result = $conn->query("SHOW TABLES LIKE 'food_requests'");
if ($result->num_rows === 0) {
    die("Error: food_requests table does not exist\n");
}

// Get table structure
$result = $conn->query("DESCRIBE food_requests");
if (!$result) {
    die("Error describing table: " . $conn->error . "\n");
}

echo "food_requests table structure:\n";
echo str_repeat("-", 60) . "\n";
echo sprintf("%-20s %-15s %-10s %-10s\n", "Field", "Type", "Null", "Key");
echo str_repeat("-", 60) . "\n";

while ($row = $result->fetch_assoc()) {
    echo sprintf("%-20s %-15s %-10s %-10s\n", 
        $row['Field'], 
        $row['Type'],
        $row['Null'],
        $row['Key']
    );
}

// Check if accepted_by column exists
$result = $conn->query("SHOW COLUMNS FROM food_requests LIKE 'accepted_by'");
if ($result->num_rows === 0) {
    echo "\nWARNING: 'accepted_by' column does not exist in food_requests table\n";
} else {
    echo "\n'accepted_by' column exists in food_requests table\n";
}

// Check if updated_at column exists
$result = $conn->query("SHOW COLUMNS FROM food_requests LIKE 'updated_at'");
if ($result->num_rows === 0) {
    echo "WARNING: 'updated_at' column does not exist in food_requests table\n";
} else {
    echo "'updated_at' column exists in food_requests table\n";
}

// Check if status column exists
$result = $conn->query("SHOW COLUMNS FROM food_requests LIKE 'status'");
if ($result->num_rows === 0) {
    echo "WARNING: 'status' column does not exist in food_requests table\n";
} else {
    echo "'status' column exists in food_requests table\n";
}

$conn->close();
?>
