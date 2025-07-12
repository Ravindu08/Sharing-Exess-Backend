<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    include 'db.php';
    
    if ($conn->ping()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Database connection successful',
            'database' => 'sharing_excess',
            'server' => $conn->server_info
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Database connection failed',
            'error' => $conn->error
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database connection error',
        'error' => $e->getMessage()
    ]);
}
?> 