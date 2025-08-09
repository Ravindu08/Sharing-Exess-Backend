<?php
require_once __DIR__ . '/db.php';

header('Content-Type: text/plain');

$result = $conn->query('SELECT id, email, role, status, password FROM users');
if (!$result) {
    echo "Query failed: " . $conn->error;
    exit;
}
while ($row = $result->fetch_assoc()) {
    echo "id: {$row['id']}\n";
    echo "email: {$row['email']}\n";
    echo "role: {$row['role']}\n";
    echo "status: {$row['status']}\n";
    echo "password: {$row['password']}\n";
    echo "-----------------------------\n";
}
?>