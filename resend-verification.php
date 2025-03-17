<?php
require_once 'functions/config.php';
require_once 'functions/db_connection.php';
require_once 'functions/functions.php';

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!isset($_POST['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

try {
    $conn = getDbConnection();
    $userId = $_POST['user_id'];
    
    $user = fetchRow("SELECT * FROM users WHERE id = ?", [$userId]);
    if (!$user || $user['email_verified'] == 1) {
        echo json_encode(['success' => false, 'message' => 'User not found or already verified']);
        exit;
    }

    // Generate new token
    $newToken = bin2hex(random_bytes(32));
    $expiration = date('Y-m-d H:i:s', strtotime('+24 hours'));
    
    // Delete old tokens
    $conn->prepare("DELETE FROM verification_tokens WHERE user_id = :user_id")
         ->execute([':user_id' => $userId]);
    
    // Insert new token
    $conn->prepare("INSERT INTO verification_tokens (user_id, token, expiration)
                   VALUES (:user_id, :token, :expiration)")
         ->execute([
             ':user_id' => $userId,
             ':token' => $newToken,
             ':expiration' => $expiration
         ]);

    // Resend verification email using PHPMailer
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com'; // Your Gmail address
        $mail->Password = 'your-app-password'; // App password from Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('noreply@yourdomain.com', 'ReachOut World Media Network');
        $mail->addAddress($user['email'], "{$user['first_name']} {$user['last_name']}");

        // Content
        $verificationLink = "http://localhost/ROWMN%20Portal/verify-email.php?token=$newToken";
        $mail->isHTML(false);
        $mail->Subject = 'Verify Your Email - ReachOut World Media Network';
        $mail->Body = "Hi {$user['first_name']},\n\nPlease verify your email by clicking the link below:\n$verificationLink\n\nThis link will expire in 24 hours.\n\nBest,\nReachOut World Media Network Team";

        $mail->send();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        error_log("PHPMailer Error: {$mail->ErrorInfo}");
        echo json_encode(['success' => false, 'message' => "Failed to send verification email. Error: {$mail->ErrorInfo}"]);
    }
    
} catch (PDOException $e) {
    error_log("Resend Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later']);
    exit;
}