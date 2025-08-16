<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

include 'db.php';
require_once 'Mailer.php';

// Create table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS money_donations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255),
  email VARCHAR(255),
  amount DECIMAL(12,2),
  card_last4 VARCHAR(4),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$amount = $data['amount'] ?? '';
$card_last4 = $data['card_last4'] ?? '';

if (!$name || !$email || !$amount || !$card_last4) {
  echo json_encode(['success' => false, 'message' => 'Missing fields']);
  exit;
}

$stmt = $conn->prepare('INSERT INTO money_donations (name, email, amount, card_last4) VALUES (?, ?, ?, ?)');
$stmt->bind_param('ssds', $name, $email, $amount, $card_last4);
if ($stmt->execute()) {
  // Send thank you email using PHPMailer
  $mailer = new Mailer();
  $subject = "Thank You for Your Donation!";
  $message = "<div style='text-align: center; margin-bottom: 20px;'>"
    . "<img src='E:\XAMPP2\htdocs\Sharing Excess\frontend\public\logo.png' alt='Sharing Excess Logo' style='width: 80px; height: 80px; object-fit: contain;'>"
    . "</div>"
    . "<h2 style='color:#28a745; text-align: center;'>Thank You for Your Generous Donation!</h2>"
    . "<p>Dear $name,</p>"
    . "<p>We truly appreciate your support. Your donation of <b>Rs $amount</b> will help us provide meals to those in need and make a real difference.</p>"
    . "<p style='color:#007bff'>Together, we can end hunger. Thank you for being a hero!</p>"
    . "<p style='font-size:13px;color:#888'>- Sharing Excess Team</p>";
  $mailer->setInfo($email, $subject, $message);
  $mailer->send();
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'message' => 'DB error']);
}
