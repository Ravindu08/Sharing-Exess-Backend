<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

// Fetch all food requests for the calendar with specific statuses
$sql = "SELECT id, food_name, needed_by, location, status FROM food_requests WHERE status IN ('accepted', 'picked_up', 'delivering', 'delivered')";
$result = $conn->query($sql);
$events = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $events[] = [
            'id' => $row['id'],
            'title' => $row['food_name'],
            'date' => $row['needed_by'], // Ensure YYYY-MM-DD format
            'location' => $row['location'],
            'status' => $row['status']
        ];
    }
    echo json_encode(['success' => true, 'events' => $events]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database query failed.']);
}
?>
