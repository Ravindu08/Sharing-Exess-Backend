<?php
//donate form
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
$expiry_date = $data['expiry_date'] ?? '';
$location = $data['location'] ?? '';
$description = $data['description'] ?? '';
$contact_phone = $data['contact_phone'] ?? '';
$contact_email = $data['contact_email'] ?? '';

if (!$donor_id || !$food_name || !$quantity || !$expiry_date || !$location) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Validate expiry_date (must be today or future)
$today = date('Y-m-d');
if (strtotime($expiry_date) < strtotime($today)) {
    echo json_encode(['success' => false, 'message' => 'Expiry date must be today or a future date']);
    exit;
}

// Validate phone (must be 10 digits if provided)
if ($contact_phone && !preg_match('/^\d{10}$/', $contact_phone)) {
    echo json_encode(['success' => false, 'message' => 'Phone number must be exactly 10 digits']);
    exit;
}

// Validate email (if provided)
if ($contact_email && !filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
