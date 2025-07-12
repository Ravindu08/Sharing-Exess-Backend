<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include 'db.php';

try {
    // First, let's add a status column if it doesn't exist
    $conn->query("ALTER TABLE food_requests ADD COLUMN IF NOT EXISTS status ENUM('pending', 'accepted', 'declined') DEFAULT 'pending'");
    
    // Get all food requests with recipient information
    $query = "
        SELECT 
            fr.id,
            fr.food_name,
            fr.quantity,
            fr.needed_by,
            fr.location,
            COALESCE(fr.status, 'pending') as status,
            fr.created_at,
            u.name as recipient_name,
            u.email as recipient_email
        FROM food_requests fr
        JOIN users u ON fr.recipient_id = u.id
        ORDER BY fr.created_at DESC
    ";
    
    $result = $conn->query($query);
    
    if ($result) {
        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $requests[] = [
                'id' => $row['id'],
                'food_item' => $row['food_name'], // Keep the frontend field name
                'quantity' => $row['quantity'],
                'needed_by' => $row['needed_by'],
                'location' => $row['location'],
                'status' => $row['status'],
                'created_at' => $row['created_at'],
                'recipient_name' => $row['recipient_name'],
                'recipient_email' => $row['recipient_email']
            ];
        }
        
        echo json_encode([
            'success' => true,
            'requests' => $requests
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to fetch requests'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 