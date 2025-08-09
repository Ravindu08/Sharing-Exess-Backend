<?php
header('Content-Type: application/json');
include 'db.php';

// Get donor_id from query parameter
$donor_id = isset($_GET['donor_id']) ? intval($_GET['donor_id']) : 0;

if (!$donor_id) {
    echo json_encode(['success' => false, 'message' => 'Please provide a donor_id']);
    exit;
}

// Check if donor exists
$stmt = $conn->prepare('SELECT id, name, email FROM users WHERE id = ?');
$stmt->bind_param('i', $donor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Donor not found']);
    exit;
}

$donor = $result->fetch_assoc();

// Get all food listings for this donor
$stmt = $conn->prepare('SELECT * FROM food_listings WHERE donor_id = ?');
$stmt->bind_param('i', $donor_id);
$stmt->execute();
$listings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get all accepted requests for this donor
$stmt = $conn->prepare('SELECT fr.* FROM food_requests fr 
    INNER JOIN food_listings fl ON fr.listing_id = fl.id 
    WHERE fl.donor_id = ? AND fr.status = "accepted"');
$stmt->bind_param('i', $donor_id);
$stmt->execute();
$accepted_requests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'success' => true,
    'donor' => $donor,
    'listings' => $listings,
    'accepted_requests' => $accepted_requests,
    'total_listings' => count($listings),
    'total_accepted_requests' => count($accepted_requests)
]);
?>
