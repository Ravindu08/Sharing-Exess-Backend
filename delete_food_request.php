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

// Fetch listing_id before deleting the request
$get_listing = $conn->prepare('SELECT listing_id FROM food_requests WHERE id = ? AND recipient_id = ?');
$get_listing->bind_param('ii', $request_id, $recipient_id);
$get_listing->execute();
$result = $get_listing->get_result();
$listing_id = null;
if ($row = $result->fetch_assoc()) {
    $listing_id = $row['listing_id'];
}

// Only allow delete if the request belongs to the recipient
$stmt = $conn->prepare('DELETE FROM food_requests WHERE id = ? AND recipient_id = ?');
$stmt->bind_param('ii', $request_id, $recipient_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    if ($listing_id) {
        // Set food listing as available and clear requested_by
        $update = $conn->prepare('UPDATE food_listings SET status = ?, requested_by = NULL WHERE id = ?');
        $status = 'available';
        $update->bind_param('si', $status, $listing_id);
        $update->execute();
    }
    echo json_encode(['success' => true, 'message' => 'Request deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete request or not authorized']);
}
?>
