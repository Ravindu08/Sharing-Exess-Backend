<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

include 'db.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Get POST data and assign variables BEFORE any SQL
$data = json_decode(file_get_contents('php://input'), true);
$request_id = $data['request_id'] ?? '';
$status = $data['status'] ?? ''; // 'accepted' or 'declined'
$user_id = $data['user_id'] ?? null;
$user_name = $data['user_name'] ?? null;

if (!$request_id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

if (!in_array($status, ['accepted', 'declined'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

$stmt = $conn->prepare('UPDATE food_requests SET status = ?, accepted_by = ?, updated_at = NOW() WHERE id = ?');
$stmt->bind_param('ssi', $status, $user_name, $request_id);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'SQL error: ' . $stmt->error]);
    exit;
}

try {
    // Accept/decline request and set accepted_by
    if ($status === 'accepted' && $user_name) {
        // Update food_requests
        $stmt = $conn->prepare('UPDATE food_requests SET status = ?, accepted_by = ?, updated_at = NOW() WHERE id = ?');
        $stmt->bind_param('ssi', $status, $user_name, $request_id);
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'SQL error (food_requests): ' . $stmt->error]);
            exit;
        }
        // Get listing_id
        $get = $conn->prepare('SELECT listing_id FROM food_requests WHERE id = ?');
        $get->bind_param('i', $request_id);
        if (!$get->execute()) {
            echo json_encode(['success' => false, 'message' => 'SQL error (get listing_id): ' . $get->error]);
            exit;
        }
        $result = $get->get_result();
        $row = $result->fetch_assoc();
        if ($row && $row['listing_id']) {
            $listing_id = $row['listing_id'];
            // Update food_listings
            $update = $conn->prepare('UPDATE food_listings SET status = ?, accepted_by = ? WHERE id = ?');
            $update->bind_param('ssi', $status, $user_name, $listing_id);
            if (!$update->execute()) {
                echo json_encode(['success' => false, 'message' => 'SQL error (food_listings): ' . $update->error]);
                exit;
            }
        }
        echo json_encode(['success' => true, 'message' => 'Request accepted by ' . $user_name]);
        exit;
    } else if ($status === 'declined') {
        $stmt = $conn->prepare('UPDATE food_requests SET status = ?, updated_at = NOW() WHERE id = ?');
        $stmt->bind_param('si', $status, $request_id);
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'SQL error (decline): ' . $stmt->error]);
            exit;
        }
        echo json_encode(['success' => true, 'message' => 'Request declined']);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>