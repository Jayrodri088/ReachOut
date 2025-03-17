<?php
require_once 'functions/config.php';
require_once 'functions/db_connection.php';
require_once 'functions/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$showError = false;
$errorMsg = "";
$showSuccess = false;
$successMsg = "";
$tokenValid = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $conn = getDbConnection();

    // Check if token exists and is not expired
    $stmt = $conn->prepare("SELECT * FROM password_reset_tokens WHERE token = ? AND expiration > NOW()");
    $stmt->execute([$token]);
    $resetToken = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resetToken) {
        $tokenValid = true;
        $userId = $resetToken['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];

            if (empty($password) || empty($confirmPassword)) {
                $showError = true;
                $errorMsg = "Please fill in all fields.";
            } elseif (strlen($password) < 8) {
                $showError = true;
                $errorMsg = "Password must be at least 8 characters long.";
            } elseif ($password !== $confirmPassword) {
                $showError = true;
                $errorMsg = "Passwords do not match.";
            } else {
                // Update the user's password
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashedPassword, $userId]);

                // Delete the used token
                $stmt = $conn->prepare("DELETE FROM password_reset_tokens WHERE token = ?");
                $stmt->execute([$token]);

                $showSuccess = true;
                $successMsg = "Your password has been reset successfully. You can now log in with your new password.";
            }
        }
    } else {
        $showError = true;
        $errorMsg = "Invalid or expired reset link.";
    }
} else {
    $showError = true;
    $errorMsg = "No reset token provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - ReachOut World Media Network</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <div class="login-container">
        <h2 class="login-title">Reset Password</h2>
        <p class="text-muted">Enter your new password below.</p>

        <?php if ($tokenValid): ?>
            <form method="post" id="resetPasswordForm">
                <div class="form-group">
                    <label for="password" class="form-label">New Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary login-btn">
                        <i class="fas fa-check me-2"></i>Reset Password
                    </button>
                </div>
            </form>
        <?php endif; ?>

        <div class="signup-link mt-3">
            <p>Return to <a href="login.php" class="fw-bold">Log In</a></p>
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
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'login.php';
                }
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