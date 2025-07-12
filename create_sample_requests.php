<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

include 'db.php';

// Sample food requests data
$sampleRequests = [
    [
        'recipient_id' => 1, // Assuming we have a recipient user
        'food_name' => 'Rice',
        'quantity' => '10kg',
        'needed_by' => '2025-07-15',
        'location' => 'Colombo'
    ],
    [
        'recipient_id' => 1,
        'food_name' => 'Fresh Vegetables',
        'quantity' => '5kg',
        'needed_by' => '2025-07-13',
        'location' => 'Kandy'
    ],
    [
        'recipient_id' => 1,
        'food_name' => 'Bread Loaves',
        'quantity' => '20 pieces',
        'needed_by' => '2025-07-12',
        'location' => 'Galle'
    ],
    [
        'recipient_id' => 1,
        'food_name' => 'Milk',
        'quantity' => '5 liters',
        'needed_by' => '2025-07-14',
        'location' => 'Matara'
    ]
];

try {
    // Check if we have any users (recipients)
    $userCheck = $conn->query('SELECT id FROM users WHERE role = "recipient" LIMIT 1');
    if ($userCheck->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'No recipient users found. Please create a recipient user first.']);
        exit;
    }
    
    $recipientId = $userCheck->fetch_assoc()['id'];
    
    // Clear existing sample requests
    $conn->query('DELETE FROM food_requests WHERE recipient_id = ' . $recipientId);
    
    // Insert sample requests
    $stmt = $conn->prepare('INSERT INTO food_requests (recipient_id, food_name, quantity, needed_by, location, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
    
    $insertedCount = 0;
    foreach ($sampleRequests as $request) {
        $request['recipient_id'] = $recipientId; // Use the actual recipient ID
        $stmt->bind_param('issss', 
            $request['recipient_id'],
            $request['food_name'],
            $request['quantity'],
            $request['needed_by'],
            $request['location']
        );
        
        if ($stmt->execute()) {
            $insertedCount++;
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => "Created $insertedCount sample food requests",
        'count' => $insertedCount
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 