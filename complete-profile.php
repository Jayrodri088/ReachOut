<?php
require_once 'functions/config.php';
require_once 'functions/db_connection.php';
require_once 'functions/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$showError = false;
$errorMsg = "";
$skills = getEnumValues('skills', 'users');

// Check for any pending user session (Google, KingsChat, or Twitter)
if (!isset($_SESSION['pending_google_user']) && !isset($_SESSION['pending_kc_user']) && !isset($_SESSION['pending_twitter_user'])) {
    header("Location: login.php");
    exit;
}
$pendingUser = $_SESSION['pending_google_user'] ?? $_SESSION['pending_kc_user'] ?? $_SESSION['pending_twitter_user'];
$email = $pendingUser['email'];
error_log('Pending KC User: ' . print_r($_SESSION['pending_kc_user'], true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Updated required fields to include password and confirm_password
    $required = ['first_name', 'last_name', 'skills', 'password', 'confirm_password'];
    $missing = array_diff($required, array_keys($_POST));

    if (!empty($missing)) {
        $showError = true;
        $errorMsg = "Please fill in all required fields.";
    } else {
        $firstName = sanitizeInput($_POST['first_name']);
        $lastName = sanitizeInput($_POST['last_name']);
        $skills = sanitizeInput($_POST['skills']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        $location = sanitizeInput($_POST['location'] ?? '');
        $website = sanitizeInput($_POST['website'] ?? '');

        // Validate password
        if (strlen($password) < 8) {
            $showError = true;
            $errorMsg = "Password must be at least 8 characters long.";
        } elseif ($password !== $confirmPassword) {
            $showError = true;
            $errorMsg = "Passwords do not match.";
        } elseif (!in_array($skills, getEnumValues('skills', 'users'))) {
            $showError = true;
            $errorMsg = "Please select a valid skill.";
        } else {
            // Determine authentication method and set appropriate fields
            $userData = [
                'username' => $email,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'skills' => $skills,
                'location' => $location,
                'website' => $website,
                'avatar' => $pendingUser['photoURL'] ?? 'assets/images/default-avatar.png',
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 'active',
                'email_verified' => 1
            ];

            // Add method-specific fields
            if (isset($_SESSION['pending_google_user'])) {
                $userData['google_uid'] = $pendingUser['uid'];
            } elseif (isset($_SESSION['pending_kc_user'])) {
                $userData['kc_uid'] = $pendingUser['uid'];
                $userData['kc_access_token'] = $pendingUser['kcAccessToken'] ?? null;
            } elseif (isset($_SESSION['pending_twitter_user'])) {
                $userData['twitter_uid'] = $pendingUser['uid'];
                $userData['twitter_token'] = $pendingUser['twitterToken'] ?? null;
                $userData['twitter_secret'] = $pendingUser['twitterSecret'] ?? null;
            }

            $userId = insertRecord('users', $userData);
            if ($userId) {
                // Clean up all pending user sessions
                unset($_SESSION['pending_google_user']);
                unset($_SESSION['pending_kc_user']);
                unset($_SESSION['pending_twitter_user']);
                $_SESSION['user_id'] = $userId;
                $_SESSION['username'] = $email;
                $_SESSION['avatar'] = $userData['avatar'];
                header("Location: index.php");
                exit;
            } else {
                $showError = true;
                $errorMsg = "Registration failed. Please try again.";
            }
        }
    }
    validation_end:
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Profile - ReachOut World Media Network</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="signup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <div class="login-container">
        <h2 class="login-title">Complete Your Profile</h2>
        <p class="text-muted">We need some additional information to create your account. Set a password to log in with email in the future.</p>

        <form method="post" id="completeProfileForm">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="first_name" class="form-label">First Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter your first name" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="last_name" class="form-label">Last Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter your last name" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="skills" class="form-label">Primary Skill</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-tools"></i></span>
                    <select class="form-select" id="skills" name="skills" required>
                        <option value="" selected disabled>Select your primary skill</option>
                        <?php foreach ($skills as $skill):
                            $formatted = ucwords(str_replace('_', ' ', $skill));
                        ?>
                            <option value="<?= $skill ?>"><?= $formatted ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="location" class="form-label">Location</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            <input type="text" class="form-control" id="location" name="location" placeholder="Your location">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="website" class="form-label">Website</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-globe"></i></span>
                            <input type="url" class="form-control" id="website" name="website" placeholder="https://example.com">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary login-btn">
                    <i class="fas fa-check me-2"></i>Complete Profile
                </button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if ($showError): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Profile Completion Error',
                text: '<?php echo $errorMsg; ?>',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        </script>
    <?php endif; ?>
</body>
</html>