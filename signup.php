<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions/config.php';
require_once 'functions/db_connection.php';
require_once 'functions/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$showError = isset($_SESSION['signup_error']);
$errorMsg = $_SESSION['signup_error'] ?? '';
unset($_SESSION['signup_error']); // Clear the error after displaying

$skills = getEnumValues('skills', 'users');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - ReachOut World Media Network</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/signup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/video.js/7.14.3/video-js.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <div class="login-container">
        <h2 class="login-title">Create Your Account</h2>

        <?php if ($showError): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($errorMsg); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="process-signup.php" id="signupForm">
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
                <label for="username" class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-at"></i></span>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Choose a username" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
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
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Create password" required>
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
                    <i class="fas fa-user-plus me-2"></i>Create Account
                </button>
            </div>

            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="social-login-section text-center mb-4">
                        <p class="text-muted mb-1">Or sign up with</p>
                        <div class="d-flex gap-5 justify-content-center">
                            <a href="#" class="btn btn-outline-light btn-social pb-1 pt-2 px-3">
                                <img src="assets/images/x.png" alt="X Logo" width="30" class="">
                            </a>
                            <a href="#" class="btn btn-outline-primary btn-social" id="kc-login-btn">
                                <img src="assets/images/app_logo.png" alt="KC Logo" width="40" class="">
                            </a>
                            <a href="#" class="btn btn-outline-light btn-social" id="google-login-btn">
                                <img src="assets/images/google.png" alt="Email logo" width="40" class="">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="signup-link">
            <p>Already have an account? <a href="login.php" class="fw-bold">Login Here</a></p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.4.0/firebase-app.js";
        import { getAuth, GoogleAuthProvider, signInWithPopup } from "https://www.gstatic.com/firebasejs/11.4.0/firebase-auth.js";

        const firebaseConfig = {
            apiKey: "AIzaSyCVNW7ArT9_ly29U1SYKUre4GKgudrYb5A",
            authDomain: "login-7f654.firebaseapp.com",
            projectId: "login-7f654",
            storageBucket: "login-7f654.firebasestorage.app",
            messagingSenderId: "405178536612",
            appId: "1:405178536612:web:41b07ed7efb64042998c31"
        };

        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        auth.languageCode = "en";
        const provider = new GoogleAuthProvider();

        const googleLogin = document.getElementById('google-login-btn');
        googleLogin.addEventListener("click", function(event) {
            event.preventDefault();

            signInWithPopup(auth, provider)
                .then((result) => {
                    const user = result.user;
                    console.log("Google Sign-In successful:", user);

                    const userData = {
                        uid: user.uid,
                        email: user.email || '',
                        displayName: user.displayName || 'User_' + user.uid.substring(0, 8),
                        photoURL: user.photoURL || null
                    };
                    console.log("Sending to server:", userData);

                    fetch('process_google_login.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(userData)
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log("Server response:", data);
                            if (data.success) {
                                window.location.href = 'index.php';
                            } else {
                                Swal.fire('Error', data.message || 'Failed to process Google login', 'error')
                                    .then(() => {
                                        if (data.message === 'User needs to complete profile') {
                                            window.location.href = 'complete-profile.php?email=' + encodeURIComponent(user.email);
                                        }
                                    });
                            }
                        })
                        .catch(error => {
                            console.error('Error sending data to server:', error);
                            Swal.fire('Error', 'Something went wrong', 'error');
                        });
                })
                .catch((error) => {
                    console.error("Google Sign-In error:", error);
                    Swal.fire('Error', error.message, 'error');
                });
        });

        const kcLogin = document.getElementById('kc-login-btn');
        kcLogin.addEventListener("click", function(event) {
            event.preventDefault();
            const clientId = 'com.kingschat';
            const scopes = ['conference_calls', 'profile'];
            const currentUrl = window.location.href;
            const baseUrl = currentUrl.substring(0, currentUrl.lastIndexOf('/') + 1);
            const callbackUrl = baseUrl + 'process_kc_login.php';

            const params = {
                client_id: clientId,
                scopes: JSON.stringify(scopes),
                redirect_uri: callbackUrl,
                response_type: 'token',
                post_redirect: true
            };

            const queryString = Object.keys(params)
                .map(key => `${encodeURIComponent(key)}=${encodeURIComponent(params[key])}`)
                .join('&');

            const loginUrl = `https://accounts.kingsch.at/?${queryString}`;

            // Add loading state to button
            const button = kcLogin;
            button.classList.add('disabled');
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';

            setTimeout(() => {
                window.location.href = loginUrl;
            }, 500);
        });
    </script>
</body>
</html>