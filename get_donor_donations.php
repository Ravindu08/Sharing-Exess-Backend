<?php
header('Content-Type: application/json');
include 'db.php';

// Accept donor_id from JSON body, POST, or GET
$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['donor_id'])) {
    $donor_id = intval($data['donor_id']);
} else {
    $donor_id = isset($_GET['donor_id']) ? intval($_GET['donor_id']) : (isset($_POST['donor_id']) ? intval($_POST['donor_id']) : 0);
}
if (!$donor_id) {
    echo json_encode(['success' => false, 'message' => 'Missing donor_id']);
    exit;
}

// Get donor name for matching accepted_by in custom requests
$stmtUser = $conn->prepare('SELECT name FROM users WHERE id = ?');
$stmtUser->bind_param('i', $donor_id);
$stmtUser->execute();
$resUser = $stmtUser->get_result();
$donor_name = ($row = $resUser->fetch_assoc()) ? $row['name'] : '';

$listings = [];
// 1. Fetch all listings for this donor
$sql = 'SELECT f.id, f.food_name, f.quantity, f.expiry_date, f.location, f.created_at, f.status, f.accepted_by,
        (SELECT COUNT(*) FROM food_requests fr WHERE fr.listing_id = f.id) as total_requests
        FROM food_listings f WHERE f.donor_id = ? ORDER BY f.created_at DESC';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $donor_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    // Fetch accepted requests for this listing
    $requests = [];
    $stmt2 = $conn->prepare('SELECT fr.id, fr.food_name, fr.quantity, fr.needed_by, fr.location, fr.status, fr.created_at, fr.accepted_by, fr.recipient_id, fr.updated_at AS accepted_at, u.name AS recipient_name FROM food_requests fr LEFT JOIN users u ON fr.recipient_id = u.id WHERE fr.listing_id = ? AND fr.status = "accepted"');
    $stmt2->bind_param('i', $row['id']);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    while ($req = $res2->fetch_assoc()) {
        $requests[] = $req;
    }
    $row['requests'] = $requests;
    $listings[] = $row;
}

// 2. Fetch accepted custom requests (no listing_id) accepted by this donor
$custom_requests = [];
$stmt3 = $conn->prepare('SELECT fr.id, fr.food_name, fr.quantity, fr.needed_by, fr.location, fr.status, fr.created_at, fr.accepted_by, fr.recipient_id, fr.updated_at AS accepted_at, u.name AS recipient_name FROM food_requests fr LEFT JOIN users u ON fr.recipient_id = u.id WHERE (fr.listing_id IS NULL OR fr.listing_id = 0) AND fr.status = "accepted" AND fr.accepted_by = ?');
$stmt3->bind_param('s', $donor_name);
$stmt3->execute();
$res3 = $stmt3->get_result();
while ($req = $res3->fetch_assoc()) {
    $custom_requests[] = $req;
}
if (count($custom_requests) > 0) {
    $listings[] = [
        'id' => 'custom',
        'food_name' => 'Custom Requests',
        'quantity' => '',
        'expiry_date' => '',
        'location' => '',
        'created_at' => '',
        'status' => 'accepted',
        'accepted_by' => $donor_name,
        'total_requests' => count($custom_requests),
        'requests' => $custom_requests
    ];
}
echo json_encode(['success' => true, 'donations' => $listings]);
