<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

// Get all accepted food requests (can be filtered by user if needed)
$sql = "SELECT id, food_name, needed_by, location, status FROM food_requests WHERE status = 'accepted'";
$result = $conn->query($sql);
$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id' => $row['id'],
        'title' => $row['food_name'],
        'date' => $row['needed_by'],
        'location' => $row['location'],
        'status' => $row['status']
    ];
}
echo json_encode(['success' => true, 'events' => $events]); 