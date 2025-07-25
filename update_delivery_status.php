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
$id = $data['id'] ?? null;
$status = $data['status'] ?? null;

$valid_statuses = ['pending', 'accepted', 'declined', 'picked_up', 'delivering', 'delivered'];

if (!$id || !$status || !in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$stmt = $conn->prepare('UPDATE food_requests SET status = ? WHERE id = ?');
$stmt->bind_param('si', $status, $id);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update status']);
}
?>
