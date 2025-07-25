<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
require_once __DIR__ . '/db.php';
$recipient_id = $_GET['recipient_id'] ?? null;
if ($recipient_id) {
    $stmt = $conn->prepare('SELECT id, food_name, quantity, needed_by, location, created_at, status FROM food_requests WHERE recipient_id = ? ORDER BY created_at DESC');
    $stmt->bind_param('i', $recipient_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query('SELECT id, food_name, quantity, needed_by, location, created_at, recipient_id, status FROM food_requests ORDER BY created_at DESC');
}
$requests = [];
while ($row = $result->fetch_assoc()) {
    $requests[] = $row;
}
echo json_encode(['success' => true, 'requests' => $requests]); 