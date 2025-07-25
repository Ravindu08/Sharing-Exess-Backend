<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require 'db.php';
require 'vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';

if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Email is required.']);
    exit;
}

// Check if user exists
$stmt = $conn->prepare('SELECT id, name FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'No user found with that email.']);
    exit;
}
$user = $result->fetch_assoc();

// Generate code and save to user
$code = rand(100000, 999999);
$stmt = $conn->prepare('UPDATE users SET verification_code = ? WHERE email = ?');
$stmt->bind_param('ss', $code, $email);
$stmt->execute();

// Send email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'your_gmail@gmail.com'; // your Gmail
    $mail->Password = 'your_app_password'; // your app password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('your_gmail@gmail.com', 'Sharing Excess');
    $mail->addAddress($email, $user['name']);
    $mail->isHTML(true);
    $mail->Subject = 'Password Reset Code - Sharing Excess';
    $mail->Body = "<p>Your password reset code is: <strong>$code</strong></p>";
    $mail->send();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to send email.']);
} 