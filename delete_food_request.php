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
$request_id = $data['request_id'] ?? null;
$recipient_id = $data['recipient_id'] ?? null;

if (!$request_id || !$recipient_id) {
    echo json_encode(['success' => false, 'message' => 'Missing request_id or recipient_id']);
    exit;
}

// Only allow delete if the request belongs to the recipient
$stmt = $conn->prepare('DELETE FROM food_requests WHERE id = ? AND recipient_id = ?');
$stmt->bind_param('ii', $request_id, $recipient_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Request deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete request or not authorized']);
}
?>
