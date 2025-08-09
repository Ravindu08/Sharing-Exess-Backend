<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

include 'db.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (
  !$data ||
  !isset($data['listing_id']) ||
  !isset($data['user_id'])
) {
  echo json_encode(['success' => false, 'message' => 'Invalid payload']);
  exit;
}

$listing_id = (int)$data['listing_id'];
$user_id = (int)$data['user_id'];

// Check if user is officer
$user_stmt = $conn->prepare('SELECT role FROM users WHERE id = ?');
$user_stmt->bind_param('i', $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
if ($user_row = $user_result->fetch_assoc()) {
  if ($user_row['role'] !== 'officer') {
    echo json_encode(['success' => false, 'message' => 'Permission denied']);
    exit;
  }
} else {
  echo json_encode(['success' => false, 'message' => 'User not found']);
  exit;
}

$stmt = $conn->prepare('DELETE FROM food_listings WHERE id = ?');
$stmt->bind_param('i', $listing_id);

if ($stmt->execute()) {
  echo json_encode(['success' => true, 'message' => 'Listing deleted']);
} else {
  echo json_encode(['success' => false, 'message' => 'Delete failed']);
}
