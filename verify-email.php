<?php
require_once 'functions/config.php';
require_once 'functions/db_connection.php';
require_once 'functions/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$success = false;
$message = '';
$icon = 'times-circle';
$button = false;

if (isset($_GET['token'])) {
    try {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT users.id FROM verification_tokens
                              JOIN users ON users.id = verification_tokens.user_id
                              WHERE token = :token AND expiration > NOW()");
        $stmt->execute([':token' => $_GET['token']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Mark as verified
            $conn->prepare("UPDATE users SET email_verified = 1, status = 'active' WHERE id = :id")
                 ->execute([':id' => $user['id']]);
            
            // Delete token
            $conn->prepare("DELETE FROM verification_tokens WHERE token = :token")
                 ->execute([':token' => $_GET['token']]);
            
            // Fetch user details
            $userData = fetchRow("SELECT * FROM users WHERE id = ?", [$user['id']]);
            if ($userData) {
                // Log the user in using $userData
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['username'] = $userData['username'];
                $_SESSION['avatar'] = $userData['avatar'];

                $success = true;
                $message = 'Email Verified Successfully!';
                $icon = 'check-circle';
                $button = true;
            } else {
                $message = 'Failed to fetch user data';
            }
        } else {
            $message = 'Invalid or expired verification link';
        }
    } catch (PDOException $e) {
        error_log("Verification Error: " . $e->getMessage());
        $message = 'Verification failed. Please try again.';
    }
} else {
    $message = 'Missing verification token';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - ReachOut World Media Network</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="signup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .verification-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="text-center">
            <i class="fas fa-<?= $icon ?> verification-icon text-<?= $success ? 'success' : 'danger' ?>"></i>
            <h2 class="login-title"><?= $message ?></h2>
            
            <?php if ($success): ?>
                <a href="index.php" class="btn btn-primary login-btn mt-3">
                    <i class="fas fa-home me-2"></i>Go to Homepage
                </a>
            <?php else: ?>
                <div class="mt-4">
                    <p>Need help? <a href="contact.php" class="fw-bold" style="color: #26d0ce;">Contact Support</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>