<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Import PHPMailer classes
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

class MailerAlternative {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);

        // Alternative server settings for Gmail
        $this->mail->isSMTP();
        $this->mail->Host       = 'smtp.gmail.com';
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = 'muralitharanabinath7@gmail.com';
        $this->mail->Password   = 'eyurrhcpuwuiprvx'; // Your app password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Try STARTTLS instead of SSL
        $this->mail->Port       = 587; // Use port 587 for STARTTLS
        
        // Additional settings for better compatibility
        $this->mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $this->mail->setFrom('muralitharanabinath7@gmail.com', 'Sharing Excess');
    }

    // Set email subject and message
    public function setInfo($recipientEmail, $subject, $message) {
        $this->mail->addAddress($recipientEmail);
        $this->mail->isHTML(true);
        $this->mail->Subject = $subject;
        $this->mail->Body    = $message;
    }

    // Send the email
    public function send() {
        try {
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
            return false;
        }
    }
}
?> 