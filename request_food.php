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

$data = json_decode(file_get_contents('php://input'), true);
$recipient_id = $data['recipient_id'] ?? '';
$listing_id = $data['listing_id'] ?? null;
$food_name = $data['food_name'] ?? '';
$quantity = $data['quantity'] ?? '';
$needed_by = $data['needed_by'] ?? null;
$location = $data['location'] ?? '';

if (!$recipient_id || !$food_name || !$quantity || !$location) {
    echo json_encode(['success' => false, 'message' => 'Missing fields']);
    exit;
}

if ($listing_id) {
    // Check if the food listing is already requested
    $check = $conn->prepare('SELECT status FROM food_listings WHERE id = ?');
    $check->bind_param('i', $listing_id);
    $check->execute();
    $result = $check->get_result();
    $row = $result->fetch_assoc();
    if ($row && isset($row['status']) && $row['status'] === 'requested') {
        echo json_encode(['success' => false, 'message' => 'This food has already been requested.']);
        exit;
    }
    // Insert request
    $stmt = $conn->prepare('INSERT INTO food_requests (recipient_id, listing_id, food_name, quantity, needed_by, location) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('iissss', $recipient_id, $listing_id, $food_name, $quantity, $needed_by, $location);
    // Mark food listing as requested
    $update = $conn->prepare('UPDATE food_listings SET status = ? WHERE id = ?');
    $statusVal = 'requested';
    $update->bind_param('si', $statusVal, $listing_id);
    $update->execute();
} else {
    // Custom request (no listing_id)
    $stmt = $conn->prepare('INSERT INTO food_requests (recipient_id, food_name, quantity, needed_by, location) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('issss', $recipient_id, $food_name, $quantity, $needed_by, $location);
}
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Food request submitted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit request']);
}
?>