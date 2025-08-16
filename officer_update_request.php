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
if (!$data || !isset($data['request_id']) || !isset($data['updates']) || !is_array($data['updates'])) {
  echo json_encode(['success' => false, 'message' => 'Invalid payload']);
  exit;
}

$request_id = (int)$data['request_id'];
$updates = $data['updates'];

// Whitelist fields
$allowed = ['status', 'quantity', 'notes'];
$setClauses = [];
$params = [];
$types = '';

foreach ($allowed as $field) {
  if (array_key_exists($field, $updates)) {
    $setClauses[] = "$field = ?";
    $val = $updates[$field];
    if ($field === 'quantity') { $types .= 'i'; $params[] = (int)$val; }
    else { $types .= 's'; $params[] = (string)$val; }
  }
}

if (empty($setClauses)) {
  echo json_encode(['success' => false, 'message' => 'No allowed fields to update']);
  exit;
}

$sql = 'UPDATE food_requests SET ' . implode(', ', $setClauses) . ' WHERE id = ?';
$types .= 'i';
$params[] = $request_id;

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
  // Send email notifications if status changed
  if (isset($updates['status'])) {
    // Get request details with donor and recipient info
    $reqSql = "SELECT fr.*, fl.donor_id, u1.name as recipient_name, u1.email as recipient_email, 
               u2.name as donor_name, u2.email as donor_email, fl.food_name
               FROM food_requests fr 
               LEFT JOIN food_listings fl ON fr.listing_id = fl.id 
               LEFT JOIN users u1 ON fr.recipient_id = u1.id 
               LEFT JOIN users u2 ON fl.donor_id = u2.id 
               WHERE fr.id = ?";
    $reqStmt = $conn->prepare($reqSql);
    $reqStmt->bind_param('i', $request_id);
    $reqStmt->execute();
    $request = $reqStmt->get_result()->fetch_assoc();
    
    if ($request) {
      $mailer = new Mailer();
      $status = $updates['status'];
      $foodName = $request['food_name'];
      $quantity = $request['quantity'];
      
      // Email to recipient
      if ($request['recipient_email']) {
        $recipientSubject = "Your Food Request Status Updated";
        $recipientMessage = "<div style='text-align: center; margin-bottom: 20px;'>"
          . "<div style='width: 80px; height: 80px; background: #28a745; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 20px; margin: 0 auto;'>SE</div>"
          . "</div>"
          . "<h2 style='color:#28a745; text-align: center;'>Request Status Updated</h2>"
          . "<p>Dear {$request['recipient_name']},</p>"
          . "<p>Your food request for <b>$foodName ($quantity)</b> has been updated to: <b style='color:#007bff'>$status</b></p>"
          . "<p>We'll keep you informed of any further updates.</p>"
          . "<p style='font-size:13px;color:#888'>- Sharing Excess Team</p>";
        try {
          $mailer->setInfo($request['recipient_email'], $recipientSubject, $recipientMessage);
          $mailer->send();
        } catch (Exception $e) {
          // Log error but don't break the main functionality
          error_log("Failed to send email to recipient: " . $e->getMessage());
        }
      }
      
      // Email to donor
      if ($request['donor_email']) {
        $donorSubject = "Food Request Status Updated";
        $donorMessage = "<div style='text-align: center; margin-bottom: 20px;'>"
          . "<div style='width: 80px; height: 80px; background: #28a745; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 20px; margin: 0 auto;'>SE</div>"
          . "</div>"
          . "<h2 style='color:#28a745; text-align: center;'>Request Status Updated</h2>"
          . "<p>Dear {$request['donor_name']},</p>"
          . "<p>The request for your donation <b>$foodName ($quantity)</b> has been updated to: <b style='color:#007bff'>$status</b></p>"
          . "<p>Thank you for your generosity!</p>"
          . "<p style='font-size:13px;color:#888'>- Sharing Excess Team</p>";
        try {
          $mailer->setInfo($request['donor_email'], $donorSubject, $donorMessage);
          $mailer->send();
        } catch (Exception $e) {
          // Log error but don't break the main functionality
          error_log("Failed to send email to donor: " . $e->getMessage());
        }
      }
    }
  }
  
  echo json_encode(['success' => true, 'message' => 'Request updated']);
} else {
  echo json_encode(['success' => false, 'message' => 'Update failed']);
}
