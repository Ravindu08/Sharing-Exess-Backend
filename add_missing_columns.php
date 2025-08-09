<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    include 'db.php';
    
    // Add missing columns to food_listings table
    $alterQueries = [
        "ALTER TABLE food_listings ADD COLUMN IF NOT EXISTS description TEXT",
        "ALTER TABLE food_listings ADD COLUMN IF NOT EXISTS contact_phone VARCHAR(20)",
        "ALTER TABLE food_listings ADD COLUMN IF NOT EXISTS contact_email VARCHAR(255)"
    ];
    
    $results = [];
    
    foreach ($alterQueries as $query) {
        if ($conn->query($query) === TRUE) {
            $results[] = "Successfully executed: " . $query;
        } else {
            // Check if column already exists (MySQL error 1060)
            if ($conn->errno == 1060) {
                $results[] = "Column already exists (skipped): " . $query;
            } else {
                $results[] = "Error executing: " . $query . " - " . $conn->error;
            }
        }
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Database migration completed',
        'details' => $results
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Migration failed: ' . $e->getMessage()
    ]);
}
?>
