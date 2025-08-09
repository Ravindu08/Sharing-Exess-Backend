<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

include 'db.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data || !isset($data['listing_id']) || !isset($data['updates']) || !is_array($data['updates'])) {
  echo json_encode(['success' => false, 'message' => 'Invalid payload']);
  exit;
}

$listing_id = (int)$data['listing_id'];
$updates = $data['updates'];
// Map frontend field names to schema columns
if (isset($updates['title'])) {
  $updates['food_name'] = $updates['title'];
  unset($updates['title']);
}

// Whitelist fields (schema uses food_name)
$allowed = ['food_name', 'description', 'quantity', 'status'];
$setClauses = [];
$params = [];
$types = '';

foreach ($allowed as $field) {
  if (array_key_exists($field, $updates)) {
    $setClauses[] = "$field = ?";
    $val = $updates[$field];
    // quantity is a VARCHAR in schema, bind as string
    $types .= 's';
    $params[] = (string)$val;
  }
}

if (empty($setClauses)) {
  echo json_encode(['success' => false, 'message' => 'No allowed fields to update']);
  exit;
}

$sql = 'UPDATE food_listings SET ' . implode(', ', $setClauses) . ' WHERE id = ?';
$types .= 'i';
$params[] = $listing_id;

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
  echo json_encode(['success' => true, 'message' => 'Listing updated']);
} else {
  echo json_encode(['success' => false, 'message' => 'Update failed']);
}
