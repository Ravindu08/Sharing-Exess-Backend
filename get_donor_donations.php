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
        FROM food_listings f 
        WHERE f.donor_id = ? 
        ORDER BY f.created_at DESC';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $donor_id);
$stmt->execute();
$result = $stmt->get_result();

// Store listing IDs we've already processed to avoid duplicates
$processed_listing_ids = [];

// 1a. First, get all listings with accepted requests
while ($row = $result->fetch_assoc()) {
    // Fetch accepted requests for this listing
    $requests = [];
    $stmt2 = $conn->prepare('SELECT fr.id, fr.food_name, fr.quantity, fr.needed_by, fr.location, fr.status, 
                           fr.created_at, fr.accepted_by, fr.recipient_id, fr.updated_at AS accepted_at, 
                           u.name AS recipient_name 
                           FROM food_requests fr 
                           LEFT JOIN users u ON fr.recipient_id = u.id 
                           WHERE fr.listing_id = ? AND fr.status = "accepted"');
    $stmt2->bind_param('i', $row['id']);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    
    while ($req = $res2->fetch_assoc()) {
        $requests[] = $req;
    }
    
    // Only include listings with accepted requests
    if (count($requests) > 0) {
        $row['requests'] = $requests;
        $listings[] = $row;
        $processed_listing_ids[] = $row['id'];
    }
}

// 1b. Then get all other accepted requests where this donor is the acceptor
$accepted_requests = [];
$stmt3 = $conn->prepare('SELECT fr.id, fr.food_name, fr.quantity, fr.needed_by, fr.location, 
                        fr.status, fr.created_at, fr.accepted_by, fr.recipient_id, 
                        fr.updated_at AS accepted_at, u.name AS recipient_name, fr.listing_id
                        FROM food_requests fr 
                        LEFT JOIN users u ON fr.recipient_id = u.id 
                        WHERE fr.status = "accepted" 
                        AND fr.accepted_by = ?
                        AND (fr.listing_id IS NULL OR fr.listing_id = 0 OR fr.listing_id NOT IN (' . 
                        (count($processed_listing_ids) > 0 ? implode(',', array_fill(0, count($processed_listing_ids), '?')) : '0') . '))');

// Build parameters for the query
$params = [$donor_name];
if (count($processed_listing_ids) > 0) {
    $params = array_merge($params, $processed_listing_ids);
}

// Bind parameters dynamically
$types = str_repeat('s', count($params));
$bindParams = array_merge([$types], $params);
$refs = [];
foreach($bindParams as $key => $value) {
    $refs[$key] = &$bindParams[$key];
}
call_user_func_array([$stmt3, 'bind_param'], $refs);

$stmt3->execute();
$res3 = $stmt3->get_result();

// Group accepted requests by listing_id
$accepted_requests_by_listing = [];
$custom_requests = [];

while ($req = $res3->fetch_assoc()) {
    if ($req['listing_id']) {
        if (!isset($accepted_requests_by_listing[$req['listing_id']])) {
            $accepted_requests_by_listing[$req['listing_id']] = [];
        }
        $accepted_requests_by_listing[$req['listing_id']][] = $req;
    } else {
        $custom_requests[] = $req;
    }
}

// Add accepted requests for listings
foreach ($accepted_requests_by_listing as $listing_id => $requests) {
    // Get listing details
    $stmt4 = $conn->prepare('SELECT * FROM food_listings WHERE id = ?');
    $stmt4->bind_param('i', $listing_id);
    $stmt4->execute();
    $listing = $stmt4->get_result()->fetch_assoc();
    
    if ($listing) {
        $listing['requests'] = $requests;
        $listing['total_requests'] = count($requests);
        $listings[] = $listing;
    }
}

// Add custom requests if any
if (count($custom_requests) > 0) {
    $listings[] = [
        'id' => 'custom',
        'food_name' => 'Custom Requests',
        'quantity' => '',
        'expiry_date' => '',
        'location' => '',
        'status' => 'accepted',
        'accepted_by' => $donor_name,
        'total_requests' => count($custom_requests),
        'requests' => $custom_requests,
        'is_custom' => true
    ];
}

echo json_encode(['success' => true, 'donations' => $listings]);
?>
