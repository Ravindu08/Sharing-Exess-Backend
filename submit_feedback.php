<?php
header('Content-Type: application/json');
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$request_id = isset($data['request_id']) ? intval($data['request_id']) : 0;
$recipient_id = isset($data['recipient_id']) ? intval($data['recipient_id']) : 0;
$rating = null;
$comment = isset($data['comment']) ? trim($data['comment']) : '';

if (!$request_id || !$recipient_id) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Check if request is delivered
$stmt = $conn->prepare('SELECT status FROM food_requests WHERE id = ? AND recipient_id = ?');
$stmt->bind_param('ii', $request_id, $recipient_id);
$stmt->execute();
$res = $stmt->get_result();
if (!($row = $res->fetch_assoc()) || $row['status'] !== 'delivered') {
    echo json_encode(['success' => false, 'message' => 'Feedback allowed only for delivered requests']);
    exit;
}
// Check if feedback already exists
$stmt2 = $conn->prepare('SELECT id FROM feedback WHERE request_id = ? AND recipient_id = ?');
$stmt2->bind_param('ii', $request_id, $recipient_id);
$stmt2->execute();
$res2 = $stmt2->get_result();
if ($res2->fetch_assoc()) {
    echo json_encode(['success' => false, 'message' => 'Feedback already submitted']);
    exit;
}
// Insert feedback (no rating)
$stmt3 = $conn->prepare('INSERT INTO feedback (request_id, recipient_id, comment) VALUES (?, ?, ?)');
$stmt3->bind_param('iis', $request_id, $recipient_id, $comment);
if ($stmt3->execute()) {
    echo json_encode(['success' => true, 'message' => 'Feedback submitted']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit feedback']);
}
?>
