<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

include 'db.php';

try {
    // Build a schema-safe query using existing columns
    $sql = "SELECT 
                fl.id,
                fl.food_name AS title,
                fl.description,
                fl.quantity,
                COALESCE(fl.status, 'available') AS status,
                fl.expiry_date,
                fl.location,
                fl.created_at,
                fl.donor_id,
                u.name AS donor_name
            FROM food_listings fl
            LEFT JOIN users u ON fl.donor_id = u.id
            ORDER BY fl.created_at DESC";
    $res = $conn->query($sql);
    if (!$res) {
        echo json_encode(['success' => false, 'message' => 'Failed to list listings']);
        exit;
    }
    $rows = [];
    while ($row = $res->fetch_assoc()) { $rows[] = $row; }
    echo json_encode(['success' => true, 'listings' => $rows]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to list listings']);
}
