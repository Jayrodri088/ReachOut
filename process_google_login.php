<?php
require_once 'functions/config.php';
require_once 'functions/db_connection.php';
require_once 'functions/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Log the raw input for debugging
$rawInput = file_get_contents('php://input');
error_log("Raw input: " . $rawInput);

$data = json_decode($rawInput, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON decode error: " . json_last_error_msg());
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit;
}

if (!$data || !isset($data['email'])) {
    error_log("Invalid data received: " . print_r($data, true));
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$email = sanitizeInput($data['email']);
$uid = $data['uid'] ?? null;
$displayName = $data['displayName'] ?? 'User_' . substr($uid ?? '', 0, 8);
$photoURL = $data['photoURL'] ?? null;

// Check if user exists in the database by email
$user = getUserByUsername($email);

if ($user) {
    // User exists, log them in
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['avatar'] = $user['avatar'] ?? $photoURL;
    echo json_encode(['success' => true]);
} else {
    // User doesn't exist, prompt to complete profile
    $_SESSION['pending_google_user'] = [
        'email' => $email,
        'uid' => $uid,
        'displayName' => $displayName,
        'photoURL' => $photoURL
    ];
    echo json_encode(['success' => false, 'message' => 'User needs to complete profile']);
}
exit;