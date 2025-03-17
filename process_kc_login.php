<?php
require_once 'functions/config.php';
require_once 'functions/db_connection.php';
require_once 'functions/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log incoming data
error_log('process_kc_login.php started');
error_log('POST data: ' . print_r($_POST, true));
error_log('GET data: ' . print_r($_GET, true));
error_log('Request Headers: ' . print_r(getallheaders(), true));

// Try to get token from various sources
$token = null;

// Check GET data (KingsChat uses URL fragment, but we can check GET)
if (!empty($_GET['accessToken'])) {
    $token = $_GET['accessToken'];
    error_log('Token found in GET data');
}

// Check POST data
if (!$token && !empty($_POST['accessToken'])) {
    $token = $_POST['accessToken'];
    error_log('Token found in POST data');
}

// Check Authorization header
if (!$token) {
    $headers = getallheaders();
    if (isset($headers['Authorization']) && strpos($headers['Authorization'], 'Bearer ') === 0) {
        $token = substr($headers['Authorization'], 7);
        error_log('Token found in Authorization header');
    }
}

// If we found a token, fetch user profile
if ($token) {
    error_log('Token successfully captured: ' . substr($token, 0, 10) . '...');
    $_SESSION['kc_access_token'] = $token;

    // Fetch user profile from KingsChat API
    $ch = curl_init('https://connect.kingsch.at/api/profile');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ]
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    error_log('Profile API Response Code: ' . $httpCode);
    error_log('Profile API Response: ' . $response);
    if ($curlError) {
        error_log('cURL Error: ' . $curlError);
    }

    if ($httpCode === 200) {
        $userData = json_decode($response, true);
        if ($userData && isset($userData['profile']) && isset($userData['profile']['user'])) {
            $profile = $userData['profile'];
            $user = $profile['user'];

            $_SESSION['kc_user'] = $user;
            error_log('User data stored in session: ' . print_r($user, true));

            // Extract required fields
            $kcUserId = $user['user_id'] ?? null;
            $email = $profile['email']['address'] ?? null;
            $displayName = $user['name'] ?? 'User_' . substr($kcUserId ?? '', 0, 8);
            $avatarUrl = $user['avatar_url'] ?? 'assets/images/default-avatar.png';

            if (!$kcUserId || !$email) {
                error_log('Could not find user ID or email in user data');
                header('Location: login.php?error=' . urlencode('Failed to fetch user ID or email'));
                exit;
            }

            // Check if user exists in your database
            $conn = getDbConnection();
            $existingUser = fetchRow("SELECT * FROM users WHERE kc_uid = ?", [$kcUserId]);

            if ($existingUser) {
                // Existing user - log them in
                $_SESSION['user_id'] = $existingUser['id'];
                $_SESSION['username'] = $existingUser['username'];
                $_SESSION['avatar'] = $existingUser['avatar'];
                header('Location: index.php');
            } else {
                // New user - redirect to complete profile
                $_SESSION['pending_kc_user'] = [
                    'uid' => $kcUserId,
                    'email' => $email,
                    'displayName' => $displayName,
                    'photoURL' => $avatarUrl,
                    'kcAccessToken' => $token
                ];
                header('Location: complete-profile.php?email=' . urlencode($email));
            }
            exit;
        }
    }
    error_log('Failed to fetch user profile or invalid response structure');
    header('Location: login.php?error=' . urlencode('Failed to fetch user profile'));
    exit;
}

// If no token was found
$error_message = 'No authentication data received';
error_log('Login error: ' . $error_message);
header('Location: login.php?error=' . urlencode($error_message));
exit;
?>