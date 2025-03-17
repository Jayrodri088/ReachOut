<?php
require_once 'functions/config.php';
require_once 'functions/db_connection.php';
require_once 'functions/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['pending_user'])) {
    header("Location: signup.php");
    exit;
}

$user = fetchRow("SELECT * FROM users WHERE id = ?", [$_SESSION['pending_user']]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - ReachOut World Media Network</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="signup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .verification-icon {
            font-size: 4rem;
            color: #26d0ce;
            margin-bottom: 1.5rem;
        }
        .resend-link {
            cursor: pointer;
            color: #26d0ce !important;
            text-decoration: underline !important;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="text-center">
            <i class="fas fa-envelope verification-icon"></i>
            <h2 class="login-title">Verify Your Email</h2>
            <p class="lead">We've sent a verification link to your email address <?php echo htmlspecialchars($user['email']); ?></p>
            
            <div class="alert alert-info" style="background: rgba(38, 208, 206, 0.1); border-color: #26d0ce;">
                <i class="fas fa-info-circle me-2"></i>
                Check your inbox and click the link to activate your account
            </div>

            <div class="mt-4">
                <p class="text-muted mb-2">Didn't receive the email?</p>
                <button class="btn btn-outline-secondary" onclick="resendVerification()">
                    <i class="fas fa-redo me-2"></i>Resend Email
                </button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function resendVerification() {
        $.post('resend-verification.php', { user_id: <?php echo $_SESSION['pending_user']; ?> })
            .done(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Email Resent!',
                    text: 'New verification link sent to your email',
                    confirmButtonColor: '#26d0ce'
                });
            })
            .fail((xhr) => {
                const response = xhr.responseJSON || {};
                Swal.fire({
                    icon: 'error',
                    title: 'Resend Failed',
                    text: response.message || 'Please try again later',
                    confirmButtonColor: '#26d0ce'
                });
            });
    }
    </script>
</body>
</html>