<?php
header('Content-Type: application/json');
include 'db.php';

$sql = 'SELECT f.id, f.food_name, f.quantity, f.expiry_date, f.location, f.created_at, u.name as donor_name FROM food_listings f JOIN users u ON f.donor_id = u.id ORDER BY f.created_at DESC';
$result = $conn->query($sql);
$listings = [];
while ($row = $result->fetch_assoc()) {
    $listings[] = $row;
}
echo json_encode(['success' => true, 'listings' => $listings]);
?> 