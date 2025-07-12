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
$listing_id = $data['listing_id'] ?? '';
$food_name = $data['food_name'] ?? '';
$quantity = $data['quantity'] ?? '';
$needed_by = $data['needed_by'] ?? null;
$location = $data['location'] ?? '';

if (!$listing_id || !$food_name || !$quantity) {
    echo json_encode(['success' => false, 'message' => 'Missing fields']);
    exit;
}

// For demo purposes, use a default recipient_id (you would get this from session in real app)
$recipient_id = 2; // Default recipient ID

$stmt = $conn->prepare('INSERT INTO food_requests (recipient_id, food_name, quantity, needed_by, location) VALUES (?, ?, ?, ?, ?)');
$stmt->bind_param('issss', $recipient_id, $food_name, $quantity, $needed_by, $location);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Food request submitted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit request']);
}
?> 