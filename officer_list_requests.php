<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

include 'db.php';

try {
    // Ensure needed columns exist on older schemas (MySQL 5.7 compatible)
    $dbNameRes = $conn->query('SELECT DATABASE() AS db');
    $dbName = ($dbNameRes && ($rowDb = $dbNameRes->fetch_assoc())) ? $rowDb['db'] : 'sharing_excess';

    $columnsNeeded = [
        ['name' => 'status', 'definition' => "ENUM('pending','accepted','declined') DEFAULT 'pending'"],
        ['name' => 'notes', 'definition' => 'TEXT NULL'],
        ['name' => 'listing_id', 'definition' => 'INT NULL']
    ];
    foreach ($columnsNeeded as $col) {
        $colName = $conn->real_escape_string($col['name']);
        $check = $conn->query(
            "SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='" . $conn->real_escape_string($dbName) . "' AND TABLE_NAME='food_requests' AND COLUMN_NAME='" . $colName . "' LIMIT 1"
        );
        if ($check && $check->num_rows === 0) {
            $conn->query("ALTER TABLE food_requests ADD COLUMN `{$col['name']}` {$col['definition']}");
        }
    }

    $sql = "SELECT 
                fr.id,
                fr.listing_id,
                fr.recipient_id,
                COALESCE(fr.status,'pending') AS status,
                fr.quantity,
                fr.notes,
                fr.food_name,
                fr.needed_by,
                fr.location,
                fr.created_at,
                fl.food_name AS listing_title,
                fl.donor_id,
                u.name AS recipient_name
            FROM food_requests fr
            LEFT JOIN food_listings fl ON fr.listing_id = fl.id
            LEFT JOIN users u ON fr.recipient_id = u.id
            ORDER BY fr.created_at DESC";
    $res = $conn->query($sql);
    $rows = [];
    if ($res) {
        while ($row = $res->fetch_assoc()) { $rows[] = $row; }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to list requests']);
        exit;
    }
    echo json_encode(['success' => true, 'requests' => $rows]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to list requests']);
}
