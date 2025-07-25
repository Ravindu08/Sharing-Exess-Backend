<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/db.php';

$data = json_decode(file_get_contents('php://input'), true);

$donor_id = $data['donor_id'] ?? null;
$food_name = $data['food_name'] ?? '';
$quantity = $data['quantity'] ?? '';
$expiry_date = $data['expiry_date'] ?? null;
$location = $data['location'] ?? '';
$description = $data['description'] ?? '';
$contact_phone = $data['contact_phone'] ?? '';
$contact_email = $data['contact_email'] ?? '';

if (!$donor_id || !$food_name || !$quantity || !$expiry_date || !$location || !$contact_phone || !$contact_email) {
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled.']);
    exit;
}

$stmt = $conn->prepare('INSERT INTO food_listings (donor_id, food_name, quantity, expiry_date, location, description, contact_phone, contact_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->bind_param('isssssss', $donor_id, $food_name, $quantity, $expiry_date, $location, $description, $contact_phone, $contact_email);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Donation submitted successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
} 