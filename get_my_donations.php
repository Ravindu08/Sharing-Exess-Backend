<?php
header('Content-Type: application/json');
include 'db.php';

// Only allow POST or GET with donor_id or recipient_id
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : (isset($_POST['user_id']) ? intval($_POST['user_id']) : 0);
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Missing user_id']);
    exit;
}

// Find all food_requests accepted by this user (as donor or recipient)
$sql = 'SELECT fr.id, fr.food_name, fr.quantity, fr.status, fr.accepted_by, fr.updated_at, fr.location, fr.needed_by, fl.donor_id, u.name as donor_name
        FROM food_requests fr
        LEFT JOIN food_listings fl ON fr.listing_id = fl.id
        LEFT JOIN users u ON fl.donor_id = u.id
        WHERE fr.accepted_by = (SELECT name FROM users WHERE id = ?) 
        ORDER BY fr.updated_at DESC';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$donations = [];
while ($row = $result->fetch_assoc()) {
    $donations[] = $row;
}
echo json_encode(['success' => true, 'donations' => $donations]);
