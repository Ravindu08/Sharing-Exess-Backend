<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

include 'db.php';

// Read and validate JSON body robustly
$raw = file_get_contents('php://input');
if ($raw === false) {
    echo json_encode(['success' => false, 'message' => 'No input']);
    exit;
}
$data = json_decode($raw, true);
if ($data === null) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
    exit;
}

$email = isset($data['email']) ? trim($data['email']) : '';
$password = isset($data['password']) ? $data['password'] : '';

if ($email === '' || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Missing email or password']);
    exit;
}

// 1) Try officers table first
$stmt = $conn->prepare('SELECT id, name, email, password, status, role FROM officers WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();
$officer = $res->fetch_assoc();

if (!$officer) {
    // 2) Fallback: users table with role=officer (in case environment stores officers there)
    $stmt2 = $conn->prepare('SELECT id, name, email, password, status, role FROM users WHERE email = ? AND role = "officer" LIMIT 1');
    $stmt2->bind_param('s', $email);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    $officer = $res2->fetch_assoc();
}

if (!$officer) {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    exit;
}

if (isset($officer['status']) && strtolower($officer['status']) !== 'active') {
    echo json_encode(['success' => false, 'message' => 'Account inactive']);
    exit;
}

if (!isset($officer['password']) || !password_verify($password, $officer['password'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    exit;
}

unset($officer['password']);
// Ensure role is set to 'officer' if missing
if (!isset($officer['role']) || $officer['role'] === '' || strtolower($officer['role']) === 'admin') {
    $officer['role'] = 'officer';
}

echo json_encode(['success' => true, 'officer' => $officer]);
