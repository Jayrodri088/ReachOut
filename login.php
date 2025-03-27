<?php
require_once 'functions/config.php';
require_once 'functions/db_connection.php';
require_once 'functions/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define variable to check if we need to show error
$showError = false;
$errorMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $showError = true;
        $errorMsg = "Please fill in all fields.";
    } else {
        $user = getUserByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['avatar'] = $user['avatar'];
            header("Location: index.php");
            exit;
        } else {
            $showError = true;
            $errorMsg = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ReachOut World Media Network</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/video.js/7.14.3/video-js.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body>
    <?php include_once 'includes/header.php'; ?>
    <div class="login-container">
        <h2 class="login-title">Welcome Back</h2>

        <form method="post" id="loginForm">
            <div class="form-group">
                <label for="username" class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username or email" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                </div>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                </div>
            </div>

            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary login-btn">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>
            </div>

            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="social-login-section text-center mb-4">
                        <p class="text-muted mb-1">Or sign in with:</p>
                        <div class="d-flex gap-5 justify-content-center">
                            <a href="#" class="btn btn-outline-light btn-social pb-1 pt-2 px-3" id="twitter-login-btn">
                                <img src="assets/images/x.png" alt="X Logo" width="30" class="">
                            </a>
                            <a href="#" class="btn btn-outline-primary btn-social" id="kc-login-btn">
                                <img src="assets/images/app_logo.png" alt="KC Logo" width="40" class="">
                            </a>
                            <a href="#" class="btn btn-outline-light btn-social" id="google-login-btn">
                                <img src="assets/images/google.png" alt="Google logo" width="40" class="">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="signup-link">
            <p>Don't have an account? <a href="signup.php" class="fw-bold">Sign Up</a></p>
            <p><a href="forgot-password.php">Forgot your password?</a></p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/video.js/7.14.3/video.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/main.js"></script>

    <?php if ($showError): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '<?php echo $errorMsg; ?>',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Try Again'
            });
        </script>
    <?php endif; ?>

    <script type="module">
        // Import Firebase functions
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

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        auth.languageCode = "en";

        // Google Login
        const googleProvider = new GoogleAuthProvider();
        const googleLogin = document.getElementById('google-login-btn');
        googleLogin.addEventListener("click", function(event) {
            event.preventDefault();
            signInWithPopup(auth, googleProvider)
                .then((result) => {
                    const user = result.user;
                    const userData = {
                        uid: user.uid,
                        email: user.email || '',
                        displayName: user.displayName || 'User_' + user.uid.substring(0, 8),
                        photoURL: user.photoURL || null
                    };
                    fetch('process_google_login.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(userData)
                    })
                    .then(response => response.json())
                    .then(data => {
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

        // KingsChat (KC) Login
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