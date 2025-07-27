<?php
header('Content-Type: application/json');
include 'db.php';

$donor_id = isset($_GET['donor_id']) ? intval($_GET['donor_id']) : (isset($_POST['donor_id']) ? intval($_POST['donor_id']) : 0);
if (!$donor_id) {
    echo json_encode(['success' => false, 'message' => 'Missing donor_id']);
    exit;
}

$sql = 'SELECT f.id, f.food_name, f.quantity, f.expiry_date, f.location, f.created_at, f.status, f.accepted_by,
        (SELECT COUNT(*) FROM food_requests fr WHERE fr.listing_id = f.id) as total_requests
        FROM food_listings f WHERE f.donor_id = ? ORDER BY f.created_at DESC';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $donor_id);
$stmt->execute();
$result = $stmt->get_result();
$listings = [];
while ($row = $result->fetch_assoc()) {
    // Fetch accepted requests for this listing
    $requests = [];
    $stmt2 = $conn->prepare('SELECT id, food_name, quantity, needed_by, location, status, created_at, accepted_by, recipient_id FROM food_requests WHERE listing_id = ? AND status = "accepted"');
    $stmt2->bind_param('i', $row['id']);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    while ($req = $res2->fetch_assoc()) {
        $requests[] = $req;
    }
    $row['requests'] = $requests;
    $listings[] = $row;
}
echo json_encode(['success' => true, 'donations' => $listings]);
