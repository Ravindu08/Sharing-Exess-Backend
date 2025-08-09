<?php
header('Content-Type: text/plain');

include 'db.php';

// Check if food_listings table exists
$result = $conn->query("SHOW TABLES LIKE 'food_listings'");
if ($result->num_rows === 0) {
    die("Error: food_listings table does not exist\n");
}

// Get table structure
$result = $conn->query("DESCRIBE food_listings");
if (!$result) {
    die("Error describing table: " . $conn->error . "\n");
}

echo "food_listings table structure:\n";
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
$result = $conn->query("SHOW COLUMNS FROM food_listings LIKE 'accepted_by'");
if ($result->num_rows === 0) {
    echo "\nWARNING: 'accepted_by' column does not exist in food_listings table\n";
    echo "To fix this, run:\n";
    echo "ALTER TABLE food_listings ADD COLUMN accepted_by VARCHAR(100) DEFAULT NULL;\n";
} else {
    echo "\n'accepted_by' column exists in food_listings table\n";
}

$conn->close();
?>
