<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$donor_id = $data['donor_id'] ?? null;
$food_name = $data['food_name'] ?? '';
$quantity = $data['quantity'] ?? '';
$expiry_date = $data['expiry_date'] ?? '';
$location = $data['location'] ?? '';
$description = $data['description'] ?? '';
$contact_phone = $data['contact_phone'] ?? '';
$contact_email = $data['contact_email'] ?? '';

if (!$donor_id || !$food_name || !$quantity || !$expiry_date || !$location) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$stmt = $conn->prepare('INSERT INTO food_listings (donor_id, food_name, quantity, expiry_date, location) VALUES (?, ?, ?, ?, ?)');
$stmt->bind_param('issss', $donor_id, $food_name, $quantity, $expiry_date, $location);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Listing added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add listing']);
}
?>
