<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

include 'db.php';
require_once 'Mailer.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data || !isset($data['listing_id']) || !isset($data['updates']) || !is_array($data['updates'])) {
  echo json_encode(['success' => false, 'message' => 'Invalid payload']);
  exit;
}

$listing_id = (int)$data['listing_id'];
$updates = $data['updates'];
// Map frontend field names to schema columns
if (isset($updates['title'])) {
  $updates['food_name'] = $updates['title'];
  unset($updates['title']);
}

// Whitelist fields (schema uses food_name)
$allowed = ['food_name', 'description', 'quantity', 'status'];
$setClauses = [];
$params = [];
$types = '';

foreach ($allowed as $field) {
  if (array_key_exists($field, $updates)) {
    $setClauses[] = "$field = ?";
    $val = $updates[$field];
    // quantity is a VARCHAR in schema, bind as string
    $types .= 's';
    $params[] = (string)$val;
  }
}

if (empty($setClauses)) {
  echo json_encode(['success' => false, 'message' => 'No allowed fields to update']);
  exit;
}

$sql = 'UPDATE food_listings SET ' . implode(', ', $setClauses) . ' WHERE id = ?';
$types .= 'i';
$params[] = $listing_id;

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
  // Send email notifications if status changed
  if (isset($updates['status'])) {
    // Get listing details with donor info
    $listSql = "SELECT fl.*, u.name as donor_name, u.email as donor_email 
                FROM food_listings fl 
                LEFT JOIN users u ON fl.donor_id = u.id 
                WHERE fl.id = ?";
    $listStmt = $conn->prepare($listSql);
    $listStmt->bind_param('i', $listing_id);
    $listStmt->execute();
    $listing = $listStmt->get_result()->fetch_assoc();
    
    if ($listing && $listing['donor_email']) {
      $mailer = new Mailer();
      $status = $updates['status'];
      $foodName = $listing['food_name'];
      $quantity = $listing['quantity'];
      
      $subject = "Your Food Donation Status Updated";
      $message = "<div style='text-align: center; margin-bottom: 20px;'>"
        . "<div style='width: 100px; height: 100px; background: #28a745; border-radius: 50%; display: inline-flex; align-items: center; justify-content: right; color: white; font-weight: bold; font-size: 20px; margin: 20px;'>SE</div>"
        . "</div>"
        . "<h2 style='color:#28a745; text-align: center;'>Donation Status Updated</h2>"
        . "<p>Dear {$listing['donor_name']},</p>"
        . "<p>Your food donation <b>$foodName ($quantity)</b> has been updated to: <b style='color:#007bff'>$status</b></p>";
      
      if ($status === 'delivered') {
        $message .= "<p style='color:#28a745; font-weight:600;'>🎉 Your donation has been successfully delivered to those in need!</p>";
      } elseif ($status === 'inactive') {
        $message .= "<p>Your donation has been marked as inactive.</p>";
      }
      
      $message .= "<p>Thank you for your generosity and for helping us fight hunger!</p>"
        . "<p style='font-size:13px;color:#888'>- Sharing Excess Team</p>";
      
      try {
        $mailer->setInfo($listing['donor_email'], $subject, $message);
        $mailer->send();
      } catch (Exception $e) {
        // Log error but don't break the main functionality
        error_log("Failed to send email to donor: " . $e->getMessage());
      }
    }
  }
  
  echo json_encode(['success' => true, 'message' => 'Listing updated']);
} else {
  echo json_encode(['success' => false, 'message' => 'Update failed']);
}
