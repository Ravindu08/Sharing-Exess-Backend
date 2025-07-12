<?php
header('Content-Type: application/json');
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$donor_id = $data['donor_id'] ?? '';
$food_name = $data['food_name'] ?? '';
$quantity = $data['quantity'] ?? '';
$expiry_date = $data['expiry_date'] ?? null;
$location = $data['location'] ?? '';

if (!$donor_id || !$food_name || !$quantity) {
    echo json_encode(['success' => false, 'message' => 'Missing fields']);
    exit;
}

$stmt = $conn->prepare('INSERT INTO food_listings (donor_id, food_name, quantity, expiry_date, location) VALUES (?, ?, ?, ?, ?)');
$stmt->bind_param('issss', $donor_id, $food_name, $quantity, $expiry_date, $location);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Food listed successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to list food']);
}
?> 