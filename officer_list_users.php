<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

include 'db.php';

try {
    $sql = "SELECT id, name, email, role, status, created_at FROM users";
    $res = $conn->query($sql);
    if (!$res) {
        echo json_encode(['success' => false, 'message' => 'Failed to list users']);
        exit;
    }
    $rows = [];
    while ($row = $res->fetch_assoc()) { $rows[] = $row; }
    echo json_encode(['success' => true, 'users' => $rows]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to list users']);
}
