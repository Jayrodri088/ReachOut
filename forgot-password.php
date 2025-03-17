<?php
require_once 'functions/config.php';
require_once 'functions/db_connection.php';
require_once 'functions/functions.php';
require_once 'vendor/autoload.php'; // PHPMailer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set PHP timezone to Lagos, Nigeria (Africa/Lagos = UTC+1)
date_default_timezone_set('Africa/Lagos');

$showError = false;
$errorMsg = "";
$showSuccess = false;
$successMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);

    if (empty($email)) {
        $showError = true;
        $errorMsg = "Please enter your email address.";
    } else {
        // Check if email exists in the database
        $conn = getDbConnection();
        $user = fetchRow("SELECT * FROM users WHERE email = ?", [$email]);

        if ($user) {
            // Generate a unique reset token
            $token = bin2hex(random_bytes(32));

            // Debug: Log current PHP time and database time
            $phpTime = date('Y-m-d H:i:s');
            $dbTime = $conn->query("SELECT NOW() as db_time")->fetch(PDO::FETCH_ASSOC)['db_time'];
            error_log("PHP Time: $phpTime, DB Time: $dbTime");

            // Use MySQL to set expiration time (1 hour from NOW())
            $stmt = $conn->prepare("INSERT INTO password_reset_tokens (user_id, token, expiration) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
            $stmt->execute([$user['id'], $token]);

            // Fetch the expiration time for the reset link (for logging purposes)
            $stmt = $conn->prepare("SELECT expiration FROM password_reset_tokens WHERE token = ?");
            $stmt->execute([$token]);
            $expiration = $stmt->fetchColumn();
            error_log("Token Expiration: $expiration");

            // Send reset email
            $resetLink = "http://localhost/ROWMN%20Portal/reset-password.php?token=" . $token;
            $subject = "Password Reset Request - ReachOut World Media Network";
            $message = "
                <html>
                <body>
                    <h2>Password Reset Request</h2>
                    <p>You requested to reset your password. Click the link below to set a new password:</p>
                    <p><a href='$resetLink'>Reset Password</a></p>
                    <p>This link will expire in 1 hour. If you did not request a password reset, please ignore this email.</p>
                </body>
                </html>
            ";

            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'Johnbetho.c.088@gmail.com';
                $mail->Password = 'zqnc nodi xzyf ntbw';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('no-reply@yourdomain.com', 'ReachOut World Media Network');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $message;
                $mail->AltBody = strip_tags($message);

                $mail->send();
                $showSuccess = true;
                $successMsg = "A password reset link has been sent to your email.";
            } catch (Exception $e) {
                $showError = true;
                $errorMsg = "Failed to send reset email. Please try again later.";
                error_log("PHPMailer Error: " . $mail->ErrorInfo);
            }
        } else {
            $showError = true;
            $errorMsg = "No account found with that email address.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - ReachOut World Media Network</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <div class="login-container">
        <h2 class="login-title">Forgot Password</h2>
        <p class="text-muted">Enter your email address to receive a password reset link.</p>

        <form method="post" id="forgotPasswordForm">
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                </div>
            </div>

            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary login-btn">
                    <i class="fas fa-paper-plane me-2"></i>Send Reset Link
                </button>
            </div>
        </form>

        <div class="signup-link mt-3">
            <p>Remembered your password? <a href="login.php" class="fw-bold">Log In</a></p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if ($showError): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $errorMsg; ?>',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        </script>
    <?php endif; ?>

    <?php if ($showSuccess): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '<?php echo $successMsg; ?>',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'login.php';
                }
            });
        </script>
    <?php endif; ?>
</body>
</html>