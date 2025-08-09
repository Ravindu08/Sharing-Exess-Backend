<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

include 'db.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data || !isset($data['request_id']) || !isset($data['updates']) || !is_array($data['updates'])) {
  echo json_encode(['success' => false, 'message' => 'Invalid payload']);
  exit;
}

$request_id = (int)$data['request_id'];
$updates = $data['updates'];

// Whitelist fields
$allowed = ['status', 'quantity', 'notes'];
$setClauses = [];
$params = [];
$types = '';

foreach ($allowed as $field) {
  if (array_key_exists($field, $updates)) {
    $setClauses[] = "$field = ?";
    $val = $updates[$field];
    if ($field === 'quantity') { $types .= 'i'; $params[] = (int)$val; }
    else { $types .= 's'; $params[] = (string)$val; }
  }
}

if (empty($setClauses)) {
  echo json_encode(['success' => false, 'message' => 'No allowed fields to update']);
  exit;
}

$sql = 'UPDATE food_requests SET ' . implode(', ', $setClauses) . ' WHERE id = ?';
$types .= 'i';
$params[] = $request_id;

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
  echo json_encode(['success' => true, 'message' => 'Request updated']);
} else {
  echo json_encode(['success' => false, 'message' => 'Update failed']);
}
