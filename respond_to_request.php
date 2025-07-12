<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$request_id = $data['request_id'] ?? '';
$status = $data['status'] ?? ''; // 'accepted' or 'declined'

if (!$request_id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

if (!in_array($status, ['accepted', 'declined'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

try {
    // Update the request status
    $stmt = $conn->prepare('UPDATE food_requests SET status = ?, updated_at = NOW() WHERE id = ?');
    $stmt->bind_param('si', $status, $request_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'success' => true, 
                'message' => 'Request ' . $status . ' successfully'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Request not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update request']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 