<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

include 'db.php';

try {
    // Create table if not exists
    $conn->query("CREATE TABLE IF NOT EXISTS money_donations (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(255),
      email VARCHAR(255),
      amount DECIMAL(12,2),
      card_last4 VARCHAR(4),
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $sql = "SELECT id, name, email, amount, card_last4, created_at FROM money_donations ORDER BY created_at DESC";
    $res = $conn->query($sql);
    if (!$res) {
        echo json_encode(['success' => false, 'message' => 'Failed to list money donations']);
        exit;
    }
    $rows = [];
    while ($row = $res->fetch_assoc()) { $rows[] = $row; }
    echo json_encode(['success' => true, 'donations' => $rows]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to list money donations']);
}
