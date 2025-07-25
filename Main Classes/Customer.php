<?php
class Customer {
    private $conn;

    public function __construct() {
        require_once __DIR__ . '/../db.php';
        $this->conn = $conn;
    }

    // Update password for the given email
    public function forgotPassword($email, $hashedPassword) {
        $stmt = $this->conn->prepare('UPDATE users SET password = ? WHERE email = ?');
        if (!$stmt) return false;
        $stmt->bind_param('ss', $hashedPassword, $email);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
} 