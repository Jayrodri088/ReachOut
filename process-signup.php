<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

$showError = false;
$errorMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required = ['username', 'email', 'password', 'confirm_password', 'first_name', 'last_name', 'skills'];
    $missing = array_diff($required, array_keys($_POST));

    if (!empty($missing)) {
        $showError = true;
        $errorMsg = "Please fill in all required fields.";
    } else {
        $username = sanitizeInput($_POST['username']);
        $email = sanitizeInput($_POST['email']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        $firstName = sanitizeInput($_POST['first_name']);
        $lastName = sanitizeInput($_POST['last_name']);
        $location = sanitizeInput($_POST['location'] ?? '');
        $website = sanitizeInput($_POST['website'] ?? '');
        $skills = getEnumValues('skills', 'users');

        if (!in_array($_POST['skills'], $skills)) {
            $showError = true;
            $errorMsg = "Please select a valid skill";
            goto validation_end;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $showError = true;
            $errorMsg = "Invalid email address.";
        } elseif ($password !== $confirmPassword) {
            $showError = true;
            $errorMsg = "Passwords do not match.";
        } elseif (strlen($password) < 8) {
            $showError = true;
            $errorMsg = "Password must be at least 8 characters long.";
        } else {
            $existingUser = getUserByUsername($username);
            if ($existingUser) {
                $showError = true;
                $errorMsg = "Username already taken.";
            } else {
                $userData = [
                    'username' => $username,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'skills' => $_POST['skills'],
                    'location' => $location,
                    'website' => $website,
                    'avatar' => 'assets/images/default-avatar.png',
                    'created_at' => date('Y-m-d H:i:s'),
                    'status' => 'pending'
                ];

                $userId = insertRecord('users', $userData);
                if ($userId) {
                    // Generate verification token
                    $verificationToken = bin2hex(random_bytes(32));
                    $expiration = date('Y-m-d H:i:s', strtotime('+24 hours'));

                    $tokenData = [
                        'user_id' => $userId,
                        'token' => $verificationToken,
                        'expiration' => $expiration
                    ];
                    insertRecord('verification_tokens', $tokenData);

                    // Send verification email using PHPMailer
                    $mail = new PHPMailer(true);
                    try {
                        // Server settings
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
                        $mail->SMTPAuth = true;
                        $mail->Username = 'Johnbetho.c.088@gmail.com'; // Your Gmail address
                        $mail->Password = 'zqnc nodi xzyf ntbw'; // App password from Gmail
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        // Recipients
                        $mail->setFrom('noreply@yourdomain.com', 'ReachOut World Media Network');
                        $mail->addAddress($email, "$firstName $lastName");

                        // Content
                        $verificationLink = "http://localhost/ROWMN%20Portal/verify-email.php?token=$verificationToken";
                        $mail->isHTML(false);
                        $mail->Subject = 'Verify Your Email - ReachOut World Media Network';
                        $mail->Body = "Hi $firstName,\n\nThank you for signing up! Please verify your email by clicking the link below:\n$verificationLink\n\nThis link will expire in 24 hours.\n\nBest,\nReachOut World Media Network Team";

                        $mail->send();
                        $_SESSION['pending_user'] = $userId;
                        header("Location: verification-pending.php");
                        exit;
                    } catch (Exception $e) {
                        $showError = true;
                        $errorMsg = "Failed to send verification email. Error: {$mail->ErrorInfo}";
                        error_log("PHPMailer Error: {$mail->ErrorInfo}");
                    }
                } else {
                    $showError = true;
                    $errorMsg = "Registration failed. Please try again.";
                    error_log("User Data: " . print_r($userData, true));
                }
            }
        }
    }
    validation_end:

    // Pass error back to signup page
    if ($showError) {
        $_SESSION['signup_error'] = $errorMsg;
        header("Location: signup.php");
        exit;
    }
}