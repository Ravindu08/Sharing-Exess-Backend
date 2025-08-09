<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

include 'db.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data || !isset($data['request_id'])) {
  echo json_encode(['success' => false, 'message' => 'Invalid payload']);
  exit;
}

$request_id = (int)$data['request_id'];

$stmt = $conn->prepare('DELETE FROM food_requests WHERE id = ?');
$stmt->bind_param('i', $request_id);

if ($stmt->execute()) {
  echo json_encode(['success' => true, 'message' => 'Request deleted']);
} else {
  echo json_encode(['success' => false, 'message' => 'Delete failed']);
}
